<?php

namespace Gunreip\TranslationWorkbench\Generation;

use PDO;
use RuntimeException;

/** Shared read-only snapshot for review and diagnostic readers. */
final class RevisionReviewContext
{
    public static function snapshot(PDO $db, int $revision): array
    {
        $query = static function (string $sql, array $params) use ($db): \PDOStatement {
            $statement = $db->prepare($sql);
            $statement->execute($params);

            return $statement;
        };
        $row = $query(<<<'SQL'
            SELECT r.id AS revision_id, r.value, s.key_id, s.locale, s.kind, s.option_identity, s.current_revision_id,
                n.id AS key_name_id, n.namespace, n.full_name,
                COALESCE((SELECT jsonb_agg(jsonb_build_object('link',l.id,'finding',l.finding_id) ORDER BY l.id)
                    FROM translation_workbench_key_findings l
                    JOIN translation_workbench_findings f ON f.id=l.finding_id
                    WHERE l.key_id=s.key_id AND l.valid_to IS NULL AND f.status='active'), '[]'::jsonb) AS usages,
                (SELECT id FROM translation_workbench_reviews WHERE revision_id=r.id ORDER BY id DESC LIMIT 1) AS latest_decision
            FROM translation_workbench_value_revisions r
            JOIN translation_workbench_value_slots s ON s.id=r.slot_id
            JOIN translation_workbench_key_names n ON n.key_id=s.key_id AND n.valid_to IS NULL
            WHERE r.id=?
            SQL, [$revision])->fetch(PDO::FETCH_ASSOC);
        if (! $row) {
            throw new RuntimeException('Revision or current key name missing');
        }
        $latest = $row['latest_decision'];
        unset($row['latest_decision']);
        $row['usages'] = json_decode($row['usages'], true, flags: JSON_THROW_ON_ERROR);

        return ['context' => $row, 'latest_decision' => $latest,
            'context_fingerprint' => hash('sha256', json_encode($row, JSON_THROW_ON_ERROR)),
            'fingerprint' => hash('sha256', json_encode([$row, $latest], JSON_THROW_ON_ERROR))];
    }
}
