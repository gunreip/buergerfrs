<?php

namespace Gunreip\TranslationWorkbench\Generation;

use Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchScanner;
use PDO;
use RuntimeException;

/** Apply a fresh complete plan within the existing operation ledger. */
final class CompleteRescanWriter
{
    private const WRITE_BATCH_SIZE = 500;

    public function __construct(private PDO $db, private TranslationWorkbenchScanner $scanner, private mixed $progress = null) {}

    public function prepare(array $paths, array $lang = []): array
    {
        $before = $this->state();
        $plan = (new CompleteRescanPlan)->build($this->scanner, $paths, $this->findings(), $lang, (new ScanCaptureDecisions($this->db))->latest(), $this->progress);
        $plan = $this->suggestions($plan);
        if ($before !== $this->state()) {
            throw new RuntimeException('Database changed during planning');
        }

        $total = 2 * count($plan['inventory']['hashes']) + 1;
        $this->progress && ($this->progress)('preview_complete', $total, $total);
        return ['paths' => $paths, 'lang' => $lang, 'database' => $before, 'plan' => $plan];
    }

    public function apply(string $operation, array $request, ?callable $fault = null, ?int $actor = null): array
    {
        return (new BaselineOperation($this->db))->run($operation, 'complete_rescan', $actor === null ? $request : $request + ['actor' => $actor], function () use ($operation, $request, $fault, $actor) {
            $this->db->exec('LOCK TABLE translation_workbench_source_files, translation_workbench_findings,
                translation_workbench_key_findings, translation_workbench_scan_runs,
                translation_workbench_key_names, published_value_evidence, published_value_sources, translation_workbench_events IN SHARE ROW EXCLUSIVE MODE');
            if ($this->state() !== $request['database']) {
                throw new RuntimeException('Stale rescan database state');
            }
            $old = $this->findings();
            $plan = (new CompleteRescanPlan)->build($this->scanner, $request['paths'], $old, $request['lang'], (new ScanCaptureDecisions($this->db))->latest(), $this->progress);
            $plan = $this->suggestions($plan);
            if ($plan !== $request['plan']) {
                throw new RuntimeException('Stale rescan source plan');
            }
            $byId = array_column($old, null, 'id');
            if ($plan['suggestion_initializations'] !== [] && ($actor === null || $actor < 1)) throw new RuntimeException('A user ID is required to record missing SuggestedKey initialization.');
            $reviews = array_filter($plan['files'], fn ($file) => in_array($file['state'], ['review', 'relocation_review'], true));
            $this->q("INSERT INTO translation_workbench_scan_runs VALUES (?,'completed',?::jsonb,now())", [$operation,
                json_encode(['format_version' => 2, 'mode' => 'generation_rescan', 'configuration' => ScanResultFreshness::configuration(), 'actor' => $actor, 'inventory' => $plan['inventory'], 'reviews' => $reviews,
                    'relocations' => $plan['relocations'], 'states' => $plan['states']], JSON_THROW_ON_ERROR)]);
            // Usage decisions have one authoritative copy in the operation result, used by the UI.
            $result = ['format_version' => 2, 'scan' => $operation, 'confirmed' => 0, 'created' => [], 'moved' => [], 'removed' => [], 'review_files' => array_keys($reviews), 'key_assignments' => [], 'usage_decisions' => $plan['usage_decisions']];
            $result['suggested_keys_initialized'] = [];
            foreach ($plan['suggestion_initializations'] as $index => $initialization) {
                $this->q("INSERT INTO translation_workbench_events(operation_id,event_index,event_type,actor_id,reason,details) VALUES (?,?,'suggested_key_initialized',?,'Initialize missing SuggestedKey during regular scan',?::jsonb)",
                    [$operation, $index, $actor, json_encode($initialization, JSON_THROW_ON_ERROR)]);
                $this->q('UPDATE translation_workbench_findings SET suggested_key=? WHERE id=? AND suggested_key IS NULL', [$initialization['suggested_key'], $initialization['finding']]);
                $byId[$initialization['finding']]['suggested_key'] = $initialization['suggested_key'];
                $result['suggested_keys_initialized'][] = $initialization;
            }
            $observedKeys = [];
            $confirmationBatch = [];
            $savedFiles = 0;
            $this->progress && ($this->progress)('saving', 0, max(1, count($plan['files'])));
            foreach ($plan['files'] as $path => $file) {
                $this->progress && ($this->progress)('saving', ++$savedFiles, max(1, count($plan['files'])));
                if (! in_array($file['state'], ['ready', 'missing_confirmed'], true)) {
                    continue;
                }
                $moves = $file['moved'] ?? [];
                $links = [];
                foreach (array_chunk(array_column($moves, 'predecessor'), self::WRITE_BATCH_SIZE) as $ids) {
                    $placeholders = implode(',', array_fill(0, count($ids), '?'));
                    $links += $this->q("SELECT finding_id,key_id FROM translation_workbench_key_findings WHERE finding_id IN ($placeholders) AND valid_to IS NULL", $ids)->fetchAll(PDO::FETCH_KEY_PAIR);
                }
                $this->retireFindings($file['removed'] ?? [], 'Absent from completed scan inventory');
                $this->retireFindings(array_column($moves, 'predecessor'), 'Unique same-file movement');
                $result['removed'] = [...$result['removed'], ...($file['removed'] ?? [])];
                $confirmed = $file['confirmed'] ?? [];
                $result['confirmed'] += count($confirmed);
                $new = array_map(fn ($capture) => ['capture' => $capture, 'predecessor' => null], $file['created'] ?? []);
                $source = null;
                foreach ([...$moves, ...$new] as $change) {
                    $row = $change['capture'];
                    $predecessor = $change['predecessor'];
                    $original = $predecessor === null ? null : $byId[$predecessor];
                    $source ??= $this->q('SELECT id FROM translation_workbench_source_files WHERE path=?', [$path])->fetchColumn();
                    if ($source === false) {
                        $source = $this->q('INSERT INTO translation_workbench_source_files(path) VALUES (?) RETURNING id', [$path])->fetchColumn();
                    }
                    $id = (int) $this->q('INSERT INTO translation_workbench_findings(source_file_id,captured_scan_id,predecessor_id,source_line,source_column,raw_expression,raw_source_code_literal,raw_literal,suggested_literal,suggested_key,provenance) VALUES (?,?,?,?,?,?,?,?,?,?,?::jsonb) RETURNING id',
                        [$source, $operation, $predecessor, $row['source_line'], $row['source_column'], $row['raw_expression'],
                            $row['raw_source_code_literal'], $row['raw_literal'],
                            $original !== null ? $original['suggested_literal'] : $row['suggested_literal'],
                            $original !== null ? $original['suggested_key'] : $row['suggested_key'],
                            json_encode(['mode' => 'generation_rescan', 'capture' => $row], JSON_THROW_ON_ERROR)])->fetchColumn();
                    if ($predecessor !== null) {
                        if (isset($links[$predecessor])) {
                            $this->q('INSERT INTO translation_workbench_key_findings(finding_id,key_id) VALUES (?,?)', [$id, $links[$predecessor]]);
                        }
                        $result['moved'][] = ['predecessor' => $predecessor, 'successor' => $id];
                    } else {
                        $result['created'][] = $id;
                        $assignment = (new RescanCandidateRules)->existingKey($this->db, $row);
                        if ($assignment['key_id'] !== null) {
                            $this->q('INSERT INTO translation_workbench_key_findings(finding_id,key_id) VALUES (?,?)', [$id, $assignment['key_id']]);
                            $observedKeys[] = [$row, $assignment];
                        }
                        $result['key_assignments'][$id] = $assignment;
                    }
                    $confirmed[] = $id;
                }
                foreach ($confirmed as $id) {
                    $confirmationBatch[] = $id;
                    if (count($confirmationBatch) === self::WRITE_BATCH_SIZE) {
                        $this->insertConfirmations($operation, $confirmationBatch);
                        $confirmationBatch = [];
                    }
                }
                if ($fault) {
                    $fault($path, $result);
                }
            }
            $this->insertConfirmations($operation, $confirmationBatch);
            $end = $this->scanner->scanInventory($request['paths'], array_unique(array_column($old, 'path')), $this->progress ? fn ($phase, $done, $total) => ($this->progress)('final_check', $done, max(1, $total)) : null);
            if ($end['file_hashes'] !== $plan['inventory']['hashes'] || $end['missing_paths'] !== $plan['inventory']['missing']
                || $end['scanned_directories'] !== $plan['inventory']['directories']) {
                throw new RuntimeException('Source changed during rescan application');
            }

            foreach ($observedKeys as [$capture, $assignment]) {
                if ((new RescanCandidateRules)->existingKey($this->db, $capture) !== $assignment) {
                    throw new RuntimeException('Key evidence changed during rescan application');
                }
            }
            // Preserve the groups observed at this scan, without copying immutable literal/key values.
            // Referenced findings remain available even after they are archived or moved.
            $result['shared_candidate_groups'] = array_map(
                static fn (array $group): array => $group['findings'],
                (new RescanCandidateRules)->sharedCandidates($this->db),
            );

            return $result;
        });
    }

    private function retireFindings(array $ids, string $reason): void
    {
        foreach (array_chunk($ids, self::WRITE_BATCH_SIZE) as $batch) {
            $placeholders = implode(',', array_fill(0, count($batch), '?'));
            $this->q("UPDATE translation_workbench_findings SET status='obsolete',retired_at=now(),retired_reason=? WHERE id IN ($placeholders)", [$reason, ...$batch]);
            $this->q("UPDATE translation_workbench_key_findings SET valid_to=now() WHERE finding_id IN ($placeholders) AND valid_to IS NULL", $batch);
        }
    }

    private function insertConfirmations(string $operation, array $ids): void
    {
        if ($ids === []) {
            return;
        }
        $parameters = [];
        foreach ($ids as $id) {
            $parameters[] = $operation;
            $parameters[] = $id;
        }
        $values = implode(',', array_fill(0, count($ids), '(?,?)'));
        // Keep FK/unique violations visible; do not discard duplicates with ON CONFLICT.
        $this->q("INSERT INTO translation_workbench_scan_findings (scan_run_id,finding_id) VALUES $values", $parameters);
    }

    private function suggestions(array $plan): array
    {
        $allocator = new SuggestedKeyAllocator($this->db);
        $factory = new \Gunreip\TranslationWorkbench\Scanner\SuggestedKeyFactory;
        $all = $this->db->query('SELECT f.*,s.path FROM translation_workbench_findings f JOIN translation_workbench_source_files s ON s.id=f.source_file_id ORDER BY f.id')->fetchAll(PDO::FETCH_ASSOC);
        $byId = array_column($all, null, 'id');
        $plan['suggestion_initializations'] = [];
        foreach ($all as $finding) {
            if ($finding['status'] !== 'active' || $finding['suggested_key'] !== null
                || !isset($plan['files'][$finding['path']]) || $plan['files'][$finding['path']]['state'] === 'outside_scope') continue;
            $ancestor = $finding;
            $inherited = null;
            while ($ancestor['predecessor_id'] !== null && isset($byId[$ancestor['predecessor_id']])) {
                $ancestor = $byId[$ancestor['predecessor_id']];
                if ($ancestor['suggested_key'] !== null) { $inherited = $ancestor['suggested_key']; break; }
            }
            $base = $finding['raw_literal'] !== null
                ? $factory->forLiteral($finding['raw_literal'], $finding['path'])->key
                : ($finding['raw_source_code_literal'] !== null
                    ? $factory->forExistingKeyAtSource($finding['raw_source_code_literal'], $finding['path'])->key
                    : $factory->forDynamicExpressionAtSource($finding['raw_expression'], $finding['path'])->key);
            $value = $inherited ?? $allocator->allocate($base);
            $byId[$finding['id']]['suggested_key'] = $value;
            $plan['suggestion_initializations'][] = ['finding' => (int) $finding['id'], 'suggested_key' => $value, 'previous' => null, 'inherited' => $inherited !== null];
        }

        foreach ($plan['files'] as &$file) {
            if (($file['state'] ?? '') !== 'ready') {
                continue;
            }
            foreach ($file['created'] as &$capture) {
                $capture['suggested_key'] = $allocator->allocate($capture['suggested_key']);
            }
            unset($capture);
        }
        unset($file);
        return $plan;
    }

    private function findings(): array
    {
        return $this->db->query("SELECT f.*,s.path FROM translation_workbench_findings f JOIN translation_workbench_source_files s ON s.id=f.source_file_id WHERE f.status='active' ORDER BY f.id")->fetchAll(PDO::FETCH_ASSOC);
    }

    private function state(): array
    {
        $hashes = [];
        foreach (['source_files', 'findings', 'key_findings', 'scan_runs'] as $table) {
            $hashes[$table] = $this->db->query('SELECT md5(coalesce(string_agg(row_to_json(t)::text,chr(10) ORDER BY id),\'\')) FROM translation_workbench_'.$table.' t')->fetchColumn();
        }

        foreach (['translation_workbench_key_names', 'published_value_evidence', 'published_value_sources'] as $table) {
            $hashes[$table] = $this->db->query('SELECT md5(coalesce(string_agg(row_to_json(t)::text,chr(10) ORDER BY row_to_json(t)::text),\'\')) FROM '.$table.' t')->fetchColumn();
        }

        $hashes['usage_decisions'] = $this->db->query("SELECT md5(coalesce(string_agg(row_to_json(e)::text,chr(10) ORDER BY id),''))
            FROM translation_workbench_events e WHERE event_type='scan_source_usage_decided'")->fetchColumn();

        return $hashes;
    }

    private function q(string $sql, array $parameters): \PDOStatement
    {
        $q = $this->db->prepare($sql);
        $q->execute($parameters);

        return $q;
    }
}
