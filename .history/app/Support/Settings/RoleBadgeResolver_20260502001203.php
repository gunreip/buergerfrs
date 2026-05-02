<?php

namespace App\Support\Settings;

use App\Settings\AppDisplaySettings;

class RoleBadgeResolver
{
    private const DEFAULT_BADGE = [
        'color' => 'zinc',
        'variant' => 'subtle',
        'icon' => 'tag',
    ];

    private const ALLOWED_COLORS = [
        'zinc',
        'red',
        'orange',
        'amber',
        'yellow',
        'lime',
        'green',
        'emerald',
        'teal',
        'cyan',
        'sky',
        'blue',
        'indigo',
        'violet',
        'purple',
        'fuchsia',
        'pink',
        'rose',
    ];

    private const ALLOWED_VARIANTS = [
        'solid',
        'subtle',
        'outline',
        'pill',
    ];

    public function __construct(
        private readonly AppDisplaySettings $settings,
    ) {
        //
    }

    public function forRole(?string $roleName): array
    {
        $roleName = trim((string) $roleName);

        if ($roleName === '') {
            return self::DEFAULT_BADGE;
        }

        $badge = $this->settings->roleBadges[$roleName] ?? [];

        return [
            'color' => $this->resolveColor($badge['color'] ?? null),
            'variant' => $this->resolveVariant($badge['variant'] ?? null),
            'icon' => $this->resolveIcon($badge['icon'] ?? null),
        ];
    }

    private function resolveColor(?string $color): string
    {
        if ($color === null || $color === '') {
            return self::DEFAULT_BADGE['color'];
        }

        if (! in_array($color, self::ALLOWED_COLORS, true)) {
            return self::DEFAULT_BADGE['color'];
        }

        return $color;
    }

    private function resolveVariant(?string $variant): string
    {
        if ($variant === null || $variant === '') {
            return self::DEFAULT_BADGE['variant'];
        }

        if (! in_array($variant, self::ALLOWED_VARIANTS, true)) {
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

        return $icon;
    }
}
