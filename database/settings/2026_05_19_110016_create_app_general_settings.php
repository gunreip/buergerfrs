<?php

// database/settings/2026_05_19_110016_create_app_general_settings.php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('app_general.locale', 'de');

        $this->migrator->add('app_general.availableLocales', [
            'de',
            'en',
        ]);
    }
};
