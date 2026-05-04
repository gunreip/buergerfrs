<?php

namespace App\Support\Settings;

use App\Settings\AppDisplaySettings;
use App\Support\Icons\IconRegistry;

class RoleBadgeResolver
{
    private const DEFAULT_BADGE = [
        'color' => 'zinc',
        'variant' => 'subtle',
        'icon' => 'tag',
    ];

    public function __construct(
        private readonly AppDisplaySettings $settings,
        private readonly IconRegistry $iconRegistry,
    ) {
        //
    }

    public function forRole(?string $roleName): array
    {
        $roleName = trim((string) $roleName);

        if ($roleName === '') {
            return self::DEFAULT_BADGE;
        }

        return $this->resolveBadge($roleName);
    }

    public function withoutRole(): array
    {
        return $this->resolveBadge('__without_role__');
    }

    private function resolveBadge(string $key): array
    {
        $badge = $this->settings->roleBadges[$key] ?? [];

        return [
            'color' => $this->resolveColor($badge['color'] ?? null),
            'variant' => $this->resolveVariant($badge['variant'] ?? null),
            'icon' => $this->resolveIcon($badge['icon'] ?? null),
        ];
    }

    private function resolveColor(?string $color): string
    {
        $color = trim((string) $color);

        if ($color === '') {
            return self::DEFAULT_BADGE['color'];
        }

        if (! $this->iconRegistry->isValidBadgeColor($color, 'role_user_management')) {
            return self::DEFAULT_BADGE['color'];
        }

        return $color;
    }

    private function resolveVariant(?string $variant): string
    {
        $variant = trim((string) $variant);

        if ($variant === '') {
            return self::DEFAULT_BADGE['variant'];
        }

        if (! $this->iconRegistry->isValidBadgeVariant($variant, 'role_user_management')) {
            return self::DEFAULT_BADGE['variant'];
        }

        return $variant;
    }

    private function resolveIcon(?string $icon): string
    {
        $icon = trim((string) $icon);

        if ($icon === '') {
            return self::DEFAULT_BADGE['icon'];
        }

        $resolvedIcon = $this->iconRegistry->resolveRoleUserManagement($icon);

        return (string) ($resolvedIcon['name'] ?? self::DEFAULT_BADGE['icon']);
    }
}
