<?php

namespace Gunreip\TranslationWorkbench\Support;

/** Lists open published values from the matching exported evidence, not live records. */
final class BaselineOpenValues
{
    public function inspect(?array $snapshot, ?array $report): array
    {
        $empty = ['state' => 'unavailable', 'rows' => []];
        if ($snapshot === null || (new BaselineTransferBalance)->inspect($report)['state'] !== 'verified'
            || ($snapshot['baseline_hash'] ?? null) !== ($report['fresh_generation_published_values']['baseline_hash'] ?? null)
            || ($snapshot['input_dump_sha256'] ?? null) !== ($report['input_dump_sha256'] ?? null)) {
            return $empty;
        }
        $rows = [];
        $counts = ['linked' => 0, 'blocked_structure' => 0, 'unassigned_key' => 0];
        foreach ($snapshot['items'] ?? [] as $key => $item) {
            if (! is_string($key) || ! is_array($item) || ($item['key'] ?? null) !== $key || ! is_array($item['locales'] ?? null)) {
                return $empty;
            }
            $groups = [];
            foreach ($item['locales'] ?? [] as $locale => $data) {
                if (! is_string($locale) || ! is_array($data)) {
                    return $empty;
                }
                $published = $data['published'] ?? null;
                if ($published === null) {
                    continue;
                }
                if (! is_array($published)) {
                    return $empty;
                }
                $state = $published['state'] ?? '';
                if (! array_key_exists($state, $counts)) {
                    return $empty;
                }
                $counts[$state]++;
                if ($state !== 'linked') {
                    $groups[$state][] = $locale;
                }
            }
            foreach ($groups as $state => $locales) {
                sort($locales);
                $rows[] = ['key' => $key, 'state' => $state, 'locales' => $locales,
                    'context' => $state === 'unassigned_key' ? ($item['unassigned_context']['kind'] ?? 'source_assignment_open') : null];
            }
        }
        foreach ($counts as $state => $count) {
            if ($count !== ($report['fresh_generation_published_values']['totals'][$state] ?? null)) {
                return $empty;
            }
        }
        usort($rows, fn ($a, $b) => strcmp($a['key'], $b['key']) ?: strcmp($a['state'], $b['state']));

        return ['state' => 'verified', 'rows' => $rows, 'values' => $counts['blocked_structure'] + $counts['unassigned_key']];
    }
}
