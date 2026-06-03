<?php

// app/Livewire/Admin/TranslationStatistics.php

namespace App\Livewire\Admin;

use App\Settings\AppGeneralSettings;
use App\Support\Locale\LocaleCode;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

/**
 * Read-only statistics dashboard for translation coverage and key health.
 */
class TranslationStatistics extends Component
{
    public function render(): View
    {
        $targetLanguages = $this->resolveTargetLanguages();
        $targetLocales   = $targetLanguages->pluck('locale')->all();

        // ── Key statistics ──────────────────────────────────────────────────
        $totalKeys = DB::table('translation_keys')->count();

        $keysByStatus = DB::table('translation_keys')
            ->selectRaw('status, COUNT(*) as cnt')
            ->groupBy('status')
            ->pluck('cnt', 'status');

        $keysByClassification = DB::table('translation_keys')
            ->selectRaw('classification, COUNT(*) as cnt')
            ->groupBy('classification')
            ->pluck('cnt', 'classification');

        $recentlySyncedAt = DB::table('translation_keys')
            ->max('updated_at');

        // ── Per-language coverage ────────────────────────────────────────────
        // Count translation_values rows per locale: translated = value IS NOT NULL AND value != ''
        $valueCoverage = DB::table('translation_values')
            ->selectRaw("locale, COUNT(*) as row_count, COUNT(CASE WHEN value IS NOT NULL AND value <> '' THEN 1 END) as translated_count, COUNT(CASE WHEN reviewed_at IS NOT NULL THEN 1 END) as reviewed_count")
            ->whereIn('locale', $targetLocales)
            ->groupBy('locale')
            ->get()
            ->mapWithKeys(fn(object $row): array => [$row->locale => $row]);

        // ── Build per-language summary rows ─────────────────────────────────
        $languageStats = $targetLanguages->map(function (object $lang) use ($valueCoverage, $totalKeys): object {
            $cov             = $valueCoverage[$lang->locale] ?? null;
            $translatedCount = (int) ($cov?->translated_count ?? 0);
            $reviewedCount   = (int) ($cov?->reviewed_count ?? 0);
            $rowCount        = (int) ($cov?->row_count ?? 0);
            $missingCount    = max(0, $totalKeys - $translatedCount);
            $coveragePct     = $totalKeys > 0 ? round($translatedCount / $totalKeys * 100, 1) : 0.0;
            $reviewedPct     = $translatedCount > 0 ? round($reviewedCount / $translatedCount * 100, 1) : 0.0;

            return (object) [
                'locale'          => $lang->locale,
                'name'            => $lang->name,
                'native_name'     => $lang->native_name,
                'total_keys'      => $totalKeys,
                'row_count'       => $rowCount,
                'translated_count' => $translatedCount,
                'missing_count'   => $missingCount,
                'reviewed_count'  => $reviewedCount,
                'coverage_pct'    => $coveragePct,
                'reviewed_pct'    => $reviewedPct,
            ];
        });

        return view('components.admin.⚡translation-statistics', [
            'targetLanguages'       => $targetLanguages,
            'languageStats'         => $languageStats,
            'totalKeys'             => $totalKeys,
            'keysByStatus'          => $keysByStatus,
            'keysByClassification'  => $keysByClassification,
            'recentlySyncedAt'      => $recentlySyncedAt,
        ]);
    }

    /**
     * Resolve target languages (non-default / non-source) from app settings.
     */
    private function resolveTargetLanguages(): \Illuminate\Support\Collection
    {
        $appGeneralSettings = app(AppGeneralSettings::class);
        $defaultLocale      = LocaleCode::normalize((string) ($appGeneralSettings->locale ?? ''));

        $availableLocales = collect($appGeneralSettings->availableLocales ?? [])
            ->map(static fn(mixed $locale): string => is_string($locale) ? LocaleCode::normalize($locale) : '')
            ->filter(static fn(string $locale): bool => $locale !== '')
            ->values();

        if ($availableLocales->isEmpty()) {
            return collect();
        }

        $languageRows = DB::table('languages')
            ->where('is_active', true)
            ->whereRaw('COALESCE(iso639_1, iso639_3) IS NOT NULL')
            ->get([
                DB::raw('COALESCE(iso639_1, iso639_3) as code'),
                'name',
                'native_name',
            ]);

        $languageByCode = $languageRows->mapWithKeys(static function (object $row): array {
            $code = LocaleCode::normalize((string) ($row->code ?? ''));

            return $code !== '' ? [$code => $row] : [];
        });

        return $availableLocales
            ->map(static function (string $locale, int $index) use ($languageByCode, $defaultLocale): object {
                $languageRow  = $languageByCode->get($locale);
                $fallbackLabel = strtoupper($locale);

                return (object) [
                    'locale'          => $locale,
                    'name'            => (string) ($languageRow->name ?? $fallbackLabel),
                    'native_name'     => (string) ($languageRow->native_name ?? $fallbackLabel),
                    'is_default'      => $defaultLocale !== '' && $locale === $defaultLocale,
                    'sort_order'      => $index,
                ];
            })
            ->filter(static fn(object $lang): bool => ! $lang->is_default)
            ->values();
    }
}
