<?php

namespace Tests\Support\TranslationWorkbench;

use Gunreip\TranslationWorkbench\Generation\BaselineDraftWriter;
use Gunreip\TranslationWorkbench\Generation\BaselineOperation;
use Gunreip\TranslationWorkbench\Generation\BaselineRevisionReviewer;
use Gunreip\TranslationWorkbench\Generation\RevisionReviewContext;
use PDO;
use PDOStatement;
use RuntimeException;

/** Isolated persistence contract; not a scanner, application repository or production writer. */
final class PostgresBaseline
{
    public readonly PDO $db;

    public function __construct(string $directory, string $application = 'baseline-test')
    {
        $directory = realpath($directory);
        if (! $directory || dirname($directory) !== realpath(sys_get_temp_dir())
            || ! preg_match('/^tw-baseline-pg-[a-f0-9]{16}$/D', basename($directory))
            || ! is_file($directory.'/data/PG_VERSION')) {
            throw new RuntimeException('Only the isolated runner cluster is allowed');
        }
        $this->db = new PDO('pgsql:host='.$directory.'/socket;dbname=baseline_test;user=workbench_test', options: [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        $this->query("SELECT set_config('application_name', ?, false)", [$application]);
        $this->db->exec("SET lock_timeout='8s'; SET statement_timeout='12s'");
    }

    public function query(string $sql, array $params = []): PDOStatement
    {
        $statement = $this->db->prepare($sql);
        $statement->execute($params);

        return $statement;
    }

    public function operation(string $id, string $kind, array $payload, callable $action): array
    {
        return (new BaselineOperation($this->db))->run($id, $kind, $payload, $action);
    }

    /** Applies an explicit, already-reviewed same-file movement; does not guess matching. */
    public function capture(string $operation, string $path, int $line, string $literal, ?int $predecessor = null): array
    {
        return $this->operation($operation, 'capture', [$path, $line, $literal, $predecessor], function () use ($operation, $path, $line, $literal, $predecessor) {
            $this->query('INSERT INTO translation_workbench_source_files(path) VALUES (?) ON CONFLICT DO NOTHING', [$path]);
            $file = $this->query('SELECT id FROM translation_workbench_source_files WHERE path=? FOR UPDATE', [$path])->fetchColumn();
            $this->query("INSERT INTO translation_workbench_scan_runs(id,status,scope_snapshot) VALUES (?,'completed',?)", [$operation, json_encode([$path])]);
            $old = null;
            $link = false;
            if ($predecessor !== null) {
                $old = $this->query('SELECT * FROM translation_workbench_findings WHERE id=? FOR UPDATE', [$predecessor])->fetch(PDO::FETCH_ASSOC);
                if (! $old || $old['status'] !== 'active' || (int) $old['source_file_id'] !== (int) $file || $old['raw_literal'] !== $literal || (int) $old['source_line'] === $line) {
                    throw new RuntimeException('No valid movement evidence');
                }
                $link = $this->query('SELECT key_id FROM translation_workbench_key_findings WHERE finding_id=? AND valid_to IS NULL', [$predecessor])->fetchColumn();
                if ($link !== false) {
                    $this->lockKeys([(int) $link]);
                }
                $this->query("UPDATE translation_workbench_findings SET status='obsolete',retired_at=now(),retired_reason='Verified movement' WHERE id=?", [$predecessor]);
                $this->query('UPDATE translation_workbench_key_findings SET valid_to=now() WHERE finding_id=? AND valid_to IS NULL', [$predecessor]);
            }
            $existing = $this->query("SELECT id,raw_literal FROM translation_workbench_findings WHERE source_file_id=? AND source_line=? AND source_column=1 AND status='active'", [$file, $line])->fetch(PDO::FETCH_ASSOC);
            if ($existing) {
                if ($old || $existing['raw_literal'] !== $literal) {
                    throw new RuntimeException('Occupied position requires explicit retirement');
                }
                $id = (int) $existing['id'];
            } else {
                $id = (int) $this->query('INSERT INTO translation_workbench_findings(source_file_id,captured_scan_id,predecessor_id,source_line,source_column,raw_expression,raw_source_code_literal,raw_literal,suggested_key,provenance) VALUES (?,?,?,?,1,?,?,?,?,?) RETURNING id',
                    [$file, $operation, $predecessor, $line, "__('".$literal."')", $literal, $literal, $old['suggested_key'] ?? 'fixture.source.literal', json_encode(['source' => 'explicit_test_fixture'])])->fetchColumn();
                if ($link !== false) {
                    $this->query('INSERT INTO translation_workbench_key_findings(finding_id,key_id) VALUES (?,?)', [$id, $link]);
                }
                $this->event($operation, $old ? 'finding_moved' : 'finding_captured', 1, 'Explicit integration fixture', ['finding' => $id, 'predecessor' => $predecessor]);
            }
            $this->query('INSERT INTO translation_workbench_scan_findings VALUES (?,?)', [$operation, $id]);

            return ['finding' => $id];
        });
    }

    public function assign(string $operation, int $finding, int $key, ?int $expectedLink, int $actor, string $reason): array
    {
        return $this->operation($operation, 'assign', [$finding, $key, $expectedLink, $actor, $reason], function () use ($operation, $finding, $key, $expectedLink, $actor, $reason) {
            $status = $this->query('SELECT status FROM translation_workbench_findings WHERE id=? FOR UPDATE', [$finding])->fetchColumn();
            if ($status !== 'active') {
                throw new RuntimeException('Finding is not active');
            }
            $link = $this->query('SELECT id,key_id FROM translation_workbench_key_findings WHERE finding_id=? AND valid_to IS NULL', [$finding])->fetch(PDO::FETCH_ASSOC);
            if (($link ? (int) $link['id'] : null) !== $expectedLink) {
                throw new RuntimeException('Assignment context changed');
            }
            $this->lockKeys($link ? [$key, (int) $link['key_id']] : [$key]);
            if ($link && (int) $link['key_id'] === $key) {
                return ['link' => (int) $link['id']];
            }
            $this->query('UPDATE translation_workbench_key_findings SET valid_to=now() WHERE finding_id=? AND valid_to IS NULL', [$finding]);
            $id = (int) $this->query('INSERT INTO translation_workbench_key_findings(finding_id,key_id) VALUES (?,?) RETURNING id', [$finding, $key])->fetchColumn();
            $this->event($operation, 'key_assigned', $actor, $reason, ['finding' => $finding, 'key' => $key]);

            return ['link' => $id];
        });
    }

    public function save(string $operation, int $key, string $locale, string $value, ?int $expectedRevision, int $actor, string $reason, ?string $optionIdentity = null): array
    {
        if ($optionIdentity === '') {
            throw new RuntimeException('Option identity must not be empty');
        }

        return $this->operation($operation, 'save', [$key, $locale, $value, $expectedRevision, $actor, $reason, $optionIdentity],
            fn () => (new BaselineDraftWriter($this->db))->save($operation, $key, $locale, $value, $expectedRevision, $actor, $reason, $optionIdentity));
    }

    private function lockKeys(array $keys): void
    {
        $keys = array_unique($keys);
        sort($keys, SORT_NUMERIC);
        foreach ($keys as $key) {
            if ($this->query('SELECT id FROM translation_workbench_keys WHERE id=? FOR UPDATE', [$key])->fetchColumn() === false) {
                throw new RuntimeException('Unknown key');
            }
        }
    }

    /** One statement gives the dialog a coherent MVCC snapshot, without writing history. */
    public function reviewSnapshot(int $revision): array
    {
        return RevisionReviewContext::snapshot($this->db, $revision);
    }

    public function decide(string $operation, int $revision, string $expectedFingerprint, bool $approve, int $actor, string $reason): array
    {
        return (new BaselineRevisionReviewer($this->db))->decide($operation, $revision, $expectedFingerprint, $approve, $actor, $reason);
    }

    public function approvalIsCurrent(int $revision): bool
    {
        return (new BaselineRevisionReviewer($this->db))->approvalIsCurrent($revision);
    }

    private function event(string $operation, string $type, int $actor, string $reason, array $details): void
    {
        $this->query('INSERT INTO translation_workbench_events(operation_id,event_type,actor_id,reason,details) VALUES (?,?,?,?,?)', [$operation, $type, $actor, $reason, json_encode($details, JSON_THROW_ON_ERROR)]);
    }
}
