<?php

declare(strict_types=1);

namespace Gunreip\TranslationWorkbench\Support\TwGraph;

final class AnchorRegistry
{
    /**
     * @var array<string, array<string, array<string, string>>>
     */
    private static array $anchors = [];

    public static function forgetGraph(string $graphId): void
    {
        unset(self::$anchors[$graphId]);
    }

    /**
     * @param  array<string, mixed>  $anchor
     */
    public static function put(string $graphId, string $key, array $anchor): void
    {
        $storedAnchor = [
            'x' => (string) ($anchor['x'] ?? '0rem'),
            'y' => (string) ($anchor['y'] ?? '0rem'),
        ];

        foreach (['source', 'sourceType', 'sourceAnchor', 'direction'] as $metadataKey) {
            if (isset($anchor[$metadataKey])) {
                $storedAnchor[$metadataKey] = (string) $anchor[$metadataKey];
            }
        }

        self::$anchors[$graphId][$key] = $storedAnchor;
    }

    /**
     * @return array<string, string>|null
     */
    public static function get(string $graphId, string $key): ?array
    {
        return self::$anchors[$graphId][$key] ?? null;
    }
}
