<?php

// packages/gunreip/laravel-translation-workbench/src/Console/DetectSharedKeyCandidates.php

// php artisan translation-workbench:detect-shared-key-candidates
// php artisan translation-workbench:detect-shared-key-candidates --dry-run

namespace Gunreip\TranslationWorkbench\Console;

use Gunreip\TranslationWorkbench\Console\Concerns\WritesTranslationWorkbenchReports;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchSharedKeyCandidate;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchKey;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchFinding;
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchTimelineRecorder;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
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
        [$candidateRows, $summary] = $dryRun
            ? $this->detect(true)
            : DB::transaction(fn () => $this->detect(false), 3);

        if (($summary['exit_code'] ?? self::SUCCESS) !== self::SUCCESS) {
            $this->error('Ambiguous active key assignments; no shared-key candidates were changed.');
            foreach ($summary['key_assignment_conflicts'] as $conflict) {
                $this->line('Finding '.$conflict['finding_id'].' -> Key IDs: '.implode(', ', $conflict['key_ids']));
            }
            $this->writeReport($candidateRows, $summary);
            return self::FAILURE;
        }

        $this->components->info('Translation Workbench shared-key candidate detection finished.');
        $this->line('Shared decision groups: ' . number_format($summary['shared_decision_groups']));
        $this->line('Candidate rows: ' . number_format($summary['candidate_rows']));
        $this->line('Proposed pending rows: ' . number_format($summary['pending_rows']));
        $this->line('Obsolete marked: ' . number_format($summary['obsolete_marked']));
        $this->line('Reviewed candidates preserved: ' . number_format($summary['reviewed_candidates_preserved']));
        $this->line('Timeline events created: ' . number_format($summary['timeline_events_created']));

        if ($dryRun) {
            $this->warn('Dry run only: no shared-key candidate rows were written.');
        }

        $this->writeReport($candidateRows, $summary);

        return self::SUCCESS;
    }

    /** @return array{Collection, array} */
    private function detect(bool $dryRun): array
    {
        if (! $dryRun) {
            $this->lockDetectionInputs();
        }
        $now = now();
        $decisionGroups = $this->sharedDecisionGroups();
        $findingIds = $decisionGroups->flatMap(fn (object $group) => $this->candidateFindingsQuery($group)->pluck('findings.id'))->all();
        $conflicts = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchDynamicKeyAssignment::class)->conflicts($findingIds);
        if ($conflicts->isNotEmpty()) {
            return [collect(), [
                'exit_code' => self::FAILURE, 'dry_run' => $dryRun,
                'error' => 'ambiguous_active_key_assignments',
                'shared_decision_groups' => $decisionGroups->count(),
                'key_assignment_conflicts' => $conflicts->all(),
                'candidate_rows' => 0, 'pending_rows' => 0, 'obsolete_marked' => 0,
                'reviewed_candidates_preserved' => 0, 'timeline_events_created' => 0,
            ]];
        }
        $candidateRows = $this->candidateRows($decisionGroups, $now);
        $obsoleteCount = 0;
        $preservedCount = 0;
        $eventCount = 0;

        if (! $dryRun) {
            DB::transaction(function () use ($candidateRows, $now, &$obsoleteCount, &$preservedCount, &$eventCount): void {
                $activeKeys = $candidateRows
                    ->map(static fn(array $row): string => (string) $row['finding_id'] . '|' . (string) $row['suggested_shared_translation_key'])
                    ->all();

                foreach ($candidateRows as $row) {
                    $existing = TranslationWorkbenchSharedKeyCandidate::query()
                        ->where('finding_id', $row['finding_id'])
                        ->where('suggested_shared_translation_key', $row['suggested_shared_translation_key'])
                        ->lockForUpdate()->first();

                    if ($existing) {
                        if (! in_array($existing->status, ['pending', 'stale', 'obsolete'], true)) {
                            $preservedCount++;
                            continue;
                        }
                        $before = $existing->attributesToArray();
                        $updates = collect($row)
                            ->except(['finding_id', 'suggested_shared_translation_key', 'first_seen_at', 'created_at', 'status'])
                            ->all();

                        if (in_array($existing->status, ['stale', 'obsolete'], true)) {
                            $updates['status'] = 'pending';
                        }

                        $existing->forceFill($updates);
                        $changed = collect($existing->getDirty())->except(['last_seen_at', 'updated_at'])->isNotEmpty();
                        $existing->save();
                        if ($changed) {
                            $this->recordCandidateChange($existing, 'shared_key_candidate_updated', $before);
                            $eventCount++;
                        }

                        continue;
                    }

                    $created = TranslationWorkbenchSharedKeyCandidate::query()->create($row);
                    $this->recordCandidateChange($created, 'shared_key_candidate_discovered');
                    $eventCount++;
                }

                TranslationWorkbenchSharedKeyCandidate::query()
                    ->whereIn('status', ['pending', 'stale'])
                    ->orderBy('id')->lockForUpdate()->get()
                    ->each(function (TranslationWorkbenchSharedKeyCandidate $candidate) use ($activeKeys, $now, &$obsoleteCount, &$eventCount): void {
                        $key = (string) $candidate->finding_id . '|' . (string) $candidate->suggested_shared_translation_key;

                        if (in_array($key, $activeKeys, true)) {
                            return;
                        }

                        if ($candidate->status !== 'obsolete') {
                            $before = $candidate->attributesToArray();
                            $candidate->forceFill([
                                'status' => 'obsolete',
                                'last_seen_at' => $now,
                            ])->save();
                            $this->recordCandidateChange($candidate, 'shared_key_candidate_obsoleted', $before);
                            $eventCount++;
                            $obsoleteCount++;
                        }
                    });
            });
        }

        $summary = [
            'exit_code' => self::SUCCESS,
            'shared_decision_groups' => $decisionGroups->count(),
            'candidate_rows' => $candidateRows->count(),
            'pending_rows' => $candidateRows->where('status', 'pending')->count(),
            'obsolete_marked' => $obsoleteCount,
            'reviewed_candidates_preserved' => $preservedCount,
            'timeline_events_created' => $eventCount,
            'dry_run' => $dryRun,
        ];

        return [$candidateRows, $summary];
    }

    private function lockDetectionInputs(): void
    {
        if (DB::transactionLevel() === 0) {
            throw new \LogicException('Shared candidate detection requires a write transaction.');
        }
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }
        // Includes new rows: row locks alone cannot protect the absence of a review/link.
        DB::statement('LOCK TABLE translation_workbench_source_files, translation_workbench_findings, translation_workbench_keys, translation_workbench_key_findings, translation_workbench_reviews IN SHARE MODE');
        // Serialize detectors and manual candidate writers, while allowing ordinary readers.
        DB::statement('LOCK TABLE translation_workbench_shared_key_candidates IN SHARE ROW EXCLUSIVE MODE');
    }

    private function recordCandidateChange(TranslationWorkbenchSharedKeyCandidate $candidate, string $eventType, ?array $before = null): void
    {
        app(TranslationWorkbenchTimelineRecorder::class)->record(
            eventType: $eventType,
            key: $candidate->key_id ? TranslationWorkbenchKey::find($candidate->key_id) : null,
            finding: TranslationWorkbenchFinding::find($candidate->finding_id),
            oldValues: $before,
            newValues: $candidate->attributesToArray(),
            context: [
                'source' => 'translation-workbench:detect-shared-key-candidates',
                'candidate_id' => $candidate->id,
                'suggested_shared_translation_key' => $candidate->suggested_shared_translation_key,
            ],
        );
    }

    private function hasRequiredTables(): bool
    {
        return collect([
            'translation_workbench_findings',
            'translation_workbench_source_files',
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
            ->join('translation_workbench_source_files as source_files', 'source_files.id', '=', 'findings.source_file_id')
            ->join('translation_workbench_keys as keys', 'keys.id', '=', 'reviews.key_id')
            ->where('reviews.decision', 'translation_key_bulk_equalized')
            ->where('findings.status', 'active')
            ->where('source_files.status', 'active')
            ->where('keys.status', '<>', 'obsolete')
            // A historical decision must still describe the current assignment and text.
            ->whereColumn('reviews.new_values->translation_key', 'keys.translation_key')
            ->whereColumn('reviews.meta->normalized_literal', DB::raw($literalExpression))
            ->whereExists(function ($query): void {
                $query->selectRaw('1')->from('translation_workbench_key_findings as reviewed_links')
                    ->whereColumn('reviewed_links.finding_id', 'findings.id')
                    ->whereColumn('reviewed_links.key_id', 'keys.id')
                    ->where('reviewed_links.status', 'active');
            })
            ->whereNotExists(function ($query): void {
                $query->selectRaw('1')->from('translation_workbench_key_findings as competing_links')
                    ->join('translation_workbench_keys as competing_keys', 'competing_keys.id', '=', 'competing_links.key_id')
                    ->whereColumn('competing_links.finding_id', 'findings.id')
                    ->whereColumn('competing_links.key_id', '<>', 'keys.id')
                    ->where('competing_links.status', 'active')
                    ->where('competing_keys.status', '<>', 'obsolete');
            })
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
    private function candidateRows(Collection $decisionGroups, mixed $now): Collection
    {
        return $decisionGroups
            ->flatMap(fn(object $group): Collection => $this->candidateRowsForGroup($group, $now))
            ->values();
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function candidateRowsForGroup(object $group, mixed $now): Collection
    {
        $matchedFindingIds = $this->postgresIntegerArrayToArray($group->matched_finding_ids ?? null);

        // The preflight has rejected multiple valid keys; obsolete keys cannot win MIN.
        $keyLinks = DB::table('translation_workbench_key_findings as links')
            ->join('translation_workbench_keys as linked_keys', 'linked_keys.id', '=', 'links.key_id')
            ->selectRaw('links.finding_id, MIN(links.key_id) as key_id')
            ->where('links.status', 'active')->where('linked_keys.status', '<>', 'obsolete')
            ->groupBy('links.finding_id');

        return $this->candidateFindingsQuery($group)
            ->leftJoinSub($keyLinks, 'key_links', function ($join): void {
                $join->on('key_links.finding_id', '=', 'findings.id');
            })
            ->leftJoin('translation_workbench_keys as keys', 'keys.id', '=', 'key_links.key_id')
            ->where(function ($query) use ($group): void {
                $query
                    ->whereNull('keys.translation_key')
                    ->orWhere('keys.translation_key', '!=', (string) $group->suggested_shared_translation_key);
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

    /** The same eligible findings feed both conflict detection and candidate generation. */
    private function candidateFindingsQuery(object $group): \Illuminate\Database\Query\Builder
    {
        return DB::table('translation_workbench_findings as findings')
            ->join('translation_workbench_source_files as source_files', 'source_files.id', '=', 'findings.source_file_id')
            ->where('findings.status', 'active')
            ->where('source_files.status', 'active')
            ->whereRaw($this->normalizedLiteralSqlExpression('findings').' = ?', [(string) $group->normalized_literal])
            ->where('findings.first_seen_at', '>', $group->latest_reviewed_at)
            ->whereNotExists(function ($query): void {
                $query
                    ->selectRaw('1')
                    ->from('translation_workbench_reviews as bulk_reviews')
                    ->where('bulk_reviews.decision', 'translation_key_bulk_equalized')
                    ->whereColumn('bulk_reviews.finding_id', 'findings.id');
            });
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
        @chmod($directory, 0777);

        if (File::exists($path) && ! is_writable($path)) {
            @unlink($path);
        }

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
        @chmod($path, 0666);

        $this->line('JSON report: ' . $path);
    }
}
