<?php

// app/Console/Commands/TranslationsAuditCompare.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Throwable;

/**
 * Compares code-level translation usage with language-file definitions.
 */
class TranslationsAuditCompare extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'translations:audit-compare';

    /**
     * The console command description.
     */
    protected $description = 'Compare translation code audit and language-file audit results.';

    private const PREVIEW_LIMIT = 20;

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $codeKeys = $this->readAuditList('code', 'keys');
        $native = $this->readAuditList('code', 'native');
        $dynamic = $this->readAuditList('code', 'dynamic');
        $codeInvalid = $this->readAuditList('code', 'invalid');

        $langKeys = $this->readAuditList('lang', 'keys');
        $langInvalid = $this->readAuditList('lang', 'invalid');

        $usage = $this->buildUsage($codeKeys);
        $defined = $this->buildDefinedKeys($langKeys);

        $missing = [];
        $ok = [];

        foreach ($usage as $usedKey) {
            $fullKey = $usedKey['full_key'];

            if (! isset($defined[$fullKey])) {
                $missing[] = [
                    'full_key' => $fullKey,
                    'usage_count' => $usedKey['usage_count'],
                    'usages' => $usedKey['usages'],
                ];

                continue;
            }

            $ok[] = [
                'full_key' => $fullKey,
                'usage_count' => $usedKey['usage_count'],
                'defined_locales' => $defined[$fullKey]['locales'],
                'defined_files' => $defined[$fullKey]['files'],
                'usages' => $usedKey['usages'],
            ];
        }

        $usedKeyNames = collect($usage)
            ->pluck('full_key')
            ->flip()
            ->all();

        $obsolete = [];

        foreach ($defined as $fullKey => $definition) {
            if (isset($usedKeyNames[$fullKey])) {
                continue;
            }

            $obsolete[] = [
                'full_key' => $fullKey,
                'defined_locales' => $definition['locales'],
                'defined_files' => $definition['files'],
            ];
        }

        $invalid = [
            'code' => $codeInvalid,
            'lang' => $langInvalid,
        ];

        $summary = [
            'generated_at' => now()->toIso8601String(),
            'root_path' => base_path(),
            'preview_limit' => self::PREVIEW_LIMIT,
            'code_keys' => count($codeKeys),
            'unique_code_keys' => count($usage),
            'lang_keys' => count($langKeys),
            'unique_lang_keys' => count($defined),
            'missing_keys' => count($missing),
            'obsolete_keys' => count($obsolete),
            'ok_keys' => count($ok),
            'native_texts' => count($native),
            'dynamic_keys' => count($dynamic),
            'invalid_code_calls' => count($codeInvalid),
            'invalid_lang_files' => count($langInvalid),
        ];

        $this->writeAuditFile('summary', $summary);
        $this->writeAuditFile('missing', $missing);
        $this->writeAuditFile('obsolete', $obsolete);
        $this->writeAuditFile('ok', $ok);
        $this->writeAuditFile('usage', array_values($usage));
        $this->writeAuditFile('native', $native);
        $this->writeAuditFile('dynamic', $dynamic);
        $this->writeAuditFile('invalid', $invalid);

        $this->components->info('Translation compare audit finished.');

        $this->table(
            ['Metric', 'Count'],
            [
                ['Code keys', $summary['code_keys']],
                ['Unique code keys', $summary['unique_code_keys']],
                ['Lang keys', $summary['lang_keys']],
                ['Unique lang keys', $summary['unique_lang_keys']],
                ['Missing keys', $summary['missing_keys']],
                ['Obsolete keys', $summary['obsolete_keys']],
                ['OK keys', $summary['ok_keys']],
                ['Native texts', $summary['native_texts']],
                ['Dynamic keys', $summary['dynamic_keys']],
                ['Invalid code calls', $summary['invalid_code_calls']],
                ['Invalid lang files', $summary['invalid_lang_files']],
            ],
        );

        $this->logRunCompletedActivity($summary);

        return self::SUCCESS;
    }

    /**
     * Build grouped usage entries from proper code keys.
     *
     * @return array<string, array{full_key: string, usage_count: int, usages: array<int, array<string, mixed>>}>
     */
    private function buildUsage(array $codeKeys): array
    {
        $usage = [];

        foreach ($codeKeys as $entry) {
            $fullKey = (string) ($entry['value'] ?? '');

            if ($fullKey === '') {
                continue;
            }

            $usage[$fullKey] ??= [
                'full_key' => $fullKey,
                'usage_count' => 0,
                'usages' => [],
            ];

            $usage[$fullKey]['usage_count']++;
            $usage[$fullKey]['usages'][] = [
                'file' => $entry['file'] ?? null,
                'line' => $entry['line'] ?? null,
                'function' => $entry['function'] ?? null,
                'raw' => $entry['raw'] ?? null,
            ];
        }

        ksort($usage);

        return $usage;
    }

    /**
     * Build grouped language definitions from lang audit keys.
     *
     * @return array<string, array{full_key: string, locales: array<int, string>, files: array<int, string>}>
     */
    private function buildDefinedKeys(array $langKeys): array
    {
        $defined = [];

        foreach ($langKeys as $entry) {
            $fullKey = (string) ($entry['full_key'] ?? '');

            if ($fullKey === '') {
                continue;
            }

            $defined[$fullKey] ??= [
                'full_key' => $fullKey,
                'locales' => [],
                'files' => [],
            ];

            if (($entry['locale'] ?? null) !== null) {
                $defined[$fullKey]['locales'][] = (string) $entry['locale'];
            }

            if (($entry['file'] ?? null) !== null) {
                $defined[$fullKey]['files'][] = (string) $entry['file'];
            }
        }

        foreach ($defined as $fullKey => $entry) {
            $defined[$fullKey]['locales'] = array_values(array_unique($entry['locales']));
            $defined[$fullKey]['files'] = array_values(array_unique($entry['files']));

            sort($defined[$fullKey]['locales']);
            sort($defined[$fullKey]['files']);
        }

        ksort($defined);

        return $defined;
    }

    /**
     * Read an audit JSON file and return a list.
     *
     * @return array<int, array<string, mixed>>
     */
    private function readAuditList(string $section, string $name): array
    {
        $path = storage_path('audits/translations/' . $section . '/' . $name . '.json');

        if (! File::isFile($path)) {
            return [];
        }

        $payload = json_decode(File::get($path), true);

        if (! is_array($payload)) {
            return [];
        }

        return array_is_list($payload) ? $payload : [];
    }

    /**
     * Write full and preview audit files.
     */
    private function writeAuditFile(string $name, array $data): void
    {
        $directory = storage_path('audits/translations/compare');

        File::ensureDirectoryExists($directory);

        $fullPath = $directory . DIRECTORY_SEPARATOR . $name . '.json';
        $previewPath = $directory . DIRECTORY_SEPARATOR . $name . '.preview.json';
        $fullPathExisted = File::exists($fullPath);
        $previewPathExisted = File::exists($previewPath);
        $fullPreviousContent = $fullPathExisted ? (string) File::get($fullPath) : null;
        $previewPreviousContent = $previewPathExisted ? (string) File::get($previewPath) : null;

        $fullContent = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL;
        $previewContent = json_encode($this->previewData($data), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL;

        File::put($fullPath, $fullContent);

        File::put($previewPath, $previewContent);

        if (! $fullPathExisted) {
            $this->logCreatedFileActivity('translations.audit.compare.file_created', $fullPath);
        } elseif ($fullPreviousContent !== $fullContent) {
            $this->logUpdatedFileActivity('translations.audit.compare.file_updated', $fullPath);
        }

        if (! $previewPathExisted) {
            $this->logCreatedFileActivity('translations.audit.compare.preview_file_created', $previewPath);
        } elseif ($previewPreviousContent !== $previewContent) {
            $this->logUpdatedFileActivity('translations.audit.compare.preview_file_updated', $previewPath);
        }
    }

    /**
     * Build preview data with a limited number of entries.
     */
    private function previewData(array $data): array
    {
        if (array_is_list($data)) {
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

    private function logCreatedFileActivity(string $event, string $path): void
    {
        try {
            activity('translations')
                ->event($event)
                ->withProperties([
                    'path' => $this->relativePath($path),
                    'absolute_path' => $path,
                    'command' => $this->getName(),
                ])
                ->log('Translation audit file created');
        } catch (Throwable $exception) {
            $this->warn('Activity log write failed for file "' . $this->relativePath($path) . '": ' . $exception->getMessage());
        }
    }

    private function logUpdatedFileActivity(string $event, string $path): void
    {
        try {
            activity('translations')
                ->event($event)
                ->withProperties([
                    'path' => $this->relativePath($path),
                    'absolute_path' => $path,
                    'command' => $this->getName(),
                ])
                ->log('Translation audit file updated');
        } catch (Throwable $exception) {
            $this->warn('Activity log write failed for file "' . $this->relativePath($path) . '": ' . $exception->getMessage());
        }
    }

    /**
     * @param array<string, mixed> $summary
     */
    private function logRunCompletedActivity(array $summary): void
    {
        try {
            activity('translations')
                ->event('translations.audit.compare.completed')
                ->withProperties([
                    'command' => $this->getName(),
                    'summary' => $summary,
                ])
                ->log('Translation compare audit completed');
        } catch (Throwable $exception) {
            $this->warn('Activity log write failed for command run summary: ' . $exception->getMessage());
        }
    }

    private function relativePath(string $path): string
    {
        return str_replace(base_path() . DIRECTORY_SEPARATOR, '', $path);
    }
}
