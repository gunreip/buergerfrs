<?php

declare(strict_types=1);

namespace Gunreip\TranslationWorkbench\Support\TwGraph;

use InvalidArgumentException;

/** Reverse traversal of an authored merge without moving its physical connections. */
final class MergeTraversal
{
    public static function orient(array $segments, string $direction): array
    {
        if ($direction === 'bottom-top') {
            return $segments;
        }
        if ($direction !== 'top-bottom') {
            throw new InvalidArgumentException('Merge direction must be bottom-top or top-bottom.');
        }
        $opposite = ['top' => 'bottom', 'bottom' => 'top', 'left' => 'right', 'right' => 'left', 'bottom-top' => 'top-bottom', 'top-bottom' => 'bottom-top', 'left-right' => 'right-left', 'right-left' => 'left-right'];
        $result = [];
        foreach (array_reverse($segments) as $entry) {
            $s = $entry['segment'];
            // Terminal decorations keep their authored caps, labels and physical positions.
            if (in_array($entry['component'], ['start', 'end'], true)) {
                $result[] = $entry;

                continue;
            }
            foreach ([['anchorStart', 'anchorEnd'], ['startAnchor', 'endAnchor'], ['nodeStart', 'nodeEnd'], ['nodeStartDot', 'nodeEndDot'], ['jointArrowStart', 'jointArrowEnd'], ['jointArrowStartDirection', 'jointArrowEndDirection'], ['jointArrowStartColor', 'jointArrowEndColor'], ['devCounterStart', 'devCounterEnd'], ['startLabel', 'endLabel'], ['capStart', 'capEnd'], ['beforeLength', 'afterLength']] as [$a,$b]) {
                $hasA = array_key_exists($a, $s);
                $hasB = array_key_exists($b, $s);
                $vA = $s[$a] ?? null;
                $vB = $s[$b] ?? null;
                unset($s[$a],$s[$b]);
                if ($hasB) {
                    $s[$a] = $vB;
                }
                if ($hasA) {
                    $s[$b] = $vA;
                }
            }
            foreach (['direction', 'jointArrowStartDirection', 'jointArrowEndDirection'] as $key) {
                if (isset($s[$key])) {
                    $s[$key] = $opposite[$s[$key]] ?? $s[$key];
                }
            }
            $entry['segment'] = $s;
            $result[] = $entry;
        }
        // At an arc -> stem junction the outgoing arc owns the single joint-arrow.
        foreach ($result as $i => &$entry) {
            if ($entry['component'] !== 'arc' || ! isset($result[$i + 1])) {
                continue;
            }
            $next = &$result[$i + 1]['segment'];
            if (! in_array($result[$i + 1]['component'], ['path', 'stem-compressed'], true) || empty($next['jointArrowStart']) || ! empty($next['nodeStartDot'])) {
                continue;
            }
            if ($entry['segment']['anchorEnd'] != $next['anchorStart']) {
                continue;
            }
            foreach (['node', 'nodeDot', 'jointArrow', 'devCounter'] as $field) {
                $from = $field === 'nodeDot' ? 'nodeStartDot' : $field.'Start';
                $to = $field === 'nodeDot' ? 'nodeEndDot' : $field.'End';
                if (array_key_exists($from, $next)) {
                    $entry['segment'][$to] = $next[$from];
                }
            }
            $entry['segment']['jointArrowEndDirection'] = match ($next['direction']) {
                'top-bottom' => 'bottom', 'bottom-top' => 'top', 'left-right' => 'right', 'right-left' => 'left',
            };
            $next['nodeStart'] = false;
            $next['nodeStartDot'] = false;
            $next['jointArrowStart'] = false;
            $next['devCounterStart'] = null;
            unset($next);
        }
        unset($entry);

        return $result;
    }
}
