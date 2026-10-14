<?php

namespace Gunreip\TranslationWorkbench\Foundation;

use App\Models\Locale;
use App\Settings\AppGeneralSettings;
use App\Support\Locale\LocaleCode;

/** The existing source, main target and active variant selection shared by UI and scalar edits. */
class TranslationWorkbenchEditorLocales
{
    public function resolve(): array
    {
        $configuredLocale = LocaleCode::normalize((string) (app(AppGeneralSettings::class)->locale ?? app()->getLocale()));
        $configuredLocale = $configuredLocale !== '' ? $configuredLocale : 'de';
        $activeLanguage = (string) (LocaleCode::parts($configuredLocale)['language'] ?? $configuredLocale);
        $activeLocale = $activeLanguage !== '' ? $activeLanguage : $configuredLocale;
        $activeSubLocales = Locale::query()
            ->where('is_active', true)
            ->ordered()
            ->get(['code', 'normalized_code'])
            ->map(static fn (Locale $locale): string => LocaleCode::normalize((string) ($locale->normalized_code ?: $locale->code)))
            ->filter(static fn (string $locale): bool => $locale !== '')
            ->filter(static function (string $locale) use ($activeLanguage): bool {
                return (string) (LocaleCode::parts($locale)['language'] ?? '') === $activeLanguage
                    && $locale !== $activeLanguage;
            })
            ->unique()
            ->values()
            ->all();

        return [
            'source' => (string) config('translation-workbench.source_locale', 'en'),
            'active' => $activeLocale,
            'sub' => $activeSubLocales,
        ];
    }
}
