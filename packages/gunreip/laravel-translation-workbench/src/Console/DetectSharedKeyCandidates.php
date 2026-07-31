<?php

// packages/gunreip/laravel-translation-workbench/src/Console/DetectSharedKeyCandidates.php

// php artisan translation-workbench:detect-shared-key-candidates
// php artisan translation-workbench:detect-shared-key-candidates --dry-run

namespace Gunreip\TranslationWorkbench\Console;

use Gunreip\TranslationWorkbench\Console\Concerns\WritesTranslationWorkbenchReports;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchSharedKeyCandidate;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

#[Signature('translation-workbench:detect-shared-key-candidates
    {--dry-run : Report possible shared-key candidates without writing database rows.}')]
#[Description('Detect new findings whose literal matches an already bulk-reviewed shared translation key.')]
class DetectSharedKeyCandidates extends Command
{
    use WritesTranslationWorkbenchReports;

    public function handle(): int
    {
        if (! $this->hasRequiredTables()) {
            $this->error('Required Translation Workbench tables are missing. Run the workbench migrations first.');
            $this->writeReport(collect(), ['error' => 'missing_required_tables']);

            return self::FAILURE;
        }

        $dryRun = (bool) $this->option('dry-run');
        $now = now();
        $latestFindingSeenCutoff = $this->latestFindingSeenCutoff();
        $decisionGroups = $this->sharedDecisionGroups();
        $candidateRows = $this->candidateRows($decisionGroups, $now, $latestFindingSeenCutoff);
        $obsoleteCount = 0;

        if (! $dryRun) {
            DB::transaction(function () use ($candidateRows, $now, &$obsoleteCount): void {
                $activeKeys = $candidateRows
                    ->map(static fn(array $row): string => (string) $row['finding_id'] . '|' . (string) $row['suggested_shared_translation_key'])
                    ->all();

                foreach ($candidateRows as $row) {
                    $existing = TranslationWorkbenchSharedKeyCandidate::query()
                        ->where('finding_id', $row['finding_id'])
                        ->where('suggested_shared_translation_key', $row['suggested_shared_translation_key'])
                        ->first();

                    if ($existing) {
                        $updates = collect($row)
                            ->except(['finding_id', 'suggested_shared_translation_key', 'first_seen_at', 'status'])
                            ->all();

                        if (in_array($existing->status, ['stale', 'obsolete'], true)) {
                            $updates['status'] = 'pending';
                        }

                        $existing->forceFill($updates)->save();

                        continue;
                    }

                    TranslationWorkbenchSharedKeyCandidate::query()->create($row);
                }

                TranslationWorkbenchSharedKeyCandidate::query()
                    ->whereIn('status', ['pending', 'stale'])
                    ->get()
                    ->each(function (TranslationWorkbenchSharedKeyCandidate $candidate) use ($activeKeys, $now, &$obsoleteCount): void {
                        $key = (string) $candidate->finding_id . '|' . (string) $candidate->suggested_shared_translation_key;

                        if (in_array($key, $activeKeys, true)) {
                            return;
                        }

                        if ($candidate->status !== 'obsolete') {
                            $candidate->forceFill([
                                'status' => 'obsolete',
                                'last_seen_at' => $now,
                            ])->save();
                            $obsoleteCount++;
                        }
                    });
            });
        }

        $summary = [
            'shared_decision_groups' => $decisionGroups->count(),
            'candidate_rows' => $candidateRows->count(),
            'pending_rows' => $candidateRows->where('status', 'pending')->count(),
            'obsolete_marked' => $obsoleteCount,
            'dry_run' => $dryRun,
            'latest_finding_seen_cutoff' => $latestFindingSeenCutoff?->toDateTimeString(),
        ];

        $this->components->info('Translation Workbench shared-key candidate detection finished.');
        $this->line('Shared decision groups: ' . number_format($summary['shared_decision_groups']));
        $this->line('Candidate rows: ' . number_format($summary['candidate_rows']));
        $this->line('Pending rows: ' . number_format($summary['pending_rows']));
        $this->line('Obsolete marked: ' . number_format($summary['obsolete_marked']));

        if ($dryRun) {
            $this->warn('Dry run only: no shared-key candidate rows were written.');
        }

        $this->writeReport($candidateRows, $summary);

        return self::SUCCESS;
    }

