<?php

namespace Gunreip\TranslationWorkbench\Generation;

use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchOption;
use PDO;
use RuntimeException;
use Throwable;

/** Journaled file publication; the application supplies the verified database and language root. */
class GenerationPublisher
{
    public readonly string $directory;

    public function __construct(private PDO $db, string $directory)
    {
        if (is_link($directory) || ! is_dir($directory) || ! ($resolved = realpath($directory))) {
            throw new RuntimeException('A real language directory is required');
        }
        $this->directory = $resolved;
    }

    public function prepare(string $run, array $revisions, int $actor, string $reason, ?array $expectedReviews = null, ?string $reasonLocale = null): array
    {
        $this->actor($actor, $reason);
        if ($reasonLocale !== null && !preg_match('/^[a-z]{2,3}(?:[-_][a-zA-Z0-9]{2,8})*$/D', $reasonLocale)) {
            throw new RuntimeException('Invalid reason language.');
        }
        if ($revisions === [] || count(array_unique($revisions)) !== count($revisions)) {
            throw new RuntimeException('Unique nonempty revision selection required');
        }

        return $this->locked($run, function () use ($run, $revisions, $actor, $reason, $expectedReviews, $reasonLocale) {
            return (new BaselineOperation($this->db))->run($run, 'publication_prepare', [$revisions, $actor, $reason, $expectedReviews, $reasonLocale], function () use ($run, $revisions, $actor, $reason, $expectedReviews, $reasonLocale) {
                $contexts = $this->contexts($revisions);
                if ($expectedReviews !== null && array_column($contexts, 'fingerprint') !== $expectedReviews) {
                    throw new RuntimeException('Review changed. Reopen the revision before preparing publication.');
                }
                $files = [];
                $targets = [];
                foreach ($contexts as $context) {
                    $c = $context['context'];
                    [$path, $nested] = $this->destination($c);
                    $target = $path.'/'.$nested;
                    if (isset($targets[$target])) {
                        throw new RuntimeException('Multiple revisions target the same file entry');
                    }
                    $targets[$target] = true;
                    if (! isset($files[$path])) {
                        $before = $this->bytes($path);
                        $files[$path] = ['before' => $before === null ? null : hash('sha256', $before),
                            'original' => $before, 'tree' => $before === null ? [] : (new BaselineValueReader)->treeFromBytes($before, allowOptionKeys: true)];
                    }
                    $this->put($files[$path]['tree'], explode('.', $nested), $c['value']);
                }
                ksort($files);
                foreach ($files as &$file) {
                    $bytes = (new TranslationFileEditor)->render($file['original'], $file['tree']);
                    if ($file['original'] !== null && (new BaselineValueReader)->treeFromBytes($file['original'], allowOptionKeys: true) === $file['tree']) {
                        $bytes = $file['original'];
                    }
                    $file = ['original' => $file['original'], 'before' => $file['before'], 'after' => hash('sha256', $bytes), 'bytes' => $bytes];
                }
                unset($file);
                if (! array_filter($files, fn ($f) => $f['before'] !== $f['after'])) {
                    throw new RuntimeException('No file changes');
                }
                $directory = $this->directory;
                $plan = json_encode(compact('directory', 'run', 'revisions', 'actor', 'reason', 'reasonLocale', 'contexts', 'files'), JSON_THROW_ON_ERROR);
                $hash = hash('sha256', $plan);
                $this->q("INSERT INTO translation_workbench_publication_runs VALUES (?, ?, ?, 'prepared', NULL)", [$run, $plan, $hash]);
                foreach ($files as $path => $file) {
                    $this->q('SELECT pg_advisory_xact_lock(hashtextextended(?,0))', ['publication-file:'.$this->directory.'/'.$path]);
                    if ($reserved = $this->q('SELECT run_id FROM translation_workbench_file_reservations WHERE path=?', [$path])->fetchColumn()) {
                        throw new RuntimeException('File reserved by publication '.$reserved.'. Open that run first.');
                    }
                    $this->q('INSERT INTO translation_workbench_publication_files VALUES (?,?,?,?)', [$run, $path, $file['before'], $file['after']]);
                    $this->q('INSERT INTO translation_workbench_file_reservations VALUES (?,?)', [$path, $run]);
                }

                return ['run' => $run, 'fingerprint' => $hash];
            });
        });
    }

