<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class AppDisplaySettings extends Settings
{
    public array $roleBadges;

    public static function group(): string
    {
        return 'app_display';
    }
}
