<?php

namespace Tests\Support\TranslationWorkbench;

use Gunreip\TranslationWorkbench\Generation\BaselineValueReader;
use Gunreip\TranslationWorkbench\Scanner\DiscoveredTranslation;
use PDO;
use RuntimeException;
use Throwable;

/** Experimental contracts only. Own in-memory DB; never registered with the application. */
final class BaselinePrototype
{
    public readonly PDO $db;

    public function __construct()
    {
        $this->db = new PDO('sqlite::memory:', options: [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        $this->db->exec('PRAGMA foreign_keys = ON');
        $this->db->exec(<<<'SQL'
            CREATE TABLE baseline_observations (id INTEGER PRIMARY KEY, operation_id TEXT NOT NULL, key_name TEXT NOT NULL, locale TEXT NOT NULL, value TEXT NOT NULL, source TEXT NOT NULL, reference TEXT NOT NULL);
            CREATE TABLE operations (id TEXT PRIMARY KEY, fingerprint TEXT NOT NULL, result TEXT NOT NULL);
            CREATE TABLE runs (id TEXT PRIMARY KEY, status TEXT NOT NULL, files TEXT NOT NULL, observed_at TEXT NOT NULL);
            CREATE TABLE findings (
                id INTEGER PRIMARY KEY, path TEXT NOT NULL, line INTEGER, col INTEGER, kind TEXT NOT NULL,
                raw_expression TEXT, raw_source_code_literal TEXT, raw_literal TEXT, suggested_literal TEXT,
                provenance TEXT, suggested_key TEXT, capture_run TEXT NOT NULL REFERENCES runs(id),
                predecessor_id INTEGER UNIQUE REFERENCES findings(id), status TEXT NOT NULL CHECK(status IN ('active','obsolete'))
            );
            CREATE UNIQUE INDEX current_position ON findings(path,line,col) WHERE status='active';
            CREATE TRIGGER immutable_capture BEFORE UPDATE OF path,line,col,kind,raw_expression,
                raw_source_code_literal,raw_literal,suggested_literal,provenance,suggested_key,capture_run,predecessor_id ON findings
                BEGIN SELECT RAISE(ABORT,'Immutable capture'); END;
            CREATE TABLE observations (run_id TEXT REFERENCES runs(id), finding_id INTEGER REFERENCES findings(id), PRIMARY KEY(run_id,finding_id));
            CREATE TABLE keys (id INTEGER PRIMARY KEY, name TEXT NOT NULL UNIQUE);
            CREATE TABLE links (id INTEGER PRIMARY KEY, finding_id INTEGER REFERENCES findings(id), key_id INTEGER REFERENCES keys(id), active INTEGER NOT NULL);
            CREATE UNIQUE INDEX current_assignment ON links(finding_id) WHERE active=1;
            CREATE TABLE revisions (id INTEGER PRIMARY KEY, key_id INTEGER REFERENCES keys(id), locale TEXT NOT NULL,
                revision INTEGER NOT NULL, value TEXT NOT NULL, actor INTEGER NOT NULL, reason TEXT NOT NULL,
                occurred_at TEXT NOT NULL, UNIQUE(key_id,locale,revision));
            CREATE TRIGGER immutable_revision BEFORE UPDATE ON revisions BEGIN SELECT RAISE(ABORT,'Immutable revision'); END;
            CREATE TABLE approvals (id INTEGER PRIMARY KEY, revision_id INTEGER NOT NULL REFERENCES revisions(id),
                decision TEXT NOT NULL CHECK(decision IN ('approved','revoked')), fingerprint TEXT NOT NULL,
                actor INTEGER NOT NULL, reason TEXT NOT NULL, occurred_at TEXT NOT NULL);
            CREATE TRIGGER immutable_approval BEFORE UPDATE ON approvals BEGIN SELECT RAISE(ABORT,'Immutable approval'); END;
            CREATE TABLE events (id INTEGER PRIMARY KEY, operation_id TEXT NOT NULL, event_type TEXT NOT NULL,
                actor_type TEXT NOT NULL, actor_id INTEGER, occurred_at TEXT NOT NULL, reason TEXT NOT NULL,
                subject TEXT NOT NULL, details TEXT NOT NULL);
            SQL);
    }

    public function rows(string $table): array
    {
        if (! in_array($table, ['findings', 'runs', 'observations', 'keys', 'links', 'revisions', 'events', 'operations', 'baseline_observations', 'approvals'], true)) {
            throw new RuntimeException('Unknown prototype table');
        }

        return $this->query('SELECT * FROM '.$table)->fetchAll(PDO::FETCH_ASSOC);
    }

    /** $files is the fully enumerated scan scope: path => content hash (including empty files).
     * $translations is verified source-locale evidence: key => [['value'=>string,'locale'=>...,'source'=>...,'reference'=>...]].
     * This prototype deliberately has no lang-file evaluator or production database adapter.
     */
    public function scan(string $operation, array $items, array $files, array $translations = [], bool $complete = true, string $sourceLocale = 'en'): array
    {
        return $this->operation($operation, ['scan', $items, $files, $translations, $complete, $sourceLocale], function () use ($operation, $items, $files, $translations, $complete, $sourceLocale) {
            $this->query('INSERT INTO runs VALUES (?,?,?,?)', [$operation, $complete ? 'completed' : 'failed', json_encode($files), gmdate('c')]);
            if (! $complete) {
                return ['status' => 'failed']; // No absence inference from an incomplete scan.
            }
            foreach ($items as $item) {
                if (! $item instanceof DiscoveredTranslation || ! array_key_exists($item->sourcePath, $files)
                    || $item->sourceLine === null || $item->sourceColumn === null) {
                    throw new RuntimeException('Complete path and position evidence required');
                }
            }
            $old = array_values(array_filter($this->rows('findings'), fn ($row) => $row['status'] === 'active' && array_key_exists($row['path'], $files)));
            $activeLinks = [];
            foreach ($this->rows('links') as $link) {
                if ($link['active']) {
                    $activeLinks[$link['finding_id']] = $link['key_id'];
                }
            }
            $matches = [];
            $used = [];
            // Match identical positions first; only unmatched occurrences participate in move detection.
            foreach ($items as $index => $item) {
                foreach ($old as $row) {
                    if (! isset($used[$row['id']]) && $this->sameExpression($row, $item)
                        && $row['line'] === $item->sourceLine && $row['col'] === $item->sourceColumn) {
                        $matches[$index] = $row;
                        $used[$row['id']] = true;
                        break;
                    }
                }
            }
            $moves = [];
            foreach ($items as $index => $item) {
                if (isset($matches[$index])) {
                    continue;
                }
                $candidates = array_values(array_filter($old, fn ($row) => ! isset($used[$row['id']]) && $this->sameExpression($row, $item)));
                $peers = array_filter($items, fn ($other, $i) => ! isset($matches[$i]) && $other->sourcePath === $item->sourcePath
                    && $other->kind === $item->kind && $other->rawExpression === $item->rawExpression, ARRAY_FILTER_USE_BOTH);
                if (count($candidates) === 1 && count($peers) === 1) {
                    $moves[$index] = $candidates[0];
                } elseif ($candidates !== []) {
                    throw new RuntimeException('Ambiguous relocation; no automatic choice');
                }
            }
            // Retire before insertion so a different expression may reuse an old position.
            foreach ($old as $row) {
                if (! isset($used[$row['id']])) {
                    $this->query("UPDATE findings SET status='obsolete' WHERE id=?", [$row['id']]);
                    $this->query('UPDATE links SET active=0 WHERE finding_id=?', [$row['id']]);
                    if (! in_array($row['id'], array_column($moves, 'id'), true)) {
                        $this->event($operation, 'finding_removed', 'scanner', null, 'absent_in_complete_scan', 'finding:'.$row['id'], []);
                    }
                }
            }
            foreach ($items as $index => $item) {
                if (isset($matches[$index])) {
                    $id = $matches[$index]['id'];
                } else {
                    $previous = $moves[$index] ?? null;
                    [$suggestion, $provenance] = $this->suggestion($item, $translations, $sourceLocale);
                    $this->query('INSERT INTO findings(path,line,col,kind,raw_expression,raw_source_code_literal,raw_literal,suggested_literal,provenance,suggested_key,capture_run,predecessor_id,status) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)', [
                        $item->sourcePath, $item->sourceLine, $item->sourceColumn, $item->kind, $item->rawExpression,
                        $item->kind === 'literal' ? $item->literalText : $item->translationKey,
                        $item->kind === 'literal' ? $item->literalText : null,
                        $previous ? $previous['suggested_literal'] : $suggestion,
                        $previous ? $previous['provenance'] : $provenance,
                        $previous ? $previous['suggested_key'] : $item->suggestedKey,
                        $operation, $previous['id'] ?? null, 'active',
                    ]);
                    $id = (int) $this->db->lastInsertId();
                    if ($previous) {
                        $link = $activeLinks[$previous['id']] ?? null;
                        if ($link !== null) {
                            $this->link($id, (int) $link);
                        }
                    } elseif ($item->kind === 'key' && $item->translationKey !== null) {
                        $this->link($id, $this->key($item->translationKey));
                    }
                    $this->event($operation, $previous ? 'finding_moved' : 'finding_captured', 'scanner', null,
                        $previous ? 'unique_same_file_relocation' : 'observed_in_source', 'finding:'.$id,
                        ['predecessor_id' => $previous['id'] ?? null, 'path' => $item->sourcePath, 'line' => $item->sourceLine]);
                }
                $this->query('INSERT INTO observations VALUES (?,?)', [$operation, $id]);
            }

            return ['status' => 'completed', 'observed' => count($items)];
        });
    }

    public function assign(string $operation, int $finding, string $name, int $actor, string $reason): array
    {
        return $this->operation($operation, ['assign', $finding, $name, $actor, $reason], function () use ($operation, $finding, $name, $actor, $reason) {
            $this->validateActor($actor, $reason);
            if (trim($name) === '' || ! $this->query("SELECT id FROM findings WHERE id=? AND status='active'", [$finding])->fetchColumn()) {
                throw new RuntimeException('Active finding and key name required');
            }
            $key = $this->key($name);
            $old = $this->query('SELECT key_id FROM links WHERE finding_id=? AND active=1', [$finding])->fetchColumn();
            if ($old !== false && (int) $old === $key) {
                return ['key_id' => $key, 'changed' => false];
            }
            $this->query('UPDATE links SET active=0 WHERE finding_id=?', [$finding]);
            $this->link($finding, $key);
            $this->event($operation, 'key_assigned', 'user', $actor, $reason, 'finding:'.$finding, ['previous_key_id' => $old === false ? null : (int) $old, 'key_id' => $key]);

            return ['key_id' => $key, 'changed' => true];
        });
    }

    public function save(string $operation, int $key, string $locale, string $value, int $actor, string $reason): array
    {
        return $this->operation($operation, ['save', $key, $locale, $value, $actor, $reason], function () use ($operation, $key, $locale, $value, $actor, $reason) {
            $this->validateActor($actor, $reason);
            if (trim($locale) === '') {
                throw new RuntimeException('Locale required');
            }
            $old = $this->query('SELECT * FROM revisions WHERE key_id=? AND locale=? ORDER BY revision DESC LIMIT 1', [$key, $locale])->fetch(PDO::FETCH_ASSOC);
            if ($old && $old['value'] === $value) {
                return ['revision_id' => $old['id'], 'changed' => false];
            }
            $this->query('INSERT INTO revisions(key_id,locale,revision,value,actor,reason,occurred_at) VALUES (?,?,?,?,?,?,?)', [$key, $locale, $old ? $old['revision'] + 1 : 1, $value, $actor, $reason, gmdate('c')]);
            $id = (int) $this->db->lastInsertId();
            $this->event($operation, 'translation_saved', 'user', $actor, $reason, 'key:'.$key, ['locale' => $locale, 'previous_revision_id' => $old ? $old['id'] : null, 'revision_id' => $id]);

            return ['revision_id' => $id, 'changed' => true];
        });
    }

    /** Keep every candidate; conflicts never become an arbitrary working value. */
    public function captureValues(string $operation, array $rows, int $actor, string $reason): array
    {
        return $this->operation($operation, ['baseline', $rows, $actor, $reason], function () use ($operation, $rows, $actor, $reason) {
            $this->validateActor($actor, $reason);
            if ($this->rows('baseline_observations') !== [] || $this->rows('revisions') !== []) {
                throw new RuntimeException('Baseline capture requires an empty value baseline');
            }
            $plan = (new BaselineValueReader)->plan($rows);
            foreach ($plan as $group) {
                foreach ($group['candidates'] as $candidate) {
                    $this->query('INSERT INTO baseline_observations(operation_id,key_name,locale,value,source,reference) VALUES (?,?,?,?,?,?)', [$operation, $group['key'], $group['locale'], $candidate['value'], $candidate['source'], $candidate['reference']]);
                }
                if ($group['state'] === 'consistent') {
                    $key = $this->key($group['key']);
                    $this->query('INSERT INTO revisions(key_id,locale,revision,value,actor,reason,occurred_at) VALUES (?,?,1,?,?,?,?)', [$key, $group['locale'], $group['value'], $actor, $reason, gmdate('c')]);
                }
            }
            if ($rows !== []) {
                $this->event($operation, 'baseline_values_captured', 'user', $actor, $reason, 'baseline:'.$operation,
                    ['candidates' => count($rows), 'conflicts' => count(array_filter($plan, fn ($group) => $group['state'] === 'conflict'))]);
            }

            return $plan;
        });
    }

    /** Read-only snapshot: approval always refers to one revision and its current usage. */
    public function reviewSnapshot(int $revision): array
    {
        $row = $this->query('SELECT r.*, k.name AS key_name FROM revisions r JOIN keys k ON k.id=r.key_id WHERE r.id=?', [$revision])->fetch(PDO::FETCH_ASSOC);
        if (! $row) {
            throw new RuntimeException('Unknown value revision');
        }
        $links = $this->query("SELECT l.id, l.finding_id FROM links l JOIN findings f ON f.id=l.finding_id WHERE l.key_id=? AND l.active=1 AND f.status='active' ORDER BY l.id", [$row['key_id']])->fetchAll(PDO::FETCH_ASSOC);
        $snapshot = ['revision_id' => $row['id'], 'key_id' => $row['key_id'], 'key_name' => $row['key_name'],
            'locale' => $row['locale'], 'revision' => $row['revision'], 'value' => $row['value'], 'usage' => $links];

        return ['snapshot' => $snapshot, 'fingerprint' => hash('sha256', json_encode($snapshot, JSON_THROW_ON_ERROR))];
    }

    public function decideRevision(string $operation, int $revision, string $expectedFingerprint, bool $approved, int $actor, string $reason): array
    {
        return $this->operation($operation, ['approval', $revision, $expectedFingerprint, $approved, $actor, $reason], function () use ($operation, $revision, $expectedFingerprint, $approved, $actor, $reason) {
            $this->validateActor($actor, $reason);
            $preview = $this->reviewSnapshot($revision);
            if (! hash_equals($preview['fingerprint'], $expectedFingerprint)) {
                throw new RuntimeException('Review context changed; inspect again');
            }
            if ($approved) {
                $this->assertCurrentRevision($preview['snapshot']);
                if ($preview['snapshot']['usage'] === []) {
                    throw new RuntimeException('No current finding uses this key');
                }
            }
            $decision = $approved ? 'approved' : 'revoked';
            $latest = $this->latestApproval($revision);
            if ($latest && $latest['decision'] === $decision && $latest['fingerprint'] === $expectedFingerprint) {
                return ['approval_id' => $latest['id'], 'changed' => false];
            }
            if (! $approved && (! $latest || $latest['decision'] !== 'approved')) {
                throw new RuntimeException('No approval to revoke');
            }
            $this->query('INSERT INTO approvals(revision_id,decision,fingerprint,actor,reason,occurred_at) VALUES (?,?,?,?,?,?)',
                [$revision, $decision, $expectedFingerprint, $actor, $reason, gmdate('c')]);
            $id = (int) $this->db->lastInsertId();
            $this->event($operation, $approved ? 'revision_approved' : 'revision_approval_revoked', 'user', $actor, $reason,
                'revision:'.$revision, ['approval_id' => $id, 'fingerprint' => $expectedFingerprint]);

            return ['approval_id' => $id, 'changed' => true];
        });
    }

    /** Eligibility manifest only. No file is read or written, and no publication is claimed. */
    public function publicationPreview(array $revisionIds): array
    {
        if ($revisionIds === [] || count(array_unique($revisionIds, SORT_REGULAR)) !== count($revisionIds)) {
            throw new RuntimeException('Choose distinct value revisions');
        }
        foreach ($revisionIds as $id) {
            if (! is_int($id) || $id < 1) {
                throw new RuntimeException('Revision IDs must be positive integers');
            }
        }
        sort($revisionIds, SORT_NUMERIC);
        $items = [];
        foreach ($revisionIds as $revision) {
            $preview = $this->reviewSnapshot($revision);
            $snapshot = $preview['snapshot'];
            $this->assertCurrentRevision($snapshot);
            $approval = $this->latestApproval($revision);
            if (! $approval || $approval['decision'] !== 'approved'
                || ! hash_equals($approval['fingerprint'], $preview['fingerprint']) || $snapshot['usage'] === []) {
                throw new RuntimeException('Current revision and context are not approved');
            }
            // Prototype supports nested scalar keys only; no silent key correction or fallback.
            if (! preg_match('/^[a-z0-9_-]+(?:\.[a-z0-9_-]+){2,}$/iD', $snapshot['key_name'])) {
                throw new RuntimeException('Nested scalar key required; correct it explicitly');
            }
            $items[] = [...$snapshot, 'approval_id' => $approval['id'], 'approval_fingerprint' => $preview['fingerprint']];
        }
        foreach ($items as $i => $item) {
            foreach ($items as $j => $other) {
                if ($i !== $j && $item['locale'] === $other['locale'] && str_starts_with($other['key_name'], $item['key_name'].'.')) {
                    throw new RuntimeException('Scalar and container keys conflict');
                }
            }
        }

        return ['items' => $items, 'fingerprint' => hash('sha256', json_encode($items, JSON_THROW_ON_ERROR))];
    }

    public function verifyPublicationPreview(array $revisionIds, string $expectedFingerprint): array
    {
        $preview = $this->publicationPreview($revisionIds);
        if (! hash_equals($preview['fingerprint'], $expectedFingerprint)) {
            throw new RuntimeException('Publication preview changed; inspect again');
        }

        return $preview;
    }

    /** Called only after the isolated publisher has verified every resulting file hash. */
    public function recordTestPublication(string $operation, array $revisionIds, string $manifestFingerprint, array $files, int $actor, string $reason): array
    {
        return $this->operation($operation, ['published', $revisionIds, $manifestFingerprint, $files, $actor, $reason], function () use ($operation, $revisionIds, $manifestFingerprint, $files, $actor, $reason) {
            $this->validateActor($actor, $reason);
            $manifest = $this->verifyPublicationPreview($revisionIds, $manifestFingerprint);
            $this->event($operation, 'revisions_published', 'user', $actor, $reason, 'publication:'.$operation,
                ['revisions' => $manifest['items'], 'files' => $files]);

            return ['operation' => $operation, 'files' => $files];
        });
    }

    public function recordTestClosure(string $run, array $review, int $actor, string $reason): array
    {
        $operation = 'publication-closure:'.$run;

        return $this->operation($operation, [$review, $actor, $reason], function () use ($operation, $run, $review, $actor, $reason) {
            $this->validateActor($actor, $reason);
            $receipt = ['review' => $review, 'actor' => $actor, 'reason' => $reason];
            $this->event($operation, 'publication_closed_without_writing', 'user', $actor, $reason, 'publication:'.$run, $receipt);

            return $receipt;
        });
    }

    private function assertCurrentRevision(array $snapshot): void
    {
        $current = $this->query('SELECT id FROM revisions WHERE key_id=? AND locale=? ORDER BY revision DESC LIMIT 1',
            [$snapshot['key_id'], $snapshot['locale']])->fetchColumn();
        if ((int) $current !== $snapshot['revision_id']) {
            throw new RuntimeException('A newer draft exists; inspect the current revision');
        }
    }

    private function latestApproval(int $revision): array|false
    {
        return $this->query('SELECT * FROM approvals WHERE revision_id=? ORDER BY id DESC LIMIT 1', [$revision])->fetch(PDO::FETCH_ASSOC);
    }

    private function suggestion(DiscoveredTranslation $item, array $translations, string $locale): array
    {
        if ($item->kind !== 'key') {
            return [null, null];
        }
        $candidates = array_values(array_filter($translations[$item->translationKey] ?? [], fn ($row) => ($row['locale'] ?? null) === $locale
            && is_string($row['value'] ?? null) && ! empty($row['source']) && ! empty($row['reference'])));
        if ($candidates === [] || count(array_unique(array_column($candidates, 'value'), SORT_STRING)) !== 1) {
            return [null, null];
        }

        return [$candidates[0]['value'], json_encode(['key' => $item->translationKey, 'locale' => $locale, 'evidence' => $candidates], JSON_THROW_ON_ERROR)];
    }

    private function sameExpression(array $row, DiscoveredTranslation $item): bool
    {
        return $row['path'] === $item->sourcePath && $row['kind'] === $item->kind && $row['raw_expression'] === $item->rawExpression;
    }

    private function key(string $name): int
    {
        $this->query('INSERT INTO keys(name) VALUES (?) ON CONFLICT(name) DO NOTHING', [$name]);

        return (int) $this->query('SELECT id FROM keys WHERE name=?', [$name])->fetchColumn();
    }

    private function link(int $finding, int $key): void
    {
        $this->query('INSERT INTO links(finding_id,key_id,active) VALUES (?,?,1)', [$finding, $key]);
    }

    private function validateActor(int $actor, string $reason): void
    {
        if ($actor < 1 || trim($reason) === '') {
            throw new RuntimeException('Actor and reason required');
        }
    }

    private function event(string $operation, string $type, string $actorType, ?int $actor, string $reason, string $subject, array $details): void
    {
        $this->query('INSERT INTO events(operation_id,event_type,actor_type,actor_id,occurred_at,reason,subject,details) VALUES (?,?,?,?,?,?,?,?)', [$operation, $type, $actorType, $actor, gmdate('c'), $reason, $subject, json_encode($details, JSON_THROW_ON_ERROR)]);
    }

    private function operation(string $id, array $payload, callable $action): array
    {
        if (trim($id) === '') {
            throw new RuntimeException('Operation ID required');
        }
        $hash = hash('sha256', json_encode($payload, JSON_THROW_ON_ERROR));
        $existing = $this->query('SELECT * FROM operations WHERE id=?', [$id])->fetch(PDO::FETCH_ASSOC);
        if ($existing) {
            if ($existing['fingerprint'] !== $hash) {
                throw new RuntimeException('Operation ID reused with different input');
            }

            return json_decode($existing['result'], true, flags: JSON_THROW_ON_ERROR);
        }
        $this->db->beginTransaction();
        try {
            $result = $action();
            $this->query('INSERT INTO operations VALUES (?,?,?)', [$id, $hash, json_encode($result, JSON_THROW_ON_ERROR)]);
            $this->db->commit();

            return $result;
        } catch (Throwable $error) {
            $this->db->rollBack();
            throw $error;
        }
    }

    private function query(string $sql, array $params = []): \PDOStatement
    {
        $statement = $this->db->prepare($sql);
        $statement->execute($params);

        return $statement;
    }
}
