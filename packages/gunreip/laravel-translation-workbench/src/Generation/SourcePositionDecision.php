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

    private function row(int $finding, ?int $key): array
    {
        $row = $this->q("SELECT f.*,s.path,l.id AS link_id,l.key_id FROM translation_workbench_findings f
            JOIN translation_workbench_source_files s ON s.id=f.source_file_id
            LEFT JOIN translation_workbench_key_findings l ON l.finding_id=f.id AND l.valid_to IS NULL
            WHERE f.id=? AND l.key_id IS NOT DISTINCT FROM CAST(? AS bigint) AND f.status='active'", [$finding, $key])->fetch(PDO::FETCH_ASSOC);
        if (! $row) throw new RuntimeException('The finding or assignment changed. Reopen the source comparison.');
        return $row;
    }

    private function evidence(array $row): array
    {
        $check = (new SourceOccurrenceCheck)->inspect($row, $this->root, includeChangedCalls: true);
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

    private function occupiedPositions(array $row, array $positions): array
    {
        if (!$positions) return [];
        $wanted = [];
        foreach ($positions as $position) {
            $wanted[$position['line'].':'.$position['column']] = true;
        }
        $occupied = [];
        $records = $this->q("SELECT f.id,f.source_line,f.source_column,f.raw_expression,l.key_id,scan.created_at AS captured_at
            FROM translation_workbench_findings f JOIN translation_workbench_scan_runs scan ON scan.id=f.captured_scan_id
            LEFT JOIN translation_workbench_key_findings l ON l.finding_id=f.id AND l.valid_to IS NULL
            WHERE f.source_file_id=? AND f.status='active' AND f.id<>? ORDER BY f.id", [$row['source_file_id'], $row['id']]);
        foreach ($records as $record) {
            $target = $record['source_line'].':'.$record['source_column'];
            if (isset($wanted[$target])) {
                $occupied[$target][] = ['id' => (int) $record['id'], 'expression' => $record['raw_expression'], 'key_id' => $record['key_id'], 'captured_at' => $record['captured_at']];
            }
        }
        return $occupied;
    }

    public static function duplicateFinding(array $finding, string $state, array $occupants): ?int
    {
        if ($state !== 'possible_move' || count($occupants) !== 1) {
            return null;
        }
        $other = $occupants[0];
        if ($other['expression'] !== $finding['raw_expression']
            || ($finding['key_id'] !== null && $finding['key_id'] !== $other['key_id'])) {
            return null;
        }
        return (int) $other['id'];
    }

    private function canRemove(array $evidence): bool
    {
        if ($evidence['state'] === 'changed_matches') {
            // The original may be gone even when another call uses the same literal.
            return count(array_column($evidence['positions'], 'expression')) === count($evidence['positions']);
        }

        return $evidence['missing_file'] || $evidence['state'] === 'expression_missing';
    }

    public function prepare(int $finding, ?int $key): array
    {
        return $this->plan($this->row($finding, $key));
    }

    private function plan(array $row): array
    {
        $evidence = $this->evidence($row);
        $occupied = $this->occupiedPositions($row, $evidence['positions']);
        $retained = self::duplicateFinding($row, $evidence['state'], array_merge([], ...array_values($occupied)));
        return ['fingerprint' => hash('sha256', json_encode([$row, $evidence, $retained], JSON_THROW_ON_ERROR)),
            'retained' => $retained,
            'evidence' => $evidence,
            'occupied' => $occupied,
            'can_move' => in_array($evidence['state'], ['possible_move', 'ambiguous_matches', 'changed_matches'], true),
            'can_remove' => $this->canRemove($evidence)];
    }

    public function apply(string $operation, int $finding, ?int $key, string $expected, string $decision, int $actor, string $reason, string $target = ''): array
    {
        if ($actor < 1 || mb_strlen(trim($reason)) < 3 || mb_strlen($reason) > 2000 || ! in_array($decision, ['moved', 'removed', 'duplicate'], true)) {
            throw new RuntimeException('A valid decision, user and reason are required.');
        }
        $payload = [$finding, $key, $expected, $decision, $actor, $reason];
        if ($target !== '') $payload[] = $target;
        return (new BaselineOperation($this->db))->run($operation, 'source_position_decision',
            $payload, function () use ($operation, $finding, $key, $expected, $decision, $actor, $reason, $target) {
                $this->db->exec('LOCK TABLE translation_workbench_source_files, translation_workbench_findings, translation_workbench_key_findings, translation_workbench_key_names IN SHARE ROW EXCLUSIVE MODE');
                $row = $this->row($finding, $key);
                $current = $this->plan($row);
                $evidence = $current['evidence'];
                $allowed = match ($decision) {
                    'moved' => $current['can_move'],
                    'duplicate' => $current['retained'] !== null,
                    'removed' => $current['can_remove'],
                };
                if (!hash_equals($expected, $current['fingerprint']) || !$allowed) {
                    throw new RuntimeException('Source or assignment changed, or this decision is not supported. Reopen the comparison.');
                }
                if ($decision !== 'removed') {
                    $path = (new SourceReplacementPreview)->path($this->root, $row['path']);
                    $bytes = file_get_contents($path);
                    if (hash('sha256', $bytes) !== $evidence['sha256']) throw new RuntimeException('Source changed during decision.');
                    $positions = $evidence['positions'];
                    if ($target !== '') {
                        $positions = array_values(array_filter($positions, static fn ($p) => $p['line'].':'.$p['column'] === $target));
                    }
                    if (count($positions) !== 1) throw new RuntimeException('Select one matching source position.');
                    $position = $positions[0];
                    $items = app(TranslationWorkbenchScanner::class)->scanSource($row['path'], $bytes)
                        ->filter(fn ($item) => $item->sourceLine === $position['line'] && $item->sourceColumn === $position['column']);
                    if ($items->count() !== 1) throw new RuntimeException('The possible match is not a unique active translation call.');
                    $capture = (new CopiedSourceCapture)->capture($items->first(), $bytes, $evidence['sha256'], []);
                    if ($capture['source_context']['state'] !== 'confirmed_call') {
                        throw new RuntimeException('The possible match is not a unique active translation call.');
                    }
                    if ($decision === 'duplicate' && $capture['raw_expression'] !== $row['raw_expression']) {
                        throw new RuntimeException('The active source call differs from the captured expression.');
                    }
                }
                if ($decision === 'moved') {
                    if ($occupant = $this->q("SELECT id FROM translation_workbench_findings WHERE source_file_id=? AND status='active' AND source_line=? AND source_column=? AND id<>? ORDER BY id LIMIT 1", [$row['source_file_id'], $position['line'], $position['column'], $finding])->fetchColumn()) {
                        throw new RuntimeException("Position {$position['line']}:{$position['column']} is occupied by finding #{$occupant}. Reopen the comparison to inspect this finding.");
                    }
                    $result = (new CorrectedSourceReconciler($this->db))->apply($operation, $key,
                        [['predecessor' => $finding, 'before_expression' => $row['raw_expression'], 'capture' => $capture,
                            'changed_expression' => $position['expression'] ?? null]],
                        $actor, $reason, movement: true, withinTransaction: true);
                } else {
                    $this->q("UPDATE translation_workbench_findings SET status='obsolete',retired_at=now(),retired_reason=? WHERE id=?", [$reason, $finding]);
                    $this->q('UPDATE translation_workbench_key_findings SET valid_to=now() WHERE finding_id=? AND valid_to IS NULL', [$finding]);
                    $result = $decision === 'duplicate'
                        ? ['key' => $key, 'archived' => [$finding], 'retained' => $current['retained']]
                        : ['key' => $key, 'removed' => [$finding]];
                    $this->q('INSERT INTO translation_workbench_events(operation_id,event_type,actor_id,reason,details) VALUES (?,?,?,?,?::jsonb)',
                        [$operation, $decision === 'duplicate' ? 'duplicate_source_capture_archived' : 'source_removal_confirmed', $actor, $reason, json_encode(['finding' => $finding, ...$result, 'evidence' => $evidence], JSON_THROW_ON_ERROR)]);
                }
                if ($this->evidence($row) !== $evidence) throw new RuntimeException('Source changed during decision. Nothing was saved.');
                return $result;
            });
    }
}
