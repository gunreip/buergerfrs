<?php

// app/Console/Commands/ProjectDbHealth.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

/**
 * Performs health checks for critical database tables.
 */
class ProjectDbHealth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'project:db-health
        {--fail-on-empty : Exit with non-zero status when critical tables are empty}
        {--quiet-ok : Skip the success summary output}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prüft den Datenbank-Zustand für Kern-Tabellen und meldet leere Datensätze.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $criticalTables = ['users', 'countries', 'languages'];
        $optionalTables = ['locales'];
        $tableCounts = [];

        $this->line('Datenbank-Healthcheck für Verbindung: ' . (string) config('database.default'));
        $this->line('Ziel-DB: ' . (string) config('database.connections.' . config('database.default') . '.database'));

        $emptyCritical = [];

        try {
            foreach (array_merge($criticalTables, $optionalTables) as $table) {
                if (! Schema::hasTable($table)) {
                    $this->warn('⚠️ Tabelle fehlt: ' . $table);

                    continue;
                }

                $count = (int) DB::table($table)->count('*');
                $this->line(sprintf('- %s: %d', $table, $count));
                $tableCounts[$table] = $count;

                if (in_array($table, $criticalTables, true) && $count === 0) {
                    $emptyCritical[] = $table;
                }
            }
        } catch (Throwable $exception) {
            $this->error('❌ Healthcheck fehlgeschlagen: ' . trim($exception->getMessage()));

            $this->logRunActivity('project.db_health.failed', 'Database health check failed.', [
                'error' => trim($exception->getMessage()),
                'table_counts' => $tableCounts,
            ]);

            return self::FAILURE;
        }

        if ($emptyCritical !== []) {
            $this->warn('⚠️ Kritische Tabellen leer: ' . implode(', ', $emptyCritical));

            if ($this->option('fail-on-empty')) {
                $this->logRunActivity('project.db_health.failed_empty', 'Database health check failed because critical tables are empty.', [
                    'empty_critical_tables' => $emptyCritical,
                    'table_counts' => $tableCounts,
                    'options' => [
                        'fail_on_empty' => (bool) $this->option('fail-on-empty'),
                        'quiet_ok' => (bool) $this->option('quiet-ok'),
                    ],
                ]);

                return self::FAILURE;
            }
        }

        if (! $this->option('quiet-ok')) {
            $this->info('✅ Datenbank-Healthcheck abgeschlossen.');
        }

        $this->logRunActivity('project.db_health.completed', 'Database health check completed.', [
            'empty_critical_tables' => $emptyCritical,
            'table_counts' => $tableCounts,
            'options' => [
                'fail_on_empty' => (bool) $this->option('fail-on-empty'),
                'quiet_ok' => (bool) $this->option('quiet-ok'),
            ],
        ]);

        return self::SUCCESS;
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
