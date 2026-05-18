<?php

// app/Console/Commands/SyncViewComponentTags.php

// php artisan views:sync-component-tags

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use SplFileInfo;

#[Signature('views:sync-component-tags')]
#[Description('Scan Blade views and write a reference of used Flux, custom and Livewire component tags.')]
class SyncViewComponentTags extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $config = config('html-audit.component_tag_scan', []);

        if (empty($config['include_prefixes']) || ! is_array($config['include_prefixes'])) {
            $this->error('Missing config: html-audit.component_tag_scan.include_prefixes');
            $this->line('Please configure config/html-audit.php before running views:sync-component-tags.');

            return self::FAILURE;
        }

        $paths = $this->configuredPaths($config);
        $previewLimit = (int) ($config['preview_limit'] ?? 20);

        $files = $this->files($paths, $config);

        $tags = [];
        $usage = [];

        foreach ($files as $file) {
            $relativePath = $this->relativePath($file->getPathname());
            $content = File::get($file->getPathname());

            foreach ($this->extractComponentTags($content, $config) as $tag) {
                $category = $this->categoryForTag($tag);
                $line = $this->lineForTag($content, $tag);

                $tags[$category][$tag] = $tag;
                $usage[$tag][] = [
                    'file' => $relativePath,
                    'line' => $line,
                ];
            }
        }

        $tags = $this->sortTags($tags);
        $allTags = $this->allTags($tags);

        $payload = [
            'generated_at' => now()->toIso8601String(),
            'paths' => array_map(fn(string $path): string => $this->relativePath($path), $paths),
            'files_scanned' => $files->count(),
            'counts' => [
                'total' => count($allTags),
                'flux' => count($tags['flux'] ?? []),
                'custom' => count($tags['custom'] ?? []),
                'livewire' => count($tags['livewire'] ?? []),
            ],
            'tags' => [
                'flux' => array_values($tags['flux'] ?? []),
                'custom' => array_values($tags['custom'] ?? []),
                'livewire' => array_values($tags['livewire'] ?? []),
            ],
            'all' => $allTags,
            'usage' => $this->sortUsage($usage),
        ];

        $previewPayload = [
            ...$payload,
            'tags' => [
                'flux' => array_slice($payload['tags']['flux'], 0, $previewLimit),
                'custom' => array_slice($payload['tags']['custom'], 0, $previewLimit),
                'livewire' => array_slice($payload['tags']['livewire'], 0, $previewLimit),
            ],
            'all' => array_slice($payload['all'], 0, $previewLimit),
            'usage' => $this->previewUsage($payload['usage'], $previewLimit),
        ];

        File::ensureDirectoryExists(storage_path('audits/html'));

        File::put(
            storage_path('audits/html/view-component-tags.json'),
            json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL,
        );

        File::put(
            storage_path('audits/html/view-component-tags-preview.json'),
            json_encode($previewPayload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL,
        );

        $this->line('View component tags synced.');
        $this->line('Files scanned: ' . $files->count());
        $this->line('Tags total: ' . $payload['counts']['total']);
        $this->line('Flux tags: ' . $payload['counts']['flux']);
        $this->line('Custom tags: ' . $payload['counts']['custom']);
        $this->line('Livewire tags: ' . $payload['counts']['livewire']);
        $this->line('Reference written: storage/audits/html/view-component-tags.json');
        $this->line('Preview written: storage/audits/html/view-component-tags-preview.json');

        return self::SUCCESS;
    }

    /**
     * @param  array<string, mixed>  $config
     * @return array<int, string>
     */
    private function configuredPaths(array $config): array
    {
        return collect($config['paths'] ?? [resource_path('views')])
            ->filter(fn(mixed $path): bool => is_string($path) && $path !== '')
            ->map(fn(string $path): string => $path)
            ->filter(fn(string $path): bool => File::isDirectory($path))
            ->values()
            ->all();
    }

    /**
     * @param  array<int, string>  $paths
     * @param  array<string, mixed>  $config
     */
    private function files(array $paths, array $config): \Illuminate\Support\Collection
    {
        return collect($paths)
            ->flatMap(fn(string $path): array => File::allFiles($path))
            ->filter(fn(SplFileInfo $file): bool => Str::endsWith($file->getFilename(), '.blade.php'))
            ->reject(fn(SplFileInfo $file): bool => $this->isExcluded($file, $config))
            ->values();
    }

    /**
     * @param  array<string, mixed>  $config
     */
    private function isExcluded(SplFileInfo $file, array $config): bool
    {
        $relativePath = $this->relativePath($file->getPathname());
        $filename = $file->getFilename();

        foreach (($config['exclude_path_fragments'] ?? []) as $fragment) {
            if (is_string($fragment) && $fragment !== '' && str_contains($relativePath, $fragment)) {
                return true;
            }
        }

        foreach (($config['exclude_file_name_fragments'] ?? []) as $fragment) {
            if (is_string($fragment) && $fragment !== '' && str_contains($filename, $fragment)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  array<string, mixed>  $config
     * @return array<int, string>
     */
    private function extractComponentTags(string $content, array $config): array
    {
        $content = $this->stripComments($content);

        preg_match_all(
            '/<\s*\/?\s*([a-z][a-z0-9:_\-\.]*)(?=[\s>\/])/iu',
            $content,
            $matches,
        );

        return collect($matches[1] ?? [])
            ->map(fn(string $tag): string => strtolower(trim($tag)))
            ->filter(fn(string $tag): bool => $this->isIncludedComponentTag($tag, $config))
            ->unique()
            ->sort()
            ->values()
            ->all();
    }

    /**
     * @param  array<string, mixed>  $config
     */
    private function isIncludedComponentTag(string $tag, array $config): bool
    {
        foreach (($config['exclude_prefixes'] ?? []) as $prefix) {
            if (is_string($prefix) && $prefix !== '' && Str::startsWith($tag, $prefix)) {
                return false;
            }
        }

        foreach (($config['include_prefixes'] ?? []) as $prefix) {
            if (is_string($prefix) && $prefix !== '' && Str::startsWith($tag, $prefix)) {
                return true;
            }
        }

        return false;
    }

    private function categoryForTag(string $tag): string
    {
        if (Str::startsWith($tag, 'flux:')) {
            return 'flux';
        }

        if (Str::startsWith($tag, 'livewire:')) {
            return 'livewire';
        }

        return 'custom';
    }

    private function lineForTag(string $content, string $tag): int
    {
        preg_match('/<\s*\/?\s*' . preg_quote($tag, '/') . '(?=[\s>\/])/iu', $content, $match, PREG_OFFSET_CAPTURE);

        if (! isset($match[0][1])) {
            return 1;
        }

        return substr_count(substr($content, 0, $match[0][1]), "\n") + 1;
    }

    /**
     * @param  array<string, array<string, string>>  $tags
     * @return array<string, array<int, string>>
     */
    private function sortTags(array $tags): array
    {
        foreach ($tags as $category => $categoryTags) {
            $categoryTags = array_values($categoryTags);
            sort($categoryTags, SORT_NATURAL);
            $tags[$category] = $categoryTags;
        }

        ksort($tags);

        return $tags;
    }

    /**
     * @param  array<string, array<int, string>>  $tags
     * @return array<int, string>
     */
    private function allTags(array $tags): array
    {
        $allTags = collect($tags)
            ->flatten()
            ->unique()
            ->sort()
            ->values()
            ->all();

        return array_values($allTags);
    }

    /**
     * @param  array<string, array<int, array{file: string, line: int}>>  $usage
     * @return array<string, array<int, array{file: string, line: int}>>
     */
    private function sortUsage(array $usage): array
    {
        ksort($usage, SORT_NATURAL);

        foreach ($usage as $tag => $items) {
            usort($items, fn(array $a, array $b): int => [$a['file'], $a['line']] <=> [$b['file'], $b['line']]);
            $usage[$tag] = $items;
        }

        return $usage;
    }

    /**
     * @param  array<string, array<int, array{file: string, line: int}>>  $usage
     * @return array<string, array<int, array{file: string, line: int}>>
     */
    private function previewUsage(array $usage, int $previewLimit): array
    {
        return collect($usage)
            ->take($previewLimit)
            ->map(fn(array $items): array => array_slice($items, 0, $previewLimit))
            ->all();
    }

    private function stripComments(string $content): string
    {
        $content = preg_replace('/{{--.*?--}}/s', '', $content) ?? $content;

        return preg_replace('/<!--.*?-->/s', '', $content) ?? $content;
    }

    private function relativePath(string $path): string
    {
        return str_replace(base_path() . DIRECTORY_SEPARATOR, '', $path);
    }
}
