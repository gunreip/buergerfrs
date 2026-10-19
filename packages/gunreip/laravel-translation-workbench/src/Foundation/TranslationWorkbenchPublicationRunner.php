<?php

namespace Gunreip\TranslationWorkbench\Foundation;

use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchReview;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/** Resumable lang-file publication. Code replacement is deliberately a separate operation. */
class TranslationWorkbenchPublicationRunner
{
    public function __construct(
        private readonly TranslationWorkbenchPublicationPreview $preview,
        private readonly TranslationWorkbenchLangFileExporter $exporter,
    ) {}

    public function publish(string $kind, int $keyId, int $findingId, array $locales, string $fingerprint): array
    {
        return $this->locked(function () use ($kind, $keyId, $findingId, $locales, $fingerprint): array {
            $run = null;
            try {
                DB::transaction(function () use ($kind, $keyId, $findingId, $locales, $fingerprint, &$run): void {
                    $this->lockDatabase();
                    $report = $this->preview->verify($kind, $keyId, $findingId, $locales, $fingerprint);
                    $this->assertPublishable($report);
                    if ($report['values_new'] + $report['values_changed'] === 0) {
                        $this->conflict('No new or changed translations to publish.');
                    }
                    $prepared = $this->exporter->prepare($locales, $report['keys'], $kind === 'option');
                    if ($this->planHashes($prepared) !== $this->planHashes($report)) {
                        $this->conflict('The files changed after preview verification. Create a new preview.');
                    }
                    $this->assertNoPendingOverlap($prepared['plans']);
                    $run = ['id' => (string) Str::uuid(), 'state' => 'prepared', 'actor_id' => Auth::id(),
                        'context' => compact('kind', 'keyId', 'findingId', 'locales'),
                        'data_fingerprint' => $report['data_fingerprint'], 'plans' => $prepared['plans'],
                        'created_at' => now()->toIso8601String(), 'error' => null];
                    $this->saveRun($run);
                    $this->apply($run);
                });
                $run['state'] = 'complete';
                $this->saveRun($run);

                return $this->summary($run);
            } catch (\Throwable $exception) {
                if ($run !== null) {
                    $run['state'] = 'interrupted';
                    $run['error'] = $exception->getMessage();
                    $this->saveRun($run);
                    throw new \RuntimeException('Publication interrupted; resume run '.$run['id'].': '.$exception->getMessage(), 0, $exception);
                }
                throw $exception;
            }
        });
    }

    public function resume(string $runId, ?array $expectedContext = null): array
    {
        return $this->locked(function () use ($runId, $expectedContext): array {
            if (! Str::isUuid($runId)) {
                $this->conflict('Invalid publication run ID.');
            }
            $run = json_decode(File::get($this->directory().'/'.$runId.'.json'), true, flags: JSON_THROW_ON_ERROR);
            if ($expectedContext !== null && ! $this->matchesContext($run, $expectedContext)) {
                $this->conflict('This publication belongs to another translation context.');
            }
            if ($this->resolution($run) !== null) {
                $this->conflict('This publication was closed without further writing. Create a new preview.');
            }
            try {
                DB::transaction(function () use (&$run): void {
                    $this->lockDatabase();
                    $this->apply($run);
                });
                $run['state'] = 'complete';
                $run['error'] = null;
                $this->saveRun($run);

                return $this->summary($run);
            } catch (\Throwable $exception) {
                $run['state'] = 'interrupted';
                $run['error'] = $exception->getMessage();
                $this->saveRun($run);
                throw $exception;
            }
        });
    }

    public function previewClosure(string $runId, array $context): array
    {
        return $this->locked(function () use ($runId, $context): array {
            return DB::transaction(function () use ($runId, $context): array {
                $this->lockDatabase();

                return $this->closureSnapshot($this->loadRun($runId, $context));
            });
        });
    }

