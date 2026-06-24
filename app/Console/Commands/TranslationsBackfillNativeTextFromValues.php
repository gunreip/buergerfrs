<?php

// app/Console/Commands/TranslationsBackfillNativeTextFromValues.php

namespace App\Console\Commands;

use App\Support\ActivityLog\ConsoleActivityContext;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Backfills translation_keys.native_text using existing translation_values.
 */
class TranslationsBackfillNativeTextFromValues extends Command
{
    protected $signature = 'translations:backfill-native-text-from-values
        {--prefer-locales=en,de : Comma-separated locale priority list for selecting source values}
        {--limit=0 : Maximum number of keys to backfill (0 = no limit)}
        {--dry-run : Preview updates without writing to the database}';

    protected $description = 'Fill missing translation_keys.native_text from available translation_values entries.';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $limit = max(0, (int) $this->option('limit'));
        $preferredLocales = $this->preferredLocales();

        $query = DB::table('translation_keys')
            ->select(['id'])
            ->where(function ($q): void {
                $q->whereNull('native_text')
                    ->orWhere('native_text', '');
            })
            ->orderBy('id', 'asc');

        if ($limit > 0) {
            $query->limit($limit);
        }

        $targetIds = $query->pluck('id')
            ->map(static fn ($id): int => (int) $id)
            ->values();

        if ($targetIds->isEmpty()) {
            $this->warn('No translation keys with empty native_text found.');

            $this->logRunCompletedActivity([
                'dry_run' => $dryRun,
                'limit' => $limit,
                'preferred_locales' => $preferredLocales,
                'target_keys' => 0,
                'updated_keys' => 0,
                'skipped_keys' => 0,
            ]);

            return self::SUCCESS;
        }

        $valueRows = DB::table('translation_values')
            ->selectRaw("translation_key_id, LOWER(REPLACE(locale, '_', '-')) as normalized_locale, value, is_base_duplicate")
            ->whereIn('translation_key_id', $targetIds->all())
            ->where('status', 'ok')
            ->whereNotNull('value')
            ->where('value', '<>', '')
            ->get();

        $rowsByKey = $valueRows->groupBy('translation_key_id');

        $updates = [];

        foreach ($targetIds as $keyId) {
            $candidateRows = $rowsByKey->get($keyId, collect());
            $selected = $this->selectPreferredValue($candidateRows, $preferredLocales);

            if ($selected === null) {
                continue;
            }

            $updates[] = [
                'id' => $keyId,
                'native_text' => (string) $selected->value,
                'locale' => (string) $selected->normalized_locale,
            ];
        }

        if ($updates === []) {
            $this->warn('No suitable translation values found for empty native_text keys.');

            $this->logRunCompletedActivity([
                'dry_run' => $dryRun,
                'limit' => $limit,
                'preferred_locales' => $preferredLocales,
                'target_keys' => $targetIds->count(),
                'updated_keys' => 0,
                'skipped_keys' => $targetIds->count(),
            ]);

            return self::SUCCESS;
        }

        if (! $dryRun) {
            foreach ($updates as $update) {
                DB::table('translation_keys')
                    ->where('id', $update['id'])
                    ->update([
                        'native_text' => $update['native_text'],
                        'updated_at' => now(),
                    ]);
            }
        }

        $this->components->info('Native text backfill completed.');

        $this->table(
            ['Metric', 'Count'],
            [
                ['Target keys (empty native_text)', $targetIds->count()],
                ['Backfillable keys', count($updates)],
                ['Skipped keys (no values)', $targetIds->count() - count($updates)],
                ['Preferred locales', implode(', ', $preferredLocales)],
            ],
        );

        $preview = collect($updates)
            ->take(20)
            ->map(static fn (array $row): array => [
                'key_id' => $row['id'],
                'locale_used' => $row['locale'],
                'native_text' => mb_strimwidth($row['native_text'], 0, 80, '...'),
            ])
            ->values()
            ->all();

        if ($preview !== []) {
            $this->line('');
            $this->table(['Key ID', 'Locale used', 'Native text (preview)'], $preview);
        }

        if ($dryRun) {
            $this->warn('Dry run only: no database rows were updated.');
        }

        $this->logRunCompletedActivity([
            'dry_run' => $dryRun,
            'limit' => $limit,
            'preferred_locales' => $preferredLocales,
            'target_keys' => $targetIds->count(),
            'updated_keys' => $dryRun ? 0 : count($updates),
            'skipped_keys' => $targetIds->count() - count($updates),
        ]);

        return self::SUCCESS;
    }

    /**
     * @return array<int, string>
     */
    private function preferredLocales(): array
    {
        $raw = trim((string) $this->option('prefer-locales'));

        if ($raw === '') {
            return ['en', 'de'];
        }

        return collect(explode(',', $raw))
            ->map(fn (string $locale): string => $this->normalizeLocale($locale))
            ->filter(fn (string $locale): bool => $locale !== '')
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @param  Collection<int, object{translation_key_id:int, normalized_locale:string, value:string, is_base_duplicate:bool|null}>  $candidateRows
     */
    private function selectPreferredValue(Collection $candidateRows, array $preferredLocales): ?object
    {
        if ($candidateRows->isEmpty()) {
            return null;
        }

        return $candidateRows
            ->sortBy(function (object $row) use ($preferredLocales): string {
                $locale = $this->normalizeLocale((string) ($row->normalized_locale ?? ''));
                $preferredIndex = array_search($locale, $preferredLocales, true);
                $localeMain = str_contains($locale, '-') ? explode('-', $locale, 2)[0] : $locale;
                $mainIndex = array_search($localeMain, $preferredLocales, true);
                $duplicatePenalty = $row->is_base_duplicate === true ? 1 : 0;

                $rank = is_int($preferredIndex)
                    ? $preferredIndex
                    : (is_int($mainIndex) ? $mainIndex + 50 : 999);

                return str_pad((string) $rank, 4, '0', STR_PAD_LEFT)
                    .'|'.$duplicatePenalty
                    .'|'.(str_contains($locale, '-') ? '1' : '0')
                    .'|'.$locale;
            }, SORT_NATURAL | SORT_FLAG_CASE)
            ->first();
    }

    private function normalizeLocale(string $locale): string
    {
        return strtolower(str_replace('_', '-', trim($locale)));
    }

    /**
     * @param  array<string, mixed>  $summary
     */
    private function logRunCompletedActivity(array $summary): void
    {
        try {
            activity('translations')
                ->event('translations.native_text.backfill.completed')
                ->withProperties(ConsoleActivityContext::merge($this, [
                    'summary' => $summary,
                ]))
                ->log('Translation native_text backfill completed');
        } catch (Throwable $exception) {
            $this->warn('Activity log write failed: '.$exception->getMessage());
        }
    }
}
