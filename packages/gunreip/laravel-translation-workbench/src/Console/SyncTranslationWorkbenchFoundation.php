<?php

// packages/gunreip/laravel-translation-workbench/src/Console/SyncTranslationWorkbenchFoundation.php

// php artisan translation-workbench:sync-foundation
// php artisan translation-workbench:sync-foundation --dry-run
// php artisan translation-workbench:sync-foundation --truncate-foundation
// php artisan translation-workbench:sync-foundation --truncate-foundation --force-truncate
// php artisan translation-workbench:sync-foundation --paths=resources/views/components
// php artisan translation-workbench:sync-foundation --mark-obsolete

namespace Gunreip\TranslationWorkbench\Console;

use Gunreip\TranslationWorkbench\Console\Concerns\ConfirmsTranslationWorkbenchTruncate;
use Gunreip\TranslationWorkbench\Console\Concerns\WritesTranslationWorkbenchReports;
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchFoundationSyncer;
use Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchScanner;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('translation-workbench:sync-foundation
    {--paths= : Comma-separated relative paths to scan. Defaults to config translation-workbench.paths.}
    {--dry-run : Scan and report only; do not write foundation database rows.}
    {--truncate-foundation : Truncate only the new foundation tables before writing.}
    {--force-truncate : Skip the interactive safety confirmation for --truncate-foundation.}
    {--mark-obsolete : Mark active foundation rows as obsolete when they are no longer seen in the latest scan.}')]
#[Description('Sync scanner findings into the new Translation Workbench foundation tables.')]
class SyncTranslationWorkbenchFoundation extends Command
{
    use ConfirmsTranslationWorkbenchTruncate;
    use WritesTranslationWorkbenchReports;

    public function handle(
        TranslationWorkbenchScanner $scanner,
        TranslationWorkbenchFoundationSyncer $syncer,
    ): int {
        $dryRun = (bool) $this->option('dry-run');
        $truncate = (bool) $this->option('truncate-foundation');
        $markObsolete = (bool) $this->option('mark-obsolete');

        if (! $this->confirmTranslationWorkbenchTruncate(
            $truncate,
            $dryRun,
            'The --truncate-foundation option will delete Translation Workbench foundation rows before syncing.',
            'force-truncate',
            [
                'scope' => 'foundation',
            ],
        )) {
            return self::FAILURE;
        }

        $items = $scanner->scan($this->paths());
        $summary = [
            'found' => $items->count(),
        ];

        if (! $dryRun) {
            $summary = $syncer->sync($items, $truncate, $markObsolete);
        }

        $this->components->info('Translation Workbench foundation sync finished.');
        $this->line('Items found: ' . number_format($summary['found']));

        if (! $dryRun) {
            $this->line('Source files created: ' . number_format($summary['source_files_created']));
            $this->line('Findings created: ' . number_format($summary['findings_created']));
            $this->line('Findings obsoleted: ' . number_format($summary['findings_obsoleted'] ?? 0));
            $this->line('Keys created: ' . number_format($summary['keys_created']));
            $this->line('Keys obsoleted: ' . number_format($summary['keys_obsoleted'] ?? 0));
            $this->line('Relations created: ' . number_format($summary['relations_created']));
            $this->line('Relations obsoleted: ' . number_format($summary['relations_obsoleted'] ?? 0));
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
