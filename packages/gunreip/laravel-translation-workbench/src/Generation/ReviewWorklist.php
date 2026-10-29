<?php

namespace Gunreip\TranslationWorkbench\Generation;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use PDO;
use RuntimeException;

/** Explicit submissions are history events tied to the exact saved review context. */
final class ReviewWorklist
{
    public function __construct(private PDO $db) {}

    // A changed value, key name, source link or later review decision invalidates a submission.
    public const CURRENT_SUBMISSIONS = <<<'SQL'
        SELECT r.id AS revision, s.key_id, s.locale, n.namespace, n.full_name,
            e.id AS submission, e.actor_id, e.occurred_at
        FROM translation_workbench_value_slots s
        JOIN translation_workbench_value_revisions r ON r.id=s.current_revision_id
        JOIN translation_workbench_key_names n ON n.key_id=s.key_id AND n.valid_to IS NULL
        JOIN LATERAL (
            SELECT * FROM translation_workbench_events
            WHERE event_type='revision_submitted' AND details->>'revision'=r.id::text
            ORDER BY id DESC LIMIT 1
        ) e ON true
        WHERE s.kind='scalar'
            AND e.details->'context'->>'key_name_id'=n.id::text
            AND e.details->'context'->'usages' = (
                SELECT COALESCE(jsonb_agg(jsonb_build_object('link',l.id,'finding',f.id) ORDER BY l.id),'[]'::jsonb)
                FROM translation_workbench_key_findings l
                JOIN translation_workbench_findings f ON f.id=l.finding_id AND f.status='active'
                WHERE l.key_id=s.key_id AND l.valid_to IS NULL
            )
            AND jsonb_array_length(e.details->'context'->'usages')>0
        SQL;

    public const PENDING = self::CURRENT_SUBMISSIONS.<<<'SQL'
            AND NOT EXISTS (
                SELECT 1 FROM translation_workbench_events d
                WHERE d.id>e.id AND d.details->>'revision'=r.id::text
                    AND d.event_type IN ('revision_approved','revision_approval_revoked')
            )
        SQL;

    public const APPROVED = <<<'SQL'
        SELECT r.id AS revision,s.key_id,s.locale,n.namespace,n.full_name,
            NULL::bigint AS submission,d.actor_id,NULL::timestamptz AS occurred_at
        FROM translation_workbench_value_slots s
        JOIN translation_workbench_value_revisions r ON r.id=s.current_revision_id
        JOIN translation_workbench_key_names n ON n.key_id=s.key_id AND n.valid_to IS NULL
        JOIN LATERAL (SELECT * FROM translation_workbench_reviews WHERE revision_id=r.id ORDER BY id DESC LIMIT 1) d ON true
        WHERE s.kind='scalar' AND d.decision='approved'
            AND d.context_snapshot->>'key_name_id'=n.id::text
            AND d.context_snapshot->'usages'=(
                SELECT COALESCE(jsonb_agg(jsonb_build_object('link',l.id,'finding',f.id) ORDER BY l.id),'[]'::jsonb)
                FROM translation_workbench_key_findings l
                JOIN translation_workbench_findings f ON f.id=l.finding_id AND f.status='active'
                WHERE l.key_id=s.key_id AND l.valid_to IS NULL
            )
            AND jsonb_array_length(d.context_snapshot->'usages')>0
        SQL;

    public const PROGRESSED = self::PENDING.' UNION '.self::APPROVED;

