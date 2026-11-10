<?php

namespace Gunreip\TranslationWorkbench\Generation;

use Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchScanner;
use Illuminate\Support\Str;
use PDO;
use RuntimeException;

/** One scan workflow shared by the terminal and the UI. */
class WorkingScanService
{
    public function __construct(private GenerationDatabase $database, private TranslationWorkbenchScanner $scanner) {}

    public function target(): array
    {
        $db = $this->database->connection();
        $identity = $this->database->verify($db);
        return ['database' => $db->query('SELECT current_database()')->fetchColumn(), 'schema' => $identity['schema'],
            'generation' => $identity['generation'], 'schema_version' => $identity['version'],
            'paths' => array_values((array) config('translation-workbench.paths', [])), 'root' => base_path()];
    }

    public function preview(?callable $progress = null): array
    {
        return $this->locked(fn ($db, $target) => $this->prepare($db, $target, $progress));
    }

    public function apply(array $preview, int $actor, ?callable $progress = null): array
    {
        if ($actor < 1) throw new RuntimeException('A user ID is required to save a scan.');
        return $this->locked(function ($db, $target) use ($preview, $actor, $progress) {
            if ($target !== $preview['target']) throw new RuntimeException('The scan target or configuration changed. Start a new preview.');
            if ($target['schema_version'] < 3) throw new RuntimeException('Upgrade the workbench schema before saving a scan: php artisan translation-workbench:upgrade-scan-schema --apply');
            $fresh = $this->prepare($db, $target, $progress);
            if (!hash_equals($preview['fingerprint'], $fresh['fingerprint'])) return ['state' => 'changed', 'preview' => $fresh];
            $result = (new CompleteRescanWriter($db, $this->scanner, $progress))->apply((string) Str::uuid(), $fresh['request'], actor: $actor);
            return ['state' => 'complete', 'result' => $result, 'counts' => $fresh['counts']];
        });
    }

    private function prepare(PDO $db, array $target, ?callable $progress = null): array
    {
        $request = (new CompleteRescanWriter($db, $this->scanner, $progress))->prepare($target['paths'], $this->database->languageEvidence($db));
        $counts = ['confirmed' => 0, 'created' => 0, 'moved' => 0, 'removed' => 0, 'review_files' => 0, 'outside_scope_files' => 0,
            'suggested_keys_initialized' => count($request['plan']['suggestion_initializations'])];
        foreach ($request['plan']['files'] as $file) {
            foreach (['confirmed', 'created', 'moved', 'removed'] as $key) $counts[$key] += count($file[$key] ?? []);
            $counts['review_files'] += (int) in_array($file['state'], ['review', 'relocation_review'], true);
            $counts['outside_scope_files'] += (int) ($file['state'] === 'outside_scope');
        }
        $counts['usage_decisions'] = count($request['plan']['usage_decisions']);
        return ['target' => $target, 'counts' => $counts, 'request' => $request,
            'fingerprint' => hash('sha256', json_encode([$target, $request], JSON_THROW_ON_ERROR))];
    }

    private function locked(callable $action): array
    {
        $target = $this->target();
        $db = $this->database->connection();
        $name = 'workbench-regular-scan:'.$target['generation'];
        $lock = $db->prepare('SELECT pg_try_advisory_lock(hashtextextended(?,0))');
        $lock->execute([$name]);
        if (!$lock->fetchColumn()) throw new RuntimeException('Another scan is running for this data set. Wait until it finishes.');
        try { return $action($db, $target); }
        finally { $unlock = $db->prepare('SELECT pg_advisory_unlock(hashtextextended(?,0))'); $unlock->execute([$name]); }
    }
}
