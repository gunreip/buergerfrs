<?php

// packages/gunreip/laravel-translation-workbench/src/Console/ClassifyDynamicValues.php

// php artisan translation-workbench:classify-dynamic-values
// php artisan translation-workbench:classify-dynamic-values --dry-run

namespace Gunreip\TranslationWorkbench\Console;

use Gunreip\TranslationWorkbench\Console\Concerns\WritesTranslationWorkbenchReports;
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchTimelineRecorder;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchFinding;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchKey;
use Gunreip\TranslationWorkbench\Scanner\TranslationFingerprintFactory;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

#[Signature('translation-workbench:classify-dynamic-values
    {--dry-run : Report classifications without writing database rows.}')]
#[Description('Classify dynamic translation findings into hardcoded/db and single/multi value sources.')]
class ClassifyDynamicValues extends Command
{
    use WritesTranslationWorkbenchReports;

    public function __construct(
        private readonly TranslationFingerprintFactory $fingerprintFactory,
        private readonly TranslationWorkbenchTimelineRecorder $timelineRecorder,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        return $this->option('dry-run')
            ? $this->classify()
            : DB::transaction(fn (): int => $this->classify());
    }

    private function classify(): int
    {
        if (! $this->hasRequiredTables()) {
            $this->error('Dynamic classification tables are missing. Run the workbench migrations first.');
            $this->writeTranslationWorkbenchReport(summary: [
                'dry_run' => (bool) $this->option('dry-run'),
                'exit_code' => self::FAILURE,
                'error' => 'missing_required_tables',
            ]);

            return self::FAILURE;
        }

        $dryRun = (bool) $this->option('dry-run');
        $now = now();
        $summary = [
            'dry_run' => $dryRun,
            'exit_code' => self::SUCCESS,
            'dynamic_findings' => 0,
            'sources_created' => 0,
            'sources_updated' => 0,
            'sources_unchanged' => 0,
            'sources_obsoleted' => 0,
            'source_values_created' => 0,
            'source_values_updated' => 0,
            'source_values_unchanged' => 0,
            'source_values_removed' => 0,
            'source_values_preserved' => 0,
            'structured_keys' => 0,
            'unstructured_keys' => 0,
            'timeline_events_created' => 0,
        ];

        if (! $dryRun) {
            $this->lockDiscoveryInventory();
        }

        // Retirement may only inspect sources that existed when this run began.
        $initialSources = $dryRun ? collect() : $this->retirementSourceSnapshot();
        $rows = $this->dynamicFindingRows();
        $summary['dynamic_findings'] = $rows->count();
        $conflicts = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchDynamicKeyAssignment::class)
            ->conflicts($rows->pluck('finding_id')->all());
        if ($conflicts->isNotEmpty()) {
            $this->error('Ambiguous active key assignments; no dynamic sources were changed.');
            foreach ($conflicts as $conflict) {
                $this->line('Finding '.$conflict['finding_id'].' -> Key IDs: '.implode(', ', $conflict['key_ids']));
            }
            $this->writeTranslationWorkbenchReport(
                summary: [...$summary, 'exit_code' => self::FAILURE, 'error' => 'ambiguous_active_key_assignments', 'conflicting_findings' => $conflicts->count()],
                planSummary: ['key_assignment_conflicts' => $conflicts->values()->all()],
            );
            return self::FAILURE;
        }
        $seenSourceIds = [];

