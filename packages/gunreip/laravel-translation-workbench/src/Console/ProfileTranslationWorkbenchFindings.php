<?php

// packages/gunreip/laravel-translation-workbench/src/Console/ProfileTranslationWorkbenchFindings.php

// php artisan translation-workbench:profile-findings
// php artisan translation-workbench:profile-findings --per-page=50

namespace Gunreip\TranslationWorkbench\Console;

use Gunreip\TranslationWorkbench\Livewire\TranslationWorkbenchEntries;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use ReflectionMethod;

#[Signature('translation-workbench:profile-findings
    {--per-page=25 : Number of rows requested by each profiled paginator call.}')]
#[Description('Profile common Translation Workbench findings table filter states.')]
class ProfileTranslationWorkbenchFindings extends Command
{
    public function handle(): int
    {
        $perPage = max(5, min(100, (int) $this->option('per-page')));
        $currentQueries = [];

        DB::listen(static function (QueryExecuted $query) use (&$currentQueries): void {
            $currentQueries[] = [
                'sql' => $query->sql,
                'bindings' => $query->bindings,
                'time_ms' => $query->time,
            ];
        });

        $cases = $this->cases();
        $results = [];

        $this->components->info('Profiling Translation Workbench findings queries.');

        foreach ($cases as $case) {
            $currentQueries = [];
            $startedAt = microtime(true);
            $paginator = $this->runCase($case['state'], $perPage);
            $elapsedMs = round((microtime(true) - $startedAt) * 1000, 3);
            $slowestQuery = collect($currentQueries)->sortByDesc('time_ms')->first();
            $queryTimeMs = round((float) collect($currentQueries)->sum('time_ms'), 3);

            $results[] = [
                'case' => $case['label'],
                'state' => $case['state'],
                'total_rows' => $paginator->total(),
                'page_rows' => $paginator->count(),
                'query_count' => count($currentQueries),
                'query_time_ms' => $queryTimeMs,
                'elapsed_ms' => $elapsedMs,
                'slowest_query_ms' => $slowestQuery ? round((float) $slowestQuery['time_ms'], 3) : 0.0,
                'slowest_query_sql' => $slowestQuery['sql'] ?? null,
                'slowest_query_bindings' => $slowestQuery['bindings'] ?? [],
            ];
        }

        $this->writeReport($results, $perPage);
        $this->renderSummary($results);

        return self::SUCCESS;
    }

    /**
     * @return array<int, array{label: string, state: array<string, mixed>}>
     */
    private function cases(): array
    {
        return [
            [
                'label' => 'Default work findings',
                'state' => [],
            ],
            [
                'label' => 'Translation key missing',
                'state' => ['findingKeyRelation' => 'missing'],
            ],
            [
                'label' => 'Target literal missing',
                'state' => ['findingLiteralState' => 'target_missing'],
            ],
            [
                'label' => 'Shared candidates all',
                'state' => ['findingKeyRelation' => 'shared_candidates'],
            ],
            [
                'label' => 'Shared candidates open',
                'state' => ['findingKeyRelation' => 'shared_candidates_open'],
            ],
            [
                'label' => 'Shared candidates done',
                'state' => ['findingKeyRelation' => 'shared_candidates_done'],
            ],
            [
                'label' => 'Dynamic multi',
                'state' => ['findingCandidateType' => 'dynamic_multi'],
            ],
            [
                'label' => 'Search open',
                'state' => ['findingSearch' => 'open'],
            ],
            [
                'label' => 'Search exact open',
                'state' => [
                    'findingSearch' => 'open',
                    'findingSearchExact' => true,
                ],
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $state
     */
    private function runCase(array $state, int $perPage): LengthAwarePaginator
    {
        $component = app(TranslationWorkbenchEntries::class);
        $component->perPage = $perPage;

        foreach ($state as $property => $value) {
            $component->{$property} = $value;
        }

        $method = new ReflectionMethod($component, 'findings');
        $method->setAccessible(true);

        return $method->invoke($component);
    }

    /**
     * @param  array<int, array<string, mixed>>  $results
     */
    private function writeReport(array $results, int $perPage): void
    {
        $path = storage_path('translation-workbench/translation-workbench-profile-findings.json');
        $directory = dirname($path);

        if (! File::isDirectory($directory)) {
            File::ensureDirectoryExists($directory);
        }

        File::put($path, json_encode([
            'command' => 'translation-workbench:profile-findings',
            'generated_at' => now()->toISOString(),
            'per_page' => $perPage,
            'results' => $results,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    }

    /**
     * @param  array<int, array<string, mixed>>  $results
     */
    private function renderSummary(array $results): void
    {
        $this->table(
            ['Case', 'Rows', 'Page', 'Queries', 'Query ms', 'Elapsed ms', 'Slowest ms'],
            collect($results)
                ->map(static fn(array $row): array => [
                    $row['case'],
                    $row['total_rows'],
                    $row['page_rows'],
                    $row['query_count'],
                    number_format((float) $row['query_time_ms'], 3),
                    number_format((float) $row['elapsed_ms'], 3),
                    number_format((float) $row['slowest_query_ms'], 3),
                ])
                ->all(),
        );

        $this->components->info('Report written to storage/translation-workbench/translation-workbench-profile-findings.json.');
    }
}
