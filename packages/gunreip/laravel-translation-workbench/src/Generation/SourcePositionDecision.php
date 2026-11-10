<?php

namespace Gunreip\TranslationWorkbench\Generation;

use Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchScanner;
use PDO;
use RuntimeException;

/** Explicit finding lifecycle decisions. Never writes source or language files. */
final class SourcePositionDecision
{
    public function __construct(private PDO $db, private string $root) {}

    private function q(string $sql, array $params = []): \PDOStatement
    {
        $query = $this->db->prepare($sql);
        $query->execute($params);
        return $query;
    }

    private function row(int $finding, int $key): array
    {
        $row = $this->q("SELECT f.*,s.path,l.id AS link_id,l.key_id FROM translation_workbench_findings f
            JOIN translation_workbench_source_files s ON s.id=f.source_file_id
            JOIN translation_workbench_key_findings l ON l.finding_id=f.id AND l.valid_to IS NULL
            WHERE f.id=? AND l.key_id=? AND f.status='active'", [$finding, $key])->fetch(PDO::FETCH_ASSOC);
        if (! $row) throw new RuntimeException('The finding or assignment changed. Reopen the source comparison.');
        return $row;
    }

    private function evidence(array $row): array
    {
        $check = (new SourceOccurrenceCheck)->inspect($row, $this->root);
        $missing = false;
        if ($check['state'] === 'source_unavailable') {
            // Prove absence by listing readable parent directories, not by treating unreadability as deletion.
            $relative = $row['path'];
            if ($relative !== '' && ! str_contains($relative, "\0") && ! str_contains($relative, '\\')
                && ! array_intersect(explode('/', $relative), ['', '.', '..']) && ! is_link($this->root)) {
                $path = realpath($this->root);
                if ($path !== false) {
                    foreach (explode('/', $relative) as $part) {
                        if (! is_dir($path) || ! is_readable($path) || ! is_executable($path)) break;
                        $entries = @scandir($path);
                        if ($entries === false) break;
                        if (! in_array($part, $entries, true)) { $missing = true; break; }
                        $path .= '/'.$part;
                        if (is_link($path)) break;
                    }
                }
            }
        }
        $evidence = ['state' => $check['state'], 'sha256' => $check['sha256'] ?? null,
            'positions' => $check['positions'], 'missing_file' => $missing];
        return $evidence;
    }

    public function prepare(int $finding, int $key): array
    {
        $row = $this->row($finding, $key);
        $evidence = $this->evidence($row);
        return ['fingerprint' => hash('sha256', json_encode([$row, $evidence], JSON_THROW_ON_ERROR)),
            'evidence' => $evidence,
            'can_move' => $evidence['state'] === 'possible_move',
            'can_remove' => $evidence['missing_file'] || $evidence['state'] === 'expression_missing'];
    }

    public function apply(string $operation, int $finding, int $key, string $expected, string $decision, int $actor, string $reason): array
    {
        if ($actor < 1 || mb_strlen(trim($reason)) < 3 || mb_strlen($reason) > 2000 || ! in_array($decision, ['moved', 'removed'], true)) {
            throw new RuntimeException('A valid decision, user and reason are required.');
        }
        return (new BaselineOperation($this->db))->run($operation, 'source_position_decision',
            [$finding, $key, $expected, $decision, $actor, $reason], function () use ($operation, $finding, $key, $expected, $decision, $actor, $reason) {
                $this->db->exec('LOCK TABLE translation_workbench_source_files, translation_workbench_findings, translation_workbench_key_findings, translation_workbench_key_names IN SHARE ROW EXCLUSIVE MODE');
                $row = $this->row($finding, $key);
                $evidence = $this->evidence($row);
                $current = ['fingerprint' => hash('sha256', json_encode([$row, $evidence], JSON_THROW_ON_ERROR)),
                    'can_move' => $evidence['state'] === 'possible_move',
                    'can_remove' => $evidence['missing_file'] || $evidence['state'] === 'expression_missing'];
                if (! hash_equals($expected, $current['fingerprint']) || ! $current[$decision === 'moved' ? 'can_move' : 'can_remove']) {
                    throw new RuntimeException('Source or assignment changed, or this decision is not supported. Reopen the comparison.');
                }
                if ($decision === 'moved') {
                    $path = (new SourceReplacementPreview)->path($this->root, $row['path']);
                    $bytes = file_get_contents($path);
                    if (hash('sha256', $bytes) !== $evidence['sha256']) throw new RuntimeException('Source changed during decision.');
                    $position = $evidence['positions'][0];
                    $items = app(TranslationWorkbenchScanner::class)->scanSource($row['path'], $bytes)
                        ->filter(fn ($item) => $item->sourceLine === $position['line'] && $item->sourceColumn === $position['column']);
                    if ($items->count() !== 1) throw new RuntimeException('The possible match is not a unique active translation call.');
                    $capture = (new CopiedSourceCapture)->capture($items->first(), $bytes, $evidence['sha256'], []);
                    if ($capture['source_context']['state'] !== 'confirmed_call') {
                        throw new RuntimeException('The possible match is not a unique active translation call.');
                    }
                    if ($this->q("SELECT id FROM translation_workbench_findings WHERE source_file_id=? AND status='active' AND source_line=? AND source_column=? AND id<>?", [$row['source_file_id'], $position['line'], $position['column'], $finding])->fetchColumn()) {
                        throw new RuntimeException('The target position is already represented by another active finding.');
                    }
                    $result = (new CorrectedSourceReconciler($this->db))->apply($operation, $key,
                        [['predecessor' => $finding, 'before_expression' => $row['raw_expression'], 'capture' => $capture]],
                        $actor, $reason, movement: true, withinTransaction: true);
                } else {
                    $this->q("UPDATE translation_workbench_findings SET status='obsolete',retired_at=now(),retired_reason=? WHERE id=?", [$reason, $finding]);
                    $this->q('UPDATE translation_workbench_key_findings SET valid_to=now() WHERE finding_id=? AND valid_to IS NULL', [$finding]);
                    $result = ['key' => $key, 'removed' => [$finding]];
                    $this->q('INSERT INTO translation_workbench_events(operation_id,event_type,actor_id,reason,details) VALUES (?,?,?,?,?::jsonb)',
                        [$operation, 'source_removal_confirmed', $actor, $reason, json_encode(['finding' => $finding, ...$result, 'evidence' => $evidence], JSON_THROW_ON_ERROR)]);
                }
                if ($this->evidence($row) !== $evidence) throw new RuntimeException('Source changed during decision. Nothing was saved.');
                return $result;
            });
    }
}
