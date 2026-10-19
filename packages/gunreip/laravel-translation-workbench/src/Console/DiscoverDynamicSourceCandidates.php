<?php

// packages/gunreip/laravel-translation-workbench/src/Console/DiscoverDynamicSourceCandidates.php

// php artisan translation-workbench:discover-dynamic-source-candidates
// php artisan translation-workbench:discover-dynamic-source-candidates --dry-run

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

#[Signature('translation-workbench:discover-dynamic-source-candidates
    {--dry-run : Report dynamic source candidates without writing database rows.}')]
#[Description('Discover reviewable source candidates for unresolved dynamic translation sources.')]
class DiscoverDynamicSourceCandidates extends Command
{
    use WritesTranslationWorkbenchReports;

    public function __construct(private readonly TranslationWorkbenchTimelineRecorder $timelineRecorder)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        if (! $this->hasRequiredTables()) {
            $this->error('Dynamic source candidate tables are missing. Run the workbench migrations first.');
            $this->writeTranslationWorkbenchReport(summary: ['dry_run' => $dryRun, 'exit_code' => self::FAILURE, 'error' => 'missing_required_tables']);

            return self::FAILURE;
        }

        $summary = [
            'dry_run' => $dryRun,
            'exit_code' => self::SUCCESS,
            'unresolved_sources' => 0,
            'candidates_discovered' => 0,
            'candidates_created' => 0,
            'candidates_updated' => 0,
            'candidates_unchanged' => 0,
            'candidates_preserved' => 0,
            'truncated_value_samples' => 0,
            'timeline_events_created' => 0,
        ];

        $sources = $this->unresolvedSources();
        $summary['unresolved_sources'] = $sources->count();