    public function publish(string $run, string $expected, ?callable $fault = null): array
    {
        return $this->locked($run, function () use ($run, $expected, $fault) {
            $plan = $this->plan($run, $expected);
            if ($outcome = $this->outcome($run)) {
                if ($outcome['kind'] !== 'completed') {
                    throw new RuntimeException('Publication closed');
                }

                return $outcome; // Historical success never authorizes rewriting later files.
            }
            $this->q("UPDATE translation_workbench_publication_runs SET state='writing',error=NULL WHERE id=?", [$run]);
            $this->db->beginTransaction();
            try {
                $contexts = $this->contexts($plan['revisions']);
                if ($contexts !== $plan['contexts']) {
                    throw new RuntimeException('Publication approval context changed');
                }
                foreach ($plan['files'] as $path => $file) {
                    if ($this->q('SELECT run_id FROM translation_workbench_file_reservations WHERE path=?', [$path])->fetchColumn() !== $run) {
                        throw new RuntimeException('Missing reservation');
                    }
                    $this->expectedFile($path, $file);
                }
                foreach ($plan['files'] as $path => $file) {
                    if ($this->expectedFile($path, $file) !== $file['after']) {
                        $this->write($path, $file['bytes']);
                        if ($fault) {
                            $fault('after_write', $path);
                        }
                    }
                }
                $snapshot = $this->snapshot($plan);
                foreach ($snapshot as $path => $file) {
                    if ($file['current'] !== $plan['files'][$path]['after']) {
                        throw new RuntimeException('Final hash differs');
                    }
                }
                $this->finish($run, 'completed', $snapshot, $plan['actor'], $plan['reason'], $plan['reasonLocale'] ?? null);
                $this->db->commit();
            } catch (Throwable $error) {
                $this->db->rollBack();
                $this->q("UPDATE translation_workbench_publication_runs SET state='interrupted',error=? WHERE id=?", [$error->getMessage(), $run]);
                throw $error;
            }
            if ($fault) {
                $fault('after_receipt', null);
            }

            return $this->outcome($run);
        });
    }

    public function closurePreview(string $run): array
    {
        return $this->locked($run, function () use ($run) {
            if ($this->outcome($run)) {
                throw new RuntimeException('Already completed or closed');
            }
            $plan = $this->plan($run);
            $snapshot = $this->snapshot($plan);

            return ['snapshot' => $snapshot, 'fingerprint' => hash('sha256', json_encode([$plan, $snapshot], JSON_THROW_ON_ERROR))];
        });
    }

    public function close(string $run, string $expected, int $actor, string $reason): array
    {
        $this->actor($actor, $reason);

        return $this->locked($run, function () use ($run, $expected, $actor, $reason) {
            if ($outcome = $this->outcome($run)) {
                $saved = json_decode($outcome['snapshot'], true, flags: JSON_THROW_ON_ERROR);
                if ($outcome['kind'] !== 'closed_without_writing' || ($saved['fingerprint'] ?? '') !== $expected
                    || $outcome['actor'] !== $actor || $outcome['reason'] !== $reason) {
                    throw new RuntimeException('Different terminal decision');
                }

                return $outcome;
            }
            $plan = $this->plan($run);
            $snapshot = $this->snapshot($plan);
            if (! hash_equals($expected, hash('sha256', json_encode([$plan, $snapshot], JSON_THROW_ON_ERROR)))) {
                throw new RuntimeException('Closure preview changed');
            }
            $this->db->beginTransaction();
            try {
                $this->finish($run, 'closed_without_writing', ['fingerprint' => $expected, 'files' => $snapshot], $actor, $reason);
                $this->db->commit();
            } catch (Throwable $error) {
                $this->db->rollBack();
                throw $error;
            }

            return $this->outcome($run);
        });
    }

