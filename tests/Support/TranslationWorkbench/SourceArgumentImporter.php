<?php

namespace Tests\Support\TranslationWorkbench;

use PDO;
use RuntimeException;

/** Additive import of offline evidence into the isolated rehearsal schema. */
final class SourceArgumentImporter
{
    public function import(PDO $db, string $json, string $expectedHash): array
    {
        if (hash('sha256', $json) !== $expectedHash) {
            throw new RuntimeException('Argument report checksum mismatch');
        }
        $report = json_decode($json, true, flags: JSON_THROW_ON_ERROR);
        if ($report['mode'] !== 'offline_source_argument_evidence' || $report['production_connected'] !== false || $report['database_writes'] !== 0) {
            throw new RuntimeException('Expected offline argument evidence');
        }
        $query = static function ($sql, $params = []) use ($db) {
            $s = $db->prepare($sql);
            $s->execute($params);

            return $s;
        };
        $encode = static fn ($value) => json_encode($value, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $db->beginTransaction();
        try {
            $db->exec('LOCK TABLE source_argument_batches IN EXCLUSIVE MODE');
            if ($query('SELECT 1 FROM source_argument_batches WHERE report_sha256=?', [$expectedHash])->fetchColumn()) {
                $db->rollBack();

                return ['replayed' => true, 'items' => count($report['items']), 'producers' => count($report['producers'])];
            }
            $query('INSERT INTO source_argument_batches VALUES (?,?,?)', [$expectedHash, $report['capture_manifest_sha256'], $report['review_sha256']]);
            foreach ($report['producers'] as $name => $producer) {
                if ($producer['runtime_exhaustiveness_proven'] !== false) {
                    throw new RuntimeException('Runtime exhaustiveness was not established');
                }
                $query('INSERT INTO source_argument_producers VALUES (?,?,?::jsonb)', [$expectedHash, $name, $encode($producer)]);
            }
            $counts = [];
            foreach ($report['items'] as $item) {
                if ($item['decision'] !== null || $item['finding_fields_changed'] !== false) {
                    throw new RuntimeException('Evidence must not contain an applied decision');
                }
                $kind = $item['evidence_kind'];
                $producer = $item['producer'] ?? null;
                if ($kind === 'option_arguments') {
                    foreach (['identity', 'fallback', 'locale'] as $argument) {
                        $value = $item['arguments'][$argument];
                        if ($value['binding'] !== 'literal' && $value['literal'] !== null) {
                            throw new RuntimeException('Dynamic argument must not claim a literal');
                        }
                    }
                }
                $query('INSERT INTO source_argument_evidence VALUES (?,?,?,?,?,?::jsonb)', [$expectedHash, $report['capture_manifest_sha256'], $item['capture_id'], $kind, $producer, $encode($item)]);
                $counts[$kind] = ($counts[$kind] ?? 0) + 1;
            }
            $expected = $report['counts'];
            ksort($counts);
            ksort($expected);
            if ($counts !== $expected) {
                throw new RuntimeException('Incomplete argument report');
            }
            $missing = $query("SELECT count(*) FROM capture_source_roles r
                WHERE r.manifest_sha256=? AND r.state='pending'
                AND r.role IN ('translation_source','option_source')
                AND NOT EXISTS (SELECT 1 FROM source_argument_evidence e
                    WHERE e.report_sha256=? AND e.manifest_sha256=r.manifest_sha256 AND e.capture_id=r.capture_id)",
                [$report['capture_manifest_sha256'], $expectedHash])->fetchColumn();
            if ((int) $missing !== 0) {
                throw new RuntimeException('Pending source evidence missing');
            }
            $db->commit();

            return ['replayed' => false, 'items' => count($report['items']), 'producers' => count($report['producers'])];
        } catch (\Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }
}
