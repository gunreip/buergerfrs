<?php

namespace Gunreip\TranslationWorkbench\Support;

use RuntimeException;

/** Compares a snapshot with a context obtained by the transactional database checker. */
final class BaselineProposalEvidence
{
    public function compare(array $snapshot, string $source, string $target, array $context): array
    {
        $evidence = $context['evidence'] ?? [];
        if (! is_string($context['fingerprint'] ?? null)
            || ! hash_equals(hash('sha256', json_encode($evidence, JSON_THROW_ON_ERROR)), $context['fingerprint'])
            || ! is_string($snapshot['baseline_hash'] ?? null)
            || ($evidence['baseline_hash'] ?? null) !== $snapshot['baseline_hash']
            || ($evidence['source'] ?? null) !== $source || ($evidence['target'] ?? null) !== $target) {
            throw new RuntimeException('Database context does not match the proposed baseline');
        }
        if (isset($snapshot['selection']) || isset($snapshot['name_inventory'])) {
            (new BaselineNameInventory)->validate($snapshot['name_inventory'] ?? []);
            if (($evidence['name_inventory_sha256'] ?? null) !== $snapshot['name_inventory']['sha256']) {
                throw new RuntimeException('Snapshot name inventory differs from the database');
            }
        }
        $item = $snapshot['items'][$source] ?? [];
        $captured = [];
        foreach ($item['blocked_sources'] ?? [] as $row) {
            $id = $row['finding_id'] ?? null;
            if (! is_int($id) || $id < 1 || isset($captured[$id])) {
                throw new RuntimeException('Snapshot finding identities are ambiguous');
            }
            $captured[$id] = [$row['path'], $row['line'], $row['column'], $row['expression']];
        }
        $current = [];
        foreach ($evidence['sources'] ?? [] as $row) {
            $id = (int) $row['id'];
            if ($id < 1 || isset($current[$id]) || $row['status'] !== 'active' || $row['raw_source_code_literal'] !== $source) {
                throw new RuntimeException('Database finding identities are ambiguous or inactive');
            }
            $current[$id] = [$row['source_path'], (int) $row['source_line'], (int) $row['source_column'], $row['raw_expression']];
        }
        ksort($captured);
        ksort($current);
        if ($captured === [] || $captured !== $current) {
            throw new RuntimeException('Snapshot occurrences differ from the database');
        }
        $values = [];
        foreach ($item['locales'] ?? [] as $locale => $row) {
            if (($row['published'] ?? null) !== null) {
                $values[] = [$source, $locale, $row['published']['path'], $row['published']['value'], $row['published']['state']];
            }
        }
        $published = array_map(fn ($row) => [$row['full_name'], $row['locale'], $row['path'], $row['value'], $row['state']],
            $evidence['published_values'] ?? []);
        sort($values);
        sort($published);
        if ($values === [] || $values !== $published) {
            throw new RuntimeException('Snapshot published evidence differs from the database');
        }

        return ['status' => 'matched', 'finding_count' => count($current), 'published_count' => count($published),
            'database_fingerprint' => $context['fingerprint'],
            'snapshot_sha256' => hash('sha256', json_encode($snapshot, JSON_THROW_ON_ERROR))];
    }
}
