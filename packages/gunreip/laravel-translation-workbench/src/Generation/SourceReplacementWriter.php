<?php

namespace Gunreip\TranslationWorkbench\Generation;

use Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchScanner;
use Illuminate\Support\Str;
use PDO;
use RuntimeException;

/** Durable intent precedes the atomic file replacement; a retry only completes the missing receipt. */
class SourceReplacementWriter
{
    public function __construct(private PDO $db, private string $root, private string $languageRoot, private TranslationWorkbenchScanner $scanner) {}

    private function q(string $sql, array $parameters = []): \PDOStatement
    {
        $q = $this->db->prepare($sql);
        $q->execute($parameters);

        return $q;
    }

    private function rows(string $path): array
    {
        return $this->q("SELECT f.*,l.id AS link_id,l.key_id,n.full_name,n.namespace FROM translation_workbench_findings f
            JOIN translation_workbench_source_files s ON s.id=f.source_file_id
            LEFT JOIN translation_workbench_key_findings l ON l.finding_id=f.id AND l.valid_to IS NULL
            LEFT JOIN translation_workbench_key_names n ON n.key_id=l.key_id AND n.valid_to IS NULL
            WHERE s.path=? AND f.status='active' ORDER BY f.id", [$path])->fetchAll(PDO::FETCH_ASSOC);
    }

    private function locked(string $path, callable $action): mixed
    {
        $identity = 'source-replacement:'.$this->root.'/'.$path;
        $this->q('SELECT pg_advisory_lock(hashtextextended(?,0))', [$identity]);
        try {
            return $action();
        } finally {
            $this->q('SELECT pg_advisory_unlock(hashtextextended(?,0))', [$identity]);
        }
    }

    private function databaseLock(): void
    {
        $this->db->exec('LOCK TABLE translation_workbench_findings,translation_workbench_key_findings,translation_workbench_key_names,translation_workbench_value_slots,translation_workbench_reviews IN SHARE ROW EXCLUSIVE MODE NOWAIT');
    }

    public function prepare(int $finding, string $locale, string $expected, int $actor, string $reason): string
    {
        if ($actor < 1 || trim($reason) === '') {
            throw new RuntimeException('Actor and reason required.');
        }
        $context = (new GenerationEditor($this->db, $this->root, $this->languageRoot))->inspect($finding, $locale);
        $preview = (new SourceReplacementPreview)->inspect($context, $locale, $this->root, $this->languageRoot);
        if (! hash_equals($preview['fingerprint'], $expected) || ! $preview['ready']) {
            throw new RuntimeException('Source preview changed or is not ready. Reopen the preview.');
        }
        $run = (string) Str::uuid();

        return $this->locked($preview['path'], function () use ($run, $context, $preview, $actor, $reason) {
            (new BaselineOperation($this->db))->run($run, 'source_replacement_prepared', [$preview, $actor, $reason], function () use ($run, $context, $preview, $actor, $reason) {
                $this->databaseLock();
                if ($pending = $this->q("SELECT o.id FROM translation_workbench_operations o WHERE o.kind='source_replacement_prepared' AND o.result->>'path'=?
                    AND NOT EXISTS(SELECT 1 FROM translation_workbench_operations done WHERE done.id=(o.result->>'finish')::uuid)", [$preview['path']])->fetchColumn()) {
                    throw new RuntimeException('This file has an open source replacement: '.$pending);
                }
                $rows = $this->rows($preview['path']);
                $selected = array_values(array_filter($rows, fn ($r) => (int) $r['id'] === $preview['finding']));
                if (count($selected) !== 1 || (int) $selected[0]['link_id'] !== $preview['link_id']
                    || RevisionReviewContext::snapshot($this->db, (int) $context['revision']['id'])['fingerprint'] !== $context['review']['fingerprint']) {
                    throw new RuntimeException('Finding or review changed.');
                }
                $path = (new SourceReplacementPreview)->path($this->root, $preview['path']);
                $before = file_get_contents($path);
                if (hash('sha256', $before) !== $preview['source_sha256']) {
                    throw new RuntimeException('Source changed after preview.');
                }
                $composed = (new SourceReplacementFilePlan)->build($preview['path'], $before, $rows, [$preview], $this->scanner);
                $after = $composed['after'];
                $transitions = $composed['transitions'];
                // Retain the legacy capture field for existing single-plan readers.
                $capture = collect($transitions)->firstWhere('predecessor', $preview['finding'])['capture'];

                return ['run' => $run, 'finish' => (string) Str::uuid(), 'root' => $this->root, 'language_root' => $this->languageRoot,
                    'path' => $preview['path'], 'actor' => $actor, 'reason' => $reason, 'preview' => $preview,
                    'review' => $context['review'], 'rows' => $rows, 'before' => $before, 'after' => $after, 'capture' => $capture, 'transitions' => $transitions];
            });

            return $run;
        });
    }

    private function remainingIds(int $key): array
    {
        return (new GenerationEditor($this->db, $this->root, $this->languageRoot))->remainingSourceGroupIds($key);
    }

    private function batchDraft(int $key, array $ids): array
    {
        sort($ids);
        if ($ids === [] || $ids !== $this->remainingIds($key)) throw new RuntimeException('The selected key group changed. Release the selection and select it again.');
        $editor = new GenerationEditor($this->db, $this->root, $this->languageRoot);
        $items = $groups = $plans = [];
        $keyName = '';
        foreach ($ids as $id) {
            try {
                $context = $editor->inspect($id, 'en');
                if ((int) $context['finding']['key_id'] !== $key) throw new RuntimeException('The key assignment changed.');
                $keyName = $context['finding']['full_name'];
                $preview = (new SourceReplacementPreview)->inspect($context, 'en', $this->root, $this->languageRoot);
                $items[$id] = $preview;
                if ($preview['ready']) $groups[$preview['path']][] = ['preview' => $preview, 'review' => $context['review']];
            } catch (RuntimeException $error) {
                $items[$id] = ['finding' => $id, 'path' => $context['finding']['path'] ?? '',
                    'line' => $context['finding']['source_line'] ?? null, 'column' => $context['finding']['source_column'] ?? null,
                    'before' => $context['finding']['raw_expression'] ?? '', 'after' => '',
                    'source_link_available' => ! ($error instanceof SourceLocationUnavailable),
                    'ready' => false, 'already_set' => false, 'blocker' => $error->getMessage()];
            }
            unset($context);
        }
        ksort($groups);
        foreach ($groups as $path => $group) {
            try {
                $pending = $this->q("SELECT id FROM translation_workbench_operations o WHERE kind='source_replacement_prepared' AND result->>'path'=?
                    AND NOT EXISTS (SELECT 1 FROM translation_workbench_operations done WHERE done.id=(o.result->>'finish')::uuid)", [$path])->fetchColumn();
                if ($pending) throw new RuntimeException('This file has an open source replacement: '.$pending);
                $absolute = (new SourceReplacementPreview)->path($this->root, $path);
                $before = file_get_contents($absolute);
                $rows = $this->rows($path);
                $previews = array_column($group, 'preview');
                foreach ($previews as $preview) {
                    if (hash('sha256', $before) !== $preview['source_sha256']) throw new RuntimeException('Source changed during the group preview.');
                }
                $composed = (new SourceReplacementFilePlan)->build($path, $before, $rows, $previews, $this->scanner);
                if (file_get_contents($absolute) !== $before) throw new RuntimeException('Source changed during the group preview.');
                $plans[$path] = ['path' => $path, 'rows' => $rows, 'before' => $before, ...$composed,
                    'preview' => $previews[0], 'previews' => $previews, 'review' => $group[0]['review'], 'reviews' => array_column($group, 'review')];
            } catch (RuntimeException $error) {
                foreach ($group as $entry) {
                    $id = $entry['preview']['finding'];
                    $items[$id]['ready'] = false;
                    $items[$id]['blocker'] = $error->getMessage();
                    if ($error instanceof SourceScanRequired) $items[$id]['scan_required'] = $error->details();
                    if (!empty($pending)) $items[$id]['pending_run'] = $pending;
                }
            }
        }
        uasort($items, fn ($a, $b) => [$a['path'], $a['finding']] <=> [$b['path'], $b['finding']]);
        $blocked = count(array_filter($items, fn ($item) => !$item['ready'] && !$item['already_set']));
        return ['key_id' => $key, 'key' => $keyName, 'finding_ids' => $ids, 'items' => array_values($items), 'plans' => $plans,
            'ready' => $blocked === 0 && $plans !== [], 'blocked' => $blocked];
    }

    public function pendingBatchForKey(int $key): ?string
    {
        $id = $this->q("SELECT batch.id FROM translation_workbench_operations batch
            WHERE batch.kind='source_replacement_batch_prepared' AND batch.result->'preview'->>'key_id'=?
                AND EXISTS (SELECT 1 FROM jsonb_array_elements(batch.result->'files') file
                    JOIN translation_workbench_operations child ON child.id=(file->>'run')::uuid
                    WHERE NOT EXISTS (SELECT 1 FROM translation_workbench_operations done WHERE done.id=(child.result->>'finish')::uuid))
            ORDER BY batch.completed_at DESC LIMIT 1", [(string) $key])->fetchColumn();

        return $id === false ? null : $id;
    }

    public function previewBatch(int $key, array $ids): array
    {
        $preview = $this->batchPreview($this->batchDraft($key, $ids));
        $observations = $this->currentScanObservations($preview['items']);
        foreach ($preview['items'] as &$item) {
            $id = $item['finding'];
            if (isset($observations[$id]) && $observations[$id]['from'] === ($item['line'] ?? '').':'.($item['column'] ?? '')) {
                $item['scan_position'] = $observations[$id];
                if (in_array($item['blocker'], [
                    SourceReplacementPreview::POSITION_CHANGED, SourceReplacementPreview::OCCURRENCE_CHANGED,
                ], true)) {
                    $item['blocker'] = 'Review the stored position against the saved scan before replacing this call.';
                }
            }
            $dependency = $item['scan_required'] ?? null;
            if ($dependency && ($observations[$dependency['finding']]['from'] ?? '') === $dependency['line'].':'.$dependency['column']) {
                $observation = $observations[$dependency['finding']];
                $reason = match ($observation['reason'] ?? '') {
                    'no_match' => __('The scan could not match the stored expression to an unassigned occurrence. Check whether the call was reformatted, changed or removed.'),
                    'ambiguous' => __('Several occurrences match. Compare the surrounding code to identify the original call.'),
                    'occupied_destination' => __('The proposed destination is occupied by another unresolved finding.'),
                    'source_usage' => __('The scan flagged the source usage at this position for review.'),
                    default => __('The current file does not match the stored call at this position.'),
                };
                $item['blocker'] = __('File replacement is blocked by finding #:id at :position.', [
                    'id' => $dependency['finding'], 'position' => $observation['from'],
                ]).' '.$reason;
                $item['blocking_finding'] = $dependency + ['expression' => $observation['expression'], 'to' => $observation['to'] ?? null];
                unset($item['scan_required']);
            }
        }
        unset($item);

        return $preview;
    }

    /** Only use positional evidence from the latest saved scan when its source bytes still match. */
    private function currentScanObservations(array $items): array
    {
        $requested = [];
        foreach ($items as $item) {
            $requested[] = ['id' => $item['finding'], 'path' => $item['path']];
            if (isset($item['scan_required'])) {
                $requested[] = ['id' => $item['scan_required']['finding'], 'path' => $item['scan_required']['path']];
            }
        }
        $rows = $this->q("WITH latest AS (
            SELECT id FROM translation_workbench_operations WHERE kind='complete_rescan' ORDER BY completed_at DESC,id DESC LIMIT 1
        ) SELECT DISTINCT r.id,r.path,f.raw_expression,s.scope_snapshot->'inventory'->'hashes'->>r.path AS hash,
            s.scope_snapshot->'reviews'->r.path->'position_preview'->r.id::text AS observation
            FROM latest JOIN translation_workbench_scan_runs s USING (id)
            CROSS JOIN jsonb_to_recordset(?::jsonb) AS r(id bigint,path text)
            JOIN translation_workbench_findings f ON f.id=r.id
            WHERE s.scope_snapshot->'reviews'->r.path->'position_preview'->r.id::text IS NOT NULL",
            [json_encode($requested, JSON_THROW_ON_ERROR)]);
        $observations = $hashes = [];
        foreach ($rows as $row) {
            $path = $row['path'];
            if (!array_key_exists($path, $hashes)) {
                try {
                    $hashes[$path] = hash_file('sha256', (new SourceReplacementPreview)->path($this->root, $path));
                } catch (RuntimeException) {
                    $hashes[$path] = false;
                }
            }
            if ($hashes[$path] && $hashes[$path] === $row['hash']) {
                $observations[$row['id']] = json_decode($row['observation'], true, flags: JSON_THROW_ON_ERROR) + ['expression' => $row['raw_expression']];
            }
        }

        return $observations;
    }

    private function batchPreview(array $draft): array
    {
        $fingerprint = hash('sha256', json_encode($draft, JSON_THROW_ON_ERROR));
        unset($draft['plans']);
        return ['batch' => true, 'finding' => $draft['finding_ids'][0], 'fingerprint' => $fingerprint,
            'source_locale' => 'en', ...$draft];
    }

    private function lockedFiles(array $paths, callable $action): mixed
    {
        sort($paths);
        $next = function (int $index) use (&$next, $paths, $action) {
            return $index === count($paths) ? $action() : $this->locked($paths[$index], fn () => $next($index + 1));
        };
        return $next(0);
    }

    public function prepareBatch(int $key, array $ids, string $expected, int $actor, string $reason): string
    {
        if ($actor < 1 || trim($reason) === '') throw new RuntimeException('Actor and reason required.');
        $draft = $this->batchDraft($key, $ids);
        $preview = $this->batchPreview($draft);
        if (!$draft['ready'] || !hash_equals($expected, $preview['fingerprint'])) throw new RuntimeException('The group preview changed or is blocked. Reopen the preview.');
        $run = (string) Str::uuid();
        $this->lockedFiles(array_keys($draft['plans']), fn () => (new BaselineOperation($this->db))->run($run, 'source_replacement_batch_prepared',
            [$expected, $actor, $reason], function () use ($draft, $preview, $run, $actor, $reason) {
                $this->databaseLock();
                if ($draft['finding_ids'] !== $this->remainingIds($draft['key_id'])) throw new RuntimeException('The selected findings changed.');
                $files = [];
                foreach ($draft['plans'] as $path => $plan) {
                    if ($pending = $this->q("SELECT id FROM translation_workbench_operations o WHERE kind='source_replacement_prepared' AND result->>'path'=?
                        AND NOT EXISTS (SELECT 1 FROM translation_workbench_operations done WHERE done.id=(o.result->>'finish')::uuid)", [$path])->fetchColumn()) throw new RuntimeException('This file has an open source replacement: '.$pending);
                    $this->validateFilePlan($plan);
                    $fileRun = (string) Str::uuid();
                    $plan += ['run' => $fileRun, 'finish' => (string) Str::uuid(), 'root' => $this->root, 'language_root' => $this->languageRoot,
                        'batch' => $run, 'actor' => $actor, 'reason' => $reason];
                    (new BaselineOperation($this->db))->run($fileRun, 'source_replacement_prepared', [$run, $path], fn () => $plan);
                    $files[] = ['run' => $fileRun, 'path' => $path];
                }
                return ['run' => $run, 'root' => $this->root, 'language_root' => $this->languageRoot, 'actor' => $actor,
                    'reason' => $reason, 'preview' => $preview, 'files' => $files, 'close' => (string) Str::uuid()];
            }));
        return $run;
    }

    private function validateFilePlan(array $plan, bool $allowWritten = false): void
    {
        if ($this->rows($plan['path']) != $plan['rows']) throw new RuntimeException('Source findings or assignments changed.');
        $path = (new SourceReplacementPreview)->path($this->root, $plan['path']);
        $bytes = file_get_contents($path);
        if ($allowWritten && $bytes === $plan['after']) return;
        if ($bytes !== $plan['before']) throw new RuntimeException('Source file changed outside this operation.');
        foreach ($plan['reviews'] ?? [$plan['review']] as $review) {
            if (!$this->reviewMatches($review, $plan)) throw new RuntimeException('Approval or published source-language file changed.');
        }
        foreach ($plan['previews'] ?? [$plan['preview']] as $preview) {
            $lang = (new SourceReplacementPreview)->path($this->languageRoot, $preview['language_path']);
            if (hash_file('sha256', $lang) !== $preview['language_sha256']) throw new RuntimeException('Approval or published source-language file changed.');
        }
    }

    private function reviewMatches(array $review, array $plan): bool
    {
        $current = RevisionReviewContext::snapshot($this->db, (int) $review['context']['revision_id']);
        return $review['latest_decision'] === $current['latest_decision']
            && ReviewUsageContinuity::contextMatches($this->db, $review['context'], $current['context']);
    }

    private function batchPlan(string $run): ?array
    {
        if (!Str::isUuid($run)) throw new RuntimeException('Invalid source replacement ID.');
        $json = $this->q("SELECT result FROM translation_workbench_operations WHERE id=? AND kind='source_replacement_batch_prepared'", [$run])->fetchColumn();
        if (!$json) return null;
        $plan = json_decode($json, true, flags: JSON_THROW_ON_ERROR);
        if ($plan['root'] !== $this->root || $plan['language_root'] !== $this->languageRoot) throw new RuntimeException('Source replacement root changed.');
        return $plan;
    }

    private function inspectBatch(array $plan): array
    {
        $files = $successors = [];
        foreach ($plan['files'] as $file) {
            try {
                $state = $this->inspect($file['run']);
                $files[] = $file + ['state' => $state['state'], 'file_state' => $state['file_state'], 'impacts' => $state['impacts']];
                $successors = [...$successors, ...($state['result']['successors'] ?? [])];
            } catch (RuntimeException $error) {
                $files[] = $file + ['state' => 'unavailable', 'file_state' => 'unavailable', 'error' => $error->getMessage(), 'impacts' => []];
            }
        }
        $states = array_column($files, 'state');
        $state = count(array_filter($states, fn ($s) => $s === 'complete')) === count($files) ? 'complete'
            : (count(array_filter($states, fn ($s) => in_array($s, ['closed', 'complete'], true))) === count($files) ? 'closed' : 'pending');
        return ['batch' => true, 'run' => $plan['run'], 'actor' => $plan['actor'], 'reason' => $plan['reason'], 'preview' => $plan['preview'],
            'files' => $files, 'state' => $state, 'file_state' => 'per_file', 'result' => ['successors' => $successors]];
    }

    public function acknowledgeBatch(string $run, int $actor): void
    {
        $plan = $this->batchPlan($run);
        if (!$plan || $actor < 1 || $actor !== $plan['actor']) {
            throw new RuntimeException('Only the preparing user may confirm the batch result.');
        }

        // Completion receipts are immutable. Record one acknowledgement on the batch operation itself.
        $files = array_column($plan['files'], 'run');
        $completed = $this->q("SELECT count(*) FROM jsonb_array_elements_text(?::jsonb) files(run)
            JOIN translation_workbench_operations prepared ON prepared.id=files.run::uuid
            JOIN translation_workbench_operations applied ON applied.id=(prepared.result->>'finish')::uuid
            WHERE applied.kind='source_replacement_applied'", [json_encode($files, JSON_THROW_ON_ERROR)])->fetchColumn();
        if (!$files || (int) $completed !== count($files)) {
            throw new RuntimeException('All selected source replacements must be completed before confirming the result.');
        }
        $this->q("INSERT INTO translation_workbench_events(operation_id,event_type,actor_id,reason,details)
            VALUES (?, 'source_replacement_batch_acknowledged', ?, 'Completed source replacements acknowledged', ?::jsonb)
            ON CONFLICT (operation_id,event_index) DO NOTHING", [
                $run, $actor, json_encode(['key' => $plan['preview']['key_id']], JSON_THROW_ON_ERROR),
            ]);
    }

    private function applyBatch(array $plan, int $actor, ?callable $fault): array
    {
        if ($actor !== $plan['actor']) throw new RuntimeException('Only the preparing user may apply this source replacement.');
        return $this->lockedFiles(array_column($plan['files'], 'path'), function () use ($plan, $actor, $fault) {
            // Check every unfinished file before the first write. Each file is checked again on application.
            foreach ($plan['files'] as $file) {
                $state = $this->inspect($file['run']);
                if ($state['state'] === 'closed') throw new RuntimeException('A file operation was closed. Inspect the group before continuing.');
                if ($state['state'] !== 'complete') $this->validateFilePlan($this->plan($file['run']), true);
            }
            $successors = [];
            foreach ($plan['files'] as $index => $file) {
                $result = $this->apply($file['run'], $actor, $fault);
                $successors = [...$successors, ...$result['successors']];
                if ($fault) $fault('after_file');
            }
            return ['key' => $plan['preview']['key_id'], 'successors' => $successors];
        });
    }

    private function plan(string $run): array
    {
        if (! Str::isUuid($run)) {
            throw new RuntimeException('Invalid source replacement ID.');
        }
        $json = $this->q("SELECT result FROM translation_workbench_operations WHERE id=? AND kind='source_replacement_prepared'", [$run])->fetchColumn();
        if (! $json) {
            throw new RuntimeException('Source replacement not found.');
        }
        $plan = json_decode($json, true, flags: JSON_THROW_ON_ERROR);
        if ($plan['root'] !== $this->root || $plan['language_root'] !== $this->languageRoot) {
            throw new RuntimeException('Source replacement root changed.');
        }

        return $plan;
    }

    public function inspect(string $run): array
    {
        if ($batch = $this->batchPlan($run)) return $this->inspectBatch($batch);
        $plan = $this->plan($run);
        $receipt = $this->q('SELECT kind,result FROM translation_workbench_operations WHERE id=?', [$plan['finish']])->fetch(PDO::FETCH_ASSOC);
        $path = (new SourceReplacementPreview)->path($this->root, $plan['path']);
        $hash = hash_file('sha256', $path);

        $originals = array_column($plan['rows'], null, 'id');
        $transitions = $plan['transitions'] ?? [[
            'predecessor' => $plan['preview']['finding'], 'capture' => $plan['capture'],
        ]];
        $impacts = array_map(function ($transition) use ($originals) {
            $original = $originals[$transition['predecessor']];

            return ['finding' => (int) $original['id'], 'kind' => ($transition['movement'] ?? false) ? 'Position changed' : 'Source argument replaced',
                'key_id' => $original['key_id'], 'key' => $original['full_name'],
                'before_line' => (int) $original['source_line'], 'before_column' => (int) $original['source_column'],
                'after_line' => $transition['capture']['source_line'], 'after_column' => $transition['capture']['source_column']];
        }, $transitions);

        return ['run' => $run, 'actor' => $plan['actor'], 'reason' => $plan['reason'], 'preview' => $plan['preview'],
            'impacts' => $impacts,
            'state' => $receipt ? ($receipt['kind'] === 'source_replacement_closed' ? 'closed' : 'complete') : 'pending',
            'file_state' => $hash === hash('sha256', $plan['after']) ? 'written' : ($hash === hash('sha256', $plan['before']) ? 'original' : 'changed'),
            'result' => $receipt ? json_decode($receipt['result'], true, flags: JSON_THROW_ON_ERROR) : null];
    }

    public function recent(): array
    {
        $runs = $this->q("SELECT p.kind,p.id,COALESCE(p.result->>'path', 'Key #'||(p.result->'preview'->>'key_id')) AS path,p.result->>'actor' AS actor,
            COALESCE(done.kind,'pending') AS state FROM translation_workbench_operations p
            LEFT JOIN translation_workbench_operations done ON done.id=(p.result->>'finish')::uuid
            WHERE (p.kind='source_replacement_prepared' AND p.result->>'batch' IS NULL) OR p.kind='source_replacement_batch_prepared'
            ORDER BY (done.id IS NULL) DESC,p.completed_at DESC")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($runs as &$run) {
            if ($run['kind'] === 'source_replacement_batch_prepared') $run['state'] = $this->inspect($run['id'])['state'];
        }
        return $runs;
    }

    public function apply(string $run, int $actor, ?callable $fault = null): array
    {
        if ($batch = $this->batchPlan($run)) return $this->applyBatch($batch, $actor, $fault);
        $plan = $this->plan($run);
        if ($actor !== $plan['actor']) {
            throw new RuntimeException('Only the preparing user may apply this source replacement.');
        }

        return $this->locked($plan['path'], fn () => (new BaselineOperation($this->db))->run($plan['finish'], 'source_replacement_applied', [$run, $actor], function () use ($plan, $fault) {
            $this->databaseLock();
            if ($this->rows($plan['path']) != $plan['rows']) {
                throw new RuntimeException('Source findings or assignments changed. No automatic reconciliation is possible.');
            }
            $path = (new SourceReplacementPreview)->path($this->root, $plan['path']);
            $bytes = file_get_contents($path);
            if ($bytes !== $plan['before'] && $bytes !== $plan['after']) {
                throw new RuntimeException('Source file changed outside this operation.');
            }
            if ($bytes === $plan['before']) {
                $this->validateFilePlan($plan);
                $temp = ReplacementFileAccess::temporary($path, '.tw-source-');
                try {
                    if (@file_put_contents($temp, $plan['after']) !== strlen($plan['after'])) {
                        throw new RuntimeException('Cannot write the temporary source file. Check permissions and available disk space: '.$path);
                    }
                    ReplacementFileAccess::permissions($temp, $path);
                    if (file_get_contents($path) !== $plan['before']) {
                        throw new RuntimeException('Source changed immediately before replacement. No replacement was performed: '.$path);
                    }
                    if (! @rename($temp, $path)) {
                        throw new RuntimeException('Cannot replace the source file. Check directory permissions, file locks and filesystem restrictions: '.$path);
                    }
                } finally {
                    if (is_file($temp)) {
                        unlink($temp);
                    }
                }
                if (function_exists('opcache_invalidate')) {
                    opcache_invalidate($path, true);
                }
                if ($fault) {
                    $fault('after_write');
                }
            }
            $result = (new CorrectedSourceReconciler($this->db))->apply($plan['finish'], $plan['preview']['key_id'], $plan['transitions'] ?? [[
                'predecessor' => $plan['preview']['finding'], 'before_expression' => $plan['preview']['before'], 'capture' => $plan['capture'],
            ]], $plan['actor'], $plan['reason'], withinTransaction: true);
            if (file_get_contents($path) !== $plan['after']) {
                throw new RuntimeException('Source changed before the reconciliation completed.');
            }

            return $result;
        }));
    }

    public function close(string $run, int $actor, string $reason): array
    {
        if ($batch = $this->batchPlan($run)) {
            if ($actor < 1 || trim($reason) === '') throw new RuntimeException('Actor and reason required.');
            return $this->lockedFiles(array_column($batch['files'], 'path'), fn () => (new BaselineOperation($this->db))->run(
                $batch['close'], 'source_replacement_batch_closed', [$run, $actor, $reason], function () use ($batch, $actor, $reason) {
                    foreach ($batch['files'] as $file) {
                        $state = $this->inspect($file['run']);
                        if ($state['state'] === 'pending' && $state['file_state'] !== 'original') throw new RuntimeException('An unfinished file was already changed. Resume reconciliation before closing.');
                    }
                    foreach ($batch['files'] as $file) {
                        if ($this->inspect($file['run'])['state'] === 'pending') $this->close($file['run'], $actor, $reason);
                    }
                    return ['run' => $batch['run']];
                }));
        }
        if ($actor < 1 || trim($reason) === '') {
            throw new RuntimeException('Actor and reason required.');
        }
        $plan = $this->plan($run);

        return $this->locked($plan['path'], fn () => (new BaselineOperation($this->db))->run($plan['finish'], 'source_replacement_closed', [$run, $actor, $reason], function () use ($plan, $actor, $reason) {
            $path = (new SourceReplacementPreview)->path($this->root, $plan['path']);
            if (file_get_contents($path) !== $plan['before']) {
                throw new RuntimeException('The source is not in its original state. Resume reconciliation instead of closing.');
            }
            $result = ['run' => $plan['run'], 'finding' => $plan['preview']['finding'], 'key' => $plan['preview']['key_id']];
            $this->q('INSERT INTO translation_workbench_events(operation_id,event_type,actor_id,reason,details) VALUES (?,?,?,?,?)', [$plan['finish'], 'source_replacement_closed', $actor, $reason, json_encode($result, JSON_THROW_ON_ERROR)]);

            return $result;
        }));
    }
}
