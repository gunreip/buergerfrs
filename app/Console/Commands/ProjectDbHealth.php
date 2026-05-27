<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

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

                if (in_array($table, $criticalTables, true) && $count === 0) {
                    $emptyCritical[] = $table;
                }
            }
        } catch (Throwable $exception) {
            $this->error('❌ Healthcheck fehlgeschlagen: ' . trim($exception->getMessage()));

            return self::FAILURE;
        }

        if ($emptyCritical !== []) {
            $this->warn('⚠️ Kritische Tabellen leer: ' . implode(', ', $emptyCritical));

            if ($this->option('fail-on-empty')) {
                return self::FAILURE;
            }
        }

        if (! $this->option('quiet-ok')) {
            $this->info('✅ Datenbank-Healthcheck abgeschlossen.');
        }

        return self::SUCCESS;
    }
}
