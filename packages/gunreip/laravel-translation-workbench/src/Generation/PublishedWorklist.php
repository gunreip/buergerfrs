<?php

namespace Gunreip\TranslationWorkbench\Generation;

use Illuminate\Pagination\LengthAwarePaginator;
use PDO;

/** Latest completed publication per scalar destination; not a live filesystem audit. */
final class PublishedWorklist
{
    public function __construct(private PDO $db) {}

    public const SORTS = ['key' => 'key_id', 'language' => 'locale', 'working' => 'working_state', 'published' => 'occurred_at'];

    public const CURRENT_PUBLICATIONS = <<<'SQL'
            WITH latest AS (
                SELECT DISTINCT ON (p.plan::jsonb->>'directory', c->>'namespace', c->>'full_name', c->>'locale')
                    p.id AS run, e.id AS event_id, e.occurred_at, e.actor_id,
                    (c->>'revision_id')::bigint AS revision, (c->>'key_id')::bigint AS key_id,
                    c->>'namespace' AS namespace, c->>'full_name' AS full_name,
                    c->>'locale' AS locale, c->>'value' AS value
                FROM translation_workbench_publication_runs p
                JOIN translation_workbench_events e ON e.operation_id=p.id AND e.event_type='revisions_published'
                CROSS JOIN LATERAL jsonb_array_elements(p.plan::jsonb->'contexts') item
                CROSS JOIN LATERAL (SELECT item->'context' AS c) context
                WHERE p.state='complete' AND c->>'kind'='scalar'
                ORDER BY p.plan::jsonb->>'directory', c->>'namespace', c->>'full_name', c->>'locale', e.id DESC
            )
            SELECT p.*, s.current_revision_id,
                CASE WHEN n.full_name IS DISTINCT FROM p.full_name OR n.namespace IS DISTINCT FROM p.namespace THEN 2
                    WHEN s.current_revision_id IS DISTINCT FROM p.revision THEN 1 ELSE 0 END AS working_state,
                n.full_name AS current_name, n.namespace AS current_namespace,
                (SELECT min(f.id) FROM translation_workbench_key_findings l
                    JOIN translation_workbench_findings f ON f.id=l.finding_id AND f.status='active'
                    WHERE l.key_id=p.key_id AND l.valid_to IS NULL) AS finding_id
            FROM latest p
            LEFT JOIN translation_workbench_value_slots s ON s.key_id=p.key_id AND s.locale=p.locale AND s.kind='scalar'
            LEFT JOIN translation_workbench_key_names n ON n.key_id=p.key_id AND n.valid_to IS NULL
            SQL;

    public function page(string $search, int $page, int $perPage = 25, string $keyId = '', string $locale = '', string $sort = 'published', string $direction = 'desc'): LengthAwarePaginator
    {
        if (! isset(self::SORTS[$sort]) || ! in_array($direction, ['asc', 'desc'], true)) {
            throw new \RuntimeException('Invalid published sort.');
        }
        $order = self::SORTS[$sort].' '.$direction.' NULLS LAST, event_id '.$direction.', revision '.$direction;
        $perPage = in_array($perPage, [10, 25, 50, 100], true) ? $perPage : 25;
        // Choose the latest destination BEFORE searching: older publications must not reappear.
        $sql = self::CURRENT_PUBLICATIONS;
        [$condition, $params] = TranslationTaskFilter::build($search, $keyId, $locale);
        $where = $condition === '' ? '' : ' WHERE '.$condition;
        $from = ' FROM ('.$sql.') published'.$where;
        $total = (int) $this->query('SELECT count(*)'.$from, $params)->fetchColumn();
        $page = max(1, min($page, max(1, (int) ceil($total / $perPage))));
        $rows = $this->query('SELECT *'.$from.' ORDER BY '.$order.' LIMIT '.$perPage.' OFFSET '.(($page - 1) * $perPage), $params)->fetchAll(PDO::FETCH_ASSOC);
        return new LengthAwarePaginator($rows, $total, $perPage, $page, ['pageName' => 'publishedPage']);
    }

    private function query(string $sql, array $params): \PDOStatement
    {
        $statement = $this->db->prepare($sql);
        $statement->execute($params);
        return $statement;
    }
}
