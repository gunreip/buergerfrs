<?php

namespace Gunreip\TranslationWorkbench\Generation;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use PDO;
use RuntimeException;

/** Repository for the new schema only. No legacy model or file-export writes. */
class GenerationEditor
{
    public function __construct(private PDO $db, private ?string $sourceRoot = null, private ?string $languageRoot = null) {}

    private function query(string $sql, array $parameters = []): \PDOStatement
    {
        $query = $this->db->prepare($sql);
        $query->execute($parameters);

        return $query;
    }

    private const FINDINGS = ' FROM translation_workbench_findings f
        JOIN translation_workbench_source_files s ON s.id=f.source_file_id
        LEFT JOIN translation_workbench_key_findings l ON l.finding_id=f.id AND l.valid_to IS NULL
        LEFT JOIN translation_workbench_key_names n ON n.key_id=l.key_id AND n.valid_to IS NULL';

    public const FINDING_SORTS = [
        'id' => ['f.id'],
        'source' => ['s.path', 'f.source_line', 'f.source_column'],
        'expression' => ['f.raw_expression'],
        'suggested_key' => ['f.suggested_key'],
        'key' => ['n.full_name'],
    ];

    public function page(string $search, string $id, string $status, int $page, string $sort = 'id', string $direction = 'desc', int $perPage = 25, bool $unassignedOnly = false, bool $includeArchived = true): LengthAwarePaginator
    {
        $perPage = in_array($perPage, [10, 25, 50, 100], true) ? $perPage : 25;
        if (! isset(self::FINDING_SORTS[$sort]) || ! in_array($direction, ['asc', 'desc'], true)) {
            throw new RuntimeException('Invalid findings sort.');
        }
        $order = array_map(fn ($column) => $column.' '.$direction.' NULLS LAST', self::FINDING_SORTS[$sort]);
        if ($sort !== 'id') {
            $order[] = 'f.id '.$direction;
        }
        $where = $unassignedOnly ? ['l.key_id IS NULL'] : [];
        $parameters = [];
        if (! in_array($status, ['all', 'all_compact'], true) || ! $includeArchived) {
            $where[] = 'f.status=?';
            $parameters[] = $status === 'obsolete' ? 'obsolete' : 'active';
        }
        if (trim($id) !== '') {
            if (! ctype_digit($id) || strlen($id) > 18) {
                $where[] = 'FALSE';
            } else {
                $where[] = 'f.id=?';
                $parameters[] = $id;
            }
        }
        if (trim($search) !== '') {
            $where[] = '(s.path ILIKE ? OR f.raw_expression ILIKE ? OR n.full_name ILIKE ?)';
            $term = '%'.str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], trim($search)).'%';
            array_push($parameters, $term, $term, $term);
        }
        $sql = self::FINDINGS.($where ? ' WHERE '.implode(' AND ', $where) : '');
        $prefix = '';
        $compactCount = '1';
        if (in_array($status, ['active_compact', 'all_compact'], true)) {
            // Group active nonempty candidate literals within the current filters.
            // Archived and unresolved captures each retain their own row.
            $group = "CASE WHEN f.status='active' AND length(trim(COALESCE(f.raw_literal,f.suggested_literal)))>0
                THEN jsonb_build_array('literal', COALESCE(f.raw_literal,f.suggested_literal))
                ELSE jsonb_build_array('finding', f.id::text) END";
            $prefix = 'WITH candidate_groups AS (SELECT f.id, min(f.id) OVER (PARTITION BY '.$group.') AS representative,
                count(*) OVER (PARTITION BY '.$group.') AS finding_count'.$sql.') ';
            $sql = self::FINDINGS.' JOIN candidate_groups g ON g.id=f.id WHERE g.id=g.representative';
            $compactCount = 'g.finding_count';
        }
        $count = (int) $this->query($prefix.'SELECT count(*)'.$sql, $parameters)->fetchColumn();
        $page = max(1, min($page, max(1, (int) ceil($count / $perPage))));
        $rows = $this->query($prefix."SELECT ".$compactCount." AS compact_count,f.id,f.status,f.retired_reason,f.raw_expression,f.suggested_key,s.path,f.source_line,f.source_column,l.key_id,n.full_name,
            (SELECT next.id FROM translation_workbench_findings next WHERE next.predecessor_id=f.id) AS successor_id,
            (SELECT CASE WHEN next.provenance->>'mode'='confirmed_rewrite' THEN 'replaced'
                WHEN next.raw_expression=f.raw_expression OR next.provenance->>'mode'='confirmed_movement' THEN 'moved'
                ELSE 'replaced' END FROM translation_workbench_findings next WHERE next.predecessor_id=f.id) AS successor_kind".$sql.' ORDER BY '.implode(', ', $order).' LIMIT '.$perPage.' OFFSET '.(($page - 1) * $perPage), $parameters)->fetchAll(PDO::FETCH_ASSOC);

        return new LengthAwarePaginator($rows, $count, $perPage, $page, ['pageName' => 'findingsPage']);
    }

    public const TRANSLATION_SORTS = ['key', 'namespace', 'findings'];

    /** One task per assigned key, regardless of the number of active source occurrences. */
    public function translationTasks(string $search, int $page, int $perPage = 25, string $sort = 'key', string $direction = 'desc', string $keyId = ''): LengthAwarePaginator
    {
        $perPage = in_array($perPage, [10, 25, 50, 100], true) ? $perPage : 25;
        if (! in_array($sort, self::TRANSLATION_SORTS, true) || ! in_array($direction, ['asc', 'desc'], true)) {
            throw new RuntimeException('Invalid translations sort.');
        }
        $order = match ($sort) {
            'key' => 'n.key_id '.$direction,
            'findings' => 'findings_count '.$direction.', n.key_id '.$direction,
            default => "(CASE WHEN n.namespace='' THEN n.full_name ELSE n.namespace || '::' || n.full_name END) ".$direction.', n.key_id '.$direction,
        };
        [$condition, $parameters] = TranslationTaskFilter::build($search, $keyId, prefix: 'n.');
        $where = $condition === '' ? '' : ' AND '.$condition;
        $from = " FROM translation_workbench_key_names n
            JOIN translation_workbench_key_findings l ON l.key_id=n.key_id AND l.valid_to IS NULL
            JOIN translation_workbench_findings f ON f.id=l.finding_id AND f.status='active'
            WHERE n.valid_to IS NULL AND (
                NOT EXISTS (SELECT 1 FROM translation_workbench_value_slots vs WHERE vs.key_id=n.key_id AND vs.kind='scalar' AND vs.current_revision_id IS NOT NULL)
                OR EXISTS (SELECT 1 FROM translation_workbench_value_slots vs WHERE vs.key_id=n.key_id AND vs.kind='scalar' AND vs.current_revision_id IS NOT NULL
                    AND NOT EXISTS (SELECT 1 FROM (".ReviewWorklist::PROGRESSED.") progressed WHERE progressed.revision=vs.current_revision_id))
            )".$where;
        $count = (int) $this->query('SELECT count(DISTINCT n.key_id)'.$from, $parameters)->fetchColumn();
        $page = max(1, min($page, max(1, (int) ceil($count / $perPage))));
        $rows = $this->query('SELECT n.key_id,n.namespace,n.full_name,count(*) AS findings_count,min(f.id) AS finding_id'.$from.
            ' GROUP BY n.key_id,n.namespace,n.full_name ORDER BY '.$order.' LIMIT '.$perPage.' OFFSET '.(($page - 1) * $perPage), $parameters)->fetchAll(PDO::FETCH_ASSOC);
        $languages = [];
        if ($rows !== []) {
            $ids = array_column($rows, 'key_id');
            $marks = implode(',', array_fill(0, count($ids), '?'));
            foreach ($this->query("SELECT key_id,locale,current_revision_id AS revision FROM translation_workbench_value_slots WHERE key_id IN ($marks) AND kind='scalar' AND current_revision_id IS NOT NULL ORDER BY locale", $ids)->fetchAll(PDO::FETCH_ASSOC) as $language) {
                $languages[$language['key_id']][] = $language;
            }
        }
        foreach ($rows as &$row) {
            $row['locales'] = $languages[$row['key_id']] ?? [];
        }
        unset($row);
        return new LengthAwarePaginator($rows, $count, $perPage, $page, ['pageName' => 'translationsPage']);
    }

    /** Exact captured text is evidence for a candidate, never an automatic assignment. */
    /** Per-locale progress for the visible active findings, using the same rules as the workflow tabs. */
    public function workflowFindingForKey(int $key): int
    {
        $id = $this->query("SELECT f.id FROM translation_workbench_key_findings l JOIN translation_workbench_findings f ON f.id=l.finding_id WHERE l.key_id=? AND l.valid_to IS NULL AND f.status='active' ORDER BY f.id LIMIT 1", [$key])->fetchColumn();
        if ($id === false) throw new RuntimeException('No active finding linked to this key.');
        return (int) $id;
    }

    public function workflowOverview(int $finding, string $target): array
    {
        $context = $this->inspect($finding, 'en');
        $row = $context['finding'];
        $states = array_column($this->workflowStates([$finding])[$finding] ?? [], null, 'locale');
        // Include source, current main language and saved variants; unused variants are not tasks.
        $locales = array_values(array_unique(['en', $target, ...array_keys($states)]));
        $languages = [];
        foreach ($locales as $locale) {
            $languages[] = ['locale' => $locale, 'state' => $states[$locale]['state'] ?? 'missing', 'revision' => $states[$locale]['revision'] ?? null];
        }
        $sourceSet = $confirmed = false;
        $sourceIssue = null;
        if ($row['status'] === 'active' && $row['key_id']) {
            try {
                $source = (new SourceReplacementPreview)->inspect($context, 'en', $this->sourceRoot ?? base_path(), $this->languageRoot ?? lang_path());
                $sourceSet = $source['already_set'];
                if ($sourceSet) {
                    $confirmed = (bool) $this->query("SELECT 1 FROM translation_workbench_scan_findings sf JOIN translation_workbench_scan_runs s ON s.id=sf.scan_run_id WHERE sf.finding_id=? AND s.status='completed' AND s.scope_snapshot->>'mode'='generation_rescan' AND s.scope_snapshot->'inventory'->'hashes'->>? = ? LIMIT 1", [$finding, $row['path'], $source['source_sha256']])->fetchColumn();
                } elseif (!$source['ready']) {
                    $sourceIssue = $source['blocker'];
                }
            } catch (RuntimeException $error) {
                $sourceIssue = $error->getMessage();
            }
        }
        return [
            'finding' => $row,
            'successor' => $context['successor'],
            'shared' => $this->sharedSummaries([$finding])[$finding] ?? [],
            'languages' => $languages,
            'steps' => (new WorkflowProgress)->steps($row, $languages, $sourceSet, $confirmed, $sourceIssue),
        ];
    }

    public function workflowStates(array $ids): array
    {
        if ($ids === []) {
            return [];
        }
        $marks = implode(',', array_fill(0, count($ids), '?'));
        $sql = 'WITH pending AS ('.ReviewWorklist::PENDING.'),
            approved AS ('.ReviewWorklist::APPROVED.'),
            published AS ('.PublishedWorklist::CURRENT_PUBLICATIONS.")
            SELECT f.id AS finding_id,s.locale,r.id AS revision,
                CASE WHEN a.revision IS NOT NULL AND pub.current_revision IS NOT NULL THEN 'published'
                    WHEN a.revision IS NOT NULL THEN 'approved'
                    WHEN p.revision IS NOT NULL THEN 'review'
                    ELSE 'draft' END AS state,
                pub.published_revision
            FROM translation_workbench_findings f
            JOIN translation_workbench_key_findings l ON l.finding_id=f.id AND l.valid_to IS NULL
            JOIN translation_workbench_value_slots s ON s.key_id=l.key_id AND s.kind='scalar'
            JOIN translation_workbench_value_revisions r ON r.id=s.current_revision_id
            LEFT JOIN pending p ON p.revision=r.id
            LEFT JOIN approved a ON a.revision=r.id
            LEFT JOIN LATERAL (
                SELECT max(revision) AS published_revision,
                    max(revision) FILTER (WHERE working_state=0) AS current_revision
                FROM published WHERE key_id=s.key_id AND locale=s.locale
            ) pub ON true
            WHERE f.status='active' AND f.id IN ($marks)
            ORDER BY f.id,s.locale";
        $result = [];
        foreach ($this->query($sql, $ids)->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $result[$row['finding_id']][] = $row;
        }

        return $result;
    }

    public function sharedSummaries(array $ids): array
    {
        if ($ids === []) {
            return [];
        }
        $marks = implode(',', array_fill(0, count($ids), '?'));
        $rows = $this->query("WITH texts AS (
            SELECT COALESCE(raw_literal,suggested_literal) AS text,count(*) AS total
            FROM translation_workbench_findings WHERE status='active'
            GROUP BY COALESCE(raw_literal,suggested_literal)
        ), usages AS (
            SELECT l.key_id,count(*) AS total FROM translation_workbench_key_findings l
            JOIN translation_workbench_findings f ON f.id=l.finding_id
            WHERE l.valid_to IS NULL AND f.status='active' GROUP BY l.key_id
        ) SELECT f.id,f.status,l.key_id,n.namespace,n.full_name,COALESCE(raw_literal,suggested_literal) AS literal,
            CASE WHEN f.status='active' AND length(trim(COALESCE(raw_literal,suggested_literal)))>0
                THEN COALESCE(t.total,0) ELSE 0 END AS candidates,
            CASE WHEN f.status='active' THEN COALESCE(u.total,0) ELSE 0 END AS shared
            FROM translation_workbench_findings f
            LEFT JOIN texts t ON t.text=COALESCE(f.raw_literal,f.suggested_literal)
            LEFT JOIN translation_workbench_key_findings l ON l.finding_id=f.id AND l.valid_to IS NULL
            LEFT JOIN translation_workbench_key_names n ON n.key_id=l.key_id AND n.valid_to IS NULL
            LEFT JOIN usages u ON u.key_id=l.key_id WHERE f.id IN ($marks)", $ids)->fetchAll(PDO::FETCH_ASSOC);

        return array_column($rows, null, 'id');
    }

    public function sharedFindings(int $finding, int $page): LengthAwarePaginator
    {
        $where = " WHERE f.status='active' AND (f.id=? OR
            (length(trim(COALESCE(f.raw_literal,f.suggested_literal)))>0 AND
                COALESCE(f.raw_literal,f.suggested_literal)=(SELECT COALESCE(raw_literal,suggested_literal) FROM translation_workbench_findings WHERE id=?))
            OR l.key_id=(SELECT key_id FROM translation_workbench_key_findings WHERE finding_id=? AND valid_to IS NULL))";
        $params = [$finding, $finding, $finding];
        $count = (int) $this->query('SELECT count(*)'.self::FINDINGS.$where, $params)->fetchColumn();
        $page = max(1, min($page, max(1, (int) ceil($count / 25))));
        $rows = $this->query('SELECT f.id,f.suggested_key,s.path,f.source_line,f.source_column,COALESCE(f.raw_literal,f.suggested_literal) AS literal,l.key_id,n.full_name'.self::FINDINGS.$where.' ORDER BY (f.id=?) DESC,f.id LIMIT 25 OFFSET '.(($page - 1) * 25), [...$params, $finding])->fetchAll(PDO::FETCH_ASSOC);

        return new LengthAwarePaginator($rows, $count, 25, $page, ['pageName' => 'sharedFindingsPage']);
    }

    public function recentScans(): array
    {
        return $this->query("SELECT id,completed_at,result->>'confirmed' AS confirmed,
            jsonb_array_length(result->'created') AS created,jsonb_array_length(result->'moved') AS moved,
            jsonb_array_length(result->'removed') AS removed,jsonb_array_length(result->'review_files') AS review_files,
            COALESCE(jsonb_array_length(result->'usage_decisions'),0) AS usage_decisions
            FROM translation_workbench_operations WHERE kind='complete_rescan' ORDER BY completed_at DESC,id DESC LIMIT 20")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function scanPage(string $run, string $category, int $page): LengthAwarePaginator
    {
        if (! Str::isUuid($run) || ! in_array($category, ['created', 'moved', 'removed', 'review_files', 'usage_decisions'], true)) {
            throw new RuntimeException('Invalid scan or result category.');
        }
        $total = $this->query("SELECT jsonb_array_length(result->?) FROM translation_workbench_operations WHERE id=? AND kind='complete_rescan'", [$category, $run])->fetchColumn();
        if ($total === false) {
            throw new RuntimeException('Scan not found.');
        }
        $page = max(1, min($page, max(1, (int) ceil($total / 25))));
        $items = $this->query("SELECT item::text FROM translation_workbench_operations o,
            LATERAL jsonb_array_elements(o.result->?) WITH ORDINALITY AS entries(item,position)
            WHERE o.id=? AND o.kind='complete_rescan' ORDER BY position LIMIT 25 OFFSET ".(($page - 1) * 25), [$category, $run])->fetchAll(PDO::FETCH_COLUMN);
        $rows = [];
        foreach ($items as $json) {
            $item = json_decode($json, true, flags: JSON_THROW_ON_ERROR);
            if ($category === 'usage_decisions') {
                $rows[] = ['id' => null, 'predecessor' => null, 'path' => $item['path'], 'source_line' => $item['line'],
                    'usage_decision' => $item['decision'], 'decision_event_id' => $item['event_id']];
                continue;
            }
            if ($category === 'review_files') {
                $review = $this->query("SELECT scope_snapshot->'reviews'->?->>'state' AS state,
                    COALESCE(scope_snapshot->'reviews'->?->'issues','[]'::jsonb)::text AS issues,
                    COALESCE(jsonb_array_length(scope_snapshot->'reviews'->?->'preserved'),0) AS preserved_count
                    FROM translation_workbench_scan_runs WHERE id=?", [$item, $item, $item, $run])->fetch(PDO::FETCH_ASSOC);
                $issues = $review ? json_decode($review['issues'], true, flags: JSON_THROW_ON_ERROR) : [];
                if (($review['state'] ?? null) === 'relocation_review') {
                    array_unshift($issues, ['reason' => 'relocation_review']);
                }
                if ($issues === []) {
                    $issues[] = ['reason' => 'review_details_unavailable'];
                }
                $rows[] = ['id' => null, 'predecessor' => null, 'path' => $item, 'source_line' => null,
                    'issues' => $issues, 'preserved_count' => $review ? (int) $review['preserved_count'] : null];
                continue;
            }
            $id = $category === 'moved' ? $item['successor'] : $item;
            $row = $this->query('SELECT f.id,s.path,f.source_line FROM translation_workbench_findings f JOIN translation_workbench_source_files s ON s.id=f.source_file_id WHERE f.id=?', [$id])->fetch(PDO::FETCH_ASSOC);
            if (! $row) {
                throw new RuntimeException('A finding referenced by this scan is missing.');
            }
            $rows[] = $row + ['predecessor' => $category === 'moved' ? $item['predecessor'] : null];
        }

        return new LengthAwarePaginator($rows, (int) $total, 25, $page, ['pageName' => 'scanResultsPage']);
    }

    public function preservedFindingsPage(string $run, string $path, int $page): LengthAwarePaginator
    {
        if (! Str::isUuid($run)) {
            throw new RuntimeException('Invalid scan.');
        }
        $count = $this->query("SELECT COALESCE(jsonb_array_length(s.scope_snapshot->'reviews'->?->'preserved'),0)
            FROM translation_workbench_scan_runs s JOIN translation_workbench_operations o ON o.id=s.id
            WHERE s.id=? AND o.kind='complete_rescan' AND o.result->'review_files' @> ?::jsonb",
            [$path, $run, json_encode([$path], JSON_THROW_ON_ERROR)])->fetchColumn();
        if ($count === false) {
            throw new RuntimeException('This file is not part of the selected scan review.');
        }
        $page = max(1, min($page, max(1, (int) ceil($count / 25))));
        $rows = $this->query("WITH preserved AS (
            SELECT value::bigint AS finding,position FROM translation_workbench_scan_runs s,
            LATERAL jsonb_array_elements_text(COALESCE(s.scope_snapshot->'reviews'->?->'preserved','[]'::jsonb)) WITH ORDINALITY AS ids(value,position)
            WHERE s.id=? ORDER BY position LIMIT 25 OFFSET ".(($page - 1) * 25).")
            SELECT f.id,f.status,f.source_line,f.source_column,f.raw_source_code_literal,n.full_name
            FROM preserved p JOIN translation_workbench_findings f ON f.id=p.finding
            LEFT JOIN translation_workbench_key_findings l ON l.finding_id=f.id AND l.valid_to IS NULL
            LEFT JOIN translation_workbench_key_names n ON n.key_id=l.key_id AND n.valid_to IS NULL ORDER BY p.position", [$path, $run])->fetchAll(PDO::FETCH_ASSOC);

        return new LengthAwarePaginator($rows, (int) $count, 25, $page, ['pageName' => 'preservedFindingsPage']);
    }

    public function scanCapturesPage(string $run, string $path, int $page): LengthAwarePaginator
    {
        if (! Str::isUuid($run)) {
            throw new RuntimeException('Invalid scan.');
        }
        $count = $this->query("SELECT COALESCE(jsonb_array_length(s.scope_snapshot->'reviews'->?->'captures'),0)
            FROM translation_workbench_scan_runs s JOIN translation_workbench_operations o ON o.id=s.id
            WHERE s.id=? AND o.kind='complete_rescan' AND o.result->'review_files' @> ?::jsonb",
            [$path, $run, json_encode([$path], JSON_THROW_ON_ERROR)])->fetchColumn();
        if ($count === false) {
            throw new RuntimeException('This file is not part of the selected scan review.');
        }
        $page = max(1, min($page, max(1, (int) ceil($count / 25))));
        $rows = $this->query("SELECT (position-1)::integer AS capture_index,capture->>'raw_expression' AS raw_expression,
            capture->>'source_line' AS source_line,capture->>'source_column' AS source_column,
            capture->>'raw_literal' AS raw_literal,capture->>'found_translation_key' AS found_translation_key,
            capture->>'reason' AS reason,capture->>'source_sha256' AS source_sha256,
            capture->>'source_offset' AS source_offset,capture->>'source_path' AS source_path,
            capture->'source_context'->>'state' AS context_state
            FROM translation_workbench_scan_runs s,
            LATERAL jsonb_array_elements(COALESCE(s.scope_snapshot->'reviews'->?->'captures','[]'::jsonb)) WITH ORDINALITY AS entries(capture,position)
            WHERE s.id=? ORDER BY position LIMIT 25 OFFSET ".(($page - 1) * 25), [$path, $run])->fetchAll(PDO::FETCH_ASSOC);

        $identities = [];
        foreach ($rows as $index => $row) {
            $rows[$index]['can_classify'] = $row['reason'] === 'dynamic_or_nonliteral_argument'
                && $row['context_state'] === 'confirmed_call';
            if ($rows[$index]['can_classify']) {
                $identities[$index] = ScanCaptureDecisions::identity([
                    ...$row, 'source_context' => ['state' => $row['context_state']],
                ]);
            }
        }
        $decisions = (new ScanCaptureDecisions($this->db, $this->sourceRoot))->forIdentities($identities);
        foreach ($rows as $index => $row) {
            $rows[$index]['usage_decision'] = isset($identities[$index]) ? ($decisions[$identities[$index]] ?? null) : null;
        }

        return new LengthAwarePaginator($rows, (int) $count, 25, $page, ['pageName' => 'scanCapturesPage']);
    }

    public function consideredScanUsage(string $run, int $event): array
    {
        if (! Str::isUuid($run) || $event < 1) {
            throw new RuntimeException('Invalid scan decision.');
        }
        $json = $this->query("SELECT e.details FROM translation_workbench_events e
            JOIN translation_workbench_operations o ON o.id=? AND o.kind='complete_rescan'
            WHERE e.id=? AND e.event_type='scan_source_usage_decided'
                AND o.result->'usage_decisions' @> ?::jsonb",
            [$run, $event, json_encode([['event_id' => $event]], JSON_THROW_ON_ERROR)])->fetchColumn();
        if (! $json) {
            throw new RuntimeException('This decision was not considered by the selected scan.');
        }
        $details = json_decode($json, true, flags: JSON_THROW_ON_ERROR);
        $context = $this->scanUsage($details['scan'], $details['source_path'], (int) $details['capture_index']);
        if ($context['identity'] !== $details['identity']) {
            throw new RuntimeException('Decision source evidence does not match.');
        }

        return $context;
    }

    public function scanUsageReasons(string $decision): array
    {
        return (new ScanCaptureDecisions($this->db, $this->sourceRoot))->reasons($decision);
    }

    public function scanUsage(string $run, string $path, int $index): array
    {
        return (new ScanCaptureDecisions($this->db, $this->sourceRoot))->inspect($run, $path, $index);
    }

    public function decideScanUsage(string $run, string $path, int $index, string $decision, string $reason, int $actor, int $expected): array
    {
        return (new ScanCaptureDecisions($this->db, $this->sourceRoot))->save($run, $path, $index, $decision, $reason, $actor, $expected);
    }

    public function keys(): array
    {
        return $this->query('SELECT key_id,namespace,full_name FROM translation_workbench_key_names WHERE valid_to IS NULL ORDER BY full_name,key_id')->fetchAll(PDO::FETCH_ASSOC);
    }

    public function totals(): array
    {
        return $this->db->query("SELECT current_database() AS database,
            (SELECT count(*) FROM translation_workbench_findings WHERE status='active') AS active,
            (SELECT count(*) FROM translation_workbench_findings WHERE status='obsolete') AS archived,
            (SELECT count(*) FROM translation_workbench_keys) AS keys,
            (SELECT count(*) FROM translation_workbench_value_revisions) AS revisions")->fetch(PDO::FETCH_ASSOC);
    }

    public function inspect(int $finding, string $locale): array
    {
        $this->db->beginTransaction();
        try {
            $this->db->exec('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ, READ ONLY');
            $row = $this->query('SELECT f.*,s.path,l.id AS link_id,l.key_id,n.full_name,n.namespace'.self::FINDINGS.' WHERE f.id=?', [$finding])->fetch(PDO::FETCH_ASSOC);
            if (! $row) {
                throw new RuntimeException('Finding not found.');
            }
            $revision = $this->query("SELECT r.* FROM translation_workbench_value_slots s JOIN translation_workbench_value_revisions r ON r.id=s.current_revision_id WHERE s.key_id=? AND s.locale=? AND s.kind='scalar'", [$row['key_id'], $locale])->fetch(PDO::FETCH_ASSOC) ?: null;
            $published = $this->query("SELECT e.value,e.path,s.sha256 FROM published_value_evidence e JOIN published_value_sources s USING(baseline_hash,path,locale) WHERE e.key_id=? AND e.locale=? AND e.state='linked' AND e.full_name=?", [$row['key_id'], $locale, $row['full_name']])->fetchAll(PDO::FETCH_ASSOC);
            foreach ($published as &$evidence) {
                $evidence['current'] = ! str_starts_with($evidence['path'], '/') && ! in_array('..', explode('/', $evidence['path']), true)
                    && @hash_file('sha256', ($this->sourceRoot ?? base_path()).'/'.$evidence['path']) === $evidence['sha256'];
            }
            unset($evidence);
            $sourceRevision = $locale === 'en' ? $revision : ($this->query("SELECT r.id,r.value FROM translation_workbench_value_slots s JOIN translation_workbench_value_revisions r ON r.id=s.current_revision_id WHERE s.key_id=? AND s.locale='en' AND s.kind='scalar'", [$row['key_id']])->fetch(PDO::FETCH_ASSOC) ?: null);
            $sourceText = $sourceRevision['value'] ?? null;
            if ($sourceText === null) {
                $sourceEvidence = $this->query("SELECT e.value,e.path,s.sha256 FROM published_value_evidence e JOIN published_value_sources s USING(baseline_hash,path,locale) WHERE e.key_id=? AND e.locale='en' AND e.state='linked' AND e.full_name=?", [$row['key_id'], $row['full_name']])->fetchAll(PDO::FETCH_ASSOC);
                if (count($sourceEvidence) === 1) {
                    $e = $sourceEvidence[0];
                    if (! str_starts_with($e['path'], '/') && ! in_array('..', explode('/', $e['path']), true)
                        && @hash_file('sha256', ($this->sourceRoot ?? base_path()).'/'.$e['path']) === $e['sha256']) {
                        $sourceText = $e['value'];
                    }
                }
            }

            $usages = $this->query('SELECT f.id,f.status,s.path,f.source_line FROM translation_workbench_key_findings l JOIN translation_workbench_findings f ON f.id=l.finding_id JOIN translation_workbench_source_files s ON s.id=f.source_file_id WHERE l.key_id=? AND l.valid_to IS NULL ORDER BY f.id LIMIT 100', [$row['key_id']])->fetchAll(PDO::FETCH_ASSOC);
            $usageCount = (int) $this->query('SELECT count(*) FROM translation_workbench_key_findings WHERE key_id=? AND valid_to IS NULL', [$row['key_id']])->fetchColumn();
            $successor = $this->query('SELECT id FROM translation_workbench_findings WHERE predecessor_id=?', [$finding])->fetchColumn();
            $history = $this->query("SELECT id,event_type,actor_id,reason,occurred_at,details FROM translation_workbench_events WHERE details->>'finding'=? OR details @> ?::jsonb OR details @> ?::jsonb OR details->>'key'=? OR details->>'revision' IN (SELECT r.id::text FROM translation_workbench_value_revisions r JOIN translation_workbench_value_slots s ON s.id=r.slot_id WHERE s.key_id=?) OR operation_id IN (SELECT id FROM translation_workbench_publication_runs WHERE plan::jsonb @> ?::jsonb) ORDER BY id DESC LIMIT 50", [(string) $finding, json_encode(['successors' => [['predecessor' => $finding]]], JSON_THROW_ON_ERROR), json_encode(['successors' => [['successor' => $finding]]], JSON_THROW_ON_ERROR), (string) ($row['key_id'] ?? ''), $row['key_id'], json_encode(['contexts' => [['context' => ['key_id' => $row['key_id']]]]], JSON_THROW_ON_ERROR)])->fetchAll(PDO::FETCH_ASSOC);
            foreach ($history as &$event) {
                $details = json_decode($event['details'], true, flags: JSON_THROW_ON_ERROR);
                $event['successors'] = $details['successors'] ?? [];
            }
            unset($event);
            $scans = $this->query("SELECT id,result,completed_at FROM translation_workbench_operations
                WHERE kind='complete_rescan' AND (result @> ?::jsonb OR result @> ?::jsonb OR result @> ?::jsonb OR result @> ?::jsonb)
                ORDER BY completed_at DESC,id DESC LIMIT 50", [
                json_encode(['created' => [$finding]], JSON_THROW_ON_ERROR),
                json_encode(['removed' => [$finding]], JSON_THROW_ON_ERROR),
                json_encode(['moved' => [['predecessor' => $finding]]], JSON_THROW_ON_ERROR),
                json_encode(['moved' => [['successor' => $finding]]], JSON_THROW_ON_ERROR),
            ])->fetchAll(PDO::FETCH_ASSOC);
            foreach ($scans as $scan) {
                $result = json_decode($scan['result'], true, flags: JSON_THROW_ON_ERROR);
                $pairs = array_values(array_filter($result['moved'] ?? [], fn ($pair) => (int) $pair['predecessor'] === $finding || (int) $pair['successor'] === $finding));
                $type = $pairs !== [] ? 'scan_finding_moved' : (in_array($finding, $result['created'] ?? [], true) ? 'scan_finding_created' : 'scan_finding_removed');
                $history[] = ['id' => $scan['id'], 'event_type' => $type, 'actor_id' => null,
                    'occurred_at' => $scan['completed_at'], 'successors' => $pairs,
                    'reason' => match ($type) {
                        'scan_finding_moved' => 'Position changed in the same source file; predecessor and successor are linked.',
                        'scan_finding_created' => 'Source occurrence captured by the scanner.',
                        default => 'Source occurrence absent from the completed scan scope; finding archived.',
                    }];
            }
            usort($history, fn ($left, $right) => strcmp($right['occurred_at'], $left['occurred_at']) ?: strcmp((string) $right['id'], (string) $left['id']));
            $history = array_slice($history, 0, 50);
            $publication = $this->query('SELECT p.id,p.state FROM translation_workbench_publication_runs p JOIN translation_workbench_operations o ON o.id=p.id WHERE p.plan::jsonb @> ?::jsonb ORDER BY o.completed_at DESC LIMIT 1', [json_encode(['contexts' => [['context' => ['key_id' => $row['key_id'], 'locale' => $locale]]]], JSON_THROW_ON_ERROR)])->fetch(PDO::FETCH_ASSOC) ?: null;
            $review = null;
            if ($revision) {
                $review = RevisionReviewContext::snapshot($this->db, (int) $revision['id']);
                $review['latest'] = $this->query('SELECT decision,actor_id,reason,occurred_at FROM translation_workbench_reviews WHERE id=?', [$review['latest_decision']])->fetch(PDO::FETCH_ASSOC) ?: null;
                $review['approved'] = (new BaselineRevisionReviewer($this->db))->approvalIsCurrent((int) $revision['id']);
            }
            $this->db->commit();

            return ['finding' => $row, 'sourceText' => $sourceText, 'sourceRevision' => $sourceRevision['id'] ?? null, 'revision' => $revision, 'published' => $published, 'usages' => $usages, 'usageCount' => $usageCount, 'successor' => $successor ?: null, 'history' => $history, 'review' => $review, 'publication' => $publication];
        } catch (\Throwable $error) {
            $this->db->rollBack();
            throw $error;
        }
    }

    public function sourcePreview(int $finding, int $key, int $link, string $sourceLocale): array
    {
        $context = $this->inspect($finding, $sourceLocale);
        if ((int) $context['finding']['key_id'] !== $key || (int) $context['finding']['link_id'] !== $link) {
            throw new RuntimeException('The key assignment changed. Reopen the finding before previewing its source.');
        }

        $preview = (new SourceReplacementPreview)->inspect($context, $sourceLocale, $this->sourceRoot ?? base_path(), $this->languageRoot ?? lang_path());
        $current = $this->inspect($finding, $sourceLocale);
        if ($current['finding'] !== $context['finding'] || ($current['review']['fingerprint'] ?? null) !== ($context['review']['fingerprint'] ?? null)) {
            throw new RuntimeException('Finding or approval changed during preview. Reopen the finding.');
        }

        return $preview;
    }

    public function review(int $revision, string $fingerprint, bool $approve, int $actor, string $reason, ?string $reasonLocale = null): array
    {
        return (new BaselineRevisionReviewer($this->db))->decide((string) Str::uuid(), $revision, $fingerprint, $approve, $actor, $reason, $reasonLocale);
    }

    public function reviewMany(array $revisions, bool $approve, int $actor, string $reason, ?string $reasonLocale = null): array
    {
        return (new BaselineRevisionReviewer($this->db))->decideMany((string) Str::uuid(), $revisions, $approve, $actor, $reason, $reasonLocale);
    }

    private function activeLink(int $finding, ?int $expected): ?array
    {
        $status = $this->query('SELECT status FROM translation_workbench_findings WHERE id=? FOR UPDATE', [$finding])->fetchColumn();
        if ($status !== 'active') {
            throw new RuntimeException('This finding is no longer active. Reload its current source occurrence.');
        }
        $link = $this->query('SELECT id,key_id FROM translation_workbench_key_findings WHERE finding_id=? AND valid_to IS NULL', [$finding])->fetch(PDO::FETCH_ASSOC) ?: null;
        if (($link ? (int) $link['id'] : null) !== $expected) {
            throw new RuntimeException('The key assignment changed. Reopen the finding before saving.');
        }

        return $link;
    }

    /** Unknown or conflicting historical language evidence is never guessed. */
    public function reasonLocales(string $kind): array
    {
        if ($kind === 'publication') {
            $rows = $this->query("SELECT plan::jsonb->>'reason' AS reason, CASE
                WHEN count(*) FILTER (WHERE plan::jsonb->>'reasonLocale' IS NULL) = 0
                    AND count(DISTINCT plan::jsonb->>'reasonLocale') = 1
                THEN min(plan::jsonb->>'reasonLocale') ELSE NULL END AS locale
                FROM translation_workbench_publication_runs GROUP BY plan::jsonb->>'reason'")->fetchAll(PDO::FETCH_ASSOC);
            return array_column($rows, 'locale', 'reason');
        }
        $types = match ($kind) {
            'change' => ['value_changed'],
            'review' => ['revision_approved', 'revision_approval_revoked'],
            'assignment' => ['key_assigned', 'key_created_and_assigned'],
            default => throw new RuntimeException('Unknown reason category.'),
        };
        $marks = implode(',', array_fill(0, count($types), '?'));
        $rows = $this->query("SELECT reason, CASE
            WHEN count(*) FILTER (WHERE details->>'reason_locale' IS NULL) = 0
                AND count(DISTINCT details->>'reason_locale') = 1
            THEN min(details->>'reason_locale') ELSE NULL END AS locale
            FROM translation_workbench_events WHERE event_type IN ($marks)
            GROUP BY reason", $types)->fetchAll(PDO::FETCH_ASSOC);

        return array_column($rows, 'locale', 'reason');
    }

    private function validateReasonLocale(?string $locale): void
    {
        if ($locale !== null && !preg_match('/^[a-z]{2,3}(?:[-_][a-zA-Z0-9]{2,8})*$/D', $locale)) {
            throw new RuntimeException('Invalid reason language.');
        }
    }

    public function changeReasons(): array
    {
        return $this->query("SELECT reason FROM translation_workbench_events WHERE event_type='value_changed'
            AND length(trim(reason)) BETWEEN 3 AND 2000 GROUP BY reason ORDER BY max(id) DESC LIMIT 100")->fetchAll(PDO::FETCH_COLUMN);
    }

    public function workflowReasons(string $kind): array
    {
        if ($kind === 'source') {
            return $this->query("SELECT result->>'reason' AS reason FROM translation_workbench_operations
                WHERE kind='source_replacement_prepared' AND length(trim(result->>'reason')) BETWEEN 3 AND 2000
                GROUP BY result->>'reason' ORDER BY max(completed_at) DESC LIMIT 100")->fetchAll(PDO::FETCH_COLUMN);
        }
        if ($kind === 'publication') {
            return $this->query("SELECT plan::jsonb->>'reason' AS reason FROM translation_workbench_publication_runs p
                JOIN translation_workbench_operations o ON o.id=p.id
                WHERE length(trim(plan::jsonb->>'reason')) BETWEEN 3 AND 2000
                GROUP BY plan::jsonb->>'reason' ORDER BY max(o.completed_at) DESC LIMIT 100")->fetchAll(PDO::FETCH_COLUMN);
        }
        $types = match ($kind) {
            'review' => ['revision_approved', 'revision_approval_revoked'],
            'closure' => ['publication_closed_without_writing'],
            'source_closure' => ['source_replacement_closed'],
            default => throw new RuntimeException('Unknown reason category.'),
        };
        $marks = implode(',', array_fill(0, count($types), '?'));

        return $this->query("SELECT reason FROM translation_workbench_events WHERE event_type IN ($marks)
            AND length(trim(reason)) BETWEEN 3 AND 2000 GROUP BY reason ORDER BY max(id) DESC LIMIT 100", $types)->fetchAll(PDO::FETCH_COLUMN);
    }

    public function assignmentReasons(): array
    {
        return $this->query(
            "SELECT reason FROM translation_workbench_events WHERE event_type IN ('key_assigned', 'key_created_and_assigned') AND length(trim(reason)) BETWEEN 3 AND 2000 GROUP BY reason ORDER BY max(id) DESC LIMIT 100"
        )->fetchAll(\PDO::FETCH_COLUMN);
    }

    public function assign(int $finding, int $key, ?int $expectedLink, int $actor, string $reason, ?string $newName = null, ?string $reasonLocale = null): array
    {
        $this->validateReasonLocale($reasonLocale);
        if ($actor < 1 || trim($reason) === '') {
            throw new RuntimeException('An actor and a reason are required.');
        }
        if ($newName !== null && ! preg_match(TranslationKeyInput::PATTERN, $newName)) {
            throw new RuntimeException('Use a group and at least two key segments, for example ui.button.save.');
        }
        if ($newName !== null) {
            TranslationKeySegments::configured()->assertAllowed($newName);
        } else {
            $name = $this->query('SELECT namespace,full_name FROM translation_workbench_key_names WHERE key_id=? AND valid_to IS NULL', [$key])->fetch(PDO::FETCH_ASSOC);
            TranslationKeySegments::configured()->assertAllowed(($name['namespace'] ?? '') === '' ? ($name['full_name'] ?? '') : '');
        }
        $operation = (string) Str::uuid();

        return (new BaselineOperation($this->db))->run($operation, 'key_assignment', [$finding, $key, $expectedLink, $actor, $reason, $newName, $reasonLocale], function () use ($operation, $finding, $key, $expectedLink, $actor, $reason, $newName, $reasonLocale) {
            $link = $this->activeLink($finding, $expectedLink);
            if ($newName !== null) {
                $this->query('SELECT pg_advisory_xact_lock(hashtextextended(?,0))', ['key-name:default-namespace']);
                if ($this->query("SELECT 1 FROM translation_workbench_key_names WHERE namespace='' AND full_name=? AND valid_to IS NULL", [$newName])->fetchColumn()) {
                    throw new RuntimeException('This key already exists. Select the existing key explicitly.');
                }
                if ($this->query("SELECT 1 FROM translation_workbench_key_names WHERE namespace='' AND valid_to IS NULL AND (left(full_name,length(?)+1)=?||'.' OR left(?,length(full_name)+1)=full_name||'.')", [$newName, $newName, $newName])->fetchColumn()) {
                    throw new RuntimeException('The key conflicts with an existing scalar or parent key. Choose a different key path.');
                }
                $key = (int) $this->query('INSERT INTO translation_workbench_keys DEFAULT VALUES RETURNING id')->fetchColumn();
                $this->query('INSERT INTO translation_workbench_key_names(key_id,full_name) VALUES (?,?)', [$key, $newName]);
            }
            $lock = array_unique([$key, ...($link ? [(int) $link['key_id']] : [])]);
            sort($lock, SORT_NUMERIC);
            foreach ($lock as $id) {
                if (! $this->query('SELECT id FROM translation_workbench_keys WHERE id=? FOR UPDATE', [$id])->fetchColumn()) {
                    throw new RuntimeException('Unknown key.');
                }
            }
            return $this->writeKeyAssignment($finding, $key, $link, $operation, $actor, $reason, $newName !== null, reasonLocale: $reasonLocale);
        });
    }

    /** Build a reviewable assignment plan; a text match never assigns a key automatically. */
    public function previewSharedAssignment(int $reference, array $ids): array
    {
        if ($ids === [] || count($ids) > 100) {
            throw new RuntimeException('Select between 1 and 100 matching findings.');
        }
        foreach ($ids as $id) {
            if (! ctype_digit((string) $id) || (int) $id < 1 || strlen((string) $id) > 18) {
                throw new RuntimeException('Invalid finding selection.');
            }
        }
        $ids = array_values(array_unique(array_map('intval', $ids)));
        sort($ids, SORT_NUMERIC);
        $fields = 'SELECT f.id,f.status,f.suggested_key,s.path,f.source_line,f.source_column,
            COALESCE(f.raw_literal,f.suggested_literal) AS literal,l.id AS link_id,l.key_id,n.namespace,n.full_name';
        $source = $this->query($fields.self::FINDINGS.' WHERE f.id=?', [$reference])->fetch(PDO::FETCH_ASSOC);
        if (! $source || $source['status'] !== 'active' || ! $source['key_id'] || ! $source['full_name']) {
            throw new RuntimeException('Assign a key to the active reference finding first.');
        }
        TranslationKeySegments::configured()->assertAllowed($source['namespace'] === '' ? $source['full_name'] : '');
        $marks = implode(',', array_fill(0, count($ids), '?'));
        $rows = $this->query($fields.self::FINDINGS." WHERE f.id IN ($marks) ORDER BY f.id", $ids)->fetchAll(PDO::FETCH_ASSOC);
        if (count($rows) !== count($ids)) {
            throw new RuntimeException('A selected finding no longer exists.');
        }
        foreach ($rows as $row) {
            $sameKey = (int) $row['key_id'] === (int) $source['key_id'];
            $sameLiteral = trim((string) $source['literal']) !== '' && $row['literal'] === $source['literal'];
            if ($row['status'] !== 'active' || (!$sameKey && !$sameLiteral)) {
                throw new RuntimeException('Select active findings with matching captured text or the same assigned key.');
            }
        }

        return ['reference' => $source, 'findings' => $rows];
    }

    /** All links are changed together, or none are changed if the reviewed state is stale. */
    public function assignShared(array $plan, int $actor, string $reason, ?string $reasonLocale = null): array
    {
        $this->validateReasonLocale($reasonLocale);
        if ($actor < 1 || strlen(trim($reason)) < 3 || strlen($reason) > 2000) {
            throw new RuntimeException('An actor and a reason are required.');
        }
        $reference = (int) ($plan['reference']['id'] ?? 0);
        $ids = array_column($plan['findings'] ?? [], 'id');
        if (! $reference || $ids === []) {
            throw new RuntimeException('Review the selected findings before confirming.');
        }
        $operation = (string) Str::uuid();

        return (new BaselineOperation($this->db))->run($operation, 'shared_key_assignment',
            [$plan, $actor, $reason, $reasonLocale], function () use ($plan, $reference, $ids, $actor, $reason, $operation, $reasonLocale) {
                $snapshots = [$plan['reference'], ...$plan['findings']];
                usort($snapshots, fn ($a, $b) => $a['id'] <=> $b['id']);
                foreach ($snapshots as $row) {
                    $this->activeLink((int) $row['id'], isset($row['link_id']) ? (int) $row['link_id'] : null);
                }
                $keys = array_values(array_unique(array_filter(array_column($snapshots, 'key_id'))));
                sort($keys, SORT_NUMERIC);
                foreach ($keys as $key) {
                    $this->query('SELECT id FROM translation_workbench_keys WHERE id=? FOR UPDATE', [$key]);
                }
                if ($this->previewSharedAssignment($reference, $ids) !== $plan) {
                    throw new RuntimeException('The reviewed findings or key changed. Review the selection again.');
                }
                $target = (int) $plan['reference']['key_id'];
                $links = [];
                foreach ($plan['findings'] as $row) {
                    $links[] = $this->writeKeyAssignment((int) $row['id'], $target,
                        $row['key_id'] ? ['key_id' => $row['key_id'], 'id' => $row['link_id']] : null,
                        $operation, $actor, $reason, eventIndex: count($links), reasonLocale: $reasonLocale);
                }

                return ['key' => $target, 'links' => $links];
            });
    }

    private function writeKeyAssignment(int $finding, int $key, ?array $link, string $operation, int $actor, string $reason, bool $created = false, int $eventIndex = 0, ?string $reasonLocale = null): array
    {
        if ($link && (int) $link['key_id'] === $key) {
            return ['key' => $key, 'link' => (int) $link['id']];
        }
        $this->query('UPDATE translation_workbench_key_findings SET valid_to=now() WHERE finding_id=? AND valid_to IS NULL', [$finding]);
        $id = (int) $this->query('INSERT INTO translation_workbench_key_findings(finding_id,key_id) VALUES (?,?) RETURNING id', [$finding, $key])->fetchColumn();
        $this->query('INSERT INTO translation_workbench_events(operation_id,event_index,event_type,actor_id,reason,details) VALUES (?,?,?,?,?,?)', [$operation, $eventIndex, $created ? 'key_created_and_assigned' : 'key_assigned', $actor, $reason, json_encode(['finding' => $finding, 'key' => $key, 'previous_key' => $link['key_id'] ?? null, 'link' => $id, 'reason_locale' => $reasonLocale], JSON_THROW_ON_ERROR)]);

        return ['key' => $key, 'link' => $id];
    }

    public function publishedWorklist(): PublishedWorklist
    {
        return new PublishedWorklist($this->db);
    }

    public function reviewWorklist(): ReviewWorklist
    {
        return new ReviewWorklist($this->db);
    }

    /** Save all edited locales atomically with their originally loaded revision IDs. */
    public function saveDrafts(int $finding, int $key, ?int $link, array $drafts, int $actor, string $reason, ?string $reasonLocale = null): array
    {
        $this->validateReasonLocale($reasonLocale);
        if ($actor < 1 || trim($reason) === '' || $drafts === []) {
            throw new RuntimeException('An actor, drafts and reason are required.');
        }
        foreach ($drafts as $locale => $draft) {
            if (! preg_match('/^[a-zA-Z0-9_-]+$/D', (string) $locale)
                || ! is_array($draft) || ! is_string($draft['value'] ?? null)
                || ! array_key_exists('revision', $draft)
                || ($draft['revision'] !== null && ! is_int($draft['revision']))) {
                throw new RuntimeException('Invalid translation draft.');
            }
        }
        $operation = (string) Str::uuid();
        return (new BaselineOperation($this->db))->run($operation, 'save_translation_drafts',
            [$finding, $key, $link, $drafts, $actor, $reason, $reasonLocale], function () use ($operation, $finding, $key, $link, $drafts, $actor, $reason, $reasonLocale) {
                $current = $this->activeLink($finding, $link);
                if (! $current || (int) $current['key_id'] !== $key) {
                    throw new RuntimeException('The finding is not assigned to this key.');
                }
                $result = [];
                $writer = new BaselineDraftWriter($this->db);
                $eventIndex = 0;
                foreach ($drafts as $locale => $draft) {
                    $result[$locale] = $writer->save($operation, $key, $locale, $draft['value'], $draft['revision'], $actor, $reason, null, $eventIndex++, $reasonLocale);
                }
                return $result;
            });
    }

    public function save(int $finding, int $key, ?int $link, string $locale, string $value, ?int $revision, int $actor, string $reason): array
    {
        if ($actor < 1 || trim($reason) === '' || ! preg_match('/^[a-zA-Z0-9_-]+$/D', $locale)) {
            throw new RuntimeException('An actor, locale and reason are required.');
        }
        $operation = (string) Str::uuid();

        return (new BaselineOperation($this->db))->run($operation, 'save_translation_draft', [$finding, $key, $link, $locale, $value, $revision, $actor, $reason], function () use ($operation, $finding, $key, $link, $locale, $value, $revision, $actor, $reason) {
            $current = $this->activeLink($finding, $link);
            if (! $current || (int) $current['key_id'] !== $key) {
                throw new RuntimeException('The finding is not assigned to this key.');
            }

            return (new BaselineDraftWriter($this->db))->save($operation, $key, $locale, $value, $revision, $actor, $reason);
        });
    }
}
