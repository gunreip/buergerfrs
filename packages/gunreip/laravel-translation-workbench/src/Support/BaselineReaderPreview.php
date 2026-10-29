<?php

namespace Gunreip\TranslationWorkbench\Support;

/** Reads an explicitly exported rehearsal snapshot. Never connects to the rehearsal database. */
class BaselineReaderPreview
{
    public function load(): ?array
    {
        $path = storage_path('app/private/workbench-previews/reader-preview.json');
        if (! is_file($path)) {
            return null;
        }
        try {
            $data = json_decode(file_get_contents($path), true, flags: JSON_THROW_ON_ERROR);
            if (($data['schema'] ?? null) !== 1 || ($data['mode'] ?? null) !== 'isolated_baseline_preview'
                || ! is_array($data['items'] ?? null) || ! is_array($data['chains'] ?? null)) {
                return null;
            }

            return $data;
        } catch (\JsonException) {
            return null;
        }
    }

    /** Filter by evidence across the exported locales, independently of the selected fallback chain. */
    public function keyGroups(?array $data): array
    {
        $groups = array_fill_keys(['all', 'linked', 'blocked_structure', 'option_source_evidenced', 'source_assignment_open'], []);
        foreach ($data['items'] ?? [] as $key => $item) {
            $groups['all'][] = $key;
            $states = array_column(array_filter(array_column($item['locales'], 'published')), 'state');
            foreach (['linked', 'blocked_structure'] as $state) {
                if (in_array($state, $states, true)) {
                    $groups[$state][] = $key;
                }
            }
            if (in_array('unassigned_key', $states, true)) {
                $group = ($item['unassigned_context']['kind'] ?? null) === 'option_source_evidenced'
                    ? 'option_source_evidenced' : 'source_assignment_open';
                $groups[$group][] = $key;
            }
        }

        return $groups;
    }

    public function selection(?array $data, string $key, string $chain, string $filter = 'all'): array
    {
        $groups = $this->keyGroups($data);
        $keys = $groups[$filter] ?? [];
        if ($data === null) {
            return ['groups' => $groups, 'keys' => $keys, 'snapshot' => null, 'selected' => null, 'result' => null, 'chain' => []];
        }
        $key = $key === '' ? ($keys[0] ?? '') : $key;
        $selected = in_array($key, $keys, true) ? ($data['items'][$key] ?? null) : null;
        $locales = $data['chains'][$chain] ?? [];
        $result = null;
        foreach ($locales as $position => $locale) {
            $published = $selected['locales'][$locale]['published'] ?? null;
            if ($published !== null) {
                $result = [...$published, 'locale' => $locale, 'fallback' => $position > 0];
                break;
            }
        }

        return ['groups' => $groups, 'keys' => $keys, 'snapshot' => $data, 'selected' => $selected, 'result' => $result, 'chain' => $locales];
    }
}
