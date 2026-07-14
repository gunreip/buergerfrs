<?php

// app/Console/Commands/SyncDynamicTranslationKeys.php

// php artisan translations:sync-dynamic-keys
// php artisan translations:sync-dynamic-keys --dry-run
// php artisan translations:sync-dynamic-keys --paths=resources/views --dry-run

namespace App\Console\Commands;

use App\Models\TranslationKey;
use App\Models\TranslationUsage;
use App\Support\ActivityLog\ConsoleActivityContext;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use SplFileInfo;
use SplFileObject;
use Symfony\Component\Finder\Finder;
use Throwable;

#[Signature('translations:sync-dynamic-keys
    {--paths= : Comma-separated relative paths to scan (default: app,routes,resources/views)}
    {--output-dir= : Relative output directory for generated audit files}
    {--dry-run : Write audit files only; do not sync database rows}')]
#[Description('Scan dynamic translation candidates and dynamic_label usages, then sync them into translation review tables.')]
class SyncDynamicTranslationKeys extends Command
{
    private const PREVIEW_LIMIT = 30;

    /**
     * Execute the command.
     */
    public function handle(): int
    {
        $paths = $this->scanPaths();
        $outputDirectory = $this->outputDirectory();
        $dryRun = (bool) $this->option('dry-run');

        $items = collect();
        $filesScanned = 0;

        foreach ($this->scannableFiles($paths) as $file) {
            $filesScanned++;
            $items = $items->merge($this->scanFile($file->getPathname()));
        }

        $items = $items
            ->sortBy(fn(array $item): array => [$item['file'], $item['line'], $item['kind'], $item['raw']])
            ->values();

        $syncSummary = $dryRun
            ? ['keys_created' => 0, 'keys_updated' => 0, 'usages_created' => 0, 'usages_updated' => 0]
            : $this->syncItems($items);

        $summary = [
            'generated_at' => now()->toIso8601String(),
            'command' => 'translations:sync-dynamic-keys',
            'dry_run' => $dryRun,
            'root_path' => base_path(),
            'paths' => $paths,
            'files_scanned' => $filesScanned,
            'items' => $items->count(),
            'dynamic_label_usages' => $items->where('kind', 'dynamic_label')->count(),
            'dynamic_candidates' => $items->where('kind', 'dynamic_candidate')->count(),
            ...$syncSummary,
        ];

        $this->writeAuditFiles($outputDirectory, $summary, $items);

        $this->components->info('Dynamic translation key sync finished.');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Files scanned', $summary['files_scanned']],
                ['Items found', $summary['items']],
                ['dynamic_label usages', $summary['dynamic_label_usages']],
                ['Dynamic candidates', $summary['dynamic_candidates']],
                ['Keys created', $summary['keys_created']],
                ['Keys updated', $summary['keys_updated']],
                ['Usages created', $summary['usages_created']],
                ['Usages updated', $summary['usages_updated']],
            ],
        );

        $this->line('Audit directory: ' . $this->relativePath($outputDirectory));

        if ($dryRun) {
            $this->warn('Dry run only: no database rows were written.');
        }

        $this->logRunCompletedActivity($summary);

        return self::SUCCESS;
    }

    /**
     * @return array<int, string>
     */
    private function scanPaths(): array
    {
        $pathsOption = trim((string) $this->option('paths'));

        if ($pathsOption === '') {
            return ['app', 'routes', 'resources/views'];
        }

        return collect(explode(',', $pathsOption))
            ->map(static fn(string $path): string => trim($path))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function outputDirectory(): string
    {
        $relative = trim((string) $this->option('output-dir'));

        if ($relative === '') {
            $relative = 'storage/audits/translations/dynamic';
        }

        return str_starts_with($relative, DIRECTORY_SEPARATOR)
            ? $relative
            : base_path($relative);
    }

    /**
     * @param  array<int, string>  $paths
     * @return iterable<int, SplFileInfo>
     */
    private function scannableFiles(array $paths): iterable
    {
        $directories = collect($paths)
            ->map(static fn(string $path): string => base_path($path))
            ->filter(static fn(string $path): bool => File::isDirectory($path))
            ->values()
            ->all();

        if ($directories === []) {
            return [];
        }

        return Finder::create()
            ->files()
            ->in($directories)
            ->name('*.php')
            ->name('*.blade.php')
            ->ignoreDotFiles(true)
            ->ignoreVCS(true);
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function scanFile(string $path): Collection
    {
        if ($this->isParkedFile($path)) {
            return collect();
        }

        $contents = File::get($path);
        $relativePath = $this->relativePath($path);

        return collect()
            ->merge($this->extractDynamicLabelUsages($contents, $path, $relativePath))
            ->merge($this->extractDynamicCandidates($contents, $path, $relativePath));
    }

    private function isParkedFile(string $path): bool
    {
        $filename = strtolower(pathinfo($path, PATHINFO_FILENAME));

        return str_contains($filename, 'xxx')
            || str_contains($filename, 'yyy')
            || str_contains($filename, 'zzz');
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function extractDynamicLabelUsages(string $contents, string $path, string $relativePath): array
    {
        preg_match_all('/(?<![\'"A-Za-z0-9_])dynamic_label\s*\(/u', $contents, $matches, PREG_OFFSET_CAPTURE);

        $items = [];

        foreach ($matches[0] ?? [] as $match) {
            $offset = (int) $match[1];
            $raw = $this->extractCallSnippet($contents, $offset);
            $scope = $this->extractDynamicLabelScope($raw);
            $line = $this->lineNumberForOffset($path, $offset);

            $items[] = [
                'kind' => 'dynamic_label',
                'function' => 'dynamic_label',
                'scope' => $scope,
                'suggested_key' => $scope !== null ? 'dynamic.' . $scope . '.*' : null,
                'value' => $raw,
                'raw' => $raw,
                'file' => $relativePath,
                'line' => $line,
                'reason' => $scope === null ? 'dynamic_label_scope_not_literal' : 'dynamic_label_usage',
            ];
        }

        return $items;
    }

    private function extractDynamicLabelScope(string $raw): ?string
    {
        if (preg_match('/dynamic_label\s*\(\s*([\'"])(?P<scope>[a-zA-Z0-9_.-]+)\1/u', $raw, $match) !== 1) {
            return null;
        }

        $scope = trim((string) ($match['scope'] ?? ''));

        return $scope !== '' ? $scope : null;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function extractDynamicCandidates(string $contents, string $path, string $relativePath): array
    {
        $patterns = [
            '/(?P<function>__|trans)\(\s*(?P<argument>[^\'"\s)][^)]*)\)/su',
            '/(?P<function>@lang)\(\s*(?P<argument>[^\'"\s)][^)]*)\)/su',
            '/(?P<function>Lang::get)\(\s*(?P<argument>[^\'"\s)][^)]*)\)/su',
        ];

        $items = [];

        foreach ($patterns as $pattern) {
            preg_match_all($pattern, $contents, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);

            foreach ($matches as $match) {
                $offset = (int) $match[0][1];

                if ($this->isInsideBladeJsCall($contents, $offset)) {
                    continue;
                }

                $raw = $this->extractCallSnippet($contents, $offset);

                if (str_starts_with($raw, 'dynamic_label(')) {
                    continue;
                }

                $argument = trim((string) $match['argument'][0]);

                $items[] = [
                    'kind' => 'dynamic_candidate',
                    'function' => (string) $match['function'][0],
                    'scope' => null,
                    'suggested_key' => $this->suggestDynamicKey($argument, $relativePath, $contents, $offset),
                    'value' => $argument,
                    'raw' => $raw,
                    'file' => $relativePath,
                    'line' => $this->lineNumberForOffset($path, $offset),
                    'reason' => 'non_literal_first_argument',
                ];
            }
        }

        return $items;
    }

    private function isInsideBladeJsCall(string $contents, int $offset): bool
    {
        $prefix = substr($contents, 0, $offset);
        $jsOffset = strrpos($prefix, '@js(');

        if ($jsOffset === false) {
            return false;
        }

        $between = substr($contents, $jsOffset, $offset - $jsOffset);

        return substr_count($between, '(') > substr_count($between, ')');
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $items
     * @return array{keys_created:int, keys_updated:int, usages_created:int, usages_updated:int}
     */
    private function syncItems(Collection $items): array
    {
        $summary = [
            'keys_created' => 0,
            'keys_updated' => 0,
            'usages_created' => 0,
            'usages_updated' => 0,
        ];

        $now = now();

        foreach ($items as $item) {
            $keyFingerprint = $this->fingerprint(implode('|', [
                'dynamic',
                $item['file'] ?? '',
                $item['line'] ?? '',
                $item['function'] ?? '',
                $item['raw'] ?? '',
            ]));

            $translationKey = TranslationKey::query()->firstOrNew([
                'fingerprint' => $keyFingerprint,
            ]);

            $wasExistingKey = $translationKey->exists;

            if (! $translationKey->exists) {
                $translationKey->first_seen_at = $now;
            }

            $existingKey = trim((string) ($translationKey->key ?? ''));
            $resolvedKey = $existingKey !== '' ? $existingKey : null;
            $resolvedStatus = $resolvedKey !== null
                ? (string) ($translationKey->status ?? 'missing')
                : 'dynamic';
            $resolvedClassification = $resolvedKey !== null
                ? (string) ($translationKey->classification ?? 'key')
                : 'dynamic';
            $isDynamicMulti = (bool) ($translationKey->is_dynamic_multi ?? false);

            if ($isDynamicMulti) {
                $resolvedStatus = 'dynamic';
                $resolvedClassification = 'dynamic';
            }

            $translationKey->fill([
                'key' => $resolvedKey,
                'namespace' => $resolvedKey !== null
                    ? $this->namespaceFromKey($resolvedKey)
                    : ($item['kind'] === 'dynamic_label' ? 'dynamic' : $this->namespaceFromPath((string) $item['file'])),
                'group' => $resolvedKey !== null
                    ? $this->groupFromKey($resolvedKey)
                    : ($item['kind'] === 'dynamic_label' ? 'dynamic' : null),
                'status' => $resolvedStatus,
                'workflow_status' => $translationKey->workflow_status ?? 'open',
                'classification' => $resolvedClassification,
                'source' => $isDynamicMulti ? 'dynamic_audit' : ($translationKey->source ?: 'dynamic_audit'),
                'suggested_key' => $this->preferSuggestedKey($translationKey->suggested_key, $item['suggested_key'] ?? null),
                'native_text' => $item['value'] ?? null,
                'last_seen_at' => $now,
                'obsolete_at' => null,
            ]);

            $translationKey->save();

            $summary[$wasExistingKey ? 'keys_updated' : 'keys_created']++;

            if (! $wasExistingKey) {
                $this->createDiscoveredAuditEvent($translationKey, $item);
            }

            $usageFingerprint = $this->fingerprint(implode('|', [
                'dynamic-usage',
                $translationKey->fingerprint,
                $item['file'] ?? '',
                $item['line'] ?? '',
                $item['function'] ?? '',
                $item['raw'] ?? '',
            ]));

            $translationUsage = TranslationUsage::query()->firstOrNew([
                'fingerprint' => $usageFingerprint,
            ]);

            $wasExistingUsage = $translationUsage->exists;

            $translationUsage->fill([
                'translation_key_id' => $translationKey->id,
                'file' => (string) ($item['file'] ?? ''),
                'line' => (int) ($item['line'] ?? 0),
                'function' => (string) ($item['function'] ?? ''),
                'classification' => 'dynamic',
                'reason' => (string) ($item['reason'] ?? ''),
                'raw' => (string) ($item['raw'] ?? ''),
                'original_raw' => $translationUsage->original_raw ?: (string) ($item['raw'] ?? ''),
            ]);

            $translationUsage->save();

            $summary[$wasExistingUsage ? 'usages_updated' : 'usages_created']++;
        }

        return $summary;
    }

    /**
     * @param  array<string, mixed>  $summary
     * @param  Collection<int, array<string, mixed>>  $items
     */
    private function writeAuditFiles(string $outputDirectory, array $summary, Collection $items): void
    {
        File::ensureDirectoryExists($outputDirectory);

        File::put(
            $outputDirectory . '/summary.json',
            json_encode($summary, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL,
        );

        File::put(
            $outputDirectory . '/items.json',
            json_encode($items->values()->all(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL,
        );

        File::put(
            $outputDirectory . '/items.preview.json',
            json_encode($items->take(self::PREVIEW_LIMIT)->values()->all(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL,
        );

        File::put($outputDirectory . '/items.md', $this->renderMarkdownItems($summary, $items));
        File::put($outputDirectory . '/items.preview.md', $this->renderMarkdownItems($summary, $items->take(self::PREVIEW_LIMIT)));
    }

    /**
     * @param  array<string, mixed>  $summary
     * @param  Collection<int, array<string, mixed>>  $items
     */
    private function renderMarkdownItems(array $summary, Collection $items): string
    {
        $lines = [
            '# Dynamic Translation Audit',
            '',
            '- Generated at: ' . ($summary['generated_at'] ?? ''),
            '- Dry run: ' . (($summary['dry_run'] ?? false) ? 'yes' : 'no'),
            '- Files scanned: ' . ($summary['files_scanned'] ?? 0),
            '- Items: ' . ($summary['items'] ?? 0),
            '',
            '| Kind | Scope | Function | File | Line | Suggested key | Raw |',
            '| --- | --- | --- | --- | ---: | --- | --- |',
        ];

        foreach ($items as $item) {
            $lines[] = implode(' | ', [
                $this->markdownCell((string) ($item['kind'] ?? '')),
                $this->markdownCell((string) ($item['scope'] ?? '')),
                $this->markdownCell((string) ($item['function'] ?? '')),
                $this->markdownCell((string) ($item['file'] ?? '')),
                (string) ($item['line'] ?? ''),
                $this->markdownCell((string) ($item['suggested_key'] ?? '')),
                $this->markdownCell((string) ($item['raw'] ?? '')),
            ]);
        }

        return implode(PHP_EOL, $lines) . PHP_EOL;
    }

    private function markdownCell(string $value): string
    {
        $value = str_replace(["\r", "\n"], ' ', trim($value));
        $value = str_replace('|', '\\|', $value);

        return $value !== '' ? '`' . $value . '`' : '';
    }

    private function extractCallSnippet(string $contents, int $offset): string
    {
        $length = strlen($contents);
        $position = $offset;
        $depth = 0;
        $quote = null;
        $escaped = false;

        while ($position < $length) {
            $character = $contents[$position];

            if ($quote !== null) {
                if ($escaped) {
                    $escaped = false;
                    $position++;

                    continue;
                }

                if ($character === '\\') {
                    $escaped = true;
                    $position++;

                    continue;
                }

                if ($character === $quote) {
                    $quote = null;
                }

                $position++;

                continue;
            }

            if ($character === '\'' || $character === '"') {
                $quote = $character;
                $position++;

                continue;
            }

            if ($character === '(') {
                $depth++;
                $position++;

                continue;
            }

            if ($character === ')') {
                $depth--;
                $position++;

                if ($depth <= 0) {
                    return trim(substr($contents, $offset, $position - $offset));
                }

                continue;
            }

            if ($character === "\n" && $depth <= 0) {
                return trim(substr($contents, $offset, $position - $offset));
            }

            $position++;
        }

        $snippet = substr($contents, $offset, 500);
        $lineEndPosition = strpos($snippet, "\n");

        if ($lineEndPosition !== false) {
            return trim(substr($snippet, 0, $lineEndPosition));
        }

        return trim($snippet);
    }

    private function lineNumberForOffset(string $path, int $offset): int
    {
        $file = new SplFileObject($path);
        $position = 0;
        $lineNumber = 1;

        while (! $file->eof()) {
            $line = $file->fgets();
            $position += strlen($line);

            if ($position > $offset) {
                return $lineNumber;
            }

            $lineNumber++;
        }

        return $lineNumber;
    }

    private function namespaceFromPath(string $relativePath): string
    {
        $path = str($relativePath)
            ->replace('\\', '/')
            ->replace('resources/views/components/', '')
            ->replace('resources/views/livewire/', '')
            ->replace('resources/views/', '')
            ->replace('app/Livewire/', '')
            ->replace('app/', '')
            ->replace('routes/', '')
            ->replaceMatches('#(^|/)partials/#', '$1')
            ->replace('.blade.php', '')
            ->replace('.php', '')
            ->replace('⚡', '')
            ->replace('/', '.')
            ->replace('-', '_')
            ->replaceMatches('/(?<!^)[A-Z]/', '_$0')
            ->lower()
            ->toString();

        $path = trim($path, '.');
        $path = preg_replace('/(^|\\.)_+/', '$1', $path) ?: $path;
        $path = preg_replace('/_+(\\.|$)/', '$1', $path) ?: $path;

        return $path !== '' ? $path : 'dynamic';
    }

    private function suggestDynamicKey(string $argument, string $relativePath, string $contents, int $offset): string
    {
        $namespace = $this->namespaceFromPath($relativePath);
        $context = $this->dynamicContextFromArgument($argument, $contents, $offset);

        return 'dynamic.' . $namespace . '.' . $context;
    }

    private function dynamicContextFromArgument(string $argument, string $contents, int $offset): string
    {
        $argument = trim($argument);

        if ($argument === '$label') {
            $foreachContext = $this->nearestForeachLabelSource($contents, $offset);

            if ($foreachContext !== null) {
                return $this->slugSegment($foreachContext);
            }
        }

        if (preg_match('/\\$([A-Za-z_][A-Za-z0-9_]*)Options/u', $argument, $match) === 1) {
            $base = (string) $match[1];

            return $this->slugSegment($base . '_options');
        }

        if (preg_match('/\\$([A-Za-z_][A-Za-z0-9_]*)->([A-Za-z_][A-Za-z0-9_]*)/u', $argument, $match) === 1) {
            return $this->slugSegment((string) $match[1] . '_' . (string) $match[2]);
        }

        if (preg_match('/\\$([A-Za-z_][A-Za-z0-9_]*)/u', $argument, $match) === 1) {
            return $this->slugSegment((string) $match[1]);
        }

        $withoutPhpNoise = str($argument)
            ->replaceMatches('/\\b(str|Str|headline|toString|__|trans|lang|get)\\b/u', ' ')
            ->toString();

        return $this->slugSegment($withoutPhpNoise);
    }

    private function nearestForeachLabelSource(string $contents, int $offset): ?string
    {
        $start = max(0, $offset - 2000);
        $window = substr($contents, $start, $offset - $start);

        preg_match_all(
            '/@foreach\s*\(\s*\$(?P<source>[A-Za-z_][A-Za-z0-9_]*)\s+as\s+(?:(?:\$?[A-Za-z_][A-Za-z0-9_]*|[^=]+)\s*=>\s*)?\$(?P<label>[A-Za-z_][A-Za-z0-9_]*)\s*\)/u',
            $window,
            $matches,
            PREG_SET_ORDER,
        );

        if ($matches === []) {
            return null;
        }

        $match = $matches[array_key_last($matches)];

        if (($match['label'] ?? null) !== 'label') {
            return null;
        }

        $source = trim((string) ($match['source'] ?? ''));

        return $source !== '' ? $source : null;
    }

    private function slugSegment(string $value): string
    {
        $slug = str($value)
            ->snake()
            ->lower()
            ->replaceMatches('/[^a-z0-9]+/', '_')
            ->trim('_')
            ->limit(80, '')
            ->toString();

        return $slug !== '' ? $slug : 'value';
    }

    private function preferSuggestedKey(?string $currentSuggestedKey, mixed $incomingSuggestedKey): ?string
    {
        $current = trim((string) ($currentSuggestedKey ?? ''));

        if ($current !== '') {
            return $currentSuggestedKey;
        }

        $incoming = trim((string) ($incomingSuggestedKey ?? ''));

        return $incoming !== '' ? $incoming : null;
    }

    private function namespaceFromKey(string $key): ?string
    {
        $key = trim($key);

        if ($key === '' || ! str_contains($key, '.')) {
            return null;
        }

        return explode('.', $key, 2)[0] ?: null;
    }

    private function groupFromKey(string $key): ?string
    {
        $key = trim($key);

        if ($key === '' || ! str_contains($key, '.')) {
            return null;
        }

        return explode('.', $key, 2)[0] ?: null;
    }

    private function fingerprint(string $value): string
    {
        return hash('sha256', $value);
    }

    /**
     * @param  array<string, mixed>  $item
     */
    private function createDiscoveredAuditEvent(TranslationKey $translationKey, array $item): void
    {
        DB::table('translation_audit_events')->insert([
            'translation_key_id' => $translationKey->id,
            'translation_usage_id' => null,
            'entity_type' => 'translation_key',
            'event_type' => 'discovered',
            'old_fingerprint' => null,
            'new_fingerprint' => $translationKey->fingerprint,
            'old_file' => null,
            'new_file' => $item['file'] ?? null,
            'old_line' => null,
            'new_line' => $item['line'] ?? null,
            'old_key' => null,
            'new_key' => $translationKey->key,
            'old_value' => null,
            'new_value' => $translationKey->native_text,
            'old_status' => null,
            'new_status' => $translationKey->status,
            'reason' => 'dynamic_translation_key_discovered_during_sync',
            'context' => json_encode([
                'kind' => $item['kind'] ?? null,
                'scope' => $item['scope'] ?? null,
                'function' => $item['function'] ?? null,
                'raw' => $item['raw'] ?? null,
                'suggested_key' => $translationKey->suggested_key,
                'source' => $translationKey->source,
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * @param  array<string, mixed>  $summary
     */
    private function logRunCompletedActivity(array $summary): void
    {
        try {
            activity('translations')
                ->event('translations.dynamic_keys.sync.completed')
                ->withProperties(ConsoleActivityContext::merge($this, [
                    'summary' => $summary,
                ]))
                ->log('Dynamic translation key sync completed');
        } catch (Throwable $exception) {
            $this->warn('Activity log write failed for command run summary: ' . $exception->getMessage());
        }
    }

    private function relativePath(string $path): string
    {
        return str_replace(base_path() . DIRECTORY_SEPARATOR, '', $path);
    }
}
