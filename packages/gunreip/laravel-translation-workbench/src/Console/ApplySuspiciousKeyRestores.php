<?php

// packages/gunreip/laravel-translation-workbench/src/Console/ApplySuspiciousKeyRestores.php

// php artisan translation-workbench:apply-suspicious-key-restores
// php artisan translation-workbench:apply-suspicious-key-restores --paths=resources/views/components
// php artisan translation-workbench:apply-suspicious-key-restores --source-locale=en
// php artisan translation-workbench:apply-suspicious-key-restores --limit=10
// php artisan translation-workbench:apply-suspicious-key-restores --limit=10 --write
// php artisan translation-workbench:apply-suspicious-key-restores --write --only-apply
// php artisan translation-workbench:apply-suspicious-key-restores --suppress-dry-run-warning

namespace Gunreip\TranslationWorkbench\Console;

use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchTimelineRecorder;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchFinding;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchKey;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchLangValue;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchReview;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

#[Signature('translation-workbench:apply-suspicious-key-restores
    {--paths= : Comma-separated relative paths to limit suspicious key restores.}
    {--source-locale=en : Source locale used to restore literal text from lang/* values.}
    {--limit= : Maximum number of reviewed restore decisions to inspect.}
    {--write : Actually update source files. Without this option the command only reports what would be changed.}
    {--only-apply : Mark this run as an early pipeline apply step for already reviewed restore decisions.}
    {--suppress-dry-run-warning : Suppress the dry-run warning when the command is used as an orchestrated report refresh step.}')]
#[Description('Restore reviewed suspicious direct translation-key calls back to source literals, dry-run by default.')]
class ApplySuspiciousKeyRestores extends Command
{
    public function __construct(
        private readonly TranslationWorkbenchTimelineRecorder $timelineRecorder,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        if (! $this->hasRequiredTables()) {
            $this->error('Required Translation Workbench tables are missing. Run the workbench migrations first.');
            $this->writeReport([
                'generated_at' => now()->toISOString(),
                'write' => (bool) $this->option('write'),
                'summary' => ['error' => 'missing_required_tables'],
                'results' => [],
            ]);

            return self::FAILURE;
        }

        $write = (bool) $this->option('write');
        $reviews = $this->restoreReviews();
        $results = $reviews
            ->map(fn (TranslationWorkbenchReview $review): array => $this->restoreReview($review, $write))
            ->values();
        $timelineEventsCreated = $write ? $this->recordAppliedTimelineEvents($results) : 0;
        $diff = $this->buildDiff($results->whereIn('state', ['would_restore', 'restored'])->values()->all());
        $report = [
            'generated_at' => now()->toISOString(),
            'write' => $write,
            'only_apply' => (bool) $this->option('only-apply'),
            'paths' => $this->paths(),
            'source_locale' => $this->sourceLocale(),
            'summary' => [
                'reviewed_restore_decisions' => $reviews->count(),
                'would_restore' => $results->where('state', 'would_restore')->count(),
                'restored' => $results->where('state', 'restored')->count(),
                'already_restored' => $results->where('state', 'already_restored')->count(),
                'skipped' => $results->where('state', 'skipped')->count(),
                'stale_source' => $results->where('state', 'stale_source')->count(),
                'missing_source_lang_value' => $results->where('state', 'missing_source_lang_value')->count(),
                'diff_files' => count($diff['files']),
                'timeline_events_created' => $timelineEventsCreated,
            ],
            'diff' => $diff,
            'results' => $results->all(),
        ];
        $diffPath = $this->writeDiff($report);
        $report['diff_path'] = $diffPath;
        $reportPath = $this->writeReport($report);

        $this->components->info($write
            ? 'Suspicious key restores applied.'
            : 'Suspicious key restore dry-run finished.');
        $this->line('Reviewed restore decisions: ' . number_format((int) $report['summary']['reviewed_restore_decisions']));
        $this->line('Would restore: ' . number_format((int) $report['summary']['would_restore']));
        $this->line('Restored: ' . number_format((int) $report['summary']['restored']));
        $this->line('Already restored: ' . number_format((int) $report['summary']['already_restored']));
        $this->line('Stale source: ' . number_format((int) $report['summary']['stale_source']));
        $this->line('Missing source lang value: ' . number_format((int) $report['summary']['missing_source_lang_value']));
        $this->line('Timeline events created: ' . number_format((int) $report['summary']['timeline_events_created']));
        $this->line('JSON report: ' . $reportPath);
        $this->line('Patch report: ' . $diffPath);

        if (! $write && ! (bool) $this->option('suppress-dry-run-warning')) {
            $this->warn('Dry run only: no source files were changed. Re-run with --write to restore reviewed suspicious keys.');
        }

        return self::SUCCESS;
    }

    private function hasRequiredTables(): bool
    {
        return collect([
            'translation_workbench_reviews',
            'translation_workbench_findings',
            'translation_workbench_keys',
            'translation_workbench_lang_values',
            'translation_workbench_timeline_events',
        ])->every(static fn (string $table): bool => Schema::hasTable($table));
    }

    /**
     * @return Collection<int, TranslationWorkbenchReview>
     */
    private function restoreReviews(): Collection
    {
        $paths = $this->paths();
        $query = TranslationWorkbenchReview::query()
            ->where('review_type', 'suspicious_key_provenance')
            ->where('decision', 'needs_literal_restore')
            ->with(['finding', 'key'])
            ->latest('reviewed_at')
            ->latest('id');

        if ($paths !== []) {
            $query->where(function ($query) use ($paths): void {
                foreach ($paths as $path) {
                    $query->orWhere('meta->source_path', 'like', rtrim($path, '/') . '%');
                }
            });
        }

        if (($limit = $this->limit()) !== null) {
            $query->limit($limit);
        }

        return $query->get()
            ->unique(static fn (TranslationWorkbenchReview $review): string => implode("\n", [
                (string) ($review->meta['source_signature'] ?? ''),
                (string) ($review->meta['translation_key'] ?? ''),
            ]))
            ->values();
    }

    /**
     * @return array<string, mixed>
     */
    private function restoreReview(TranslationWorkbenchReview $review, bool $write): array
    {
        $meta = is_array($review->meta) ? $review->meta : [];
        $translationKey = trim((string) ($meta['translation_key'] ?? ''));
        $sourcePath = trim((string) ($meta['source_path'] ?? $review->finding?->source_path ?? ''));
        $sourceLine = $meta['source_line'] ?? $review->finding?->source_line;
        $rawExpression = trim((string) ($meta['raw_expression'] ?? $review->finding?->raw_expression ?? ''));
        $literalTextSuggested = trim((string) ($meta['literal_text_suggested'] ?? $review->finding?->literal_text_suggested ?? ''));
        $sourceLangValue = $this->restoreLiteralValue($translationKey, $rawExpression, $literalTextSuggested, $meta);
        $newExpression = $sourceLangValue !== null ? $this->literalExpression($rawExpression, $sourceLangValue) : null;
        $base = [
            'review_id' => $review->id,
            'finding_id' => $review->finding_id,
            'key_id' => $review->key_id,
            'source_path' => $sourcePath,
            'source_line' => $sourceLine,
            'translation_key' => $translationKey,
            'raw_expression' => $rawExpression,
            'literal_text_suggested' => $literalTextSuggested,
            'source_lang_value' => $sourceLangValue,
            'new_expression' => $newExpression,
        ];

        if ($translationKey === '' || $sourcePath === '' || $rawExpression === '') {
            return [
                ...$base,
                'state' => 'skipped',
                'reason' => 'missing_review_context',
            ];
        }

        if ($sourceLangValue === null || $sourceLangValue === '') {
            return [
                ...$base,
                'state' => 'missing_source_lang_value',
                'reason' => 'source_lang_value_missing_or_empty',
            ];
        }

        if ($newExpression !== null && $rawExpression === $newExpression) {
            return [
                ...$base,
                'state' => 'already_restored',
                'reason' => 'raw_expression_is_already_literal_expression',
                'occurrences' => 0,
                'new_occurrences' => 1,
                'matched_expression' => null,
            ];
        }

        $absolutePath = base_path($sourcePath);

        if (! File::exists($absolutePath)) {
            return [
                ...$base,
                'state' => 'skipped',
                'reason' => 'source_file_missing',
            ];
        }

        $source = (string) File::get($absolutePath);
        $matchedExpression = $rawExpression;
        $occurrences = substr_count($source, $matchedExpression);

        if ($occurrences === 0) {
            $fallbackExpression = $this->matchedTranslationExpression($source, $translationKey, $sourceLine);

            if ($fallbackExpression !== null) {
                $matchedExpression = $fallbackExpression;
                $occurrences = substr_count($source, $matchedExpression);
            }
        }

        $newOccurrences = $newExpression ? substr_count($source, $newExpression) : 0;

        if ($occurrences === 0) {
            return [
                ...$base,
                'state' => $newOccurrences > 0 ? 'already_restored' : 'stale_source',
                'reason' => $newOccurrences > 0
                    ? 'literal_expression_already_present'
                    : 'raw_expression_not_found_in_current_source_file',
                'occurrences' => $occurrences,
                'new_occurrences' => $newOccurrences,
                'matched_expression' => null,
            ];
        }

        if (! $write) {
            return [
                ...$base,
                'state' => 'would_restore',
                'reason' => 'dry_run_only',
                'occurrences' => $occurrences,
                'matched_expression' => $matchedExpression,
            ];
        }

        File::put($absolutePath, str_replace($matchedExpression, (string) $newExpression, $source));

        return [
            ...$base,
            'state' => 'restored',
            'reason' => 'source_key_restored_to_literal',
            'occurrences' => $occurrences,
            'matched_expression' => $matchedExpression,
        ];
    }

    private function sourceLangValue(string $translationKey): ?string
    {
        if ($translationKey === '') {
            return null;
        }

        $value = TranslationWorkbenchLangValue::query()
            ->where('locale', $this->sourceLocale())
            ->where('translation_key', $translationKey)
            ->orderByRaw("CASE WHEN status = 'active' THEN 0 WHEN status = 'obsolete' THEN 1 ELSE 2 END")
            ->orderBy('id')
            ->value('value');

        return is_string($value) ? $value : null;
    }

    /**
     * @param  array<string, mixed>  $meta
     */
    private function restoreLiteralValue(string $translationKey, string $rawExpression, string $literalTextSuggested, array $meta): ?string
    {
        $sourceLangValue = $this->sourceLangValue($translationKey);

        if (is_string($sourceLangValue) && $sourceLangValue !== '') {
            return $sourceLangValue;
        }

        $metaSourceValue = trim((string) ($meta['source_lang_value'] ?? ''));

        if ($metaSourceValue !== '') {
            return $metaSourceValue;
        }

        $rawLiteral = $this->firstStringArgument($rawExpression);

        if ($rawLiteral !== null && $rawLiteral !== '' && $rawLiteral !== $translationKey) {
            return $rawLiteral;
        }

        if ($literalTextSuggested !== '') {
            return Str::of($literalTextSuggested)
                ->replace(['_', '-'], ' ')
                ->squish()
                ->headline()
                ->toString();
        }

        return null;
    }

    private function firstStringArgument(string $expression): ?string
    {
        if (! preg_match('/\(\s*([\'"])((?:\\\\.|(?!\1).)*)\1/s', $expression, $matches)) {
            return null;
        }

        $value = stripcslashes((string) ($matches[2] ?? ''));

        return $value !== '' ? $value : null;
    }

    private function literalExpression(string $rawExpression, string $literal): string
    {
        $function = match (true) {
            str_starts_with(ltrim($rawExpression), 'trans(') => 'trans',
            str_starts_with(ltrim($rawExpression), '@lang(') => '@lang',
            str_starts_with(ltrim($rawExpression), 'Lang::get(') => 'Lang::get',
            default => '__',
        };

        return $function . '(' . var_export($literal, true) . ')';
    }

    private function matchedTranslationExpression(string $source, string $translationKey, mixed $sourceLine): ?string
    {
        if ($translationKey === '') {
            return null;
        }

        $quotedKey = preg_quote($translationKey, '/');
        $patterns = [
            '/__\s*\(\s*([\'"])' . $quotedKey . '\1\s*,?\s*\)/s',
            '/trans\s*\(\s*([\'"])' . $quotedKey . '\1\s*,?\s*\)/s',
            '/@lang\s*\(\s*([\'"])' . $quotedKey . '\1\s*,?\s*\)/s',
            '/Lang::get\s*\(\s*([\'"])' . $quotedKey . '\1\s*,?\s*\)/s',
        ];
        $matches = [];

        foreach ($patterns as $pattern) {
            if (! preg_match_all($pattern, $source, $patternMatches, PREG_OFFSET_CAPTURE)) {
                continue;
            }

            foreach ($patternMatches[0] as $match) {
                $matches[] = [
                    'expression' => (string) $match[0],
                    'offset' => (int) $match[1],
                ];
            }
        }

        if ($matches === []) {
            return null;
        }

        $line = (int) $sourceLine;

        if ($line <= 0) {
            return (string) $matches[0]['expression'];
        }

        $lineOffsets = $this->lineOffsets($source);

        return collect($matches)
            ->sortBy(function (array $match) use ($line, $lineOffsets): int {
                $matchLine = $this->lineForOffset($lineOffsets, (int) $match['offset']);

                return abs($matchLine - $line);
            })
            ->value('expression');
    }

    /**
     * @return array<int, int>
     */
    private function lineOffsets(string $source): array
    {
        $offsets = [1 => 0];
        $offset = 0;

        foreach (explode("\n", $source) as $line => $content) {
            if ($line === 0) {
                $offset += strlen($content) + 1;

                continue;
            }

            $offsets[$line + 1] = $offset;
            $offset += strlen($content) + 1;
        }

        return $offsets;
    }

    /**
     * @param  array<int, int>  $lineOffsets
     */
    private function lineForOffset(array $lineOffsets, int $offset): int
    {
        $line = 1;

        foreach ($lineOffsets as $candidateLine => $candidateOffset) {
            if ($candidateOffset > $offset) {
                break;
            }

            $line = $candidateLine;
        }

        return $line;
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $results
     */
    private function recordAppliedTimelineEvents(Collection $results): int
    {
        $appliedRows = $results->where('state', 'restored')->values();

        if ($appliedRows->isEmpty()) {
            return 0;
        }

        $reviews = TranslationWorkbenchReview::query()
            ->whereIn('id', $appliedRows->pluck('review_id')->filter()->unique()->values())
            ->get()
            ->keyBy('id');
        $keys = TranslationWorkbenchKey::query()
            ->whereIn('id', $appliedRows->pluck('key_id')->filter()->unique()->values())
            ->get()
            ->keyBy('id');
        $findings = TranslationWorkbenchFinding::query()
            ->whereIn('id', $appliedRows->pluck('finding_id')->filter()->unique()->values())
            ->get()
            ->keyBy('id');
        $created = 0;

        foreach ($appliedRows as $row) {
            $review = $reviews->get((int) ($row['review_id'] ?? 0));

            $this->timelineRecorder->record(
                eventType: 'suspicious_key_restored_to_literal',
                key: $keys->get((int) ($row['key_id'] ?? 0)),
                finding: $findings->get((int) ($row['finding_id'] ?? 0)),
                review: $review instanceof TranslationWorkbenchReview ? $review : null,
                oldValues: [
                    'raw_expression' => $row['raw_expression'] ?? null,
                    'matched_expression' => $row['matched_expression'] ?? null,
                    'translation_key' => $row['translation_key'] ?? null,
                    'source_path' => $row['source_path'] ?? null,
                    'source_line' => $row['source_line'] ?? null,
                ],
                newValues: [
                    'new_expression' => $row['new_expression'] ?? null,
                    'source_lang_value' => $row['source_lang_value'] ?? null,
                    'source_path' => $row['source_path'] ?? null,
                    'source_line' => $row['source_line'] ?? null,
                ],
                context: [
                    'source' => 'translation-workbench:apply-suspicious-key-restores',
                    'review_id' => $row['review_id'] ?? null,
                    'reason' => $row['reason'] ?? null,
                ],
            );

            $created++;
        }

        return $created;
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
                    $matchedExpression = (string) ($row['matched_expression'] ?? '');
                    $newExpression = (string) ($row['new_expression'] ?? '');
                    $sourceExpression = $matchedExpression !== '' ? $matchedExpression : $rawExpression;

                    if ($sourceExpression === '' || $newExpression === '' || substr_count($newContent, $sourceExpression) < 1) {
                        continue;
                    }

                    $newContent = str_replace($sourceExpression, $newExpression, $newContent);
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
        $externalDiff = $this->externalUnifiedDiffForFile($sourcePath, $oldContent, $newContent);

        if ($externalDiff !== null) {
            return $externalDiff;
        }

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

    private function externalUnifiedDiffForFile(string $sourcePath, string $oldContent, string $newContent): ?string
    {
        if (! function_exists('proc_open')) {
            return null;
        }

        $oldFile = tempnam(sys_get_temp_dir(), 'twb_old_');
        $newFile = tempnam(sys_get_temp_dir(), 'twb_new_');

        if (! is_string($oldFile) || ! is_string($newFile)) {
            return null;
        }

        File::put($oldFile, $oldContent);
        File::put($newFile, $newContent);

        $command = [
            'diff',
            '-u',
            '--label',
            'a/' . $sourcePath,
            '--label',
            'b/' . $sourcePath,
            $oldFile,
            $newFile,
        ];
        $process = proc_open($command, [
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ], $pipes);

        if (! is_resource($process)) {
            @unlink($oldFile);
            @unlink($newFile);

            return null;
        }

        $output = stream_get_contents($pipes[1]);
        $errorOutput = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        $exitCode = proc_close($process);
        @unlink($oldFile);
        @unlink($newFile);

        if (! in_array($exitCode, [0, 1], true) || ! is_string($output)) {
            return null;
        }

        if ($output === '' && is_string($errorOutput) && $errorOutput !== '') {
            return null;
        }

        return $output;
    }

    /**
     * @param  array<string, mixed>  $report
     */
    private function writeReport(array $report): string
    {
        $path = storage_path('translation-workbench/' . Str::of((string) $this->getName())->replace(':', '-') . '.json');
        $directory = dirname($path);

        File::ensureDirectoryExists($directory);
        @chmod($directory, 0777);

        if (File::exists($path) && ! is_writable($path)) {
            @unlink($path);
        }

        File::put($path, json_encode([
            'command' => $this->getName(),
            ...$report,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL);
        @chmod($path, 0666);

        return $path;
    }

    /**
     * @param  array<string, mixed>  $report
     */
    private function writeDiff(array $report): string
    {
        $path = storage_path('translation-workbench/' . Str::of((string) $this->getName())->replace(':', '-') . '.patch');
        $content = (string) ($report['diff']['content'] ?? '');
        $directory = dirname($path);

        File::ensureDirectoryExists($directory);
        @chmod($directory, 0777);

        if (File::exists($path) && ! is_writable($path)) {
            @unlink($path);
        }

        File::put($path, $content === '' ? '' : rtrim($content) . PHP_EOL);
        @chmod($path, 0666);

        return $path;
    }

    /**
     * @return array<int, string>
     */
    private function paths(): array
    {
        $paths = trim((string) $this->option('paths'));

        if ($paths === '') {
            return [];
        }

        return collect(explode(',', $paths))
            ->map(static fn (string $path): string => trim(str_replace('\\', '/', $path)))
            ->filter()
            ->values()
            ->all();
    }

    private function sourceLocale(): string
    {
        $sourceLocale = trim((string) $this->option('source-locale'));

        return $sourceLocale !== '' ? $sourceLocale : 'en';
    }

    private function limit(): ?int
    {
        $limit = trim((string) $this->option('limit'));

        if ($limit === '') {
            return null;
        }

        return max(1, (int) $limit);
    }
}
