<?php

// packages/gunreip/laravel-translation-workbench/src/Console/CleanupExcludedTranslationWorkbenchPaths.php

// php artisan translation-workbench:cleanup-excluded-paths
// php artisan translation-workbench:cleanup-excluded-paths --apply
// php artisan translation-workbench:cleanup-excluded-paths --apply --force
// php artisan translation-workbench:cleanup-excluded-paths --apply --force --samples=8

namespace Gunreip\TranslationWorkbench\Console;

use Gunreip\TranslationWorkbench\Console\Concerns\ConfirmsTranslationWorkbenchTruncate;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

#[Signature('translation-workbench:cleanup-excluded-paths
    {--apply : Mark currently active rows below configured exclude_paths as obsolete/stale.}
    {--force : Skip the interactive confirmation when --apply is used.}
    {--samples=8 : Number of sample paths to print per affected table.}')]
#[Description('Report and optionally obsolete Translation Workbench rows that match configured exclude_paths.')]
class CleanupExcludedTranslationWorkbenchPaths extends Command
{
    use ConfirmsTranslationWorkbenchTruncate;

    public function handle(): int
    {
        $excludedPaths = $this->excludedPaths();

        if ($excludedPaths->isEmpty()) {
            $this->components->warn('No translation-workbench.exclude_paths configured.');

            return self::SUCCESS;
        }

        $apply = (bool) $this->option('apply');
        $samples = max(1, (int) $this->option('samples'));
        $summary = $this->summary($excludedPaths, $samples);
        $affectedRows = collect($summary)->sum('active_rows');

        $this->components->info('Translation Workbench excluded-path cleanup report.');
        $this->line('Configured exclude paths:');
        $excludedPaths->each(fn(string $path) => $this->line('  - ' . $path));
        $this->newLine();

        $this->table(
            ['Table', 'Active rows', 'Cleanup status'],
            collect($summary)
                ->map(fn(array $row): array => [
                    $row['table'],
                    number_format($row['active_rows']),
                    $row['cleanup_status'],
                ])
                ->all(),
        );

        foreach ($summary as $row) {
            if ($row['active_rows'] < 1 || $row['samples']->isEmpty()) {
                continue;
            }

            $this->newLine();
            $this->line($row['table'] . ' samples:');
            $row['samples']->each(fn(string $path) => $this->line('  - ' . $path));
        }

        if (! $apply) {
            $this->newLine();
            $this->components->warn('Report only: run with --apply to mark matching rows obsolete/stale.');
            $this->recordCleanupActivity('translation_workbench.excluded_paths_reported', 'Translation Workbench excluded paths reported', [
                'affected_rows' => $affectedRows,
                'summary' => $this->activitySummary($summary),
            ]);

            return self::SUCCESS;
        }

        if ($affectedRows < 1) {
            $this->components->info('No active rows matched configured exclude paths.');

            return self::SUCCESS;
        }

        if (! $this->confirmApply($affectedRows, $excludedPaths)) {
            return self::FAILURE;
        }

        $updated = DB::transaction(fn(): array => $this->applyCleanup($excludedPaths));

        $this->newLine();
        $this->components->info('Excluded-path cleanup finished.');
        $this->table(
            ['Table', 'Updated rows'],
            collect($updated)
                ->map(fn(int $count, string $table): array => [$table, number_format($count)])
                ->all(),
        );

        $this->recordCleanupActivity('translation_workbench.excluded_paths_cleaned', 'Translation Workbench excluded paths cleaned', [
            'affected_rows' => $affectedRows,
            'updated' => $updated,
            'summary' => $this->activitySummary($summary),
        ]);

        return self::SUCCESS;
    }

    /**
     * @return Collection<int, string>
     */
    private function excludedPaths(): Collection
    {
        return collect((array) config('translation-workbench.exclude_paths', []))
            ->filter(static fn(mixed $path): bool => is_string($path) && trim($path) !== '')
            ->map(static fn(string $path): string => trim(str_replace('\\', '/', $path), '/'))
            ->unique()
            ->values();
    }

    /**
     * @param  Collection<int, string>  $excludedPaths
     * @return array<int, array{table: string, active_rows: int, cleanup_status: string, samples: Collection<int, string>}>
     */
    private function summary(Collection $excludedPaths, int $samples): array
    {
        return collect([
            $this->sourceFilesSummary($excludedPaths, $samples),
            $this->findingsSummary($excludedPaths, $samples),
            $this->keyFindingsSummary($excludedPaths, $samples),
            $this->dynamicSourcesSummary($excludedPaths, $samples),
            $this->dynamicSourceCandidatesSummary($excludedPaths, $samples),
            $this->legacyEntriesSummary($excludedPaths, $samples),
            $this->legacyOccurrencesSummary($excludedPaths, $samples),
            $this->optionDiscoveriesSummary($excludedPaths, $samples),
        ])
            ->filter()
            ->values()
            ->all();
    }

