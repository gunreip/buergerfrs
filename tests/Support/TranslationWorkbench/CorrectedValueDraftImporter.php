<?php

namespace Tests\Support\TranslationWorkbench;

use Gunreip\TranslationWorkbench\Generation\BaselineDraftWriter;
use Gunreip\TranslationWorkbench\Generation\BaselineOperation;
use PDO;
use RuntimeException;
use Throwable;

/** Explicitly copies one exact baseline locale into an initially empty working slot. */
final class CorrectedValueDraftImporter
{
    public function __construct(private PDO $db) {}

    public function preview(string $application, string $locale): array
    {
        $this->db->beginTransaction();
        try {
            $this->db->exec('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ, READ ONLY');
            $result = $this->context($application, $locale);
            $this->db->commit();

            return $result;
        } catch (Throwable $error) {
            $this->db->rollBack();
            throw $error;
        }
    }

    public function import(string $operation, string $application, string $locale, string $expected, int $actor, string $reason, ?callable $fault = null): array
    {
        if ($actor < 1 || trim($reason) === '') {
            throw new RuntimeException('Actor and reason are required');
        }

        return (new BaselineOperation($this->db))->run($operation, 'baseline_value_import', [$application, $locale, $expected, $actor, $reason], function () use ($operation, $application, $locale, $expected, $actor, $reason, $fault) {
            $this->db->exec('LOCK TABLE translation_workbench_key_names, translation_workbench_key_findings IN SHARE MODE');
            $key = $this->query('SELECT key_id FROM baseline_key_correction_applications WHERE operation_id=?', [$application])->fetchColumn();
            if ($key === false) {
                throw new RuntimeException('Unknown correction application');
            }
            $this->query('SELECT id FROM translation_workbench_keys WHERE id=? FOR UPDATE', [$key]);
            $context = $this->context($application, $locale);
            if (! hash_equals($context['fingerprint'], $expected)) {
                throw new RuntimeException('Import context changed');
            }
            if ($context['evidence']['current_revision'] !== null) {
                throw new RuntimeException('Working value already exists');
            }
            $evidence = $context['evidence'];
            $result = (new BaselineDraftWriter($this->db))->save($operation, (int) $key, $locale, $evidence['value'], null, $actor, $reason);
            if ($fault !== null) {
                $fault();
            }
            $this->query('INSERT INTO baseline_value_draft_imports VALUES (?,?,?,?::jsonb)', [$operation, $result['revision'], $application, json_encode($evidence, JSON_THROW_ON_ERROR)]);

            return $result;
        });
    }

    private function context(string $application, string $locale): array
    {
        $source = $this->query("SELECT a.key_id,p.baseline_hash,p.source_key,n.id AS name_id,n.full_name
            FROM baseline_key_correction_applications a JOIN baseline_key_correction_proposals p ON p.operation_id=a.proposal_id
            JOIN translation_workbench_key_names n ON n.key_id=a.key_id AND n.namespace='' AND n.valid_to IS NULL
            WHERE a.operation_id=?", [$application])->fetch(PDO::FETCH_ASSOC);
        if (! $source) {
            throw new RuntimeException('No current corrected key');
        }
        $value = $this->query('SELECT v.value,s.path,s.sha256 FROM published_value_evidence v
            JOIN published_value_sources s ON s.baseline_hash=v.baseline_hash AND s.path=v.path
            WHERE v.baseline_hash=? AND v.full_name=? AND v.locale=?', [$source['baseline_hash'], $source['source_key'], $locale])->fetch(PDO::FETCH_ASSOC);
        if ($value === false) {
            throw new RuntimeException('No exact baseline value for this locale');
        }
        $links = $this->query("SELECT l.id,l.finding_id FROM translation_workbench_key_findings l
            JOIN translation_workbench_findings f ON f.id=l.finding_id
            WHERE l.key_id=? AND l.valid_to IS NULL AND f.status='active' ORDER BY l.id", [$source['key_id']])->fetchAll(PDO::FETCH_ASSOC);
        if ($links === []) {
            throw new RuntimeException('No active key usages');
        }
        $revision = $this->query("SELECT current_revision_id FROM translation_workbench_value_slots WHERE key_id=? AND locale=? AND kind='scalar'", [$source['key_id'], $locale])->fetchColumn();
        $evidence = [...$source, ...$value, 'application_id' => $application, 'locale' => $locale, 'links' => $links,
            'current_revision' => $revision === false || $revision === null ? null : (int) $revision];

        return ['evidence' => $evidence, 'fingerprint' => hash('sha256', json_encode($evidence, JSON_THROW_ON_ERROR))];
    }

    private function query(string $sql, array $params): \PDOStatement
    {
        $statement = $this->db->prepare($sql);
        $statement->execute($params);

        return $statement;
    }
}
