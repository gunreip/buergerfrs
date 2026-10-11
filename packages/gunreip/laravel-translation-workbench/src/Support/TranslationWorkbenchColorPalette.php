<?php

namespace Gunreip\TranslationWorkbench\Support;

final class TranslationWorkbenchColorPalette
{
    public static function rgb(?string $color, ?string $default = null): ?string
    {
        return match ($color) {
            'zinc' => '113 113 122',
            'red' => '239 68 68',
            'orange' => '249 115 22',
            'amber' => '245 158 11',
            'yellow' => '234 179 8',
            'lime' => '132 204 22',
            'green' => '44 144 103',
            'emerald' => '16 185 129',
            'teal' => '20 184 166',
            'cyan' => '6 182 212',
            'sky' => '14 165 233',
            'blue' => '59 130 246',
            'indigo' => '99 102 241',
            'violet' => '139 92 246',
            'purple' => '168 85 247',
            'fuchsia' => '217 70 239',
            'pink' => '236 72 153',
            'rose' => '244 63 94',
            default => $default,
        };
    }

    public static function surfaceRgb(?string $color, ?string $default = null): ?string
    {
        $opacity = match ($color) {
            'zinc' => 15,
            'amber', 'yellow', 'lime' => 25,
            default => 20,
        };

        return self::badgeSurfaceChannels($color, $opacity, $default);
    }

    public static function darkSurfaceRgb(?string $color, ?string $default = null): ?string
    {
        return self::badgeSurfaceChannels($color, 40, $default);
    }

    /**
     * Flux badge surfaces use Tailwind's 400 shade with transparency.
     * Composite it over the graph's label-mask background so joining paths stay opaque.
     * Clamp Tailwind's wide-gamut channels before mixing, as badge compositing does in sRGB.
     * Relative RGB channels preserve the existing rgb(var(...) / alpha) rendering contract.
     */
    private static function badgeSurfaceChannels(?string $color, int $opacity, ?string $default): ?string
    {
        if (self::rgb($color) === null) {
            return $default;
        }

        return "from color-mix(in srgb, rgb(from var(--color-{$color}-400) clamp(0, r, 255) clamp(0, g, 255) clamp(0, b, 255)) {$opacity}%, var(--tw-graph-protocol-label-mask-bg)) r g b";
    }
}