    /** Reopen a durable plan after navigation or a failed request, without changing it. */
    public function inspect(string $run): array
    {
        return $this->locked($run, function () use ($run) {
            $plan = $this->plan($run);
            $row = $this->q('SELECT state,error,plan_hash FROM translation_workbench_publication_runs WHERE id=?', [$run])->fetch(PDO::FETCH_ASSOC);
            $changes = [];
            foreach ($plan['contexts'] as $context) {
                $c = $context['context'];
                [$path, $nested] = $this->destination($c);
                $original = $plan['files'][$path]['original'] ?? null;
                $before = $original === null ? null : (new BaselineValueReader)->treeFromBytes($original, true);
                foreach (explode('.', $nested) as $part) {
                    $before = is_array($before) && array_key_exists($part, $before) ? $before[$part] : null;
                }
                $changes[] = ['path' => $path, 'key_id' => $c['key_id'], 'key' => $c['full_name'], 'locale' => $c['locale'], 'revision' => $c['revision_id'], 'before' => $before, 'after' => $c['value']];
            }

            return ['run' => $run, 'fingerprint' => $row['plan_hash'], 'state' => $row['state'], 'error' => $row['error'],
                'actor' => $plan['actor'], 'reason' => $plan['reason'], 'changes' => $changes, 'files' => $this->snapshot($plan)];
        });
    }

