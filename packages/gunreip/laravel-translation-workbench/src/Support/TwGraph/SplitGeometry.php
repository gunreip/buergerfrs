<?php

namespace Gunreip\TranslationWorkbench\Support\TwGraph;

use InvalidArgumentException;

/** A binary horizontal fork, with one output above and one below its input. */
final class SplitGeometry
{
    public static function plan(array $start, array $outputs, string $direction, string $arcRadius, string $minStemLength): array
    {
        $number = fn ($value) => BoundsRegistry::evaluateRemExpression((string) $value);
        $x = $number($start['x'] ?? '');
        $y = $number($start['y'] ?? '');
        $radius = $number($arcRadius);
        $minimum = $number($minStemLength);
        if (count($outputs) !== 2 || !in_array($direction, ['left-right', 'right-left'], true)
            || in_array(null, [$x, $y, $radius, $minimum], true) || $radius <= 0 || $minimum < 0) {
            throw new InvalidArgumentException('parts.split requires two outputs, horizontal direction, rem coordinates, a positive radius and nonnegative minStemLength.');
        }
        $lanes = [];
        foreach ($outputs as $output) {
            $key = (string) ($output['key'] ?? '');
            $offset = $number($output['offset'] ?? '');
            if ($key === '' || isset($lanes[$key]) || $offset === null || abs($offset) < 1e-8) {
                throw new InvalidArgumentException('parts.split outputs require unique keys and nonzero rem offsets.');
            }
            $lanes[$key] = [...$output, 'key' => $key, 'offset' => $offset];
        }
        $offsets = array_column($lanes, 'offset');
        if ($offsets[0] * $offsets[1] >= 0) {
            throw new InvalidArgumentException('parts.split requires one output above and one below the input.');
        }
        $radius = FusionGeometry::radius(min(array_map('abs', $offsets)), $radius, $minimum);
        // One shared radius; ensure the farther lane also has no short compensator.
        foreach ($offsets as $offset) {
            $stem = abs($offset) - 2 * $radius;
            if ($stem > 1e-8 && $stem < $minimum - 1e-8) {
                throw new InvalidArgumentException('parts.split offsets leave a short compensator; adjust offsets or arcRadius.');
            }
        }
        foreach ($lanes as &$lane) {
            $lane['anchor'] = ['x' => ($x + ($direction === 'left-right' ? 1 : -1) * 2 * $radius).'rem', 'y' => ($y + $lane['offset']).'rem'];
        }
        return ['radius' => $radius.'rem', 'lanes' => array_values($lanes)];
    }
}
