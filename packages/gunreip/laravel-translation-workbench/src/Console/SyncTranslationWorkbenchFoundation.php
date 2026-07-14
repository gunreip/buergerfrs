<?php

namespace Gunreip\TranslationWorkbench\Console;

use Gunreip\TranslationWorkbench\Console\Concerns\WritesTranslationWorkbenchReports;
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchFoundationSyncer;
use Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchScanner;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('translation-workbench:sync-foundation
    {--paths= : Comma-separated relative paths to scan. Defaults to config translation-workbench.paths.}
    {--dry-run : Scan and report only; do not write foundation database rows.}
    {--truncate-foundation : Truncate only the new foundation tables before writing.}')]
#[Description('Sync scanner findings into the new Translation Workbench foundation tables.')]
class SyncTranslationWorkbenchFoundation extends Command
{
    use WritesTranslationWorkbenchReports;

    public function handle(
        TranslationWorkbenchScanner $scanner,
        TranslationWorkbenchFoundationSyncer $syncer,
    ): int {
        $items = $scanner->scan($this->paths());
        $dryRun = (bool) $this->option('dry-run');
        $truncate = (bool) $this->option('truncate-foundation');
        $summary = [
            'found' => $items->count(),
        ];

        if (! $dryRun) {
            $summary = $syncer->sync($items, $truncate);
        }

        $this->components->info('Translation Workbench foundation sync finished.');
        $this->line('Items found: ' . number_format($summary['found']));

        if (! $dryRun) {
            $this->line('Source files created: ' . number_format($summary['source_files_created']));
            $this->line('Findings created: ' . number_format($summary['findings_created']));
            $this->line('Keys created: ' . number_format($summary['keys_created']));
            $this->line('Relations created: ' . number_format($summary['relations_created']));
            $this->line('Timeline events created: ' . number_format($summary['timeline_events_created']));
        }

        if ($dryRun) {
            $this->warn('Dry run only: no foundation database rows were written.');

            if ($truncate) {
                $this->warn('The --truncate-foundation option was ignored because --dry-run is active.');
            }
        }

        /**
         * Shared raw-data report.
         *
         * The report structure is centralized in WritesTranslationWorkbenchReports.
         * Do not add command-specific raw_data fields here or change the report
         * contract silently; discuss report contract changes first.
         */
        $this->writeTranslationWorkbenchReport();

        return self::SUCCESS;
    }

    /**
     * @return array<int, string>|null
     */
    private function paths(): ?array
    {
        $paths = trim((string) $this->option('paths'));

        if ($paths === '') {
            return null;
        }

        return collect(explode(',', $paths))
            ->map(static fn(string $path): string => trim($path))
            ->filter()
            ->values()
            ->all();
    }
}