    private function hasRequiredTables(): bool
    {
        return collect([
            'translation_workbench_findings',
            'translation_workbench_keys',
            'translation_workbench_key_findings',
            'translation_workbench_reviews',
            'translation_workbench_shared_key_candidates',
        ])->every(static fn(string $table): bool => Schema::hasTable($table));
    }

    /**
     * @return Collection<int, object>
     */
    private function sharedDecisionGroups(): Collection
    {
        $literalExpression = $this->normalizedLiteralSqlExpression('findings');

        $groups = DB::table('translation_workbench_reviews as reviews')
            ->join('translation_workbench_findings as findings', 'findings.id', '=', 'reviews.finding_id')
            ->join('translation_workbench_keys as keys', 'keys.id', '=', 'reviews.key_id')
            ->where('reviews.decision', 'translation_key_bulk_equalized')
            ->whereRaw("NULLIF(keys.translation_key, '') IS NOT NULL")
            ->whereRaw("NULLIF({$literalExpression}, '') IS NOT NULL")
            ->groupBy(DB::raw($literalExpression), 'keys.translation_key')
            ->selectRaw($literalExpression . ' as normalized_literal')
            ->selectRaw('keys.translation_key as suggested_shared_translation_key')
            ->selectRaw('MIN(keys.id) as matched_key_id')
            ->selectRaw('COUNT(DISTINCT reviews.id) as matched_review_count')
            ->selectRaw('COUNT(DISTINCT findings.id) as matched_finding_count')
            ->selectRaw('array_agg(DISTINCT findings.id) as matched_finding_ids')
            ->selectRaw('MAX(COALESCE(reviews.reviewed_at, reviews.created_at)) as latest_reviewed_at')
            ->havingRaw('COUNT(DISTINCT findings.id) >= 2')
            ->orderBy(DB::raw($literalExpression))
            ->get();

        $ambiguousLiterals = $groups
            ->groupBy('normalized_literal')
            ->filter(static fn(Collection $literalGroups): bool => $literalGroups->count() > 1)
            ->keys()
            ->all();

        return $groups
            ->reject(static fn(object $group): bool => in_array((string) $group->normalized_literal, $ambiguousLiterals, true))
            ->values();
    }

