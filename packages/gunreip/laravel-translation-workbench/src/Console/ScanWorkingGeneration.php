<?php

namespace Gunreip\TranslationWorkbench\Console;

use Gunreip\TranslationWorkbench\Generation\CompleteRescanWriter;
use Gunreip\TranslationWorkbench\Generation\GenerationDatabase;
use Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchScanner;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Throwable;

class ScanWorkingGeneration extends Command
{
    protected $signature = 'translation-workbench:generation-scan {--apply : Persist the freshly checked scan in the working database}';

    protected $description = 'Scan configured source paths against the working database; default is read-only, never rewrites source or language files';

    public function handle(GenerationDatabase $database, TranslationWorkbenchScanner $scanner): int
    {
        try {
            $db = $database->connection();
            $identity = $database->verify($db);
            $this->line('Database: '.$db->query('SELECT current_database()')->fetchColumn().' · Schema: '.$identity['schema']);
            $this->line('Dataset: '.$identity['generation']);
            $this->info('Scanning configured source paths…');
            $writer = new CompleteRescanWriter($db, $scanner);
            $request = $writer->prepare((array) config('translation-workbench.paths', []), $database->languageEvidence($db));
            $counts = ['confirmed' => 0, 'created' => 0, 'moved' => 0, 'removed' => 0, 'review_files' => 0, 'outside_scope_files' => 0];
            foreach ($request['plan']['files'] as $file) {
                foreach (['confirmed', 'created', 'moved', 'removed'] as $key) {
                    $counts[$key] += count($file[$key] ?? []);
                }
                $counts['review_files'] += (int) in_array($file['state'], ['review', 'relocation_review'], true);
                $counts['outside_scope_files'] += (int) ($file['state'] === 'outside_scope');
            }
            $counts['usage_decisions'] = count($request['plan']['usage_decisions']);
            $this->table(['Result', 'Count'], collect($counts)->map(fn ($count, $key) => [$key, $count])->values()->all());
            if (! $this->option('apply')) {
                $this->info('Read-only scan complete. No database, source or language file was changed.');

                return self::SUCCESS;
            }
            $result = $writer->apply((string) Str::uuid(), $request);
            $this->info('Saved scan: '.$result['scan']);
            $this->info('Open Translation Workbench → Scan results and refresh the list to inspect this scan.');
            $this->warn('Files requiring review were preserved. Saved usage decisions are listed separately; consideration does not imply that translation work is complete.');

            return self::SUCCESS;
        } catch (Throwable $error) {
            report($error);
            $this->components->error($error->getMessage());

            return self::FAILURE;
        }
    }
}
