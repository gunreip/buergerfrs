<?php

namespace Gunreip\TranslationWorkbench\Console;

use Illuminate\Console\Command;
use Throwable;

class ScanWorkingGeneration extends Command
{
    protected $signature = 'translation-workbench:generation-scan {--apply : Persist the freshly checked scan in the working database} {--actor= : User ID for the scan audit}';

    protected $description = 'Scan configured source paths against the working database; default is read-only, never rewrites source or language files';

    public function handle(\Gunreip\TranslationWorkbench\Generation\WorkingScanService $service): int
    {
        try {
            $preview = $service->preview();
            $this->line('Database: '.$preview['target']['database'].' · Schema: '.$preview['target']['schema']);
            $this->table(['Result', 'Count'], collect($preview['counts'])->map(fn ($count, $key) => [$key, $count])->values()->all());
            if (!$this->option('apply')) { $this->info('Read-only scan complete. No findings or source files changed.'); return self::SUCCESS; }
            $actor = filter_var($this->option('actor'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
            if (!$actor) throw new \RuntimeException('Use --actor=<user ID> to record who started this scan.');
            $result = $service->apply($preview, $actor);
            if ($result['state'] !== 'complete') throw new \RuntimeException('Source or database changed during scanning. Review a fresh preview before applying.');
            $this->info('Saved scan: '.$result['result']['scan']);
            $this->info('Open Translation Workbench → Scan results to inspect this scan. Files requiring review were preserved.');
            return self::SUCCESS;
        } catch (Throwable $error) { report($error); $this->components->error($error->getMessage()); return self::FAILURE; }
    }
}
