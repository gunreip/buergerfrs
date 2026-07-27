<?php

// packages/gunreip/laravel-translation-workbench/src/Console/DiscoverDynamicSourceCandidates.php

// php artisan translation-workbench:discover-dynamic-source-candidates
// php artisan translation-workbench:discover-dynamic-source-candidates --dry-run

namespace Gunreip\TranslationWorkbench\Console;

use Gunreip\TranslationWorkbench\Console\Concerns\WritesTranslationWorkbenchReports;
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

    public function handle(): int
    {
        if (! $this->hasRequiredTables()) {
            $this->error('Dynamic source candidate tables are missing. Run the workbench migrations first.');
            $this->writeTranslationWorkbenchReport();

            return self::FAILURE;
        }

        $dryRun = (bool) $this->option('dry-run');
        $summary = [
            'unresolved_sources' => 0,
            'candidates_discovered' => 0,
            'candidates_created' => 0,
            'candidates_updated' => 0,
            'candidates_unchanged' => 0,
        ];

        $sources = $this->unresolvedSources();
        $summary['unresolved_sources'] = $sources->count();

        foreach ($sources as $source) {
            $candidates = $this->candidatesForSource($source);
            $summary['candidates_discovered'] += count($candidates);

            if ($dryRun) {
                continue;
            }

            foreach ($candidates as $candidate) {
                $result = $this->syncCandidate($source, $candidate);
                $summary[$result]++;
            }
        }

        $this->components->info('Translation Workbench dynamic source candidate discovery finished.');
        $this->line('Unresolved sources: ' . number_format($summary['unresolved_sources']));
        $this->line('Candidates discovered: ' . number_format($summary['candidates_discovered']));
        $this->line('Candidates created: ' . number_format($summary['candidates_created']));
        $this->line('Candidates updated: ' . number_format($summary['candidates_updated']));

        if ($dryRun) {
            $this->warn('Dry run only: no dynamic source candidates were written.');
        }

        $this->writeTranslationWorkbenchReport();

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
    private function unresolvedSources(): Collection
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
                'keys.translation_key',
                'keys.suggested_key as key_suggested_key',
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
            $values = $this->valuesForModelColumn($model, $column);

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

    /**
     * @return array<string, string>
     */
    private function valuesForModelColumn(string $modelVariable, string $column): array
    {
        $table = match ($modelVariable) {
            'document' => 'person_documents',
            default => null,
        };

        if (! $table || ! Schema::hasTable($table) || ! Schema::hasColumn($table, $column)) {
            return [];
        }

        return DB::table($table)
            ->whereNotNull($column)
            ->select($column)
            ->distinct()
            ->orderBy($column)
            ->limit(200)
            ->pluck($column)
            ->mapWithKeys(static fn(mixed $value): array => [(string) $value => str((string) $value)->headline()->toString()])
            ->all();
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

    /**
     * @param  array<string, mixed>  $candidate
     */
    private function syncCandidate(object $source, array $candidate): string
    {
        $existing = DB::table('translation_workbench_dynamic_source_candidates')
            ->where('dynamic_source_id', $candidate['dynamic_source_id'])
            ->where('candidate_source_type', $candidate['candidate_source_type'])
            ->where('candidate_reference', $candidate['candidate_reference'])
            ->first();

        $attributes = [
            ...$candidate,
            'candidate_values' => $candidate['candidate_values'] !== null
                ? json_encode($candidate['candidate_values'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
                : null,
            'meta' => json_encode($candidate['meta'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        ];

        if (! $existing) {
            DB::table('translation_workbench_dynamic_source_candidates')->insert([
                ...$attributes,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return 'candidates_created';
        }

        $changedValues = collect($attributes)
            ->filter(static fn(mixed $value, string $key): bool => ($existing->{$key} ?? null) != $value)
            ->all();

        if ($changedValues === []) {
            return 'candidates_unchanged';
        }

        DB::table('translation_workbench_dynamic_source_candidates')
            ->where('id', $existing->id)
            ->update([
                ...$changedValues,
                'updated_at' => now(),
            ]);

        return 'candidates_updated';
    }
}
