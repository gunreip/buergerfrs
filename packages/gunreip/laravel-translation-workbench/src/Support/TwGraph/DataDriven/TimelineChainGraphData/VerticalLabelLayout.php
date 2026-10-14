<?php

declare(strict_types=1);

namespace Gunreip\TranslationWorkbench\Support\TwGraph\DataDriven\TimelineChainGraphData;

use Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry;

/** Trunk props corresponding to incoming stems of measured label rows. */
final class VerticalLabelLayout
{
    public static function targets(array $preview): array
    {
        $owner = $preview['graph']['graph_id'].'.strang.trunk.1';
        $default = $preview['graph']['stem_length'];
        $targets = [];
        $add = static function ($key, $suffix, $value) use (&$targets, $owner): void {
            $targets[] = ['key' => $key, 'prefix' => $owner.'.paths.trunk.'.$suffix,
                'owner' => $owner, 'component' => 'strang.trunk', 'value' => $value,
                'element' => $owner.'.paths.trunk.'.$suffix];
        };
        $add('trunk.start_length', 'start', $preview['trunk']['start_length'] ?? $default);
        for ($i = 1; $i <= ($preview['trunk']['path_count'] ?? 0); $i++) {
            $entry = $preview['trunk']['path_lengths'][$i] ?? $default;
            $add('trunk.path_lengths.'.$i, 'path.'.$i, is_array($entry) ? ($entry['length'] ?? $default) : $entry);
        }
        $add('trunk.end_length', 'end', $preview['trunk']['end_length'] ?? $default);
        return [...$targets, ...SideLabelLayout::targets($preview)];
    }

    /** Only explicit trunk attachments can identify a stem separating line and label. */
    public static function lineAttachments(array $preview): array
    {
        $attachments = [];
        // A merge's public end anchor is its trunk attachment. The last rendered
        // merge of each side owns that alias, just as in AnchorRegistry.
        $mergeEnds = [];
        foreach ($preview['merges'] ?? [] as $merge) {
            if (in_array($merge['side'] ?? null, ['left', 'right'], true)) {
                $mergeEnds['strang.merge-'.$merge['side'].'.end'] = $merge['attach_to'] ?? '';
            }
        }
        foreach (['merges', 'branches', 'rekeys'] as $family) {
            foreach ($preview[$family] ?? [] as $index => $part) {
                $anchor = $part['attach_to'] ?? '';
                $anchor = $mergeEnds[$anchor] ?? $anchor;
                if (! preg_match('/^strang\.trunk\.path\.(\d+)\.end$/D', $anchor, $match)) {
                    continue;
                }
                $id = $part['id'] ?? $preview['graph']['graph_id'].'.'.str_replace('tw-graph.', '', $part['component']).'.'.($part['component_counter'] ?? $index + 1);
                $attachments[] = ['prefix' => $id, 'row' => (int) $match[1] + 1];
                // branch-end is a separate component, but follows the same trunk attachment.
                if ($family === 'branches' && isset($part['side'])) {
                    $attachments[] = ['prefix' => $preview['graph']['graph_id'].'.strang.branch-end.'.$part['side'].'.'.($part['component_counter'] ?? $index + 1),
                        'row' => (int) $match[1] + 1];
                }
            }
        }
        return $attachments;
    }

    public static function recordLengths(array $preview, array $increments, ?\Gunreip\TranslationWorkbench\Support\TwGraph\CalculatedLengths $lengths): void
    {
        foreach (self::targets($preview) as $target) {
            if ($target['fixed'] ?? false) continue;
            $extra = $increments[$target['key']] ?? 0;
            if ($extra <= 0) {
                continue;
            }
            $entry = data_get($preview, $target['key']);
            $pieces = is_array($entry) && (($entry['component'] ?? '') === 'stem-compressed' || ($entry['compressed'] ?? false))
                ? ['.stem.before' => 0.25, '.stem.dotted' => 0.5, '.stem.after' => 0.25] : ['' => 1];
            $base = BoundsRegistry::evaluateRemExpression($target['value']);
            foreach ($pieces as $suffix => $fraction) {
                $lengths?->record($target['element'].$suffix, $target['component'], $target['owner'], $target['key'],
                    ['additional' => ($extra * $fraction).'rem'], 'Vertical clearance between measured labels and neighbouring labels or paths.',
                    $base === null ? null : (($base - $extra) * $fraction).'rem');
            }
        }
    }

    public static function apply(array $preview, array $increments): array
    {
        foreach (self::targets($preview) as $target) {
            if ($target['fixed'] ?? false) continue;
            $extra = $increments[$target['key']] ?? 0;
            $base = BoundsRegistry::evaluateRemExpression($target['value']);
            if (! is_numeric($extra) || $extra <= 0 || $base === null) {
                continue;
            }
            $entry = data_get($preview, $target['key']);
            $value = ($base + (float) $extra).'rem';
            data_set($preview, $target['key'], is_array($entry) ? array_replace($entry, ['length' => $value]) : $value);
        }
        return $preview;
    }
}
