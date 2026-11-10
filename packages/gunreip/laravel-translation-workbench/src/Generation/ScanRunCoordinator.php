<?php

namespace Gunreip\TranslationWorkbench\Generation;

use Gunreip\TranslationWorkbench\Jobs\RunWorkbenchScan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use RuntimeException;

class ScanRunCoordinator
{
    public function start(int $actor): string
    {
        if ($actor < 1) throw new RuntimeException('A user ID is required to start a scan.');
        if (in_array(config('cache.default'), ['array', 'null'], true)) throw new RuntimeException('Background scans require a shared persistent cache.');
        $target = app(WorkingScanService::class)->target();
        $id = (string) Str::uuid();
        $record = ['id' => $id, 'actor' => $actor, 'target' => $target, 'state' => 'queued_preview', 'updated' => time()];
        $this->store($id, $record);
        try { RunWorkbenchScan::dispatch($id, false)->onConnection('background'); }
        catch (\Throwable $error) { Cache::forget($this->key($id)); throw $error; }
        return $id;
    }

    public function confirm(string $id, int $actor): void
    {
        Cache::lock($this->key($id).':dispatch', 15)->block(5, function () use ($id, $actor) {
            $record = $this->owned($id, $actor);
            if (!in_array($record['state'], ['preview_ready', 'changed'], true)) throw new RuntimeException('Open a completed scan preview first.');
            Cache::forget($this->key($id).':progress');
            $record['state'] = 'queued_apply';
            $record['updated'] = time();
            $this->store($id, $record);
            try { RunWorkbenchScan::dispatch($id, true)->onConnection('background'); }
            catch (\Throwable $error) { $record['state'] = 'failed'; $record['error'] = $error->getMessage(); $this->store($id, $record); throw $error; }
        });
    }

    public function status(string $id, int $actor): array
    {
        $record = $this->owned($id, $actor);
        $record['progress'] = Cache::get($this->key($id).':progress', ['steps' => []]);
        if (in_array($record['state'], ['queued_preview', 'scanning', 'queued_apply', 'applying'], true) && time() - max($record['updated'] ?? time(), $record['progress']['updated'] ?? 0) > 900) {
            $record['state'] = 'failed';
            $record['error'] = 'No scan progress has been reported for 15 minutes. Check Scan results before starting a new preview; an interrupted status update does not prove that saving failed.';
        }
        unset($record['preview']['request']);
        return $record;
    }

    private function owned(string $id, int $actor): array
    {
        if (!Str::isUuid($id)) throw new RuntimeException('Invalid scan ID.');
        $record = Cache::get($this->key($id));
        if (!$record || $record['actor'] !== $actor) throw new RuntimeException('This scan preview has expired or belongs to another user.');
        return $record;
    }

    /** Keep the web-facing status small; only the worker needs the full executable plan. */
    public function store(string $id, array $record): void
    {
        if (isset($record['preview']['request'])) {
            $payload = gzencode(json_encode($record['preview']['request'], JSON_THROW_ON_ERROR));
            if ($payload === false) throw new RuntimeException('Could not encode the scan plan.');
            Cache::put($this->key($id).':plan', $payload, 7200);
            unset($record['preview']['request']);
            $record['preview']['plan_stored'] = true;
        }
        if (isset($record['result'])) {
            $record['result'] = ['scan' => $record['result']['scan']];
        }
        Cache::put($this->key($id), $record, 7200);
    }

    public function previewForWorker(string $id, array $record): array
    {
        $preview = $record['preview'];
        if (!empty($preview['plan_stored'])) {
            $payload = Cache::get($this->key($id).':plan');
            if (!is_string($payload) || ($json = gzdecode($payload)) === false) {
                throw new RuntimeException('The scan plan has expired. Start a new preview.');
            }
            $preview['request'] = json_decode($json, true, flags: JSON_THROW_ON_ERROR);
            unset($preview['plan_stored']);
        }
        return $preview;
    }

    public function key(string $id): string { return 'workbench-ui-scan:'.$id; }
}
