<?php

namespace Gunreip\TranslationWorkbench\Console;

use Gunreip\TranslationWorkbench\Console\Concerns\WritesTranslationWorkbenchReports;
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchLangValueImporter;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('translation-workbench:import-lang-values
    {--source-locale=en : Source locale directory below lang/ to import.}
    {--all-locales : Import all locale directories below lang/, excluding lang/vendor.}
    {--dry-run : Read and report only; do not write lang value rows.}
    {--truncate-lang-values : Truncate imported lang value rows before importing.}')]
#[Description('Import existing lang file values into the separate Translation Workbench lang values table.')]
class ImportTranslationWorkbenchLangValues extends Command
{
    use WritesTranslationWorkbenchReports;

    public function handle(TranslationWorkbenchLangValueImporter $importer): int
    {
        $locales = $this->locales($importer);
        $dryRun = (bool) $this->option('dry-run');
        $truncate = (bool) $this->option('truncate-lang-values');
        $values = collect($locales)
            ->flatMap(fn(string $locale): array => $importer->readLangValues($locale))
            ->values()
            ->all();
        $summary = [
            'locales' => count($locales),
            'files' => collect($values)->pluck('source_path')->unique()->count(),
            'values_found' => count($values),
        ];

        if (! $dryRun) {
            $summary = $importer->importLocales($locales, $truncate);
        }

        $this->components->info('Translation Workbench lang value import finished.');
        $this->line('Locales: ' . implode(', ', $locales));
        $this->line('Lang files: ' . number_format($summary['files']));
        $this->line('Values found: ' . number_format($summary['values_found']));

        if (! $dryRun) {
            $this->line('Values created: ' . number_format($summary['values_created']));
            $this->line('Values updated: ' . number_format($summary['values_updated']));
            $this->line('Values unchanged: ' . number_format($summary['values_unchanged']));
            $this->line('Values obsoleted: ' . number_format($summary['values_obsoleted']));
            $this->line('Timeline events created: ' . number_format($summary['timeline_events_created']));
        }

        if ($dryRun) {
            $this->warn('Dry run only: no lang value rows were written.');

            if ($truncate) {
                $this->warn('The --truncate-lang-values option was ignored because --dry-run is active.');
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
     * @return array<int, string>
     */
    private function locales(TranslationWorkbenchLangValueImporter $importer): array
    {
        if ((bool) $this->option('all-locales')) {
            return $importer->availableLocales();
        }

        $sourceLocale = trim((string) $this->option('source-locale'));

        return [$sourceLocale !== '' ? $sourceLocale : 'en'];
    }
}
