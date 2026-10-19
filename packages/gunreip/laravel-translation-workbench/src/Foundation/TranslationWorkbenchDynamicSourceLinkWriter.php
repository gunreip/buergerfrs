<?php

namespace Gunreip\TranslationWorkbench\Foundation;

use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchFinding;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchKey;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchReview;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Persists resolved dynamic-source links and their history atomically.
 * Callers resolve eligible sources. Writes require the current Admin or Super-Admin role.
 * Option snapshots belong to the link;
 * translations and original source values are not changed. Unlinking retains inactive links.
 */
class TranslationWorkbenchDynamicSourceLinkWriter
{
    public function __construct(private readonly TranslationWorkbenchTimelineRecorder $timelineRecorder) {}

    /**
     * @param  array<int, array<string, mixed>>  $runtimeSources
     * @param  array<string, mixed>  $relatedSource
     */
    public function confirm(object $selectedFinding, array $runtimeSources, array $relatedSource, array $expectedLinks): void
    {
        app(TranslationWorkbenchEditorAccess::class)->authorize();

        if ($runtimeSources === [] || empty($relatedSource['id'])) {
            throw new \InvalidArgumentException('Runtime sources and a related dynamic source are required.');
        }

        $runtimeSources = collect($runtimeSources);
        $key = $selectedFinding->key_id
            ? TranslationWorkbenchKey::query()->find($selectedFinding->key_id)
            : null;
        $finding = TranslationWorkbenchFinding::query()->find($selectedFinding->id);

        $createdOrUpdated = [];
        $oldValues = [];

        DB::transaction(function () use ($runtimeSources, $relatedSource, $selectedFinding, $key, $finding, $expectedLinks, &$createdOrUpdated, &$oldValues): void {
            [$consumerFinding, $consumerKey] = $this->consumerOwner($relatedSource);
            $relatedSource = $this->currentSource($relatedSource, false);
            $runtimeSources = $runtimeSources->map(fn (array $source) => $this->currentSource($source, true));
            $this->requireCurrentLinks($runtimeSources->pluck('id')->all(), $relatedSource['id'], $expectedLinks);
            foreach ($runtimeSources as $runtimeSource) {
                $candidateReference = 'dynamic_source:'.$relatedSource['id'];
                $candidateValues = collect($relatedSource['values'] ?? [])
                    ->mapWithKeys(static fn (array $value): array => [
                        $value['value_key'] => $value['native_label'],
                    ])
                    ->all();
                $attributes = [
                    'key_id' => $consumerKey,
                    'finding_id' => $consumerFinding,
                    'suggested_key' => $relatedSource['suggested_key'] ?? $selectedFinding->suggested_key,
                    'dynamic_scope' => $relatedSource['dynamic_scope'] ?? $selectedFinding->dynamic_scope,
                    'source_expression' => $relatedSource['source_expression'] ?? null,
                    'candidate_source_type' => 'related_dynamic_source',
                    'candidate_reference' => $candidateReference,
                    'candidate_values_count' => (int) ($relatedSource['values_count'] ?? 0),
                    'candidate_values' => json_encode($candidateValues, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    'confidence' => 'manual',
                    'review_status' => 'confirmed',
                    'status' => 'active',
                    'meta' => json_encode([
                        'source' => 'translation-workbench:dynamic-review-modal',
                        'runtime_source_id' => $runtimeSource['id'],
                        'runtime_suggested_key' => $runtimeSource['suggested_key'] ?? null,
                        'related_source_id' => $relatedSource['id'],
                        'related_suggested_key' => $relatedSource['suggested_key'] ?? null,
                        'review_finding_id' => $selectedFinding->id,
                    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                ];

                $existing = DB::table('translation_workbench_dynamic_source_candidates')
                    ->where('dynamic_source_id', $runtimeSource['id'])
                    ->where('candidate_source_type', 'related_dynamic_source')
                    ->where('candidate_reference', $candidateReference)
                    ->first();

                if (! $existing) {
                    $candidateId = DB::table('translation_workbench_dynamic_source_candidates')->insertGetId([
                        'dynamic_source_id' => $runtimeSource['id'],
                        ...$attributes,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $createdOrUpdated[] = [
                        'id' => $candidateId,
                        'runtime_source_id' => $runtimeSource['id'],
                        'related_source_id' => $relatedSource['id'],
                        'review_status' => 'confirmed',
                    ];

                    continue;
                }

                $oldValues[] = [
                    'id' => $existing->id,
                    'review_status' => $existing->review_status,
                    'status' => $existing->status,
                    'candidate_reference' => $existing->candidate_reference,
                ];

                DB::table('translation_workbench_dynamic_source_candidates')
                    ->where('id', $existing->id)
                    ->update([
                        ...$attributes,
                        'updated_at' => now(),
                    ]);

                $createdOrUpdated[] = [
                    'id' => (int) $existing->id,
                    'runtime_source_id' => $runtimeSource['id'],
                    'related_source_id' => $relatedSource['id'],
                    'review_status' => 'confirmed',
                ];
            }

            $review = TranslationWorkbenchReview::query()->create([
                'key_id' => $key?->id,
                'finding_id' => $finding?->id,
                'review_type' => 'dynamic_source_link',
                'decision' => 'runtime_options_link_confirmed',
                'old_values' => $oldValues,
                'new_values' => $createdOrUpdated,
                'meta' => [
                    'source' => 'translation-workbench:dynamic-review-modal',
                    'runtime_source_count' => $runtimeSources->count(),
                    'related_source_id' => $relatedSource['id'],
                ],
                'reviewed_by_user_id' => Auth::id(),
                'reviewed_at' => now(),
            ]);

            $this->timelineRecorder->recordReviewEvent(
                review: $review,
                eventType: 'dynamic_source_link_confirmed',
                oldValues: $oldValues,
                newValues: $createdOrUpdated,
                context: [
                    'source' => 'translation-workbench:dynamic-review-modal',
                    'runtime_source_count' => $runtimeSources->count(),
                    'related_source_id' => $relatedSource['id'],
                ],
            );
        });

    }

    /** The same ordered snapshot is used for the displayed review and the transactional comparison. */
    public function linkSnapshot(array $runtimeSourceIds, int $relatedSourceId, bool $lock = false): array
    {
        $query = DB::table('translation_workbench_dynamic_source_candidates')
            ->whereIn('dynamic_source_id', $runtimeSourceIds)
            ->where('candidate_source_type', 'related_dynamic_source')
            ->where('candidate_reference', 'dynamic_source:'.$relatedSourceId)
            ->orderBy('id');
        if ($lock) {
            $query->lockForUpdate();
        }

        return $query->get(['id', 'dynamic_source_id', 'key_id', 'finding_id', 'review_status', 'status', 'candidate_values'])
            ->map(static fn (object $row): array => (array) $row)->all();
    }

    private function requireCurrentLinks(array $runtimeSourceIds, int $relatedSourceId, array $expectedLinks): void
    {
        if ($this->linkSnapshot($runtimeSourceIds, $relatedSourceId, true) !== $expectedLinks) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'dynamicSourceLinkRelatedSourceId' => __('The links have changed since the source review. Close and reopen the confirmation before saving.'),
            ]);
        }
    }

    /** Read and lock the authoritative snapshot; reject a preview that changed before this write. */
    private function currentSource(array $preview, bool $runtime): array
    {
        $invalid = static function (): never {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'dynamicSourceLinkRelatedSourceId' => __('The source values or provenance have changed. Refresh the source review before confirming.'),
            ]);
        };
        $source = DB::table('translation_workbench_dynamic_sources')->where('id', $preview['id'] ?? null)->lockForUpdate()->first();
        if (! $source || ! in_array($source->status, ['active', 'needs_review'], true)
            || in_array($source->source_type, ['runtime_options', 'runtime_db_options'], true) !== $runtime) {
            $invalid();
        }
        $displayText = static fn ($value) => trim((string) $value) === '' ? null : trim((string) $value);
        foreach (['key_id', 'finding_id', 'source_type', 'dynamic_scope', 'source_expression', 'source_reference'] as $field) {
            $actual = $source->{$field};
            $shown = $preview[$field] ?? null;
            if (! in_array($field, ['key_id', 'finding_id'], true)) {
                $actual = $displayText($actual);
                $shown = $displayText($shown);
            }
            if ($shown !== $actual) { $invalid(); }
        }
        $values = DB::table('translation_workbench_dynamic_source_values')->where('dynamic_source_id', $source->id)
            ->where('status', '<>', 'obsolete')->orderBy('value_key')->lockForUpdate()
            ->get(['value_key', 'native_label', 'status'])->map(fn ($row) => (array) $row)->all();
        $normalize = static fn (array $rows) => collect($rows)->map(fn ($row) => [
            'value_key' => (string) $row['value_key'], 'native_label' => $displayText($row['native_label']), 'status' => $row['status'],
        ])->sortBy('value_key')->values()->all();
        if ($normalize($values) !== $normalize($preview['values'] ?? [])) { $invalid(); }
        $key = $source->key_id ? TranslationWorkbenchKey::find($source->key_id) : null;
        if ($key?->status === 'obsolete') { $invalid(); }

        return [...(array) $source, 'suggested_key' => $key?->suggested_key, 'values' => $values, 'values_count' => count($values)];
    }

