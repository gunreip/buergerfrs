<?php

namespace Gunreip\TranslationWorkbench\Generation;

use Illuminate\Support\Str;
use PDO;
use RuntimeException;

/** Explicit usage decisions for exact, immutable scan captures. */
final class ScanCaptureDecisions
{
    public function __construct(private PDO $db, private ?string $sourceRoot = null) {}

    private function query(string $sql, array $parameters = []): \PDOStatement
    {
        $query = $this->db->prepare($sql);
        $query->execute($parameters);

        return $query;
    }

    public static function identity(array $capture): string
    {
        return hash('sha256', json_encode([
            $capture['source_path'], $capture['source_sha256'], (int) $capture['source_offset'],
            $capture['raw_expression'], $capture['reason'], $capture['source_context']['state'],
        ], JSON_THROW_ON_ERROR));
    }

    /** Recently used reasons for the same classification, without duplicate suggestions. */
    public function reasons(string $decision): array
    {
        return $this->query("SELECT btrim(reason) AS reason FROM translation_workbench_events
            WHERE event_type='scan_source_usage_decided' AND details->>'decision'=?
                AND btrim(reason)<>''
            GROUP BY btrim(reason) ORDER BY max(id) DESC LIMIT 100", [$decision])->fetchAll(PDO::FETCH_COLUMN);
    }

    /** Restrict the journal lookup to captures on the visible page. */
    public function forIdentities(array $identities): array
    {
        if ($identities === []) {
            return [];
        }
        $placeholders = implode(',', array_fill(0, count($identities), '?'));
        $rows = $this->query("SELECT DISTINCT ON (details->>'identity') id,actor_id,reason,occurred_at,details
            FROM translation_workbench_events WHERE event_type='scan_source_usage_decided'
                AND details->>'identity' IN ($placeholders)
            ORDER BY details->>'identity',id DESC", array_values($identities))->fetchAll(PDO::FETCH_ASSOC);
        $decisions = [];
        foreach ($rows as $row) {
            $details = json_decode($row['details'], true, flags: JSON_THROW_ON_ERROR);
            $decisions[$details['identity']] = [...$details, 'id' => (int) $row['id'], 'actor_id' => (int) $row['actor_id'],
                'reason' => $row['reason'], 'occurred_at' => $row['occurred_at']];
        }

        return $decisions;
    }

    public function latest(): array
    {
        $rows = $this->query("SELECT DISTINCT ON (details->>'identity') id,actor_id,reason,occurred_at,details
            FROM translation_workbench_events WHERE event_type='scan_source_usage_decided'
            ORDER BY details->>'identity',id DESC")->fetchAll(PDO::FETCH_ASSOC);
        $decisions = [];
        foreach ($rows as $row) {
            $details = json_decode($row['details'], true, flags: JSON_THROW_ON_ERROR);
            $decisions[$details['identity']] = [...$details, 'id' => (int) $row['id'], 'actor_id' => (int) $row['actor_id'],
                'reason' => $row['reason'], 'occurred_at' => $row['occurred_at']];
        }

        return $decisions;
    }

    public function inspect(string $run, string $path, int $index): array
    {
        if (! Str::isUuid($run) || $index < 0) {
            throw new RuntimeException('Invalid scan capture.');
        }
        $json = $this->query("SELECT (s.scope_snapshot->'reviews'->?->'captures'->CAST(? AS integer))::text
            FROM translation_workbench_scan_runs s JOIN translation_workbench_operations o ON o.id=s.id
            WHERE s.id=? AND o.kind='complete_rescan' AND o.result->'review_files' @> ?::jsonb",
            [$path, $index, $run, json_encode([$path], JSON_THROW_ON_ERROR)])->fetchColumn();
        if (! $json) {
            throw new RuntimeException('Scan capture not found.');
        }
        $capture = json_decode($json, true, flags: JSON_THROW_ON_ERROR);
        if ($capture['source_path'] !== $path || $capture['reason'] !== 'dynamic_or_nonliteral_argument'
            || $capture['source_context']['state'] !== 'confirmed_call') {
            throw new RuntimeException('Usage classification requires a confirmed dynamic translation call.');
        }
        $identity = self::identity($capture);

        return ['run' => $run, 'path' => $path, 'index' => $index, 'identity' => $identity,
            'capture' => $capture, 'decision' => $this->latest()[$identity] ?? null];
    }

    public function save(string $run, string $path, int $index, string $decision, string $reason, int $actor, int $expected): array
    {
        if (! in_array($decision, ['translate', 'database_value'], true) || $actor < 1 || trim($reason) === '' || mb_strlen($reason) > 2000) {
            throw new RuntimeException('A usage decision, actor and reason are required.');
        }
        $operation = (string) Str::uuid();

        return (new BaselineOperation($this->db))->run($operation, 'scan_source_usage_decision',
            [$run, $path, $index, $decision, trim($reason), $actor, $expected], function () use ($operation, $run, $path, $index, $decision, $reason, $actor, $expected) {
                $this->db->exec('LOCK TABLE translation_workbench_findings,translation_workbench_events IN SHARE ROW EXCLUSIVE MODE');
                $context = $this->inspect($run, $path, $index);
                $previous = $context['decision'];
                if ((int) ($previous['id'] ?? 0) !== $expected) {
                    throw new RuntimeException('Another decision was saved. Reopen this capture.');
                }
                $capture = $context['capture'];
                $absolute = (new SourceReplacementPreview)->path($this->sourceRoot ?? base_path(), $path);
                $bytes = file_get_contents($absolute);
                if (hash('sha256', $bytes) !== $capture['source_sha256']
                    || substr($bytes, (int) $capture['source_offset'], strlen($capture['raw_expression'])) !== $capture['raw_expression']) {
                    throw new RuntimeException('Source changed. Run a fresh scan before deciding.');
                }
                if ($decision === 'database_value' && $this->query("SELECT f.id FROM translation_workbench_findings f
                    JOIN translation_workbench_source_files s ON s.id=f.source_file_id
                    WHERE s.path=? AND f.status='active' AND f.source_line=? AND f.source_column=?",
                    [$path, $capture['source_line'], $capture['source_column']])->fetchColumn()) {
                    throw new RuntimeException('An active finding occupies this position. Review it before excluding the source usage.');
                }
                if ($previous && $previous['decision'] === $decision && $previous['reason'] === trim($reason)) {
                    return $previous;
                }
                $details = ['identity' => $context['identity'], 'scan' => $run, 'capture_index' => $index,
                    'source_path' => $path, 'source_sha256' => $capture['source_sha256'], 'source_offset' => $capture['source_offset'],
                    'raw_expression' => $capture['raw_expression'], 'decision' => $decision, 'previous_event_id' => $previous['id'] ?? null];
                $event = $this->query("INSERT INTO translation_workbench_events(operation_id,event_type,actor_id,reason,details)
                    VALUES (?,'scan_source_usage_decided',?,?,?::jsonb) RETURNING id,occurred_at", [$operation, $actor, trim($reason), json_encode($details, JSON_THROW_ON_ERROR)])->fetch(PDO::FETCH_ASSOC);
                if (hash_file('sha256', $absolute) !== $capture['source_sha256']) {
                    throw new RuntimeException('Source changed while saving the decision.');
                }

                return [...$details, 'id' => (int) $event['id'], 'occurred_at' => $event['occurred_at'], 'actor_id' => $actor, 'reason' => trim($reason)];
            });
    }
}
