<?php

namespace Gunreip\TranslationWorkbench\Generation;

use PDO;
use RuntimeException;

/** Explicit confirmed rewrite reconciliation; no heuristic matching or obsolete reactivation. */
final class CorrectedSourceReconciler
{
    public function __construct(private PDO $db) {}

    public function apply(string $operation, ?int $key, array $transitions, int $actor, string $reason, ?callable $fault = null, bool $movement = false, bool $withinTransaction = false): array
    {
        if ($actor < 1 || trim($reason) === '' || $transitions === []) {
            throw new RuntimeException('Explicit transitions, actor and reason required');
        }

        $action = function () use ($operation, $key, $transitions, $actor, $reason, $fault, $movement) {
            $this->db->exec('LOCK TABLE translation_workbench_findings, translation_workbench_key_findings, translation_workbench_key_names IN SHARE ROW EXCLUSIVE MODE');
            $verified = [];
            $confirmed = [];
            foreach ($transitions as $transition) {
                $transitionKey = array_key_exists('key', $transition) ? $transition['key'] : $key;
                $transitionMovement = $transition['movement'] ?? $movement;
                $this->q('SELECT id FROM translation_workbench_keys WHERE id=? FOR UPDATE', [$transitionKey]);
                $name = $this->q("SELECT full_name FROM translation_workbench_key_names WHERE key_id=? AND namespace='' AND valid_to IS NULL", [$transitionKey])->fetchColumn();
                $id = $transition['predecessor'];
                $row = $transition['capture'];
                $old = $this->q('SELECT f.*,s.path FROM translation_workbench_findings f JOIN translation_workbench_source_files s ON s.id=f.source_file_id WHERE f.id=?', [$id])->fetch(PDO::FETCH_ASSOC);
                $link = $this->q('SELECT key_id FROM translation_workbench_key_findings WHERE finding_id=? AND valid_to IS NULL', [$id])->fetchColumn();
                if ($old && $old['status'] === 'obsolete') {
                    $successor = $this->q("SELECT f.* FROM translation_workbench_findings f LEFT JOIN translation_workbench_key_findings l ON l.finding_id=f.id AND l.valid_to IS NULL WHERE f.predecessor_id=? AND f.status='active' AND l.key_id IS NOT DISTINCT FROM ?", [$id, $transitionKey])->fetch(PDO::FETCH_ASSOC);
                    if (! $successor || isset($confirmed[$id]) || $old['raw_expression'] !== $transition['before_expression']
                        || $old['path'] !== $row['source_path'] || (! $transitionMovement && $row['found_translation_key'] !== $name)
                        || json_decode($successor['provenance'], true, flags: JSON_THROW_ON_ERROR)['capture'] != $row) {
                        throw new RuntimeException('Confirmed successor differs from rescan');
                    }
                    $confirmed[$id] = ['predecessor' => (int) $id, 'successor' => (int) $successor['id']];

                    continue;
                }
                $contentValid = $old && ($transitionMovement
                    ? $old['raw_expression'] === $row['raw_expression'] && $old['raw_source_code_literal'] === $row['raw_source_code_literal']
                        && $old['raw_literal'] === $row['raw_literal']
                        && ((int) $old['source_line'] !== $row['source_line'] || (int) $old['source_column'] !== $row['source_column'])
                    : $transitionKey !== null && $row['found_translation_key'] === $name && $row['raw_source_code_literal'] === $name
                        && $row['raw_literal'] === null && $old['raw_expression'] !== $row['raw_expression']);
                if (isset($verified[$id]) || ! $old || $old['status'] !== 'active' || ($link === false ? null : (int) $link) !== $transitionKey
                    || $old['raw_expression'] !== $transition['before_expression'] || $old['path'] !== $row['source_path']
                    || ! $contentValid || $row['source_context']['state'] !== 'confirmed_call') {
                    throw new RuntimeException('Rewrite evidence or active assignment changed');
                }
                $verified[$id] = [$old, $row, $transitionKey, $transitionMovement];
            }
            if ($confirmed !== []) {
                if ($verified !== []) {
                    throw new RuntimeException('Mixed applied and unapplied rewrite');
                }

                return ['key' => $key, 'successors' => array_values($confirmed)];
            }
            $this->q("INSERT INTO translation_workbench_scan_runs VALUES (?,'completed',?::jsonb,now())", [$operation, json_encode(['mode' => $movement ? 'confirmed_movement_only' : 'confirmed_rewrite_only', 'exhaustive' => false, 'transitions' => $transitions], JSON_THROW_ON_ERROR)]);
            foreach ($verified as $id => [$old, $row, $transitionKey, $transitionMovement]) {
                $this->q("UPDATE translation_workbench_findings SET status='obsolete',retired_at=now(),retired_reason=? WHERE id=?", [$reason, $id]);
                $this->q('UPDATE translation_workbench_key_findings SET valid_to=now() WHERE finding_id=? AND valid_to IS NULL', [$id]);
            }
            $successors = [];
            foreach ($verified as $id => [$old, $row, $transitionKey, $transitionMovement]) {
                $new = (int) $this->q('INSERT INTO translation_workbench_findings(source_file_id,captured_scan_id,predecessor_id,source_line,source_column,raw_expression,raw_source_code_literal,raw_literal,suggested_literal,suggested_key,provenance) VALUES (?,?,?,?,?,?,?,?,?,?,?::jsonb) RETURNING id',
                    [$old['source_file_id'], $operation, $id, $row['source_line'], $row['source_column'], $row['raw_expression'], $row['raw_source_code_literal'], $row['raw_literal'],
                        $transitionMovement ? $old['suggested_literal'] : $row['suggested_literal'], $old['suggested_key'], json_encode(['mode' => $transitionMovement ? 'confirmed_movement' : 'confirmed_rewrite', 'capture' => $row], JSON_THROW_ON_ERROR)])->fetchColumn();
                if ($transitionKey !== null) {
                    $this->q('INSERT INTO translation_workbench_key_findings(finding_id,key_id) VALUES (?,?)', [$new, $transitionKey]);
                }
                $this->q('INSERT INTO translation_workbench_scan_findings VALUES (?,?)', [$operation, $new]);
                $successors[] = ['predecessor' => (int) $id, 'successor' => $new];
                if ($fault) {
                    $fault(count($successors));
                }
            }
            $this->q('INSERT INTO translation_workbench_events(operation_id,event_type,actor_id,reason,details) VALUES (?,?,?,?,?::jsonb)', [$operation, $movement ? 'source_movement_reconciled' : 'source_rewrite_reconciled', $actor, $reason, json_encode(['key' => $key, 'successors' => $successors], JSON_THROW_ON_ERROR)]);

            return ['key' => $key, 'successors' => $successors];
        };
        if ($withinTransaction) {
            if (! $this->db->inTransaction()) {
                throw new RuntimeException('An outer transaction is required');
            }

            return $action();
        }

        return (new BaselineOperation($this->db))->run($operation, 'confirmed_source_rewrite', [$key, $transitions, $actor, $reason, $movement], $action);
    }

    private function q(string $sql, array $params): \PDOStatement
    {
        $statement = $this->db->prepare($sql);
        $statement->execute($params);

        return $statement;
    }
}
