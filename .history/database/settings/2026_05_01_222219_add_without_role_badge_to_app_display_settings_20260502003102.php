<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->update(
            'app_display.roleBadges',
            function ($roleBadges): array {
                $roleBadges = json_decode(json_encode($roleBadges), true);

                $roleBadges['__without_role__'] = [
                    'color' => 'red',
                    'variant' => 'subtle',
                    'icon' => 'refresh-cw-off',
                ];

                return $roleBadges;
            }
        );
    }
};
