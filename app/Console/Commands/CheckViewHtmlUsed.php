<?php

// app/Console/Commands/CheckViewHtmlUsed.php

// php artisan html:check-view-html-used

namespace App\Console\Commands;

use App\Support\ActivityLog\ConsoleActivityContext;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use SplFileInfo;
use Throwable;

#[Signature('html:check-view-html-used')]
#[Description('Audit used native HTML tags, Flux components and custom Blade components in Blade views.')]
/**
 * Audit command for tracking native HTML and component usage across Blade views.
 */
class CheckViewHtmlUsed extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Check used native HTML tags, Flux components and custom Blade components');

        $config = config('html-audit.view_html_used', []);

        $scanPaths = $config['scan_paths'] ?? [resource_path('views')];
        $componentPaths = $config['component_paths'] ?? [];

        $componentPathStatus = $this->componentPathStatus($componentPaths);

        $availableNativeTags = $this->availableNativeTags((string) ($config['native_reference_path'] ?? ''));
        $availableFluxComponents = $this->availableComponents($componentPaths['flux'] ?? [], $config);
        $availableCustomComponents = $this->availableComponents($componentPaths['custom'] ?? [], $config);

        $scanResult = $this->scanViews($scanPaths, $config);

        $native = $this->buildNativeSection($availableNativeTags, $scanResult['native']);
        $flux = $this->buildComponentSection($availableFluxComponents, $scanResult['flux']);
        $custom = $this->buildComponentSection($availableCustomComponents, $scanResult['custom']);

        $payload = [
            'generated_at' => now()->toIso8601String(),
            'scan' => [
                'paths' => $this->normalizeConfiguredPaths($scanPaths),
                'files_scanned' => $scanResult['files_scanned'],
                'skipped_paths' => $scanResult['skipped_paths'],
                'excluded_files' => $scanResult['excluded_files'],
            ],
            'components' => [
                'source_paths' => $componentPathStatus['source_paths'],
                'skipped_paths' => $componentPathStatus['skipped_paths'],
            ],
            'native' => $native,
            'flux' => $flux,
            'custom' => $custom,
            'includes' => [
                'counts' => [
                    'used' => count($scanResult['includes']),
                ],
                'used' => $scanResult['includes'],
            ],
            'livewire' => [
                'counts' => [
                    'used' => count($scanResult['livewire']),
                ],
                'used' => $scanResult['livewire'],
            ],
        ];

        $preview = $this->previewPayload($payload, (int) ($config['preview_limit'] ?? 20));

        $this->writeJson((string) ($config['output_path'] ?? storage_path('audits/html/view-html-used.json')), $payload);
        $this->writeJson((string) ($config['preview_path'] ?? storage_path('audits/html/view-html-used-preview.json')), $preview);

        $this->line('');
        $this->info('View HTML usage audit written.');
        $this->line('Native available:    '.$native['counts']['available']);
        $this->line('Native used:         '.$native['counts']['used']);
        $this->line('Native unused:       '.$native['counts']['unused']);
        $this->line('Native unknown:      '.$native['counts']['unknown']);
        $this->line('Flux available:      '.$flux['counts']['available']);
        $this->line('Flux used:           '.$flux['counts']['used']);
        $this->line('Flux unused:         '.$flux['counts']['unused']);
        $this->line('Flux used unknown:   '.$flux['counts']['used_unknown']);
        $this->line('Custom available:    '.$custom['counts']['available']);
        $this->line('Custom used:         '.$custom['counts']['used']);
        $this->line('Custom unused:       '.$custom['counts']['unused']);
        $this->line('Custom used unknown: '.$custom['counts']['used_unknown']);
        $this->logRunActivity('html.view_usage_check.completed', 'HTML view usage audit completed.', [
            'files_scanned' => $scanResult['files_scanned'],
            'native_counts' => $native['counts'],
            'flux_counts' => $flux['counts'],
            'custom_counts' => $custom['counts'],
            'includes_used' => count($scanResult['includes']),
            'livewire_used' => count($scanResult['livewire']),
        ]);

        return self::SUCCESS;
    }

    /**
     * @param  array<string, array<int, array<string, string>|string>>  $componentPaths
     * @return array<string, array<string, array<int, array<string, string>>>>
     */
    private function componentPathStatus(array $componentPaths): array
    {
        $sourcePaths = [];
        $skippedPaths = [];

        foreach ($componentPaths as $group => $definitions) {
            foreach ($definitions as $definition) {
                $path = is_array($definition) ? (string) ($definition['path'] ?? '') : (string) $definition;
                $prefix = is_array($definition) ? (string) ($definition['prefix'] ?? '') : '';

                $entry = [
                    'path' => $this->relativePath($path),
                    'prefix' => $prefix,
                ];

                if ($path !== '' && is_dir($path)) {
                    $sourcePaths[(string) $group][] = $entry;

                    continue;
                }

                $skippedPaths[(string) $group][] = $entry;
            }
        }

        ksort($sourcePaths, SORT_NATURAL);
        ksort($skippedPaths, SORT_NATURAL);

        return [
            'source_paths' => $sourcePaths,
            'skipped_paths' => $skippedPaths,
        ];
    }

    /**
     * @return array<int, string>
     */
    private function availableNativeTags(string $path): array
    {
        if ($path === '' || ! is_file($path)) {
            return [];
        }

        $payload = json_decode((string) file_get_contents($path), true);

        if (! is_array($payload)) {
            return [];
        }

        $normal = $payload['tags']['normal'] ?? [];
        $void = $payload['tags']['void'] ?? [];

        return collect([...$normal, ...$void])
            ->filter(fn (mixed $tag): bool => is_string($tag) && $tag !== '')
            ->map(fn (string $tag): string => strtolower($tag))
            ->unique()
            ->sort()
            ->values()
            ->all();
    }

    /**
     * @param  array<int, array<string, string>|string>  $definitions
     * @param  array<string, mixed>  $config
     * @return array<string, array<string, string>>
     */
    private function availableComponents(array $definitions, array $config): array
    {
        $components = [];

        foreach ($definitions as $definition) {
            $path = is_array($definition) ? (string) ($definition['path'] ?? '') : (string) $definition;
            $prefix = is_array($definition) ? (string) ($definition['prefix'] ?? '') : '';

            if ($path === '' || $prefix === '' || ! is_dir($path)) {
                continue;
            }

            foreach (File::allFiles($path) as $file) {
                if (! $file instanceof SplFileInfo || $file->getExtension() !== 'php') {
                    continue;
                }

                if (! str_ends_with($file->getFilename(), '.blade.php')) {
                    continue;
                }

                $relativePath = $this->relativePath($file->getPathname());

                if ($this->isExcluded($relativePath, $file->getFilename(), $config)) {
                    continue;
                }

                $name = $this->componentNameFromPath($path, $file->getPathname(), $prefix);

                if ($name === null) {
                    continue;
                }

                $components[$name] = [
                    'path' => $relativePath,
                ];
            }
        }

        ksort($components, SORT_NATURAL);

        return $components;
    }

    private function componentNameFromPath(string $basePath, string $filePath, string $prefix): ?string
    {
        $relative = Str::of($filePath)
            ->after(rtrim($basePath, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR)
            ->replace(DIRECTORY_SEPARATOR, '/')
            ->replaceEnd('.blade.php', '')
            ->toString();

        if ($relative === '') {
            return null;
        }

        if (str_ends_with($relative, '/index')) {
            $relative = Str::beforeLast($relative, '/index');
        }

        $name = str_replace('/', '.', $relative);

        if ($name === '') {
            return null;
        }

        return $prefix.$name;
    }

    /**
     * @param  array<int, string>  $scanPaths
     * @param  array<string, mixed>  $config
     * @return array<string, mixed>
     */
    private function scanViews(array $scanPaths, array $config): array
    {
        $result = [
            'files_scanned' => 0,
            'skipped_paths' => [],
            'excluded_files' => [],
            'native' => [],
            'flux' => [],
            'custom' => [],
            'includes' => [],
            'livewire' => [],
        ];

        $componentTagPrefixes = $config['component_tag_prefixes'] ?? [];

        $customTagPrefixes = collect($componentTagPrefixes['custom'] ?? ['x-ui.'])
            ->filter(fn (mixed $prefix): bool => is_string($prefix) && $prefix !== '')
            ->values()
            ->all();

        $fluxTagPrefixes = collect($componentTagPrefixes['flux'] ?? ['flux:'])
            ->filter(fn (mixed $prefix): bool => is_string($prefix) && $prefix !== '')
            ->values()
            ->all();

        foreach ($scanPaths as $path) {
            if (! is_dir($path)) {
                $result['skipped_paths'][] = $path;

                continue;
            }

            foreach (File::allFiles($path) as $file) {
                if (! $file instanceof SplFileInfo || $file->getExtension() !== 'php') {
                    continue;
                }

                if (! str_ends_with($file->getFilename(), '.blade.php')) {
                    continue;
                }

                $pathName = $file->getPathname();
                $relativePath = $this->relativePath($pathName);

                if ($this->isExcluded($relativePath, $file->getFilename(), $config)) {
                    $result['excluded_files'][] = $relativePath;

                    continue;
                }

                $result['files_scanned']++;

                $contents = $this->stripIgnoredContent((string) file_get_contents($pathName));
                $nativeContents = $this->stripNonNativeHtmlContent($contents);

                $this->mergeOccurrences($result['native'], $this->extractNativeTags($nativeContents, $relativePath));
                $this->mergeOccurrences($result['flux'], $this->extractComponentTags($contents, $relativePath, $fluxTagPrefixes));
                $this->mergeOccurrences($result['custom'], $this->extractComponentTags($contents, $relativePath, $customTagPrefixes));
                $this->mergeOccurrences($result['includes'], $this->extractIncludes($contents, $relativePath));
                $this->mergeOccurrences($result['livewire'], $this->extractLivewireReferences($contents, $relativePath));
            }
        }

        return $result;
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function extractNativeTags(string $contents, string $path): array
    {
        return $this->extractOccurrences(
            '/<\s*\/?\s*([a-z][a-z0-9-]*)(?=[\s>\/])/iu',
            $contents,
            $path,
            fn (string $tag): string => strtolower($tag),
        );
    }

    /**
     * @param  array<int, string>  $prefixes
     * @return array<string, array<string, mixed>>
     */
    private function extractComponentTags(string $contents, string $path, array $prefixes): array
    {
        $components = [];

        foreach ($prefixes as $prefix) {
            $quotedPrefix = preg_quote($prefix, '/');

            $this->mergeOccurrences($components, $this->extractOccurrences(
                '/<\s*\/?\s*('.$quotedPrefix.'[a-z0-9._:-]+)\b/iu',
                $contents,
                $path,
                fn (string $tag): string => strtolower($tag),
            ));
        }

        return $components;
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function extractIncludes(string $contents, string $path): array
    {
        return $this->extractOccurrences(
            '/@include(?:If|When|Unless|First)?\(\s*[\'"]([^\'"]+)[\'"]/u',
            $contents,
            $path,
            fn (string $include): string => $include,
        );
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function extractLivewireReferences(string $contents, string $path): array
    {
        $references = [];

        $this->mergeOccurrences($references, $this->extractOccurrences(
            '/<\s*\/?\s*(livewire:[a-z0-9._:-]+)\b/iu',
            $contents,
            $path,
            fn (string $tag): string => strtolower($tag),
        ));

        $this->mergeOccurrences($references, $this->extractOccurrences(
            '/@livewire\(\s*[\'"]([^\'"]+)[\'"]/u',
            $contents,
            $path,
            fn (string $component): string => $component,
        ));

        return $references;
    }

    /**
     * @param  callable(string): string  $normalizer
     * @return array<string, array<string, mixed>>
     */
    private function extractOccurrences(string $pattern, string $contents, string $path, callable $normalizer): array
    {
        preg_match_all($pattern, $contents, $matches, PREG_OFFSET_CAPTURE);

        $occurrences = [];

        foreach ($matches[1] ?? [] as $match) {
            $name = $normalizer((string) $match[0]);
            $line = $this->lineForOffset($contents, (int) $match[1]);

            if ($name === '') {
                continue;
            }

            $occurrences[$name] ??= [
                'count' => 0,
                'files' => [],
            ];

            $occurrences[$name]['count']++;
            $occurrences[$name]['files'][$path] ??= [
                'path' => $path,
                'lines' => [],
            ];

            $occurrences[$name]['files'][$path]['lines'][] = $line;
            $occurrences[$name]['files'][$path]['lines'] = array_values(array_unique($occurrences[$name]['files'][$path]['lines']));
        }

        foreach ($occurrences as $name => $occurrence) {
            $occurrences[$name]['files'] = array_values($occurrence['files']);
        }

        ksort($occurrences, SORT_NATURAL);

        return $occurrences;
    }

    /**
     * @param  array<string, array<string, mixed>>  $target
     * @param  array<string, array<string, mixed>>  $source
     */
    private function mergeOccurrences(array &$target, array $source): void
    {
        foreach ($source as $name => $occurrence) {
            $target[$name] ??= [
                'count' => 0,
                'files' => [],
            ];

            $target[$name]['count'] += (int) ($occurrence['count'] ?? 0);

            foreach ($occurrence['files'] ?? [] as $file) {
                if (! is_array($file) || ! isset($file['path'])) {
                    continue;
                }

                $path = (string) $file['path'];

                $target[$name]['files'][$path] ??= [
                    'path' => $path,
                    'lines' => [],
                ];

                $target[$name]['files'][$path]['lines'] = array_values(array_unique([
                    ...$target[$name]['files'][$path]['lines'],
                    ...($file['lines'] ?? []),
                ]));

                sort($target[$name]['files'][$path]['lines']);
            }

            $target[$name]['files'] = array_values($target[$name]['files']);
        }

        ksort($target, SORT_NATURAL);
    }

    /**
     * @param  array<int, string>  $available
     * @param  array<string, array<string, mixed>>  $used
     * @return array<string, mixed>
     */
    private function buildNativeSection(array $available, array $used): array
    {
        $usedNames = array_keys($used);

        $unused = collect($available)
            ->diff($usedNames)
            ->values()
            ->all();

        $unknown = collect($usedNames)
            ->diff($available)
            ->values()
            ->mapWithKeys(fn (string $tag): array => [$tag => $used[$tag]])
            ->all();

        $usedKnown = collect($usedNames)
            ->intersect($available)
            ->values()
            ->mapWithKeys(fn (string $tag): array => [$tag => $used[$tag]])
            ->all();

        return [
            'counts' => [
                'available' => count($available),
                'used' => count($usedKnown),
                'unused' => count($unused),
                'unknown' => count($unknown),
            ],
            'available' => $available,
            'used' => $usedKnown,
            'unused' => $unused,
            'unknown' => $unknown,
        ];
    }

    /**
     * @param  array<string, array<string, string>>  $available
     * @param  array<string, array<string, mixed>>  $used
     * @return array<string, mixed>
     */
    private function buildComponentSection(array $available, array $used): array
    {
        $availableNames = array_keys($available);
        $usedNames = array_keys($used);

        $unusedNames = collect($availableNames)
            ->diff($usedNames)
            ->values()
            ->all();

        $unused = collect($unusedNames)
            ->mapWithKeys(fn (string $name): array => [$name => $available[$name]])
            ->all();

        $usedKnown = collect($usedNames)
            ->intersect($availableNames)
            ->values()
            ->mapWithKeys(fn (string $name): array => [$name => $used[$name]])
            ->all();

        $usedUnknown = collect($usedNames)
            ->diff($availableNames)
            ->values()
            ->mapWithKeys(fn (string $name): array => [$name => $used[$name]])
            ->all();

        return [
            'counts' => [
                'available' => count($available),
                'used' => count($usedKnown),
                'unused' => count($unused),
                'used_unknown' => count($usedUnknown),
            ],
            'available' => $available,
            'used' => $usedKnown,
            'unused' => $unused,
            'used_unknown' => $usedUnknown,
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function previewPayload(array $payload, int $limit): array
    {
        return [
            'generated_at' => $payload['generated_at'],
            'scan' => $payload['scan'],
            'components' => $payload['components'],
            'native' => [
                'counts' => $payload['native']['counts'],
                'top_used' => $this->topUsed($payload['native']['used'], $limit),
                'unused' => array_slice($payload['native']['unused'], 0, $limit),
                'unknown' => array_slice($payload['native']['unknown'], 0, $limit, true),
            ],
            'flux' => [
                'counts' => $payload['flux']['counts'],
                'top_used' => $this->topUsed($payload['flux']['used'], $limit),
                'unused' => array_slice($payload['flux']['unused'], 0, $limit, true),
                'used_unknown' => array_slice($payload['flux']['used_unknown'], 0, $limit, true),
            ],
            'custom' => [
                'counts' => $payload['custom']['counts'],
                'top_used' => $this->topUsed($payload['custom']['used'], $limit),
                'unused' => array_slice($payload['custom']['unused'], 0, $limit, true),
                'used_unknown' => array_slice($payload['custom']['used_unknown'], 0, $limit, true),
            ],
            'includes' => [
                'counts' => $payload['includes']['counts'],
                'used' => array_slice($payload['includes']['used'], 0, $limit, true),
            ],
            'livewire' => [
                'counts' => $payload['livewire']['counts'],
                'used' => array_slice($payload['livewire']['used'], 0, $limit, true),
            ],
        ];
    }

    /**
     * @param  array<string, array<string, mixed>>  $used
     * @return array<string, array<string, mixed>>
     */
    private function topUsed(array $used, int $limit): array
    {
        uasort($used, function (array $left, array $right): int {
            return (int) ($right['count'] ?? 0) <=> (int) ($left['count'] ?? 0);
        });

        return array_slice($used, 0, $limit, true);
    }

    private function stripIgnoredContent(string $contents): string
    {
        $contents = preg_replace('/\{\{--.*?--\}\}/su', '', $contents) ?? $contents;
        $contents = preg_replace('/<!--.*?-->/su', '', $contents) ?? $contents;

        return $contents;
    }

    private function stripNonNativeHtmlContent(string $contents): string
    {
        $contents = preg_replace('/<svg\b[^>]*>.*?<\/svg>/isu', '', $contents) ?? $contents;
        $contents = preg_replace('/<script\b[^>]*>.*?<\/script>/isu', '', $contents) ?? $contents;
        $contents = preg_replace('/<style\b[^>]*>.*?<\/style>/isu', '', $contents) ?? $contents;

        $contents = preg_replace('/<\s*\/?\s*(?:x-|flux:|livewire:|ui-)[^>]*>/iu', '', $contents) ?? $contents;

        return $contents;
    }

    /**
     * @param  array<string, mixed>  $config
     */
    private function isExcluded(string $path, string $fileName, array $config): bool
    {
        foreach ($config['exclude_path_fragments'] ?? [] as $fragment) {
            if ($fragment !== '' && str_contains($path, (string) $fragment)) {
                return true;
            }
        }

        foreach ($config['exclude_file_name_fragments'] ?? [] as $fragment) {
            if ($fragment !== '' && str_contains($fileName, (string) $fragment)) {
                return true;
            }
        }

        return false;
    }

    private function lineForOffset(string $contents, int $offset): int
    {
        return substr_count(substr($contents, 0, $offset), "\n") + 1;
    }

    private function writeJson(string $path, array $payload): void
    {
        File::ensureDirectoryExists(dirname($path));

        file_put_contents(
            $path,
            json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE).PHP_EOL,
        );
    }

    /**
     * @param  array<int, string>  $paths
     * @return array<int, string>
     */
    private function normalizeConfiguredPaths(array $paths): array
    {
        return collect($paths)
            ->map(fn (string $path): string => $this->relativePath($path))
            ->values()
            ->all();
    }

    private function relativePath(string $path): string
    {
        return Str::of($path)
            ->replace('\\', '/')
            ->replace(Str::of(base_path())->replace('\\', '/')->toString().'/', '')
            ->toString();
    }

    private function logRunActivity(string $event, string $description, array $properties = []): void
    {
        try {
            $activity = activity('html')
                ->event($event);

            $activity
                ->withProperties(ConsoleActivityContext::merge($this, $properties))
                ->log($description);
        } catch (Throwable $exception) {
            $this->warn('Activity log write failed: '.$exception->getMessage());
        }
    }
}
