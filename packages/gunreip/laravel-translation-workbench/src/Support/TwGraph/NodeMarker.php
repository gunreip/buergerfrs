<?php

declare(strict_types=1);

namespace Gunreip\TranslationWorkbench\Support\TwGraph;

/** Marker choice is independent of label and anchor existence. */
final class NodeMarker
{
    public static function resolve(bool $visible, bool $dot, array $labels = [], string $position = 'end'): array
    {
        $labels = array_values(array_filter($labels, static fn ($label) => is_array($label) && filled($label['text'] ?? null)));
        if ($labels !== []) {
            $key = $position === 'start' ? 'nodeStart' : 'nodeEnd';
            // A shared anchor is visible while at least one attached label requests it.
            $visible = collect($labels)->contains(static fn ($label) => ($label[$key] ?? true) !== false);
            $dot = true;
        }

        return ['visible' => $visible, 'dot' => $visible && $dot, 'arrow' => $visible && ! $dot];
    }
}
