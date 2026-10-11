<?php

declare(strict_types=1);

namespace Gunreip\TranslationWorkbench\Support\TwGraph\DataDriven\TimelineChainGraphData;

final class LabelLineClearance
{
    public static function collisions(array $boxes): array
    {
        $pairs = [];
        foreach ($boxes as $i => $a) {
            foreach (array_slice($boxes, $i + 1) as $b) {
                if ($a['owner'] === $b['owner'] || ($a['type'] !== 'label' && $b['type'] !== 'label')) {
                    continue;
                }
                if ($a['xStart'] < $b['xEnd'] && $b['xStart'] < $a['xEnd']
                    && $a['yStart'] < $b['yEnd'] && $b['yStart'] < $a['yEnd']) {
                    $pairs[] = [$a, $b];
                }
            }
        }

        return $pairs;
    }

    /** Place owners in order; not-yet-placed geometry cannot move an earlier owner. */
    public static function resolveOrdered(array $state, callable $geometry, array $options, float $gap, array $deferredOwners): array
    {
        $pending = array_fill_keys($deferredOwners, true);
        foreach ($options as $option) {
            unset($pending[$option['owner']]);
            $visibleGeometry = static fn (array $candidate): array => array_values(array_filter(
                $geometry($candidate),
                static fn (array $box): bool => ! isset($pending[$box['owner']]),
            ));
            $initial = $state;
            $state = self::resolve($state, $visibleGeometry, [$option], $gap);
            if (isset($option['stemLength'], $option['bridgeLength'])) {
                $stemAdded = $option['stemLength']($state) - $option['stemLength']($initial);
                $bridgeAdded = $option['bridgeLength']($state) - $option['bridgeLength']($initial);
                if ($stemAdded > 0 && $bridgeAdded > 0) {
                    // The unavoidable bridge extension may make earlier vertical detours obsolete.
                    // Re-evaluate from the authored stem, preserving already settled owners.
                    $candidate = ($option['bridge'])($initial, $bridgeAdded);
                    $candidate = self::resolve($candidate, $visibleGeometry, [$option], $gap);
                    $keys = static fn (array $pairs): array => array_map(
                        static fn (array $pair): string => $pair[0]['id'].'|'.$pair[1]['id'], $pairs,
                    );
                    if ($option['stemLength']($candidate) < $option['stemLength']($state)
                        && $option['bridgeLength']($candidate) <= $option['bridgeLength']($state)
                        && array_diff($keys(self::collisions($visibleGeometry($candidate))),
                            $keys(self::collisions($visibleGeometry($state)))) === []) {
                        $state = $candidate;
                    }
                }
            }
        }

        return $state;
    }

    /** Try bounded candidate lengths, retaining useful vertical separation without introducing new collisions. */
    public static function resolve(array $state, callable $geometry, array $options, float $gap): array
    {
        // Try every lane vertically before allowing any horizontal fallback.
        foreach (['stem', 'bridge'] as $axis) {
            foreach ($options as $option) {
                $owner = $option['owner'];
                $pairs = array_filter(self::collisions($geometry($state)), static fn ($pair) => $pair[0]['owner'] === $owner || $pair[1]['owner'] === $owner);
                if ($pairs === []) {
                    continue;
                }
                $beforePairs = self::collisions($geometry($state));

                $candidate = $state;
                $trialPairs = $pairs;
                for ($attempt = 0; $attempt < 6; $attempt++) {
                    $delta = 0.0;
                    foreach ($trialPairs as [$a, $b]) {
                        // A vertical stagger can separate labels; a long neighbouring line
                        // must not force an arbitrarily deep stem before horizontal clearance.
                        if ($axis === 'stem' && ($a['type'] !== 'label' || $b['type'] !== 'label')) {
                            continue;
                        }
                        [$moving, $fixed] = $a['owner'] === $owner ? [$a, $b] : [$b, $a];
                        $delta = max($delta, $axis === 'stem'
                            ? ($option['vertical'] > 0 ? $fixed['yEnd'] - $moving['yStart'] : $moving['yEnd'] - $fixed['yStart']) + $gap
                            : ($option['side'] === 'left' ? $moving['xEnd'] - $fixed['xStart'] : $fixed['xEnd'] - $moving['xStart']) + $gap);
                    }
                    if ($delta <= 0) {
                        continue;
                    }
                    $candidate = ($option[$axis])($candidate, $delta);
                    $after = self::collisions($geometry($candidate));
                    // Keep useful vertical separation even when a bridge must still clear a line.
                    $remaining = array_filter($after, static fn ($pair) => $pair[0]['owner'] === $owner || $pair[1]['owner'] === $owner);
                    $keys = static fn (array $pairs): array => array_map(static fn ($p) => $p[0]['id'].'|'.$p[1]['id'], $pairs);
                    if (($remaining === [] || ($axis === 'stem' && count($remaining) < count($pairs))) && array_diff($keys($after), $keys($beforePairs)) === []) {
                        $state = $candidate;
                        break;
                    }
                    if ($remaining === []) {
                        break;
                    }
                    $trialPairs = $remaining;
                }
            }
        }

        return $state;
    }
}
