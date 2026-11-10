<?php

namespace Gunreip\TranslationWorkbench\Generation;

use Illuminate\Pagination\LengthAwarePaginator;
use PDO;

/** Read-only work queue derived from manual events and current workflow records. */
final class WorkInProgress
{
    public function __construct(private PDO $db) {}

    public function page(string $target, ?int $actor, string $search, int $page, int $perPage = 25): LengthAwarePaginator
    {
        $perPage = in_array($perPage, [10, 25, 50, 100], true) ? $perPage : 25;
        [$sql, $params] = $this->tasks($target, $actor);
        $where = " WHERE (step NOT IN ('complete','archived') OR open_publication IS NOT NULL OR open_source IS NOT NULL)";
        if (trim($search) !== '') {
            $where .= ' AND (full_name ILIKE ? OR key_id::text=? OR EXISTS (
                SELECT 1 FROM translation_workbench_key_findings search_link
                JOIN translation_workbench_findings search_finding ON search_finding.id=search_link.finding_id
                JOIN translation_workbench_source_files search_file ON search_file.id=search_finding.source_file_id
                WHERE search_link.key_id=tasks.key_id AND (search_finding.id::text=? OR search_file.path ILIKE ?)))';
            $needle = trim($search);
            array_push($params, '%'.addcslashes($needle, '\\%_').'%', ltrim($needle, '#'), ltrim($needle, '#'), '%'.addcslashes($needle, '\\%_').'%');
        }
        $total = (int) $this->query($sql.' SELECT count(*) FROM tasks'.$where, $params)->fetchColumn();
        $page = max(1, min($page, max(1, (int) ceil($total / $perPage))));
        $rows = $this->query($sql.' SELECT * FROM tasks'.$where.' ORDER BY occurred_at DESC,event_id DESC,key_id DESC LIMIT '.$perPage.' OFFSET '.(($page - 1) * $perPage), $params)->fetchAll(PDO::FETCH_ASSOC);

        return new LengthAwarePaginator($rows, $total, $perPage, $page, ['pageName' => 'workPage']);
    }

    public function recent(string $target, int $actor): array
    {
        [$sql, $params] = $this->tasks($target, $actor);

        return $this->query($sql.' SELECT * FROM tasks ORDER BY occurred_at DESC,event_id DESC,key_id DESC LIMIT 5', $params)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find(int $key, string $target): ?array
    {
        [$sql, $params] = $this->tasks($target, null);
        $row = $this->query($sql.' SELECT * FROM tasks WHERE key_id=?', [...$params, $key])->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    private function tasks(string $target, ?int $actor): array
    {
        $params = [$target, $actor];
        $sql = <<<'SQL'
            WITH input AS (SELECT ?::text AS target, ?::bigint AS actor), manual AS (
                SELECT e.* FROM translation_workbench_events e
                WHERE e.actor_id IS NOT NULL AND e.event_type IN (
                    'key_assigned','key_created_and_assigned','value_changed','revision_submitted',
                    'revision_approved','revision_approval_revoked','revisions_published',
                    'publication_closed_without_writing','source_movement_reconciled',
                    'source_rewrite_reconciled','source_removal_confirmed','source_replacement_closed'
                )
                AND ((SELECT actor FROM input) IS NULL OR e.actor_id=(SELECT actor FROM input))
            ), event_keys AS (
                SELECT e.id,e.occurred_at,e.actor_id,(e.details->>'key')::bigint AS key_id FROM manual e
                    WHERE e.details->>'key' ~ '^[0-9]+$'
                UNION
                SELECT e.id,e.occurred_at,e.actor_id,s.key_id FROM manual e
                    JOIN translation_workbench_value_revisions r ON r.id::text=e.details->>'revision'
                    JOIN translation_workbench_value_slots s ON s.id=r.slot_id
                UNION
                SELECT e.id,e.occurred_at,e.actor_id,(c->'context'->>'key_id')::bigint FROM manual e
                    JOIN translation_workbench_publication_runs p ON p.id=e.operation_id
                    CROSS JOIN LATERAL jsonb_array_elements(p.plan::jsonb->'contexts') c
                UNION
                SELECT 0::bigint,o.completed_at,(p.plan::jsonb->>'actor')::bigint,(c->'context'->>'key_id')::bigint
                    FROM translation_workbench_operations o
                    JOIN translation_workbench_publication_runs p ON p.id=o.id
                    CROSS JOIN LATERAL jsonb_array_elements(p.plan::jsonb->'contexts') c
                    WHERE o.kind='publication_prepare'
                        AND ((SELECT actor FROM input) IS NULL OR p.plan::jsonb->>'actor'=(SELECT actor::text FROM input))
                UNION
                SELECT 0::bigint,o.completed_at,(o.result->>'actor')::bigint,(o.result->'preview'->>'key_id')::bigint
                    FROM translation_workbench_operations o
                    WHERE o.kind='source_replacement_prepared'
                        AND o.result->'preview'->>'key_id' IS NOT NULL
                        AND ((SELECT actor FROM input) IS NULL OR o.result->>'actor'=(SELECT actor::text FROM input))
            ), last_work AS (
                SELECT DISTINCT ON (key_id) key_id,id AS event_id,occurred_at,actor_id
                FROM event_keys ORDER BY key_id,occurred_at DESC,id DESC
            -- Evaluate confirmations once and use the small operation receipt, not the large scan JSON.
            ), active AS MATERIALIZED (
                SELECT l.key_id,f.id,f.raw_source_code_literal,
                    (f.raw_source_code_literal IS NOT DISTINCT FROM
                        CASE WHEN n.namespace='' THEN n.full_name ELSE n.namespace||'::'||n.full_name END) AS source_set,
                    EXISTS (SELECT 1 FROM translation_workbench_scan_findings sf
                        JOIN translation_workbench_scan_runs sr ON sr.id=sf.scan_run_id AND sr.status='completed'
                        JOIN translation_workbench_operations op ON op.id=sr.id AND op.kind='complete_rescan'
                        WHERE sf.finding_id=f.id) AS scanned
                FROM translation_workbench_key_findings l
                JOIN last_work w ON w.key_id=l.key_id
                JOIN translation_workbench_findings f ON f.id=l.finding_id AND f.status='active'
                JOIN translation_workbench_key_names n ON n.key_id=l.key_id AND n.valid_to IS NULL
                WHERE l.valid_to IS NULL
            ), usages AS (
                SELECT key_id,count(*) AS finding_count,
                    (array_agg(id ORDER BY source_set,scanned,id))[1] AS finding_id,
                    count(*) FILTER (WHERE NOT source_set) AS source_pending,
                    count(*) FILTER (WHERE NOT scanned) AS scan_pending
                FROM active GROUP BY key_id
            ), pending AS (
            SQL.ReviewWorklist::PENDING.'), approved AS ('.ReviewWorklist::APPROVED.'), published AS ('.PublishedWorklist::CURRENT_PUBLICATIONS.<<<'SQL'
            ), languages AS (
                SELECT key_id,'en' AS locale FROM last_work
                UNION SELECT key_id,(SELECT target FROM input) FROM last_work
                UNION SELECT s.key_id,s.locale FROM translation_workbench_value_slots s JOIN last_work w USING(key_id) WHERE s.kind='scalar'
                UNION SELECT e.key_id,e.locale FROM published_value_evidence e JOIN last_work w USING(key_id) WHERE e.state='linked'
            ), language_states AS (
                SELECT wanted.key_id,wanted.locale,
                    CASE WHEN a.revision IS NOT NULL AND pub.revision IS NOT NULL THEN 4
                         WHEN a.revision IS NOT NULL THEN 3
                         WHEN p.revision IS NOT NULL THEN 2
                         WHEN s.current_revision_id IS NULL AND EXISTS (
                             SELECT 1 FROM published_value_evidence e WHERE e.key_id=wanted.key_id
                             AND e.locale=wanted.locale AND e.state='linked' AND e.full_name=n.full_name AND n.namespace=''
                         ) THEN 4 ELSE 1 END AS stage
                FROM languages wanted
                JOIN translation_workbench_key_names n ON n.key_id=wanted.key_id AND n.valid_to IS NULL
                LEFT JOIN translation_workbench_value_slots s ON s.key_id=wanted.key_id AND s.locale=wanted.locale AND s.kind='scalar'
                LEFT JOIN pending p ON p.revision=s.current_revision_id
                LEFT JOIN approved a ON a.revision=s.current_revision_id
                LEFT JOIN published pub ON pub.key_id=wanted.key_id AND pub.locale=wanted.locale AND pub.working_state=0
            ), progress AS (
                SELECT key_id,min(stage) AS stage,string_agg(DISTINCT locale,', ' ORDER BY locale) AS locales
                FROM language_states GROUP BY key_id
            ), tasks AS (
                SELECT w.*,n.namespace,n.full_name,COALESCE(u.finding_count,0) AS finding_count,
                    COALESCE(u.finding_id,(SELECT l.finding_id FROM translation_workbench_key_findings l WHERE l.key_id=w.key_id ORDER BY l.id DESC LIMIT 1)) AS finding_id,
                    COALESCE(u.source_pending,0) AS source_pending,COALESCE(u.scan_pending,0) AS scan_pending,
                    p.locales,
                    (SELECT COALESCE(o.result->>'batch',o.id::text) FROM translation_workbench_operations o
                        WHERE o.kind='source_replacement_prepared'
                        AND o.result->'preview'->>'key_id'=w.key_id::text
                        AND NOT EXISTS (SELECT 1 FROM translation_workbench_operations done WHERE done.id=(o.result->>'finish')::uuid)
                        ORDER BY o.completed_at DESC LIMIT 1) AS open_source,
                    (SELECT run.id FROM translation_workbench_publication_runs run
                        WHERE run.state IN ('prepared','writing','interrupted')
                        AND EXISTS (SELECT 1 FROM jsonb_array_elements(run.plan::jsonb->'contexts') c
                            WHERE c->'context'->>'key_id'=w.key_id::text)
                        ORDER BY run.id LIMIT 1) AS open_publication,
                    CASE WHEN u.finding_count IS NULL THEN 'archived'
                         WHEN p.stage=1 THEN 'translations'
                         WHEN p.stage=2 THEN 'review'
                         WHEN p.stage=3 THEN 'publication'
                         WHEN u.source_pending>0 THEN 'source'
                         WHEN u.scan_pending>0 THEN 'scan'
                         ELSE 'complete' END AS step
                FROM last_work w
                JOIN translation_workbench_key_names n ON n.key_id=w.key_id AND n.valid_to IS NULL
                LEFT JOIN usages u ON u.key_id=w.key_id
                LEFT JOIN progress p ON p.key_id=w.key_id
            )
            SQL;

        return [$sql, $params];
    }

    private function query(string $sql, array $params): \PDOStatement
    {
        $statement = $this->db->prepare($sql);
        $statement->execute($params);

        return $statement;
    }
}
