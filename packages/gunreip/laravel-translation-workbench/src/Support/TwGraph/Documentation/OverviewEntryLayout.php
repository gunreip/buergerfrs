<?php

declare(strict_types=1);

namespace Gunreip\TranslationWorkbench\Support\TwGraph\Documentation;

use Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry;
use Gunreip\TranslationWorkbench\Support\TwGraph\Defaults;

/** Shared inheritance for rendering and validation; optional values are not copied into the data. */
final class OverviewEntryLayout
{
    /** Explicit color wins; otherwise continue the color carried by the actual anchor. */
    public static function connectedColor(string $graph, string $anchor, ?string $explicit = null): string
    {
        $source = AnchorRegistry::get($graph, $anchor);

        return Defaults::string($explicit, $source['color'] ?? null, 'zinc');
    }

    public static function resolve(array $defaults, array $entry): array
    {
        return array_replace_recursive($defaults, array_intersect_key($entry, $defaults));
    }

    public static function group(array $tree, array $entry): array
    {
        $defaults = $tree['levels']['subtabs'];
        $defaults['nodes'] = $tree['levels']['subsubtabs'];
        unset($defaults['nodes']['endCap']);
        if ($entry['children'] !== []) {
            $defaults['endCap'] = $tree['levels']['subsubtabs']['endCap'];
        }

        return self::resolve($defaults, $entry);
    }

    public static function node(array $defaults, array $parent, array $entry): array
    {
        unset($defaults['endCap']);

        return self::resolve(['color' => $parent['color'], 'direction' => $parent['direction'], ...$defaults], $entry);
    }

    /** Optional keys are valid only at existing entry paths and only for supported layout fields. */
    public static function inheritedKeys(array $base, array $path, array $entry): array
    {
        if (count($path) === 2 && $path[0] === 'tabs') {
            return $base['tabLayout'];
        }
        if (count($path) === 2 && $path[0] === 'merges') {
            // Schema only: absent per-extension dimensions keep the component's fallback.
            // Do not populate the resolved data with these type prototypes.
            $dimensions = [];
            for ($index = 1; $index <= $entry['extensionCount']; $index++) {
                $dimensions[$index] = '0rem';
            }

            return [
                'arcRadius' => '',
                'extensionStemLengths' => $dimensions,
                'extensionBridgeContinuations' => $dimensions,
                'extensionArcRadiuss' => $dimensions,
                'extensionColors' => array_fill_keys(array_keys($dimensions), ''),
            ];
        }
        if (count($path) === 3 && isset($base[$path[0]]['levels']) && $path[1] === 'children') {
            $layout = self::group($base[$path[0]], $entry);
            $layout['nodes'] += ['color' => '', 'direction' => $layout['direction']];

            return $layout;
        }
        if (count($path) === 5 && isset($base[$path[0]]['levels']) && $path[1] === 'children' && $path[3] === 'children') {
            $parent = self::group($base[$path[0]], $base[$path[0]]['children'][$path[2]]);

            return [...self::node($parent['nodes'], $parent, []), 'labelNodeEnd' => true];
        }

        return [];
    }
}