        foreach ($rows as $row) {
            $classifications = $this->classificationsForRow($row);
            if (! $dryRun) {
                $this->lockClassificationInput($row, $classifications);
            }
            $rowHasStructuredSource = false;

            foreach ($classifications as $classification) {
                $rowHasStructuredSource = $rowHasStructuredSource || $classification['classification'] !== 'unknown';

                if ($dryRun) {
                    continue;
                }

                $sourceResult = $this->syncDynamicSource($row, $classification, $now);
                $seenSourceIds[] = (int) $sourceResult['source_id'];
                $summary[$sourceResult['result']]++;
                $summary['timeline_events_created'] += $sourceResult['timeline_events_created'];

                $valueSummary = $this->syncDynamicSourceValues(
                    dynamicSourceId: (int) $sourceResult['source_id'],
                    row: $row,
                    classification: $classification,
                );

                foreach ($valueSummary as $key => $value) {
                    $summary[$key] += $value;
                }
            }

            if (! $dryRun) {
                $state = $rowHasStructuredSource ? 'structured' : 'unstructured';
                $summary[$state === 'structured' ? 'structured_keys' : 'unstructured_keys'] += $this->syncDynamicDataState($row, $state);
                $this->retainOwnClassificationState($initialSources, $row, $state);
            }
        }

        if (! $dryRun) {
            $summary['sources_obsoleted'] = $this->obsoleteUnseenSources($seenSourceIds, $initialSources);
            $summary['timeline_events_created'] += $summary['sources_obsoleted'];
        }

        $this->components->info('Translation Workbench dynamic value classification finished.');
        $this->line('Dynamic findings: ' . number_format($summary['dynamic_findings']));
        $this->line('Sources created: ' . number_format($summary['sources_created']));
        $this->line('Sources updated: ' . number_format($summary['sources_updated']));
        $this->line('Source values created: ' . number_format($summary['source_values_created']));
        $this->line('Source values updated: ' . number_format($summary['source_values_updated']));
        $this->line('Source values preserved (unresolved options): ' . number_format($summary['source_values_preserved']));
        $this->line('Structured keys/findings: ' . number_format($summary['structured_keys']));
        $this->line('Unstructured keys/findings: ' . number_format($summary['unstructured_keys']));

        if ($dryRun) {
            $this->warn('Dry run only: no dynamic classifications were written.');
        }

        $this->writeTranslationWorkbenchReport(summary: $summary);

