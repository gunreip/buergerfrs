<?php

// app/Console/Commands/PreviewTranslationUsageAuditDecisions.php

// php artisan translations:usage-decisions:preview

namespace App\Console\Commands;

use App\Models\TranslationUsageAuditDecision;
use App\Support\ActivityLog\ConsoleActivityContext;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Throwable;

#[Signature('translations:usage-decisions:preview')]
#[Description('Create preview files for ready translation usage audit decisions.')]
class PreviewTranslationUsageAuditDecisions extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $outputDirectory = storage_path('audits/translations/usage-decisions');

        File::ensureDirectoryExists($outputDirectory);

        $previewItems = collect();

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
            ->chunkById(100, function (Collection $decisions) use (&$previewItems): void {
                foreach ($decisions as $decision) {
                    $usageItems = $decision->usages
                        ->filter(static fn ($usage): bool => trim((string) $usage->target_translation_key) !== '')
                        ->map(function ($usage): array {
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

                            return [
                                'usage_id' => $usage->id,
                                'translation_key_id' => $usage->translation_key_id,
                                'current_translation_key' => $usage->current_translation_key,
                                'target_translation_key' => $usage->target_translation_key,
                                'file' => $usage->file,
                                'line' => $usage->line,
                                'detected_function' => $usage->detected_function,
                                'classification' => $usage->classification,
                                'change_status' => $usage->change_status,
                                'is_stale' => (bool) $usage->is_stale,
                                'raw' => $usage->raw,
                                'original_raw' => $usage->original_raw,
                                'proposed_raw' => $replacementPreview['proposed_raw'],
                                'can_build_replacement' => $replacementPreview['can_build_replacement'],
                                'replacement_kind' => $replacementPreview['replacement_kind'],
                                'replacement_warning' => $replacementPreview['replacement_warning'],
                                'file_exists' => $fileVerification['file_exists'],
                                'line_matches_raw' => $fileVerification['line_matches_raw'],
                                'line_content' => $fileVerification['line_content'],
                                'replacement_mode' => $fileVerification['replacement_mode'],
                                'verification_warning' => $fileVerification['verification_warning'],
                            ];
                        })
                        ->values();

                    if ($usageItems->isEmpty()) {
                        continue;
                    }

                    $previewItems->push([
                        'decision_id' => $decision->id,
                        'audit_type' => $decision->audit_type,
                        'normalized_value' => $decision->normalized_value,
                        'normalized_value_hash' => $decision->normalized_value_hash,
                        'source_locale' => $decision->source_locale,
                        'source_value' => $decision->source_value,
                        'suggested_translation_key' => $decision->suggested_translation_key,
                        'target_translation_key' => $decision->target_translation_key,
                        'decision_action' => $decision->decision_action,
                        'decision_status' => $decision->decision_status,
                        'review_note' => $decision->review_note,
                        'usage_count' => $usageItems->count(),
                        'usages' => $usageItems->all(),
                    ]);
                }
            });

        $payload = [
            'meta' => [
                'command' => 'translations:usage-decisions:preview',
                'decision_action' => 'unify_to_target_key',
                'decision_status' => 'ready',
                'decision_count' => $previewItems->count(),
                'usage_count' => $previewItems->sum('usage_count'),
            ],
            'items' => $previewItems->values()->all(),
        ];

        $samplePayload = [
            'meta' => [
                ...$payload['meta'],
                'sample' => true,
                'sample_limit' => 20,
            ],
            'items' => $previewItems->take(20)->values()->all(),
        ];

        File::put(
            $outputDirectory.'/preview.json',
            json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE).PHP_EOL,
        );

        File::put(
            $outputDirectory.'/preview.sample.json',
            json_encode($samplePayload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE).PHP_EOL,
        );

        File::put(
            $outputDirectory.'/preview.md',
            $this->renderMarkdownPreview($previewItems),
        );

        File::put(
            $outputDirectory.'/preview.sample.md',
            $this->renderMarkdownPreview($previewItems->take(20)),
        );

        File::put(
            $outputDirectory.'/preview.diff.md',
            $this->renderDiffPreview($previewItems),
        );

        File::put(
            $outputDirectory.'/preview.diff.sample.md',
            $this->renderDiffPreview($previewItems->take(20)),
        );

        $this->info('Translation usage decision preview written.');
        $this->line('Directory: '.$outputDirectory);
        $this->line('Decisions: '.$previewItems->count());
        $this->line('Usages: '.$previewItems->sum('usage_count'));

        $this->logRunCompletedActivity($payload['meta']);

        return self::SUCCESS;
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

        $lines = file($absolutePath, FILE_IGNORE_NEW_LINES);

        if (! is_array($lines) || ! array_key_exists($line - 1, $lines)) {
            return [
                'file_exists' => true,
                'line_matches_raw' => false,
                'line_content' => null,
                'replacement_mode' => 'line_not_found',
                'verification_warning' => 'Usage line does not exist in the current file.',
            ];
        }

        $lineContent = (string) $lines[$line - 1];

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
                'replacement_warning' => 'Only __() translation calls are supported in this preview step.',
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
     * Render a readable diff-style markdown preview.
     *
     * @param  Collection<int, array<string, mixed>>  $previewItems
     */
    private function renderDiffPreview(Collection $previewItems): string
    {
        $lines = [
            '# Translation Usage Decision Diff Preview',
            '',
            '- Command: `translations:usage-decisions:preview`',
            '- Decisions: '.$previewItems->count(),
            '- Usages: '.$previewItems->sum('usage_count'),
            '',
            '> Preview only. No files were changed.',
            '',
        ];

        foreach ($previewItems as $item) {
            $lines[] = '## Decision #'.$item['decision_id'];
            $lines[] = '';
            $lines[] = '- Source value: `'.($item['source_value'] ?? '—').'`';
            $lines[] = '- Target key: `'.($item['target_translation_key'] ?? '—').'`';
            $lines[] = '- Usage count: '.(int) ($item['usage_count'] ?? 0);
            $lines[] = '';

            foreach ($item['usages'] ?? [] as $usage) {
                $file = (string) ($usage['file'] ?? '');
                $line = (int) ($usage['line'] ?? 0);
                $raw = (string) ($usage['raw'] ?? '');
                $proposedRaw = (string) ($usage['proposed_raw'] ?? '');
                $canBuildReplacement = (bool) ($usage['can_build_replacement'] ?? false);
                $lineMatchesRaw = (bool) ($usage['line_matches_raw'] ?? false);
                $lineContent = (string) ($usage['line_content'] ?? '');
                $warning = trim((string) ($usage['replacement_warning'] ?? ''));
                $verificationWarning = trim((string) ($usage['verification_warning'] ?? ''));

                $lines[] = '### Usage #'.($usage['usage_id'] ?? '—');
                $lines[] = '';
                $lines[] = '- File: `'.($file !== '' ? $file : '—').'`';
                $lines[] = '- Line: '.($line > 0 ? $line : '—');
                $lines[] = '- Current key: `'.($usage['current_translation_key'] ?? '—').'`';
                $lines[] = '- Target key: `'.($usage['target_translation_key'] ?? '—').'`';
                $lines[] = '- Can replace: `'.($canBuildReplacement ? 'yes' : 'no').'`';
                $lines[] = '- Line matches raw: `'.($lineMatchesRaw ? 'yes' : 'no').'`';

                if ($lineContent !== '') {
                    $lines[] = '- Current line: `'.$lineContent.'`';
                }

                if ($warning !== '') {
                    $lines[] = '- Replacement warning: '.$warning;
                }

                if ($verificationWarning !== '') {
                    $lines[] = '- Verification warning: '.$verificationWarning;
                }

                $lines[] = '';
                $lines[] = '```diff';
                $lines[] = '--- '.($file !== '' ? $file : 'unknown');
                $lines[] = '+++ '.($file !== '' ? $file : 'unknown');
                $lines[] = '@@ Line '.($line > 0 ? $line : '?').' @@';

                if ($canBuildReplacement && $lineMatchesRaw) {
                    $lines[] = '- '.$raw;
                    $lines[] = '+ '.$proposedRaw;
                } elseif ($canBuildReplacement) {
                    $lines[] = '! Replacement can be built, but the current file line does not match the stored raw usage.';
                    $lines[] = '! '.($raw !== '' ? $raw : 'No raw usage available.');
                } else {
                    $lines[] = '! '.($raw !== '' ? $raw : 'No raw usage available.');
                }

                $lines[] = '```';
                $lines[] = '';
            }
        }

        return implode(PHP_EOL, $lines).PHP_EOL;
    }

    /**
     * Render a readable markdown preview.
     *
     * @param  Collection<int, array<string, mixed>>  $previewItems
     */
    private function renderMarkdownPreview(Collection $previewItems): string
    {
        $lines = [
            '# Translation Usage Decision Preview',
            '',
            '- Command: `translations:usage-decisions:preview`',
            '- Decisions: '.$previewItems->count(),
            '- Usages: '.$previewItems->sum('usage_count'),
            '',
        ];

        foreach ($previewItems as $item) {
            $lines[] = '## Decision #'.$item['decision_id'];
            $lines[] = '';
            $lines[] = '- Audit type: `'.($item['audit_type'] ?? '—').'`';
            $lines[] = '- Source value: `'.($item['source_value'] ?? '—').'`';
            $lines[] = '- Normalized value: `'.($item['normalized_value'] ?? '—').'`';
            $lines[] = '- Target key: `'.($item['target_translation_key'] ?? '—').'`';
            $lines[] = '- Usage count: '.(int) ($item['usage_count'] ?? 0);

            if (trim((string) ($item['review_note'] ?? '')) !== '') {
                $lines[] = '- Review note: '.trim((string) $item['review_note']);
            }

            $lines[] = '';
            $lines[] = '| Current key | Target key | File | Line | Raw | Proposed raw | Can replace | Line matches | Status |';
            $lines[] = '|---|---|---|---:|---|---|---|---|---|';

            foreach ($item['usages'] ?? [] as $usage) {
                $lines[] = sprintf(
                    '| `%s` | `%s` | `%s` | %s | `%s` | `%s` | `%s` | `%s` | `%s` |',
                    (string) ($usage['current_translation_key'] ?? '—'),
                    (string) ($usage['target_translation_key'] ?? '—'),
                    (string) ($usage['file'] ?? '—'),
                    (string) ($usage['line'] ?? '—'),
                    (string) ($usage['raw'] ?? '—'),
                    (string) ($usage['proposed_raw'] ?? '—'),
                    (bool) ($usage['can_build_replacement'] ?? false) ? 'yes' : 'no',
                    (bool) ($usage['line_matches_raw'] ?? false) ? 'yes' : 'no',
                    (string) ($usage['change_status'] ?? '—'),
                );

                if (trim((string) ($usage['replacement_warning'] ?? '')) !== '') {
                    $lines[] = '';
                    $lines[] = '> Replacement warning for usage #'.($usage['usage_id'] ?? '—').': '.trim((string) $usage['replacement_warning']);
                    $lines[] = '';
                }

                if (trim((string) ($usage['verification_warning'] ?? '')) !== '') {
                    $lines[] = '';
                    $lines[] = '> Verification warning for usage #'.($usage['usage_id'] ?? '—').': '.trim((string) $usage['verification_warning']);
                    $lines[] = '';
                }
            }

            $lines[] = '';
        }

        return implode(PHP_EOL, $lines).PHP_EOL;
    }

    /**
     * @param  array<string, mixed>  $meta
     */
    private function logRunCompletedActivity(array $meta): void
    {
        try {
            activity('translations')
                ->event('translations.usage_decisions.preview.completed')
                ->withProperties(ConsoleActivityContext::merge($this, [
                    'summary' => $meta,
                ]))
                ->log('Translation usage decision preview command completed');
        } catch (Throwable $exception) {
            $this->warn('Activity log write failed: '.$exception->getMessage());
        }
    }
}
