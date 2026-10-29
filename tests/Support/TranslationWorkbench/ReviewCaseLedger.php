<?php

namespace Tests\Support\TranslationWorkbench;

use PDO;
use RuntimeException;
use Throwable;

/** V2 persistence rehearsal. Decisions record intent; never apply translations or source changes. */
final class ReviewCaseLedger
{
    public function __construct(private PDO $db) {}

    public function observe(string $subject, string $run, array $evidence, string $reason, string $action, bool $needsReview): string
    {
        if ($subject === '' || $run === '' || $reason === '' || $action === '') {
            throw new RuntimeException('Review provenance and action are required');
        }
        $json = json_encode($this->canonical($evidence), JSON_THROW_ON_ERROR);
        $id = hash('sha256', $subject);
        $version = hash('sha256', json_encode([$json, $reason, $action, $needsReview], JSON_THROW_ON_ERROR));
        $this->db->beginTransaction();
        try {
            $this->query('INSERT INTO workbench_review_cases(id,subject,current_version) VALUES (?,?,?) ON CONFLICT DO NOTHING', [$id, $subject, $version]);
            // Serialize observations and decisions on the same case, including PostgreSQL.
            $this->query('UPDATE workbench_review_cases SET subject=subject WHERE id=?', [$id]);
            $previous = $this->query('SELECT version FROM workbench_review_observations WHERE case_id=? AND run_id=?', [$id, $run])->fetchColumn();
            if ($previous !== false) {
                if ($previous !== $version) {
                    throw new RuntimeException('Run replay has different evidence');
                }
                // A replay must not roll the current version back after a newer observation.
                $this->db->commit();

                return $id;
            }
            $this->query('INSERT INTO workbench_review_versions VALUES (?,?,?,?,?,?) ON CONFLICT DO NOTHING', [$id, $version, $json, $reason, $action, (int) $needsReview]);
            $this->query('INSERT INTO workbench_review_observations VALUES (?,?,?)', [$id, $version, $run]);
            $this->query('UPDATE workbench_review_cases SET current_version=? WHERE id=?', [$version, $id]);
            $this->db->commit();

            return $id;
        } catch (Throwable $error) {
            $this->db->rollBack();
            throw $error;
        }
    }

    public function decide(string $id, string $version, string $decision, string $actor, string $explanation, int $expectedRevision = 0): void
    {
        if (! in_array($decision, ['retain_published', 'propose_historical', 'archive_only', 'clarify'], true)
            || trim($actor) === '' || trim($explanation) === '') {
            throw new RuntimeException('Explicit decision, actor and explanation required');
        }
        $this->db->beginTransaction();
        try {
            $this->query('UPDATE workbench_review_cases SET subject=subject WHERE id=?', [$id]);
            $current = $this->query('SELECT current_version FROM workbench_review_cases WHERE id=?', [$id])->fetchColumn();
            if ($current !== $version) {
                throw new RuntimeException('Evidence changed; reload before deciding');
            }
            $revision = (int) $this->query('SELECT COALESCE(MAX(revision),0) FROM workbench_review_decisions WHERE case_id=? AND version=?', [$id, $version])->fetchColumn();
            if ($revision !== $expectedRevision) {
                throw new RuntimeException('Decision changed; reload before deciding');
            }
            $this->query('INSERT INTO workbench_review_decisions VALUES (?,?,?,?,?,?,?)', [$id, $version, $revision + 1, $decision, $actor, $explanation, gmdate(DATE_ATOM)]);
            $this->db->commit();
        } catch (Throwable $error) {
            $this->db->rollBack();
            throw $error;
        }
    }

    public function cases(): array
    {
        return $this->query("SELECT c.id,c.subject,v.*,d.decision,d.actor,d.explanation,d.revision AS decision_revision,
            CASE WHEN d.decision='clarify' THEN 'clarification'
                 WHEN d.decision IS NOT NULL THEN 'decided'
                 WHEN v.needs_review=1 THEN 'needs_review' ELSE 'recorded' END AS state
            FROM workbench_review_cases c
            JOIN workbench_review_versions v ON v.case_id=c.id AND v.version=c.current_version
            LEFT JOIN workbench_review_decisions d ON d.case_id=c.id AND d.version=c.current_version
                AND d.revision=(SELECT MAX(latest.revision) FROM workbench_review_decisions latest
                    WHERE latest.case_id=c.id AND latest.version=c.current_version)
            ORDER BY c.subject")->fetchAll(PDO::FETCH_ASSOC);
    }

    private function query(string $sql, array $parameters = []): \PDOStatement
    {
        $query = $this->db->prepare($sql);
        $query->execute($parameters);

        return $query;
    }

    private function canonical(array $value): array
    {
        if (! array_is_list($value)) {
            ksort($value);
        }
        foreach ($value as &$item) {
            if (is_array($item)) {
                $item = $this->canonical($item);
            }
        }

        return $value;
    }
}