    public function recent(): array
    {
        return $this->q("SELECT id,state,error,plan::jsonb->>'actor' AS actor,plan::jsonb->>'reason' AS reason
            FROM translation_workbench_publication_runs
            WHERE state IN ('prepared','writing','interrupted') OR id IN (
                SELECT r.id FROM translation_workbench_publication_runs r JOIN translation_workbench_operations o ON o.id=r.id
                WHERE r.state IN ('complete','closed') ORDER BY o.completed_at DESC LIMIT 20)
            ORDER BY (state IN ('prepared','writing','interrupted')) DESC,
            (SELECT completed_at FROM translation_workbench_operations WHERE id=translation_workbench_publication_runs.id) DESC")->fetchAll(PDO::FETCH_ASSOC);
    }

    private function contexts(array $revisions): array
    {
        // Fail fast rather than deadlocking against a writer holding a key row.
        // Protect the identity inventory and active links for this short file transaction.
        $this->q('LOCK TABLE translation_workbench_key_names, translation_workbench_value_slots, translation_workbench_key_findings, translation_workbench_findings IN SHARE MODE NOWAIT');
        $keys = [];
        foreach ($revisions as $revision) {
            $keys[] = RevisionReviewContext::snapshot($this->db, $revision)['context']['key_id'];
        }
        $keys = array_unique($keys);
        sort($keys, SORT_NUMERIC);
        foreach ($keys as $key) {
            $this->q('SELECT id FROM translation_workbench_keys WHERE id=? FOR UPDATE', [$key]);
        }
        $contexts = [];
        foreach ($revisions as $revision) {
            if (! (new BaselineRevisionReviewer($this->db))->approvalIsCurrent($revision)) {
                throw new RuntimeException('Revision not currently approved');
            }
            $contexts[] = RevisionReviewContext::snapshot($this->db, $revision);
        }

        $this->assertDestinations($contexts);

        return $contexts;
    }

    /** Namespace hints require an explicit loader-root mapping; never infer them from a file group. */
    private function destination(array $context): array
    {
        if ($context['namespace'] !== '') {
            throw new RuntimeException('Namespace publication requires an explicit loader-root mapping');
        }
        TranslationKeySegments::configured()->assertAllowed($context['full_name']);
        if (! preg_match('/^[A-Za-z][A-Za-z0-9]*(?:[-_][A-Za-z0-9]+)*$/D', $context['locale'])
            || ! preg_match('/^[A-Za-z_][A-Za-z0-9_-]*(\.[A-Za-z_][A-Za-z0-9_-]*){2,}$/D', $context['full_name'])) {
            throw new RuntimeException('Unsupported publication key or locale');
        }
        $name = $context['kind'] === 'option'
            ? TranslationWorkbenchOption::key($context['full_name'], $context['option_identity'])
            : $context['full_name'];
        [$group, $nested] = explode('.', $name, 2);

        // Language file directories use the application's lowercase, hyphenated convention.
        // Keep the captured/reviewed locale spelling in the database unchanged.
        return [strtolower(str_replace('_', '-', $context['locale'])).'/'.$group.'.php', $nested];
    }

    private function assertDestinations(array $contexts): void
    {
        $inventory = $this->q('SELECT s.current_revision_id AS revision_id, s.locale, s.kind, s.option_identity,
            n.namespace, n.full_name FROM translation_workbench_value_slots s
            JOIN translation_workbench_key_names n ON n.key_id=s.key_id AND n.valid_to IS NULL
            WHERE s.current_revision_id IS NOT NULL')->fetchAll(PDO::FETCH_ASSOC);
        foreach ($contexts as $context) {
            $selected = $context['context'];
            [$path, $nested] = $this->destination($selected);
            $localeIdentity = strtolower(str_replace('_', '-', $selected['locale']));
            // Existing directories also count, even if no database slot uses them yet.
            foreach (glob($this->directory.'/*', GLOB_ONLYDIR) ?: [] as $folder) {
                $locale = basename($folder);
                if ($locale !== $localeIdentity && strtolower(str_replace('_', '-', $locale)) === $localeIdentity) {
                    throw new RuntimeException('Ambiguous locale directory: '.$selected['locale'].' / '.$locale);
                }
            }
            foreach ($inventory as $other) {
                if ($other['namespace'] !== $selected['namespace']) {
                    continue;
                }
                if ($other['locale'] !== $selected['locale'] && strtolower(str_replace('_', '-', $other['locale'])) === $localeIdentity) {
                    throw new RuntimeException('Ambiguous locale spelling: '.$selected['locale'].' / '.$other['locale']);
                }
                if ($other['locale'] !== $selected['locale'] || (int) $other['revision_id'] === (int) $selected['revision_id']) {
                    continue;
                }
                if (explode('.', $other['full_name'], 2)[0] !== explode('.', $selected['full_name'], 2)[0]) {
                    continue;
                }
                [$otherPath, $otherNested] = $this->destination($other);
                if ($path === $otherPath && ($nested === $otherNested || str_starts_with($nested, $otherNested.'.') || str_starts_with($otherNested, $nested.'.'))) {
                    throw new RuntimeException('Publication target conflict with revision #'.$other['revision_id']);
                }
            }
        }
    }

    private function finish(string $run, string $kind, array $snapshot, int $actor, string $reason, ?string $reasonLocale = null): void
    {
        $json = json_encode($snapshot, JSON_THROW_ON_ERROR);
        $this->q('INSERT INTO translation_workbench_publication_outcomes(run_id,kind,snapshot,actor,reason) VALUES (?,?,?,?,?)', [$run, $kind, $json, $actor, $reason]);
        // Preparation is event-free; terminal work has one unique index within that run operation.
        $this->q('INSERT INTO translation_workbench_events(operation_id,event_type,actor_id,reason,details) VALUES (?,?,?,?,?)', [$run, $kind === 'completed' ? 'revisions_published' : 'publication_closed_without_writing', $actor, $reason, json_encode($snapshot + ['reason_locale' => $reasonLocale], JSON_THROW_ON_ERROR)]);
        $this->q('DELETE FROM translation_workbench_file_reservations WHERE run_id=?', [$run]);
        $this->q('UPDATE translation_workbench_publication_runs SET state=?,error=NULL WHERE id=?', [$kind === 'completed' ? 'complete' : 'closed', $run]);
    }

    private function plan(string $run, ?string $expected = null): array
    {
        $row = $this->q('SELECT * FROM translation_workbench_publication_runs WHERE id=?', [$run])->fetch(PDO::FETCH_ASSOC);
        if (! $row || ! hash_equals($row['plan_hash'], hash('sha256', $row['plan'])) || ($expected !== null && $expected !== $row['plan_hash'])) {
            throw new RuntimeException('Publication plan mismatch');
        }

        $plan = json_decode($row['plan'], true, flags: JSON_THROW_ON_ERROR);
        if (($plan['directory'] ?? null) !== $this->directory) {
            throw new RuntimeException('Publication language root changed');
        }

        return $plan;
    }

    private function outcome(string $run): array|false
    {
        return $this->q('SELECT * FROM translation_workbench_publication_outcomes WHERE run_id=?', [$run])->fetch(PDO::FETCH_ASSOC);
    }

    private function snapshot(array $plan): array
    {
        $result = [];
        foreach ($plan['files'] as $path => $file) {
            $result[$path] = ['before' => $file['before'], 'after' => $file['after'], 'current' => $this->hash($path)];
        }

        return $result;
    }

    private function expectedFile(string $path, array $file): ?string
    {
        $hash = $this->hash($path);
        if ($hash !== $file['before'] && $hash !== $file['after']) {
            throw new RuntimeException('External file change: '.$path);
        }

        return $hash;
    }

    private function path(string $path): string
    {
        if (! preg_match('~^[A-Za-z0-9_-]+/[A-Za-z0-9_-]+\.php$~D', $path)
            || is_link($this->directory) || is_link(dirname($this->directory.'/'.$path)) || is_link($this->directory.'/'.$path)) {
            throw new RuntimeException('Unsafe publication path');
        }

        return $this->directory.'/'.$path;
    }

    private function bytes(string $path): ?string
    {
        $target = $this->path($path);
        if (! file_exists($target)) {
            return null;
        }
        $bytes = file_get_contents($target);
        if ($bytes === false) {
            throw new RuntimeException('Cannot read file');
        }

        return $bytes;
    }

    private function hash(string $path): ?string
    {
        $bytes = $this->bytes($path);

        return $bytes === null ? null : hash('sha256', $bytes);
    }

    private function write(string $path, string $bytes): void
    {
        $target = $this->path($path);
        if (! is_dir(dirname($target))) {
            mkdir(dirname($target), 0755);
        }
        $temp = tempnam(dirname($target), '.publish-');
        try {
            if (file_put_contents($temp, $bytes) !== strlen($bytes)
                || ! chmod($temp, is_file($target) ? (fileperms($target) & 0777) : 0644) || ! rename($temp, $target)) {
                throw new RuntimeException('Atomic replace failed');
            }
            if (function_exists('opcache_invalidate')) {
                opcache_invalidate($target, true);
            }
        } finally {
            if (is_file($temp)) {
                unlink($temp);
            }
        }
    }

    private function put(array &$tree, array $parts, string $value): void
    {
        $part = array_shift($parts);
        if ($parts === []) {
            if (isset($tree[$part]) && is_array($tree[$part])) {
                throw new RuntimeException('Container collision');
            }
            $tree[$part] = $value;

            return;
        }
        if (isset($tree[$part]) && ! is_array($tree[$part])) {
            throw new RuntimeException('Scalar collision');
        }
        $tree[$part] ??= [];
        $this->put($tree[$part], $parts, $value);
    }

    private function actor(int $actor, string $reason): void
    {
        if ($actor < 1 || trim($reason) === '') {
            throw new RuntimeException('Actor and reason required');
        }
    }

    private function q(string $sql, array $params = []): \PDOStatement
    {
        $statement = $this->db->prepare($sql);
        $statement->execute($params);

        return $statement;
    }

    private function locked(string $run, callable $action): mixed
    {
        if (! preg_match('/^[a-f0-9-]{36}$/D', $run)) {
            throw new RuntimeException('Invalid run UUID');
        }
        $this->q('SELECT pg_advisory_lock(hashtextextended(?, 0))', ['publication:'.$run]);
        try {
            return $action();
        } finally {
            $this->q('SELECT pg_advisory_unlock(hashtextextended(?, 0))', ['publication:'.$run]);
        }
    }
}
