<?php

declare(strict_types=1);

namespace Gunreip\TranslationWorkbench\Support\TwGraph\DataDriven\TimelineChainGraphData;

use App\Settings\AppGeneralSettings;
use App\Support\Locale\LocaleCode;

final class LocaleResolver
{
    public static function activeTargetMainLocale(): string
    {
        $configuredLocale = '';

        if (class_exists(AppGeneralSettings::class)) {
            $configuredLocale = LocaleCode::normalize((string) (app(AppGeneralSettings::class)->locale ?? ''));
        }

        $configuredLocale = $configuredLocale !== ''
            ? $configuredLocale
            : LocaleCode::normalize((string) app()->getLocale());
        $activeLanguage = (string) (LocaleCode::parts($configuredLocale)['language'] ?? $configuredLocale);

        return $activeLanguage !== '' ? $activeLanguage : $configuredLocale;
    }
}
