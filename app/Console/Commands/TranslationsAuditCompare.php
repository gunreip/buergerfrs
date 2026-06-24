<?php

// app/Console/Commands/TranslationsAuditCompare.php

namespace App\Console\Commands;

use App\Support\ActivityLog\ConsoleActivityContext;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
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
        $localeScopedDefinitions = $this->buildLocaleScopedDefinitions($langKeys);
        $exportableDefinitions = $this->buildExportableDefinitions();

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

        $fileObsolete = [];

        foreach ($localeScopedDefinitions as $scopedKey => $definition) {
            if (isset($exportableDefinitions[$scopedKey])) {
                continue;
            }

            $fileObsolete[] = [
                'locale' => $definition['locale'],
                'group' => $definition['group'],
                'key' => $definition['key'],
                'full_key' => $definition['full_key'],
                'defined_files' => $definition['files'],
                'reason' => 'present_in_lang_files_but_not_exportable_from_current_db_state',
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
            'file_obsolete_entries' => count($fileObsolete),
            'ok_keys' => count($ok),
            'native_texts' => count($native),
            'dynamic_keys' => count($dynamic),
            'invalid_code_calls' => count($codeInvalid),
            'invalid_lang_files' => count($langInvalid),
        ];

        $this->writeAuditFile('summary', $summary);
        $this->writeAuditFile('missing', $missing);
        $this->writeAuditFile('obsolete', $obsolete);
        $this->writeAuditFile('file-obsolete', $fileObsolete);
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
                ['File-obsolete entries', $summary['file_obsolete_entries']],
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
     * Build locale-scoped language definitions from lang audit keys.
     *
     * @return array<string, array{locale:string, group:?string, key:string, full_key:string, files:array<int, string>}>
     */
    private function buildLocaleScopedDefinitions(array $langKeys): array
    {
        $definitions = [];

        foreach ($langKeys as $entry) {
            $locale = trim((string) ($entry['locale'] ?? ''));
            $fullKey = trim((string) ($entry['full_key'] ?? ''));

            if ($locale === '' || $fullKey === '') {
                continue;
            }

            $scopedKey = $locale.'::'.$fullKey;

            $definitions[$scopedKey] ??= [
                'locale' => $locale,
                'group' => $entry['group'] !== null ? (string) $entry['group'] : null,
                'key' => (string) ($entry['key'] ?? ''),
                'full_key' => $fullKey,
                'files' => [],
            ];

            if (($entry['file'] ?? null) !== null) {
                $definitions[$scopedKey]['files'][] = (string) $entry['file'];
            }
        }

        foreach ($definitions as $scopedKey => $definition) {
            $definitions[$scopedKey]['files'] = array_values(array_unique($definition['files']));
            sort($definitions[$scopedKey]['files']);
        }

        ksort($definitions);

        return $definitions;
    }

    /**
     * Build locale-scoped definitions from the current DB export rules.
     *
     * @return array<string, array{locale:string, group:?string, key:string, full_key:string}>
     */
    private function buildExportableDefinitions(): array
    {
        $definitions = [];

        $rows = DB::table('translation_values')
            ->select([
                'translation_values.locale',
                'translation_keys.group',
                'translation_keys.key',
            ])
            ->join('translation_keys', 'translation_keys.id', '=', 'translation_values.translation_key_id')
            ->whereNotNull('translation_values.locale')
            ->where('translation_values.locale', '<>', '')
            ->where('translation_values.status', '=', 'ok')
            ->whereNotNull('translation_values.value')
            ->where('translation_values.value', '<>', '')
            ->where(function ($query): void {
                $query
                    ->whereNull('translation_values.is_base_duplicate')
                    ->orWhere('translation_values.is_base_duplicate', false);
            })
            ->whereNotNull('translation_keys.key')
            ->where('translation_keys.key', '<>', '')
            ->where(function ($query): void {
                $query
                    ->whereNull('translation_keys.status')
                    ->orWhere('translation_keys.status', '<>', 'obsolete')
                    ->orWhere(function ($query): void {
                        $query
                            ->where('translation_keys.status', 'obsolete')
                            ->where(function ($query): void {
                                $query
                                    ->whereNull('translation_keys.workflow_status')
                                    ->orWhere('translation_keys.workflow_status', '<>', 'reviewed');
                            });
                    });
            })
            ->orderBy('translation_values.locale', 'asc')
            ->orderBy('translation_keys.group', 'asc')
            ->orderBy('translation_keys.key', 'asc')
            ->get();

        foreach ($rows as $row) {
            $locale = str_replace('_', '-', strtolower(trim((string) $row->locale)));
            $group = $row->group !== null ? trim((string) $row->group) : null;
            $key = trim((string) $row->key);

            if ($locale === '' || $key === '') {
                continue;
            }

            $normalizedKey = $this->normalizeFullKeyForExport($group, $key);
            $scopedKey = $locale.'::'.$normalizedKey;

            $definitions[$scopedKey] = [
                'locale' => $locale,
                'group' => $group !== '' ? $group : null,
                'key' => $key,
                'full_key' => $normalizedKey,
            ];
        }

        ksort($definitions);

        return $definitions;
    }

    private function normalizeFullKeyForExport(?string $group, string $key): string
    {
        $normalizedGroup = $group !== null ? trim($group) : null;
        $normalizedKey = trim($key);

        if ($normalizedGroup === null || $normalizedGroup === '') {
            return $normalizedKey;
        }

        if (str_contains($normalizedKey, '.')) {
            $firstSegment = explode('.', $normalizedKey, 2)[0] ?? null;

            if (is_string($firstSegment) && $firstSegment !== '' && $firstSegment !== $normalizedGroup) {
                $normalizedGroup = $firstSegment;
            }
        }

        $prefix = $normalizedGroup.'.';

        if (str_starts_with($normalizedKey, $prefix)) {
            return $normalizedKey;
        }

        return $prefix.$normalizedKey;
    }

    /**
     * Read an audit JSON file and return a list.
     *
     * @return array<int, array<string, mixed>>
     */
    private function readAuditList(string $section, string $name): array
    {
        $path = storage_path('audits/translations/'.$section.'/'.$name.'.json');

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

        $fullPath = $directory.DIRECTORY_SEPARATOR.$name.'.json';
        $previewPath = $directory.DIRECTORY_SEPARATOR.$name.'.preview.json';
        $fullContent = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE).PHP_EOL;
        $previewContent = json_encode($this->previewData($data), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE).PHP_EOL;

        File::put($fullPath, $fullContent);

        File::put($previewPath, $previewContent);
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

    /**
     * @param  array<string, mixed>  $summary
     */
    private function logRunCompletedActivity(array $summary): void
    {
        try {
            activity('translations')
                ->event('translations.audit.compare.completed')
                ->withProperties(ConsoleActivityContext::merge($this, [
                    'summary' => $summary,
                ]))
                ->log('Translation compare audit completed');
        } catch (Throwable $exception) {
            $this->warn('Activity log write failed for command run summary: '.$exception->getMessage());
        }
    }

    private function relativePath(string $path): string
    {
        return str_replace(base_path().DIRECTORY_SEPARATOR, '', $path);
    }
}