    /** Close the attempt, preserving all files and its original prepared plan. */
    public function closeWithoutWriting(string $runId, array $context, string $fingerprint, string $reason): array
    {
        return $this->locked(function () use ($runId, $context, $fingerprint, $reason): array {
            $reason = trim($reason);
            if ($reason === '' || mb_strlen($reason) > 2000) {
                $this->conflict('Provide a closure reason of 1 to 2000 characters.');
            }
            $run = $this->loadRun($runId, $context);
            DB::transaction(function () use ($run, $fingerprint, $reason): void {
                $this->lockDatabase();
                $snapshot = $this->closureSnapshot($run);
                if (! hash_equals($snapshot['fingerprint'], $fingerprint)) {
                    $this->conflict('The publication or its files changed. Review the closure preview again.');
                }
                TranslationWorkbenchReview::create([
                    'key_id' => $run['context']['keyId'], 'finding_id' => $run['context']['findingId'],
                    'review_type' => 'publication', 'decision' => 'publication_closed_without_writing',
                    'new_values' => ['files' => $snapshot['files'], 'reason' => $reason],
                    'meta' => ['run_id' => $run['id'], 'closure_fingerprint' => $fingerprint, 'initiated_by' => $run['actor_id']],
                    'reviewed_at' => now(), 'reviewed_by_user_id' => Auth::id(),
                ]);
            });
            // The DB receipt is authoritative even if this journal update fails.
            $run['state'] = 'closed';
            $this->saveRun($run);

            return $this->summary($run);
        });
    }

    private function loadRun(string $runId, array $context): array
    {
        if (! Str::isUuid($runId)) {
            $this->conflict('Invalid publication run ID.');
        }
        $run = json_decode(File::get($this->directory().'/'.$runId.'.json'), true, flags: JSON_THROW_ON_ERROR);
        if (! $this->matchesContext($run, $context)) {
            $this->conflict('This publication belongs to another translation context.');
        }

        return $run;
    }

    private function resolution(array $run): ?TranslationWorkbenchReview
    {
        return TranslationWorkbenchReview::where('review_type', 'publication')
            ->where('decision', 'publication_closed_without_writing')->where('meta->run_id', $run['id'])->first();
    }

    private function closureSnapshot(array $run): array
    {
        if ($run['state'] === 'complete' || $this->resolution($run) !== null ||
            TranslationWorkbenchReview::where('review_type', 'publication')->where('decision', 'lang_files_published')
                ->where('meta->run_id', $run['id'])->exists()) {
            $this->conflict('Only unfinished publications without a completion receipt can be closed.');
        }
        $files = [];
        foreach ($run['plans'] as $plan) {
            $hash = $this->hash($this->path($plan));
            $files[] = [
                'path' => $plan['path'], 'source_hash' => $plan['source_hash'], 'result_hash' => $plan['result_hash'],
                'observed_hash' => $hash,
                'current_state' => $hash === $plan['result_hash'] ? 'result' : ($hash === $plan['source_hash'] ? 'source' : 'changed'),
            ];
        }

        return ['id' => $run['id'], 'files' => $files,
            'fingerprint' => hash('sha256', json_encode([$run, $files], JSON_THROW_ON_ERROR))];
    }

    /** Read-only journal projection: prepared file contents never leave this service. */
    public function runs(string $kind, int $keyId, int $findingId): array
    {
        app(TranslationWorkbenchEditorAccess::class)->authorize();
        $runs = [];
        foreach (glob($this->directory().'/*.json') ?: [] as $file) {
            $run = json_decode(File::get($file), true, flags: JSON_THROW_ON_ERROR);
            if (! $this->matchesContext($run, compact('kind', 'keyId', 'findingId'))) {
                continue;
            }
            $summary = $this->summary($run);
            $resolution = $this->resolution($run);
            if ($resolution !== null) {
                $summary['state'] = 'closed';
            }
            $summary['closure_reason'] = $resolution?->new_values['reason'] ?? null;
            $summary['created_at'] = $run['created_at'];
            $summary['error'] = $run['error'];
            $summary['recorded'] = TranslationWorkbenchReview::where('review_type', 'publication')
                ->where('decision', 'lang_files_published')->where('meta->run_id', $run['id'])->exists();
            foreach ($run['plans'] as $index => $plan) {
                $hash = $this->hash($this->path($plan));
                $summary['files'][$index]['current_state'] = match (true) {
                    $hash === $plan['result_hash'] => 'result',
                    $hash === $plan['source_hash'] => 'source',
                    default => 'changed',
                };
            }
            $runs[] = $summary;
        }
        usort($runs, fn ($a, $b) => strcmp($b['created_at'], $a['created_at']));

        return $runs;
    }

