<?php

// packages/gunreip/laravel-translation-workbench/src/Console/ResolveUnknownDynamicSources.php

// php artisan translation-workbench:resolve-unknown-dynamic-sources
// php artisan translation-workbench:resolve-unknown-dynamic-sources --dry-run

namespace Gunreip\TranslationWorkbench\Console;

use Gunreip\TranslationWorkbench\Console\Concerns\WritesTranslationWorkbenchReports;
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchTimelineRecorder;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchFinding;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchKey;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

#[Signature('translation-workbench:resolve-unknown-dynamic-sources
    {--dry-run : Report resolvable unknown dynamic sources without writing database rows.}')]
#[Description('Resolve unknown dynamic sources by matching option discoveries and writing concrete dynamic source values.')]
class ResolveUnknownDynamicSources extends Command
{
    use WritesTranslationWorkbenchReports;

    public function __construct(
        private readonly TranslationWorkbenchTimelineRecorder $timelineRecorder,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        if (! $this->hasRequiredTables()) {
            $this->error('Dynamic source tables are missing. Run the workbench migrations first.');
            $this->writeTranslationWorkbenchReport();

            return self::FAILURE;
        }

        $dryRun = (bool) $this->option('dry-run');
        $summary = [
            'unknown_sources' => 0,
            'sources_resolved' => 0,
            'sources_still_unknown' => 0,
            'source_values_created' => 0,
            'source_values_updated' => 0,
            'source_values_unchanged' => 0,
            'dynamic_states_structured' => 0,
            'runtime_sources_matched' => 0,
            'runtime_source_links_created' => 0,
            'runtime_source_links_updated' => 0,
            'runtime_source_links_unchanged' => 0,
            'runtime_sources_reactivated' => 0,
            'timeline_events_created' => 0,
        ];

        $sources = $this->unknownSources();
        $summary['unknown_sources'] = $sources->count();

        foreach ($sources as $source) {
            $discovery = $this->bestDiscoveryForSource($source);

            if (! $discovery) {
                $this->tryRuntimeSourceMatch($source, $summary, $dryRun);

                continue;
            }

            $options = $this->optionsFromDiscovery($discovery);

            if ($options === []) {
                $this->tryRuntimeSourceMatch($source, $summary, $dryRun);

                continue;
            }

            $attributes = $this->resolvedAttributes($source, $discovery, $options);

            if ($dryRun) {
                $summary['sources_resolved']++;

                continue;
            }

            $oldValues = collect($attributes)
                ->keys()
                ->mapWithKeys(static fn(string $key): array => [$key => $source->{$key} ?? null])
                ->all();
            $changedValues = collect($attributes)
                ->filter(static fn(mixed $value, string $key): bool => ($oldValues[$key] ?? null) != $value)
                ->all();

            if ($changedValues !== []) {
                DB::table('translation_workbench_dynamic_sources')
                    ->where('id', $source->id)
                    ->update([
                        ...$changedValues,
                        'updated_at' => now(),
                    ]);

                $summary['sources_resolved']++;
                $summary['timeline_events_created'] += $this->recordResolvedEvent($source, $oldValues, $changedValues, $discovery);
            } else {
                $summary['sources_still_unknown']++;
            }

            $valueSummary = $this->syncSourceValues((int) $source->id, $options);
            foreach ($valueSummary as $key => $value) {
                $summary[$key] += $value;
            }

            $summary['dynamic_states_structured'] += $this->syncStructuredState($source);
        }

        $this->components->info('Translation Workbench unknown dynamic source resolver finished.');
        $this->line('Unknown sources: ' . number_format($summary['unknown_sources']));
        $this->line('Sources resolved: ' . number_format($summary['sources_resolved']));
        $this->line('Sources still unknown: ' . number_format($summary['sources_still_unknown']));
        $this->line('Source values created: ' . number_format($summary['source_values_created']));
        $this->line('Source values updated: ' . number_format($summary['source_values_updated']));
        $this->line('Runtime sources matched: ' . number_format($summary['runtime_sources_matched']));
        $this->line('Runtime source links created: ' . number_format($summary['runtime_source_links_created']));
        $this->line('Runtime source links updated: ' . number_format($summary['runtime_source_links_updated']));

        if ($dryRun) {
            $this->warn('Dry run only: no unknown dynamic sources were updated.');
        }

        $this->writeTranslationWorkbenchReport();

        return self::SUCCESS;
    }

