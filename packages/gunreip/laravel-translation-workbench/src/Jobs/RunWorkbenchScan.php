<?php

namespace Gunreip\TranslationWorkbench\Jobs;

use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchEditorAccess;
use Gunreip\TranslationWorkbench\Generation\ScanRunCoordinator;
use Gunreip\TranslationWorkbench\Generation\ScanProgressSteps;
use Gunreip\TranslationWorkbench\Generation\WorkingScanService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Throwable;

class RunWorkbenchScan implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $run, public bool $apply) {}

    public function handle(WorkingScanService $service, ScanRunCoordinator $coordinator): void
    {
        $key = $coordinator->key($this->run);
        $lock = Cache::lock($key.':worker', 3600);
        if (!$lock->get()) return;
        try {
            $record = Cache::get($key);
            if (!$record || $record['state'] !== ($this->apply ? 'queued_apply' : 'queued_preview')) return;
            $steps = new ScanProgressSteps;
            try {
                Auth::onceUsingId($record['actor']);
                app(TranslationWorkbenchEditorAccess::class)->authorize();
                if ($record['target'] !== $service->target()) throw new \RuntimeException('The scan target changed. Start a new preview.');
                $record['state'] = $this->apply ? 'applying' : 'scanning';
                $record['updated'] = time();
                $coordinator->store($this->run, $record);
                $lastProgress = 0.0;
                $lastPhase = '';
                $progress = function (string $phase, int $done, int $total) use ($key, $steps, &$lastProgress, &$lastPhase) {
                    $currentSteps = $steps->advance($phase, $done, $total);
                    $now = microtime(true);
                    if ($phase !== $lastPhase || $done === $total || $now - $lastProgress >= 0.25) {
                        Cache::put($key.':progress', ['steps' => $currentSteps, 'updated' => time()], 7200);
                        $lastProgress = $now;
                        $lastPhase = $phase;
                    }
                };
                Cache::forget($key.':progress');
                if ($this->apply) {
                    $result = $service->apply($coordinator->previewForWorker($this->run, $record), $record['actor'], $progress);
                    $record['state'] = $result['state'];
                    if (isset($result['preview'])) $record['preview'] = $result['preview'];
                    if (isset($result['result'])) $record['result'] = $result['result'];
                } else {
                    $record['preview'] = $service->preview($progress);
                    $record['state'] = 'preview_ready';
                }
            } catch (Throwable $error) {
                report($error);
                $record['state'] = 'failed';
                $record['error'] = $error->getMessage();
            }
            Cache::put($key.':progress', ['steps' => $steps->finish($record['state'] !== 'failed'), 'updated' => time()], 7200);
            $record['updated'] = time();
            $coordinator->store($this->run, $record);
        } finally { $lock->release(); }
    }
}
