<?php

// database/settings/2026_06_21_192144_add_added_primary_locales_to_app_general_settings.php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('app_general.addedPrimaryLocales', []);
    }
};