    private function hasRequiredTables(): bool
    {
        return Schema::hasTable('translation_workbench_dynamic_sources')
            && Schema::hasTable('translation_workbench_dynamic_source_values')
            && Schema::hasTable('translation_workbench_option_discoveries')
            && Schema::hasTable('translation_workbench_findings')
            && Schema::hasTable('translation_workbench_source_files')
            && Schema::hasTable('translation_workbench_keys')
            && Schema::hasTable('translation_workbench_key_findings');
    }

    /**
     * @return Collection<int, object>
     */
    private function unknownSources(): Collection
    {
        $keyLinks = DB::table('translation_workbench_key_findings')
            ->selectRaw('finding_id, MIN(key_id) as key_id')
            ->where('status', 'active')
            ->groupBy('finding_id');

        return DB::table('translation_workbench_dynamic_sources as sources')
            ->leftJoin('translation_workbench_findings as findings', 'findings.id', '=', 'sources.finding_id')
            ->leftJoin('translation_workbench_source_files as source_files', 'source_files.id', '=', 'findings.source_file_id')
            ->leftJoinSub($keyLinks, 'key_links', function ($join): void {
                $join->on('key_links.finding_id', '=', 'findings.id');
            })
            ->leftJoin('translation_workbench_keys as keys', 'keys.id', '=', 'key_links.key_id')
            ->where('sources.status', '<>', 'obsolete')
            ->where(function ($query): void {
                $query
                    ->where('sources.classification', 'unknown')
                    ->orWhere('sources.cardinality', 'unknown')
                    ->orWhere('sources.origin', 'unknown');
            })
            ->orderBy('sources.id')
            ->get([
                'sources.*',
                'findings.suggested_key as finding_suggested_key',
                'findings.found_translation_key',
                'findings.existing_key',
                'findings.raw_expression as finding_raw_expression',
                'source_files.path as finding_source_path',
                'keys.translation_key',
                'keys.suggested_key as key_suggested_key',
            ]);
    }

    private function bestDiscoveryForSource(object $source): ?object
    {
        $keys = collect([
            $source->translation_key,
            $source->key_suggested_key,
            $source->finding_suggested_key,
            $source->found_translation_key,
            $source->existing_key,
        ])
            ->filter(static fn(mixed $value): bool => filled($value))
            ->map(static fn(mixed $value): string => (string) $value)
            ->unique()
            ->values();

        $query = DB::table('translation_workbench_option_discoveries')
            ->where('status', '<>', 'obsolete')
            ->where(function ($query) use ($source, $keys): void {
                if ($keys->isNotEmpty()) {
                    $query
                        ->orWhereIn('suggested_key', $keys->all())
                        ->orWhereIn('workbench_suggested_key', $keys->all())
                        ->orWhereIn('suggested_dynamic_key', $keys->all());
                }

                if (filled($source->dynamic_scope)) {
                    $query->orWhere('scope', $source->dynamic_scope);
                }

                if (filled($source->finding_source_path ?? $source->source_path)) {
                    $query->orWhere('source_path', (string) ($source->finding_source_path ?? $source->source_path));
                }
            });

        return $query
            ->get()
            ->sortByDesc(fn(object $discovery): int => $this->discoveryScore($source, $discovery, $keys))
            ->first();
    }

    /**
     * @param  array<string, int>  $summary
     */
    private function tryRuntimeSourceMatch(object $source, array &$summary, bool $dryRun): void
    {
        $runtimeSource = $this->bestRuntimeSourceForSource($source);

        if (! $runtimeSource) {
            $summary['sources_still_unknown']++;

            return;
        }

        $summary['runtime_sources_matched']++;

        if ($dryRun) {
            return;
        }

        $linkSummary = $this->syncRuntimeSourceLink($runtimeSource, $source);
        foreach ($linkSummary as $key => $value) {
            $summary[$key] += $value;
        }
    }

