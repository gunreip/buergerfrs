<?php

// app/Console/Commands/TranslationsApplyLangBallastDecisions.php

namespace App\Console\Commands;

use App\Models\TranslationLangBallastDecision;
use App\Support\ActivityLog\ConsoleActivityContext;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Throwable;

#[Signature('translations:lang-ballast:apply {--write : Actually modify lang files. Without --write only preview files are generated.}')]
#[Description('Preview or apply approved lang ballast decisions to lang/* files.')]
class TranslationsApplyLangBallastDecisions extends Command
{
    private const OUTPUT_DIR = 'audits/translations/lang-ballast';

    private const PREVIEW_LIMIT = 20;

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Apply lang ballast decisions');
        $this->line('Source: storage/audits/translations/lang-ballast/full.json');

        $writeRequested = (bool) $this->option('write');

        if ($writeRequested) {
            $this->warn('Write mode enabled for approved remove_from_lang candidates only. add_to_lang stays preview-only.');
        }

        $payload = $this->readJsonFile(storage_path(self::OUTPUT_DIR.'/full.json'));

        if ($payload === []) {
            $this->error('Missing or invalid lang ballast full.json. Run php artisan translations:audit-lang-ballast first.');

            $this->logRunFailedActivity('missing_or_invalid_lang_ballast_full_json', [
                'source' => 'storage/audits/translations/lang-ballast/full.json',
                'write_requested' => $writeRequested,
            ]);

            return self::FAILURE;
        }

        $removeCandidates = collect((array) data_get($payload, 'actions.remove_from_lang', []));
        $addCandidates = collect((array) data_get($payload, 'actions.add_to_lang', []));

        $approvedRemoveCandidates = $this->approvedActionEntries(
            entries: $removeCandidates,
            expectedActionCandidate: TranslationLangBallastDecision::ACTION_REMOVE_FROM_LANG,
        );

        $approvedAddCandidates = $this->approvedActionEntries(
            entries: $addCandidates,
            expectedActionCandidate: TranslationLangBallastDecision::ACTION_ADD_TO_LANG,
        );

        $removeSafety = $this->evaluateRemoveCandidates($approvedRemoveCandidates);
        $addSafety = $this->evaluateAddCandidates($approvedAddCandidates);

        $writeResult = [
            'written' => collect(),
            'failed' => collect(),
        ];

        if ($writeRequested) {
            $writeResult = $this->writeRemoveCandidates($removeSafety['ready']);
        }

        $summary = [
            'generated_at' => now()->toISOString(),
            'mode' => $writeRequested ? 'write' : 'preview',
            'write_requested' => $writeRequested,
            'source' => 'storage/audits/translations/lang-ballast/full.json',
            'remove_candidate_entries' => $removeCandidates->count(),
            'add_candidate_entries' => $addCandidates->count(),
            'approved_remove_candidate_entries' => $approvedRemoveCandidates->count(),
            'approved_add_candidate_entries' => $approvedAddCandidates->count(),
            'approved_total_entries' => $approvedRemoveCandidates->count() + $approvedAddCandidates->count(),
            'write_ready_remove_entries' => $removeSafety['ready']->count(),
            'write_ready_add_entries' => $addSafety['ready']->count(),
            'write_ready_total_entries' => $removeSafety['ready']->count() + $addSafety['ready']->count(),
            'write_blocked_remove_entries' => $removeSafety['blocked']->count(),
            'write_blocked_add_entries' => $addSafety['blocked']->count(),
            'write_blocked_total_entries' => $removeSafety['blocked']->count() + $addSafety['blocked']->count(),
            'written_remove_entries' => $writeResult['written']->count(),
            'written_add_entries' => 0,
            'written_total_entries' => $writeResult['written']->count(),
            'write_failed_remove_entries' => $writeResult['failed']->count(),
            'write_failed_add_entries' => 0,
            'write_failed_total_entries' => $writeResult['failed']->count(),
            'skipped_not_approved_entries' => $removeCandidates->count()
                + $addCandidates->count()
                - $approvedRemoveCandidates->count()
                - $approvedAddCandidates->count(),
            'preview_limit' => self::PREVIEW_LIMIT,
            'safety' => [
                'requires_decision_status_approved' => true,
                'requires_candidate_hash_match' => true,
                'requires_value_hash_match_before_write' => true,
                'database_delete_allowed' => false,
                'write_enabled' => $writeRequested,
                'write_remove_from_lang_enabled' => $writeRequested,
                'write_add_to_lang_enabled' => false,
            ],
        ];

