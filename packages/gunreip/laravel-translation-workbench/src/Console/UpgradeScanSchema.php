<?php

namespace Gunreip\TranslationWorkbench\Console;

use Gunreip\TranslationWorkbench\Generation\GenerationDatabase;
use Gunreip\TranslationWorkbench\Support\WorkingGenerationSchema;
use Illuminate\Console\Command;
use Throwable;

class UpgradeScanSchema extends Command
{
    protected $signature = 'translation-workbench:upgrade-scan-schema {--apply}';
    protected $description = 'Enable audited NULL SuggestedKey initialization; no finding values are changed';

    public function handle(GenerationDatabase $database): int
    {
        try {
            $db = $database->connection();
            $identity = $database->verify($db);
            $name = $db->query('SELECT current_database()')->fetchColumn();
            $this->line($name.' / '.$identity['schema'].' · version '.$identity['version']);
            if (!$this->option('apply')) { $this->info('Use --apply to enable audited initialization of missing SuggestedKeys.'); return self::SUCCESS; }
            $result = (new WorkingGenerationSchema)->upgrade($db, $name, $identity['schema'], $identity['generation']);
            $this->info('Schema version '.$result['version'].'. Finding values unchanged.');
            return self::SUCCESS;
        } catch (Throwable $error) { $this->error($error->getMessage()); return self::FAILURE; }
    }
}