        $conflicts = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchDynamicKeyAssignment::class)
            ->conflicts($sources->pluck('finding_id')->filter()->all());
        if ($conflicts->isNotEmpty()) {
            $this->error('Ambiguous active key assignments; no candidates were changed.');
            foreach ($conflicts as $conflict) {
                $this->line('Finding '.$conflict['finding_id'].' -> Key IDs: '.implode(', ', $conflict['key_ids']));
            }
            $this->writeTranslationWorkbenchReport(
                summary: [...$summary, 'exit_code' => self::FAILURE, 'error' => 'ambiguous_active_key_assignments', 'conflicting_findings' => $conflicts->count()],
                planSummary: ['key_assignment_conflicts' => $conflicts->all()],
            );
            return self::FAILURE;
        }

        $plan = [];
        $candidateConflicts = [];
        foreach ($sources as $source) {
            $candidates = $this->candidatesForSource($source);
            $summary['candidates_discovered'] += count($candidates);
            foreach ($candidates as $candidate) {
                if ($candidate['meta']['value_observation']['truncated'] ?? false) {
                    $summary['truncated_value_samples']++;
                    $this->warn('Source '.$source->dynamic_source_id.': candidate '.$candidate['candidate_reference'].' contains only the first 200 distinct values; this is an unverified sample.');
                }
            }

            foreach ($candidates as $candidate) {
                $matches = $this->candidateMatches($candidate)->get(['id']);
                if ($matches->count() > 1) {
                    $candidateConflicts[] = [
                        'dynamic_source_id' => (int) $source->dynamic_source_id,
                        'candidate_reference' => $candidate['candidate_reference'],
                        'candidate_ids' => $matches->pluck('id')->map(fn ($id): int => (int) $id)->all(),
                    ];
                }
                $plan[] = ['source' => $source, 'candidate' => $candidate];
            }
        }
        if ($candidateConflicts !== []) {
            $this->error('Ambiguous candidate identities; no candidates were changed.');
            foreach ($candidateConflicts as $conflict) {
                $this->line('Source '.$conflict['dynamic_source_id'].' ('.$conflict['candidate_reference'].') -> Candidate IDs: '.implode(', ', $conflict['candidate_ids']));
            }
            $this->writeTranslationWorkbenchReport(
                summary: [...$summary, 'exit_code' => self::FAILURE, 'error' => 'ambiguous_candidate_identities', 'candidate_conflicts' => count($candidateConflicts)],
                planSummary: ['candidate_identity_conflicts' => $candidateConflicts],
            );
            return self::FAILURE;
        }

        if (! $dryRun) {
            foreach ($plan as ['source' => $source, 'candidate' => $candidate]) {
                $result = $this->syncCandidate($source, $candidate);
                $summary[$result]++;
                if (in_array($result, ['candidates_created', 'candidates_updated'], true)) {
                    $summary['timeline_events_created']++;
                }
            }
        }

        $this->components->info('Translation Workbench dynamic source candidate discovery finished.');
        $this->line('Unresolved sources: ' . number_format($summary['unresolved_sources']));
        $this->line('Candidates discovered: ' . number_format($summary['candidates_discovered']));
        $this->line('Candidates created: ' . number_format($summary['candidates_created']));
        $this->line('Candidates updated: ' . number_format($summary['candidates_updated']));
        $this->line('Reviewed/inactive candidates preserved: ' . number_format($summary['candidates_preserved']));

        if ($dryRun) {
            $this->warn('Dry run only: no dynamic source candidates were written.');
        }

        $this->writeTranslationWorkbenchReport(summary: $summary);

        return self::SUCCESS;
    }

    private function hasRequiredTables(): bool
    {
        return Schema::hasTable('translation_workbench_dynamic_sources')
            && Schema::hasTable('translation_workbench_dynamic_source_candidates')
            && Schema::hasTable('translation_workbench_findings')
            && Schema::hasTable('translation_workbench_source_files')
            && Schema::hasTable('translation_workbench_keys')
            && Schema::hasTable('translation_workbench_key_findings');
    }

    /**
     * @return Collection<int, object>
     */
    private function unresolvedSources(?int $sourceId = null): Collection
    {
        // MIN only keeps one row per source. Multiple valid keys are rejected by
        // the command preflight and again under the per-candidate write locks.
        $keyLinks = DB::table('translation_workbench_key_findings as links')
            ->join('translation_workbench_keys as linked_keys', 'linked_keys.id', '=', 'links.key_id')
            ->selectRaw('links.finding_id, MIN(links.key_id) as key_id, COUNT(DISTINCT links.key_id) as key_count')
            ->where('links.status', 'active')->where('linked_keys.status', '<>', 'obsolete')
            ->groupBy('links.finding_id');

        return DB::table('translation_workbench_dynamic_sources as sources')
            ->leftJoin('translation_workbench_findings as findings', 'findings.id', '=', 'sources.finding_id')
            ->leftJoin('translation_workbench_source_files as source_files', 'source_files.id', '=', 'findings.source_file_id')
            ->leftJoinSub($keyLinks, 'key_links', function ($join): void {
                $join->on('key_links.finding_id', '=', 'findings.id');
            })
            ->leftJoin('translation_workbench_keys as keys', 'keys.id', '=', 'key_links.key_id')
            ->leftJoin('translation_workbench_keys as source_keys', 'source_keys.id', '=', 'sources.key_id')
            ->where(function ($query): void {
                $query->whereNull('sources.finding_id')->orWhere(function ($query): void {
                    $query->where('findings.status', 'active')->where('source_files.status', 'active');
                });
            })
            ->where(function ($query): void {
                $query->whereNull('sources.key_id')->orWhere(function ($query): void {
                    $query->where('source_keys.status', '<>', 'obsolete')
                        ->where(function ($query): void {
                            $query->whereNull('sources.finding_id')->orWhereColumn('sources.key_id', 'key_links.key_id')
                                ->orWhere('key_links.key_count', '>', 1);
                        });
                });
            })
            ->where('sources.status', '<>', 'obsolete')
            ->where(function ($query): void {
                $query
                    ->where('sources.classification', 'unknown')
                    ->orWhere('sources.cardinality', 'unknown')
                    ->orWhere('sources.origin', 'unknown');
            })
            ->when($sourceId !== null, fn ($query) => $query->where('sources.id', $sourceId))
            ->orderBy('sources.id')
            ->get([
                'sources.id as dynamic_source_id',
                'sources.key_id',
                'sources.finding_id',
                'sources.dynamic_scope',
                'sources.source_expression',
                'sources.source_type',
                'sources.source_reference',
                'sources.source_path',
                'sources.source_line',
                'findings.suggested_key as finding_suggested_key',
                'findings.found_translation_key',
                'findings.existing_key',
                'source_files.path as finding_source_path',
                'findings.source_file_id as finding_source_file_id',
                'key_links.key_count',
                'key_links.key_id as assigned_key_id',
                DB::raw('COALESCE(source_keys.translation_key, keys.translation_key) as translation_key'),
                DB::raw('COALESCE(source_keys.suggested_key, keys.suggested_key) as key_suggested_key'),
            ]);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function candidatesForSource(object $source): array
    {
        $candidates = [];
        $expression = trim((string) $source->source_expression);
        $dynamicScope = trim((string) $source->dynamic_scope);
        $suggestedKey = $this->suggestedKey($source);

        if (preg_match('/\\$(?<model>[A-Za-z_][A-Za-z0-9_]*)->(?<column>[A-Za-z_][A-Za-z0-9_]*)/u', $expression, $match) === 1) {
            $model = (string) $match['model'];
            $column = (string) $match['column'];
            $reference = $model . '.' . $column;
            $observation = $this->valuesForModelColumn($model, $column);
            $values = $observation['values'];

            $candidates[] = $this->candidate(
                source: $source,
                suggestedKey: $suggestedKey,
                type: $values !== [] ? 'model_column' : 'model_column_unresolved',
                reference: $reference,
                values: $values,
                confidence: $values !== [] ? 'medium' : 'low',
                meta: [
                    'model_variable' => $model,
                    'column' => $column,
                    'heuristic' => 'source_expression_model_column',
                    'value_observation' => $observation['meta'],
                ],
            );
        }

        if (str_ends_with($dynamicScope, '_options') || str_contains($expression, 'Options')) {
            $candidates[] = $this->candidate(
                source: $source,
                suggestedKey: $suggestedKey,
                type: 'view_option_variable',
                reference: $dynamicScope !== '' ? $dynamicScope : $source->source_reference,
                values: [],
                confidence: 'low',
                meta: [
                    'heuristic' => 'dynamic_scope_options',
                    'source_reference' => $source->source_reference,
                ],
            );
        }

        if (preg_match('/__\(\s*\\$(?<prop>[A-Za-z_][A-Za-z0-9_]*)\s*\)/u', $expression, $match) === 1
            && $candidates === []) {
            $prop = (string) $match['prop'];
            $candidates[] = $this->candidate(
                source: $source,
                suggestedKey: $suggestedKey,
                type: str_ends_with(strtolower($prop), 'label') ? 'component_prop' : 'runtime_variable',
                reference: $prop,
                values: [],
                confidence: str_ends_with(strtolower($prop), 'label') ? 'medium' : 'low',
                meta: [
                    'variable' => $prop,
                    'heuristic' => 'direct_variable_translation',
                ],
            );
        }

        if (str_contains($expression, 'Str::headline($') && $candidates === []) {
            $candidates[] = $this->candidate(
                source: $source,
                suggestedKey: $suggestedKey,
                type: 'runtime_group_key',
                reference: $dynamicScope !== '' ? $dynamicScope : $expression,
                values: [],
                confidence: 'low',
                meta: [
                    'heuristic' => 'headline_runtime_group_key',
                ],
            );
        }

        if ($candidates === []) {
            $candidates[] = $this->candidate(
                source: $source,
                suggestedKey: $suggestedKey,
                type: 'unknown',
                reference: $dynamicScope !== '' ? $dynamicScope : $expression,
                values: [],
                confidence: 'low',
                meta: [
                    'heuristic' => 'fallback_unknown_candidate',
                ],
            );
        }

        return $candidates;
    }

    private function suggestedKey(object $source): ?string
    {
        foreach ([
            $source->translation_key,
            $source->key_suggested_key,
            $source->finding_suggested_key,
            $source->found_translation_key,
            $source->existing_key,
        ] as $candidate) {
            $candidate = trim((string) $candidate);

            if ($candidate !== '') {
                return $candidate;
            }
        }

        return null;
    }

    /** @return array{values: array, meta: array} */
    private function valuesForModelColumn(string $modelVariable, string $column): array
    {
        $table = match ($modelVariable) {
            'document' => 'person_documents',
            default => null,
        };
        $meta = [
            'table' => $table, 'column' => $column, 'limit' => 200,
            'truncated' => false, 'source_verified' => false,
            'label_origin' => 'raw_database_value',
        ];
        if (! $table || ! Schema::hasTable($table) || ! Schema::hasColumn($table, $column)) {
            return ['values' => [], 'meta' => [...$meta, 'status' => 'unavailable']];
        }

        // One extra distinct value proves truncation without a separate, racing count query.
        $observed = DB::table($table)
            ->whereNotNull($column)
            ->select($column)
            ->distinct()
            ->orderBy($column)
            ->limit(201)
            ->pluck($column);

        return [
            'values' => $observed->take(200)
                ->mapWithKeys(static fn (mixed $value): array => [(string) $value => (string) $value])->all(),
            'meta' => [...$meta, 'status' => 'observed', 'truncated' => $observed->count() > 200],
        ];
    }

    /**
     * @param  array<string, string>  $values
     * @param  array<string, mixed>  $meta
     * @return array<string, mixed>
     */
    private function candidate(
        object $source,
        ?string $suggestedKey,
        string $type,
        mixed $reference,
        array $values,
        string $confidence,
        array $meta,
    ): array {
        return [
            'dynamic_source_id' => (int) $source->dynamic_source_id,
            'key_id' => $source->key_id,
            'finding_id' => $source->finding_id,
            'suggested_key' => $suggestedKey,
            'dynamic_scope' => $source->dynamic_scope,
            'source_expression' => $source->source_expression,
            'candidate_source_type' => $type,
            'candidate_reference' => trim((string) $reference) !== '' ? trim((string) $reference) : null,
            'candidate_values_count' => count($values),
            'candidate_values' => $values !== [] ? $values : null,
            'confidence' => $confidence,
            'review_status' => 'pending',
            'status' => 'active',
            'meta' => [
                ...$meta,
                'source' => 'translation-workbench:discover-dynamic-source-candidates',
                'source_path' => $source->source_path,
                'source_line' => $source->source_line,
                'source_reference' => $source->source_reference,
            ],
        ];
    }

    /** Both preflight and locked writes must use exactly the same identity rules. */
    private function candidateMatches(array $candidate): \Illuminate\Database\Query\Builder
    {
        // Availability changes the state, not the identity of a model-column proposal.
        $types = in_array($candidate['candidate_source_type'], ['model_column', 'model_column_unresolved'], true)
            ? ['model_column', 'model_column_unresolved']
            : [$candidate['candidate_source_type']];

        return DB::table('translation_workbench_dynamic_source_candidates')
            ->where('dynamic_source_id', $candidate['dynamic_source_id'])
            ->whereIn('candidate_source_type', $types)
            ->where('candidate_reference', $candidate['candidate_reference'])
            ->orderBy('id');
    }

    /** Source is already locked; preserve the context used to build the suggestion. */
    private function lockCandidateContext(object $source): void
    {
        $keyIds = collect([$source->key_id])->filter();
        if ($source->finding_id !== null) {
            $finding = DB::table('translation_workbench_findings')->where('id', $source->finding_id)->lockForUpdate()->first();
            if ($finding) {
                DB::table('translation_workbench_source_files')->where('id', $finding->source_file_id)->lockForUpdate()->first();
            }
            $links = DB::table('translation_workbench_key_findings')->where('finding_id', $source->finding_id)
                ->where('status', 'active')->orderBy('id')->lockForUpdate()->get();
            $keyIds = $keyIds->merge($links->pluck('key_id'));
        }
        DB::table('translation_workbench_keys')->whereIn('id', $keyIds->unique())->orderBy('id')->lockForUpdate()->get();
        $fresh = $this->unresolvedSources((int) $source->dynamic_source_id)->first();
        if (! $fresh || (int) $fresh->key_count > 1) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'dynamic_source' => 'The candidate source ownership changed. Refresh its context before retrying.',
            ]);
        }
        foreach (['translation_key', 'key_suggested_key', 'finding_suggested_key', 'found_translation_key', 'existing_key',
            'finding_source_path', 'finding_source_file_id', 'assigned_key_id'] as $field) {
            if (property_exists($source, $field) && $source->{$field} !== $fresh->{$field}) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'dynamic_source' => 'The candidate source context changed. Refresh it before retrying.',
                ]);
            }
        }
    }

    /**
     * @param  array<string, mixed>  $candidate
     */
    private function syncCandidate(object $source, array $candidate): string
    {
        return DB::transaction(function () use ($source, $candidate): string {
            // Serialize discovery and manual review on the same source, including first inserts.
            $current = DB::table('translation_workbench_dynamic_sources')
                ->where('id', $source->dynamic_source_id)->lockForUpdate()->first();
            $changed = $current === null || $current->status === 'obsolete';
            if ($current) {
                foreach (['key_id', 'finding_id', 'dynamic_scope', 'source_expression', 'source_type', 'source_reference', 'source_path', 'source_line'] as $field) {
                    $changed = $changed || $current->{$field} !== $source->{$field};
                }
                $changed = $changed || ! in_array('unknown', [$current->classification, $current->cardinality, $current->origin], true);
            }
            if ($changed) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'dynamic_source' => 'The dynamic source changed during candidate discovery. Refresh its context before retrying.',
                ]);
            }
            $this->lockCandidateContext($source);
            $matches = $this->candidateMatches($candidate)->lockForUpdate()->get();
            if ($matches->count() > 1) {
                throw new \LogicException('Ambiguous candidate identity for dynamic source '.$source->dynamic_source_id
                    .' ('.$candidate['candidate_reference'].'); candidate IDs: '.$matches->pluck('id')->implode(', ').'.');
            }
            $existing = $matches->first();
            if ($existing && ($existing->review_status !== 'pending' || $existing->status !== 'active')) {
                return 'candidates_preserved';
            }

            $attributes = [
                ...$candidate,
                'candidate_values' => $candidate['candidate_values'] !== null
                    ? json_encode($candidate['candidate_values'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
                    : null,
                'meta' => json_encode($candidate['meta'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            ];
            $oldValues = $existing ? collect($attributes)->keys()
                ->mapWithKeys(fn (string $key): array => [$key => $existing->{$key} ?? null])->all() : null;
            $changedValues = collect($attributes)->filter(static function (mixed $value, string $key) use ($oldValues): bool {
                $old = $oldValues[$key] ?? null;
                if (in_array($key, ['candidate_values', 'meta'], true)) {
                    return json_decode((string) $old, true) != json_decode((string) $value, true);
                }
                return $old != $value;
            })->all();
            if ($existing && $changedValues === []) {
                return 'candidates_unchanged';
            }

            if ($existing) {
                $candidateId = (int) $existing->id;
                DB::table('translation_workbench_dynamic_source_candidates')->where('id', $candidateId)
                    ->update([...$changedValues, 'updated_at' => now()]);
            } else {
                $candidateId = DB::table('translation_workbench_dynamic_source_candidates')->insertGetId([
                    ...$attributes, 'created_at' => now(), 'updated_at' => now(),
                ]);
            }
            $this->timelineRecorder->record(
                eventType: $existing ? 'dynamic_source_candidate_updated' : 'dynamic_source_candidate_discovered',
                key: $source->key_id ? TranslationWorkbenchKey::query()->find($source->key_id) : null,
                finding: $source->finding_id ? TranslationWorkbenchFinding::query()->find($source->finding_id) : null,
                oldValues: $oldValues,
                newValues: $attributes,
                context: [
                    'source' => 'translation-workbench:discover-dynamic-source-candidates',
                    'dynamic_source_id' => (int) $source->dynamic_source_id,
                    'candidate_id' => $candidateId,
                    'source_path' => $source->source_path,
                    'source_line' => $source->source_line,
                ],
            );

            return $existing ? 'candidates_updated' : 'candidates_created';
        });
    }
}