    /** Resolve the consumer's current identity; a runtime provider's key is not its consumer's key. */
    private function consumerOwner(array $relatedSource): array
    {
        $invalid = static function (): never {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'dynamicSourceLinkRelatedSourceId' => __('The source assignment has changed or is no longer active. Refresh the source review before confirming.'),
            ]);
        };
        $source = DB::table('translation_workbench_dynamic_sources')->where('id', $relatedSource['id'])->lockForUpdate()->first();
        if (! $source || $source->status === 'obsolete' || ! $source->finding_id
            || $source->finding_id !== ($relatedSource['finding_id'] ?? null)
            || $source->key_id !== ($relatedSource['key_id'] ?? null)) {
            $invalid();
        }
        $finding = TranslationWorkbenchFinding::with('sourceFile')->whereKey($source->finding_id)->lockForUpdate()->first();
        if (! $finding || $finding->status !== 'active' || $finding->sourceFile?->status !== 'active') {
            $invalid();
        }
        $keys = DB::table('translation_workbench_key_findings as links')
            ->join('translation_workbench_keys as keys', 'keys.id', '=', 'links.key_id')
            ->where('links.finding_id', $finding->id)->where('links.status', 'active')->where('keys.status', '<>', 'obsolete')
            ->pluck('keys.id')->unique()->values();
        if ($keys->count() !== 1 || ($source->key_id !== null && $source->key_id !== $keys->sole())) {
            $invalid();
        }

        return [$finding->id, $keys->sole()];
    }

    /**
     * @param  array<int, array<string, mixed>>  $runtimeSources
     * @param  array<string, mixed>  $relatedSource
     */
    public function unlink(object $selectedFinding, array $runtimeSources, array $relatedSource, array $expectedLinks): bool
    {
        app(TranslationWorkbenchEditorAccess::class)->authorize();

        if ($runtimeSources === [] || empty($relatedSource['id'])) {
            throw new \InvalidArgumentException('Runtime sources and a related dynamic source are required.');
        }

        $runtimeSources = collect($runtimeSources);
        $key = $selectedFinding->key_id
            ? TranslationWorkbenchKey::query()->find($selectedFinding->key_id)
            : null;
        $finding = TranslationWorkbenchFinding::query()->find($selectedFinding->id);
        $candidateReference = 'dynamic_source:'.$relatedSource['id'];
        $runtimeSourceIds = $runtimeSources
            ->pluck('id')
            ->map(static fn (mixed $id): int => (int) $id)
            ->all();
        $oldValues = [];
        $newValues = [];

        DB::transaction(function () use ($runtimeSourceIds, $candidateReference, $relatedSource, $runtimeSources, $key, $finding, $expectedLinks, &$oldValues, &$newValues): void {
            [$consumerFinding, $consumerKey] = $this->consumerOwner($relatedSource);
            $this->currentSource($relatedSource, false);
            foreach ($runtimeSources as $runtimeSource) {
                $this->currentSource($runtimeSource, true);
            }

            $this->requireCurrentLinks($runtimeSourceIds, $relatedSource['id'], $expectedLinks);

            $candidates = DB::table('translation_workbench_dynamic_source_candidates')
                ->whereIn('dynamic_source_id', $runtimeSourceIds)
                ->where('candidate_source_type', 'related_dynamic_source')
                ->where('candidate_reference', $candidateReference)
                ->where('status', 'active')
                ->lockForUpdate()->get();

            if ($candidates->isEmpty()) {
                return;
            }

            foreach ($candidates as $candidate) {
                if ($candidate->key_id !== $consumerKey || $candidate->finding_id !== $consumerFinding) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'dynamicSourceLinkRelatedSourceId' => __('The link ownership has changed. Reopen the source review before removing links.'),
                    ]);
                }
            }

            $oldValues = $candidates
                ->map(static fn (object $candidate): array => [
                    'id' => (int) $candidate->id,
                    'runtime_source_id' => (int) $candidate->dynamic_source_id,
                    'related_source_id' => (int) $relatedSource['id'],
                    'review_status' => (string) $candidate->review_status,
                    'status' => (string) $candidate->status,
                ])
                ->all();

            DB::table('translation_workbench_dynamic_source_candidates')
                ->whereIn('id', $candidates->pluck('id')->all())
                ->update([
                    'review_status' => 'unlinked',
                    'status' => 'inactive',
                    'updated_at' => now(),
                ]);

            $newValues = $candidates
                ->map(static fn (object $candidate): array => [
                    'id' => (int) $candidate->id,
                    'runtime_source_id' => (int) $candidate->dynamic_source_id,
                    'related_source_id' => (int) $relatedSource['id'],
                    'review_status' => 'unlinked',
                    'status' => 'inactive',
                ])
                ->all();

            $review = TranslationWorkbenchReview::query()->create([
                'key_id' => $key?->id,
                'finding_id' => $finding?->id,
                'review_type' => 'dynamic_source_link',
                'decision' => 'runtime_options_link_unlinked',
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'meta' => [
                    'source' => 'translation-workbench:dynamic-review-modal',
                    'runtime_source_count' => $runtimeSources->count(),
                    'related_source_id' => $relatedSource['id'],
                ],
                'reviewed_by_user_id' => Auth::id(),
                'reviewed_at' => now(),
            ]);

            $this->timelineRecorder->recordReviewEvent(
                review: $review,
                eventType: 'dynamic_source_link_unlinked',
                oldValues: $oldValues,
                newValues: $newValues,
                context: [
                    'source' => 'translation-workbench:dynamic-review-modal',
                    'runtime_source_count' => $runtimeSources->count(),
                    'related_source_id' => $relatedSource['id'],
                ],
            );
        });

        return $newValues !== [];
    }
}
