<?php

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
        if (! $this->hasRequiredTables()) {
            $this->error('Dynamic classification tables are missing. Run the workbench migrations first.');
            $this->writeTranslationWorkbenchReport();

            return self::FAILURE;
        }

        $dryRun = (bool) $this->option('dry-run');
        $now = now();
        $summary = [
            'dynamic_findings' => 0,
            'sources_created' => 0,
            'sources_updated' => 0,
            'sources_unchanged' => 0,
            'sources_obsoleted' => 0,
            'source_values_created' => 0,
            'source_values_updated' => 0,
            'source_values_unchanged' => 0,
            'source_values_removed' => 0,
            'structured_keys' => 0,
            'unstructured_keys' => 0,
            'timeline_events_created' => 0,
        ];

        $rows = $this->dynamicFindingRows();
        $summary['dynamic_findings'] = $rows->count();
        $seenFingerprints = [];

        if (! $dryRun) {
            DB::table('translation_workbench_dynamic_sources')->update(['status' => 'obsolete']);
        }

        foreach ($rows as $row) {
            $classifications = $this->classificationsForRow($row);
            $rowHasStructuredSource = false;

            foreach ($classifications as $classification) {
                $seenFingerprints[] = $classification['fingerprint'];
                $rowHasStructuredSource = $rowHasStructuredSource || $classification['classification'] !== 'unknown';

                if ($dryRun) {
                    continue;
                }

                $sourceResult = $this->syncDynamicSource($row, $classification, $now);
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
            }
        }

        if (! $dryRun) {
            $summary['sources_obsoleted'] = $this->obsoleteUnseenSources($seenFingerprints);
        }

        $this->components->info('Translation Workbench dynamic value classification finished.');
        $this->line('Dynamic findings: ' . number_format($summary['dynamic_findings']));
        $this->line('Sources created: ' . number_format($summary['sources_created']));
        $this->line('Sources updated: ' . number_format($summary['sources_updated']));
        $this->line('Source values created: ' . number_format($summary['source_values_created']));
        $this->line('Source values updated: ' . number_format($summary['source_values_updated']));
        $this->line('Structured keys/findings: ' . number_format($summary['structured_keys']));
        $this->line('Unstructured keys/findings: ' . number_format($summary['unstructured_keys']));

        if ($dryRun) {
            $this->warn('Dry run only: no dynamic classifications were written.');
        }

        $this->writeTranslationWorkbenchReport();

        return self::SUCCESS;
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
    private function dynamicFindingRows(): Collection
    {
        $keyLinks = DB::table('translation_workbench_key_findings')
            ->selectRaw('finding_id, MIN(key_id) as key_id')
            ->where('status', 'active')
            ->groupBy('finding_id');

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
            ->where('findings.status', 'active')
            ->get([
                'findings.id as finding_id',
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

    /**
     * @return array<int, array<string, mixed>>
     */
    private function classificationsForRow(object $row): array
    {
        $discoveries = $this->matchingOptionDiscoveries($row);

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
    private function matchingOptionDiscoveries(object $row): Collection
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

        return DB::table('translation_workbench_option_discoveries')
            ->where('status', '!=', 'obsolete')
            ->where(function ($query) use ($row, $keys): void {
                $query->where(function ($query) use ($row): void {
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
            ->get();
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
            ->first();

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
     * @return array{source_values_created: int, source_values_updated: int, source_values_unchanged: int, source_values_removed: int}
     */
    private function syncDynamicSourceValues(int $dynamicSourceId, object $row, array $classification): array
    {
        $summary = [
            'source_values_created' => 0,
            'source_values_updated' => 0,
            'source_values_unchanged' => 0,
            'source_values_removed' => 0,
        ];
        $seenValueKeys = [];
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
                ->first();

            if (! $existing) {
                DB::table('translation_workbench_dynamic_source_values')->insert([
                    ...$attributes,
                    'dynamic_source_id' => $dynamicSourceId,
                    'value_key' => (string) $valueKey,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
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
            $summary['source_values_updated']++;
        }

        $deleted = DB::table('translation_workbench_dynamic_source_values')
            ->where('dynamic_source_id', $dynamicSourceId)
            ->when($seenValueKeys !== [], fn($query) => $query->whereNotIn('value_key', $seenValueKeys))
            ->delete();
        $summary['source_values_removed'] += (int) $deleted;

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

    /**
     * @param  array<int, string>  $seenFingerprints
     */
    private function obsoleteUnseenSources(array $seenFingerprints): int
    {
        return (int) DB::table('translation_workbench_dynamic_sources')
            ->where('status', '!=', 'obsolete')
            ->when($seenFingerprints !== [], fn($query) => $query->whereNotIn('fingerprint', array_values(array_unique($seenFingerprints))))
            ->update([
                'status' => 'obsolete',
                'updated_at' => now(),
            ]);
    }

    /**
     * @param  array<string, mixed>|null  $oldValues
     * @param  array<string, mixed>|null  $newValues
     */
    private function recordTimelineEvent(object $row, string $eventType, ?array $oldValues, ?array $newValues): void
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
