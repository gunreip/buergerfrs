<?php

namespace Tests\Support\TranslationWorkbench;

use Gunreip\TranslationWorkbench\Support\BaselineNameInventory;
use Gunreip\TranslationWorkbench\Support\BaselineProposalEvidence;
use Illuminate\Support\Str;
use PDO;
use RuntimeException;
use Throwable;

/** Isolated transfer rehearsal for one explicitly selected blocked key; not a production importer. */
final class SelectedGenerationTransfer
{
    public function transfer(PDO $db, string $sourceSchema, string $targetSchema, string $generation, array $snapshot, array $context, ?callable $fault = null): array
    {
        foreach ([$sourceSchema, $targetSchema] as $schema) {
            if (preg_match('/^[a-z_][a-z0-9_]{0,62}$/D', $schema) !== 1) {
                throw new RuntimeException('Invalid explicit schema');
            }
        }
        if ($sourceSchema === $targetSchema || $db->inTransaction()) {
            throw new RuntimeException('A separate empty target outside a transaction is required');
        }
        $evidence = $context['evidence'];
        $source = $evidence['source'];
        (new BaselineProposalEvidence)->compare($snapshot, $source, $evidence['target'], $context);
        $plan = ['kind' => 'selected_blocked_key_transfer', 'generation' => $generation,
            'source_schema' => $sourceSchema, 'source_baseline' => $snapshot['baseline_hash'],
            'snapshot_sha256' => hash('sha256', json_encode($snapshot, JSON_THROW_ON_ERROR)),
            'context' => $context];
        $hash = hash('sha256', json_encode($plan, JSON_THROW_ON_ERROR));
        $json = fn ($value) => json_encode($value, JSON_THROW_ON_ERROR);
        $query = static function (string $sql, array $parameters = []) use ($db) {
            $statement = $db->prepare($sql);
            $statement->execute($parameters);

            return $statement;
        };
        $db->beginTransaction();
        try {
            $db->exec('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ');
            $nameService = new BaselineNameInventory;
            $inventory = $nameService->read($db, $sourceSchema, $snapshot['baseline_hash']);
            if ($inventory['sha256'] !== ($evidence['name_inventory_sha256'] ?? null)) {
                throw new RuntimeException('Source name inventory changed');
            }
            if ($nameService->capture($db, $sourceSchema, $snapshot['baseline_hash'])['sha256'] !== $inventory['sha256']) {
                throw new RuntimeException('Current source names differ from the recorded inventory');
            }
            $db->exec("LOCK TABLE $targetSchema.workbench_generation IN EXCLUSIVE MODE");
            if ($query("SELECT generation FROM $targetSchema.workbench_generation")->fetchColumn() !== $generation) {
                throw new RuntimeException('Wrong target generation');
            }
            $tables = $query("SELECT tablename FROM pg_catalog.pg_tables WHERE schemaname=? AND tablename<>'workbench_generation'", [$targetSchema])->fetchAll(PDO::FETCH_COLUMN);
            foreach ($tables as $table) {
                if ($query('SELECT EXISTS(SELECT 1 FROM "'.$targetSchema.'"."'.str_replace('"', '""', $table).'")')->fetchColumn()) {
                    throw new RuntimeException('Target generation is not empty');
                }
            }
            $currentValues = $query("SELECT full_name,locale,path,value,state FROM $sourceSchema.published_value_evidence
                WHERE baseline_hash=? AND full_name=? ORDER BY locale,path", [$snapshot['baseline_hash'], $source])->fetchAll(PDO::FETCH_ASSOC);
            if ($currentValues !== $evidence['published_values']) {
                throw new RuntimeException('Published evidence changed before transfer');
            }
            $activeIds = array_map('intval', $query("SELECT id FROM $sourceSchema.translation_workbench_findings
                WHERE raw_source_code_literal=? AND status='active' ORDER BY id", [$source])->fetchAll(PDO::FETCH_COLUMN));
            if ($activeIds !== array_map('intval', array_column($evidence['sources'], 'id'))) {
                throw new RuntimeException('Selected active finding set changed');
            }
            if ($query("SELECT 1 FROM $sourceSchema.translation_workbench_key_findings l
                JOIN $sourceSchema.translation_workbench_findings f ON f.id=l.finding_id
                WHERE f.raw_source_code_literal=? AND f.status='active' AND l.valid_to IS NULL LIMIT 1", [$source])->fetchColumn()) {
                throw new RuntimeException('Selected findings already have active key assignments');
            }
            $scan = (string) Str::uuid();
            $query("INSERT INTO $targetSchema.translation_workbench_scan_runs(id,status,scope_snapshot) VALUES (?,'completed',?::jsonb)",
                [$scan, $json(['kind' => 'evidence_transfer_not_new_scan', 'plan' => $plan])]);
            $query("INSERT INTO $targetSchema.capture_imports(manifest_sha256,candidates_sha256,scan_id,totals) VALUES (?,?,?,?::jsonb)",
                [$hash, $context['fingerprint'], $scan, $json(['selected_findings' => count($evidence['sources'])])]);
            $query("INSERT INTO $targetSchema.existing_key_imports(hash,manifest_sha256,plan) VALUES (?,?,?::jsonb)",
                [$hash, $hash, $json($plan)]);
            $files = [];
            $mapping = [];
            foreach ($evidence['sources'] as $observed) {
                $row = $query("SELECT f.*,s.path FROM $sourceSchema.translation_workbench_findings f
                    JOIN $sourceSchema.translation_workbench_source_files s ON s.id=f.source_file_id WHERE f.id=?", [$observed['id']])->fetch(PDO::FETCH_ASSOC);
                if (! $row || $row['status'] !== 'active' || $row['raw_expression'] !== $observed['raw_expression']
                    || $row['raw_source_code_literal'] !== $source || $row['path'] !== $observed['source_path']
                    || (int) $row['source_line'] !== (int) $observed['source_line']
                    || (int) $row['source_column'] !== (int) $observed['source_column']) {
                    throw new RuntimeException('Selected capture changed before transfer');
                }
                if (! isset($files[$row['path']])) {
                    $files[$row['path']] = (int) $query("INSERT INTO $targetSchema.translation_workbench_source_files(path) VALUES (?) RETURNING id", [$row['path']])->fetchColumn();
                }
                $provenance = ['transfer' => ['plan_sha256' => $hash, 'source_schema' => $sourceSchema,
                    'source_baseline' => $snapshot['baseline_hash'], 'finding_id' => (int) $row['id'],
                    'captured_scan_id' => $row['captured_scan_id'], 'predecessor_id' => $row['predecessor_id']],
                    'original_provenance' => json_decode($row['provenance'], true, flags: JSON_THROW_ON_ERROR)];
                $id = (int) $query("INSERT INTO $targetSchema.translation_workbench_findings
                    (source_file_id,captured_scan_id,source_line,source_column,raw_expression,raw_source_code_literal,
                    raw_literal,suggested_literal,suggested_key,provenance) VALUES (?,?,?,?,?,?,?,?,?,?::jsonb) RETURNING id",
                    [$files[$row['path']], $scan, $row['source_line'], $row['source_column'], $row['raw_expression'],
                        $row['raw_source_code_literal'], $row['raw_literal'], $row['suggested_literal'], $row['suggested_key'], $json($provenance)])->fetchColumn();
                $mapping[(int) $row['id']] = $id;
                $capture = 'transferred:'.$row['id'];
                $query("INSERT INTO $targetSchema.capture_outcomes(manifest_sha256,capture_id,disposition,reason,finding_id)
                    VALUES (?,?,'captured','Explicit selected evidence transfer',?)", [$hash, $capture, $id]);
                $query("INSERT INTO $targetSchema.translation_workbench_scan_findings(scan_run_id,finding_id) VALUES (?,?)", [$scan, $id]);
                $query("INSERT INTO $targetSchema.existing_key_observations(import_hash,manifest_sha256,capture_id,finding_id,state)
                    VALUES (?,?,?,?,'blocked_structure')", [$hash, $hash, $capture, $id]);
                if ($fault) {
                    $fault(count($mapping));
                }
            }
            $backup = $query("SELECT backup_manifest_sha256 FROM $sourceSchema.published_value_baselines WHERE hash=?", [$snapshot['baseline_hash']])->fetchColumn();
            $query("INSERT INTO $targetSchema.published_value_baselines(hash,backup_manifest_sha256,excluded_sources,totals) VALUES (?,?,?::jsonb,?::jsonb)",
                [$hash, $backup, $json(['scope' => 'selected_key_only', 'source_baseline' => $snapshot['baseline_hash']]),
                    $json(['selected_key' => $source, 'values' => count($evidence['published_values'])])]);
            $nameService->store($db, $targetSchema, $hash, $snapshot['baseline_hash'], $inventory);
            $paths = [];
            foreach ($evidence['published_values'] as $value) {
                if ($value['state'] !== 'blocked_structure') {
                    throw new RuntimeException('Transfer requires blocked evidence, not active assignments');
                }
                if (! isset($paths[$value['path']])) {
                    $query("INSERT INTO $targetSchema.published_value_sources(baseline_hash,path,sha256,locale,file_group)
                        SELECT ?,path,sha256,locale,file_group FROM $sourceSchema.published_value_sources WHERE baseline_hash=? AND path=?",
                        [$hash, $snapshot['baseline_hash'], $value['path']]);
                    $paths[$value['path']] = true;
                }
                $query("INSERT INTO $targetSchema.published_value_evidence(baseline_hash,path,locale,full_name,value,state)
                    VALUES (?,?,?,?,?,'blocked_structure')", [$hash, $value['path'], $value['locale'], $source, $value['value']]);
            }
            $db->commit();
            $newSnapshot = $snapshot;
            $newSnapshot['baseline_hash'] = $hash;
            $newSnapshot['items'] = [$source => $snapshot['items'][$source]];
            $newSnapshot['name_inventory'] = $inventory;
            $newSnapshot['selection'] = ['source_baseline' => $snapshot['baseline_hash'], 'generation' => $generation, 'plan_sha256' => $hash];
            unset($newSnapshot['unassigned_counts']);
            foreach ($newSnapshot['items'][$source]['blocked_sources'] as &$finding) {
                $finding['finding_id'] = $mapping[$finding['finding_id']];
            }
            unset($finding);

            return ['snapshot' => $newSnapshot, 'finding_map' => $mapping, 'plan_sha256' => $hash, 'source_files' => count($files)];
        } catch (Throwable $error) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            throw $error;
        }
    }
}
