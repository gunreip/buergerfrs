<?php

declare(strict_types=1);

namespace Gunreip\TranslationWorkbench\Support\TwGraph\DataDriven\TimelineChainGraphData;

use Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry;
use Gunreip\TranslationWorkbench\Support\TwGraph\Defaults;

/** Maps measured horizontal clearance back to authored component props. No rectangle estimates. */
final class HorizontalLabelLayout
{
    public static function targets(array $preview): array
    {
        $graph = $preview['graph'];
        $targets = [];
        foreach (['merges', 'rekeys', 'branches'] as $family) {
            foreach ($preview[$family] ?? [] as $index => $part) {
                $counter = $part['component_counter'] ?? $index + 1;
                $name = str_replace('tw-graph.', '', $part['component']);
                $id = $part['id'] ?? $graph['graph_id'].'.'.$name.'.'.$counter;
                $prefixes = [$id];
                if ($family === 'branches') {
                    $prefixes[] = $graph['graph_id'].'.strang.branch-end.'.$part['side'].'.'.$counter;
                }
                $tail = $part['stem_continuation'] ?? [];
                $tailKey = array_key_last($tail);
                $coupledStem = $family === 'merges' && $tailKey !== null && is_array($tail[$tailKey])
                    && array_diff_key($tail[$tailKey], ['length' => true]) === []
                    ? ['key' => "merges.$index.stem_continuation.$tailKey", 'element' => $id.'.paths.merge.stem'.($tailKey + (array_is_list($tail) ? 2 : 1))] : null;
                $targets[] = ['coupledStem' => $coupledStem, 'key' => "$family.$index.bridge_length", 'prefixes' => $prefixes,
                    'side' => $part['side'], 'component' => $name, 'owner' => $id,
                    'entryStem' => $family === 'branches' ? "branches.$index.entry_stem_length" : null,
                    'terminals' => [$family === 'branches'
                        ? $prefixes[1].'.path.branch-end'
                        : $id.($family === 'merges' ? '.paths.merge.start'
                            : (($part['kind'] ?? '') === 'source' ? '.paths.rekey-source.start' : '.path.rekey-target.end'))],
                    'element' => $id.match ($family) {
                        'merges' => '.paths.merge.bridge',
                        'branches' => '.paths.branch.bridge.1',
                        default => ($part['kind'] ?? '') === 'source' ? '.paths.rekey-source.bridge' : '.paths.rekey-target.bridge.1',
                    },
                    'value' => $part['bridge_length'] ?? $graph['bridge_length']];
                if ($family === 'merges') {
                    for ($e = 1; $e <= ($part['extension_count'] ?? 0); $e++) {
                        $continuation = $part['extension_stem_continuations'][$e] ?? [];
                        $lastKey = array_key_last($continuation);
                        // A trailing unlabelled stem moves the full label block below the fixed arc.
                        $compactStem = $e === 1 && $lastKey !== null && is_array($continuation[$lastKey])
                            && array_diff_key($continuation[$lastKey], ['length' => true]) === []
                            ? "merges.$index.extension_stem_continuations.$e.$lastKey" : null;
                        $targets[] = ['key' => "$family.$index.extension_bridge_continuations.$e",
                            'extensionCompactionStem' => in_array($e, Defaults::dataDriven('merge_extension_compaction_indices', [3, 4]), true)
                                && $lastKey !== null && is_array($continuation[$lastKey])
                                && array_diff_key($continuation[$lastKey], ['length' => true]) === []
                                ? ['key' => "merges.$index.extension_stem_continuations.$e.$lastKey",
                                    'element' => $id.'.extension.'.$e.'.paths.merge-extension.stem'.($lastKey + (array_is_list($continuation) ? 2 : 1))] : null,
                            'compactStem' => $compactStem,
                            'compactStemElement' => $compactStem ? $id.'.extension.'.$e.'.paths.merge-extension.stem'.($lastKey + (array_is_list($continuation) ? 2 : 1)) : null, 'parentPrefix' => $id,
                            'prefixes' => [$id.'.extension.'.$e], 'side' => $part['side'],
                            'terminals' => [$id.'.extension.'.$e.'.paths.merge-extension.start'],
                            'component' => $name, 'owner' => $id, 'element' => $id.'.extension.'.$e.'.paths.merge-extension.bridge',
                            'value' => $part['extension_bridge_continuations'][$e] ?? $part['extension_bridge_length'] ?? $graph['bridge_length']];
                    }
                }
            }
        }
        return $targets;
    }

    public static function apply(array $preview, array $increments): array
    {
        foreach (self::targets($preview) as $target) {
            $extra = $increments[$target['key']] ?? 0;
            if (! is_numeric($extra) || $extra <= 0) {
                continue;
            }
            $base = BoundsRegistry::evaluateRemExpression($target['value']);
            if ($base === null) {
                continue;
            }
            data_set($preview, $target['key'], ($base + (float) $extra).'rem');
        }
        $preview['merge'] = $preview['merges'][0] ?? null;
        return $preview;
    }
}
