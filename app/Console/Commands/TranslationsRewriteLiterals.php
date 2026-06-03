<?php

namespace App\Console\Commands;

use App\Models\TranslationKey;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use SplFileInfo;
use Symfony\Component\Finder\Finder;
use Throwable;

/**
 * Rewrites translation literal calls in source files to translation keys.
 */
class TranslationsRewriteLiterals extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'translations:rewrite-literals
        {--dry-run : Show replacements without writing files}
        {--paths= : Comma-separated relative paths to scan (default: app,routes,resources/views)}';

    /**
     * The console command description.
     */
    protected $description = 'Rewrite translation literal calls to translation keys using translation_keys.native_text mappings.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $paths = $this->scanPaths();
        $literalToKeyMap = $this->literalToKeyMap();

        if ($literalToKeyMap === []) {
            $this->warn('No native_text -> key mappings found. Nothing to rewrite.');

            $this->logRunCompletedActivity($dryRun, $paths, 0, 0, 0, 0);

            return self::SUCCESS;
        }

        $filesScanned = 0;
        $filesChanged = 0;
        $replacementsTotal = 0;
        $rows = [];

        foreach ($this->scannableFiles($paths) as $file) {
            $path = $file->getPathname();
            $relativePath = $this->relativePath($path);

            $filesScanned++;

            $contents = File::get($path);
            [$rewritten, $replacements] = $this->rewriteContent($contents, $literalToKeyMap);

            if ($replacements === 0) {
                continue;
            }

            $filesChanged++;
            $replacementsTotal += $replacements;

            if (! $dryRun) {
                File::put($path, $rewritten);
                $this->logFileRewrittenActivity($relativePath, $replacements);
            }

            $rows[] = [$relativePath, $replacements, $dryRun ? 'would_update' : 'updated'];
        }

        $this->components->info('Translation literal rewrite finished.');

        if ($rows !== []) {
            $this->table(['File', 'Replacements', 'Status'], $rows);
        } else {
            $this->line('No literal translation calls matched the current mappings.');
        }

        $this->line('');
        $this->line('Files scanned: ' . $filesScanned);
        $this->line('Files changed: ' . $filesChanged);
        $this->line('Replacements: ' . $replacementsTotal);

        if ($dryRun) {
            $this->warn('Dry run only: no files were written.');
        }

        $this->logRunCompletedActivity($dryRun, $paths, $filesScanned, $filesChanged, $replacementsTotal, count($literalToKeyMap));

        return self::SUCCESS;
    }

    /**
     * @return array<int, string>
     */
    private function scanPaths(): array
    {
        $pathsOption = trim((string) $this->option('paths'));

        if ($pathsOption === '') {
            return [
                'app',
                'routes',
                'resources/views',
            ];
        }

        return collect(explode(',', $pathsOption))
            ->map(fn(string $path): string => trim($path))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @return array<string, string>
     */
    private function literalToKeyMap(): array
    {
        $mappings = TranslationKey::query()
            ->whereNotNull('native_text')
            ->where('native_text', '<>', '')
            ->whereNotNull('key')
            ->where('key', '<>', '')
            ->orderBy('id', 'asc')
            ->get(['native_text', 'key']);

        $map = [];

        foreach ($mappings as $mapping) {
            $nativeText = trim((string) $mapping->native_text);
            $key = trim((string) $mapping->key);

            if ($nativeText === '' || $key === '' || $nativeText === $key) {
                continue;
            }

            if (! isset($map[$nativeText])) {
                $map[$nativeText] = $key;
            }
        }

        return $map;
    }

    /**
     * @param array<int, string> $paths
     * @return iterable<int, SplFileInfo>
     */
    private function scannableFiles(array $paths): iterable
    {
        $directories = collect($paths)
            ->map(fn(string $path): string => base_path($path))
            ->filter(fn(string $path): bool => File::isDirectory($path))
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
     * @param array<string, string> $literalToKeyMap
     * @return array{0: string, 1: int}
     */
    private function rewriteContent(string $contents, array $literalToKeyMap): array
    {
        $replacements = 0;

        $rewritten = preg_replace_callback(
            '/(?P<function>__|trans|@lang|Lang::get)\(\s*(?P<quote>[\'\"])(?P<value>(?:\\\\.|(?!\k<quote>).)*)\k<quote>/u',
            function (array $match) use ($literalToKeyMap, &$replacements): string {
                $quote = $match['quote'];
                $literalRaw = $match['value'];
                $literalDecoded = stripcslashes($literalRaw);

                if (! isset($literalToKeyMap[$literalDecoded])) {
                    return $match[0];
                }

                $key = $literalToKeyMap[$literalDecoded];

                if ($this->looksLikeTranslationKey($literalDecoded)) {
                    return $match[0];
                }

                $escapedKey = addcslashes($key, "\\{$quote}");
                $replacements++;

                return $match['function'] . '(' . $quote . $escapedKey . $quote;
            },
            $contents,
        );

        if (! is_string($rewritten)) {
            return [$contents, 0];
        }

        return [$rewritten, $replacements];
    }

    private function looksLikeTranslationKey(string $value): bool
    {
        return str_contains($value, '.')
            && ! str_contains($value, ' ')
            && preg_match('/^[a-z0-9_.-]+$/', $value) === 1;
    }

    private function relativePath(string $path): string
    {
        return str_replace(base_path() . DIRECTORY_SEPARATOR, '', $path);
    }

    private function logFileRewrittenActivity(string $path, int $replacements): void
    {
        try {
            activity('translations')
                ->event('translations.literals.file_rewritten')
                ->withProperties([
                    'command' => $this->getName(),
                    'path' => $path,
                    'replacements' => $replacements,
                ])
                ->log('Translation literal calls rewritten in file');
        } catch (Throwable $exception) {
            $this->warn('Activity log write failed for file "' . $path . '": ' . $exception->getMessage());
        }
    }

    /**
     * @param array<int, string> $paths
     */
    private function logRunCompletedActivity(
        bool $dryRun,
        array $paths,
        int $filesScanned,
        int $filesChanged,
        int $replacements,
        int $mappingCount,
    ): void {
        try {
            activity('translations')
                ->event('translations.literals.rewrite.completed')
                ->withProperties([
                    'command' => $this->getName(),
                    'options' => [
                        'dry_run' => $dryRun,
                        'paths' => $paths,
                    ],
                    'summary' => [
                        'files_scanned' => $filesScanned,
                        'files_changed' => $filesChanged,
                        'replacements' => $replacements,
                        'mapping_count' => $mappingCount,
                    ],
                ])
                ->log('Translation literal rewrite completed');
        } catch (Throwable $exception) {
            $this->warn('Activity log write failed for command run summary: ' . $exception->getMessage());
        }
    }
}
