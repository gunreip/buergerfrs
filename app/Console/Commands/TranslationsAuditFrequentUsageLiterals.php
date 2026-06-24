<?php

// app/Console/Commands/TranslationsAuditFrequentUsageLiterals.php

// Frequent usage literals audit
//
// Zweck:
// Listet häufig verwendete Source-Language-Literals.
// Auch Werte mit nur einem TranslationKey werden gelistet, wenn usage_count >= min-usages ist.
// Geeignet um zu sehen, ob Begriffe wie Save, Close, Continue, ID usw. bereits zentralisiert sind.
//
// Standard:
// php artisan translations:audit-frequent-usage-literals
//
// Source-Locale explizit:
// php artisan translations:audit-frequent-usage-literals --source-locale=en
//
// Nur bestimmte Ziel-Languages im Report mit ausgeben:
// php artisan translations:audit-frequent-usage-literals --locales=en,de
// php artisan translations:audit-frequent-usage-literals --locales=en,de,de-at,de-ch
//
// Mindestanzahl Usages je Source-Literal:
// php artisan translations:audit-frequent-usage-literals --min-usages=2
// php artisan translations:audit-frequent-usage-literals --min-usages=3
// php artisan translations:audit-frequent-usage-literals --min-usages=5
//
// Mindestlänge des Source-Literals:
// php artisan translations:audit-frequent-usage-literals --min-length=2
// php artisan translations:audit-frequent-usage-literals --min-length=4
//
// Auch Source-Literals ohne Usage-Datensätze aufnehmen:
// php artisan translations:audit-frequent-usage-literals --include-zero-usage
//
// Kombiniert:
// php artisan translations:audit-frequent-usage-literals --source-locale=en --locales=en,de --min-usages=2 --min-length=2
// php artisan translations:audit-frequent-usage-literals --source-locale=en --locales=en,de --min-usages=5
// php artisan translations:audit-frequent-usage-literals --source-locale=en --locales=en,de --include-zero-usage
//
// Output:
// storage/audits/translations/frequent-usage-literals/summary.json
// storage/audits/translations/frequent-usage-literals/summary.preview.json
// storage/audits/translations/frequent-usage-literals/literals.json
// storage/audits/translations/frequent-usage-literals/literals.preview.json

namespace App\Console\Commands;

use App\Support\ActivityLog\ConsoleActivityContext;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Throwable;

/**
 * Audits frequently used source-language translation values.
 */
#[Signature('translations:audit-frequent-usage-literals
    {--source-locale=en : Source locale used for usage literal reporting}
    {--locales= : Comma-separated target locale list to include in the report, e.g. de,en}
    {--min-usages=2 : Minimum usage count for a source literal}
    {--min-length=2 : Minimum trimmed source literal length}
    {--include-zero-usage : Include source values without usage records}')]
#[Description('Audit frequently used source-language translation values and show whether they are already centralized.')]
class TranslationsAuditFrequentUsageLiterals extends Command
{
    private const PREVIEW_LIMIT = 20;

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $sourceLocale = self::normalizeLocale((string) $this->option('source-locale'));
        $locales = $this->targetLocales();
        $minUsages = max(1, (int) $this->option('min-usages'));
        $minLength = max(1, (int) $this->option('min-length'));
        $includeZeroUsage = (bool) $this->option('include-zero-usage');

        if ($sourceLocale === '') {
            $this->error('Source locale must not be empty.');

            return self::FAILURE;
        }

        $sourceRows = $this->sourceRows($sourceLocale);
        $valueRowsByKeyId = $this->valueRowsByKeyId($sourceRows, $locales);
        $usageRowsByKeyId = $this->usageRowsByKeyId($sourceRows);

        $literals = $this->buildLiterals(
            sourceRows: $sourceRows,
            usageRowsByKeyId: $usageRowsByKeyId,
            valueRowsByKeyId: $valueRowsByKeyId,
            minUsages: $minUsages,
            minLength: $minLength,
            includeZeroUsage: $includeZeroUsage,
        );

        $summary = [
            'generated_at' => now()->toIso8601String(),
            'root_path' => base_path(),
            'preview_limit' => self::PREVIEW_LIMIT,
            'options' => [
                'source_locale' => $sourceLocale,
                'locales' => $locales,
                'min_usages' => $minUsages,
                'min_length' => $minLength,
                'include_zero_usage' => $includeZeroUsage,
            ],
            'source_rows' => $sourceRows->count(),
            'reported_literals' => count($literals),
            'reported_translation_keys' => collect($literals)->sum('translation_key_count'),
            'reported_usages' => collect($literals)->sum('usage_count_total'),
            'reported_current_usages' => collect($literals)->sum('usage_count_current'),
            'reported_stale_usages' => collect($literals)->sum('usage_count_stale'),
            'output_directory' => $this->relativePath($this->auditDirectory()),
        ];

