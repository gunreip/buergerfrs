<?php

namespace Tests\Support\TranslationWorkbench;

use PDO;
use RuntimeException;
use Throwable;

/** Exposes old file evidence as working material, never as publication under the corrected key. */
final class CorrectedKeyValueReader
{
    public function __construct(private PDO $db) {}

    public function read(string $baseline, string $key, array $locales): array
    {
        if ($this->db->inTransaction()) {
            throw new RuntimeException('Reader requires its own snapshot transaction');
        }
        $this->db->beginTransaction();
        try {
            $this->db->exec('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ, READ ONLY');
            $result = (new BaselineTranslationReader($this->db))->readInSnapshot($baseline, $key, $locales);
            $application = null;
            if ($result['key_id'] !== null) {
                $rows = $this->query('SELECT a.operation_id,a.proposal_id,p.source_key,p.target_key
                    FROM baseline_key_correction_applications a JOIN baseline_key_correction_proposals p ON p.operation_id=a.proposal_id
                    WHERE a.key_id=? AND p.baseline_hash=?', [$result['key_id'], $baseline])->fetchAll(PDO::FETCH_ASSOC);
                if (count($rows) > 1) {
                    throw new RuntimeException('Ambiguous correction provenance');
                }
                $application = $rows[0] ?? null;
            }
            foreach ($result['locales'] as $locale => &$row) {
                $row['working_basis'] = null;
                if ($application !== null) {
                    $value = $this->query('SELECT v.value,v.state,s.path,s.sha256 FROM published_value_evidence v
                        JOIN published_value_sources s ON s.baseline_hash=v.baseline_hash AND s.path=v.path
                        WHERE v.baseline_hash=? AND v.full_name=? AND v.locale=?',
                        [$baseline, $application['source_key'], $locale])->fetch(PDO::FETCH_ASSOC);
                    if ($value !== false) {
                        $row['working_basis'] = [...$value, 'source_key' => $application['source_key'],
                            'application_id' => $application['operation_id'], 'proposal_id' => $application['proposal_id'],
                            'baseline_hash' => $baseline, 'published_under_requested_key' => false];
                    }
                }
            }
            unset($row);
            $result['correction'] = $application;
            $this->db->commit();

            return $result;
        } catch (Throwable $error) {
            $this->db->rollBack();
            throw $error;
        }
    }

    private function query(string $sql, array $parameters): \PDOStatement
    {
        $statement = $this->db->prepare($sql);
        $statement->execute($parameters);

        return $statement;
    }
}
