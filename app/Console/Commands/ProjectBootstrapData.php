<?php

// php artisan project:bootstrap-data
// php artisan project:bootstrap-data --with-test-users
// php artisan project:bootstrap-data --skip-migrate --skip-reference-import

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ProjectBootstrapData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'project:bootstrap-data
        {--skip-migrate : Skip running database migrations}
        {--skip-seed : Skip running DatabaseSeeder}
        {--skip-reference-import : Skip locale/country/language reference import}
        {--with-test-users : Also run TestUsersSeeder (adds generated test users)}
        {--without-subdivisions : Skip country subdivisions import}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bootstraps local data state: migrate, seed, import full locale reference data, then run a DB healthcheck.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (! $this->option('skip-migrate')) {
            if (! $this->runStep(
                'Migrationen ausfuehren',
                'migrate',
                ['--force' => true],
            )) {
                return self::FAILURE;
            }
        }

        if (! $this->option('skip-seed')) {
            if (! $this->seedBaselineData()) {
                return self::FAILURE;
            }
        }

        if (! $this->option('skip-reference-import')) {
            $importOptions = [
                '--with-country-meta' => true,
                '--with-addressing' => true,
            ];

            if (! $this->option('without-subdivisions')) {
                $importOptions['--with-subdivisions'] = true;
            }

            if (! $this->runStep(
                'Referenzdaten importieren',
                'reference:import-locale-data',
                $importOptions,
            )) {
                return self::FAILURE;
            }
        }

        if (! $this->runStep(
            'Datenbank-Healthcheck',
            'project:db-health',
            ['--fail-on-empty' => true],
        )) {
            return self::FAILURE;
        }

        $this->info('Projekt-Daten-Bootstrap abgeschlossen.');

        return self::SUCCESS;
    }

    /**
     * Seed baseline data using idempotent seeders and optional test users.
     */
    private function seedBaselineData(): bool
    {
        $seeders = [
            'CountrySeeder',
            'LanguageSeeder',
            'RolesAndPermissionsSeeder',
            'SuperAdminSeeder',
        ];

        if ($this->option('with-test-users')) {
            $seeders[] = 'TestUsersSeeder';
        }

        foreach ($seeders as $seeder) {
            if (! $this->runStep(
                'Seeder ausfuehren: ' . $seeder,
                'db:seed',
                ['--class' => $seeder, '--force' => true],
            )) {
                return false;
            }
        }

        return true;
    }

    /**
     * Run one artisan step and fail hard on non-zero return code.
     */
    private function runStep(string $description, string $command, array $arguments = []): bool
    {
        $this->info('-> ' . $description);

        $result = $this->call($command, $arguments);

        if ($result === self::SUCCESS) {
            return true;
        }

        $this->error('Schritt fehlgeschlagen: ' . $command);

        return false;
    }
}
