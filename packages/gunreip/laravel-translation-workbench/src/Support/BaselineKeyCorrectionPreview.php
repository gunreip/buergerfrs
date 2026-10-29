<?php

namespace Gunreip\TranslationWorkbench\Support;

use RuntimeException;

/** A read-only proposal against exported evidence, never an authorization to write. */
final class BaselineKeyCorrectionPreview
{
    public function inspect(?array $snapshot, string $source, string $target): array
    {
        $target = trim($target);
        $result = ['source' => $source, 'target' => $target, 'status' => 'unavailable', 'conflicts' => [],
            'source_count' => null, 'locales' => []];
        $item = $snapshot['items'][$source] ?? null;
        if ($item === null || ! in_array($source, (new BaselineReaderPreview)->keyGroups($snapshot)['blocked_structure'], true)) {
            return $result;
        }
        $result['source_count'] = isset($item['blocked_sources']) ? count($item['blocked_sources']) : null;
        foreach ($item['locales'] as $locale => $row) {
            if (($row['published'] ?? null) !== null) {
                $result['locales'][] = $locale;
            }
        }
        if ($target === $source) {
            return [...$result, 'status' => 'unchanged'];
        }
        if (preg_match('/^[a-zA-Z0-9_-]+(?:\.[a-zA-Z0-9_-]+){2,}$/D', $target) !== 1) {
            return [...$result, 'status' => 'invalid'];
        }
        $names = array_keys($snapshot['items']);
        if (isset($snapshot['selection']) || isset($snapshot['name_inventory'])) {
            try {
                (new BaselineNameInventory)->validate($snapshot['name_inventory'] ?? []);
            } catch (RuntimeException) {
                return [...$result, 'status' => 'name_inventory_unavailable'];
            }
            $names = array_unique([...$names, ...array_column(array_filter($snapshot['name_inventory']['names'],
                fn ($row) => $row['namespace'] === ''), 'full_name')]);
        }
        foreach ($names as $existing) {
            // The source itself is being replaced in this hypothetical proposal.
            if ($existing === $source) {
                continue;
            }
            $relation = match (true) {
                $existing === $target => 'occupied',
                str_starts_with($target, $existing.'.') => 'scalar_parent',
                str_starts_with($existing, $target.'.') => 'array_parent',
                default => null,
            };
            if ($relation !== null) {
                $result['conflicts'][] = ['key' => $existing, 'relation' => $relation];
            }
        }

        return [...$result, 'status' => $result['conflicts'] ? 'conflict' : 'no_snapshot_conflict'];
    }
}