    private function sourceFilesSummary(Collection $excludedPaths, int $samples): ?array
    {
        if (! Schema::hasTable('translation_workbench_source_files')) {
            return null;
        }

        $query = $this->sourceFilesQuery($excludedPaths)->where('status', '<>', 'obsolete');

        return $this->tableSummary(
            'translation_workbench_source_files',
            $query,
            'obsolete',
            $query->clone()->orderBy('path')->limit($samples)->pluck('path'),
        );
    }

    private function findingsSummary(Collection $excludedPaths, int $samples): ?array
    {
        if (! Schema::hasTable('translation_workbench_findings') || ! Schema::hasTable('translation_workbench_source_files')) {
            return null;
        }

        $query = $this->findingsQuery($excludedPaths)->where('findings.status', '<>', 'obsolete');

        return $this->tableSummary(
            'translation_workbench_findings',
            $query,
            'obsolete',
            $query->clone()
                ->orderBy('source_files.path')
                ->orderBy('findings.source_line')
                ->limit($samples)
                ->pluck('source_files.path'),
        );
    }

    private function keyFindingsSummary(Collection $excludedPaths, int $samples): ?array
    {
        if (
            ! Schema::hasTable('translation_workbench_key_findings')
            || ! Schema::hasTable('translation_workbench_findings')
            || ! Schema::hasTable('translation_workbench_source_files')
        ) {
            return null;
        }

        $query = $this->keyFindingsQuery($excludedPaths)->where('key_findings.status', '<>', 'obsolete');

        return $this->tableSummary(
            'translation_workbench_key_findings',
            $query,
            'obsolete',
            $query->clone()
                ->orderBy('source_files.path')
                ->orderBy('findings.source_line')
                ->limit($samples)
                ->pluck('source_files.path'),
        );
    }

    private function dynamicSourcesSummary(Collection $excludedPaths, int $samples): ?array
    {
        if (! Schema::hasTable('translation_workbench_dynamic_sources')) {
            return null;
        }

        $query = $this->dynamicSourcesQuery($excludedPaths)->where('dynamic_sources.status', '<>', 'obsolete');

        return $this->tableSummary(
            'translation_workbench_dynamic_sources',
            $query,
            'obsolete',
            $query->clone()->orderBy('dynamic_sources.source_path')->limit($samples)->pluck('dynamic_sources.source_path'),
        );
    }

    private function dynamicSourceCandidatesSummary(Collection $excludedPaths, int $samples): ?array
    {
        if (
            ! Schema::hasTable('translation_workbench_dynamic_source_candidates')
            || ! Schema::hasTable('translation_workbench_dynamic_sources')
        ) {
            return null;
        }

        $query = $this->dynamicSourceCandidatesQuery($excludedPaths)->where('candidates.status', '<>', 'obsolete');

        return $this->tableSummary(
            'translation_workbench_dynamic_source_candidates',
            $query,
            'obsolete',
            $query->clone()->orderBy('dynamic_sources.source_path')->limit($samples)->pluck('dynamic_sources.source_path'),
        );
    }

    private function legacyEntriesSummary(Collection $excludedPaths, int $samples): ?array
    {
        if (! Schema::hasTable('translation_workbench_entries')) {
            return null;
        }

        $query = $this->legacyPathQuery('translation_workbench_entries', 'source_path', $excludedPaths)
            ->where('status', '<>', 'obsolete');

        return $this->tableSummary(
            'translation_workbench_entries',
            $query,
            'obsolete',
            $query->clone()->orderBy('source_path')->limit($samples)->pluck('source_path'),
        );
    }

    private function legacyOccurrencesSummary(Collection $excludedPaths, int $samples): ?array
    {
        if (! Schema::hasTable('translation_workbench_occurrences')) {
            return null;
        }

        $query = $this->legacyPathQuery('translation_workbench_occurrences', 'source_path', $excludedPaths)
            ->where('status', '<>', 'stale');

        return $this->tableSummary(
            'translation_workbench_occurrences',
            $query,
            'stale',
            $query->clone()->orderBy('source_path')->limit($samples)->pluck('source_path'),
        );
    }

