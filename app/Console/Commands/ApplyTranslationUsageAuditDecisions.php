<?php

// app/Console/Commands/ApplyTranslationUsageAuditDecisions.php

namespace App\Console\Commands;

use App\Models\TranslationUsageAuditDecision;
use App\Support\ActivityLog\ConsoleActivityContext;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Throwable;

#[Signature('translations:usage-decisions:apply {--write : Write changes to files. Defaults to dry-run.} {--sample=20 : Maximum number of items written to sample report files.}')]
#[Description('Apply ready translation usage audit decisions to source files.')]
class ApplyTranslationUsageAuditDecisions extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $write = (bool) $this->option('write');
        $sampleLimit = max(1, (int) $this->option('sample'));
        $outputDirectory = storage_path('audits/translations/usage-decisions');

        File::ensureDirectoryExists($outputDirectory);

        $applyItems = collect();

        TranslationUsageAuditDecision::query()
            ->with([
                'usages' => fn ($query) => $query
                    ->where('include_in_change', true)
                    ->orderBy('file')
                    ->orderBy('line')
                    ->orderBy('id'),
            ])
            ->where('decision_action', 'unify_to_target_key')
            ->where('decision_status', 'ready')
            ->whereNotNull('target_translation_key')
            ->orderBy('id')
            ->chunkById(100, function (Collection $decisions) use (&$applyItems): void {
                foreach ($decisions as $decision) {
                    foreach ($decision->usages as $usage) {
                        if (trim((string) $usage->target_translation_key) === '') {
                            continue;
                        }

                        $replacementPreview = $this->buildReplacementPreview(
                            raw: (string) $usage->raw,
                            detectedFunction: (string) $usage->detected_function,
                            targetTranslationKey: (string) $usage->target_translation_key,
                        );

                        $fileVerification = $this->verifyUsageFileLine(
                            file: (string) $usage->file,
                            line: (int) $usage->line,
                            raw: (string) $usage->raw,
                        );

                        $canApply = (bool) $replacementPreview['can_build_replacement']
                            && (bool) $fileVerification['file_exists']
                            && (bool) $fileVerification['line_matches_raw'];

                        $applyItems->push([
                            'decision_id' => $decision->id,
                            'usage_id' => $usage->id,
                            'translation_key_id' => $usage->translation_key_id,
                            'current_translation_key' => $usage->current_translation_key,
                            'target_translation_key' => $usage->target_translation_key,
                            'file' => $usage->file,
                            'line' => $usage->line,
                            'detected_function' => $usage->detected_function,
                            'classification' => $usage->classification,
                            'change_status_before' => $usage->change_status,
                            'is_stale' => (bool) $usage->is_stale,
                            'raw' => $usage->raw,
                            'original_raw' => $usage->original_raw,
                            'proposed_raw' => $replacementPreview['proposed_raw'],
                            'can_build_replacement' => $replacementPreview['can_build_replacement'],
                            'replacement_kind' => $replacementPreview['replacement_kind'],
                            'replacement_warning' => $replacementPreview['replacement_warning'],
                            'file_exists' => $fileVerification['file_exists'],
                            'line_matches_raw' => $fileVerification['line_matches_raw'],
                            'line_content_before' => $fileVerification['line_content'],
                            'replacement_mode' => $fileVerification['replacement_mode'],
                            'verification_warning' => $fileVerification['verification_warning'],
                            'can_apply' => $canApply,
                            'applied' => false,
                            'skipped' => ! $canApply,
                            'skip_reason' => $canApply ? null : $this->applySkipReason($replacementPreview, $fileVerification),
                            'line_content_after' => null,
                        ]);
                    }
                }
            });

        if ($write) {
            $applyItems = $this->writeApplyItems($applyItems);
            $this->markAppliedItems($applyItems);
        }

        $payload = [
            'meta' => [
                'command' => 'translations:usage-decisions:apply',
                'dry_run' => ! $write,
                'write' => $write,
                'decision_action' => 'unify_to_target_key',
                'decision_status' => 'ready',
                'item_count' => $applyItems->count(),
                'can_apply_count' => $applyItems->where('can_apply', true)->count(),
                'applied_count' => $applyItems->where('applied', true)->count(),
                'skipped_count' => $applyItems->where('skipped', true)->count(),
                'file_count' => $applyItems
                    ->pluck('file')
                    ->filter()
                    ->unique()
                    ->count(),
                'written_file_count' => $applyItems
                    ->where('applied', true)
                    ->pluck('file')
                    ->filter()
                    ->unique()
                    ->count(),
            ],
            'items' => $applyItems->values()->all(),
        ];

        $samplePayload = [
            'meta' => [
                ...$payload['meta'],
                'sample' => true,
                'sample_limit' => $sampleLimit,
            ],
            'items' => $applyItems->take($sampleLimit)->values()->all(),
        ];

        File::put(
            $outputDirectory.'/apply.json',
            json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE).PHP_EOL,
        );

        File::put(
            $outputDirectory.'/apply.sample.json',
            json_encode($samplePayload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE).PHP_EOL,
        );

        File::put(
            $outputDirectory.'/apply.md',
            $this->renderApplyMarkdown($applyItems, ! $write),
        );

        File::put(
            $outputDirectory.'/apply.sample.md',
            $this->renderApplyMarkdown($applyItems->take($sampleLimit), ! $write),
        );

        $this->info(
            $write
                ? 'Translation usage decisions applied.'
                : 'Translation usage decision apply dry-run written.'
        );

        $this->line('Directory: '.$outputDirectory);
        $this->line('Dry-run: '.(! $write ? 'yes' : 'no'));
        $this->line('Items: '.$applyItems->count());
        $this->line('Can apply: '.$applyItems->where('can_apply', true)->count());
        $this->line('Applied: '.$applyItems->where('applied', true)->count());
        $this->line('Skipped: '.$applyItems->where('skipped', true)->count());

        $this->logRunCompletedActivity($payload['meta']);

        return self::SUCCESS;
    }

    /**
     * Write eligible apply items to files.
     *
     * @param  Collection<int, array<string, mixed>>  $applyItems
     * @return Collection<int, array<string, mixed>>
     */
    private function writeApplyItems(Collection $applyItems): Collection
    {
        $items = $applyItems->values();

        $items
            ->where('can_apply', true)
            ->groupBy(static fn (array $item): string => (string) ($item['file'] ?? ''))
            ->each(function (Collection $fileItems, string $file) use (&$items): void {
                $file = trim($file);

                if ($file === '') {
                    $items = $this->markFileGroupSkipped(
                        items: $items,
                        fileItems: $fileItems,
                        reason: 'Usage file path is empty.',
                    );

                    return;
                }

                $absolutePath = base_path($file);

                if (! File::isFile($absolutePath)) {
                    $items = $this->markFileGroupSkipped(
                        items: $items,
                        fileItems: $fileItems,
                        reason: 'Usage file does not exist at the expected path.',
                    );

                    return;
                }

                $contents = File::get($absolutePath);
                $lines = $this->splitLinesPreservingEndings($contents);
                $fileWasChanged = false;

                foreach ($fileItems->sortBy('line')->values() as $fileItem) {
                    $usageId = (int) ($fileItem['usage_id'] ?? 0);
                    $line = (int) ($fileItem['line'] ?? 0);
                    $lineIndex = $line - 1;
                    $raw = (string) ($fileItem['raw'] ?? '');
                    $proposedRaw = (string) ($fileItem['proposed_raw'] ?? '');

                    $itemIndex = $items->search(static fn (array $item): bool => (int) ($item['usage_id'] ?? 0) === $usageId);

                    if ($itemIndex === false) {
                        continue;
                    }

                    if ($line <= 0 || ! array_key_exists($lineIndex, $lines)) {
                        $items = $this->updateApplyItem(
                            items: $items,
                            itemIndex: $itemIndex,
                            values: [
                                'applied' => false,
                                'skipped' => true,
                                'skip_reason' => 'Usage line does not exist in the current file.',
                            ],
                        );

                        continue;
                    }

                    if ($raw === '' || $proposedRaw === '') {
                        $items = $this->updateApplyItem(
                            items: $items,
                            itemIndex: $itemIndex,
                            values: [
                                'applied' => false,
                                'skipped' => true,
                                'skip_reason' => 'Raw usage or proposed raw replacement is empty.',
                            ],
                        );

                        continue;
                    }

                    if (! str_contains($lines[$lineIndex], $raw)) {
                        $items = $this->updateApplyItem(
                            items: $items,
                            itemIndex: $itemIndex,
                            values: [
                                'applied' => false,
                                'skipped' => true,
                                'skip_reason' => 'Current line no longer contains the stored raw usage string.',
                                'line_content_after' => $this->lineWithoutEnding($lines[$lineIndex]),
                            ],
                        );

                        continue;
                    }

                    $lines[$lineIndex] = $this->replaceFirst($raw, $proposedRaw, $lines[$lineIndex]);

                    $items = $this->updateApplyItem(
                        items: $items,
                        itemIndex: $itemIndex,
                        values: [
                            'applied' => true,
                            'skipped' => false,
                            'skip_reason' => null,
                            'line_content_after' => $this->lineWithoutEnding($lines[$lineIndex]),
                        ],
                    );

                    $fileWasChanged = true;
                }

                if ($fileWasChanged) {
                    File::put($absolutePath, implode('', $lines));
                }
            });

        return $items->values();
    }

    /**
     * Update one apply item inside the collection.
     *
     * @param  Collection<int, array<string, mixed>>  $items
     * @param  array<string, mixed>  $values
     * @return Collection<int, array<string, mixed>>
     */
    private function updateApplyItem(Collection $items, int|string $itemIndex, array $values): Collection
    {
        $item = $items->get($itemIndex);

        if (! is_array($item)) {
            return $items;
        }

        $items->put($itemIndex, [
            ...$item,
            ...$values,
        ]);

        return $items;
    }

    /**
     * Mark all successfully applied usage rows and decisions.
     *
     * @param  Collection<int, array<string, mixed>>  $applyItems
     */
    private function markAppliedItems(Collection $applyItems): void
    {
        $appliedUsageIds = $applyItems
            ->where('applied', true)
            ->pluck('usage_id')
            ->map(static fn (mixed $usageId): int => (int) $usageId)
            ->filter()
            ->unique()
            ->values();

        if ($appliedUsageIds->isEmpty()) {
            return;
        }

        TranslationUsageAuditDecision::query()
            ->whereHas('usages', fn ($query) => $query->whereIn('id', $appliedUsageIds->all()))
            ->with('usages')
            ->get()
            ->each(function (TranslationUsageAuditDecision $decision) use ($appliedUsageIds): void {
                foreach ($decision->usages as $usage) {
                    if ($appliedUsageIds->contains((int) $usage->id)) {
                        $usage->forceFill([
                            'change_status' => 'applied',
                        ])->save();
                    }
                }

                $hasOpenIncludedUsages = $decision->usages
                    ->filter(static fn ($usage): bool => (bool) $usage->include_in_change)
                    ->contains(static fn ($usage): bool => $usage->change_status !== 'applied');

                if (! $hasOpenIncludedUsages) {
                    $decision->forceFill([
                        'decision_status' => 'applied',
                    ])->save();
                }
            });
    }

    /**
     * Mark a whole file group as skipped.
     *
     * @param  Collection<int, array<string, mixed>>  $items
     * @param  Collection<int, array<string, mixed>>  $fileItems
     * @return Collection<int, array<string, mixed>>
     */
    private function markFileGroupSkipped(Collection $items, Collection $fileItems, string $reason): Collection
    {
        $usageIds = $fileItems
            ->pluck('usage_id')
            ->map(static fn (mixed $usageId): int => (int) $usageId)
            ->filter()
            ->all();

        return $items
            ->map(function (array $item) use ($usageIds, $reason): array {
                if (in_array((int) ($item['usage_id'] ?? 0), $usageIds, true)) {
                    $item['applied'] = false;
                    $item['skipped'] = true;
                    $item['skip_reason'] = $reason;
                }

                return $item;
            })
            ->values();
    }

    /**
     * Resolve why an item cannot be applied.
     *
     * @param  array<string, mixed>  $replacementPreview
     * @param  array<string, mixed>  $fileVerification
     */
    private function applySkipReason(array $replacementPreview, array $fileVerification): ?string
    {
        if (! (bool) ($replacementPreview['can_build_replacement'] ?? false)) {
            return (string) ($replacementPreview['replacement_warning'] ?? 'Replacement cannot be built.');
        }

        if (! (bool) ($fileVerification['file_exists'] ?? false)) {
            return (string) ($fileVerification['verification_warning'] ?? 'Usage file does not exist.');
        }

        if (! (bool) ($fileVerification['line_matches_raw'] ?? false)) {
            return (string) ($fileVerification['verification_warning'] ?? 'Usage line does not match raw value.');
        }

        return null;
    }

    /**
     * Verify that the stored usage still exists at the expected file and line.
     *
     * @return array{
     *     file_exists: bool,
     *     line_matches_raw: bool,
     *     line_content: string|null,
     *     replacement_mode: string,
     *     verification_warning: string|null
     * }
     */
    private function verifyUsageFileLine(string $file, int $line, string $raw): array
    {
        $file = trim($file);
        $raw = trim($raw);

        if ($file === '') {
            return [
                'file_exists' => false,
                'line_matches_raw' => false,
                'line_content' => null,
                'replacement_mode' => 'missing_file_path',
                'verification_warning' => 'Usage file path is empty.',
            ];
        }

        $absolutePath = base_path($file);

        if (! File::isFile($absolutePath)) {
            return [
                'file_exists' => false,
                'line_matches_raw' => false,
                'line_content' => null,
                'replacement_mode' => 'file_not_found',
                'verification_warning' => 'Usage file does not exist at the expected path.',
            ];
        }

        if ($line <= 0) {
            return [
                'file_exists' => true,
                'line_matches_raw' => false,
                'line_content' => null,
                'replacement_mode' => 'invalid_line',
                'verification_warning' => 'Usage line is missing or invalid.',
            ];
        }

        $contents = File::get($absolutePath);
        $lines = $this->splitLinesPreservingEndings($contents);

        if (! array_key_exists($line - 1, $lines)) {
            return [
                'file_exists' => true,
                'line_matches_raw' => false,
                'line_content' => null,
                'replacement_mode' => 'line_not_found',
                'verification_warning' => 'Usage line does not exist in the current file.',
            ];
        }

        $lineContent = $this->lineWithoutEnding($lines[$line - 1]);

        if ($raw === '') {
            return [
                'file_exists' => true,
                'line_matches_raw' => false,
                'line_content' => $lineContent,
                'replacement_mode' => 'empty_raw',
                'verification_warning' => 'Raw usage string is empty.',
            ];
        }

        if (! str_contains($lineContent, $raw)) {
            return [
                'file_exists' => true,
                'line_matches_raw' => false,
                'line_content' => $lineContent,
                'replacement_mode' => 'line_does_not_contain_raw',
                'verification_warning' => 'Current line does not contain the stored raw usage string.',
            ];
        }

        return [
            'file_exists' => true,
            'line_matches_raw' => true,
            'line_content' => $lineContent,
            'replacement_mode' => 'line_contains_raw',
            'verification_warning' => null,
        ];
    }

    /**
     * Build the exact raw replacement preview for one usage.
     *
     * @return array{
     *     proposed_raw: string|null,
     *     can_build_replacement: bool,
     *     replacement_kind: string,
     *     replacement_warning: string|null
     * }
     */
    private function buildReplacementPreview(string $raw, string $detectedFunction, string $targetTranslationKey): array
    {
        $raw = trim($raw);
        $detectedFunction = trim($detectedFunction);
        $targetTranslationKey = trim($targetTranslationKey);

        if ($raw === '') {
            return [
                'proposed_raw' => null,
                'can_build_replacement' => false,
                'replacement_kind' => 'empty_raw',
                'replacement_warning' => 'Raw usage string is empty.',
            ];
        }

        if ($targetTranslationKey === '') {
            return [
                'proposed_raw' => null,
                'can_build_replacement' => false,
                'replacement_kind' => 'empty_target_translation_key',
                'replacement_warning' => 'Target translation key is empty.',
            ];
        }

        if ($detectedFunction !== '__') {
            return [
                'proposed_raw' => null,
                'can_build_replacement' => false,
                'replacement_kind' => 'unsupported_detected_function',
                'replacement_warning' => 'Only __() translation calls are supported in this apply step.',
            ];
        }

        $proposedRaw = preg_replace(
            "/^__\\(\\s*(['\"])(.*?)\\1\\s*\\)$/",
            "__('$targetTranslationKey')",
            $raw,
            1,
            $replacementCount,
        );

        if ($replacementCount !== 1 || ! is_string($proposedRaw)) {
            return [
                'proposed_raw' => null,
                'can_build_replacement' => false,
                'replacement_kind' => 'unsupported_raw_pattern',
                'replacement_warning' => 'Raw usage string is not a simple __() call with one string argument.',
            ];
        }

        return [
            'proposed_raw' => $proposedRaw,
            'can_build_replacement' => true,
            'replacement_kind' => 'translation_call_string_argument',
            'replacement_warning' => null,
        ];
    }

    /**
     * Render a readable markdown apply report.
     *
     * @param  Collection<int, array<string, mixed>>  $applyItems
     */
    private function renderApplyMarkdown(Collection $applyItems, bool $dryRun): string
    {
        $lines = [
            '# Translation Usage Decision Apply Report',
            '',
            '- Command: `translations:usage-decisions:apply`',
            '- Dry-run: `'.($dryRun ? 'yes' : 'no').'`',
            '- Items: '.$applyItems->count(),
            '- Can apply: '.$applyItems->where('can_apply', true)->count(),
            '- Applied: '.$applyItems->where('applied', true)->count(),
            '- Skipped: '.$applyItems->where('skipped', true)->count(),
            '',
        ];

        if ($dryRun) {
            $lines[] = '> Dry-run only. No files were changed.';
            $lines[] = '';
        }

        foreach ($applyItems as $item) {
            $lines[] = '## Usage #'.($item['usage_id'] ?? '—');
            $lines[] = '';
            $lines[] = '- Decision: #'.($item['decision_id'] ?? '—');
            $lines[] = '- File: `'.($item['file'] ?? '—').'`';
            $lines[] = '- Line: '.((int) ($item['line'] ?? 0) > 0 ? (int) $item['line'] : '—');
            $lines[] = '- Current key: `'.($item['current_translation_key'] ?? '—').'`';
            $lines[] = '- Target key: `'.($item['target_translation_key'] ?? '—').'`';
            $lines[] = '- Can apply: `'.((bool) ($item['can_apply'] ?? false) ? 'yes' : 'no').'`';
            $lines[] = '- Applied: `'.((bool) ($item['applied'] ?? false) ? 'yes' : 'no').'`';

            if (trim((string) ($item['skip_reason'] ?? '')) !== '') {
                $lines[] = '- Skip reason: '.trim((string) $item['skip_reason']);
            }

            if (trim((string) ($item['line_content_before'] ?? '')) !== '') {
                $lines[] = '- Line before: `'.$item['line_content_before'].'`';
            }

            if (trim((string) ($item['line_content_after'] ?? '')) !== '') {
                $lines[] = '- Line after: `'.$item['line_content_after'].'`';
            }

            $lines[] = '';
            $lines[] = '```diff';
            $lines[] = '--- '.((string) ($item['file'] ?? '') !== '' ? (string) $item['file'] : 'unknown');
            $lines[] = '+++ '.((string) ($item['file'] ?? '') !== '' ? (string) $item['file'] : 'unknown');
            $lines[] = '@@ Line '.((int) ($item['line'] ?? 0) > 0 ? (int) $item['line'] : '?').' @@';

            if ((bool) ($item['can_apply'] ?? false)) {
                $lines[] = '- '.(string) ($item['raw'] ?? '');
                $lines[] = '+ '.(string) ($item['proposed_raw'] ?? '');
            } else {
                $lines[] = '! '.((string) ($item['raw'] ?? '') !== '' ? (string) $item['raw'] : 'No raw usage available.');
            }

            $lines[] = '```';
            $lines[] = '';
        }

        return implode(PHP_EOL, $lines).PHP_EOL;
    }

    /**
     * Split file contents into lines while preserving original line endings.
     *
     * @return array<int, string>
     */
    private function splitLinesPreservingEndings(string $contents): array
    {
        if ($contents === '') {
            return [];
        }

        return preg_split('/(?<=\r\n)|(?<=\n)|(?<=\r)/', $contents) ?: [];
    }

    /**
     * Remove the line ending from one line.
     */
    private function lineWithoutEnding(string $line): string
    {
        return preg_replace('/\r\n|\n|\r$/', '', $line) ?? $line;
    }

    /**
     * Replace the first occurrence of a string.
     */
    private function replaceFirst(string $search, string $replace, string $subject): string
    {
        $position = strpos($subject, $search);

        if ($position === false) {
            return $subject;
        }

        return substr($subject, 0, $position)
            .$replace
            .substr($subject, $position + strlen($search));
    }

    /**
     * @param  array<string, mixed>  $meta
     */
    private function logRunCompletedActivity(array $meta): void
    {
        try {
            $activity = activity('translations')
                ->event('translations.usage_decisions.apply.completed');

            $activity
                ->withProperties(ConsoleActivityContext::merge($this, [
                    'summary' => $meta,
                ]))
                ->log('Translation usage decision apply command completed');
        } catch (Throwable $exception) {
            $this->warn('Activity log write failed: '.$exception->getMessage());
        }
    }
}
