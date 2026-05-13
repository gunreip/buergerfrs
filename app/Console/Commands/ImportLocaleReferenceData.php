<?php

// app/Console/Commands/ImportLocaleReferenceData.php

namespace App\Console\Commands;

use App\Support\Locale\LocaleReferenceImporter;
use Illuminate\Console\Command;

class ImportLocaleReferenceData extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'reference:import-locale-data
        {--dry-run : Run the import inside a rollback transaction}
        {--locales=de,en : Comma-separated display locales for localized names}
        {--with-country-meta : Import country metadata from database/reference/restcountries.v3.1*.json}
        {--with-addressing : Import country address metadata from commerceguys/addressing}
        {--with-subdivisions : Import country subdivisions from commerceguys/addressing}';

    /**
     * The console command description.
     */
    protected $description = 'Import and enrich locale reference data for countries, languages and locales.';

    /**
     * Execute the console command.
     */
    public function handle(LocaleReferenceImporter $importer): int
    {
        $displayLocales = collect(explode(',', (string) $this->option('locales')))
            ->map(fn(string $locale): string => trim($locale))
            ->filter()
            ->unique()
            ->values()
            ->all();

        $result = $importer->import(
            displayLocales: $displayLocales,
            dryRun: (bool) $this->option('dry-run'),
            withCountryMeta: (bool) $this->option('with-country-meta'),
            withAddressing: (bool) $this->option('with-addressing'),
            withSubdivisions: (bool) $this->option('with-subdivisions'),
        );

        $this->components->info('Locale reference data import finished.');

        $this->table(
            ['Area', 'Created', 'Updated', 'Skipped'],
            [
                ['Countries', $result['countries']['created'], $result['countries']['updated'], $result['countries']['skipped']],
                ['Country names', $result['country_names']['created'], $result['country_names']['updated'], $result['country_names']['skipped']],
                ['Country subdivisions', $result['country_subdivisions']['created'], $result['country_subdivisions']['updated'], $result['country_subdivisions']['skipped']],
                ['Languages', $result['languages']['created'], $result['languages']['updated'], $result['languages']['skipped']],
                ['Language names', $result['language_names']['created'], $result['language_names']['updated'], $result['language_names']['skipped']],
                ['Locales', $result['locales']['created'], $result['locales']['updated'], $result['locales']['skipped']],
            ],
        );

        if ((bool) $this->option('dry-run')) {
            $this->components->warn('Dry run only: all database changes were rolled back.');
        }

        return self::SUCCESS;
    }
}