    private function bestRuntimeSourceForSource(object $source): ?object
    {
        if (blank($source->dynamic_scope)) {
            return null;
        }

        return DB::table('translation_workbench_dynamic_sources as runtime_sources')
            ->leftJoin('translation_workbench_keys as runtime_keys', 'runtime_keys.id', '=', 'runtime_sources.key_id')
            ->whereIn('runtime_sources.source_type', $this->runtimeSourceTypes())
            ->where('runtime_sources.dynamic_scope', $source->dynamic_scope)
            ->where('runtime_sources.values_count', '>', 0)
            ->whereExists(function ($query): void {
                $query
                    ->selectRaw('1')
                    ->from('translation_workbench_dynamic_source_values as source_values')
                    ->whereColumn('source_values.dynamic_source_id', 'runtime_sources.id')
                    ->whereIn('source_values.status', ['active', 'stale']);
            })
            ->orderByRaw("CASE WHEN runtime_sources.status = 'active' THEN 0 WHEN runtime_sources.status = 'needs_review' THEN 1 ELSE 2 END")
            ->orderByDesc('runtime_sources.values_count')
            ->orderByDesc('runtime_sources.last_seen_at')
            ->orderBy('runtime_sources.id')
            ->first([
                'runtime_sources.*',
                'runtime_keys.suggested_key as runtime_suggested_key',
                'runtime_keys.translation_key as runtime_translation_key',
            ]);
    }