    public const PUBLICATION_PENDING = 'SELECT a.* FROM ('.self::APPROVED.<<<'SQL'
        ) a WHERE NOT EXISTS (
            SELECT 1 FROM translation_workbench_publication_runs p,
                jsonb_array_elements(p.plan::jsonb->'contexts') c
            WHERE p.state='complete'
                AND c->'context'->>'revision_id'=a.revision::text
                AND c->'context'->>'full_name'=a.full_name
                AND c->'context'->>'namespace'=a.namespace
        )
        SQL;

    public const PUBLICATION_SORTS = ['revision' => 'revision', 'key' => 'key_id', 'language' => 'locale'];

    public function publicationPage(string $search, int $page, int $perPage = 25, string $keyId = '', string $locale = '', string $sort = 'revision', string $direction = 'asc'): LengthAwarePaginator
    {
        if (! isset(self::PUBLICATION_SORTS[$sort]) || ! in_array($direction, ['asc', 'desc'], true)) {
            throw new RuntimeException('Invalid publication sort.');
        }
        $order = self::PUBLICATION_SORTS[$sort].' '.$direction.', revision '.$direction;
        return $this->workPage(self::PUBLICATION_PENDING, 'publicationPage', $search, $page, $perPage, $order, $keyId, $locale);
    }

    public function inspectPublication(int $revision): array
    {
        if (! $this->query('SELECT 1 FROM ('.self::PUBLICATION_PENDING.') p WHERE revision=?', [$revision])->fetchColumn()) {
            throw new RuntimeException('This revision is no longer waiting for publication.');
        }
        return RevisionReviewContext::snapshot($this->db, $revision);
    }

    public function submit(array $expected, int $actor): array
    {
        if ($actor < 1 || $expected === []) {
            throw new RuntimeException('Saved revisions and an actor are required.');
        }
        $operation = (string) Str::uuid();
        return (new BaselineOperation($this->db))->run($operation, 'submit_revisions', [$expected, $actor], function () use ($operation, $expected, $actor) {
            $submitted = [];
            foreach ($expected as $revision => $fingerprint) {
                $key = $this->query('SELECT s.key_id FROM translation_workbench_value_revisions r JOIN translation_workbench_value_slots s ON s.id=r.slot_id WHERE r.id=?', [$revision])->fetchColumn();
                if ($key === false) {
                    throw new RuntimeException('Unknown revision');
                }
                $this->query('SELECT id FROM translation_workbench_keys WHERE id=? FOR UPDATE', [$key]);
                $snapshot = RevisionReviewContext::snapshot($this->db, (int) $revision);
                if (! hash_equals($snapshot['fingerprint'], $fingerprint)
                    || (int) $snapshot['context']['current_revision_id'] !== (int) $revision
                    || $snapshot['context']['usages'] === []) {
                    throw new RuntimeException('Review context changed');
                }
                if ((new BaselineRevisionReviewer($this->db))->approvalIsCurrent((int) $revision)) {
                    continue;
                }
                if ($this->query('SELECT 1 FROM ('.self::PENDING.') p WHERE revision=?', [$revision])->fetchColumn()) {
                    continue;
                }
                $this->query('INSERT INTO translation_workbench_events(operation_id,event_index,event_type,actor_id,reason,details) VALUES (?,?,?,?,?,?::jsonb)',
                    [$operation, count($submitted), 'revision_submitted', $actor, 'Submitted saved revision for review.',
                        json_encode(['revision' => (int) $revision, 'context' => $snapshot['context']], JSON_THROW_ON_ERROR)]);
                $submitted[] = (int) $revision;
            }
            return ['submitted' => $submitted];
        });
    }

    public const SORTS = ['key' => 'key_id', 'language' => 'locale', 'submitted' => 'occurred_at'];

    public function page(string $search, int $page, int $perPage = 25, string $sort = 'submitted', string $direction = 'asc', string $keyId = '', string $locale = ''): LengthAwarePaginator
    {
        if (! isset(self::SORTS[$sort]) || ! in_array($direction, ['asc', 'desc'], true)) {
            throw new RuntimeException('Invalid review sort.');
        }
        $order = self::SORTS[$sort].' '.$direction.' NULLS LAST, submission '.$direction.', revision '.$direction;
        return $this->workPage(self::PENDING, 'reviewPage', $search, $page, $perPage, $order, $keyId, $locale);
    }

    private function workPage(string $query, string $pageName, string $search, int $page, int $perPage, string $order = 'submission ASC NULLS LAST, revision ASC', string $keyId = '', string $locale = ''): LengthAwarePaginator
    {
        $perPage = in_array($perPage, [10, 25, 50, 100], true) ? $perPage : 25;
        [$condition, $params] = TranslationTaskFilter::build($search, $keyId, $locale);
        $where = $condition === '' ? '' : ' WHERE '.$condition;
        $from = ' FROM ('.$query.') p'.$where;
        $total = (int) $this->query('SELECT count(*)'.$from, $params)->fetchColumn();
        $page = max(1, min($page, max(1, (int) ceil($total / $perPage))));
        $rows = $this->query("SELECT p.*, (SELECT count(DISTINCT l.finding_id) FROM translation_workbench_key_findings l JOIN translation_workbench_findings f ON f.id=l.finding_id WHERE l.key_id=p.key_id AND l.valid_to IS NULL AND f.status='active') AS active_findings_count".$from.' ORDER BY '.$order.' LIMIT '.$perPage.' OFFSET '.(($page - 1) * $perPage), $params)->fetchAll(PDO::FETCH_ASSOC);
        return new LengthAwarePaginator($rows, $total, $perPage, $page, ['pageName' => $pageName]);
    }

    public function inspect(int $revision): array
    {
        if (! $this->query('SELECT 1 FROM ('.self::PENDING.') p WHERE revision=?', [$revision])->fetchColumn()) {
            throw new RuntimeException('This revision is no longer waiting for review.');
        }
        return RevisionReviewContext::snapshot($this->db, $revision);
    }

    public function decide(int $revision, string $fingerprint, bool $approve, int $actor, string $reason, ?string $reasonLocale = null): array
    {
        $this->inspect($revision);
        return (new BaselineRevisionReviewer($this->db))->decide((string) Str::uuid(), $revision, $fingerprint, $approve, $actor, $reason, $reasonLocale);
    }

    /** Apply the displayed batch atomically; stale submissions invalidate the entire batch. */
    public function decideMany(array $revisions, bool $approve, int $actor, string $reason, ?string $reasonLocale = null): array
    {
        if ($revisions === []) {
            throw new RuntimeException('Select at least one saved revision.');
        }
        ksort($revisions, SORT_NUMERIC);
        return (new BaselineOperation($this->db))->run((string) Str::uuid(), 'review_worklist_batch', [$revisions, $approve, $actor, $reason, $reasonLocale], function () use ($revisions, $approve, $actor, $reason, $reasonLocale) {
            $marks = implode(',', array_fill(0, count($revisions), '?'));
            $keys = $this->query("SELECT DISTINCT s.key_id FROM translation_workbench_value_revisions r JOIN translation_workbench_value_slots s ON s.id=r.slot_id WHERE r.id IN ($marks) ORDER BY s.key_id", array_keys($revisions))->fetchAll(PDO::FETCH_COLUMN);
            foreach ($keys as $key) {
                $this->query('SELECT id FROM translation_workbench_keys WHERE id=? FOR UPDATE', [$key]);
            }
            foreach ($revisions as $revision => $fingerprint) {
                $this->inspect((int) $revision);
            }
            return (new BaselineRevisionReviewer($this->db))->decideMany((string) Str::uuid(), $revisions, $approve, $actor, $reason, $reasonLocale);
        });
    }

    private function query(string $sql, array $params = []): \PDOStatement
    {
        $statement = $this->db->prepare($sql);
        $statement->execute($params);
        return $statement;
    }
}
