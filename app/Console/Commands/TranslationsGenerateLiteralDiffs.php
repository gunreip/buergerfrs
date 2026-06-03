<?php

namespace App\Console\Commands;

use App\Models\TranslationKey;
use App\Models\TranslationLanguage;
use App\Models\TranslationValue;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use SplFileInfo;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Process;
use Throwable;

/**
 * Generates patch files for replacing literal translation calls with keys.
 */
class TranslationsGenerateLiteralDiffs extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'translations:generate-literal-diffs
        {--paths= : Comma-separated relative paths to scan (default: app,routes,resources/views)}
        {--output-dir= : Relative output directory for generated patch files}
        {--allow-suggested : Also use suggested_key when key is empty}
        {--require-complete-values : Require non-empty translation values for all enabled locales (default: true)}
        {--no-require-complete-values : Allow mappings even if some locale values are missing}
        {--write-per-file-patches : Also write individual per-file patch files}';

    /**
     * The console command description.
     */
    protected $description = 'Generate runnable patch diffs for replacing literal translation calls with translation keys.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $paths = $this->scanPaths();
        $outputDir = $this->outputDir();
        $allowSuggested = (bool) $this->option('allow-suggested');
        $requireCompleteValues = ! (bool) $this->option('no-require-complete-values');
        $writePerFilePatches = (bool) $this->option('write-per-file-patches');

        $enabledLocales = $this->enabledLocales();
        $literalToKeyMap = $this->literalToKeyMap($allowSuggested, $requireCompleteValues, $enabledLocales);

        if ($literalToKeyMap === []) {
            $this->warn('No eligible native_text -> key mappings found. No patches generated.');
            $this->line('Tip: set real keys first, or rerun with --allow-suggested / --no-require-complete-values as needed.');

            $this->logCompletedActivity(
                paths: $paths,
                outputDir: $outputDir,
                allowSuggested: $allowSuggested,
                requireCompleteValues: $requireCompleteValues,
                enabledLocales: $enabledLocales,
                filesScanned: 0,
                filesWithDiff: 0,
                replacements: 0,
                mappingCount: 0,
            );

            return self::SUCCESS;
        }

        File::ensureDirectoryExists($outputDir);
        $this->cleanupOldPatches($outputDir);

        $filesScanned = 0;
        $filesWithDiff = 0;
        $replacements = 0;
        $rows = [];
        $index = [];
        $combinedPatch = '';

        foreach ($this->scannableFiles($paths) as $file) {
            $path = $file->getPathname();
            $relativePath = $this->relativePath($path);
            $filesScanned++;

            $original = File::get($path);
            [$rewritten, $replacementsInFile] = $this->rewriteContent($original, $literalToKeyMap);

            if ($replacementsInFile === 0 || $rewritten === $original) {
                continue;
            }

            $patch = $this->buildPatchForFile($relativePath, $original, $rewritten);

            if ($patch === null) {
                $this->warn('Could not generate patch for: ' . $relativePath);

                continue;
            }

            $filesWithDiff++;
            $replacements += $replacementsInFile;

            $patchPathForTable = '-';

            if ($writePerFilePatches) {
                $patchFile = $outputDir . '/files/' . $this->safePatchName($relativePath) . '.patch';
                File::ensureDirectoryExists(dirname($patchFile));
                File::put($patchFile, $patch);
                $patchPathForTable = $this->relativePath($patchFile);
            }

            $combinedPatch .= ($combinedPatch === '' ? '' : "\n") . rtrim($patch) . "\n";
            $rows[] = [$relativePath, $replacementsInFile, $patchPathForTable];

            $index[] = [
                'file' => $relativePath,
                'replacements' => $replacementsInFile,
            ];

            $this->logPatchEntryCreatedActivity(
                sourcePath: $relativePath,
                patchPath: $patchPathForTable,
                replacements: $replacementsInFile,
            );
        }

        $combinedPatchPath = $outputDir . '/latest.patch';
        $applyScriptPath = $outputDir . '/latest.apply.sh';
        $indexPath = $outputDir . '/latest.index.json';

        if ($combinedPatch !== '') {
            File::put($combinedPatchPath, $combinedPatch);
            File::put($applyScriptPath, $this->applyScriptContent($this->relativePath($combinedPatchPath)));
            File::put($indexPath, json_encode([
                'generated_at' => now()->toIso8601String(),
                'files_with_diff' => $filesWithDiff,
                'replacements' => $replacements,
                'entries' => $index,
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            @chmod($applyScriptPath, 0755);
        }

        $this->components->info('Translation literal diff generation finished.');

        if ($rows !== []) {
            $this->table(['File', 'Replacements', 'Patch'], $rows);
        } else {
            $this->line('No diffs generated from current mappings.');
        }

        $this->line('');
        $this->line('Files scanned: ' . $filesScanned);
        $this->line('Files with diff: ' . $filesWithDiff);
        $this->line('Replacements: ' . $replacements);
        $this->line('Patch output dir: ' . $this->relativePath($outputDir));

        if ($combinedPatch !== '') {
            $this->line('Combined patch: ' . $this->relativePath($combinedPatchPath));
            $this->line('Apply script: ' . $this->relativePath($applyScriptPath));
            $this->line('Index file: ' . $this->relativePath($indexPath));
            $this->line('Run: bash ' . $this->relativePath($applyScriptPath));
        }

        $this->logCompletedActivity(
            paths: $paths,
            outputDir: $outputDir,
            allowSuggested: $allowSuggested,
            requireCompleteValues: $requireCompleteValues,
            enabledLocales: $enabledLocales,
            filesScanned: $filesScanned,
            filesWithDiff: $filesWithDiff,
            replacements: $replacements,
            mappingCount: count($literalToKeyMap),
        );

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
            ->map(fn(string $path): string => trim($path))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function outputDir(): string
    {
        $relative = trim((string) $this->option('output-dir'));

        if ($relative === '') {
            $relative = 'storage/audits/translations/diffs';
        }

        return str_starts_with($relative, DIRECTORY_SEPARATOR)
            ? $relative
            : base_path($relative);
    }

    /**
     * @return array<int, string>
     */
    private function enabledLocales(): array
    {
        return TranslationLanguage::query()
            ->where('is_enabled_for_translation', true)
            ->orderBy('sort_order', 'asc')
            ->orderBy('locale', 'asc')
            ->pluck('locale')
            ->filter(fn(?string $locale): bool => $locale !== null && $locale !== '')
            ->values()
            ->all();
    }

    /**
     * @param array<int, string> $enabledLocales
     * @return array<string, string>
     */
    private function literalToKeyMap(bool $allowSuggested, bool $requireCompleteValues, array $enabledLocales): array
    {
        $rows = TranslationKey::query()
            ->orderBy('id', 'asc')
            ->get(['id', 'native_text', 'key', 'suggested_key']);

        $map = [];

        foreach ($rows as $row) {
            $nativeText = trim((string) $row->native_text);
            $key = trim((string) $row->key);

            if ($nativeText === '') {
                continue;
            }

            if ($key === '' && $allowSuggested) {
                $key = trim((string) $row->suggested_key);
            }

            if ($key === '' || $nativeText === $key || isset($map[$nativeText])) {
                continue;
            }

            if ($requireCompleteValues && ! $this->hasCompleteValues((int) $row->id, $enabledLocales)) {
                continue;
            }

            $map[$nativeText] = $key;
        }

        return $map;
    }

    /**
     * @param array<int, string> $enabledLocales
     */
    private function hasCompleteValues(int $translationKeyId, array $enabledLocales): bool
    {
        if ($enabledLocales === []) {
            return true;
        }

        $values = TranslationValue::query()
            ->where('translation_key_id', $translationKeyId)
            ->get(['locale', 'value']);

        $existingLocales = $values
            ->filter(fn(TranslationValue $value): bool => in_array((string) $value->locale, $enabledLocales, true))
            ->filter(fn(TranslationValue $value): bool => trim((string) $value->value) !== '')
            ->pluck('locale')
            ->unique()
            ->values()
            ->all();

        return count(array_diff($enabledLocales, $existingLocales)) === 0;
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

                if ($this->looksLikeTranslationKey($literalDecoded)) {
                    return $match[0];
                }

                $key = $literalToKeyMap[$literalDecoded];
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

    private function buildPatchForFile(string $relativePath, string $original, string $rewritten): ?string
    {
        $tmpBase = storage_path('app/tmp/translations-diff-' . bin2hex(random_bytes(6)));
        File::ensureDirectoryExists($tmpBase);

        $oldFile = $tmpBase . '/old.tmp';
        $newFile = $tmpBase . '/new.tmp';

        File::put($oldFile, $original);
        File::put($newFile, $rewritten);

        $process = new Process([
            'diff',
            '-u',
            $oldFile,
            $newFile,
        ], base_path());

        $process->run();

        $exitCode = $process->getExitCode();
        $output = $process->getOutput();
        $errorOutput = $process->getErrorOutput();

        File::deleteDirectory($tmpBase);

        if ($exitCode !== 0 && $exitCode !== 1) {
            $this->warn('diff failed for ' . $relativePath . ': ' . trim($errorOutput));

            return null;
        }

        if (trim($output) === '') {
            return null;
        }

        $lines = preg_split('/\R/', $output) ?: [];

        if (count($lines) >= 2) {
            $lines[0] = '--- a/' . $relativePath;
            $lines[1] = '+++ b/' . $relativePath;
        }

        return implode("\n", $lines) . "\n";
    }

    private function safePatchName(string $relativePath): string
    {
        return preg_replace('/[^A-Za-z0-9._-]+/', '__', $relativePath) ?? 'patch';
    }

    private function relativePath(string $path): string
    {
        return str_replace(base_path() . DIRECTORY_SEPARATOR, '', $path);
    }

    private function cleanupOldPatches(string $outputDir): void
    {
        $files = File::glob($outputDir . '/*.patch') ?: [];

        foreach ($files as $file) {
            File::delete($file);
        }

        $perFilePatches = File::glob($outputDir . '/files/*.patch') ?: [];

        foreach ($perFilePatches as $file) {
            File::delete($file);
        }

        File::delete($outputDir . '/latest.apply.sh');
        File::delete($outputDir . '/latest.index.json');
        File::delete($outputDir . '/apply-all.sh');
    }

    private function applyScriptContent(string $relativeCombinedPatchPath): string
    {
        return implode("\n", [
            '#!/usr/bin/env bash',
            'set -euo pipefail',
            '',
            'echo "Checking patch..."',
            'git apply --check ' . escapeshellarg($relativeCombinedPatchPath),
            'echo "Applying patch..."',
            'git apply ' . escapeshellarg($relativeCombinedPatchPath),
            'echo "Done."',
            '',
        ]);
    }

    private function logPatchEntryCreatedActivity(string $sourcePath, string $patchPath, int $replacements): void
    {
        try {
            activity('translations')
                ->event('translations.literals.diff.file_created')
                ->withProperties([
                    'command' => $this->getName(),
                    'source_path' => $sourcePath,
                    'patch_path' => $patchPath,
                    'replacements' => $replacements,
                ])
                ->log('Translation literal diff patch created');
        } catch (Throwable $exception) {
            $this->warn('Activity log write failed for patch "' . $patchPath . '": ' . $exception->getMessage());
        }
    }

    /**
     * @param array<int, string> $paths
     * @param array<int, string> $enabledLocales
     */
    private function logCompletedActivity(
        array $paths,
        string $outputDir,
        bool $allowSuggested,
        bool $requireCompleteValues,
        array $enabledLocales,
        int $filesScanned,
        int $filesWithDiff,
        int $replacements,
        int $mappingCount,
    ): void {
        try {
            activity('translations')
                ->event('translations.literals.diff.completed')
                ->withProperties([
                    'command' => $this->getName(),
                    'options' => [
                        'paths' => $paths,
                        'output_dir' => $this->relativePath($outputDir),
                        'allow_suggested' => $allowSuggested,
                        'require_complete_values' => $requireCompleteValues,
                        'enabled_locales' => $enabledLocales,
                    ],
                    'summary' => [
                        'files_scanned' => $filesScanned,
                        'files_with_diff' => $filesWithDiff,
                        'replacements' => $replacements,
                        'mapping_count' => $mappingCount,
                    ],
                ])
                ->log('Translation literal diff generation completed');
        } catch (Throwable $exception) {
            $this->warn('Activity log write failed for diff command summary: ' . $exception->getMessage());
        }
    }
}
