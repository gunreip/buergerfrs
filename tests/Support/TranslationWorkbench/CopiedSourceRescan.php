<?php

namespace Tests\Support\TranslationWorkbench;

use Gunreip\TranslationWorkbench\Generation\CopiedSourceCapture;
use Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchScanner;
use Illuminate\Support\Str;
use PDO;
use RuntimeException;
use Throwable;

/** Rehearsal only. Reconcile one successfully read file; never infer removal outside it. */
final class CopiedSourceRescan
{
    public function scan(PDO $db, TranslationWorkbenchScanner $scanner, string $absolutePath, string $relativePath, array $lang = []): array
    {
        if (! is_file($absolutePath)) {
            throw new RuntimeException('Missing file does not authorize removal');
        }
        $eligible = $scanner->scannableFiles([$relativePath]);
        if (count($eligible) !== 1 || $eligible[0]->getRealPath() !== realpath($absolutePath)) {
            throw new RuntimeException('Excluded file does not authorize removal');
        }
        $bytes = file_get_contents($absolutePath);
        $hash = hash('sha256', $bytes);
        $adapter = new CopiedSourceCapture;
        $rows = [];
        $excluded = 0;
        foreach ($scanner->scanFile($absolutePath) as $item) {
            if ($item->sourcePath !== $relativePath) {
                throw new RuntimeException('Scan scope differs');
            }
            $row = $adapter->capture($item, $bytes, $hash, $lang);
            if (str_starts_with($row['status'], 'excluded_')) {
                $excluded++;

                continue;
            }
            if ($row['source_context']['state'] !== 'confirmed_call' || ! in_array($row['reason'], ['literal_candidate', 'key_with_exact_lang_evidence'], true)) {
                throw new RuntimeException('Unresolved capture blocks reconciliation');
            }
            $position = $row['source_line'].':'.$row['source_column'];
            if (isset($rows[$position])) {
                throw new RuntimeException('Duplicate position');
            }
            $rows[$position] = $row;
        }
        $query = static function (string $sql, array $params = []) use ($db): \PDOStatement {
            $statement = $db->prepare($sql);
            $statement->execute($params);

            return $statement;
        };
        $db->beginTransaction();
        try {
            $query('SELECT pg_advisory_xact_lock(hashtextextended(?,0))', ['rescan:'.$relativePath]);
            $file = $query('SELECT id FROM translation_workbench_source_files WHERE path=? FOR UPDATE', [$relativePath])->fetchColumn();
            if ($file === false) {
                $file = $query('INSERT INTO translation_workbench_source_files(path) VALUES (?) RETURNING id', [$relativePath])->fetchColumn();
            }
            $old = $query("SELECT * FROM translation_workbench_findings WHERE source_file_id=? AND status='active' FOR UPDATE", [$file])->fetchAll(PDO::FETCH_ASSOC);
            $unmatched = array_column($old, null, 'id');
            $remaining = $rows;
            $confirmed = [];
            foreach ($old as $finding) {
                $position = $finding['source_line'].':'.$finding['source_column'];
                if (isset($rows[$position]) && $finding['raw_expression'] === $rows[$position]['raw_expression']) {
                    $confirmed[] = (int) $finding['id'];
                    unset($unmatched[$finding['id']], $remaining[$position]);
                }
            }
            // Movement is inferred only from exactly one unmatched old/new expression in this file.
            $oldByExpression = [];
            $newByExpression = [];
            foreach ($unmatched as $id => $finding) {
                $oldByExpression[$finding['raw_expression']][] = $id;
            }
            foreach ($remaining as $position => $row) {
                $newByExpression[$row['raw_expression']][] = $position;
            }
            $moves = [];
            foreach ($newByExpression as $expression => $positions) {
                $ids = $oldByExpression[$expression] ?? [];
                if ($ids && (count($ids) !== 1 || count($positions) !== 1)) {
                    throw new RuntimeException('Ambiguous movement blocks reconciliation');
                }
                if (count($ids) === 1) {
                    $moves[$positions[0]] = $unmatched[$ids[0]];
                }
            }
            $scan = (string) Str::uuid();
            $totals = ['confirmed' => count($confirmed), 'moved' => count($moves), 'created' => count($remaining) - count($moves),
                'removed' => count($unmatched) - count($moves), 'excluded' => $excluded];
            $snapshot = ['mode' => 'isolated_file_rescan', 'path' => $relativePath, 'sha256' => $hash, 'totals' => $totals,
                'movement_policy' => 'unique_unmatched_expression_same_file', 'discovery_is_exhaustive' => false];
            $query("INSERT INTO translation_workbench_scan_runs VALUES (?,'completed',?,now())", [$scan, json_encode($snapshot, JSON_THROW_ON_ERROR)]);
            $movingIds = array_column($moves, 'id');
            $links = [];
            foreach ($unmatched as $id => $finding) {
                $links[$id] = $query('SELECT key_id FROM translation_workbench_key_findings WHERE finding_id=? AND valid_to IS NULL FOR UPDATE', [$id])->fetchColumn();
                $query("UPDATE translation_workbench_findings SET status='obsolete',retired_at=now(),retired_reason=? WHERE id=?", [in_array($id, $movingIds) ? 'Unique same-file movement' : 'Absent from completed file scan', $id]);
                $query('UPDATE translation_workbench_key_findings SET valid_to=now() WHERE finding_id=? AND valid_to IS NULL', [$id]);
            }
            foreach ($remaining as $position => $row) {
                $predecessor = $moves[$position] ?? null;
                $provenance = [...$row['provenance'], 'source_sha256' => $hash, 'source_offset' => $row['source_offset'],
                    'source_context' => $row['source_context'], 'found_translation_key' => $row['found_translation_key'],
                    'classification' => $row['reason'], 'mode' => 'isolated_rehearsal_rescan'];
                $id = (int) $query('INSERT INTO translation_workbench_findings(source_file_id,captured_scan_id,predecessor_id,source_line,source_column,raw_expression,raw_source_code_literal,raw_literal,suggested_literal,suggested_key,provenance) VALUES (?,?,?,?,?,?,?,?,?,?,?) RETURNING id',
                    [$file, $scan, $predecessor['id'] ?? null, $row['source_line'], $row['source_column'], $row['raw_expression'],
                        $row['raw_source_code_literal'], $row['raw_literal'], $row['suggested_literal'],
                        $predecessor ? $predecessor['suggested_key'] : $row['suggested_key'], json_encode($provenance, JSON_THROW_ON_ERROR)])->fetchColumn();
                if ($predecessor && $links[$predecessor['id']] !== false) {
                    $query('INSERT INTO translation_workbench_key_findings(finding_id,key_id) VALUES (?,?)', [$id, $links[$predecessor['id']]]);
                }
                $confirmed[] = $id;
            }
            foreach ($confirmed as $id) {
                $query('INSERT INTO translation_workbench_scan_findings VALUES (?,?)', [$scan, $id]);
            }
            if (hash_file('sha256', $absolutePath) !== $hash) {
                throw new RuntimeException('Source changed during scan');
            }
            $db->commit();

            return [...$totals, 'findings' => $confirmed, 'scan' => $scan];
        } catch (Throwable $error) {
            $db->rollBack();
            throw $error;
        }
    }
}