    private function matchesContext(array $run, array $context): bool
    {
        foreach (['kind', 'keyId', 'findingId'] as $key) {
            if ($run['context'][$key] !== $context[$key]) {
                return false;
            }
        }

        return true;
    }

    private function apply(array &$run): void
    {
        $context = $run['context'];
        $receipt = TranslationWorkbenchReview::where('review_type', 'publication')
            ->where('decision', 'lang_files_published')->where('meta->run_id', $run['id'])->first();
        if ($receipt !== null) {
            foreach ($run['plans'] as &$plan) {
                if (($plan['values_new'] + $plan['values_changed']) > 0) {
                    if ($this->hash($this->path($plan)) !== $plan['result_hash']) {
                        $this->conflict('A completed publication file has changed; it will not be overwritten.');
                    }
                    $plan['written'] = true;
                }
            }
            unset($plan);

            return;
        }
        $current = $this->preview->preview($context['kind'], $context['keyId'], $context['findingId'], $context['locales']);
        if (! hash_equals($run['data_fingerprint'], $current['data_fingerprint'])) {
            $this->conflict('Publication data or approvals changed. The interrupted run cannot continue.');
        }
        $this->assertPublishable($current);
        // Validate every file before touching the next one, including unchanged files in scope.
        foreach ($run['plans'] as $plan) {
            if (! hash_equals($plan['result_hash'], hash('sha256', $plan['_contents']))) {
                $this->conflict('The prepared publication contents do not match their checksum.');
            }
            $hash = $this->hash($this->path($plan));
            if ($hash !== $plan['source_hash'] && $hash !== $plan['result_hash']) {
                $this->conflict('A publication file was changed externally. It will not be overwritten.');
            }
        }
        foreach ($run['plans'] as &$plan) {
            if ($plan['values_new'] + $plan['values_changed'] === 0) {
                continue;
            }
            if ($this->hash($this->path($plan)) !== $plan['result_hash']) {
                $this->replaceFile($this->path($plan), $plan['_contents'], $plan['source_hash']);
            }
            $plan['written'] = true;
            $run['state'] = 'writing';
            $this->saveRun($run);
        }
        unset($plan);
        foreach ($run['plans'] as $plan) {
            if ($plan['written'] && $this->hash($this->path($plan)) !== $plan['result_hash']) {
                $this->conflict('A written publication file changed before history could be recorded.');
            }
        }
        $events = $this->exporter->recordPreparedPublicationHistory($run['plans'], $run['id']);
        TranslationWorkbenchReview::create([
            'key_id' => $context['keyId'], 'finding_id' => $context['findingId'],
            'review_type' => 'publication', 'decision' => 'lang_files_published',
            'new_values' => ['files' => $this->summary($run)['files']],
            'meta' => ['run_id' => $run['id'], 'data_fingerprint' => $run['data_fingerprint'], 'events' => $events, 'initiated_by' => $run['actor_id']],
            'reviewed_at' => now(), 'reviewed_by_user_id' => Auth::id(),
        ]);
    }

    protected function replaceFile(string $path, string $contents, ?string $expectedHash): void
    {
        File::ensureDirectoryExists(dirname($path));
        $temp = tempnam(dirname($path), '.tw-publication-');
        if ($temp === false) {
            throw new \RuntimeException('Could not stage publication file.');
        }
        try {
            if (file_put_contents($temp, $contents) !== strlen($contents)) {
                throw new \RuntimeException('Could not write complete publication file.');
            }
            if (! chmod($temp, file_exists($path) ? fileperms($path) & 0777 : 0644)) {
                throw new \RuntimeException('Could not set publication file permissions.');
            }
            if ($this->hash($path) !== $expectedHash) {
                $this->conflict('A publication file changed before replacement.');
            }
            if (! rename($temp, $path)) {
                throw new \RuntimeException('Could not replace publication file.');
            }
            if (function_exists('opcache_invalidate')) {
                opcache_invalidate($path, true);
            }
        } finally {
            if (file_exists($temp)) { unlink($temp); }
        }
    }

