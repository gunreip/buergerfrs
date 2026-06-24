<?php

// app/Livewire/Admin/TranslationStatistics.php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\InteractsWithUserSettings;
use App\Models\Locale;
use App\Settings\AppGeneralSettings;
use App\Support\Locale\LocaleCode;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

/**
 * Read-only statistics dashboard for translation coverage and key health.
 */
class TranslationStatistics extends Component
{
    use InteractsWithUserSettings;

    private const SUB_LANGUAGE_UI_STATE_SETTING_KEY = 'ui.pages.admin_translation_sub_languages';

    public function render(): View
    {
        $targetLanguages = $this->resolveTargetLanguages();
        $targetLocales = $targetLanguages->pluck('locale')->all();

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
            ->mapWithKeys(fn (object $row): array => [$row->locale => $row]);

        // ── Build per-language summary rows ─────────────────────────────────
        $languageStats = $targetLanguages->map(function (object $lang) use ($valueCoverage, $totalKeys): object {
            $cov = $valueCoverage[$lang->locale] ?? null;
            $translatedCount = (int) ($cov?->translated_count ?? 0);
            $reviewedCount = (int) ($cov?->reviewed_count ?? 0);
            $rowCount = (int) ($cov?->row_count ?? 0);
            $missingCount = max(0, $totalKeys - $translatedCount);
            $coveragePct = $totalKeys > 0 ? round($translatedCount / $totalKeys * 100, 1) : 0.0;
            $reviewedPct = $translatedCount > 0 ? round($reviewedCount / $translatedCount * 100, 1) : 0.0;

            return (object) [
                'locale' => $lang->locale,
                'main_locale' => $lang->main_locale ?? $lang->locale,
                'name' => $lang->name,
                'native_name' => $lang->native_name,
                'is_active' => (bool) ($lang->is_active ?? true),
                'is_current' => (bool) ($lang->is_current ?? false),
                'is_source' => (bool) ($lang->is_source ?? false),
                'is_sub_language' => (bool) ($lang->is_sub_language ?? false),
                'language_type' => (string) ($lang->language_type ?? 'main'),
                'sub_language_active_count' => (int) ($lang->sub_language_active_count ?? 0),
                'sub_language_possible_count' => (int) ($lang->sub_language_possible_count ?? 0),
                'sort_order' => (int) ($lang->sort_order ?? 0),
                'total_keys' => $totalKeys,
                'row_count' => $rowCount,
                'translated_count' => $translatedCount,
                'missing_count' => $missingCount,
                'reviewed_count' => $reviewedCount,
                'coverage_pct' => $coveragePct,
                'reviewed_pct' => $reviewedPct,
            ];
        });

        return view('components.admin.⚡translation-statistics', [
            'targetLanguages' => $targetLanguages,
            'languageStats' => $languageStats,
            'totalKeys' => $totalKeys,
            'keysByStatus' => $keysByStatus,
            'keysByClassification' => $keysByClassification,
            'recentlySyncedAt' => $recentlySyncedAt,
        ]);
    }

    /**
     * Resolve configured active main locales from app settings.
     */
    private function resolveTargetLanguages(): Collection
    {
        $appGeneralSettings = app(AppGeneralSettings::class);
        $currentLocale = LocaleCode::normalize((string) ($appGeneralSettings->locale ?? ''));
        $sourceLocale = 'en';

        $availableLocales = collect($appGeneralSettings->availableLocales ?? [])
            ->map(static fn (mixed $locale): string => is_string($locale) ? LocaleCode::normalize($locale) : '')
            ->filter(static fn (string $locale): bool => $locale !== '')
            ->unique()
            ->values();

        if ($availableLocales->isEmpty()) {
            return collect();
        }

        $subLanguageUiState = $this->userSetting(self::SUB_LANGUAGE_UI_STATE_SETTING_KEY, []);

        $selectedSubLanguageLocales = collect(is_array($subLanguageUiState)
            ? ($subLanguageUiState['selectedSubLanguageLocales'] ?? [])
            : [])
            ->filter(static fn (mixed $locale): bool => is_string($locale) && trim($locale) !== '')
            ->map(static fn (string $locale): string => LocaleCode::normalize($locale))
            ->filter(static fn (string $locale): bool => $locale !== '')
            ->unique()
            ->values();

        $activatedSubLanguageCountsByMainLocale = $selectedSubLanguageLocales
            ->map(static function (string $locale): ?string {
                $parts = LocaleCode::parts($locale);
                $language = (string) ($parts['language'] ?? '');

                return $language !== '' && $locale !== $language ? $language : null;
            })
            ->filter()
            ->countBy();

        $possibleSubLanguageCountsByMainLocale = Locale::query()
            ->where('is_active', true)
            ->ordered()
            ->get([
                'code',
                'normalized_code',
            ])
            ->map(static function (Locale $locale): ?string {
                $normalized = LocaleCode::normalize((string) ($locale->normalized_code ?: $locale->code));

                if ($normalized === '') {
                    return null;
                }

                $parts = LocaleCode::parts($normalized);
                $language = (string) ($parts['language'] ?? '');

                return $language !== '' && $normalized !== $language ? $language : null;
            })
            ->filter()
            ->countBy();

        $primaryLocales = $availableLocales
            ->filter(static function (string $locale): bool {
                $parts = LocaleCode::parts($locale);
                $language = (string) ($parts['language'] ?? '');

                return $language !== '' && $locale === $language;
            })
            ->values();

        if ($primaryLocales->isEmpty()) {
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

        return $primaryLocales
            ->map(static function (string $locale, int $index) use (
                $activatedSubLanguageCountsByMainLocale,
                $currentLocale,
                $languageByCode,
                $possibleSubLanguageCountsByMainLocale,
                $sourceLocale,
            ): object {
                $languageRow = $languageByCode->get($locale);
                $fallbackLabel = strtoupper($locale);

                return (object) [
                    'locale' => $locale,
                    'main_locale' => $locale,
                    'name' => (string) ($languageRow->name ?? $fallbackLabel),
                    'native_name' => (string) ($languageRow->native_name ?? $fallbackLabel),
                    'is_active' => true,
                    'is_current' => $currentLocale !== '' && $locale === $currentLocale,
                    'is_source' => $locale === $sourceLocale,
                    'is_sub_language' => false,
                    'language_type' => 'main',
                    'sub_language_active_count' => (int) $activatedSubLanguageCountsByMainLocale->get($locale, 0),
                    'sub_language_possible_count' => (int) $possibleSubLanguageCountsByMainLocale->get($locale, 0),
                    'sort_order' => $index,
                ];
            })
            ->values();
    }
}
