<?php

namespace Gunreip\TranslationWorkbench\Support\TwGraph\DataDriven\TimelineChainGraphData;

use Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry;
use Gunreip\TranslationWorkbench\Support\TwGraph\ElementIdentifier;

/** Keep merge groups beside the trunk axis; use occupied rectangles for label clearance. */
final class MergeTrunkLabelClearance
{
    public static function apply(array $merges, array $trunkBounds, string $defaultBridge, float $gap, bool $axisOnly = false): array
    {
        $number = static fn ($value): float => BoundsRegistry::evaluateRemExpression((string) $value) ?? 0.0;
        $rem = static fn (float $value): string => rtrim(rtrim(number_format($value, 4, '.', ''), '0'), '.').'rem';
        foreach ($merges as $index => &$merge) {
            $side = $merge['side'] ?? 'left';
            $delta = 0.0;
            $sources = [];
            // A bridge keeps its trunk-side endpoint when lengthened. Its entire
            // rectangle must not be treated as a horizontally translated obstacle.
            // Actual bridge overlaps remain checked by LabelLineClearance.
            foreach ((array) data_get($merge, 'layout.mergeBoundsDebug', []) as $box) {
                // A later extension must not lengthen the already placed main bridge.
                if (preg_match('/extension\.?\d+/', $box['renderId'] ?? $box['id'] ?? '')) {
                    continue;
                }
                if (in_array($box['type'] ?? '', ['merge-start-stem', 'merge-extension-start-stem'], true)) {
                    // The body remains on its side even where the trunk has no current event label.
                    // Connecting bridges are excluded because they intentionally approach the trunk.
                    $required = $side === 'left'
                        ? $number($box['x']) + $number($box['width']) + $gap
                        : $gap - $number($box['x']);
                    if ($required > 0.0001) {
                        $delta = max($delta, $required);
                        $sources[] = ['source' => 'merge-trunk-label-clearance', 'constraint' => 'trunk-axis',
                            'against' => $box['id'], 'requiredIncrement' => $rem($required)];
                    }
                    continue;
                }
                if ($axisOnly || ! in_array($box['type'] ?? '', ['clearance-label', 'clearance-line'], true)) {
                    continue;
                }
                foreach ($trunkBounds as $label) {
                    $central = in_array($label['type'] ?? '', ['strang', 'trunk-terminal-label'], true);
                    // Lines may meet lines intentionally; labels must clear either kind.
                    if ($central ? ($box['type'] !== 'clearance-label')
                        : (($label['type'] ?? '') !== 'trunk-'.$side.'-label')) {
                        continue;
                    }
                    // Empty space inside a group envelope is not an obstacle.
                    if ($number($box['y']) >= $number($label['y']) + $number($label['height']) + $gap
                        || $number($label['y']) >= $number($box['y']) + $number($box['height']) + $gap) {
                        continue;
                    }
                    $required = $side === 'left'
                        ? $number($box['x']) + $number($box['width']) + $gap - $number($label['x'])
                        : $number($label['x']) + $number($label['width']) + $gap - $number($box['x']);
                    if ($required <= 0.0001) {
                        continue;
                    }
                    $delta = max($delta, $required);
                    $sources[] = ['source' => 'merge-trunk-label-clearance', 'trunk' => $label['id'], 'against' => $box['id'], 'requiredIncrement' => $rem($required)];
                }
            }
            if ($delta <= 0.0) {
                continue;
            }
            $base = $merge['bridge_length'] ?? $defaultBridge;
            // Moving the main stem shifts its extensions; their own bridges keep their lengths.
            if (($merge['extension_count'] ?? 0) > 0) {
                $merge['extension_bridge_length'] ??= $defaultBridge;
            }
            $merge['bridge_length'] = $rem($number($base) + $delta);
            $merge['layout']['appliedCompensations'][] = [
                'target' => ElementIdentifier::normalize('strang.merge-'.$side.'.'.($index + 1).'.main.path.merge.bridge1'),
                'prop' => 'bridge_length', 'baseValue' => $base,
                'delta' => $rem($delta), 'effectiveValue' => $merge['bridge_length'],
                'gap' => $rem($gap), 'sources' => $sources,
                'reason' => 'Horizontal clearance from the trunk axis and occupied trunk label areas.',
            ];
        }
        unset($merge);

        return $merges;
    }
}