    /**
     * @param  Collection<int, object>  $decisionGroups
     * @return Collection<int, array<string, mixed>>
     */
    private function candidateRows(Collection $decisionGroups, mixed $now, ?Carbon $latestFindingSeenCutoff): Collection
    {
        return $decisionGroups
            ->flatMap(fn(object $group): Collection => $this->candidateRowsForGroup($group, $now, $latestFindingSeenCutoff))
            ->values();
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function candidateRowsForGroup(object $group, mixed $now, ?Carbon $latestFindingSeenCutoff): Collection
    {
        $literalExpression = $this->normalizedLiteralSqlExpression('findings');
        $matchedFindingIds = $this->postgresIntegerArrayToArray($group->matched_finding_ids ?? null);

        $keyLinks = DB::table('translation_workbench_key_findings')
            ->selectRaw('finding_id, MIN(key_id) as key_id')
            ->where('status', 'active')
            ->groupBy('finding_id');

        return DB::table('translation_workbench_findings as findings')
            ->leftJoinSub($keyLinks, 'key_links', function ($join): void {
                $join->on('key_links.finding_id', '=', 'findings.id');
            })
            ->leftJoin('translation_workbench_keys as keys', 'keys.id', '=', 'key_links.key_id')
            ->where('findings.status', '!=', 'obsolete')
            ->when($latestFindingSeenCutoff !== null, fn($query) => $query->where('findings.last_seen_at', '>=', $latestFindingSeenCutoff))
            ->whereRaw($literalExpression . ' = ?', [(string) $group->normalized_literal])
            ->where('findings.first_seen_at', '>', $group->latest_reviewed_at)
            ->where(function ($query) use ($group): void {
                $query
                    ->whereNull('keys.translation_key')
                    ->orWhere('keys.translation_key', '!=', (string) $group->suggested_shared_translation_key);
            })
            ->whereNotExists(function ($query): void {
                $query
                    ->selectRaw('1')
                    ->from('translation_workbench_reviews as bulk_reviews')
                    ->where('bulk_reviews.decision', 'translation_key_bulk_equalized')
                    ->whereColumn('bulk_reviews.finding_id', 'findings.id');
            })
            ->where(function ($query): void {
                $query
                    ->whereNull('keys.id')
                    ->orWhereNotExists(function ($query): void {
                        $query
                            ->selectRaw('1')
                            ->from('translation_workbench_reviews as bulk_reviews')
                            ->where('bulk_reviews.decision', 'translation_key_bulk_equalized')
                            ->whereColumn('bulk_reviews.key_id', 'keys.id');
                    });
            })
            ->get([
                'findings.id as finding_id',
                'findings.literal_text',
                'findings.literal_text_suggested',
                'findings.suggested_key as finding_suggested_key',
                'keys.id as key_id',
                'keys.translation_key as current_translation_key',
            ])
            ->map(function (object $row) use ($group, $matchedFindingIds, $now): array {
                return [
                    'finding_id' => (int) $row->finding_id,
                    'key_id' => $row->key_id !== null ? (int) $row->key_id : null,
                    'matched_key_id' => $group->matched_key_id !== null ? (int) $group->matched_key_id : null,
                    'normalized_literal' => (string) $group->normalized_literal,
                    'literal_text' => $this->nullableString($row->literal_text ?? null)
                        ?? $this->nullableString($row->literal_text_suggested ?? null),
                    'current_translation_key' => $this->nullableString($row->current_translation_key ?? null),
                    'suggested_shared_translation_key' => (string) $group->suggested_shared_translation_key,
                    'matched_review_count' => (int) $group->matched_review_count,
                    'matched_finding_count' => (int) $group->matched_finding_count,
                    'confidence' => ((int) $group->matched_finding_count >= 3) ? 'high' : 'medium',
                    'status' => 'pending',
                    'first_seen_at' => $now,
                    'last_seen_at' => $now,
                    'matched_finding_ids' => $matchedFindingIds,
                    'meta' => [
                        'source' => 'translation-workbench:detect-shared-key-candidates',
                        'finding_suggested_key' => $this->nullableString($row->finding_suggested_key ?? null),
                    ],
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            });
    }

    private function latestFindingSeenCutoff(): ?Carbon
    {
        $latestSeenAt = DB::table('translation_workbench_findings')
            ->where('status', '!=', 'obsolete')
            ->max('last_seen_at');

        if (! $latestSeenAt) {
            return null;
        }

        return Carbon::parse($latestSeenAt)->subMinutes(10);
    }

    private function normalizedLiteralSqlExpression(string $tableAlias): string
    {
        return "LOWER(REGEXP_REPLACE(BTRIM(COALESCE({$tableAlias}.literal_text, {$tableAlias}.literal_text_suggested, '')), '\\s+', ' ', 'g'))";
    }

    /**
     * @return array<int, int>
     */
    private function postgresIntegerArrayToArray(mixed $value): array
    {
        return collect(explode(',', trim((string) $value, '{}')))
            ->map(static fn(string $id): int => (int) trim($id))
            ->filter(static fn(int $id): bool => $id > 0)
            ->unique()
            ->values()
            ->all();
    }

    private function nullableString(mixed $value): ?string
    {
        $value = trim((string) $value);

        return $value !== '' ? $value : null;
    }

    /**
     * Shared raw-data report.
     *
     * The raw_data base structure is centralized in WritesTranslationWorkbenchReports.
     * Do not add command-specific raw_data fields here or change the report
     * contract silently; discuss report contract changes first.
     *
     * @param  Collection<int, array<string, mixed>>  $rows
     * @param  array<string, mixed>  $summary
     */
    private function writeReport(Collection $rows, array $summary): void
    {
        $directory = storage_path('translation-workbench');
        $path = $directory . DIRECTORY_SEPARATOR . Str::of((string) $this->getName())->replace(':', '-')->append('.json');

        File::ensureDirectoryExists($directory);
        File::put($path, json_encode([
            'command' => $this->getName(),
            'generated_at' => now()->toISOString(),
            'summary' => $summary,
            'candidates' => $rows
                ->take(100)
                ->map(static fn(array $row): array => collect($row)->except(['created_at', 'updated_at'])->all())
                ->values()
                ->all(),
            'raw_data' => $this->translationWorkbenchReportRawData(),
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL);

        $this->line('JSON report: ' . $path);
    }
}
