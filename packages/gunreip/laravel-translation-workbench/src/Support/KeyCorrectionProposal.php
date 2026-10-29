<?php

namespace Gunreip\TranslationWorkbench\Support;

use PDO;
use RuntimeException;
use Throwable;

/**
 * Requires an explicitly supplied PostgreSQL connection with the new-generation schema.
 * Recording a proposal never applies a correction; no default connection is resolved.
 */
final class KeyCorrectionProposal
{
    private PDO $db;

    public function __construct(private KeyCorrectionDatabase $database)
    {
        $this->db = $database->connection;
    }

    public function preview(string $baseline, string $source, string $target): array
    {
        $this->db->beginTransaction();
        try {
            $this->db->exec('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ, READ ONLY');
            $result = $this->context($baseline, $source, $target);
            $this->db->commit();

            return $result;
        } catch (Throwable $error) {
            $this->db->rollBack();
            throw $error;
        }
    }

    public function record(string $operation, string $baseline, string $source, string $target, string $expected, int $actor, string $reason): array
    {
        if ($actor < 1 || trim($reason) === '') {
            throw new RuntimeException('Actor and reason are required');
        }
        $this->db->beginTransaction();
        try {
            $this->database->assertReady($baseline);
            $this->query('SELECT pg_advisory_xact_lock(hashtextextended(?,0))', ['key-correction-proposal:'.$operation]);
            $existing = $this->query('SELECT * FROM baseline_key_correction_proposals WHERE operation_id=?', [$operation])->fetch(PDO::FETCH_ASSOC);
            if ($existing) {
                if ($existing['baseline_hash'] !== $baseline || $existing['source_key'] !== $source
                    || $existing['target_key'] !== $target || $existing['context_fingerprint'] !== $expected
                    || (int) $existing['actor_id'] !== $actor || $existing['reason'] !== $reason) {
                    throw new RuntimeException('Operation request changed');
                }
                $this->db->commit();

                return ['operation_id' => $operation, 'replayed' => true];
            }
            // Coarse locks prevent source/link/name changes between check and insert.
            $this->db->exec('LOCK TABLE translation_workbench_source_files, translation_workbench_findings, translation_workbench_key_findings, translation_workbench_key_names IN SHARE MODE');
            $context = $this->context($baseline, $source, $target);
            if (! hash_equals($context['fingerprint'], $expected)) {
                throw new RuntimeException('Proposal context changed');
            }
            $this->query('INSERT INTO baseline_key_correction_proposals(operation_id,baseline_hash,source_key,target_key,actor_id,reason,context_fingerprint,context) VALUES (?,?,?,?,?,?,?,?::jsonb)',
                [$operation, $baseline, $source, $target, $actor, $reason, $expected, json_encode($context['evidence'], JSON_THROW_ON_ERROR)]);
            $this->db->commit();

            return ['operation_id' => $operation, 'replayed' => false];
        } catch (Throwable $error) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $error;
        }
    }

    /** Shared validation for preview, recording and application within the caller's transaction. */
    public function context(string $baseline, string $source, string $target): array
    {
        if (! $this->db->inTransaction()) {
            throw new RuntimeException('Proposal context requires a transaction');
        }
        $this->database->assertReady($baseline);
        if ($source === $target || preg_match('/^[a-zA-Z0-9_-]+(?:\.[a-zA-Z0-9_-]+){2,}$/D', $target) !== 1) {
            throw new RuntimeException('Invalid correction target');
        }
        $values = $this->query('SELECT full_name,locale,path,value,state FROM published_value_evidence WHERE baseline_hash=? AND full_name=? ORDER BY locale,path', [$baseline, $source])->fetchAll(PDO::FETCH_ASSOC);
        if ($values === [] || array_unique(array_column($values, 'state')) !== ['blocked_structure']) {
            throw new RuntimeException('Expected a structurally blocked baseline key');
        }
        $sources = $this->query("SELECT f.id,f.source_file_id,s.path AS source_path,f.source_line,f.source_column,f.status,f.raw_expression,f.raw_source_code_literal,
            o.capture_id,o.import_hash FROM existing_key_observations o
            JOIN translation_workbench_findings f ON f.id=o.finding_id
            JOIN translation_workbench_source_files s ON s.id=f.source_file_id
            WHERE o.state='blocked_structure' AND f.raw_source_code_literal=? ORDER BY f.id", [$source])->fetchAll(PDO::FETCH_ASSOC);
        if ($sources === [] || array_filter($sources, fn ($row) => $row['status'] !== 'active')) {
            throw new RuntimeException('Source evidence is missing or inactive');
        }
        $active = $this->query("SELECT id FROM translation_workbench_findings WHERE raw_source_code_literal=? AND status='active' ORDER BY id", [$source])->fetchAll(PDO::FETCH_COLUMN);
        if (array_map('intval', $active) !== array_map('intval', array_column($sources, 'id'))) {
            throw new RuntimeException('Active source set changed');
        }
        foreach ($sources as $row) {
            if ($this->query('SELECT 1 FROM translation_workbench_key_findings WHERE finding_id=? AND valid_to IS NULL', [$row['id']])->fetchColumn()) {
                throw new RuntimeException('Source assignment already changed');
            }
        }
        $inventory = (new BaselineNameInventory)->read($this->db, $this->database->schema, $baseline);
        $reserved = array_column(array_filter($inventory['names'], fn ($row) => $row['namespace'] === ''), 'full_name');
        $names = $this->query("SELECT full_name FROM published_value_evidence WHERE baseline_hash=?
            UNION SELECT full_name FROM translation_workbench_key_names WHERE namespace='' AND valid_to IS NULL", [$baseline])->fetchAll(PDO::FETCH_COLUMN);
        foreach (array_unique([...$reserved, ...$names]) as $name) {
            if ($name === $source) {
                continue;
            }
            if ($name === $target || str_starts_with($name, $target.'.') || str_starts_with($target, $name.'.')) {
                throw new RuntimeException('Target name or array path is occupied');
            }
        }
        $evidence = ['baseline_hash' => $baseline, 'source' => $source, 'target' => $target,
            'sources' => $sources, 'published_values' => $values, 'name_inventory_sha256' => $inventory['sha256']];

        return ['evidence' => $evidence, 'fingerprint' => hash('sha256', json_encode($evidence, JSON_THROW_ON_ERROR))];
    }

    private function query(string $sql, array $parameters = []): \PDOStatement
    {
        $statement = $this->db->prepare($sql);
        $statement->execute($parameters);

        return $statement;
    }
}
