<?php

namespace Gunreip\TranslationWorkbench\Generation;

use PDO;
use RuntimeException;
use Throwable;

/** Shared idempotent transaction boundary for generation writers. */
final class BaselineOperation
{
    public function __construct(private PDO $db) {}

    public function run(string $id, string $kind, array $payload, callable $action): array
    {
        $fingerprint = hash('sha256', json_encode([$kind, $payload], JSON_THROW_ON_ERROR));
        $savepoint = $this->db->inTransaction() ? 'tw_operation_'.bin2hex(random_bytes(8)) : null;
        if ($savepoint !== null) {
            $this->db->exec('SAVEPOINT '.$savepoint);
        } else {
            $this->db->beginTransaction();
        }
        try {
            // Transaction-scoped serialization also covers a not-yet-existing operation row.
            $this->query('SELECT pg_advisory_xact_lock(hashtextextended(?, 0))', ['operation:'.$id]);
            $previous = $this->query('SELECT * FROM translation_workbench_operations WHERE id=?', [$id])->fetch(PDO::FETCH_ASSOC);
            if ($previous) {
                if ($previous['request_fingerprint'] !== $fingerprint) {
                    throw new RuntimeException('Operation request changed');
                }
                $result = json_decode($previous['result'], true, flags: JSON_THROW_ON_ERROR);
            } else {
                $result = $action();
                $this->query('INSERT INTO translation_workbench_operations(id,kind,request_fingerprint,result) VALUES (?,?,?,?)', [$id, $kind, $fingerprint, json_encode($result, JSON_THROW_ON_ERROR)]);
            }
            if ($savepoint !== null) {
                $this->db->exec('RELEASE SAVEPOINT '.$savepoint);
            } else {
                $this->db->commit();
            }

            return $result;
        } catch (Throwable $error) {
            if ($savepoint !== null) {
                $this->db->exec('ROLLBACK TO SAVEPOINT '.$savepoint);
                $this->db->exec('RELEASE SAVEPOINT '.$savepoint);
            } else {
                $this->db->rollBack();
            }
            throw $error;
        }
    }

    private function query(string $sql, array $params): \PDOStatement
    {
        $statement = $this->db->prepare($sql);
        $statement->execute($params);

        return $statement;
    }
}