    private function locked(callable $callback): array
    {
        app(TranslationWorkbenchEditorAccess::class)->authorize();
        if (DB::transactionLevel() !== 0) {
            throw new \LogicException('Publication must own its database transaction.');
        }
        File::ensureDirectoryExists($this->directory(), 0700);
        $lock = fopen($this->directory().'/publication.lock', 'c');
        if ($lock === false) { throw new \RuntimeException('Could not open publication lock.'); }
        try {
            if (! flock($lock, LOCK_EX)) { throw new \RuntimeException('Could not acquire publication lock.'); }

            return $callback();
        } finally {
            flock($lock, LOCK_UN);
            fclose($lock);
        }
    }

    private function lockDatabase(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('LOCK TABLE translation_workbench_source_files, translation_workbench_findings, translation_workbench_keys, translation_workbench_key_findings, translation_workbench_lang_values, translation_workbench_dynamic_key_values, translation_workbench_reviews IN SHARE ROW EXCLUSIVE MODE');
        }
    }

    private function assertPublishable(array $report): void
    {
        if ($report['pruning_requires_separate_review']) {
            $this->conflict('Publication blocked: this preview would remove published translations. Clearing a stored value does not authorize removal.');
        }
        if ($report['changes_without_current_approval'] || $report['values_conflicted'] || $report['values_excluded_by_legacy_review_filter']) {
            $this->conflict('Publication requires current value approvals, resolved conflicts and no unreviewed deletions or excluded options.');
        }
    }

    private function planHashes(array $report): array
    {
        return array_map(fn ($plan) => [$plan['locale'], $plan['namespace'], $plan['source_hash'], $plan['result_hash']], $report['plans']);
    }

    private function saveRun(array $run): void
    {
        $path = $this->directory().'/'.$run['id'].'.json';
        $temp = tempnam($this->directory(), '.journal-');
        if ($temp === false) { throw new \RuntimeException('Could not stage publication journal.'); }
        try {
            $contents = json_encode($run, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
            if (file_put_contents($temp, $contents) !== strlen($contents) || ! chmod($temp, 0600) || ! rename($temp, $path)) {
                throw new \RuntimeException('Could not persist publication journal.');
            }
        } finally {
            if (file_exists($temp)) { unlink($temp); }
        }
    }

    private function path(array $plan): string
    {
        if (! preg_match('/^[a-zA-Z0-9_-]+$/D', $plan['locale']) || ! preg_match('/^[a-zA-Z0-9_-]+$/D', $plan['namespace'])) {
            $this->conflict('Unsafe publication file scope.');
        }
        $path = lang_path($plan['locale'].'/'.$plan['namespace'].'.php');
        if (is_link(dirname($path)) || is_link($path)) {
            $this->conflict('Publication does not replace linked language paths.');
        }

        return $path;
    }

    private function assertNoPendingOverlap(array $plans): void
    {
        $paths = array_map(fn ($plan) => $this->path($plan), $plans);
        foreach (glob($this->directory().'/*.json') as $file) {
            $pending = json_decode(File::get($file), true, flags: JSON_THROW_ON_ERROR);
            if ($pending['state'] === 'complete' || $this->resolution($pending) !== null) { continue; }
            foreach ($pending['plans'] as $plan) {
                if (in_array($this->path($plan), $paths, true)) {
                    $this->conflict('Resume or resolve the interrupted publication run '.$pending['id'].' before starting an overlapping run.');
                }
            }
        }
    }

    private function hash(string $path): ?string
    {
        clearstatcache(true, $path);
        if (! file_exists($path)) { return null; }
        $hash = hash_file('sha256', $path);
        if ($hash === false) { throw new \RuntimeException('Could not hash publication file.'); }

        return $hash;
    }

    private function directory(): string
    {
        return storage_path('app/private/translation-workbench/publications');
    }

    private function summary(array $run): array
    {
        return ['id' => $run['id'], 'state' => $run['state'], 'files' => array_map(
            fn ($plan) => ['path' => $plan['path'], 'written' => $plan['written'], 'source_hash' => $plan['source_hash'], 'result_hash' => $plan['result_hash']],
            $run['plans'],
        )];
    }

    private function conflict(string $message): never
    {
        throw ValidationException::withMessages(['valueApproval' => __($message)]);
    }
}
