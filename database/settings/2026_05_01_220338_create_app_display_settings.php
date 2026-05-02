<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('app_display.roleBadges', [
            'Admin' => [
                'color' => 'red',
                'variant' => 'solid',
                'icon' => 'shield-check',
            ],
            'Super-Admin' => [
                'color' => 'purple',
                'variant' => 'solid',
                'icon' => 'crown',
            ],
            'User' => [
                'color' => 'zinc',
                'variant' => 'subtle',
                'icon' => 'user',
            ],
        ]);
    }
};
