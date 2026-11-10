<?php

namespace Gunreip\TranslationWorkbench\Console;

use Gunreip\TranslationWorkbench\Generation\ScanCacheMaintenance;
use Illuminate\Console\Command;

class PruneScanCache extends Command
{
    protected $signature = 'translation-workbench:prune-scan-cache';
    protected $description = 'Remove expired workbench scan cache entries only; preserve saved scans and finding history';

    public function handle(ScanCacheMaintenance $cache): int
    {
        if (!$cache->summary()['supported']) {
            $this->info('No database scan cache to prune for the configured cache store.');
            return self::SUCCESS;
        }
        $this->info('Expired scan cache entries removed: '.$cache->prune());
        return self::SUCCESS;
    }
}
