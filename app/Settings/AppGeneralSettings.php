<?php

// app/Settings/AppGeneralSettings.php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

/**
 * Persisted general application settings.
 *
 * Stored in the Spatie settings group `app_general`.
 */
class AppGeneralSettings extends Settings
{
    /**
     * Global default app locale (normalized locale code).
     */
    public string $locale;

    /**
     * @var array<int, string>
     */
    public array $availableLocales;

    /**
     * Primary locales that were explicitly added to the selection pool,
     * but are not necessarily active for the app UI.
     *
     * @var array<int, string>
     */
    public array $addedPrimaryLocales;

    /**
     * Settings group identifier.
     */
    public static function group(): string
    {
        return 'app_general';
    }
}
