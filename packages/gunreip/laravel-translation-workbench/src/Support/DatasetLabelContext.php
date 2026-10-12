<?php

namespace Gunreip\TranslationWorkbench\Support;

final class DatasetLabelContext
{
    public static function menu(array $source): array
    {
        if (empty($source['type']) || empty($source['id'])) {
            return [];
        }

        $sections = ['overview' => __('Overview')];
        if (in_array($source['type'], ['event', 'review', 'lang-value', 'finding'], true)) {
            $sections['values'] = __('Values and changes');
        }
        $sections['context'] = __('Context and relationships');
        $sections['technical'] = __('Technical details');

        $menu = collect($sections)
            ->map(fn (string $label, string $section): array => [
                'label' => $label,
                'event' => 'tw-graph-dataset-details',
                'detail' => ['source' => $source, 'section' => $section],
            ])->values()->all();

        if ($source['type'] === 'finding' && ! empty($source['dataset_id'])) {
            $menu[] = [
                'label' => __('Merge relationships and reviews'),
                'event' => 'tw-graph-dataset-details',
                'detail' => ['source' => ['type' => 'merge-group', 'id' => $source['dataset_id'], 'ids' => [$source['id']]], 'section' => 'overview'],
            ];
        }
        if ($source['type'] === 'finding-group' && ($source['relation'] ?? '') === 'merge') {
            $menu[] = [
                'label' => __('Merge relationships and reviews'),
                'event' => 'tw-graph-dataset-details',
                'detail' => ['source' => ['type' => 'merge-group', 'id' => $source['id'], 'ids' => $source['ids']], 'section' => 'overview'],
            ];
        }

        return $menu;
    }

    /** Add references only inside a label tree, preserving its layout props. */
    public static function labels(array $labels, array $source): array
    {
        $menu = self::menu($source);
        if ($menu === []) {
            return $labels;
        }
        if (array_key_exists('text', $labels)) {
            $labels['contextMenu'] ??= $menu;

            return $labels;
        }
        foreach ($labels as $key => $entry) {
            if (in_array($key, ['left', 'right', 'top', 'bottom', 'center', 'start', 'end'], true)
                && (is_string($entry) || (is_array($entry) && array_is_list($entry) && $entry !== []))) {
                $labels[$key] = ['text' => $entry, 'contextMenu' => $menu];
            } elseif (is_array($entry)) {
                $labels[$key] = self::labels($entry, $source);
            }
        }

        return $labels;
    }
}
