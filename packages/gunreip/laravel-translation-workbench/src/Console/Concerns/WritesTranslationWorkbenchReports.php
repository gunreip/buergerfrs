<?php

namespace Gunreip\TranslationWorkbench\Console\Concerns;

use Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchScanner;
use Gunreip\TranslationWorkbench\Scanner\TranslationKeyPartsFactory;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Throwable;

trait WritesTranslationWorkbenchReports
{
    /**
     * Build the shared translation-workbench raw-data report in one central place.
     *
     * Do not redefine the raw_data structure inside individual commands. All
     * commands must use this method so their reports are comparable and based on
     * the same scanner output. Changes to this report contract must be discussed
     * before implementation; do not change it silently from a command.
     */
    /**
     * @param  array<string, mixed>  $summary
     * @param  array<string, mixed>  $planSummary
     */
    protected function writeTranslationWorkbenchReport(?string $commandName = null, array $summary = [], array $planSummary = []): string
    {
        $commandName ??= (string) $this->getName();
        $directory = storage_path('translation-workbench');
        $filename = Str::of($commandName)
            ->replace(':', '-')
            ->replace('\\', '-')
            ->replace('/', '-')
            ->slug('-')
            ->append('.json')
            ->toString();
        $path = $directory . DIRECTORY_SEPARATOR . $filename;

        File::ensureDirectoryExists($directory);
        File::put($path, json_encode([
            'command' => $commandName,
            'generated_at' => now()->toISOString(),
            'summary' => $summary,
            'plan_summary' => $planSummary,
            'raw_data' => $this->translationWorkbenchReportRawData(),
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL);

        $this->line('JSON report: ' . $path);

        return $path;
    }

    /**
     * @return array{scanned_paths: array<int, string>, file_patterns: array<int, string>, files: int, found: int, files_found: array<int, array{file: string, found: int, findings: array<int, array{line: int|null, kind: string, function: string|null, literal_text: string|null, literal_text_suggested: string|null, translation_key: string|null, existing_key: string|null, raw_expression: string|null, suggested_key: string|null, namespace: string|null, group: string|null, path_key: string|null, scope: string|null, dynamic_scope: string|null}>}>}
     */
    private function translationWorkbenchReportRawData(): array
    {
        $scanner = app(TranslationWorkbenchScanner::class);
        $keyPartsFactory = app(TranslationKeyPartsFactory::class);
        $paths = $this->translationWorkbenchReportPaths();
        $files = collect($scanner->scannableFiles($paths))
            ->map(static fn(\SplFileInfo $file): string => str_replace('\\', '/', $file->getPathname()))
            ->sort()
            ->values();
        $filesFound = $files
            ->map(function (string $path) use ($scanner, $keyPartsFactory): array {
                $findings = $scanner->scanFile($path)
                    ->map(function ($item) use ($keyPartsFactory): array {
                        $keyParts = $keyPartsFactory->fromKey($item->suggestedKey);

                        return [
                            'line' => $item->sourceLine,
                            'kind' => $item->kind,
                            'function' => $item->functionName,
                            'literal_text' => $item->literalText,
                            'literal_text_suggested' => $item->literalTextSuggested,
                            'translation_key' => $item->translationKey,
                            'existing_key' => $item->existingKey,
                            'raw_expression' => $item->rawExpression,
                            'suggested_key' => $item->suggestedKey,
                            'namespace' => $keyParts['namespace'],
                            'group' => $keyParts['group'],
                            'path_key' => $keyParts['path_key'],
                            'scope' => $keyParts['scope'],
                            'dynamic_scope' => self::translationWorkbenchDynamicScope($item->meta),
                        ];
                    })
                    ->values()
                    ->all();

                return [
                    'file' => $this->translationWorkbenchRelativePath($path),
                    'found' => count($findings),
                    'findings' => $findings,
                ];
            })
            ->values()
            ->all();

        return [
            'scanned_paths' => $this->translationWorkbenchReportScannedPaths($paths),
            'file_patterns' => $this->translationWorkbenchReportFilePatterns(),
            'files' => $files->count(),
            'found' => collect($filesFound)->sum('found'),
            'files_found' => $filesFound,
        ];
    }

    /**
     * @return array<int, string>|null
     */
    private function translationWorkbenchReportPaths(): ?array
    {
        try {
            $paths = trim((string) $this->option('paths'));
        } catch (Throwable) {
            return null;
        }

        if ($paths === '') {
            return null;
        }

        return collect(explode(',', $paths))
            ->map(static fn(string $path): string => trim($path))
            ->filter()
            ->values()
            ->all();
    }

    private function translationWorkbenchRelativePath(string $path): string
    {
        $path = str_replace('\\', '/', $path);
        $basePath = str_replace('\\', '/', base_path());

        return str_starts_with($path, $basePath . '/')
            ? substr($path, strlen($basePath) + 1)
            : $path;
    }

    /**
     * @param  array<int, string>|null  $paths
     * @return array<int, string>
     */
    private function translationWorkbenchReportScannedPaths(?array $paths): array
    {
        return collect($paths ?? (array) config('translation-workbench.paths', []))
            ->map(static fn(string $path): string => trim(str_replace('\\', '/', $path)))
            ->filter()
            ->values()
            ->all();
    }

    /**
     * @return array<int, string>
     */
    private function translationWorkbenchReportFilePatterns(): array
    {
        return collect((array) config('translation-workbench.file_patterns', []))
            ->filter(static fn(mixed $pattern): bool => is_string($pattern) && trim($pattern) !== '')
            ->values()
            ->all();
    }

    /**
     * @param  array<string, mixed>  $meta
     */
    private static function translationWorkbenchDynamicScope(array $meta): ?string
    {
        $scope = $meta['dynamic_scope'] ?? $meta['dynamic_option_context']['scope'] ?? null;

        return is_string($scope) && trim($scope) !== ''
            ? trim($scope)
            : null;
    }
}
