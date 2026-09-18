<?php

namespace Gunreip\TranslationWorkbench\Support\TwGraph;

use InvalidArgumentException;

final class FusionGeometry
{
    /** Keep the stem either absent or long enough for its two separate joints. */
    public static function radius(float $offset, float $preferred, float $minimumStem, float $width = INF): float
    {
        if ($preferred <= 0 || $minimumStem < 0 || $width < 0) {
            throw new InvalidArgumentException('Fusion requires a positive radius and nonnegative minimum stem length and width.');
        }

        $offset = abs($offset);
        $limit = min($preferred, $width / 2);
        $radius = min($limit, $offset / 2);
        $stem = $offset - 2 * $radius;

        if ($stem > 1e-8 && $stem < $minimumStem - 1e-8) {
            $radius = $offset / 2;
            if (2 * $radius > $width + 1e-8) {
                throw new InvalidArgumentException('Fusion needs more horizontal space to replace a short stem with a direct arc pair.');
            }
        }

        return $radius;
    }

    /** Plan shared trunks from the outside toward the nearest input on each side. */
    public static function group(array $inputs, string $direction, float $preferred, float $minimumStem): array
    {
        if (count($inputs) < 2 || !in_array($direction, ['left-right', 'right-left'], true)) {
            throw new InvalidArgumentException('Fusion requires at least two inputs and a horizontal direction.');
        }
        $lanes = [];
        foreach ($inputs as $index => $input) {
            $key = (string) ($input['key'] ?? $index);
            $x = BoundsRegistry::evaluateRemExpression((string) ($input['anchor']['x'] ?? ''));
            $y = BoundsRegistry::evaluateRemExpression((string) ($input['anchor']['y'] ?? ''));
            if ($key === '' || isset($lanes[$key]) || $x === null || $y === null) {
                throw new InvalidArgumentException('Fusion requires unique input keys and resolvable rem coordinates.');
            }
            $lanes[$key] = [...$input, 'key' => $key, 'x' => $x, 'y' => $y];
        }
        $ys = array_column($lanes, 'y');
        $center = (min($ys) + max($ys)) / 2;
        $offsets = array_filter(array_map(fn ($y) => abs($center - $y), $ys), fn ($offset) => $offset > 1e-8);
        $radius = self::radius($offsets === [] ? 0 : min($offsets), $preferred, $minimumStem);
        $sx = $direction === 'left-right' ? 1 : -1;
        $xs = array_column($lanes, 'x');
        $entryX = $sx > 0 ? max($xs) : min($xs);
        $point = fn ($x, $y) => ['x' => $x.'rem', 'y' => $y.'rem'];
        $output = $point($entryX + $sx * 2 * $radius, $center);
        foreach ($lanes as &$lane) {
            $lane['sy'] = $lane['y'] < $center ? 1 : -1;
            $lane['straight'] = abs($lane['y'] - $center) < 1e-8;
            $lane['bendStart'] = $point($entryX, $lane['y']);
            $lane['arcEnd'] = $point($entryX + $sx * $radius, $lane['y'] + $lane['sy'] * $radius);
            $lane['next'] = null;
        }
        unset($lane);
        foreach ([-1, 1] as $sy) {
            $side = array_filter($lanes, fn ($lane) => !$lane['straight'] && $lane['sy'] === $sy);
            uasort($side, fn ($a, $b) => abs($b['y'] - $center) <=> abs($a['y'] - $center));
            $keys = array_keys($side);
            foreach ($keys as $i => $key) {
                $next = $keys[$i + 1] ?? null;
                $lanes[$key]['next'] = $next;
                $lanes[$key]['stemEnd'] = $next === null
                    ? $point($entryX + $sx * $radius, $center - $sy * $radius)
                    : $lanes[$next]['arcEnd'];
            }
        }

        return ['radius' => $radius, 'output' => $output, 'lanes' => array_values($lanes)];
    }

}
