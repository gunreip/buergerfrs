<?php

declare(strict_types=1);

namespace Gunreip\TranslationWorkbench\Support\TwGraph\DataDriven\TimelineChainGraphData;

/** Maps existing public side-strang props to the label rows they actually move. */
final class SideLabelLayout
{
    public static function targets(array $preview): array
    {
        $targets = [];
        $graph = $preview['graph'];
        foreach (['branches', 'rekeys', 'merges'] as $family) {
            foreach ($preview[$family] ?? [] as $i => $part) {
                $counter = $part['component_counter'] ?? $i + 1;
                $component = str_replace('tw-graph.', '', $part['component']);
                $owner = $part['id'] ?? $graph['graph_id'].'.'.$component.'.'.$counter;
                $merge = $family === 'merges' || ($part['kind'] ?? '') === 'source';
                $path = $family === 'branches' ? 'branch' : ($family === 'merges' ? 'merge' : 'rekey-'.$part['kind']);
                $prefix = $owner.'.paths.'.$path;
                $default = $part['stem_length'] ?? $graph['stem_length'];
                $add = static function ($key, $element, $value, $lane = null, $fixed = false) use (&$targets, $component, $owner): void {
                    $targets[] = compact('key', 'element', 'value', 'component', 'owner', 'fixed') + ['prefix' => $element, 'lane' => $lane ?? $owner];
                };
                if ($family === 'branches') {
                    $add("branches.$i.entry_stem_length", $prefix.'.entry-stem', $part['entry_stem_length'] ?? '0rem');
                }
                if ($merge) {
                    $add("$family.$i.start_length", $prefix.'.start', $part['start_length'] ?? $graph['arc_radius']);
                    $add("$family.$i.stem_length", $prefix.'.stem1', $default);
                }
                if ($family === 'branches' && ! empty($part['step'])) {
                    $add("$family.$i.step.beforeLength", $prefix.'.step.label', $part['step']['beforeLength'] ?? '1.5rem', fixed: true);
                    $add("$family.$i.step.afterLength", $prefix.'.step.stem.after', $part['step']['afterLength'] ?? '2.5rem');
                }
                foreach ($part['stem_continuation'] ?? [] as $key => $entry) {
                    $number = array_is_list($part['stem_continuation']) ? $key + 1 : $key;
                    $element = $prefix.($merge ? '.stem'.($number + 1) : '.stem.'.$number);
                    $value = is_array($entry) ? ($entry['length'] ?? $entry[0] ?? $default) : ($entry ?: $default);
                    $add("$family.$i.stem_continuation.$key", $element, $value);
                }
                if (! $merge) {
                    $element = $family === 'branches'
                        ? $graph['graph_id'].'.strang.branch-end.'.$part['side'].'.'.$counter.'.path.branch-end'
                        : $owner.'.path.rekey-target.end';
                    $add("$family.$i.end_length", $element, $part['end_length'] ?? $graph['line_length'] ?? '4rem');
                }
                if ($family === 'merges') {
                    for ($e = 1; $e <= ($part['extension_count'] ?? 0); $e++) {
                        $lane = $owner.'.extension.'.$e;
                        $extension = $lane.'.paths.merge-extension';
                        // Start and stem labels must be measured together, even when start has no override.
                        $add("$family.$i.extension_stem_lengths.$e", $extension.'.start', $default, $lane, true);
                        $extensionDefault = $part['extension_stem_lengths'][$e] ?? $part['extension_stem_length'] ?? $default;
                        $add("$family.$i.extension_stem_lengths.$e", $extension.'.stem1', $extensionDefault, $lane);
                        foreach ($part['extension_stem_continuations'][$e] ?? [] as $key => $entry) {
                            $number = array_is_list($part['extension_stem_continuations'][$e]) ? $key + 1 : $key;
                            $add("$family.$i.extension_stem_continuations.$e.$key", $extension.'.stem'.($number + 1), is_array($entry) ? ($entry['length'] ?? $entry[0] ?? $extensionDefault) : ($entry ?: $extensionDefault), $lane);
                        }
                    }
                }
            }
        }
        return $targets;
    }
}
