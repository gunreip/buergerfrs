<?php

namespace Gunreip\TranslationWorkbench\Foundation;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Collection;

class TranslationWorkbenchCodeUpdateApplier
{
    public function __construct(
        private readonly TranslationWorkbenchCodeUpdatePlanner $planner,
    ) {}

    /**
     * @param  array<int, string>  $paths
     * @return array<string, mixed>
     */
    public function apply(array $paths = [], ?int $limit = null, bool $write = false): array
    {
        $plan = $this->planner->plan($paths, $limit);
        $safeUpdates = collect($plan['updates'])
            ->where('state', 'safe_update')
            ->values();
        $duplicateGroups = $safeUpdates
            ->groupBy(static fn(array $row): string => ($row['source_path'] ?? '') . "\n" . ($row['raw_expression'] ?? ''))
            ->map(static fn(Collection $rows): array => [
                'rows' => $rows->count(),
                'new_expressions' => $rows
                    ->pluck('new_expression')
                    ->filter()
                    ->unique()
                    ->values()
                    ->all(),
            ]);
        $results = $safeUpdates
            ->map(fn(array $row): array => $this->applyRow($row, $write, $duplicateGroups[
                ($row['source_path'] ?? '') . "\n" . ($row['raw_expression'] ?? '')
            ] ?? null))
            ->values();
        $diff = $this->buildDiff($results->whereIn('state', ['would_apply', 'applied'])->values()->all());

        return [
            'generated_at' => now()->toISOString(),
            'write' => $write,
            'paths' => array_values($paths),
            'plan_summary' => $plan['summary'],
            'summary' => [
                'planned_safe_updates' => $safeUpdates->count(),
                'applied' => $results->where('state', 'applied')->count(),
                'would_apply' => $results->where('state', 'would_apply')->count(),
                'skipped' => $results->where('state', 'skipped')->count(),
                'stale_source' => $results->where('state', 'stale_source')->count(),
                'duplicate_expression' => $results->where('state', 'duplicate_expression')->count(),
                'duplicate_reviewed' => $results->where('state', 'duplicate_reviewed')->count(),
                'diff_files' => count($diff['files']),
            ],
            'diff' => $diff,
            'results' => $results->all(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function applyRow(array $row, bool $write, ?array $duplicateGroup = null): array
    {
        $sourcePath = base_path((string) $row['source_path']);
        $rawExpression = (string) ($row['raw_expression'] ?? '');
        $newExpression = (string) ($row['new_expression'] ?? '');
        $base = [
            'finding_id' => $row['finding_id'],
            'key_id' => $row['key_id'],
            'source_path' => $row['source_path'],
            'source_line' => $row['source_line'],
            'translation_key' => $row['translation_key'],
            'raw_expression' => $rawExpression,
            'new_expression' => $newExpression,
        ];

        if ($rawExpression === '' || $newExpression === '' || ! File::exists($sourcePath)) {
            return [
                ...$base,
                'state' => 'skipped',
                'reason' => 'missing_expression_or_source_file',
            ];
        }

        $source = (string) File::get($sourcePath);
        $occurrences = substr_count($source, $rawExpression);
        $conflictReview = $this->latestConflictReview(
            findingId: (int) $row['finding_id'],
            keyId: (int) $row['key_id'],
        );

        if ($occurrences === 0) {
            return [
                ...$base,
                'state' => 'stale_source',
                'reason' => 'raw_expression_not_found_in_current_source_file',
            ];
        }

        $conflictDecision = $conflictReview['decision'] ?? null;

        if ($conflictDecision === 'duplicate_dynamic_manual_workflow') {
            return [
                ...$base,
                'state' => 'duplicate_reviewed',
                'reason' => 'duplicate_expression_review_requires_manual_dynamic_workflow',
                'occurrences' => $occurrences,
                'conflict_review' => $conflictReview,
            ];
        }

        if ($occurrences > 1) {
            $groupRows = (int) ($duplicateGroup['rows'] ?? 0);
            $groupNewExpressions = $duplicateGroup['new_expressions'] ?? [];

            if ($groupRows === $occurrences && count($groupNewExpressions) === 1) {
                if (! $write) {
                    return [
                        ...$base,
                        'state' => 'would_apply',
                        'reason' => 'duplicate_expression_group_has_same_reviewed_replacement',
                        'occurrences' => $occurrences,
                        'replacement_scope' => 'all_matching_expressions_in_source_file',
                    ];
                }

                File::put($sourcePath, str_replace($rawExpression, $newExpression, $source));

                return [
                    ...$base,
                    'state' => 'applied',
                    'reason' => 'duplicate_expression_group_reviewed_and_source_file_updated',
                    'occurrences' => $occurrences,
                    'replacement_scope' => 'all_matching_expressions_in_source_file',
                ];
            }

            if (in_array($conflictDecision, ['duplicate_confirmed_same_key', 'existing_key_should_be_replaced'], true)) {
                if (! $write) {
                    return [
                        ...$base,
                        'state' => 'would_apply',
                        'reason' => 'duplicate_expression_review_allows_replacement',
                        'occurrences' => $occurrences,
                        'conflict_review' => $conflictReview,
                        'replacement_scope' => 'all_matching_expressions_in_source_file',
                    ];
                }

                File::put($sourcePath, str_replace($rawExpression, $newExpression, $source));

                return [
                    ...$base,
                    'state' => 'applied',
                    'reason' => 'duplicate_expression_reviewed_and_source_file_updated',
                    'occurrences' => $occurrences,
                    'conflict_review' => $conflictReview,
                    'replacement_scope' => 'all_matching_expressions_in_source_file',
                ];
            }

            if ($conflictReview !== null) {
                return [
                    ...$base,
                    'state' => 'duplicate_reviewed',
                    'reason' => 'duplicate_expression_has_review_decision',
                    'occurrences' => $occurrences,
                    'conflict_review' => $conflictReview,
                ];
            }

            return [
                ...$base,
                'state' => 'duplicate_expression',
                'reason' => 'raw_expression_occurs_more_than_once_in_source_file',
                'occurrences' => $occurrences,
            ];
        }

        if (! $write) {
            return [
                ...$base,
                'state' => 'would_apply',
                'reason' => 'dry_run_only',
            ];
        }

        File::put($sourcePath, str_replace($rawExpression, $newExpression, $source));

        return [
            ...$base,
            'state' => 'applied',
            'reason' => 'source_file_updated',
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     * @return array{files: array<int, string>, content: string}
     */
    private function buildDiff(array $rows): array
    {
        $files = collect($rows)
            ->groupBy('source_path')
            ->map(function ($fileRows, string $sourcePath): ?array {
                $absolutePath = base_path($sourcePath);

                if (! File::exists($absolutePath)) {
                    return null;
                }

                $oldContent = (string) File::get($absolutePath);
                $newContent = $oldContent;

                foreach ($fileRows as $row) {
                    $rawExpression = (string) ($row['raw_expression'] ?? '');
                    $newExpression = (string) ($row['new_expression'] ?? '');

                    if ($rawExpression === '' || $newExpression === '' || substr_count($newContent, $rawExpression) < 1) {
                        continue;
                    }

                    $newContent = str_replace($rawExpression, $newExpression, $newContent);
                }

                if ($oldContent === $newContent) {
                    return null;
                }

                return [
                    'source_path' => $sourcePath,
                    'content' => $this->unifiedDiffForFile($sourcePath, $oldContent, $newContent),
                ];
            })
            ->filter()
            ->values();

        return [
            'files' => $files->pluck('source_path')->all(),
            'content' => $files->pluck('content')->implode("\n"),
        ];
    }

    private function unifiedDiffForFile(string $sourcePath, string $oldContent, string $newContent): string
    {
        $oldLines = preg_split('/\\R/', $oldContent);
        $newLines = preg_split('/\\R/', $newContent);
        $oldLines = $oldLines === false ? [] : $oldLines;
        $newLines = $newLines === false ? [] : $newLines;
        $max = max(count($oldLines), count($newLines));
        $changedIndexes = [];

        for ($index = 0; $index < $max; $index++) {
            if (($oldLines[$index] ?? null) !== ($newLines[$index] ?? null)) {
                $changedIndexes[] = $index;
            }
        }

        if ($changedIndexes === []) {
            return '';
        }

        $lines = [
            '--- a/' . $sourcePath,
            '+++ b/' . $sourcePath,
        ];
        $context = 3;
        $hunks = [];

        foreach ($changedIndexes as $index) {
            $start = max(0, $index - $context);
            $end = min($max - 1, $index + $context);

            if ($hunks !== [] && $start <= $hunks[array_key_last($hunks)]['end'] + 1) {
                $hunks[array_key_last($hunks)]['end'] = max($hunks[array_key_last($hunks)]['end'], $end);

                continue;
            }

            $hunks[] = ['start' => $start, 'end' => $end];
        }

        foreach ($hunks as $hunk) {
            $start = $hunk['start'];
            $end = $hunk['end'];

            $lines[] = '@@ -' . ($start + 1) . ',' . ($end - $start + 1) . ' +' . ($start + 1) . ',' . ($end - $start + 1) . ' @@';

            for ($index = $start; $index <= $end; $index++) {
                $oldLine = $oldLines[$index] ?? null;
                $newLine = $newLines[$index] ?? null;

                if ($oldLine === $newLine) {
                    $lines[] = ' ' . $oldLine;

                    continue;
                }

                if ($oldLine !== null) {
                    $lines[] = '-' . $oldLine;
                }

                if ($newLine !== null) {
                    $lines[] = '+' . $newLine;
                }
            }
        }

        return implode("\n", $lines) . "\n";
    }

    /**
     * @return array<string, mixed>|null
     */
    private function latestConflictReview(int $findingId, int $keyId): ?array
    {
        if (! DB::getSchemaBuilder()->hasTable('translation_workbench_reviews')) {
            return null;
        }

        $review = DB::table('translation_workbench_reviews')
            ->where('review_type', 'code_update_conflict')
            ->where('finding_id', $findingId)
            ->where('key_id', $keyId)
            ->latest('id')
            ->first();

        if (! $review) {
            return null;
        }

        $meta = is_string($review->meta ?? null)
            ? json_decode((string) $review->meta, true)
            : [];

        return [
            'id' => (int) $review->id,
            'decision' => (string) $review->decision,
            'label' => str((string) $review->decision)->replace('_', ' ')->title()->toString(),
            'note' => is_array($meta) ? ($meta['note'] ?? null) : null,
            'reviewed_at' => (string) $review->reviewed_at,
        ];
    }
}
