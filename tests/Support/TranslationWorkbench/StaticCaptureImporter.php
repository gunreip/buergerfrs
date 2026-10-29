<?php

namespace Tests\Support\TranslationWorkbench;

use Illuminate\Support\Str;
use PDO;
use RuntimeException;

/** Rehearsal-only import into a fresh baseline database, not a production service. */
final class StaticCaptureImporter
{
    public function installLedger(PDO $db): void
    {
        $db->exec(file_get_contents(dirname(__DIR__, 3).'/packages/gunreip/laravel-translation-workbench/database/generations/v1/capture-ledger.sql'));
    }

    public function import(PDO $db, string $manifestPath, string $expectedHash, string $candidateHash): array
    {
        if (hash_file('sha256', $manifestPath) !== $expectedHash) {
            throw new RuntimeException('Capture manifest changed');
        }
        $manifest = json_decode(file_get_contents($manifestPath), true, flags: JSON_THROW_ON_ERROR);
        $candidates = dirname($manifestPath).'/candidates.jsonl';
        if ($manifest['state'] !== 'candidates_captured' || $manifest['production_connected'] || $manifest['active_generation_created']
            || $manifest['candidate_file_sha256'] !== $candidateHash || hash_file('sha256', $candidates) !== $candidateHash) {
            throw new RuntimeException('Unverified capture');
        }
        $query = static function (string $sql, array $params = []) use ($db): \PDOStatement {
            $stmt = $db->prepare($sql);
            $stmt->execute($params);

            return $stmt;
        };
        $db->beginTransaction();
        try {
            // Serialize repeated requests; an identical retry must not generate new history.
            $db->exec('LOCK TABLE capture_imports IN EXCLUSIVE MODE');
            $existing = $query('SELECT candidates_sha256,totals FROM capture_imports WHERE manifest_sha256=?', [$expectedHash])->fetch(PDO::FETCH_ASSOC);
            if ($existing) {
                if ($existing['candidates_sha256'] !== $candidateHash) {
                    throw new RuntimeException('Import identity conflict');
                }
                $db->rollBack();

                return [...json_decode($existing['totals'], true, flags: JSON_THROW_ON_ERROR), 'replayed' => true];
            }
            $scan = (string) Str::uuid();
            $snapshot = ['mode' => 'isolated_static_capture', 'capture_manifest_sha256' => $expectedHash,
                'backup_manifest_sha256' => $manifest['backup_manifest_sha256'], 'scope' => $manifest['scope'],
                'files' => $manifest['files'], 'scanner_code_sha256' => $manifest['scanner_code_sha256'],
                'support_code_sha256' => $manifest['support_code_sha256'], 'discovery_is_exhaustive' => false];
            $query("INSERT INTO translation_workbench_scan_runs(id,status,scope_snapshot) VALUES (?,'completed',?)", [$scan, json_encode($snapshot, JSON_THROW_ON_ERROR)]);
            $files = [];
            foreach ($manifest['files'] as $path => $info) {
                $files[$path] = $query('INSERT INTO translation_workbench_source_files(path) VALUES (?) ON CONFLICT(path) DO UPDATE SET path=excluded.path RETURNING id', [$path])->fetchColumn();
            }
            $totals = ['captured' => 0, 'excluded' => 0, 'pending' => 0];
            $reasons = [];
            $seen = [];
            $stream = fopen($candidates, 'r');
            try {
                while (($line = fgets($stream)) !== false) {
                    $row = json_decode($line, true, flags: JSON_THROW_ON_ERROR);
                    $id = $row['capture_id'];
                    $path = $row['source_path'];
                    $expectedId = hash('sha256', json_encode([$manifest['backup_manifest_sha256'], $path, $row['source_sha256'], $row['source_offset']], JSON_THROW_ON_ERROR));
                    if (isset($seen[$id]) || $id !== $expectedId || ! isset($files[$path]) || $manifest['files'][$path]['sha256'] !== $row['source_sha256']) {
                        throw new RuntimeException('Invalid or duplicate capture provenance');
                    }
                    $seen[$id] = true;
                    $reasons[$row['reason']] = ($reasons[$row['reason']] ?? 0) + 1;
                    $disposition = 'pending';
                    if (in_array($row['status'], ['excluded_comment', 'excluded_string', 'excluded_verbatim', 'excluded_escaped_blade'], true)) {
                        $disposition = 'excluded';
                    } elseif ($row['source_context']['state'] === 'confirmed_call'
                        && in_array($row['reason'], ['literal_candidate', 'key_with_exact_lang_evidence'], true)) {
                        $disposition = 'captured';
                    }
                    $finding = null;
                    if ($disposition === 'captured') {
                        if (! is_string($row['raw_expression']) || ! is_string($row['raw_source_code_literal'])
                            || ($row['reason'] === 'literal_candidate' && (! is_string($row['raw_literal']) || ! is_string($row['suggested_key'])))
                            || ($row['reason'] === 'key_with_exact_lang_evidence' && ($row['raw_literal'] !== null || $row['suggested_key'] !== null || ! isset($row['provenance']['suggested_literal'])))) {
                            throw new RuntimeException('Invalid immutable capture fields');
                        }
                        $provenance = [...$row['provenance'], 'capture_id' => $id, 'capture_manifest_sha256' => $expectedHash,
                            'source_sha256' => $row['source_sha256'], 'source_offset' => $row['source_offset'],
                            'source_context' => $row['source_context'], 'found_translation_key' => $row['found_translation_key'],
                            'classification' => $row['reason'], 'mode' => 'isolated_rehearsal'];
                        $finding = $query('INSERT INTO translation_workbench_findings(source_file_id,captured_scan_id,source_line,source_column,raw_expression,raw_source_code_literal,raw_literal,suggested_literal,suggested_key,provenance) VALUES (?,?,?,?,?,?,?,?,?,?) RETURNING id',
                            [$files[$path], $scan, $row['source_line'], $row['source_column'], $row['raw_expression'], $row['raw_source_code_literal'], $row['raw_literal'], $row['suggested_literal'], $row['suggested_key'], json_encode($provenance, JSON_THROW_ON_ERROR)])->fetchColumn();
                        $query('INSERT INTO translation_workbench_scan_findings VALUES (?,?)', [$scan, $finding]);
                    }
                    $query('INSERT INTO capture_outcomes VALUES (?,?,?,?,?)', [$expectedHash, $id, $disposition, $row['reason'], $finding]);
                    $totals[$disposition]++;
                }
            } finally {
                fclose($stream);
            }
            ksort($reasons);
            $expectedReasons = $manifest['counts'];
            ksort($expectedReasons);
            if ($reasons !== $expectedReasons || hash_file('sha256', $candidates) !== $candidateHash || hash_file('sha256', $manifestPath) !== $expectedHash) {
                throw new RuntimeException('Capture changed or accounting differs');
            }
            $query('INSERT INTO capture_imports VALUES (?,?,?,?)', [$expectedHash, $candidateHash, $scan, json_encode($totals, JSON_THROW_ON_ERROR)]);
            // The completed scan and immutable ledger document this rehearsal; no fabricated user actor/history.
            $db->commit();

            return [...$totals, 'replayed' => false];
        } catch (\Throwable $error) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            throw $error;
        }
    }
}
