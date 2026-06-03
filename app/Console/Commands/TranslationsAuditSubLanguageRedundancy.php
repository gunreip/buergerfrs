<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Detects redundant sub-language translation values (same as base locale).
 */
class TranslationsAuditSubLanguageRedundancy extends Command
{
    protected $signature = 'translations:audit-sub-language-redundancy
        {--locales= : Comma-separated sub-language locales override (e.g. de-at,de-ch)}
        {--dry-run : Show what would be changed without persisting}';

    protected $description = 'Mark sub-language values as base-duplicates when identical to their base locale values.';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $targetLocales = $this->targetSubLanguageLocales();

        if ($targetLocales === []) {
            $this->warn('No sub-language locales found. Nothing to audit.');

            return self::SUCCESS;
        }

        $subValues = $this->subLanguageValues($targetLocales);

        if ($subValues->isEmpty()) {
            $this->warn('No eligible sub-language translation values found.');

            return self::SUCCESS;
        }

        $baseValues = $this->baseLocaleValueMap($subValues);

        $setTrueIds = [];
        $resetToNullIds = [];
        $lockedFalseCount = 0;
        $unchangedTrueCount = 0;
        $unchangedNullCount = 0;

        foreach ($subValues as $row) {
            $baseLocale = $this->baseLocaleFromSubLocale($row->normalized_locale);

            if ($baseLocale === null) {
                continue;
            }

            $mapKey = $row->translation_key_id . '|' . $baseLocale;
            $baseValue = $baseValues[$mapKey] ?? null;
            $isDuplicate = is_string($baseValue) && $row->value === $baseValue;

            if ((bool) $row->is_base_duplicate === false && $row->is_base_duplicate !== null) {
                $lockedFalseCount++;

                continue;
            }

            if ($isDuplicate) {
                if ($row->is_base_duplicate === true) {
                    $unchangedTrueCount++;

                    continue;
                }

                $setTrueIds[] = (int) $row->id;

                continue;
            }

            if ($row->is_base_duplicate === true) {
                $resetToNullIds[] = (int) $row->id;

                continue;
            }

            $unchangedNullCount++;
        }

        if (! $dryRun) {
            if ($setTrueIds !== []) {
                DB::table('translation_values')
                    ->whereIn('id', $setTrueIds)
                    ->update(['is_base_duplicate' => true, 'updated_at' => now()]);
            }

            if ($resetToNullIds !== []) {
                DB::table('translation_values')
                    ->whereIn('id', $resetToNullIds)
                    ->update(['is_base_duplicate' => null, 'updated_at' => now()]);
            }
        }

        $this->components->info('Sub-language redundancy audit finished.');

        $this->table(
            ['Metric', 'Count'],
            [
                ['Target sub-language locales', count($targetLocales)],
                ['Checked values', $subValues->count()],
                ['Marked duplicate (set true)', count($setTrueIds)],
                ['Reset duplicate flag (true -> null)', count($resetToNullIds)],
                ['Locked overrides (false)', $lockedFalseCount],
                ['Already duplicate (true)', $unchangedTrueCount],
                ['Unchanged (null)', $unchangedNullCount],
            ],
        );

        if ($dryRun) {
            $this->warn('Dry run only: no database changes were written.');
        }

        return self::SUCCESS;
    }

    /**
     * @return array<int, string>
     */
    private function targetSubLanguageLocales(): array
    {
        $localesOption = trim((string) $this->option('locales'));

        if ($localesOption !== '') {
            return collect(explode(',', $localesOption))
                ->map(fn(string $locale): string => $this->normalizeLocale($locale))
                ->filter(fn(string $locale): bool => str_contains($locale, '-'))
                ->unique()
                ->sort()
                ->values()
                ->all();
        }

        return DB::table('translation_values')
            ->whereNotNull('locale')
            ->where('locale', '<>', '')
            ->whereRaw("LOWER(REPLACE(locale, '_', '-')) like ?", ['%-%'])
            ->distinct()
            ->pluck('locale')
            ->map(fn(string $locale): string => $this->normalizeLocale($locale))
            ->filter(fn(string $locale): bool => str_contains($locale, '-'))
            ->unique()
            ->sort()
            ->values()
            ->all();
    }

    /**
     * @param array<int, string> $targetLocales
     * @return Collection<int, object{id: int, translation_key_id: int, normalized_locale: string, value: string, is_base_duplicate: bool|null}>
     */
    private function subLanguageValues(array $targetLocales): Collection
    {
        return DB::table('translation_values')
            ->selectRaw("id, translation_key_id, LOWER(REPLACE(locale, '_', '-')) as normalized_locale, value, is_base_duplicate")
            ->whereIn(DB::raw("LOWER(REPLACE(locale, '_', '-'))"), $targetLocales)
            ->where('status', 'ok')
            ->whereNotNull('value')
            ->where('value', '<>', '')
            ->orderBy('translation_key_id')
            ->orderBy('locale')
            ->get();
    }

    /**
     * @param Collection<int, object{id: int, translation_key_id: int, normalized_locale: string, value: string, is_base_duplicate: bool|null}> $subValues
     * @return array<string, string>
     */
    private function baseLocaleValueMap(Collection $subValues): array
    {
        $keyIds = $subValues
            ->pluck('translation_key_id')
            ->unique()
            ->values()
            ->all();

        $baseLocales = $subValues
            ->pluck('normalized_locale')
            ->map(fn(string $locale): ?string => $this->baseLocaleFromSubLocale($locale))
            ->filter()
            ->unique()
            ->values()
            ->all();

        if ($keyIds === [] || $baseLocales === []) {
            return [];
        }

        $rows = DB::table('translation_values')
            ->selectRaw("translation_key_id, LOWER(REPLACE(locale, '_', '-')) as normalized_locale, value")
            ->whereIn('translation_key_id', $keyIds)
            ->whereIn(DB::raw("LOWER(REPLACE(locale, '_', '-'))"), $baseLocales)
            ->where('status', 'ok')
            ->whereNotNull('value')
            ->where('value', '<>', '')
            ->get();

        $map = [];

        foreach ($rows as $row) {
            $map[$row->translation_key_id . '|' . $row->normalized_locale] = (string) $row->value;
        }

        return $map;
    }

    private function normalizeLocale(string $locale): string
    {
        return strtolower(str_replace('_', '-', trim($locale)));
    }

    private function baseLocaleFromSubLocale(string $locale): ?string
    {
        $normalized = $this->normalizeLocale($locale);

        if (! str_contains($normalized, '-')) {
            return null;
        }

        return explode('-', $normalized, 2)[0] ?: null;
    }
}
