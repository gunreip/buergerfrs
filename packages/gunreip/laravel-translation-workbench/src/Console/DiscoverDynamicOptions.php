<?php

// packages/gunreip/laravel-translation-workbench/src/Console/DiscoverDynamicOptions.php

// php artisan translation-workbench:discover-dynamic-options
// php artisan translation-workbench:discover-dynamic-options --dry-run
// php artisan translation-workbench:discover-dynamic-options --sync
// php artisan translation-workbench:discover-dynamic-options --paths=resources/views/components

namespace Gunreip\TranslationWorkbench\Console;

use Gunreip\TranslationWorkbench\Console\Concerns\WritesTranslationWorkbenchReports;
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchDynamicOptionSynchronizer;
use Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchDynamicOptionCollector;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('translation-workbench:discover-dynamic-options
    {--paths= : Comma-separated relative view paths to scan. Defaults to translation-workbench paths.}
    {--sync : Store option observations and their Foundation assignment or review status.}
    {--dry-run : Report only; do not write database rows.}')]
#[Description('Discover possible dynamic multi option values from Blade foreach option lists and simple hard-coded component arrays.')]
class DiscoverDynamicOptions extends Command
{
    use WritesTranslationWorkbenchReports;

    public function handle(
        TranslationWorkbenchDynamicOptionCollector $collector,
        TranslationWorkbenchDynamicOptionSynchronizer $synchronizer,
    ): int {
        $sync = (bool) $this->option('sync') && ! (bool) $this->option('dry-run');
        $discoveries = $collector->collect($this->paths());
        $result = $synchronizer->process($discoveries, $sync);
        $foundationPlan = $result['plan'];
        $summary = $result['summary'];

        $this->components->info('Dynamic option discovery finished.');
        $this->line('Discoveries: '.number_format($summary['discoveries']));

        $this->line($sync ? 'Foundation observation assignment:' : 'Foundation assignment preview (read-only):');
        foreach ($foundationPlan['summary'] as $status => $count) {
            $this->line('  '.$status.': '.$count);
        }
        foreach ($foundationPlan['assignments'] as $row) {
            if ($row['status'] !== 'matched') {
                $this->warn($row['source_path'].':'.$row['source_line'].' — '.$row['status']
                    .' (findings: '.implode(', ', $row['candidate_finding_ids']).'; keys: '.implode(', ', $row['candidate_key_ids']).')');
            }
        }

        if ((bool) $this->option('dry-run')) {
            $this->warn('Dry run only: no database rows were written.');
        } elseif (! $sync) {
            $this->warn('No database rows were written. Use --sync to persist discovered options.');
        }

        /**
         * Shared raw-data report.
         *
         * The report structure is centralized in WritesTranslationWorkbenchReports.
         * Do not add command-specific raw_data fields here or change the report
         * contract silently; discuss report contract changes first.
         */
        $this->writeTranslationWorkbenchReport(summary: $summary, planSummary: ['foundation_assignment' => $foundationPlan]);

        return self::SUCCESS;
    }

    /**
     * @return array<int, string>
     */
    private function paths(): array
    {
        $paths = trim((string) $this->option('paths'));

        if ($paths !== '') {
            return collect(explode(',', $paths))
                ->map(static fn (string $path): string => trim($path))
                ->filter()
                ->values()
                ->all();
        }

        return collect((array) config('translation-workbench.paths', ['resources/views']))
            ->filter(static fn (mixed $path): bool => is_string($path) && trim($path) !== '')
            ->values()
            ->all();
    }
}
