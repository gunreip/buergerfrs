<?php

// app/Console/Commands/TranslationsExportLangFiles.php

namespace App\Console\Commands;

use App\Models\TranslationLanguage;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Throwable;

/**
 * Exports translation values from database records into lang files.
 */
class TranslationsExportLangFiles extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'translations:export-lang-files
        {--locales= : Comma-separated locale list override (e.g. de,en,fr)}
        {--dry-run : Show what would be written without writing files}';

    /**
     * The console command description.
     */
    protected $description = 'Export translation values from database to lang/{locale}/*.php and lang/{locale}.json files.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $locales = $this->targetLocales();
        $dryRun = (bool) $this->option('dry-run');

        if ($locales === []) {
            $this->warn('No target locales found. Nothing to export.');
            $this->logNoTargetLocalesActivity($dryRun);

            return self::SUCCESS;
        }

        if (! $dryRun) {
            File::ensureDirectoryExists(lang_path());
        }

        $rows = [];
        $createdDirectories = 0;
        $createdFiles = 0;
        $updatedFiles = 0;
        $unchangedFiles = 0;
        $writtenFiles = 0;

        foreach ($locales as $locale) {
            $localeDirectory = lang_path($locale);
            $localeDirectoryExisted = File::isDirectory($localeDirectory);

            if (! $localeDirectoryExisted) {
                if (! $dryRun) {
                    File::ensureDirectoryExists($localeDirectory);
                    $this->logDirectoryCreatedActivity($locale, $localeDirectory);
                }

                $createdDirectories++;
            }

            $values = $this->translationValuesForLocale($locale);

            $grouped = $values
                ->filter(fn(array $entry): bool => $entry['group'] !== null)
                ->groupBy('group');

            foreach ($grouped as $group => $entries) {
                if (! is_string($group) || trim($group) === '') {
                    continue;
                }

                $payload = $this->buildPhpGroupPayload($group, $entries);
                $content = $this->renderPhpTranslationArray($payload);
                $path = $localeDirectory . DIRECTORY_SEPARATOR . $group . '.php';

                $result = $this->writeTranslationFile(
                    path: $path,
                    content: $content,
                    dryRun: $dryRun,
                    locale: $locale,
                    format: 'php',
                );

                $writtenFiles += $result['written'];
                $createdFiles += $result['created'];
                $updatedFiles += $result['updated'];
                $unchangedFiles += $result['unchanged'];

                $rows[] = [$locale, $this->relativePath($path), $result['status']];
            }

            $jsonEntries = $values
                ->filter(fn(array $entry): bool => $entry['group'] === null && trim($entry['key']) !== '')
                ->mapWithKeys(fn(array $entry): array => [$entry['key'] => $entry['value']])
                ->all();

            if ($jsonEntries !== []) {
                ksort($jsonEntries);

                $jsonContent = json_encode($jsonEntries, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL;
                $jsonPath = lang_path($locale . '.json');

                $result = $this->writeTranslationFile(
                    path: $jsonPath,
                    content: $jsonContent,
                    dryRun: $dryRun,
                    locale: $locale,
                    format: 'json',
                );

                $writtenFiles += $result['written'];
                $createdFiles += $result['created'];
                $updatedFiles += $result['updated'];
                $unchangedFiles += $result['unchanged'];

                $rows[] = [$locale, $this->relativePath($jsonPath), $result['status']];
            }
        }

        $this->components->info('Translation export finished.');

        if ($rows !== []) {
            $this->table(['Locale', 'File', 'Status'], $rows);
        } else {
            $this->warn('No exportable translation values found for selected locales.');
        }

        $this->line('');
        $this->line('Directories created: ' . $createdDirectories);
        $this->line('Files created: ' . $createdFiles);
        $this->line('Files updated: ' . $updatedFiles);
        $this->line('Files unchanged: ' . $unchangedFiles);

        if ($dryRun) {
            $this->warn('Dry run only: no files were written.');
        }

        $this->logRunCompletedActivity(
            locales: $locales,
            createdDirectories: $createdDirectories,
            createdFiles: $createdFiles,
            updatedFiles: $updatedFiles,
            unchangedFiles: $unchangedFiles,
            writtenFiles: $writtenFiles,
            dryRun: $dryRun,
            hadRows: $rows !== [],
        );

        return self::SUCCESS;
    }

    /**
     * @return array<int, string>
     */
    private function targetLocales(): array
    {
        $localesOption = trim((string) $this->option('locales'));

        if ($localesOption !== '') {
            return collect(explode(',', $localesOption))
                ->map(static fn(string $locale): string => self::normalizeLocale($locale))
                ->filter()
                ->unique()
                ->sort()
                ->values()
                ->all();
        }

        $fromSettings = TranslationLanguage::query()
            ->where('is_enabled_for_translation', true)
            ->pluck('locale')
            ->map(static fn(string $locale): string => self::normalizeLocale($locale))
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->all();

        $variantLocales = DB::table('translation_values')
            ->whereNotNull('locale')
            ->where('locale', '<>', '')
            ->whereRaw('locale like ?', ['%-%'])
            ->distinct()
            ->pluck('locale')
            ->map(static fn(string $locale): string => self::normalizeLocale($locale))
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->all();

        $locales = array_values(array_unique(array_merge($fromSettings, $variantLocales)));

        if ($locales !== []) {
            return $locales;
        }

        return ['de', 'en'];
    }

    /**
     * @return Collection<int, array{group: string|null, key: string, value: string}>
     */
    private function translationValuesForLocale(string $locale): Collection
    {
        return DB::table('translation_values')
            ->select([
                'translation_values.value',
                'translation_keys.group',
                'translation_keys.key',
            ])
            ->join('translation_keys', 'translation_keys.id', '=', 'translation_values.translation_key_id')
            ->whereRaw("LOWER(REPLACE(translation_values.locale, '_', '-')) = ?", [$locale])
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
            ->orderBy('translation_keys.group', 'asc')
            ->orderBy('translation_keys.key', 'asc')
            ->get()
            ->map(static function (object $value): array {
                $key = (string) $value->key;
                $group = $value->group !== null ? (string) $value->group : null;

                if (
                    $group !== null
                    && $group !== ''
                    && str_contains($key, '.')
                ) {
                    $firstSegment = explode('.', $key, 2)[0] ?? null;

                    if (is_string($firstSegment) && $firstSegment !== '' && $firstSegment !== $group) {
                        $group = $firstSegment;
                    }
                }

                return [
                    'group' => $group,
                    'key' => $key,
                    'value' => (string) $value->value,
                ];
            })
            ->values();
    }

    /**
     * @param Collection<int, array{group: string|null, key: string, value: string}> $entries
     * @return array<string, mixed>
     */
    private function buildPhpGroupPayload(string $group, Collection $entries): array
    {
        $result = [];

        foreach ($entries as $entry) {
            $relativeKey = $this->relativeGroupKey($group, $entry['key']);

            if ($relativeKey === '') {
                continue;
            }

            $this->setNestedArrayValue($result, $relativeKey, $entry['value']);
        }

        $this->sortRecursive($result);

        return $result;
    }

    private function relativeGroupKey(string $group, string $key): string
    {
        $normalizedGroup = trim($group);
        $normalizedKey = trim($key);

        if ($normalizedGroup === '') {
            return $normalizedKey;
        }

        $prefix = $normalizedGroup . '.';

        if (str_starts_with($normalizedKey, $prefix)) {
            return substr($normalizedKey, strlen($prefix));
        }

        return $normalizedKey;
    }

    /**
     * @param array<string, mixed> $target
     */
    private function setNestedArrayValue(array &$target, string $dotKey, string $value): void
    {
        $segments = array_values(array_filter(explode('.', $dotKey), static fn(string $segment): bool => $segment !== ''));

        if ($segments === []) {
            return;
        }

        $cursor = &$target;

        foreach ($segments as $index => $segment) {
            $isLast = $index === count($segments) - 1;

            if ($isLast) {
                $cursor[$segment] = $value;

                return;
            }

            if (! isset($cursor[$segment]) || ! is_array($cursor[$segment])) {
                $cursor[$segment] = [];
            }

            $cursor = &$cursor[$segment];
        }
    }

    /**
     * @param array<string, mixed> $array
     */
    private function sortRecursive(array &$array): void
    {
        ksort($array);

        foreach ($array as &$value) {
            if (is_array($value)) {
                $this->sortRecursive($value);
            }
        }
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function renderPhpTranslationArray(array $payload): string
    {
        return "<?php\n\nreturn " . $this->exportArray($payload, 0) . ";\n";
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function exportArray(array $payload, int $indentLevel): string
    {
        if ($payload === []) {
            return '[]';
        }

        $indent = str_repeat('    ', $indentLevel);
        $childIndent = str_repeat('    ', $indentLevel + 1);
        $lines = [];
        $previousWasArray = false;

        foreach ($payload as $key => $value) {
            $exportedKey = var_export($key, true);
            $isArrayValue = is_array($value);

            if ($isArrayValue) {
                $exportedValue = $this->exportArray($value, $indentLevel + 1);
            } else {
                $exportedValue = var_export($value, true);
            }

            $line = $childIndent . $exportedKey . ' => ' . $exportedValue . ',';

            if ($isArrayValue && $lines !== []) {
                $line = "" . PHP_EOL . $line;
            }

            $lines[] = $line;

            $previousWasArray = $isArrayValue;
        }

        return "[\n" . implode("\n", $lines) . "\n" . $indent . ']';
    }

    /**
     * @return array{status: string, created: int, updated: int, unchanged: int, written: int}
     */
    private function writeTranslationFile(
        string $path,
        string $content,
        bool $dryRun,
        string $locale,
        string $format,
    ): array {
        $exists = File::exists($path);
        $current = $exists ? (string) File::get($path) : null;

        if (! $exists) {
            if (! $dryRun) {
                File::put($path, $content);
                $this->logFileCreatedActivity($locale, $path, $format);
            }

            return [
                'status' => $dryRun ? 'would_create' : 'created',
                'created' => 1,
                'updated' => 0,
                'unchanged' => 0,
                'written' => 1,
            ];
        }

        if ($current === $content) {
            return [
                'status' => 'unchanged',
                'created' => 0,
                'updated' => 0,
                'unchanged' => 1,
                'written' => 0,
            ];
        }

        if (! $dryRun) {
            File::put($path, $content);
            $this->logFileUpdatedActivity($locale, $path, $format);
        }

        return [
            'status' => $dryRun ? 'would_update' : 'updated',
            'created' => 0,
            'updated' => 1,
            'unchanged' => 0,
            'written' => 1,
        ];
    }

    private static function normalizeLocale(string $locale): string
    {
        $normalized = strtolower(trim($locale));

        if ($normalized === '') {
            return '';
        }

        return str_replace('_', '-', $normalized);
    }

    private function logDirectoryCreatedActivity(string $locale, string $path): void
    {
        try {
            activity('translations')
                ->event('translations.lang.directory_created')
                ->withProperties([
                    'locale' => $locale,
                    'path' => $this->relativePath($path),
                    'absolute_path' => $path,
                    'command' => $this->getName(),
                ])
                ->log('Translation language directory created');
        } catch (Throwable $exception) {
            $this->warn('Activity log write failed for locale "' . $locale . '": ' . $exception->getMessage());
        }
    }

    private function logFileCreatedActivity(string $locale, string $path, string $format): void
    {
        try {
            activity('translations')
                ->event('translations.lang.file_created')
                ->withProperties([
                    'locale' => $locale,
                    'format' => $format,
                    'path' => $this->relativePath($path),
                    'absolute_path' => $path,
                    'command' => $this->getName(),
                ])
                ->log('Translation language file created');
        } catch (Throwable $exception) {
            $this->warn('Activity log write failed for file "' . $this->relativePath($path) . '": ' . $exception->getMessage());
        }
    }

    private function logFileUpdatedActivity(string $locale, string $path, string $format): void
    {
        try {
            activity('translations')
                ->event('translations.lang.file_updated')
                ->withProperties([
                    'locale' => $locale,
                    'format' => $format,
                    'path' => $this->relativePath($path),
                    'absolute_path' => $path,
                    'command' => $this->getName(),
                ])
                ->log('Translation language file updated');
        } catch (Throwable $exception) {
            $this->warn('Activity log write failed for file "' . $this->relativePath($path) . '": ' . $exception->getMessage());
        }
    }

    private function logNoTargetLocalesActivity(bool $dryRun): void
    {
        try {
            activity('translations')
                ->event('translations.lang.export.no_target_locales')
                ->withProperties([
                    'command' => $this->getName(),
                    'options' => [
                        'locales' => (string) $this->option('locales'),
                        'dry_run' => $dryRun,
                    ],
                ])
                ->log('No target locales found for translation export run');
        } catch (Throwable $exception) {
            $this->warn('Activity log write failed for no-target-locales event: ' . $exception->getMessage());
        }
    }

    /**
     * @param array<int, string> $locales
     */
    private function logRunCompletedActivity(
        array $locales,
        int $createdDirectories,
        int $createdFiles,
        int $updatedFiles,
        int $unchangedFiles,
        int $writtenFiles,
        bool $dryRun,
        bool $hadRows,
    ): void {
        try {
            activity('translations')
                ->event('translations.lang.export.completed')
                ->withProperties([
                    'command' => $this->getName(),
                    'summary' => [
                        'locales' => $locales,
                        'created_directories' => $createdDirectories,
                        'created_files' => $createdFiles,
                        'updated_files' => $updatedFiles,
                        'unchanged_files' => $unchangedFiles,
                        'written_files' => $writtenFiles,
                        'dry_run' => $dryRun,
                        'had_export_rows' => $hadRows,
                    ],
                ])
                ->log('Translation export run completed');
        } catch (Throwable $exception) {
            $this->warn('Activity log write failed for command run summary: ' . $exception->getMessage());
        }
    }

    private function relativePath(string $path): string
    {
        return str_replace(base_path() . DIRECTORY_SEPARATOR, '', $path);
    }
}
