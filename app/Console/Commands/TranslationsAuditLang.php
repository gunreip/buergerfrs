<?php

// app/Console/Commands/TranslationsAuditLang.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Throwable;

class TranslationsAuditLang extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'translations:audit-lang';

    /**
     * The console command description.
     */
    protected $description = 'Audit application language files and write machine-readable reports.';

    private const PREVIEW_LIMIT = 20;

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $files = $this->scannableLangFiles();

        $keys = [];
        $locales = [];
        $invalid = [];

        foreach ($files as $path) {
            $relativePath = $this->relativePath($path);

            try {
                $entries = $this->extractKeysFromLangFile($path);

                foreach ($entries as $entry) {
                    $keys[] = [
                        'locale' => $entry['locale'],
                        'group' => $entry['group'],
                        'key' => $entry['key'],
                        'full_key' => $entry['full_key'],
                        'file' => $relativePath,
                    ];

                    $locales[$entry['locale']] ??= [
                        'locale' => $entry['locale'],
                        'files' => 0,
                        'keys' => 0,
                    ];

                    $locales[$entry['locale']]['keys']++;
                }

                $locale = $this->localeFromPath($path);
                if ($locale !== null) {
                    $locales[$locale] ??= [
                        'locale' => $locale,
                        'files' => 0,
                        'keys' => 0,
                    ];

                    $locales[$locale]['files']++;
                }
            } catch (Throwable $exception) {
                $invalid[] = [
                    'file' => $relativePath,
                    'error' => $exception->getMessage(),
                ];
            }
        }

        usort($keys, fn(array $a, array $b): int => [$a['locale'], $a['full_key'], $a['file']] <=> [$b['locale'], $b['full_key'], $b['file']]);

        $locales = array_values($locales);
        usort($locales, fn(array $a, array $b): int => $a['locale'] <=> $b['locale']);

        $duplicates = $this->findDuplicateKeys($keys);

        $summary = [
            'generated_at' => now()->toIso8601String(),
            'root_path' => base_path(),
            'preview_limit' => self::PREVIEW_LIMIT,
            'files_scanned' => $files->count(),
            'locales' => count($locales),
            'defined_keys' => count($keys),
            'duplicate_keys' => count($duplicates),
            'invalid_files' => count($invalid),
            'vendor_files_ignored' => true,
        ];

        $this->writeAuditFile('summary', $summary);
        $this->writeAuditFile('keys', $keys);
        $this->writeAuditFile('locales', $locales);
        $this->writeAuditFile('duplicates', $duplicates);
        $this->writeAuditFile('invalid', $invalid);

        $this->components->info('Translation language-file audit finished.');

        $this->table(
            ['Metric', 'Count'],
            [
                ['Files scanned', $summary['files_scanned']],
                ['Locales', $summary['locales']],
                ['Defined keys', $summary['defined_keys']],
                ['Duplicate keys', $summary['duplicate_keys']],
                ['Invalid files', $summary['invalid_files']],
            ],
        );

        return self::SUCCESS;
    }

    /**
     * Files that are scanned by this language-file audit.
     */
    private function scannableLangFiles(): Collection
    {
        $langPath = lang_path();

        if (! File::isDirectory($langPath)) {
            return collect();
        }

        return collect(File::allFiles($langPath))
            ->map(fn($file): ?string => $file->getRealPath() ?: null)
            ->filter(fn(?string $path): bool => $path !== null && File::isFile($path))
            ->filter(fn(string $path): bool => in_array(pathinfo($path, PATHINFO_EXTENSION), ['php', 'json'], true))
            ->reject(fn(string $path): bool => str_contains($this->relativePath($path), 'lang/vendor/'))
            ->reject(fn(string $path): bool => $this->isParkedFile($path))
            ->values();
    }

    /**
     * Determine whether the file is intentionally parked and should not be audited.
     */
    private function isParkedFile(string $path): bool
    {
        $filename = strtolower(pathinfo($path, PATHINFO_FILENAME));

        return str_contains($filename, 'xxx')
            || str_contains($filename, 'yyy')
            || str_contains($filename, 'zzz');
    }

    /**
     * Extract flattened translation keys from a PHP or JSON language file.
     *
     * @return array<int, array{locale: string, group: string|null, key: string, full_key: string}>
     */
    private function extractKeysFromLangFile(string $path): array
    {
        return match (pathinfo($path, PATHINFO_EXTENSION)) {
            'php' => $this->extractKeysFromPhpLangFile($path),
            'json' => $this->extractKeysFromJsonLangFile($path),
            default => [],
        };
    }

    /**
     * Extract flattened translation keys from a PHP language file.
     *
     * @return array<int, array{locale: string, group: string|null, key: string, full_key: string}>
     */
    private function extractKeysFromPhpLangFile(string $path): array
    {
        $locale = $this->localeFromPath($path);
        $group = $this->groupFromPath($path);

        if ($locale === null || $group === null) {
            return [];
        }

        $payload = require $path;

        if (! is_array($payload)) {
            throw new \RuntimeException('PHP language file must return an array.');
        }

        $flattened = $this->flattenArray($payload);

        return collect($flattened)
            ->keys()
            ->map(fn(string $key): array => [
                'locale' => $locale,
                'group' => $group,
                'key' => $key,
                'full_key' => $group . '.' . $key,
            ])
            ->values()
            ->all();
    }

    /**
     * Extract flattened translation keys from a JSON language file.
     *
     * @return array<int, array{locale: string, group: string|null, key: string, full_key: string}>
     */
    private function extractKeysFromJsonLangFile(string $path): array
    {
        $locale = $this->localeFromPath($path);

        if ($locale === null) {
            return [];
        }

        $payload = json_decode(File::get($path), true, flags: JSON_THROW_ON_ERROR);

        if (! is_array($payload)) {
            throw new \RuntimeException('JSON language file must decode to an object/array.');
        }

        return collect($payload)
            ->keys()
            ->map(fn(string $key): array => [
                'locale' => $locale,
                'group' => null,
                'key' => $key,
                'full_key' => $key,
            ])
            ->values()
            ->all();
    }

    /**
     * Infer locale from a language-file path.
     */
    private function localeFromPath(string $path): ?string
    {
        $relativePath = str($this->relativePath($path))
            ->replace('\\', '/')
            ->toString();

        if (preg_match('#^lang/([a-zA-Z0-9_-]+)/[^/]+\.(php|json)$#', $relativePath, $matches) === 1) {
            return str_replace('_', '-', $matches[1]);
        }

        if (preg_match('#^lang/([a-zA-Z0-9_-]+)\.json$#', $relativePath, $matches) === 1) {
            return str_replace('_', '-', $matches[1]);
        }

        return null;
    }

    /**
     * Infer group name from a PHP language-file path.
     */
    private function groupFromPath(string $path): ?string
    {
        $relativePath = str($this->relativePath($path))
            ->replace('\\', '/')
            ->toString();

        if (preg_match('#^lang/[a-zA-Z0-9_-]+/([^/]+)\.php$#', $relativePath, $matches) === 1) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Flatten nested arrays to dot notation.
     *
     * @return array<string, mixed>
     */
    private function flattenArray(array $items, string $prefix = ''): array
    {
        $result = [];

        foreach ($items as $key => $value) {
            $fullKey = $prefix === '' ? (string) $key : $prefix . '.' . $key;

            if (is_array($value)) {
                $result += $this->flattenArray($value, $fullKey);

                continue;
            }

            $result[$fullKey] = $value;
        }

        return $result;
    }

    /**
     * Find duplicate full keys per locale.
     *
     * @return array<int, array{locale: string, full_key: string, count: int, files: array<int, string>}>
     */
    private function findDuplicateKeys(array $keys): array
    {
        $grouped = [];

        foreach ($keys as $key) {
            $index = $key['locale'] . '|' . $key['full_key'];

            $grouped[$index] ??= [
                'locale' => $key['locale'],
                'full_key' => $key['full_key'],
                'count' => 0,
                'files' => [],
            ];

            $grouped[$index]['count']++;
            $grouped[$index]['files'][] = $key['file'];
        }

        return collect($grouped)
            ->filter(fn(array $entry): bool => $entry['count'] > 1)
            ->map(function (array $entry): array {
                $entry['files'] = array_values(array_unique($entry['files']));

                return $entry;
            })
            ->values()
            ->all();
    }

    /**
     * Write full and preview audit files.
     */
    private function writeAuditFile(string $name, array $data): void
    {
        $directory = storage_path('audits/translations/lang');

        File::ensureDirectoryExists($directory);

        File::put(
            $directory . DIRECTORY_SEPARATOR . $name . '.json',
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL,
        );

        File::put(
            $directory . DIRECTORY_SEPARATOR . $name . '.preview.json',
            json_encode($this->previewData($data), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL,
        );
    }

    /**
     * Build preview data with a limited number of entries.
     */
    private function previewData(array $data): array
    {
        if ($this->isList($data)) {
            return [
                'preview' => true,
                'preview_limit' => self::PREVIEW_LIMIT,
                'total' => count($data),
                'items' => array_slice($data, 0, self::PREVIEW_LIMIT),
            ];
        }

        return [
            'preview' => true,
            'preview_limit' => self::PREVIEW_LIMIT,
            'data' => $data,
        ];
    }

    /**
     * Determine whether the given array is a list.
     */
    private function isList(array $data): bool
    {
        return array_keys($data) === range(0, count($data) - 1);
    }

    /**
     * Convert an absolute path to a project-relative path.
     */
    private function relativePath(string $path): string
    {
        return str_replace(base_path() . DIRECTORY_SEPARATOR, '', $path);
    }
}
