<?php

declare(strict_types=1);

namespace Gunreip\TranslationWorkbench\Support\TwGraph;

final class AnchorRegistry
{
    /**
     * @var array<string, array<string, array<string, string>>>
     */
    private static array $anchors = [];

    /**
     * @var array<string, array<string, array<string, string>>>
     */
    private static array $forcedNodes = [];

    public static function forgetGraph(string $graphId): void
    {
        unset(self::$anchors[$graphId]);
        unset(self::$forcedNodes[$graphId]);
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

        foreach (['source', 'sourceType', 'sourceAnchor', 'direction', 'devCounterNext', 'color', 'zIndex'] as $metadataKey) {
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
     * @param  array<string, mixed>  $metadata
     */
    public static function forceNode(string $graphId, string $key, array $metadata = []): void
    {
        $key = ElementIdentifier::normalize(trim($key));

        if ($key === '') {
            return;
        }

        self::$forcedNodes[$graphId][$key] = [
            'key' => $key,
            'reason' => (string) ($metadata['reason'] ?? 'forced-node'),
        ];
    }

    /**
     * @return list<array<string, string>>
     */
    public static function forcedNodes(string $graphId): array
    {
        $forcedNodes = [];

        foreach (self::$forcedNodes[$graphId] ?? [] as $forcedNode) {
            $key = $forcedNode['key'] ?? null;
            if ($key === null) {
                continue;
            }

            $anchor = self::get($graphId, $key);
            if ($anchor === null) {
                continue;
            }

            $forcedNodes[$key] = array_replace($anchor, $forcedNode);
        }

        return array_values($forcedNodes);
    }

    /**
     * @return list<string>
     */
    private static function keyAliases(string $key): array
    {
        $key = trim($key);
        $normalizedKey = ElementIdentifier::normalize($key);
        $strangKey = str_starts_with($normalizedKey, 'trunk.')
            ? 'strang.' . $normalizedKey
            : null;

        return array_values(array_unique(array_filter([
            $key,
            $normalizedKey,
            $strangKey,
        ], static fn (mixed $alias): bool => is_string($alias) && $alias !== '')));
    }
}
