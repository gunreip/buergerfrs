<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->update(
            'app_display.roleBadges',
            function (array $roleBadges): array {
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
