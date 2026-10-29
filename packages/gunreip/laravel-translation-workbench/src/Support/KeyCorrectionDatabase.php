<?php

namespace Gunreip\TranslationWorkbench\Support;

use PDO;
use RuntimeException;

/**
 * Explicit target binding, not a connection factory or production authorization.
 * Checks the required schema surface; migrations remain responsible for constraints.
 */
final class KeyCorrectionDatabase
{
    private const TABLES = [
        'published_value_baselines' => ['hash', 'backup_manifest_sha256'],
        'baseline_name_inventories' => ['baseline_hash', 'source_baseline_hash', 'names', 'names_sha256', 'name_count'],
        'published_value_evidence' => ['baseline_hash', 'full_name', 'locale', 'path', 'value', 'state'],
        'existing_key_observations' => ['finding_id', 'state', 'capture_id', 'import_hash'],
        'translation_workbench_source_files' => ['id', 'path'],
        'translation_workbench_findings' => ['id', 'source_file_id', 'source_line', 'source_column', 'status', 'raw_expression', 'raw_source_code_literal'],
        'translation_workbench_keys' => ['id'],
        'translation_workbench_key_findings' => ['id', 'finding_id', 'key_id', 'valid_to'],
        'translation_workbench_key_names' => ['key_id', 'namespace', 'full_name', 'valid_to'],
        'baseline_key_correction_proposals' => ['operation_id', 'baseline_hash', 'source_key', 'target_key', 'actor_id', 'reason', 'context_fingerprint', 'context'],
    ];

    private const APPLICATION_TABLES = [
        'translation_workbench_operations' => ['id', 'kind', 'request_fingerprint', 'result'],
        'translation_workbench_events' => ['operation_id', 'event_type', 'actor_id', 'reason', 'details'],
        'baseline_key_correction_applications' => ['operation_id', 'proposal_id', 'key_id', 'links'],
    ];

    public function __construct(
        public readonly PDO $connection,
        public readonly string $database,
        public readonly string $schema,
        public readonly string $baseline,
        public readonly string $backupManifest,
    ) {
        if ($connection->getAttribute(PDO::ATTR_DRIVER_NAME) !== 'pgsql'
            || $connection->getAttribute(PDO::ATTR_ERRMODE) !== PDO::ERRMODE_EXCEPTION
            || trim($database) === ''
            || preg_match('/^[a-z_][a-z0-9_]*$/D', $schema) !== 1
            || preg_match('/^[a-f0-9]{64}$/D', $baseline) !== 1
            || preg_match('/^[a-f0-9]{64}$/D', $backupManifest) !== 1) {
            throw new RuntimeException('Explicit PostgreSQL database, schema and baseline evidence are required');
        }
    }

    /** Run before any business query, inside the caller's transaction. */
    public function assertReady(string $baseline, bool $application = false): void
    {
        if (! $this->connection->inTransaction()) {
            throw new RuntimeException('Database target verification requires a transaction');
        }
        if ($baseline !== $this->baseline
            || $this->connection->query('SELECT pg_catalog.current_database()')->fetchColumn() !== $this->database) {
            throw new RuntimeException('Database target or baseline differs from the explicit binding');
        }

        $tables = $application ? [...self::TABLES, ...self::APPLICATION_TABLES] : self::TABLES;
        foreach ($tables as $table => $columns) {
            // Fixed table names and validated schema; fully qualified locks prevent DDL races.
            $relation = '"'.$this->schema.'"."'.$table.'"';
            $query = $this->connection->prepare('SELECT c.oid, c.relkind FROM pg_catalog.pg_class c
                JOIN pg_catalog.pg_namespace n ON n.oid=c.relnamespace
                WHERE n.nspname=? AND c.relname=?');
            $query->execute([$this->schema, $table]);
            $row = $query->fetch(PDO::FETCH_ASSOC);
            if (! $row || $row['relkind'] !== 'r') {
                throw new RuntimeException('Required generation table is missing: '.$table);
            }
            $this->connection->exec('LOCK TABLE '.$relation.' IN ACCESS SHARE MODE');
            $resolved = $this->connection->prepare('SELECT pg_catalog.to_regclass(?)::oid');
            $resolved->execute([$table]);
            if ((string) $resolved->fetchColumn() !== (string) $row['oid']) {
                throw new RuntimeException('Generation table is shadowed or not reachable: '.$table);
            }
            $attributes = $this->connection->prepare('SELECT attname FROM pg_catalog.pg_attribute WHERE attrelid=? AND attnum>0 AND NOT attisdropped');
            $attributes->execute([$row['oid']]);
            $missing = array_diff($columns, $attributes->fetchAll(PDO::FETCH_COLUMN));
            if ($missing !== []) {
                throw new RuntimeException('Required generation columns are missing: '.$table.'.'.implode(', ', $missing));
            }
        }
        $evidence = $this->connection->prepare('SELECT backup_manifest_sha256 FROM "'.$this->schema.'".published_value_baselines WHERE hash=?');
        $evidence->execute([$baseline]);
        if ($evidence->fetchColumn() !== $this->backupManifest) {
            throw new RuntimeException('Generation baseline or backup evidence does not match');
        }
    }
}