    /**
     * @return array{runtime_source_links_created: int, runtime_source_links_updated: int, runtime_source_links_unchanged: int, runtime_sources_reactivated: int, timeline_events_created: int}
     */
    private function syncRuntimeSourceLink(object $runtimeSource, object $consumerSource): array
    {
        $summary = [
            'runtime_source_links_created' => 0,
            'runtime_source_links_updated' => 0,
            'runtime_source_links_unchanged' => 0,
            'runtime_sources_reactivated' => 0,
            'timeline_events_created' => 0,
        ];
        $candidateReference = 'dynamic_source:' . $consumerSource->id;
        $candidateValues = DB::table('translation_workbench_dynamic_source_values')
            ->where('dynamic_source_id', $runtimeSource->id)
            ->whereIn('status', ['active', 'stale'])
            ->orderBy('value_key')
            ->pluck('native_label', 'value_key')
            ->mapWithKeys(static fn(mixed $label, mixed $key): array => [(string) $key => (string) $label])
            ->all();

        $attributes = [
            'key_id' => $consumerSource->key_id,
            'finding_id' => $consumerSource->finding_id,
            'suggested_key' => $consumerSource->key_suggested_key ?: $consumerSource->finding_suggested_key,
            'dynamic_scope' => $consumerSource->dynamic_scope,
            'source_expression' => $consumerSource->source_expression,
            'candidate_source_type' => 'related_dynamic_source',
            'candidate_reference' => $candidateReference,
            'candidate_values_count' => count($candidateValues),
            'candidate_values' => json_encode($candidateValues, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'confidence' => 'scope',
            'review_status' => 'pending',
            'status' => 'active',
            'meta' => json_encode([
                'source' => 'translation-workbench:resolve-unknown-dynamic-sources',
                'match' => 'runtime_source_by_dynamic_scope',
                'runtime_source_id' => $runtimeSource->id,
                'runtime_suggested_key' => $runtimeSource->runtime_suggested_key ?? null,
                'runtime_translation_key' => $runtimeSource->runtime_translation_key ?? null,
                'consumer_source_id' => $consumerSource->id,
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        ];

        if ($runtimeSource->status === 'obsolete') {
            DB::table('translation_workbench_dynamic_sources')
                ->where('id', $runtimeSource->id)
                ->update([
                    'status' => 'active',
                    'updated_at' => now(),
                ]);
            $summary['runtime_sources_reactivated']++;
        }

        $existing = DB::table('translation_workbench_dynamic_source_candidates')
            ->where('dynamic_source_id', $runtimeSource->id)
            ->where('candidate_source_type', 'related_dynamic_source')
            ->where('candidate_reference', $candidateReference)
            ->first();

        if (! $existing) {
            DB::table('translation_workbench_dynamic_source_candidates')->insert([
                'dynamic_source_id' => $runtimeSource->id,
                ...$attributes,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $summary['runtime_source_links_created']++;
            $summary['timeline_events_created'] += $this->recordRuntimeLinkEvent($runtimeSource, $consumerSource, null, $attributes);

            return $summary;
        }

        $changedValues = collect($attributes)
            ->reject(static fn(mixed $value, string $key): bool => $key === 'review_status' && $existing->review_status === 'confirmed')
            ->filter(static fn(mixed $value, string $key): bool => ($existing->{$key} ?? null) != $value)
            ->all();

        if ($changedValues === []) {
            $summary['runtime_source_links_unchanged']++;

            return $summary;
        }

        DB::table('translation_workbench_dynamic_source_candidates')
            ->where('id', $existing->id)
            ->update([
                ...$changedValues,
                'updated_at' => now(),
            ]);
        $summary['runtime_source_links_updated']++;
        $summary['timeline_events_created'] += $this->recordRuntimeLinkEvent(
            $runtimeSource,
            $consumerSource,
            collect((array) $existing)->only(array_keys($changedValues))->all(),
            $changedValues,
        );

        return $summary;
    }

    /**
     * @param  array<string, mixed>|null  $oldValues
     * @param  array<string, mixed>  $newValues
     */
    private function recordRuntimeLinkEvent(object $runtimeSource, object $consumerSource, ?array $oldValues, array $newValues): int
    {
        $finding = $consumerSource->finding_id
            ? TranslationWorkbenchFinding::query()->find($consumerSource->finding_id)
            : null;
        $key = $consumerSource->key_id
            ? TranslationWorkbenchKey::query()->find($consumerSource->key_id)
            : null;

        if (! $finding && ! $key) {
            return 0;
        }

        $this->timelineRecorder->record(
            eventType: $oldValues === null ? 'dynamic_runtime_source_link_suggested' : 'dynamic_runtime_source_link_changed',
            key: $key,
            finding: $finding,
            oldValues: $oldValues,
            newValues: $newValues,
            context: [
                'source' => 'translation-workbench:resolve-unknown-dynamic-sources',
                'runtime_source_id' => $runtimeSource->id,
                'consumer_source_id' => $consumerSource->id,
                'dynamic_scope' => $consumerSource->dynamic_scope,
            ],
        );

        return 1;
    }

    /**
     * @param  Collection<int, string>  $keys
     */
    private function discoveryScore(object $source, object $discovery, Collection $keys): int
    {
        $score = 0;

        if ($keys->contains((string) $discovery->suggested_key)
            || $keys->contains((string) $discovery->workbench_suggested_key)
            || $keys->contains((string) $discovery->suggested_dynamic_key)) {
            $score += 100;
        }

        if (filled($source->dynamic_scope) && $source->dynamic_scope === $discovery->scope) {
            $score += 50;
        }

        if (($source->finding_source_path ?? $source->source_path) === $discovery->source_path) {
            $score += 25;
        }

        if ($source->source_line !== null && $discovery->source_line !== null) {
            $score += max(0, 20 - min(20, abs((int) $source->source_line - (int) $discovery->source_line)));
        }

        if ((int) $discovery->options_count > 0) {
            $score += 20;
        }

        return $score;
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
            ->filter(static fn(string $label, string $key): bool => $key !== '')
            ->all();
    }

    /**
     * @param  array<string, string>  $options
     * @return array<string, mixed>
     */
    private function resolvedAttributes(object $source, object $discovery, array $options): array
    {
        $valuesCount = count($options);
        $cardinality = $valuesCount > 1 ? 'multi' : 'single';
        $origin = $this->originFromSourceType((string) $discovery->source_type);
        $classification = $origin !== 'unknown' ? $cardinality . '_' . $origin : 'unknown';

        return [
            'option_discovery_id' => $discovery->id,
            'classification' => $classification,
            'cardinality' => $cardinality,
            'origin' => $origin,
            'source_type' => $discovery->source_type,
            'source_reference' => $discovery->source_reference,
            'source_path' => $source->finding_source_path ?? $source->source_path,
            'source_line' => $source->source_line,
            'dynamic_scope' => $discovery->scope ?: $source->dynamic_scope,
            'values_count' => $valuesCount,
            'confidence' => $classification !== 'unknown' ? 'medium' : 'low',
            'status' => $classification !== 'unknown' ? 'active' : 'needs_review',
            'meta' => json_encode([
                'source' => 'translation-workbench:resolve-unknown-dynamic-sources',
                'matched_option_discovery_id' => $discovery->id,
                'matched_option_discovery_line' => $discovery->source_line,
                'matched_option_discovery_path' => $discovery->source_path,
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
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
     * @param  array<string, string>  $options
     * @return array{source_values_created: int, source_values_updated: int, source_values_unchanged: int}
     */
    private function syncSourceValues(int $dynamicSourceId, array $options): array
    {
        $summary = [
            'source_values_created' => 0,
            'source_values_updated' => 0,
            'source_values_unchanged' => 0,
        ];

        foreach ($options as $valueKey => $nativeLabel) {
            $existing = DB::table('translation_workbench_dynamic_source_values')
                ->where('dynamic_source_id', $dynamicSourceId)
                ->where('value_key', $valueKey)
                ->first();

            $attributes = [
                'native_label' => $nativeLabel,
                'origin' => 'discovery',
                'status' => 'active',
            ];

            if (! $existing) {
                DB::table('translation_workbench_dynamic_source_values')->insert([
                    'dynamic_source_id' => $dynamicSourceId,
                    'value_key' => $valueKey,
                    ...$attributes,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $summary['source_values_created']++;

                continue;
            }

            $changedValues = collect($attributes)
                ->filter(static fn(mixed $value, string $key): bool => ($existing->{$key} ?? null) != $value)
                ->all();

            if ($changedValues === []) {
                $summary['source_values_unchanged']++;

                continue;
            }

            DB::table('translation_workbench_dynamic_source_values')
                ->where('id', $existing->id)
                ->update([
                    ...$changedValues,
                    'updated_at' => now(),
                ]);
            $summary['source_values_updated']++;
        }

        return $summary;
    }

    private function syncStructuredState(object $source): int
    {
        $changed = 0;

        if ($source->finding_id && Schema::hasColumn('translation_workbench_findings', 'dynamic_data_state')) {
            $changed += DB::table('translation_workbench_findings')
                ->where('id', $source->finding_id)
                ->where('dynamic_data_state', '<>', 'structured')
                ->update(['dynamic_data_state' => 'structured', 'updated_at' => now()]);
        }

        if ($source->key_id && Schema::hasColumn('translation_workbench_keys', 'dynamic_data_state')) {
            $changed += DB::table('translation_workbench_keys')
                ->where('id', $source->key_id)
                ->where('dynamic_data_state', '<>', 'structured')
                ->update(['dynamic_data_state' => 'structured', 'updated_at' => now()]);
        }

        return $changed;
    }

    /**
     * @param  array<string, mixed>  $oldValues
     * @param  array<string, mixed>  $newValues
     */
    private function recordResolvedEvent(object $source, array $oldValues, array $newValues, object $discovery): int
    {
        $finding = $source->finding_id
            ? TranslationWorkbenchFinding::query()->find($source->finding_id)
            : null;
        $key = $source->key_id
            ? TranslationWorkbenchKey::query()->find($source->key_id)
            : null;

        if (! $finding && ! $key) {
            return 0;
        }

        $this->timelineRecorder->record(
            eventType: 'dynamic_source_unknown_resolved',
            key: $key,
            finding: $finding,
            oldValues: collect($oldValues)->only(array_keys($newValues))->all(),
            newValues: $newValues,
            context: [
                'source' => 'translation-workbench:resolve-unknown-dynamic-sources',
                'dynamic_source_id' => $source->id,
                'option_discovery_id' => $discovery->id,
            ],
        );

        return 1;
    }
}
