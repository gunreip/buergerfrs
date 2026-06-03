<?php

// php artisan project:bootstrap-data
// php artisan project:bootstrap-data --with-test-users
// php artisan project:bootstrap-data --skip-migrate --skip-reference-import

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Throwable;

/**
 * Bootstraps project data state for local environments.
 */
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
                $this->logRunActivity('project.bootstrap_data.failed', 'Project bootstrap failed during migrate step.', [
                    'failed_step' => 'migrate',
                ]);

                return self::FAILURE;
            }
        }

        if (! $this->option('skip-seed')) {
            if (! $this->seedBaselineData()) {
                $this->logRunActivity('project.bootstrap_data.failed', 'Project bootstrap failed during seeding step.', [
                    'failed_step' => 'seed',
                ]);

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
                $this->logRunActivity('project.bootstrap_data.failed', 'Project bootstrap failed during reference import step.', [
                    'failed_step' => 'reference_import',
                ]);

                return self::FAILURE;
            }
        }

        if (! $this->runStep(
            'Datenbank-Healthcheck',
            'project:db-health',
            ['--fail-on-empty' => true],
        )) {
            $this->logRunActivity('project.bootstrap_data.failed', 'Project bootstrap failed during database health check step.', [
                'failed_step' => 'db_health',
            ]);

            return self::FAILURE;
        }

        $this->info('Projekt-Daten-Bootstrap abgeschlossen.');

        $this->logRunActivity('project.bootstrap_data.completed', 'Project bootstrap data command completed.', [
            'options' => [
                'skip_migrate' => (bool) $this->option('skip-migrate'),
                'skip_seed' => (bool) $this->option('skip-seed'),
                'skip_reference_import' => (bool) $this->option('skip-reference-import'),
                'with_test_users' => (bool) $this->option('with-test-users'),
                'without_subdivisions' => (bool) $this->option('without-subdivisions'),
            ],
        ]);

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

    private function logRunActivity(string $event, string $description, array $properties = []): void
    {
        try {
            activity('project')
                ->event($event)
                ->withProperties(array_merge([
                    'command' => $this->getName(),
                ], $properties))
                ->log($description);
        } catch (Throwable $exception) {
            $this->warn('Activity log write failed: ' . $exception->getMessage());
        }
    }
}
