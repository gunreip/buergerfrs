<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $roleBadges = $this->migrator->get('app_display.roleBadges');

        $roleBadges['__without_role__'] = [
            'color' => 'red',
            'variant' => 'subtle',
            'icon' => 'refresh-cw-off',
        ];

        $this->migrator->update('app_display.roleBadges', $roleBadges);
    }
};
