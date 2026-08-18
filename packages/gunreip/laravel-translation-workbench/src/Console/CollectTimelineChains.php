<?php

// packages/gunreip/laravel-translation-workbench/src/Console/CollectTimelineChains.php

// php artisan translation-workbench:collect-timeline-chains
// php artisan translation-workbench:collect-timeline-chains --dry-run
// php artisan translation-workbench:collect-timeline-chains --sync

namespace Gunreip\TranslationWorkbench\Console;

use Gunreip\TranslationWorkbench\Console\Concerns\WritesTranslationWorkbenchReports;
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchTimelineChainCollector;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('translation-workbench:collect-timeline-chains
    {--sync : Write the current timeline-chain snapshot into the database.}
    {--dry-run : Report only; do not write timeline-chain database rows.}')]
#[Description('Collect translation-chain context for a future extended timeline without changing existing timeline events.')]
class CollectTimelineChains extends Command
{
    use WritesTranslationWorkbenchReports;

    public function handle(TranslationWorkbenchTimelineChainCollector $collector): int
    {
        if (! $collector->hasRequiredTables()) {
            $this->error('Required Translation Workbench tables are missing. Run the workbench migrations first.');
            $this->writeTranslationWorkbenchReport(summary: ['error' => 'missing_required_tables']);

            return self::FAILURE;
        }

        $sync = (bool) $this->option('sync') && ! (bool) $this->option('dry-run');
        $result = $collector->collect(sync: $sync);
        $summary = $result['summary'];
        $rows = $result['rows'];

        $this->components->info('Translation Workbench timeline-chain collection finished.');
        $this->line('Chain rows: ' . number_format((int) $summary['chain_rows']));
        $this->line('Shared rows: ' . number_format((int) $summary['shared_rows']));
        $this->line('Bulk rows: ' . number_format((int) $summary['bulk_rows']));
        $this->line('Moved rows: ' . number_format((int) $summary['moved_rows']));

        if (! $sync) {
            $this->warn('Dry run/report only: no timeline-chain rows were written. Use --sync to update the snapshot table.');
        } else {
            $this->line('Stale marked: ' . number_format((int) $summary['stale_marked']));
        }

        $this->writeTranslationWorkbenchReport(
            summary: $summary,
            planSummary: [
                'rows' => $rows
                    ->take(250)
                    ->values()
                    ->all(),
            ],
        );

        return self::SUCCESS;
    }
}