        $applyPayload = [
            'generated_at' => $summary['generated_at'],
            'mode' => $summary['mode'],
            'summary' => $summary,
            'actions' => [
                'remove_from_lang' => $removeSafety['ready']->values()->all(),
                'add_to_lang' => $addSafety['ready']->values()->all(),
            ],
            'blocked' => [
                'remove_from_lang' => $removeSafety['blocked']->values()->all(),
                'add_to_lang' => $addSafety['blocked']->values()->all(),
            ],
            'written' => [
                'remove_from_lang' => $writeResult['written']->values()->all(),
                'add_to_lang' => [],
            ],
            'failed' => [
                'remove_from_lang' => $writeResult['failed']->values()->all(),
                'add_to_lang' => [],
            ],
        ];

        $this->writeApplyFiles($applyPayload);

        $this->info('Lang ballast apply preview finished');
        $this->line('Approved remove candidates: '.$approvedRemoveCandidates->count());
        $this->line('Approved add candidates: '.$approvedAddCandidates->count());
        $this->line('Write-ready remove candidates: '.$summary['write_ready_remove_entries']);
        $this->line('Write-ready add candidates: '.$summary['write_ready_add_entries']);
        $this->line('Write-blocked remove candidates: '.$summary['write_blocked_remove_entries']);
        $this->line('Write-blocked add candidates: '.$summary['write_blocked_add_entries']);
        $this->line('Written remove candidates: '.$summary['written_remove_entries']);
        $this->line('Write-failed remove candidates: '.$summary['write_failed_remove_entries']);
        $this->line('Skipped not approved candidates: '.$summary['skipped_not_approved_entries']);

        $this->logRunCompletedActivity($summary);

