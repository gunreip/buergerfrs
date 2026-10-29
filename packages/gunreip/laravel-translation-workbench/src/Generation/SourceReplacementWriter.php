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
                $lines = explode("\n", $before);
                $offset = array_sum(array_map('strlen', array_slice($lines, 0, $preview['line'] - 1))) + $preview['line'] - 1 + $preview['column'] - 1;
                $after = substr_replace($before, $preview['after'], $offset, strlen($preview['before']));
                $scanned = $this->scanner->scanSource($preview['path'], $after)->groupBy(fn ($item) => $item->meta['source_offset'] ?? -1);
                $items = $scanned->get($offset, collect());
                if ($items->count() !== 1) {
                    throw new RuntimeException('The changed call cannot be uniquely recaptured by the scanner.');
                }
                $capture = (new CopiedSourceCapture)->capture($items->first(), $after, hash('sha256', $after), [
                    $preview['key'] => ['value' => $preview['published_value'], 'provenance' => ['path' => $preview['language_path'], 'sha256' => $preview['language_sha256'], 'locale' => $preview['source_locale']]],
                ]);
                if ($capture['found_translation_key'] !== $preview['key'] || $capture['source_context']['state'] !== 'confirmed_call') {
                    throw new RuntimeException('Scanner did not confirm the proposed key call.');
                }

                $transitions = [['predecessor' => $preview['finding'], 'before_expression' => $preview['before'], 'capture' => $capture]];
                $edit = $preview['edit'];
                $delta = strlen($edit['replacement']) - $edit['length'];
                foreach ($rows as $row) {
                    if ((int) $row['id'] === $preview['finding']) {
                        continue;
                    }
                    $line = (int) $row['source_line'];
                    $column = (int) $row['source_column'];
                    $oldOffset = array_sum(array_map('strlen', array_slice($lines, 0, $line - 1))) + $line - 1 + $column - 1;
                    if ($line < 1 || $column < 1 || ! isset($lines[$line - 1]) || $column > strlen($lines[$line - 1]) + 1
                        || $row['raw_expression'] === '' || substr($before, $oldOffset, strlen($row['raw_expression'])) !== $row['raw_expression']) {
                        throw new RuntimeException('Stored source position is stale for finding #'.$row['id'].'. Run a fresh scan.');
                    }
                    if ($oldOffset < $edit['offset'] + $edit['length'] && $oldOffset + strlen($row['raw_expression']) > $edit['offset']) {
                        throw new RuntimeException('Another stored call overlaps the edited argument: finding #'.$row['id'].'.');
                    }
                    $newOffset = $oldOffset >= $edit['offset'] + $edit['length'] ? $oldOffset + $delta : $oldOffset;
                    $prefix = substr($after, 0, $newOffset);
                    $newLine = substr_count($prefix, "\n") + 1;
                    $lastBreak = strrpos($prefix, "\n");
                    $newColumn = $newOffset - ($lastBreak === false ? -1 : $lastBreak);
                    if ($newLine === $line && $newColumn === $column) {
                        continue;
                    }
                    $matches = $scanned->get($newOffset);
                    if (! $matches || $matches->count() !== 1) {
                        throw new RuntimeException('Moved finding cannot be uniquely recaptured: #'.$row['id'].'.');
                    }
                    $moved = (new CopiedSourceCapture)->capture($matches->first(), $after, hash('sha256', $after), []);
                    if ($moved['raw_expression'] !== $row['raw_expression'] || $moved['raw_literal'] !== $row['raw_literal']
                        || $moved['raw_source_code_literal'] !== $row['raw_source_code_literal'] || $moved['source_context']['state'] !== 'confirmed_call') {
                        throw new RuntimeException('Moved finding content changed: #'.$row['id'].'.');
                    }
                    $transitions[] = ['predecessor' => (int) $row['id'], 'before_expression' => $row['raw_expression'],
                        'key' => $row['key_id'] === null ? null : (int) $row['key_id'], 'movement' => true, 'capture' => $moved];
                }

                return ['run' => $run, 'finish' => (string) Str::uuid(), 'root' => $this->root, 'language_root' => $this->languageRoot,
                    'path' => $preview['path'], 'actor' => $actor, 'reason' => $reason, 'preview' => $preview,
                    'review' => $context['review'], 'rows' => $rows, 'before' => $before, 'after' => $after, 'capture' => $capture, 'transitions' => $transitions];
            });

            return $run;
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
        return $this->q("SELECT p.id,p.result->>'path' AS path,p.result->>'actor' AS actor,
            COALESCE(done.kind,'pending') AS state FROM translation_workbench_operations p
            LEFT JOIN translation_workbench_operations done ON done.id=(p.result->>'finish')::uuid
            WHERE p.kind='source_replacement_prepared'
            ORDER BY (done.id IS NULL) DESC,p.completed_at DESC")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function apply(string $run, int $actor, ?callable $fault = null): array
    {
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
                $review = RevisionReviewContext::snapshot($this->db, (int) $plan['review']['context']['revision_id']);
                $lang = (new SourceReplacementPreview)->path($this->languageRoot, $plan['preview']['language_path']);
                if ($review['fingerprint'] !== $plan['review']['fingerprint'] || hash_file('sha256', $lang) !== $plan['preview']['language_sha256']) {
                    throw new RuntimeException('Approval or published source-language file changed.');
                }
                $temp = @tempnam(dirname($path), '.tw-source-');
                if ($temp === false) {
                    throw new RuntimeException('Cannot create a temporary file beside the source. Check directory permissions.');
                }
                try {
                    if (@file_put_contents($temp, $plan['after']) !== strlen($plan['after']) || ! @chmod($temp, fileperms($path) & 0777)
                        || file_get_contents($path) !== $plan['before'] || ! @rename($temp, $path)) {
                        throw new RuntimeException('Atomic source replacement failed.');
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
