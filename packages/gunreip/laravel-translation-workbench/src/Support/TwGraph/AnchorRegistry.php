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

        foreach (['source', 'sourceType', 'sourceAnchor', 'direction', 'devCounterNext'] as $metadataKey) {
            if (isset($anchor[$metadataKey])) {
                $storedAnchor[$metadataKey] = (string) $anchor[$metadataKey];
            }
        }

        foreach (self::keyAliases($key) as $alias) {
            self::$anchors[$graphId][$alias] = $storedAnchor;
        }
    }

    /**
     * @return array<string, string>|null
     */
    public static function get(string $graphId, string $key): ?array
    {
        foreach (self::keyAliases($key) as $alias) {
            if (isset(self::$anchors[$graphId][$alias])) {
                return self::$anchors[$graphId][$alias];
            }
        }

        return null;
    }

    /**
     * @return list<string>
     */
    private static function keyAliases(string $key): array
    {
        $key = trim($key);
        $normalizedKey = ElementIdentifier::normalize($key);

        return array_values(array_unique(array_filter([
            $key,
            $normalizedKey,
        ], static fn (string $alias): bool => $alias !== '')));
    }
}
