<?php

declare(strict_types=1);

namespace Gunreip\TranslationWorkbench\Support\TwGraph;

final class AnchorRegistry
{
    /**
     * @var array<string, array<string, array{x: string, y: string}>>
     */
    private static array $anchors = [];

    public static function forgetGraph(string $graphId): void
    {
        unset(self::$anchors[$graphId]);
    }

    /**
     * @param  array{x?: string, y?: string}  $anchor
     */
    public static function put(string $graphId, string $key, array $anchor): void
    {
        self::$anchors[$graphId][$key] = [
            'x' => (string) ($anchor['x'] ?? '0rem'),
            'y' => (string) ($anchor['y'] ?? '0rem'),
        ];
    }

    /**
     * @return array{x: string, y: string}|null
     */
    public static function get(string $graphId, string $key): ?array
    {
        return self::$anchors[$graphId][$key] ?? null;
    }
}
