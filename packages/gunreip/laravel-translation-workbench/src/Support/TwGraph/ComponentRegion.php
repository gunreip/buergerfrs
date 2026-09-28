<?php

declare(strict_types=1);

namespace Gunreip\TranslationWorkbench\Support\TwGraph;

use Gunreip\TranslationWorkbench\Support\TranslationWorkbenchColorPalette;

/** Render provenance only: no dimensions, anchor lookup or geometry overrides. */
final class ComponentRegion
{
    private static ?array $current = null;

    private static int $sequence = 0;

    public static function begin(string $component, CanvasDiagnostics $canvas): array
    {
        $previous = self::$current;
        $owns = $previous === null || $previous['canvas'] !== $canvas;
        if ($owns) {
            self::$current = ['token' => ++self::$sequence, 'component' => $component, 'canvas' => $canvas];
        }

        return ['previous' => $previous, 'owns' => $owns, 'region' => self::$current];
    }

    public static function current(): ?int
    {
        return self::$current['token'] ?? null;
    }

    public static function finish(array $frame, mixed $id, mixed $color, bool $showBox = true): ?array
    {
        $resolvedId = filled($id) ? (string) $id : (self::$current['resolvedId'] ?? '');
        self::$current = $frame['previous'];
        if (! $frame['owns']) {
            if ($resolvedId !== '') {
                self::$current['resolvedId'] = $resolvedId;
            }
            return null;
        }

        return [
            'token' => $frame['region']['token'],
            'component' => $frame['region']['component'],
            'id' => $resolvedId,
            'showBox' => $showBox,
            'colorRgb' => TranslationWorkbenchColorPalette::rgb($color ?: 'sky', '14 165 233'),
        ];
    }
}
