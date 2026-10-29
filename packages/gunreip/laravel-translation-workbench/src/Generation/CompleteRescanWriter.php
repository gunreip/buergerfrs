<?php

namespace Gunreip\TranslationWorkbench\Generation;

use Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchScanner;
use PDO;
use RuntimeException;

/** Apply a fresh complete plan within the existing operation ledger. */
final class CompleteRescanWriter
{
    public function __construct(private PDO $db, private TranslationWorkbenchScanner $scanner) {}

    public function prepare(array $paths, array $lang = []): array
    {
        $before = $this->state();
        $plan = (new CompleteRescanPlan)->build($this->scanner, $paths, $this->findings(), $lang, (new ScanCaptureDecisions($this->db))->latest());
        $plan = $this->suggestions($plan);
        if ($before !== $this->state()) {
            throw new RuntimeException('Database changed during planning');
        }

        return ['paths' => $paths, 'lang' => $lang, 'database' => $before, 'plan' => $plan];
    }

    public function apply(string $operation, array $request, ?callable $fault = null): array
    {
        return (new BaselineOperation($this->db))->run($operation, 'complete_rescan', $request, function () use ($operation, $request, $fault) {
            $this->db->exec('LOCK TABLE translation_workbench_source_files, translation_workbench_findings,
                translation_workbench_key_findings, translation_workbench_scan_runs,
                translation_workbench_key_names, published_value_evidence, published_value_sources, translation_workbench_events IN SHARE ROW EXCLUSIVE MODE');
            if ($this->state() !== $request['database']) {
                throw new RuntimeException('Stale rescan database state');
            }
            $old = $this->findings();
            $plan = (new CompleteRescanPlan)->build($this->scanner, $request['paths'], $old, $request['lang'], (new ScanCaptureDecisions($this->db))->latest());
            $plan = $this->suggestions($plan);
            if ($plan !== $request['plan']) {
                throw new RuntimeException('Stale rescan source plan');
            }
            $byId = array_column($old, null, 'id');
            $reviews = array_filter($plan['files'], fn ($file) => in_array($file['state'], ['review', 'relocation_review'], true));
            $this->q("INSERT INTO translation_workbench_scan_runs VALUES (?,'completed',?::jsonb,now())", [$operation,
                json_encode(['mode' => 'generation_rescan', 'inventory' => $plan['inventory'], 'reviews' => $reviews,
                    'usage_decisions' => $plan['usage_decisions'], 'relocations' => $plan['relocations'], 'states' => $plan['states']], JSON_THROW_ON_ERROR)]);
            $result = ['scan' => $operation, 'confirmed' => 0, 'created' => [], 'moved' => [], 'removed' => [], 'review_files' => array_keys($reviews), 'key_assignments' => [], 'usage_decisions' => $plan['usage_decisions']];
            $observedKeys = [];
            foreach ($plan['files'] as $path => $file) {
                if (! in_array($file['state'], ['ready', 'missing_confirmed'], true)) {
                    continue;
                }
                $moves = $file['moved'] ?? [];
                $retire = [...($file['removed'] ?? []), ...array_column($moves, 'predecessor')];
                $links = [];
                foreach ($retire as $id) {
                    $links[$id] = $this->q('SELECT key_id FROM translation_workbench_key_findings WHERE finding_id=? AND valid_to IS NULL', [$id])->fetchColumn();
                    $this->q("UPDATE translation_workbench_findings SET status='obsolete',retired_at=now(),retired_reason=? WHERE id=?", [
                        in_array($id, array_column($moves, 'predecessor')) ? 'Unique same-file movement' : 'Absent from completed scan inventory', $id]);
                    $this->q('UPDATE translation_workbench_key_findings SET valid_to=now() WHERE finding_id=? AND valid_to IS NULL', [$id]);
                }
                $result['removed'] = [...$result['removed'], ...($file['removed'] ?? [])];
                $confirmed = $file['confirmed'] ?? [];
                $result['confirmed'] += count($confirmed);
                $new = array_map(fn ($capture) => ['capture' => $capture, 'predecessor' => null], $file['created'] ?? []);
                foreach ([...$moves, ...$new] as $change) {
                    $row = $change['capture'];
                    $predecessor = $change['predecessor'];
                    $original = $predecessor === null ? null : $byId[$predecessor];
                    $source = $this->q('SELECT id FROM translation_workbench_source_files WHERE path=?', [$path])->fetchColumn();
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
                        if ($links[$predecessor] !== false) {
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
                    $this->q('INSERT INTO translation_workbench_scan_findings VALUES (?,?)', [$operation, $id]);
                }
                if ($fault) {
                    $fault($path, $result);
                }
            }
            $end = $this->scanner->scanWithManifest($request['paths'], array_unique(array_column($old, 'path')));
            if ($end['file_hashes'] !== $plan['inventory']['hashes'] || $end['missing_paths'] !== $plan['inventory']['missing']
                || $end['scanned_directories'] !== $plan['inventory']['directories']) {
                throw new RuntimeException('Source changed during rescan application');
            }

            foreach ($observedKeys as [$capture, $assignment]) {
                if ((new RescanCandidateRules)->existingKey($this->db, $capture) !== $assignment) {
                    throw new RuntimeException('Key evidence changed during rescan application');
                }
            }
            $result['shared_candidates'] = (new RescanCandidateRules)->sharedCandidates($this->db);

            return $result;
        });
    }

    private function suggestions(array $plan): array
    {
        $allocator = new SuggestedKeyAllocator($this->db);
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
