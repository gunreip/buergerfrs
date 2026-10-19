<?php

// app/Console/Commands/TranslationsExportLangFiles.php

namespace App\Console\Commands;

use App\Models\TranslationLanguage;
use App\Support\ActivityLog\ConsoleActivityContext;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Throwable;

/**
 * Compares legacy translation records with lang files without publishing.
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
    protected $description = 'Compare legacy translation values with lang files (--dry-run only); publication belongs to Translation Workbench.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (! (bool) $this->option('dry-run')) {
            $this->error('Legacy export writes are disabled. No language files were written. Use --dry-run for comparison; publish reviewed Workbench values through translation-workbench:export-lang-files.');

            return self::FAILURE;
        }
        $locales = $this->targetLocales();
        $dryRun = (bool) $this->option('dry-run');

        if ($locales === []) {
            $this->warn('No target locales found. Nothing to export.');
            $this->logNoTargetLocalesActivity($dryRun);

            return self::SUCCESS;
        }

        // Plan every locale before writing: a conflict in the last file must not
        // leave earlier files exported from a different translation inventory.
        $plans = $this->exportPlans($locales);
        $conflicts = $this->workbenchConflicts($plans);
        if ($conflicts !== []) {
            $this->error('Export blocked: active Workbench translations would be removed or overwritten. No language files were written.');
            $this->table(['Locale', 'File', 'Translation key', 'Conflict'], $conflicts);
            $this->warn('Reconcile the TranslationList and Workbench inventories before exporting.');

            return self::FAILURE;
        }

        $rows = [];
        $createdDirectories = 0;
        $createdFiles = 0;
        $updatedFiles = 0;
        $unchangedFiles = 0;
        $writtenFiles = 0;

        foreach ($locales as $locale) {
            if (! File::isDirectory(lang_path($locale))) {
                $createdDirectories++;
            }
        }

        foreach ($plans as $plan) {
            $result = $this->previewTranslationFile($plan['path'], $plan['content']);
            $writtenFiles += $result['written'];
            $createdFiles += $result['created'];
            $updatedFiles += $result['updated'];
            $unchangedFiles += $result['unchanged'];
            $rows[] = [$plan['locale'], $this->relativePath($plan['path']), $result['status']];
        }

        $this->components->info('Legacy export comparison finished; publication belongs to Translation Workbench.');

        if ($rows !== []) {
            $this->table(['Locale', 'File', 'Status'], $rows);
        } else {
            $this->warn('No exportable translation values found for selected locales.');
        }

        $this->line('');
        $this->line('Directories that would be created: '.$createdDirectories);
        $this->line('Files that would be created: '.$createdFiles);
        $this->line('Files that would be updated: '.$updatedFiles);
        $this->line('Files unchanged: '.$unchangedFiles);

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
     * @param  array<int, string>  $locales
     * @return array<int, array{locale: string, namespace: ?string, path: string, payload: array, content: string}>
     */
    private function exportPlans(array $locales): array
    {
        $plans = [];
        foreach ($locales as $locale) {
            $values = $this->translationValuesForLocale($locale);
            foreach ($values->filter(fn (array $entry): bool => $entry['group'] !== null)->groupBy('group') as $group => $entries) {
                if (! is_string($group) || trim($group) === '') {
                    continue;
                }
                $payload = $this->buildPhpGroupPayload($group, $entries);
                $plans[] = [
                    'locale' => $locale, 'namespace' => $group,
                    'path' => lang_path($locale.'/'.$group.'.php'),
                    'payload' => $payload, 'content' => $this->renderPhpTranslationArray($payload),
                ];
            }
            $payload = $values->filter(fn (array $entry): bool => $entry['group'] === null && trim($entry['key']) !== '')
                ->mapWithKeys(fn (array $entry): array => [$entry['key'] => $entry['value']])->all();
            if ($payload !== []) {
                ksort($payload);
                $plans[] = [
                    'locale' => $locale, 'namespace' => null, 'path' => lang_path($locale.'.json'),
                    'payload' => $payload,
                    'content' => json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES).PHP_EOL,
                ];
            }
        }

        return $plans;
    }

    /**
     * Protect existing values owned by the other active translation inventory.
     * This preflight deliberately does not pick a winner or change either DB.
     *
     * @param  array<int, array{locale: string, namespace: ?string, path: string, payload: array, content: string}>  $plans
     * @return array<int, array<int, string>>
     */
    private function workbenchConflicts(array $plans): array
    {
        if (! Schema::hasTable('translation_workbench_lang_values')) {
            return [];
        }
        $owned = DB::table('translation_workbench_lang_values')->where('status', 'active')
            ->whereIn(DB::raw("LOWER(REPLACE(locale, '_', '-'))"), array_unique(array_column($plans, 'locale')))
            ->get(['locale', 'namespace', 'lang_key'])
            ->groupBy(fn (object $row): string => self::normalizeLocale($row->locale).'/'.$row->namespace);
        $conflicts = [];
        foreach ($plans as $plan) {
            // Workbench lang values are PHP namespace entries, not JSON literals.
            if ($plan['namespace'] === null || ! File::exists($plan['path'])) {
                continue;
            }
            $current = File::getRequire($plan['path']);
            if (! is_array($current)) {
                continue;
            }
            foreach ($owned->get($plan['locale'].'/'.$plan['namespace'], collect()) as $row) {
                $key = $row->lang_key;
                if (! Arr::has($current, $key)) {
                    continue;
                }
                $value = Arr::get($current, $key);
                $leaves = is_array($value) && $value !== [] ? Arr::dot($value, $key.'.') : [$key => $value];
                foreach ($leaves as $leaf => $oldValue) {
                    $exists = Arr::has($plan['payload'], $leaf);
                    if (! $exists || Arr::get($plan['payload'], $leaf) !== $oldValue) {
                        $conflicts[$plan['locale'].'/'.$plan['namespace'].'.'.$leaf] = [
                            $plan['locale'], $this->relativePath($plan['path']),
                            $plan['namespace'].'.'.$leaf, $exists ? 'would overwrite' : 'would remove',
                        ];
                    }
                }
            }
        }

        return array_values($conflicts);
    }

    /**
     * @return array<int, string>
     */
    private function targetLocales(): array
    {
        $localesOption = trim((string) $this->option('locales'));

        if ($localesOption !== '') {
            return collect(explode(',', $localesOption))
                ->map(static fn (string $locale): string => self::normalizeLocale($locale))
                ->filter()
                ->unique()
                ->sort()
                ->values()
                ->all();
        }

        $fromSettings = TranslationLanguage::query()
            ->where('is_enabled_for_translation', true)
            ->pluck('locale')
            ->map(static fn (string $locale): string => self::normalizeLocale($locale))
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
            ->map(static fn (string $locale): string => self::normalizeLocale($locale))
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
     * @param  Collection<int, array{group: string|null, key: string, value: string}>  $entries
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

        $prefix = $normalizedGroup.'.';

        if (str_starts_with($normalizedKey, $prefix)) {
            return substr($normalizedKey, strlen($prefix));
        }

        return $normalizedKey;
    }

    /**
     * @param  array<string, mixed>  $target
     */
    private function setNestedArrayValue(array &$target, string $dotKey, string $value): void
    {
        $segments = array_values(array_filter(explode('.', $dotKey), static fn (string $segment): bool => $segment !== ''));

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
     * @param  array<string, mixed>  $array
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
     * @param  array<string, mixed>  $payload
     */
    private function renderPhpTranslationArray(array $payload): string
    {
        return "<?php\n\nreturn ".$this->exportArray($payload, 0).";\n";
    }

    /**
     * @param  array<string, mixed>  $payload
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

            $line = $childIndent.$exportedKey.' => '.$exportedValue.',';

            if ($isArrayValue && $lines !== []) {
                $line = ''.PHP_EOL.$line;
            }

            $lines[] = $line;

            $previousWasArray = $isArrayValue;
        }

        return "[\n".implode("\n", $lines)."\n".$indent.']';
    }

    /**
     * @return array{status: string, created: int, updated: int, unchanged: int, written: int}
     */
    private function previewTranslationFile(
        string $path,
        string $content,
    ): array {
        $exists = File::exists($path);
        $current = $exists ? (string) File::get($path) : null;

        if (! $exists) {

            return [
                'status' => 'would_create',
                'created' => 1,
                'updated' => 0,
                'unchanged' => 0,
                'written' => 0,
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


        return [
            'status' => 'would_update',
            'created' => 0,
            'updated' => 1,
            'unchanged' => 0,
            'written' => 0,
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

    private function logNoTargetLocalesActivity(bool $dryRun): void
    {
        try {
            activity('translations')
                ->event('translations.lang.export.no_target_locales')
                ->withProperties(ConsoleActivityContext::merge($this, [
                    'options' => [
                        'locales' => (string) $this->option('locales'),
                        'dry_run' => $dryRun,
                    ],
                ]))
                ->log('No target locales found for translation export run');
        } catch (Throwable $exception) {
            $this->warn('Activity log write failed for no-target-locales event: '.$exception->getMessage());
        }
    }

    /**
     * @param  array<int, string>  $locales
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
                ->withProperties(ConsoleActivityContext::merge($this, [
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
                ]))
                ->log('Translation export run completed');
        } catch (Throwable $exception) {
            $this->warn('Activity log write failed for command run summary: '.$exception->getMessage());
        }
    }

    private function relativePath(string $path): string
    {
        return str_replace(base_path().DIRECTORY_SEPARATOR, '', $path);
    }
}
