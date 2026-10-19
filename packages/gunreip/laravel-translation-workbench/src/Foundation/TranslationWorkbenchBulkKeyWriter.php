<?php

namespace Gunreip\TranslationWorkbench\Foundation;

use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchFinding;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchKey;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchKeyFinding;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchReview;
use Gunreip\TranslationWorkbench\Scanner\TranslationFingerprintFactory;
use Gunreip\TranslationWorkbench\Scanner\TranslationKeyPartsFactory;
use Gunreip\TranslationWorkbench\Support\TranslationKeySegmentFactory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

/**
 * Existing Bulk Equalize selection and write rules, independent of Livewire state.
 * Writes require the current Admin or Super-Admin role; no source or language file is published.
 * UI callers supply a preview-context fingerprint; operation IDs are not idempotency keys.
 */
class TranslationWorkbenchBulkKeyWriter
{
    public const TARGET_KEY_RULES = ['required', 'string', 'regex:/^[a-z0-9][a-z0-9_-]*(\.[a-z0-9][a-z0-9_-]*)+$/'];

    public function equalize(array $selectedFindingIds, string $translationKey, ?string $expectedContextFingerprint = null): int
    {
        app(TranslationWorkbenchEditorAccess::class)->authorize();

        $translationKey = trim($translationKey);
        Validator::make(['translation_key' => $translationKey], [
            'translation_key' => self::TARGET_KEY_RULES,
        ])->validate();

        return DB::transaction(function () use ($selectedFindingIds, $translationKey, $expectedContextFingerprint): int {
            $this->lockWriteInputs();
            $context = $this->context($selectedFindingIds);
            if ($expectedContextFingerprint !== null && ! hash_equals($expectedContextFingerprint, $this->contextFingerprint($context))) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'bulkEqualizeSelectedFindingIds' => __('The selection changed after the preview. Reopen the dialog and review the current findings before confirming.'),
                ]);
            }
            if ($context['selection_error'] !== null) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'bulkEqualizeSelectedFindingIds' => $context['selection_error'],
                ]);
            }
            if (! $context['can_confirm']) {
                return 0;
            }
            $keyIds = collect($context['rows'])
                ->pluck('key_id')
                ->map(static fn (mixed $id): int => (int) $id)
                ->filter(static fn (int $id): bool => $id > 0)
                ->unique()
                ->values();

            if ($keyIds->isEmpty() && ! ($context['equalize_to_existing_shared_key'] ?? false)) {
                return 0;
            }

            $operationId = (string) Str::uuid();
            $updated = 0;

            $keys = TranslationWorkbenchKey::query()
                ->whereIn('id', $keyIds->all())
                ->get()
                ->keyBy('id');
            $findings = TranslationWorkbenchFinding::query()
                ->whereIn('id', collect($context['rows'])->pluck('id')->all())
                ->get()
                ->keyBy('id');

            foreach ($context['rows'] as $row) {
                $finding = $findings->get((int) $row['id']);
                $key = $keys->get((int) $row['key_id'])
                    ?? $this->keyForBulkEqualizeRow($row, $finding, $translationKey, $operationId);

                if (! $key || ! $finding) {
                    continue;
                }

                $attributes = [
                    ...app(TranslationWorkbenchKeyAttributes::class)->forKey($translationKey, $key),
                    'review_status' => 'reviewed',
                    'reviewed_at' => now(),
                    'reviewed_by_user_id' => Auth::id(),
                ];
                $trackedAttributes = array_values(array_unique(array_keys($attributes)));
                $oldValues = $key->only([
                    ...$trackedAttributes,
                    'review_status',
                    'reviewed_at',
                    'reviewed_by_user_id',
                ]);
                $changedValues = collect($attributes)
                    ->filter(static fn (mixed $value, string $attribute): bool => ($oldValues[$attribute] ?? null) != $value)
                    ->all();

                if ($changedValues === []) {
                    continue;
                }

                $key->forceFill($changedValues)->save();
                $newValues = $key->only([
                    ...$trackedAttributes,
                    'review_status',
                    'reviewed_at',
                    'reviewed_by_user_id',
                ]);
                $review = TranslationWorkbenchReview::query()->create([
                    'key_id' => $key->id,
                    'finding_id' => $finding->id,
                    'review_type' => 'translation_key',
                    'decision' => 'translation_key_bulk_equalized',
                    'old_values' => $oldValues,
                    'new_values' => $newValues,
                    'meta' => [
                        'source' => 'translation-workbench:bulk-equalize-translation-key',
                        'operation_id' => $operationId,
                        'literal' => $context['literal'],
                        'normalized_literal' => $context['normalized_literal'],
                        'selected_finding_ids' => $context['selected_ids'],
                        'target_translation_key' => $translationKey,
                    ],
                    'reviewed_by_user_id' => Auth::id(),
                    'reviewed_at' => now(),
                ]);

                app(TranslationWorkbenchTimelineRecorder::class)->recordReviewEvent(
                    review: $review,
                    eventType: 'translation_key_bulk_equalized',
                    oldValues: $oldValues,
                    newValues: $newValues,
                    context: [
                        'source' => 'translation-workbench:bulk-equalize-translation-key',
                        'operation_id' => $operationId,
                        'literal' => $context['literal'],
                        'normalized_literal' => $context['normalized_literal'],
                        'selected_count' => $context['selected_count'],
                        'target_translation_key' => $translationKey,
                    ],
                );

                $updated++;
            }

            return $updated;
        }, 3);
    }

    public function contextFingerprint(array $context): string
    {
        return hash('sha256', json_encode($context, JSON_THROW_ON_ERROR));
    }

    private function lockWriteInputs(): void
    {
        if (DB::transactionLevel() === 0) {
            throw new \LogicException('Bulk key equalization requires a write transaction.');
        }
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }
        // Protect existing rows and newly inserted links/reviews through history recording.
        // Serialize writers without blocking ordinary SELECT queries.
        DB::statement('LOCK TABLE translation_workbench_source_files, translation_workbench_findings, translation_workbench_keys, translation_workbench_key_findings, translation_workbench_reviews IN SHARE ROW EXCLUSIVE MODE');
    }

    public function context(array $selectedFindingIds): array
    {
        $selectedIds = collect($selectedFindingIds)
            ->map(static fn (mixed $id): int => (int) $id)
            ->filter(static fn (int $id): bool => $id > 0)
            ->unique()
            ->values();

        if ($selectedIds->isEmpty() || ! $this->hasTables([
            'translation_workbench_findings',
            'translation_workbench_source_files',
            'translation_workbench_keys',
            'translation_workbench_key_findings',
        ])) {
            return $this->emptyBulkEqualizeContext($selectedIds->all());
        }

        $conflicts = app(TranslationWorkbenchDynamicKeyAssignment::class)->conflicts($selectedIds->all());
        if ($conflicts->isNotEmpty()) {
            return [
                ...$this->emptyBulkEqualizeContext($selectedIds->all()),
                'key_assignment_conflicts' => $conflicts->all(),
                'selection_error' => __('Multiple active keys are linked to selected findings: :assignments. Resolve these assignments before equalizing.', [
                    'assignments' => $conflicts->map(static fn (array $conflict): string =>
                        $conflict['finding_id'].' → '.implode(', ', $conflict['key_ids'])
                    )->implode('; '),
                ]),
            ];
        }

        $keyLinks = DB::table('translation_workbench_key_findings as links')
            ->join('translation_workbench_keys as linked_keys', 'linked_keys.id', '=', 'links.key_id')
            ->selectRaw('links.finding_id, MIN(links.key_id) as key_id')
            ->where('links.status', 'active')
            ->where('linked_keys.status', '<>', 'obsolete')
            ->groupBy('links.finding_id');

        $rows = DB::table('translation_workbench_findings as findings')
            ->join('translation_workbench_source_files as source_files', 'source_files.id', '=', 'findings.source_file_id')
            ->where('findings.status', 'active')
            ->where('source_files.status', 'active')
            ->leftJoinSub($keyLinks, 'key_links', function ($join): void {
                $join->on('key_links.finding_id', '=', 'findings.id');
            })
            ->leftJoin('translation_workbench_keys as keys', 'keys.id', '=', 'key_links.key_id')
            ->whereIn('findings.id', $selectedIds->all())
            ->select([
                'findings.id',
                'findings.literal_text',
                'findings.literal_text_suggested',
                'findings.suggested_key as finding_suggested_key',
                'findings.existing_key',
                'findings.found_translation_key',
                'keys.id as key_id',
                'keys.translation_key',
                'keys.suggested_key as key_suggested_key',
                'keys.review_status',
            ])
            ->orderBy('findings.id')
            ->get()
            ->map(function (object $row): array {
                $literal = $this->nullableString($row->literal_text ?? null)
                    ?? $this->nullableString($row->literal_text_suggested ?? null);

                return [
                    'id' => (int) $row->id,
                    'key_id' => $row->key_id !== null ? (int) $row->key_id : null,
                    'literal' => $literal,
                    'normalized_literal' => $this->normalizedBulkLiteral($literal),
                    'translation_key' => $this->nullableString($row->translation_key ?? null),
                    'key_suggested_key' => $this->nullableString($row->key_suggested_key ?? null),
                    'finding_suggested_key' => $this->nullableString($row->finding_suggested_key ?? null),
                    'existing_key' => $this->nullableString($row->existing_key ?? null),
                    'found_translation_key' => $this->nullableString($row->found_translation_key ?? null),
                    'review_status' => $this->nullableString($row->review_status ?? null),
                ];
            })
            ->all();

        $literalGroups = collect($rows)
            ->pluck('normalized_literal')
            ->filter()
            ->unique()
            ->values();
        $translationKeys = collect($rows)
            ->pluck('translation_key')
            ->filter()
            ->unique()
            ->values()
            ->all();
        $suggestedKeys = collect($rows)
            ->flatMap(static fn (array $row): array => [
                $row['key_suggested_key'],
                $row['finding_suggested_key'],
            ])
            ->filter()
            ->unique()
            ->values()
            ->all();
        $literal = collect($rows)
            ->pluck('literal')
            ->filter()
            ->first();
        $missingKeyCount = collect($rows)
            ->filter(static fn (array $row): bool => empty($row['key_id']))
            ->count();
        $existingSharedKey = $literalGroups->count() === 1
            ? $this->existingBulkSharedKeyForLiteral((string) $literalGroups->first(), $selectedIds->all())
            : null;
        $suggestedTargetKey = collect($translationKeys)->first()
            ?? ($existingSharedKey['translation_key'] ?? null)
            ?? collect($suggestedKeys)->first()
            ?? $this->bulkTranslationKeySuggestionFromLiteral($literal);
        $selectionError = null;
        $missingKeyCandidates = [];
        if ($missingKeyCount > 0 && $existingSharedKey !== null && filled($suggestedTargetKey)) {
            foreach ($rows as $row) {
                if (! empty($row['key_id'])) {
                    continue;
                }
                try {
                    [, , $candidate] = $this->missingKeyCandidate($row, $suggestedTargetKey);
                    $missingKeyCandidates[$row['id']] = $candidate?->id;
                } catch (\Illuminate\Validation\ValidationException $exception) {
                    $selectionError = $exception->errors()['bulkEqualizeSelectedFindingIds'][0];
                    break;
                }
            }
        }
        $canConfirm = $selectionError === null
            && count($rows) >= 1
            && count($rows) === $selectedIds->count()
            && $literalGroups->count() === 1
            && ($missingKeyCount === 0 || $existingSharedKey !== null)
            && filled($suggestedTargetKey)
            && (count($rows) >= 2 || $existingSharedKey !== null);

        return [
            'selected_ids' => $selectedIds->all(),
            'selected_count' => count($rows),
            'rows' => $rows,
            'literal' => $literal,
            'normalized_literal' => $literalGroups->first(),
            'literal_count' => $literalGroups->count(),
            'missing_key_count' => $missingKeyCount,
            'can_confirm' => $canConfirm,
            'key_assignment_conflicts' => [],
            'selection_error' => $selectionError,
            'missing_key_candidates' => $missingKeyCandidates,
            'equalize_to_existing_shared_key' => $existingSharedKey !== null && collect($translationKeys)->isEmpty(),
            'existing_shared_key' => $existingSharedKey['translation_key'] ?? null,
            'existing_shared_key_count' => $existingSharedKey['finding_count'] ?? 0,
            'suggested_target_key' => $suggestedTargetKey,
            'translation_keys' => $translationKeys,
            'suggested_keys' => $suggestedKeys,
        ];
    }

    private function existingBulkSharedKeyForLiteral(string $normalizedLiteral, array $selectedIds): ?array
    {
        if ($normalizedLiteral === '' || ! $this->hasTables([
            'translation_workbench_findings',
            'translation_workbench_source_files',
            'translation_workbench_key_findings',
            'translation_workbench_keys',
            'translation_workbench_reviews',
        ])) {
            return null;
        }

        $literalExpression = $this->bulkLiteralSqlExpression('findings');
        $selectedIds = collect($selectedIds)
            ->map(static fn (mixed $id): int => (int) $id)
            ->filter(static fn (int $id): bool => $id > 0)
            ->unique()
            ->values()
            ->all();

        $query = DB::table('translation_workbench_findings as findings')
            ->join('translation_workbench_source_files as source_files', 'source_files.id', '=', 'findings.source_file_id')
            ->join('translation_workbench_key_findings as key_findings', function ($join): void {
                $join
                    ->on('key_findings.finding_id', '=', 'findings.id')
                    ->where('key_findings.status', '=', 'active');
            })
            ->join('translation_workbench_keys as keys', 'keys.id', '=', 'key_findings.key_id')
            ->where('findings.status', 'active')
            ->where('source_files.status', 'active')
            ->where('keys.status', '<>', 'obsolete')
            ->whereRaw($literalExpression.' = ?', [$normalizedLiteral])
            ->whereRaw("NULLIF(BTRIM(keys.translation_key), '') IS NOT NULL")
            ->whereExists(function ($query) use ($literalExpression): void {
                $query->selectRaw('1')->from('translation_workbench_reviews as bulk_reviews')
                    ->where('bulk_reviews.decision', 'translation_key_bulk_equalized')
                    ->whereColumn('bulk_reviews.finding_id', 'findings.id')
                    ->whereColumn('bulk_reviews.key_id', 'keys.id')
                    ->whereColumn('bulk_reviews.new_values->translation_key', 'keys.translation_key')
                    ->whereColumn('bulk_reviews.meta->normalized_literal', DB::raw($literalExpression));
            })
            ->whereNotExists(function ($query): void {
                $query->selectRaw('1')->from('translation_workbench_key_findings as competing_links')
                    ->join('translation_workbench_keys as competing_keys', 'competing_keys.id', '=', 'competing_links.key_id')
                    ->whereColumn('competing_links.finding_id', 'findings.id')
                    ->whereColumn('competing_links.key_id', '<>', 'keys.id')
                    ->where('competing_links.status', 'active')
                    ->where('competing_keys.status', '<>', 'obsolete');
            });

        if ($selectedIds !== []) {
            $query->whereNotIn('findings.id', $selectedIds);
        }

        $targets = $query
            ->selectRaw('keys.translation_key, COUNT(DISTINCT findings.id) as finding_count')
            ->groupBy('keys.translation_key')
            ->limit(2)
            ->get();

        // Competing confirmed targets must never be resolved by frequency or name.
        if ($targets->count() !== 1) {
            return null;
        }

        $row = $targets->sole();

        return [
            'translation_key' => (string) $row->translation_key,
            'finding_count' => (int) $row->finding_count,
        ];
    }

    private function emptyBulkEqualizeContext(array $selectedIds = []): array
    {
        return [
            'selected_ids' => $selectedIds,
            'selected_count' => 0,
            'rows' => [],
            'literal' => null,
            'normalized_literal' => null,
            'literal_count' => 0,
            'missing_key_count' => 0,
            'can_confirm' => false,
            'key_assignment_conflicts' => [],
            'selection_error' => null,
            'suggested_target_key' => null,
            'translation_keys' => [],
            'suggested_keys' => [],
        ];
    }

    public function bulkLiteralSqlExpression(string $tableAlias): string
    {
        return "LOWER(REGEXP_REPLACE(BTRIM(COALESCE({$tableAlias}.literal_text, {$tableAlias}.literal_text_suggested, '')), '\\s+', ' ', 'g'))";
    }

    public function normalizedBulkLiteral(?string $literal): ?string
    {
        $literal = $this->nullableString($literal);

        return $literal === null
            ? null
            : Str::lower(preg_replace('/\s+/u', ' ', $literal) ?: $literal);
    }

    public function bulkTranslationKeySuggestionFromLiteral(?string $literal): ?string
    {
        $literal = $this->nullableString($literal);

        if ($literal === null) {
            return null;
        }

        $lastSegment = Str::slug($literal, '_');

        return $lastSegment !== '' ? 'ui.'.$lastSegment : null;
    }

    /** @return array{0: string, 1: string, 2: ?TranslationWorkbenchKey} */
    private function missingKeyCandidate(array $row, string $translationKey): array
    {
        $suggestedKey = $this->nullableString($row['key_suggested_key'] ?? null)
            ?? $this->nullableString($row['finding_suggested_key'] ?? null)
            ?? $translationKey;
        $fingerprint = app(TranslationFingerprintFactory::class)->signature([
            'foundation-key',
            '',
            $suggestedKey,
        ]);
        $candidates = TranslationWorkbenchKey::query()
            ->where('fingerprint', $fingerprint)
            ->orWhere(function ($query) use ($suggestedKey): void {
                $query->where('suggested_key', $suggestedKey)
                    ->where(function ($query): void {
                        $query->whereNull('translation_key')
                            ->orWhereRaw("NULLIF(BTRIM(translation_key), '') IS NULL");
                    });
            })
            ->orderBy('id')
            ->get();
        $key = $candidates->count() === 1 ? $candidates->sole() : null;
        if ($candidates->count() > 1 || ($key && (
            $key->status === 'obsolete'
            || $key->review_status !== 'pending'
            || $this->nullableString($key->translation_key) !== null
            || TranslationWorkbenchKeyFinding::query()->where('key_id', $key->id)
                ->where('finding_id', '<>', $row['id'])->where('status', 'active')->exists()
        ))) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'bulkEqualizeSelectedFindingIds' => __('Finding :finding cannot reuse key candidates :keys. Review the existing keys and assignments before equalizing.', [
                    'finding' => $row['id'],
                    'keys' => $candidates->pluck('id')->implode(', '),
                ]),
            ]);
        }
        return [$suggestedKey, $fingerprint, $key];
    }

    private function keyForBulkEqualizeRow(
        array $row,
        ?TranslationWorkbenchFinding $finding,
        string $translationKey,
        string $operationId,
    ): ?TranslationWorkbenchKey {
        if (! $finding) {
            return null;
        }

        [$suggestedKey, $fingerprint, $key] = $this->missingKeyCandidate($row, $translationKey);
        $timelineRecorder = app(TranslationWorkbenchTimelineRecorder::class);

        if (! $key) {
            $keyParts = app(TranslationKeyPartsFactory::class)->fromKey($suggestedKey);
            $keySegments = app(TranslationKeySegmentFactory::class)->fromKey($suggestedKey);
            $key = TranslationWorkbenchKey::query()->create([
                'fingerprint' => $fingerprint,
                'translation_key' => null,
                'suggested_key' => $suggestedKey,
                'namespace' => $keyParts['namespace'],
                'group' => $keyParts['group'],
                'path_key' => $keyParts['path_key'],
                'scope' => $keyParts['scope'],
                ...$keySegments,
                'key_type' => str_starts_with((string) $finding->kind, 'dynamic') ? 'dynamic_candidate' : 'static_candidate',
                'status' => 'open',
                'review_status' => 'pending',
                'meta' => [
                    'source' => 'translation-workbench:bulk-equalize-missing-key',
                    'operation_id' => $operationId,
                    'finding_id' => $finding->id,
                ],
            ]);

            $timelineRecorder->recordKeyEvent(
                key: $key,
                eventType: 'key_candidate_discovered',
                newValues: $key->only([
                    'id',
                    'fingerprint',
                    'translation_key',
                    'suggested_key',
                    'namespace',
                    'group',
                    'path_key',
                    'scope',
                    'key_type',
                    'status',
                    'review_status',
                ]),
                context: [
                    'source' => 'translation-workbench:bulk-equalize-missing-key',
                    'operation_id' => $operationId,
                    'finding_id' => $finding->id,
                ],
            );
        }

        $relation = TranslationWorkbenchKeyFinding::query()
            ->where('key_id', $key->id)
            ->where('finding_id', $finding->id)
            ->where('relation_type', 'candidate')
            ->first();

        if (! $relation) {
            $relation = TranslationWorkbenchKeyFinding::query()->create([
                'key_id' => $key->id,
                'finding_id' => $finding->id,
                'relation_type' => 'candidate',
                'status' => 'active',
                'meta' => [
                    'source' => 'translation-workbench:bulk-equalize-missing-key',
                    'operation_id' => $operationId,
                ],
            ]);

            $timelineRecorder->recordKeyFindingEvent(
                key: $key,
                finding: $finding,
                eventType: 'key_finding_relation_created',
                newValues: $relation->only(['id', 'key_id', 'finding_id', 'relation_type', 'status']),
                context: [
                    'source' => 'translation-workbench:bulk-equalize-missing-key',
                    'operation_id' => $operationId,
                ],
            );

            return $key;
        }

        if ($relation->status !== 'active') {
            $oldValues = $relation->only(['status', 'meta']);
            $relation->forceFill([
                'status' => 'active',
                'meta' => [
                    ...($relation->meta ?? []),
                    'source' => 'translation-workbench:bulk-equalize-missing-key',
                    'operation_id' => $operationId,
                ],
            ])->save();

            $timelineRecorder->recordKeyFindingEvent(
                key: $key,
                finding: $finding,
                eventType: 'key_finding_relation_updated',
                oldValues: $oldValues,
                newValues: $relation->only(['status', 'meta']),
                context: [
                    'source' => 'translation-workbench:bulk-equalize-missing-key',
                    'operation_id' => $operationId,
                ],
            );
        }

        return $key;
    }

    private function nullableString(mixed $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function hasTables(array $tables): bool
    {
        foreach ($tables as $table) {
            if (! Schema::hasTable($table)) {
                return false;
            }
        }

        return true;
    }
}