        return self::SUCCESS;
    }

    /** Keep PostgreSQL discovery inserts/updates/deletes out of this classification pass. */
    private function lockDiscoveryInventory(): void
    {
        if (DB::transactionLevel() < 1) {
            throw new \LogicException('Discovery inventory locking requires the classification transaction.');
        }
        if (DB::connection()->getDriverName() === 'pgsql'
            && Schema::hasTable('translation_workbench_option_discoveries')) {
            // Row locks cannot protect the absence of a matching discovery. SHARE also
            // blocks inserts, while permitting ordinary readers, until commit/rollback.
            DB::statement('LOCK TABLE translation_workbench_option_discoveries IN SHARE MODE');
        }
    }

    private function hasRequiredTables(): bool
    {
        return Schema::hasTable('translation_workbench_findings')
            && Schema::hasTable('translation_workbench_keys')
            && Schema::hasTable('translation_workbench_key_findings')
            && Schema::hasTable('translation_workbench_source_files')
            && Schema::hasTable('translation_workbench_dynamic_sources')
            && Schema::hasTable('translation_workbench_dynamic_source_values');
    }

    /**
     * @return Collection<int, object>
     */
    private function dynamicFindingRows(?int $findingId = null): Collection
    {
        $keyLinks = DB::table('translation_workbench_key_findings as links')
            ->join('translation_workbench_keys as linked_keys', 'linked_keys.id', '=', 'links.key_id')
            ->selectRaw('links.finding_id, MIN(links.key_id) as key_id')
            ->where('links.status', 'active')->where('linked_keys.status', '<>', 'obsolete')
            ->groupBy('links.finding_id');

        return DB::table('translation_workbench_findings as findings')
            ->join('translation_workbench_source_files as source_files', 'source_files.id', '=', 'findings.source_file_id')
            ->leftJoinSub($keyLinks, 'key_links', function ($join): void {
                $join->on('key_links.finding_id', '=', 'findings.id');
            })
            ->leftJoin('translation_workbench_keys as keys', 'keys.id', '=', 'key_links.key_id')
            ->where(function ($query): void {
                $query
                    ->where('findings.candidate_type', 'dynamic')
                    ->orWhere('findings.entry_type', 'dynamic')
                    ->orWhere('findings.kind', 'like', 'dynamic%')
                    ->orWhere('findings.dynamic_data_state', 'unstructured')
                    ->orWhere('keys.dynamic_data_state', 'unstructured')
                    ->orWhere('keys.is_dynamic_key', true)
                    ->orWhere('keys.is_dynamic_multi', true);
            })
            ->when($findingId !== null, fn ($query) => $query->where('findings.id', $findingId))
            ->where('findings.status', 'active')
            ->where('source_files.status', 'active')
            ->get([
                'findings.id as finding_id',
                'findings.source_file_id',
                'findings.source_line',
                'findings.raw_expression',
                'findings.suggested_key as finding_suggested_key',
                'findings.found_translation_key',
                'findings.existing_key',
                'findings.dynamic_scope',
                'source_files.path as source_path',
                'keys.id as key_id',
                'keys.translation_key',
                'keys.suggested_key as key_suggested_key',
                'keys.is_dynamic_multi',
            ]);
    }

    /** Lock existing owners before validating the inputs selected for this finding. */
    private function lockClassificationInput(object $row, array $classifications): void
    {
        if (DB::transactionLevel() < 1) {
            throw new \LogicException('Classification input validation requires a write transaction.');
        }

        // Match the source -> finding -> file -> links -> keys order used by source review.
        DB::table('translation_workbench_dynamic_sources')->where('finding_id', $row->finding_id)
            ->where(fn ($query) => $query->whereNull('source_type')->orWhereNotIn('source_type', $this->runtimeSourceTypes()))
            ->orderBy('id')->lockForUpdate()->get();
        DB::table('translation_workbench_findings')->where('id', $row->finding_id)->lockForUpdate()->first();
        DB::table('translation_workbench_source_files')->where('id', $row->source_file_id)->lockForUpdate()->first();
        $links = DB::table('translation_workbench_key_findings')->where('finding_id', $row->finding_id)
            ->orderBy('id')->lockForUpdate()->get();
        DB::table('translation_workbench_keys')->whereIn('id', $links->pluck('key_id'))
            ->orderBy('id')->lockForUpdate()->get();

        $current = $this->dynamicFindingRows((int) $row->finding_id)->first();
        $conflicts = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchDynamicKeyAssignment::class)
            ->conflicts([(int) $row->finding_id]);
        if ($current === null || (array) $current !== (array) $row || $conflicts->isNotEmpty()
            || $this->classificationsForRow($current, lock: true) !== $classifications) {
            throw ValidationException::withMessages([
                'dynamic_source' => 'Classification input for finding '.$row->finding_id.' changed during classification. No classification changes were saved; retry with the current source context.',
            ]);
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function classificationsForRow(object $row, bool $lock = false): array
    {
        $discoveries = $this->matchingOptionDiscoveries($row, $lock);

        if ($discoveries->isEmpty()) {
            return [$this->unknownClassification($row)];
        }

        return $discoveries
            ->map(fn(object $discovery): array => $this->classificationFromDiscovery($row, $discovery))
            ->values()
            ->all();
    }

    /**
     * @return Collection<int, object>
     */
    private function matchingOptionDiscoveries(object $row, bool $lock = false): Collection
    {
        if (! Schema::hasTable('translation_workbench_option_discoveries')) {
            return collect();
        }

        $keys = collect([
            $row->translation_key,
            $row->key_suggested_key,
            $row->finding_suggested_key,
            $row->found_translation_key,
            $row->existing_key,
        ])
            ->filter(static fn(mixed $value): bool => filled($value))
            ->map(static fn(mixed $value): string => (string) $value)
            ->unique()
            ->values();

        $matches = DB::table('translation_workbench_option_discoveries')
            ->where('status', '!=', 'obsolete')
            ->where(function ($query) use ($row, $keys): void {
                $query->where('meta->foundation_assignment->finding_id', (int) $row->finding_id);
                $query->orWhere(function ($query) use ($row): void {
                    $query
                        ->where('source_path', $row->source_path)
                        ->where('source_line', $row->source_line);
                });

                if ($keys->isNotEmpty()) {
                    $query
                        ->orWhereIn('suggested_key', $keys->all())
                        ->orWhereIn('workbench_suggested_key', $keys->all())
                        ->orWhereIn('suggested_dynamic_key', $keys->all());
                }
            })
            ->orderBy('id')
            ->when($lock, fn ($query) => $query->lockForUpdate())
            ->get()->filter(fn ($discovery) => \Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchDynamicOptionSynchronizer::appliesTo(
                $discovery, (int) $row->finding_id, $row->key_id === null ? null : (int) $row->key_id,
            ));

        // A proven assignment supersedes the transitional broad historical matching.
        // Keep those historical observations intact for their own consumers.
        $assigned = $matches->filter(function ($discovery): bool {
            $meta = is_string($discovery->meta) ? json_decode($discovery->meta, true) : $discovery->meta;
            return ($meta['foundation_assignment']['status'] ?? null) === 'matched';
        });

        return $assigned->isNotEmpty() ? $assigned : $matches;
    }

    /**
     * @return array<string, mixed>
     */
    private function unknownClassification(object $row): array
    {
        $fingerprint = $this->fingerprintFactory->signature([
            'dynamic-source',
            'unknown',
            (string) $row->finding_id,
            (string) $row->key_id,
            (string) $row->source_path,
            (string) $row->source_line,
            (string) $row->raw_expression,
        ]);

        return [
            'fingerprint' => $fingerprint,
            'classification' => 'unknown',
            'cardinality' => 'unknown',
            'origin' => 'unknown',
            'source_type' => null,
            'source_reference' => null,
            'source_expression' => $row->raw_expression,
            'dynamic_scope' => $row->dynamic_scope,
            'values_count' => 0,
            'confidence' => 'low',
            'status' => 'needs_review',
            'option_discovery_id' => null,
            'options' => [],
            'meta' => [
                'source' => 'translation-workbench:classify-dynamic-values',
                'reason' => 'no_matching_option_discovery',
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function classificationFromDiscovery(object $row, object $discovery): array
    {
        $options = $this->optionsFromDiscovery($discovery);
        $valuesCount = count($options);
        $cardinality = match (true) {
            $valuesCount === 1 => 'single',
            $valuesCount > 1 => 'multi',
            default => 'unknown',
        };
        $origin = $this->originFromSourceType((string) $discovery->source_type);
        $classification = $cardinality !== 'unknown' && $origin !== 'unknown'
            ? $cardinality . '_' . $origin
            : 'unknown';
        $confidence = match (true) {
            $classification !== 'unknown' => 'high',
            $valuesCount > 0 => 'medium',
            default => 'low',
        };

        $fingerprint = $this->fingerprintFactory->signature([
            'dynamic-source',
            (string) $row->finding_id,
            (string) $row->key_id,
            (string) $discovery->id,
            (string) $discovery->source_path,
            (string) $discovery->source_line,
            (string) $discovery->options_variable,
        ]);

        return [
            'fingerprint' => $fingerprint,
            'classification' => $classification,
            'cardinality' => $cardinality,
            'origin' => $origin,
            'source_type' => $discovery->source_type,
            'source_reference' => $this->nullableScalar($discovery->source_reference),
            'source_expression' => $row->raw_expression,
            'dynamic_scope' => $discovery->scope ?: $row->dynamic_scope,
            'values_count' => $valuesCount,
            'confidence' => $confidence,
            'status' => $classification === 'unknown' ? 'needs_review' : 'active',
            'option_discovery_id' => $discovery->id,
            'options' => $options,
            'meta' => [
                'source' => 'translation-workbench:classify-dynamic-values',
                'options_variable' => $discovery->options_variable,
                'key_variable' => $discovery->key_variable,
                'label_variable' => $discovery->label_variable,
                'label_usage' => $discovery->label_usage,
                'suggested_dynamic_key' => $discovery->suggested_dynamic_key,
                'workbench_suggested_key' => $discovery->workbench_suggested_key,
                'option_values_state' => $options === [] ? 'unresolved' : 'observed',
            ],
        ];
    }

    private function originFromSourceType(string $sourceType): string
    {
        $sourceType = strtolower($sourceType);

        return match (true) {
            str_contains($sourceType, 'hardcoded') => 'hardcoded',
            str_contains($sourceType, 'db'),
            str_contains($sourceType, 'database'),
            str_contains($sourceType, 'model'),
            str_contains($sourceType, 'eloquent'),
            str_contains($sourceType, 'query') => 'db',
            default => 'unknown',
        };
    }

    /**
     * @return array<string, string>
     */
    private function optionsFromDiscovery(object $discovery): array
    {
        $options = $discovery->options;

        if (is_string($options)) {
            $decoded = json_decode($options, true);
            $options = is_array($decoded) ? $decoded : [];
        }

        if (! is_array($options)) {
            return [];
        }

        return collect($options)
            ->mapWithKeys(static fn(mixed $label, mixed $key): array => [(string) $key => (string) $label])
            ->all();
    }

    /**
     * @param  array<string, mixed>  $classification
     * @return array{result: string, source_id: int, timeline_events_created: int}
     */
    private function syncDynamicSource(object $row, array $classification, mixed $now): array
    {
        $attributes = [
            'fingerprint' => $classification['fingerprint'],
            'key_id' => $row->key_id,
            'finding_id' => $row->finding_id,
            'option_discovery_id' => $classification['option_discovery_id'],
            'classification' => $classification['classification'],
            'cardinality' => $classification['cardinality'],
            'origin' => $classification['origin'],
            'source_type' => $classification['source_type'],
            'source_reference' => $classification['source_reference'],
            'source_path' => $row->source_path,
            'source_line' => $row->source_line,
            'source_expression' => $classification['source_expression'],
            'dynamic_scope' => $classification['dynamic_scope'],
            'values_count' => $classification['values_count'],
            'confidence' => $classification['confidence'],
            'status' => $classification['status'],
            'last_seen_at' => $now,
            'meta' => $this->jsonValue($classification['meta']),
        ];
        $source = DB::table('translation_workbench_dynamic_sources')
            ->where('fingerprint', $classification['fingerprint'])
            ->lockForUpdate()
            ->first();

        // Position is mutable; finding, key and observation establish the identity.
        // Only recorded finding succession may retain the source identity; equal text is insufficient.
        if ($classification['option_discovery_id'] !== null) {
            $existing = DB::table('translation_workbench_dynamic_sources')
                ->whereIn('finding_id', \Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchFindingVersioner::lineageIds((int) $row->finding_id))
                ->where('key_id', $row->key_id)
                ->where('option_discovery_id', $classification['option_discovery_id'])
                ->where('source_path', $row->source_path)
                ->whereNotIn('source_type', $this->runtimeSourceTypes())
                ->lockForUpdate()->get();
            if ($existing->count() > 1 || ($source !== null && $existing->count() === 1 && $source->id !== $existing->sole()->id)) {
                throw new \LogicException('Ambiguous dynamic source identity for finding '.$row->finding_id
                    .', key '.$row->key_id.', observation '.$classification['option_discovery_id']
                    .'; source IDs: '.$existing->pluck('id')->implode(', '));
            }
            $source ??= $existing->first();
        }

        if (! $source) {
            $sourceId = (int) DB::table('translation_workbench_dynamic_sources')->insertGetId([
                ...$attributes,
                'fingerprint' => $classification['fingerprint'],
                'first_seen_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $this->recordTimelineEvent($row, 'dynamic_source_classified', null, $attributes);

            return [
                'result' => 'sources_created',
                'source_id' => $sourceId,
                'timeline_events_created' => 1,
            ];
        }

        $oldValues = collect($attributes)
            ->keys()
            ->mapWithKeys(static fn(string $key): array => [$key => $source->{$key} ?? null])
            ->all();
        $changedValues = collect($attributes)
            ->filter(fn(mixed $value, string $key): bool => $this->normalizeComparable($oldValues[$key] ?? null) !== $this->normalizeComparable($value))
            ->all();

        if ($changedValues === []) {
            return [
                'result' => 'sources_unchanged',
                'source_id' => (int) $source->id,
                'timeline_events_created' => 0,
            ];
        }

        DB::table('translation_workbench_dynamic_sources')
            ->where('id', $source->id)
            ->update([
                ...$changedValues,
                'updated_at' => $now,
            ]);
        $this->recordTimelineEvent($row, 'dynamic_source_classification_changed', collect($oldValues)->only(array_keys($changedValues))->all(), $changedValues);

        return [
            'result' => 'sources_updated',
            'source_id' => (int) $source->id,
            'timeline_events_created' => 1,
        ];
    }

    /**
     * @param  array<string, mixed>  $classification
     * @return array{source_values_created: int, source_values_updated: int, source_values_unchanged: int, source_values_removed: int, source_values_preserved: int, timeline_events_created: int}
     */
    private function syncDynamicSourceValues(int $dynamicSourceId, object $row, array $classification): array
    {
        $summary = [
            'source_values_created' => 0,
            'source_values_updated' => 0,
            'source_values_unchanged' => 0,
            'source_values_removed' => 0,
            'source_values_preserved' => 0,
            'timeline_events_created' => 0,
        ];
        // The current discovery contract cannot prove an authoritative empty set.
        // Keep the last observed values while the source itself needs review.
        if ($classification['options'] === []) {
            $summary['source_values_preserved'] = DB::table('translation_workbench_dynamic_source_values')
                ->where('dynamic_source_id', $dynamicSourceId)->count();

            return $summary;
        }
        $seenValueKeys = [];
        $oldValues = [];
        $newValues = [];
        $translationKey = $row->translation_key ?: $row->key_suggested_key ?: $row->finding_suggested_key;

        foreach ($classification['options'] as $valueKey => $nativeLabel) {
            $seenValueKeys[] = (string) $valueKey;
            $attributes = [
                'native_label' => $nativeLabel,
                'origin' => $classification['origin'],
                'translation_key' => $translationKey,
                'status' => 'active',
                'meta' => $this->jsonValue([
                    'source' => 'translation-workbench:classify-dynamic-values',
                    'classification' => $classification['classification'],
                ]),
            ];
            $existing = DB::table('translation_workbench_dynamic_source_values')
                ->where('dynamic_source_id', $dynamicSourceId)
                ->where('value_key', (string) $valueKey)
                ->lockForUpdate()->first();

            if (! $existing) {
                DB::table('translation_workbench_dynamic_source_values')->insert([
                    ...$attributes,
                    'dynamic_source_id' => $dynamicSourceId,
                    'value_key' => (string) $valueKey,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $oldValues[(string) $valueKey] = null;
                $newValues[(string) $valueKey] = $attributes;
                $summary['source_values_created']++;

                continue;
            }

            $changedValues = collect($attributes)
                ->filter(fn(mixed $value, string $key): bool => $this->normalizeComparable($existing->{$key} ?? null) !== $this->normalizeComparable($value))
                ->all();

            if ($changedValues === []) {
                $summary['source_values_unchanged']++;

                continue;
            }

            DB::table('translation_workbench_dynamic_source_values')
                ->where('id', $existing->id)
                ->update([
                    ...collect($changedValues)
                        ->mapWithKeys(fn(mixed $value, string $key): array => [$key => $value])
                        ->all(),
                    'updated_at' => now(),
                ]);
            $oldValues[(string) $valueKey] = collect((array) $existing)->only(array_keys($attributes))->all();
            $newValues[(string) $valueKey] = $attributes;
            $summary['source_values_updated']++;
        }

        $removed = DB::table('translation_workbench_dynamic_source_values')
            ->where('dynamic_source_id', $dynamicSourceId)
            ->when($seenValueKeys !== [], fn($query) => $query->whereNotIn('value_key', $seenValueKeys))
            ->lockForUpdate()->get();
        foreach ($removed as $value) {
            $oldValues[(string) $value->value_key] = collect((array) $value)
                ->only(['native_label', 'origin', 'translation_key', 'status', 'meta'])->all();
            $newValues[(string) $value->value_key] = null;
        }
        $summary['source_values_removed'] += DB::table('translation_workbench_dynamic_source_values')
            ->whereIn('id', $removed->pluck('id'))->delete();

        if ($oldValues !== []) {
            $this->recordTimelineEvent($row, 'dynamic_source_options_reconciled',
                ['values' => $oldValues], ['values' => $newValues],
                ['dynamic_source_id' => $dynamicSourceId, 'option_discovery_id' => $classification['option_discovery_id']],
            );
            $summary['timeline_events_created']++;
        }

        return $summary;
    }

    private function syncDynamicDataState(object $row, string $state): int
    {
        $updates = 0;

        if ($row->key_id && DB::table('translation_workbench_keys')
            ->where('id', $row->key_id)
            ->where(function ($query) use ($state): void {
                $query->whereNull('dynamic_data_state')->orWhere('dynamic_data_state', '!=', $state);
            })
            ->update(['dynamic_data_state' => $state, 'updated_at' => now()])) {
            $updates++;
        }

        if (DB::table('translation_workbench_findings')
            ->where('id', $row->finding_id)
            ->where(function ($query) use ($state): void {
                $query->whereNull('dynamic_data_state')->orWhere('dynamic_data_state', '!=', $state);
            })
            ->update(['dynamic_data_state' => $state, 'updated_at' => now()])) {
            $updates++;
        }

        return $updates;
    }

    /** @return Collection<int, object> */
    private function retirementSourceSnapshot(): Collection
    {
        return DB::table('translation_workbench_dynamic_sources')
            ->where('status', '!=', 'obsolete')
            ->where(fn ($query) => $query->whereNull('source_type')->orWhereNotIn('source_type', $this->runtimeSourceTypes()))
            ->orderBy('id')->get()->keyBy('id')->map(function (object $source): object {
                $source->retirement_context = $this->retirementContext($source);
                return $source;
            });
    }

    /** Capture the owners even when inactive: inactivity can be a valid retirement reason. */
    private function retirementContext(object $source, bool $lock = false): array
    {
        $read = static function (string $table, array $ids) use ($lock): array {
            return DB::table('translation_workbench_'.$table)->whereIn('id', array_filter($ids))
                ->orderBy('id')->when($lock, fn ($query) => $query->lockForUpdate())->get()
                ->mapWithKeys(fn (object $row): array => [(int) $row->id => (array) $row])->all();
        };
        $findings = $read('findings', [$source->finding_id]);
        $files = $read('source_files', array_column($findings, 'source_file_id'));
        $links = DB::table('translation_workbench_key_findings')->where('finding_id', $source->finding_id)
            ->orderBy('id')->when($lock, fn ($query) => $query->lockForUpdate())->get()
            ->mapWithKeys(fn (object $row): array => [(int) $row->id => (array) $row])->all();
        $keys = $read('keys', array_unique([$source->key_id, ...array_column($links, 'key_id')]));
        $discoveries = Schema::hasTable('translation_workbench_option_discoveries')
            ? $read('option_discoveries', [$source->option_discovery_id]) : [];

        // Timestamps do not define ownership or classification; our own state writes update them.
        return collect(compact('findings', 'files', 'links', 'keys', 'discoveries'))
            ->map(fn (array $rows): array => array_map(static function (array $row): array {
                unset($row['updated_at'], $row['created_at']);
                return $row;
            }, $rows))->all();
    }

    /** Account only for the state fields explicitly written by this transaction. */
    private function retainOwnClassificationState(Collection $sources, object $row, string $state): void
    {
        foreach ($sources as $source) {
            if (isset($source->retirement_context['findings'][$row->finding_id])) {
                $source->retirement_context['findings'][$row->finding_id]['dynamic_data_state'] = $state;
            }
            if ($row->key_id !== null && isset($source->retirement_context['keys'][$row->key_id])) {
                $source->retirement_context['keys'][$row->key_id]['dynamic_data_state'] = $state;
            }
        }
    }

    /**
     * @param  array<int, int>  $seenSourceIds
     * @param  Collection<int, object>  $initialSources
     */
    private function obsoleteUnseenSources(array $seenSourceIds, Collection $initialSources): int
    {
        if (DB::transactionLevel() < 1) {
            throw new \LogicException('Source retirement requires the classification transaction.');
        }

        $candidates = $initialSources->reject(
            fn (object $source): bool => in_array((int) $source->id, $seenSourceIds, true),
        );
        if ($candidates->isEmpty()) {
            return 0;
        }

        // Read again after acquiring the locks: another writer may have changed a source
        // while classification was running. Never retire that newer state from an old plan.
        $current = DB::table('translation_workbench_dynamic_sources')
            ->whereIn('id', $candidates->keys()->all())->orderBy('id')
            ->lockForUpdate()->get()->keyBy('id');
        foreach ($candidates as $id => $snapshot) {
            $expected = clone $snapshot;
            unset($expected->retirement_context);
            if (! $current->has($id) || (array) $current->get($id) !== (array) $expected
                || $this->retirementContext($expected, lock: true) !== $snapshot->retirement_context) {
                throw ValidationException::withMessages([
                    'dynamic_source' => 'Dynamic source '.$id.' changed during classification. No classification changes were saved; retry with the current source context.',
                ]);
            }
        }

        $retired = (int) DB::table('translation_workbench_dynamic_sources')
            ->whereIn('id', $candidates->keys()->all())
            ->update(['status' => 'obsolete', 'updated_at' => now()]);
        foreach ($current as $source) {
            $this->recordTimelineEvent($source, 'dynamic_source_obsoleted',
                ['status' => $source->status], ['status' => 'obsolete'], [
                    'dynamic_source_id' => (int) $source->id,
                    'option_discovery_id' => $source->option_discovery_id,
                    'fingerprint' => $source->fingerprint,
                    'reason' => 'not_classified_in_current_pass',
                ]);
        }

        return $retired;
    }

    /**
     * Runtime sources are observed while the app renders database-backed option lists. They are not
     * tied to source-code scan presence, so classifier runs must not obsolete them merely because
     * no matching code-source fingerprint was produced in the current scanner pass.
     *
     * @return array<int, string>
     */
    private function runtimeSourceTypes(): array
    {
        return [
            'runtime_options',
            'runtime_db_options',
        ];
    }

    /**
     * @param  array<string, mixed>|null  $oldValues
     * @param  array<string, mixed>|null  $newValues
     */
    private function recordTimelineEvent(object $row, string $eventType, ?array $oldValues, ?array $newValues, array $context = []): void
    {
        $this->timelineRecorder->record(
            eventType: $eventType,
            key: $row->key_id ? TranslationWorkbenchKey::query()->find($row->key_id) : null,
            finding: TranslationWorkbenchFinding::query()->find($row->finding_id),
            oldValues: $oldValues,
            newValues: $newValues,
            context: [
                'source' => 'translation-workbench:classify-dynamic-values',
                'source_path' => $row->source_path,
                'source_line' => $row->source_line,
                ...$context,
            ],
        );
    }

    private function normalizeComparable(mixed $value): mixed
    {
        if (is_array($value)) {
            ksort($value);

            return $value;
        }

        if (is_object($value)) {
            return json_decode(json_encode($value), true);
        }

        return $value;
    }

    private function nullableScalar(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if (is_scalar($value)) {
            $value = trim((string) $value);

            return $value !== '' ? $value : null;
        }

        return json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    private function jsonValue(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if (is_string($value)) {
            return $value;
        }

        return json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
}
