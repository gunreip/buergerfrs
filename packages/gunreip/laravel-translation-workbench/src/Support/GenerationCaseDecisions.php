<?php

namespace Gunreip\TranslationWorkbench\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PDO;
use RuntimeException;
use Throwable;

/** Decisions are persisted in the generation's existing immutable operation/event journal. */
class GenerationCaseDecisions
{
    public function __construct(private ?string $sourceRoot = null, private ?string $snapshotFile = null, private ?PDO $testConnection = null) {}

    public function snapshot(): array
    {
        if ($this->snapshotFile !== null) {
            return json_decode(file_get_contents($this->snapshotFile), true, flags: JSON_THROW_ON_ERROR);
        }
        $base = storage_path('app/private/workbench-previews');
        $pointer = json_decode(@file_get_contents($base.'/working-generation-ui.json') ?: '', true, flags: JSON_THROW_ON_ERROR);
        if (! preg_match('/^working-generation-ui-\d{8}-\d{6}-[a-f0-9]{12}\.json$/D', $pointer['file'] ?? '')) {
            throw new RuntimeException('Invalid working generation pointer');
        }
        $bytes = file_get_contents($base.'/'.$pointer['file']);
        if (hash('sha256', $bytes) !== $pointer['sha256']) {
            throw new RuntimeException('Working generation evidence changed');
        }

        return json_decode($bytes, true, flags: JSON_THROW_ON_ERROR);
    }

    public function connection(array $snapshot): PDO
    {
        if ($this->testConnection !== null) {
            if ($this->testConnection->query('SELECT current_database()')->fetchColumn() !== $snapshot['database']) {
                throw new RuntimeException('UI test snapshot database differs.');
            }
            return $this->testConnection;
        }
        $connection = DB::connection();
        if ($connection->getDriverName() !== 'pgsql' || $connection->getDatabaseName() !== $snapshot['database']
            || $snapshot['generation']['schema'] !== 'current_generation_v2') {
            throw new RuntimeException('Working generation connection differs');
        }
        $db = $connection->getPdo();
        $generation = $db->query('SELECT generation FROM current_generation_v2.workbench_generation')->fetchColumn();
        if ($generation !== $snapshot['generation']['generation']) {
            throw new RuntimeException('Working generation differs');
        }

        return $db;
    }

    public function decisions(PDO $db): array
    {
        $rows = $db->query("SELECT DISTINCT ON (details->>'capture_id', details->>'manifest_sha256') id,actor_id,reason,occurred_at,details
            FROM current_generation_v2.translation_workbench_events WHERE event_type='source_review_decided'
            ORDER BY details->>'capture_id', details->>'manifest_sha256',id DESC")->fetchAll(PDO::FETCH_ASSOC);
        $result = [];
        foreach ($rows as $row) {
            $details = json_decode($row['details'], true, flags: JSON_THROW_ON_ERROR);
            $result[$details['capture_id']] = [...$row, ...$details];
        }

        return $result;
    }

    public function save(PDO $db, array $capture, string $decision, string $reason, int $actor, int $expectedId): array
    {
        if (! in_array($decision, ['translate', 'database_value'], true) || trim($reason) === '' || $actor < 1) {
            throw new RuntimeException('Explicit decision, explanation and actor required');
        }
        $path = $capture['source_path'];
        if (str_contains($path, '..') || str_starts_with($path, '/') || @hash_file('sha256', ($this->sourceRoot ?? base_path()).'/'.$path) !== $capture['source_sha256']) {
            throw new RuntimeException('Source changed; a fresh scan is required before deciding');
        }
        $db->beginTransaction();
        try {
            $query = $db->prepare('SELECT pg_advisory_xact_lock(hashtextextended(?,0))');
            $query->execute(['source-review:'.$capture['manifest_sha256'].':'.$capture['capture_id']]);
            $query = $db->prepare('SELECT disposition FROM current_generation_v2.capture_outcomes WHERE manifest_sha256=? AND capture_id=?');
            $query->execute([$capture['manifest_sha256'], $capture['capture_id']]);
            if ($query->fetchColumn() !== 'pending') {
                throw new RuntimeException('The capture is not a pending case in this generation');
            }
            $previous = $this->decisions($db)[$capture['capture_id']] ?? null;
            if ((int) ($previous['id'] ?? 0) !== $expectedId) {
                throw new RuntimeException('Another decision was saved; reload the case');
            }
            if ($previous && $previous['decision'] === $decision && $previous['reason'] === trim($reason)) {
                $db->commit();

                return $previous;
            }
            $details = ['capture_id' => $capture['capture_id'], 'manifest_sha256' => $capture['manifest_sha256'],
                'decision' => $decision, 'scope' => 'single_source_occurrence', 'source_path' => $path,
                'source_sha256' => $capture['source_sha256'], 'raw_expression' => $capture['raw_expression'],
                'previous_event_id' => $previous['id'] ?? null];
            $operation = (string) Str::uuid();
            $json = json_encode($details, JSON_THROW_ON_ERROR);
            $query = $db->prepare('INSERT INTO current_generation_v2.translation_workbench_operations(id,kind,request_fingerprint,result) VALUES (?,?,?,?)');
            $query->execute([$operation, 'source_review_decision', hash('sha256', $json), $json]);
            $query = $db->prepare("INSERT INTO current_generation_v2.translation_workbench_events(operation_id,event_type,actor_id,reason,details)
                VALUES (?,'source_review_decided',?,?,?) RETURNING id");
            $query->execute([$operation, $actor, trim($reason), $json]);
            $id = $query->fetchColumn();
            if (hash_file('sha256', ($this->sourceRoot ?? base_path()).'/'.$path) !== $capture['source_sha256']) {
                throw new RuntimeException('Source changed during the decision');
            }
            $db->commit();

            return [...$details, 'id' => $id];
        } catch (Throwable $error) {
            $db->rollBack();
            throw $error;
        }
    }
}