    private function optionDiscoveriesSummary(Collection $excludedPaths, int $samples): ?array
    {
        if (! Schema::hasTable('translation_workbench_option_discoveries')) {
            return null;
        }

        $query = $this->legacyPathQuery('translation_workbench_option_discoveries', 'source_path', $excludedPaths)
            ->where('status', '<>', 'obsolete');

        return $this->tableSummary(
            'translation_workbench_option_discoveries',
            $query,
            'obsolete',
            $query->clone()->orderBy('source_path')->limit($samples)->pluck('source_path'),
        );
    }

    /**
     * @param  Collection<int, string>  $samples
     * @return array{table: string, active_rows: int, cleanup_status: string, samples: Collection<int, string>}
     */
    private function tableSummary(string $table, Builder $query, string $cleanupStatus, Collection $samples): array
    {
        return [
            'table' => $table,
            'active_rows' => $query->count(),
            'cleanup_status' => $cleanupStatus,
            'samples' => $samples
                ->filter(static fn(mixed $path): bool => is_string($path) && trim($path) !== '')
                ->unique()
                ->values(),
        ];
    }

    /**
     * @param  Collection<int, string>  $excludedPaths
     */
    private function sourceFilesQuery(Collection $excludedPaths): Builder
    {
        return $this->whereExcludedPath(DB::table('translation_workbench_source_files'), 'path', $excludedPaths);
    }

    /**
     * @param  Collection<int, string>  $excludedPaths
     */
    private function findingsQuery(Collection $excludedPaths): Builder
    {
        return $this->whereExcludedPath(
            DB::table('translation_workbench_findings as findings')
                ->join('translation_workbench_source_files as source_files', 'source_files.id', '=', 'findings.source_file_id'),
            'source_files.path',
            $excludedPaths,
        );
    }

    /**
     * @param  Collection<int, string>  $excludedPaths
     */
    private function keyFindingsQuery(Collection $excludedPaths): Builder
    {
        return $this->whereExcludedPath(
            DB::table('translation_workbench_key_findings as key_findings')
                ->join('translation_workbench_findings as findings', 'findings.id', '=', 'key_findings.finding_id')
                ->join('translation_workbench_source_files as source_files', 'source_files.id', '=', 'findings.source_file_id'),
            'source_files.path',
            $excludedPaths,
        );
    }

    /**
     * @param  Collection<int, string>  $excludedPaths
     */
    private function dynamicSourcesQuery(Collection $excludedPaths): Builder
    {
        return $this->whereExcludedPath(DB::table('translation_workbench_dynamic_sources as dynamic_sources'), 'dynamic_sources.source_path', $excludedPaths);
    }

    /**
     * @param  Collection<int, string>  $excludedPaths
     */
    private function dynamicSourceCandidatesQuery(Collection $excludedPaths): Builder
    {
        return $this->whereExcludedPath(
            DB::table('translation_workbench_dynamic_source_candidates as candidates')
                ->join('translation_workbench_dynamic_sources as dynamic_sources', 'dynamic_sources.id', '=', 'candidates.dynamic_source_id'),
            'dynamic_sources.source_path',
            $excludedPaths,
        );
    }

    /**
     * @param  Collection<int, string>  $excludedPaths
     */
    private function legacyPathQuery(string $table, string $column, Collection $excludedPaths): Builder
    {
        return $this->whereExcludedPath(DB::table($table), $column, $excludedPaths);
    }

    /**
     * @param  Collection<int, string>  $excludedPaths
     */
    private function whereExcludedPath(Builder $query, string $column, Collection $excludedPaths): Builder
    {
        return $query->where(function (Builder $query) use ($column, $excludedPaths): void {
            foreach ($excludedPaths as $path) {
                $query->orWhere($column, $path)
                    ->orWhere($column, 'like', $path . '/%');
            }
        });
    }

    /**
     * @param  Collection<int, string>  $excludedPaths
     * @return array<string, int>
     */
    private function applyCleanup(Collection $excludedPaths): array
    {
        $now = now();

        return [
            'translation_workbench_key_findings' => $this->updateKeyFindings($excludedPaths, $now),
            'translation_workbench_dynamic_source_candidates' => $this->updateDynamicSourceCandidates($excludedPaths, $now),
            'translation_workbench_dynamic_sources' => $this->updateDynamicSources($excludedPaths, $now),
            'translation_workbench_findings' => $this->updateFindings($excludedPaths, $now),
            'translation_workbench_source_files' => $this->updateSourceFiles($excludedPaths, $now),
            'translation_workbench_entries' => $this->updateLegacyPathTable('translation_workbench_entries', 'obsolete', $excludedPaths, $now),
            'translation_workbench_occurrences' => $this->updateLegacyPathTable('translation_workbench_occurrences', 'stale', $excludedPaths, $now),
            'translation_workbench_option_discoveries' => $this->updateLegacyPathTable('translation_workbench_option_discoveries', 'obsolete', $excludedPaths, $now),
        ];
    }