        return self::SUCCESS;
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $entries
     * @return Collection<int, array<string, mixed>>
     */
    private function approvedActionEntries(Collection $entries, string $expectedActionCandidate): Collection
    {
        $candidateHashes = $entries
            ->pluck('candidate_hash')
            ->map(static fn (mixed $value): string => trim((string) $value))
            ->filter()
            ->unique()
            ->values();

        if ($candidateHashes->isEmpty()) {
            return collect();
        }

        $approvedDecisionIndex = TranslationLangBallastDecision::query()
            ->whereIn('candidate_hash', $candidateHashes->all())
            ->where('decision_status', TranslationLangBallastDecision::STATUS_APPROVED)
            ->get()
            ->keyBy('candidate_hash');

        return $entries
            ->filter(function (array $entry) use ($approvedDecisionIndex, $expectedActionCandidate): bool {
                $candidateHash = trim((string) ($entry['candidate_hash'] ?? ''));

                if ($candidateHash === '' || ! $approvedDecisionIndex->has($candidateHash)) {
                    return false;
                }

                $actionCandidate = trim((string) ($entry['action_candidate'] ?? $entry['action'] ?? ''));

                return $actionCandidate === $expectedActionCandidate;
            })
            ->map(function (array $entry) use ($approvedDecisionIndex): array {
                $candidateHash = trim((string) ($entry['candidate_hash'] ?? ''));
                $decision = $approvedDecisionIndex->get($candidateHash);

                return [
                    ...$entry,
                    'decision_exists' => true,
                    'decision_id' => $decision?->id,
                    'decision_status' => $decision?->decision_status,
                    'decision_note' => $decision?->decision_note,
                    'decision_reviewed_at' => $decision?->reviewed_at?->toISOString(),
                    'decision_reviewed_by_user_id' => $decision?->reviewed_by_user_id,
                ];
            })
            ->values();
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $entries
     * @return array{ready: Collection<int, array<string, mixed>>, blocked: Collection<int, array<string, mixed>>}
     */
    private function evaluateRemoveCandidates(Collection $entries): array
    {
        $ready = collect();
        $blocked = collect();

        foreach ($entries as $entry) {
            $fileKey = trim((string) ($entry['file_key'] ?? ''));
            $expectedValueHash = trim((string) ($entry['value_hash'] ?? ''));
            $path = $this->langFilePath($entry);

            if ($path === null) {
                $blocked->push($this->blockedEntry($entry, 'invalid_lang_file_path'));

                continue;
            }

            if ($fileKey === '') {
                $blocked->push($this->blockedEntry($entry, 'missing_file_key'));

                continue;
            }

            if ($expectedValueHash === '') {
                $blocked->push($this->blockedEntry($entry, 'missing_value_hash'));

                continue;
            }

            $payload = $this->readLangFilePayload($path);

            if ($payload === null) {
                $blocked->push($this->blockedEntry($entry, 'lang_file_missing_or_invalid'));

                continue;
            }

            if (! $this->langFileKeyExists($payload, $fileKey, $path)) {
                $blocked->push($this->blockedEntry($entry, 'file_key_missing_in_current_lang_file'));

                continue;
            }

            $currentValue = $this->langFileValue($payload, $fileKey, $path);

            if (! is_scalar($currentValue)) {
                $blocked->push($this->blockedEntry($entry, 'current_value_not_scalar'));

                continue;
            }

            $currentValue = (string) $currentValue;
            $currentValueHash = hash('sha256', $currentValue);

            if ($currentValueHash !== $expectedValueHash) {
                $blocked->push($this->blockedEntry($entry, 'current_value_hash_mismatch', [
                    'current_value_hash' => $currentValueHash,
                    'expected_value_hash' => $expectedValueHash,
                ]));

                continue;
            }

            $ready->push($this->readyEntry($entry, [
                'current_value' => $currentValue,
                'current_value_hash' => $currentValueHash,
            ]));
        }

        return [
            'ready' => $ready->values(),
            'blocked' => $blocked->values(),
        ];
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $entries
     * @return array{ready: Collection<int, array<string, mixed>>, blocked: Collection<int, array<string, mixed>>}
     */
    private function evaluateAddCandidates(Collection $entries): array
    {
        $ready = collect();
        $blocked = collect();

        foreach ($entries as $entry) {
            $fileKey = trim((string) ($entry['file_key'] ?? ''));
            $expectedValueHash = trim((string) ($entry['value_hash'] ?? ''));
            $path = $this->langFilePath($entry);
            $value = is_scalar($entry['value'] ?? null) ? (string) $entry['value'] : null;

            if ($path === null) {
                $blocked->push($this->blockedEntry($entry, 'invalid_lang_file_path'));

                continue;
            }

            if ($fileKey === '') {
                $blocked->push($this->blockedEntry($entry, 'missing_file_key'));

                continue;
            }

            if ($value === null || $value === '') {
                $blocked->push($this->blockedEntry($entry, 'missing_candidate_value'));

                continue;
            }

            if ($expectedValueHash === '') {
                $blocked->push($this->blockedEntry($entry, 'missing_value_hash'));

                continue;
            }

            $candidateValueHash = hash('sha256', $value);

            if ($candidateValueHash !== $expectedValueHash) {
                $blocked->push($this->blockedEntry($entry, 'candidate_value_hash_mismatch', [
                    'candidate_value_hash' => $candidateValueHash,
                    'expected_value_hash' => $expectedValueHash,
                ]));

                continue;
            }

            $payload = $this->readLangFilePayload($path);

            if ($payload === null) {
                $blocked->push($this->blockedEntry($entry, 'lang_file_missing_or_invalid'));

                continue;
            }

            if ($this->langFileKeyExists($payload, $fileKey, $path)) {
                $blocked->push($this->blockedEntry($entry, 'file_key_already_exists_in_current_lang_file'));

                continue;
            }

            $ready->push($this->readyEntry($entry, [
                'new_value' => $value,
                'new_value_hash' => $candidateValueHash,
            ]));
        }

        return [
            'ready' => $ready->values(),
            'blocked' => $blocked->values(),
        ];
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $entries
     * @return array{written: Collection<int, array<string, mixed>>, failed: Collection<int, array<string, mixed>>}
     */
    private function writeRemoveCandidates(Collection $entries): array
    {
        $written = collect();
        $failed = collect();

        foreach ($entries->groupBy(fn (array $entry): string => trim((string) ($entry['file'] ?? ''))) as $file => $fileEntries) {
            $firstEntry = $fileEntries->first();
            $path = is_array($firstEntry) ? $this->langFilePath($firstEntry) : null;

            if ($path === null) {
                $failed = $failed->merge(
                    $fileEntries->map(fn (array $entry): array => $this->writeFailedEntry($entry, 'invalid_lang_file_path')),
                );

                continue;
            }

            $payload = $this->readLangFilePayload($path);

            if ($payload === null) {
                $failed = $failed->merge(
                    $fileEntries->map(fn (array $entry): array => $this->writeFailedEntry($entry, 'lang_file_missing_or_invalid')),
                );

                continue;
            }

            $fileWritten = collect();
            $fileChanged = false;

            foreach ($fileEntries as $entry) {
                $fileKey = trim((string) ($entry['file_key'] ?? ''));

                if ($fileKey === '') {
                    $failed->push($this->writeFailedEntry($entry, 'missing_file_key'));

                    continue;
                }

                if (! $this->langFileKeyExists($payload, $fileKey, $path)) {
                    $failed->push($this->writeFailedEntry($entry, 'file_key_missing_before_write'));

                    continue;
                }

                if (! $this->forgetLangFileKey($payload, $fileKey, $path)) {
                    $failed->push($this->writeFailedEntry($entry, 'file_key_could_not_be_removed'));

                    continue;
                }

                $fileChanged = true;
                $fileWritten->push($this->writtenEntry($entry));
            }

            if (! $fileChanged) {
                continue;
            }

            try {
                $this->writeLangFilePayload($path, $payload);
            } catch (Throwable $exception) {
                $failed = $failed->merge(
                    $fileWritten->map(fn (array $entry): array => $this->writeFailedEntry($entry, 'lang_file_write_failed', [
                        'write_error' => $exception->getMessage(),
                    ])),
                );

                continue;
            }

            $written = $written->merge($fileWritten);
        }

        return [
            'written' => $written->values(),
            'failed' => $failed->values(),
        ];
    }

    /**
     * @param  array<string, mixed>  $entry
     * @param  array<string, mixed>  $extra
     * @return array<string, mixed>
     */
    private function writtenEntry(array $entry, array $extra = []): array
    {
        return [
            ...$entry,
            ...$extra,
            'write_success' => true,
            'write_error_reason' => null,
        ];
    }

    /**
     * @param  array<string, mixed>  $entry
     * @param  array<string, mixed>  $extra
     * @return array<string, mixed>
     */
    private function writeFailedEntry(array $entry, string $reason, array $extra = []): array
    {
        return [
            ...$entry,
            ...$extra,
            'write_success' => false,
            'write_error_reason' => $reason,
        ];
    }

    /**
     * @param  array<string, mixed>  $entry
     * @param  array<string, mixed>  $extra
     * @return array<string, mixed>
     */
    private function readyEntry(array $entry, array $extra = []): array
    {
        return [
            ...$entry,
            ...$extra,
            'apply_ready' => true,
            'apply_block_reason' => null,
        ];
    }

    /**
     * @param  array<string, mixed>  $entry
     * @param  array<string, mixed>  $extra
     * @return array<string, mixed>
     */
    private function blockedEntry(array $entry, string $reason, array $extra = []): array
    {
        return [
            ...$entry,
            ...$extra,
            'apply_ready' => false,
            'apply_block_reason' => $reason,
        ];
    }

    /**
     * @param  array<string, mixed>  $entry
     */
    private function langFilePath(array $entry): ?string
    {
        $file = trim((string) ($entry['file'] ?? ''));

        if ($file === '' || str_contains($file, '..') || ! str_starts_with($file, 'lang/')) {
            return null;
        }

        return base_path($file);
    }

    /**
     * @return array<string, mixed>|null
     */
    private function readLangFilePayload(string $path): ?array
    {
        if (! File::exists($path)) {
            return null;
        }

        if (pathinfo($path, PATHINFO_EXTENSION) === 'php') {
            $payload = require $path;

            return is_array($payload) ? $payload : null;
        }

        if (pathinfo($path, PATHINFO_EXTENSION) === 'json') {
            $payload = json_decode((string) File::get($path), true);

            return is_array($payload) ? $payload : null;
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function langFileKeyExists(array $payload, string $fileKey, string $path): bool
    {
        if (pathinfo($path, PATHINFO_EXTENSION) === 'json') {
            return array_key_exists($fileKey, $payload);
        }

        $current = $payload;

        foreach (explode('.', $fileKey) as $segment) {
            if (! is_array($current) || ! array_key_exists($segment, $current)) {
                return false;
            }

            $current = $current[$segment];
        }

        return true;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function langFileValue(array $payload, string $fileKey, string $path): mixed
    {
        if (pathinfo($path, PATHINFO_EXTENSION) === 'json') {
            return $payload[$fileKey] ?? null;
        }

        $current = $payload;

        foreach (explode('.', $fileKey) as $segment) {
            if (! is_array($current) || ! array_key_exists($segment, $current)) {
                return null;
            }

            $current = $current[$segment];
        }

        return $current;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function forgetLangFileKey(array &$payload, string $fileKey, string $path): bool
    {
        if (pathinfo($path, PATHINFO_EXTENSION) === 'json') {
            if (! array_key_exists($fileKey, $payload)) {
                return false;
            }

            unset($payload[$fileKey]);

            return true;
        }

        return $this->forgetNestedLangFileKey($payload, explode('.', $fileKey));
    }

    /**
     * @param  array<string, mixed>  $payload
     * @param  array<int, string>  $segments
     */
    private function forgetNestedLangFileKey(array &$payload, array $segments): bool
    {
        $segment = array_shift($segments);

        if ($segment === null || ! array_key_exists($segment, $payload)) {
            return false;
        }

        if ($segments === []) {
            unset($payload[$segment]);

            return true;
        }

        if (! is_array($payload[$segment])) {
            return false;
        }

        $removed = $this->forgetNestedLangFileKey($payload[$segment], $segments);

        if ($removed && $payload[$segment] === []) {
            unset($payload[$segment]);
        }

        return $removed;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function writeLangFilePayload(string $path, array $payload): void
    {
        if (pathinfo($path, PATHINFO_EXTENSION) === 'json') {
            File::put(
                $path,
                json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES).PHP_EOL,
            );

            return;
        }

        File::put($path, $this->exportPhpLangPayload($payload));
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function exportPhpLangPayload(array $payload): string
    {
        return "<?php\n\nreturn ".$this->exportPhpArray($payload).";\n";
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function exportPhpArray(array $payload, int $level = 0): string
    {
        if ($payload === []) {
            return '[]';
        }

        $indent = str_repeat('    ', $level);
        $childIndent = str_repeat('    ', $level + 1);
        $lines = ['['];

        foreach ($payload as $key => $value) {
            $exportedKey = is_int($key)
                ? (string) $key
                : var_export((string) $key, true);

            $exportedValue = is_array($value)
                ? $this->exportPhpArray($value, $level + 1)
                : var_export($value, true);

            $lines[] = $childIndent.$exportedKey.' => '.$exportedValue.',';
        }

        $lines[] = $indent.']';

        return implode(PHP_EOL, $lines);
    }

    /**
     * @return array<string, mixed>
     */
    private function readJsonFile(string $path): array
    {
        if (! File::exists($path)) {
            return [];
        }

        $payload = json_decode((string) File::get($path), true);

        return is_array($payload) ? $payload : [];
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function writeApplyFiles(array $payload): void
    {
        $directory = storage_path(self::OUTPUT_DIR);

        File::ensureDirectoryExists($directory);

        $summary = is_array($payload['summary'] ?? null)
            ? $payload['summary']
            : [];

        $actions = is_array($payload['actions'] ?? null)
            ? $payload['actions']
            : [];

        $blocked = is_array($payload['blocked'] ?? null)
            ? $payload['blocked']
            : [];

        $written = is_array($payload['written'] ?? null)
            ? $payload['written']
            : [];

        $failed = is_array($payload['failed'] ?? null)
            ? $payload['failed']
            : [];

        File::put(
            $directory.'/apply-summary.json',
            json_encode($summary, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES).PHP_EOL,
        );

        File::put(
            $directory.'/apply-full.json',
            json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES).PHP_EOL,
        );

        File::put(
            $directory.'/apply-preview.json',
            json_encode([
                ...$payload,
                'actions' => [
                    'remove_from_lang' => collect($actions['remove_from_lang'] ?? [])
                        ->take(self::PREVIEW_LIMIT)
                        ->values()
                        ->all(),
                    'add_to_lang' => collect($actions['add_to_lang'] ?? [])
                        ->take(self::PREVIEW_LIMIT)
                        ->values()
                        ->all(),
                ],
                'blocked' => [
                    'remove_from_lang' => collect($blocked['remove_from_lang'] ?? [])
                        ->take(self::PREVIEW_LIMIT)
                        ->values()
                        ->all(),
                    'add_to_lang' => collect($blocked['add_to_lang'] ?? [])
                        ->take(self::PREVIEW_LIMIT)
                        ->values()
                        ->all(),
                ],
                'written' => [
                    'remove_from_lang' => collect($written['remove_from_lang'] ?? [])
                        ->take(self::PREVIEW_LIMIT)
                        ->values()
                        ->all(),
                    'add_to_lang' => collect($written['add_to_lang'] ?? [])
                        ->take(self::PREVIEW_LIMIT)
                        ->values()
                        ->all(),
                ],
                'failed' => [
                    'remove_from_lang' => collect($failed['remove_from_lang'] ?? [])
                        ->take(self::PREVIEW_LIMIT)
                        ->values()
                        ->all(),
                    'add_to_lang' => collect($failed['add_to_lang'] ?? [])
                        ->take(self::PREVIEW_LIMIT)
                        ->values()
                        ->all(),
                ],
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES).PHP_EOL,
        );
    }

    /**
     * @param  array<string, mixed>  $summary
     */
    private function logRunCompletedActivity(array $summary): void
    {
        try {
            activity('translations')
                ->event('translations.lang_ballast.apply.completed')
                ->withProperties(ConsoleActivityContext::merge($this, [
                    'summary' => $summary,
                ]))
                ->log('Translation lang ballast apply command completed');
        } catch (Throwable $exception) {
            $this->warn('Activity log write failed: '.$exception->getMessage());
        }
    }

    /**
     * @param  array<string, mixed>  $properties
     */
    private function logRunFailedActivity(string $reason, array $properties = []): void
    {
        try {
            activity('translations')
                ->event('translations.lang_ballast.apply.failed')
                ->withProperties(ConsoleActivityContext::merge($this, [
                    'reason' => $reason,
                    ...$properties,
                ]))
                ->log('Translation lang ballast apply command failed');
        } catch (Throwable $exception) {
            $this->warn('Activity log write failed: '.$exception->getMessage());
        }
    }
}
