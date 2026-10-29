<?php

namespace Gunreip\TranslationWorkbench\Support;

use PDO;
use RuntimeException;

/** Immutable name evidence, not a key assignment or an editorial decision. */
final class BaselineNameInventory
{
    public function capture(PDO $db, string $schema, string $baseline): array
    {
        $this->assertContext($db, $schema);
        $query = $db->prepare("SELECT '' AS namespace,full_name FROM $schema.published_value_evidence WHERE baseline_hash=?
            UNION SELECT namespace,full_name FROM $schema.translation_workbench_key_names WHERE valid_to IS NULL");
        $query->execute([$baseline]);

        return $this->create($query->fetchAll(PDO::FETCH_ASSOC));
    }

    public function create(array $names): array
    {
        if ($names === [] || ! array_is_list($names)) {
            throw new RuntimeException('Name inventory is empty');
        }
        foreach ($names as $index => $row) {
            if (! is_array($row) || count($row) !== 2
                || ! is_string($row['namespace'] ?? null) || ! is_string($row['full_name'] ?? null) || $row['full_name'] === '') {
                throw new RuntimeException('Invalid name inventory entry');
            }
            $names[$index] = ['namespace' => $row['namespace'], 'full_name' => $row['full_name']];
        }
        usort($names, fn ($a, $b) => strcmp($a['namespace'], $b['namespace']) ?: strcmp($a['full_name'], $b['full_name']));
        if (count(array_unique(array_map(fn ($row) => json_encode($row, JSON_THROW_ON_ERROR), $names))) !== count($names)) {
            throw new RuntimeException('Duplicate name inventory entry');
        }

        return ['names' => $names, 'sha256' => hash('sha256', json_encode($names, JSON_THROW_ON_ERROR)), 'count' => count($names)];
    }

    public function store(PDO $db, string $schema, string $baseline, string $sourceBaseline, array $inventory): void
    {
        $this->assertContext($db, $schema);
        $this->validate($inventory);
        $query = $db->prepare("INSERT INTO $schema.baseline_name_inventories
            (baseline_hash,source_baseline_hash,names,names_sha256,name_count) VALUES (?,?,?::jsonb,?,?)");
        $query->execute([$baseline, $sourceBaseline, json_encode($inventory['names'], JSON_THROW_ON_ERROR), $inventory['sha256'], $inventory['count']]);
    }

    public function read(PDO $db, string $schema, string $baseline): array
    {
        $this->assertContext($db, $schema);
        $query = $db->prepare("SELECT names,names_sha256,name_count FROM $schema.baseline_name_inventories WHERE baseline_hash=?");
        $query->execute([$baseline]);
        $row = $query->fetch(PDO::FETCH_ASSOC);
        if (! $row) {
            throw new RuntimeException('Complete baseline name inventory is missing');
        }
        $names = json_decode($row['names'], true, flags: JSON_THROW_ON_ERROR);
        $inventory = ['names' => $names, 'sha256' => $row['names_sha256'], 'count' => (int) $row['name_count']];
        $this->validate($inventory);

        // JSONB may reorder object keys; expose the canonical representation.
        return $this->create($names);
    }

    public function validate(array $inventory): void
    {
        if (! is_array($inventory['names'] ?? null)) {
            throw new RuntimeException('Name inventory entries are missing');
        }
        $actual = $this->create($inventory['names'] ?? []);
        if ($actual['sha256'] !== ($inventory['sha256'] ?? null) || $actual['count'] !== ($inventory['count'] ?? null)) {
            throw new RuntimeException('Name inventory fingerprint or count differs');
        }
    }

    private function assertContext(PDO $db, string $schema): void
    {
        if (! $db->inTransaction() || preg_match('/^[a-z_][a-z0-9_]{0,62}$/D', $schema) !== 1) {
            throw new RuntimeException('Name inventory requires a transaction and explicit schema');
        }
    }
}