    private function updateSourceFiles(Collection $excludedPaths, mixed $now): int
    {
        if (! Schema::hasTable('translation_workbench_source_files')) {
            return 0;
        }

        return $this->sourceFilesQuery($excludedPaths)
            ->where('status', '<>', 'obsolete')
            ->update(['status' => 'obsolete', 'updated_at' => $now]);
    }

    private function updateFindings(Collection $excludedPaths, mixed $now): int
    {
        if (! Schema::hasTable('translation_workbench_findings') || ! Schema::hasTable('translation_workbench_source_files')) {
            return 0;
        }

        return $this->findingsQuery($excludedPaths)
            ->where('findings.status', '<>', 'obsolete')
            ->update(['status' => 'obsolete', 'updated_at' => $now]);
    }

    private function updateKeyFindings(Collection $excludedPaths, mixed $now): int
    {
        if (
            ! Schema::hasTable('translation_workbench_key_findings')
            || ! Schema::hasTable('translation_workbench_findings')
            || ! Schema::hasTable('translation_workbench_source_files')
        ) {
            return 0;
        }

        return $this->keyFindingsQuery($excludedPaths)
            ->where('key_findings.status', '<>', 'obsolete')
            ->update(['status' => 'obsolete', 'updated_at' => $now]);
    }

    private function updateDynamicSources(Collection $excludedPaths, mixed $now): int
    {
        if (! Schema::hasTable('translation_workbench_dynamic_sources')) {
            return 0;
        }

        return $this->dynamicSourcesQuery($excludedPaths)
            ->where('dynamic_sources.status', '<>', 'obsolete')
            ->update(['status' => 'obsolete', 'updated_at' => $now]);
    }

    private function updateDynamicSourceCandidates(Collection $excludedPaths, mixed $now): int
    {
        if (
            ! Schema::hasTable('translation_workbench_dynamic_source_candidates')
            || ! Schema::hasTable('translation_workbench_dynamic_sources')
        ) {
            return 0;
        }

        return $this->dynamicSourceCandidatesQuery($excludedPaths)
            ->where('candidates.status', '<>', 'obsolete')
            ->update(['status' => 'obsolete', 'updated_at' => $now]);
    }

    private function updateLegacyPathTable(string $table, string $status, Collection $excludedPaths, mixed $now): int
    {
        if (! Schema::hasTable($table)) {
            return 0;
        }

        return $this->legacyPathQuery($table, 'source_path', $excludedPaths)
            ->where('status', '<>', $status)
            ->update(['status' => $status, 'updated_at' => $now]);
    }

    /**
     * @param  Collection<int, string>  $excludedPaths
     */
    private function confirmApply(int $affectedRows, Collection $excludedPaths): bool
    {
        if ((bool) $this->option('force')) {
            return true;
        }

        $this->newLine();
        $this->components->warn(sprintf(
            'This will mark %s Translation Workbench rows below configured exclude_paths as obsolete/stale.',
            number_format($affectedRows),
        ));

        $excludedPaths->each(fn(string $path) => $this->line('  - ' . $path));

        return $this->confirm('Apply excluded-path cleanup?', false);
    }

    /**
     * @param  array<int, array{table: string, active_rows: int, cleanup_status: string, samples: Collection<int, string>}>  $summary
     * @return array<int, array<string, mixed>>
     */
    private function activitySummary(array $summary): array
    {
        return collect($summary)
            ->map(fn(array $row): array => [
                'table' => $row['table'],
                'active_rows' => $row['active_rows'],
                'cleanup_status' => $row['cleanup_status'],
                'samples' => $row['samples']->take(5)->values()->all(),
            ])
            ->values()
            ->all();
    }

    /**
     * @param  array<string, mixed>  $properties
     */
    private function recordCleanupActivity(string $event, string $description, array $properties): void
    {
        $this->recordTranslationWorkbenchActivity($event, $description, array_replace_recursive([
            'scope' => 'excluded_paths_cleanup',
            'apply' => (bool) $this->option('apply'),
            'force' => (bool) $this->option('force'),
        ], $properties));
    }
}
