<?php

namespace Gunreip\TranslationWorkbench\Support;

use PDO;
use RuntimeException;
use Throwable;

/**
 * Applies to the explicitly supplied new-generation PostgreSQL connection.
 * Source and published language files are checked, never rewritten.
 * The caller supplies the snapshot, source root and authenticated actor.
 */
final class KeyCorrectionApplication
{
    private PDO $db;

    public function __construct(private KeyCorrectionDatabase $database, private array $snapshot, private string $sourceRoot)
    {
        $this->db = $database->connection;
    }

    public function apply(string $operation, string $proposal, string $expectedPreflight, int $actor, string $reason, ?callable $fault = null): array
    {
        if ($actor < 1 || trim($reason) === '') {
            throw new RuntimeException('Actor and reason are required');
        }
        $kind = 'key_correction_applied';
        $request = hash('sha256', json_encode([$kind, $proposal, $expectedPreflight,
            hash('sha256', json_encode($this->snapshot, JSON_THROW_ON_ERROR)), $actor, $reason], JSON_THROW_ON_ERROR));
        $this->db->beginTransaction();
        try {
            $this->database->assertReady($this->snapshot['baseline_hash'] ?? '', application: true);
            $this->query('SELECT pg_advisory_xact_lock(hashtextextended(?,0))', ['operation:'.$operation]);
            $previous = $this->query('SELECT * FROM translation_workbench_operations WHERE id=?', [$operation])->fetch(PDO::FETCH_ASSOC);
            if ($previous) {
                if ($previous['kind'] !== $kind || $previous['request_fingerprint'] !== $request) {
                    throw new RuntimeException('Operation request changed');
                }
                $this->db->commit();

                return json_decode($previous['result'], true, flags: JSON_THROW_ON_ERROR);
            }
            // Serializes against assignment/name writers as well as competing applications.
            $this->db->exec('LOCK TABLE translation_workbench_source_files, translation_workbench_findings, translation_workbench_key_findings, translation_workbench_key_names IN SHARE ROW EXCLUSIVE MODE');
            $record = $this->query('SELECT * FROM baseline_key_correction_proposals WHERE operation_id=? FOR UPDATE', [$proposal])->fetch(PDO::FETCH_ASSOC);
            if (! $record) {
                throw new RuntimeException('Unknown proposal');
            }
            if ($this->query('SELECT 1 FROM baseline_key_correction_applications WHERE proposal_id=?', [$proposal])->fetchColumn()) {
                throw new RuntimeException('Proposal already applied');
            }
            $context = (new KeyCorrectionProposal($this->database))->context($record['baseline_hash'], $record['source_key'], $record['target_key']);
            if (! hash_equals($record['context_fingerprint'], $context['fingerprint'])) {
                throw new RuntimeException('Proposal context changed');
            }
            $preflight = (new BaselineCorrectionPreflight)->inspect($this->snapshot, $record['source_key'],
                $record['target_key'], $this->sourceRoot,
                fn () => (new KeyCorrectionProposal($this->database))->context($record['baseline_hash'], $record['source_key'], $record['target_key']));
            if (! hash_equals($expectedPreflight, $preflight['fingerprint'])) {
                throw new RuntimeException('Confirmed preflight changed');
            }
            $key = (int) $this->query('INSERT INTO translation_workbench_keys DEFAULT VALUES RETURNING id')->fetchColumn();
            $this->query("INSERT INTO translation_workbench_key_names(key_id,namespace,full_name) VALUES (?,'',?)", [$key, $record['target_key']]);
            $links = [];
            foreach ($context['evidence']['sources'] as $source) {
                $link = (int) $this->query('INSERT INTO translation_workbench_key_findings(finding_id,key_id) VALUES (?,?) RETURNING id', [$source['id'], $key])->fetchColumn();
                $links[] = ['finding' => (int) $source['id'], 'link' => $link];
                if ($fault !== null) {
                    $fault(count($links));
                }
            }
            // Detect a file change during the DB writes, without rewriting or repairing that file.
            $files = (new BaselineFileFreshness)->inspect($this->snapshot['items'][$record['source_key']], $this->sourceRoot);
            if (! $files['matches']) {
                throw new RuntimeException('Referenced files changed before commit');
            }
            $result = ['proposal' => $proposal, 'key' => $key, 'links' => $links,
                'preflight_fingerprint' => $preflight['fingerprint']];
            $this->query('INSERT INTO baseline_key_correction_applications(operation_id,proposal_id,key_id,links) VALUES (?,?,?,?::jsonb)', [$operation, $proposal, $key, json_encode($links, JSON_THROW_ON_ERROR)]);
            $this->query('INSERT INTO translation_workbench_events(operation_id,event_type,actor_id,reason,details) VALUES (?,?,?,?,?::jsonb)',
                [$operation, $kind, $actor, $reason, json_encode([...$result, 'source_key' => $record['source_key'],
                    'target_key' => $record['target_key'], 'baseline_hash' => $record['baseline_hash'],
                    'context_fingerprint' => $context['fingerprint'], 'source_fingerprint' => $preflight['source_fingerprint'],
                    'snapshot_sha256' => $preflight['snapshot_sha256']], JSON_THROW_ON_ERROR)]);
            $this->query('INSERT INTO translation_workbench_operations(id,kind,request_fingerprint,result) VALUES (?,?,?,?::jsonb)', [$operation, $kind, $request, json_encode($result, JSON_THROW_ON_ERROR)]);
            $this->db->commit();

            return $result;
        } catch (Throwable $error) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $error;
        }
    }

    private function query(string $sql, array $parameters = []): \PDOStatement
    {
        $statement = $this->db->prepare($sql);
        $statement->execute($parameters);

        return $statement;
    }
}