        $this->writeAuditFile('summary', $summary);
        $this->writeAuditFile('literals', $literals);

        $this->components->info('Frequent usage literal audit finished.');

        $this->table(
            ['Metric', 'Count'],
            [
                ['Source rows', $summary['source_rows']],
                ['Reported literals', $summary['reported_literals']],
                ['Reported translation keys', $summary['reported_translation_keys']],
                ['Reported usages', $summary['reported_usages']],
            ],
        );

        if ($literals === []) {
            $this->line('');
            $this->warn('No frequent usage literals found.');

            $this->logRunCompletedActivity($summary);

            return self::SUCCESS;
        }

        $this->line('');
        $this->table(
            ['Source', 'Source value', 'Keys', 'Usages', 'Current', 'Stale', 'UI candidate', 'UI keys'],
            collect($literals)
                ->take(self::PREVIEW_LIMIT)
                ->map(static fn (array $literal): array => [
                    $literal['locale'],
                    mb_strimwidth($literal['value'], 0, 44, '...'),
                    $literal['translation_key_count'],
                    $literal['usage_count_total'],
                    $literal['usage_count_current'],
                    $literal['usage_count_stale'],
                    $literal['already_has_ui_candidate'] ? 'yes' : 'no',
                    $literal['ui_keys'] === [] ? '—' : implode(', ', $literal['ui_keys']),
                ])
                ->values()
                ->all(),
        );

        $this->line('');
        $this->line('Audit files written to: '.$this->relativePath($this->auditDirectory()));

