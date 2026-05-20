<?php

// app/Settings/AppGeneralSettings.php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class AppGeneralSettings extends Settings
{
    public string $locale;

    /**
     * @var array<int, string>
     */
    public array $availableLocales;

    public static function group(): string
    {
        return 'app_general';
    }
}