        $this->logRunCompletedActivity($summary);

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
                ->map(static fn (string $locale): string => self::normalizeLocale($locale))
                ->filter()
                ->unique()
                ->sort()
                ->values()
                ->all();
        }

        return DB::table('translation_values')
            ->whereNotNull('locale')
            ->where('locale', '<>', '')
            ->selectRaw("LOWER(REPLACE(locale, '_', '-')) as normalized_locale")
            ->distinct()
            ->pluck('normalized_locale')
            ->map(static fn (string $locale): string => self::normalizeLocale($locale))
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->all();
    }

    /**
     * @return Collection<int, object>
     */
    private function sourceRows(string $sourceLocale): Collection
    {
        return DB::table('translation_values as tv')
            ->join('translation_keys as tk', 'tk.id', '=', 'tv.translation_key_id')
            ->select([
                'tv.translation_key_id',
                'tv.locale',
                'tv.value',
                'tv.status as value_status',
                'tv.is_base_duplicate',
                'tk.key',
                'tk.namespace',
                'tk.group',
                'tk.status as key_status',
                'tk.classification',
                'tk.native_text',
            ])
            ->selectRaw("LOWER(REPLACE(tv.locale, '_', '-')) as normalized_locale")
            ->whereRaw("LOWER(REPLACE(tv.locale, '_', '-')) = ?", [$sourceLocale])
            ->where('tv.status', '=', 'ok')
            ->whereNotNull('tv.value')
            ->where('tv.value', '<>', '')
            ->where(function ($query): void {
                $query
                    ->whereNull('tv.is_base_duplicate')
                    ->orWhere('tv.is_base_duplicate', false);
            })
            ->where('tk.classification', '=', 'key')
            ->where(function ($query): void {
                $query
                    ->whereNull('tk.status')
                    ->orWhere('tk.status', '<>', 'obsolete');
            })
            ->orderBy('tv.value')
            ->orderBy('tk.key')
            ->get();
    }

    /**
     * @param  Collection<int, object>  $sourceRows
     * @param  array<int, string>  $locales
     * @return array<int, array<string, array<string, mixed>>>
     */
    private function valueRowsByKeyId(Collection $sourceRows, array $locales): array
    {
        $translationKeyIds = $sourceRows
            ->pluck('translation_key_id')
            ->map(static fn ($id): int => (int) $id)
            ->unique()
            ->values();

        if ($translationKeyIds->isEmpty()) {
            return [];
        }

        $query = DB::table('translation_values')
            ->select([
                'translation_key_id',
                'locale',
                'value',
                'status',
                'source',
                'is_base_duplicate',
            ])
            ->selectRaw("LOWER(REPLACE(locale, '_', '-')) as normalized_locale")
            ->whereIn('translation_key_id', $translationKeyIds->all())
            ->whereNotNull('locale')
            ->where('locale', '<>', '')
            ->orderByRaw("LOWER(REPLACE(locale, '_', '-')) asc");

        if ($locales !== []) {
            $query->whereIn(DB::raw("LOWER(REPLACE(locale, '_', '-'))"), $locales);
        }

        return $query
            ->get()
            ->groupBy('translation_key_id')
            ->map(static function (Collection $rows): array {
                return $rows
                    ->mapWithKeys(static fn (object $row): array => [
                        (string) $row->normalized_locale => [
                            'locale' => (string) $row->normalized_locale,
                            'value' => $row->value,
                            'status' => $row->status,
                            'source' => $row->source,
                            'is_base_duplicate' => $row->is_base_duplicate,
                        ],
                    ])
                    ->all();
            })
            ->all();
    }

    /**
     * @param  Collection<int, object>  $sourceRows
     * @return array<int, Collection<int, object>>
     */
    private function usageRowsByKeyId(Collection $sourceRows): array
    {
        $translationKeyIds = $sourceRows
            ->pluck('translation_key_id')
            ->map(static fn ($id): int => (int) $id)
            ->unique()
            ->values();

        if ($translationKeyIds->isEmpty()) {
            return [];
        }

        return DB::table('translation_usages')
            ->select([
                'translation_key_id',
                'file',
                'line',
                'function',
                'classification',
                'reason',
                'raw',
                'original_raw',
            ])
            ->whereIn('translation_key_id', $translationKeyIds->all())
            ->orderBy('file')
            ->orderBy('line')
            ->get()
            ->groupBy('translation_key_id')
            ->all();
    }

    /**
     * @param  Collection<int, object>  $sourceRows
     * @param  array<int, Collection<int, object>>  $usageRowsByKeyId
     * @param  array<int, array<string, array<string, mixed>>>  $valueRowsByKeyId
     * @return array<int, array<string, mixed>>
     */
    private function buildLiterals(
        Collection $sourceRows,
        array $usageRowsByKeyId,
        array $valueRowsByKeyId,
        int $minUsages,
        int $minLength,
        bool $includeZeroUsage,
    ): array {
        $groups = [];

        foreach ($sourceRows as $row) {
            $locale = self::normalizeLocale((string) ($row->normalized_locale ?? $row->locale ?? ''));
            $value = trim((string) ($row->value ?? ''));

            if ($locale === '' || $value === '') {
                continue;
            }

            if (mb_strlen($value) < $minLength) {
                continue;
            }

            $normalizedValue = $this->normalizeLiteralValue($value);

            if ($normalizedValue === '') {
                continue;
            }

            $translationKeyId = (int) $row->translation_key_id;
            $fullKey = $this->fullTranslationKey(
                group: $row->group ?? null,
                key: $row->key ?? null,
            );
            $isUiKey = $this->isUiTranslationKey(
                group: $row->group ?? null,
                key: $row->key ?? null,
            );

            $groupKey = $locale.'|'.$normalizedValue;

            $groups[$groupKey] ??= [
                'locale' => $locale,
                'value' => $value,
                'normalized_value' => $normalizedValue,
                'value_variants' => [],
                'keys' => [],
            ];

            $groups[$groupKey]['value_variants'][$value] = true;
            $groups[$groupKey]['keys'][$translationKeyId] ??= [
                'translation_key_id' => $translationKeyId,
                'key' => (string) ($row->key ?? ''),
                'full_key' => $fullKey,
                'namespace' => $row->namespace,
                'group' => $row->group,
                'status' => $row->key_status,
                'classification' => $row->classification,
                'native_text' => $row->native_text,
                'is_ui_key' => $isUiKey,
            ];
        }

        $literals = [];

        foreach ($groups as $group) {
            $keys = collect($group['keys'])
                ->map(function (array $key) use ($usageRowsByKeyId, $valueRowsByKeyId): array {
                    $usageRows = $usageRowsByKeyId[$key['translation_key_id']] ?? collect();

                    $usages = collect($usageRows)
                        ->map(static function (object $usage): array {
                            $isStale = (string) ($usage->reason ?? '') === 'stale_audit_usage_not_seen_in_latest_sync';

                            return [
                                'file' => $usage->file,
                                'line' => $usage->line === null ? null : (int) $usage->line,
                                'function' => $usage->function,
                                'classification' => $usage->classification,
                                'reason' => $usage->reason,
                                'is_stale' => $isStale,
                                'raw' => $usage->raw,
                                'original_raw' => $usage->original_raw,
                            ];
                        })
                        ->values()
                        ->all();

                    $usageCountTotal = count($usages);
                    $usageCountStale = collect($usages)
                        ->filter(static fn (array $usage): bool => (bool) $usage['is_stale'])
                        ->count();
                    $usageCountCurrent = $usageCountTotal - $usageCountStale;

                    return array_merge($key, [
                        'usage_count' => $usageCountTotal,
                        'usage_count_total' => $usageCountTotal,
                        'usage_count_current' => $usageCountCurrent,
                        'usage_count_stale' => $usageCountStale,
                        'has_stale_usages' => $usageCountStale > 0,
                        'values' => $valueRowsByKeyId[$key['translation_key_id']] ?? [],
                        'usages' => $usages,
                    ]);
                })
                ->sortBy([
                    ['is_ui_key', 'desc'],
                    ['usage_count', 'desc'],
                    ['full_key', 'asc'],
                ])
                ->values()
                ->all();

            $usageCountTotal = collect($keys)->sum('usage_count_total');
            $usageCountCurrent = collect($keys)->sum('usage_count_current');
            $usageCountStale = collect($keys)->sum('usage_count_stale');

            if ($usageCountTotal < $minUsages && ! ($includeZeroUsage && $usageCountTotal === 0)) {
                continue;
            }

            $uiKeys = collect($keys)
                ->filter(static fn (array $key): bool => (bool) $key['is_ui_key'])
                ->pluck('full_key')
                ->filter()
                ->unique()
                ->values()
                ->all();

            $literals[] = [
                'locale' => $group['locale'],
                'value' => $group['value'],
                'normalized_value' => $group['normalized_value'],
                'value_variants' => array_keys($group['value_variants']),
                'translation_key_count' => count($keys),
                'non_ui_translation_key_count' => collect($keys)
                    ->filter(static fn (array $key): bool => ! (bool) $key['is_ui_key'])
                    ->count(),
                'usage_count' => $usageCountTotal,
                'usage_count_total' => $usageCountTotal,
                'usage_count_current' => $usageCountCurrent,
                'usage_count_stale' => $usageCountStale,
                'has_stale_usages' => $usageCountStale > 0,
                'already_has_ui_candidate' => $uiKeys !== [],
                'ui_keys' => $uiKeys,
                'keys' => $keys,
            ];
        }

        usort(
            $literals,
            static fn (array $a, array $b): int => [
                -1 * $a['usage_count_total'],
                -1 * $a['translation_key_count'],
                $a['locale'],
                $a['normalized_value'],
            ] <=> [
                -1 * $b['usage_count_total'],
                -1 * $b['translation_key_count'],
                $b['locale'],
                $b['normalized_value'],
            ],
        );

        return $literals;
    }

    private function writeAuditFile(string $name, array $payload): void
    {
        File::ensureDirectoryExists($this->auditDirectory());

        File::put(
            $this->auditDirectory().'/'.$name.'.json',
            json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES).PHP_EOL,
        );

        $previewPayload = array_is_list($payload)
            ? array_slice($payload, 0, self::PREVIEW_LIMIT)
            : $payload;

        File::put(
            $this->auditDirectory().'/'.$name.'.preview.json',
            json_encode($previewPayload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES).PHP_EOL,
        );
    }

    private function auditDirectory(): string
    {
        return storage_path('audits/translations/frequent-usage-literals');
    }

    private function normalizeLiteralValue(string $value): string
    {
        $normalized = preg_replace('/\s+/u', ' ', trim($value));

        return mb_strtolower((string) $normalized);
    }

    private static function normalizeLocale(string $locale): string
    {
        return strtolower(str_replace('_', '-', trim($locale)));
    }

    private function fullTranslationKey(mixed $group, mixed $key): ?string
    {
        $group = trim((string) $group);
        $key = trim((string) $key);

        if ($key === '') {
            return null;
        }

        if ($this->looksLikeFullTranslationKey($key)) {
            return $key;
        }

        if ($group === '') {
            return $key;
        }

        if ($key === $group || str_starts_with($key, $group.'.')) {
            return $key;
        }

        return $group.'.'.$key;
    }

    private function looksLikeFullTranslationKey(string $key): bool
    {
        return str_starts_with($key, 'ui.')
            || str_starts_with($key, 'admin.')
            || str_starts_with($key, 'auth.')
            || str_starts_with($key, 'pagination.')
            || str_starts_with($key, 'passwords.')
            || str_starts_with($key, 'validation.');
    }

    private function isUiTranslationKey(mixed $group, mixed $key): bool
    {
        $group = mb_strtolower(trim((string) $group));
        $key = mb_strtolower(trim((string) $key));
        $fullKey = mb_strtolower((string) $this->fullTranslationKey($group, $key));

        return $group === 'ui'
            || $key === 'ui'
            || str_starts_with($key, 'ui.')
            || str_starts_with($fullKey, 'ui.');
    }

    private function relativePath(string $path): string
    {
        return str_replace(base_path().DIRECTORY_SEPARATOR, '', $path);
    }

    /**
     * @param  array<string, mixed>  $summary
     */
    private function logRunCompletedActivity(array $summary): void
    {
        try {
            activity('translations')
                ->event('translations.frequent_usage_literals.audit.completed')
                ->withProperties(ConsoleActivityContext::merge($this, [
                    'summary' => $summary,
                ]))
                ->log('Translation frequent usage literals audit completed');
        } catch (Throwable $exception) {
            $this->warn('Activity log write failed: '.$exception->getMessage());
        }
    }
}
