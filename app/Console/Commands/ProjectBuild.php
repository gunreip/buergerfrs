<?php

// app/Console/Commands/ProjectBuild.php

// php artisan project:build
// php artisan project:build --no-assets
// php artisan project:build --no-optimize

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Symfony\Component\Process\Process;
use Throwable;

/**
 * Executes the project maintenance and build pipeline.
 */
class ProjectBuild extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'project:build
        {--no-assets : Überspringt npm run build}
        {--no-optimize : Überspringt php artisan optimize}
        {--allow-empty-db : Erlaubt Build auch bei leeren Kern-Tabellen}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Führt alle wichtigen Build- und Wartungsbefehle für das Projekt aus.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        try {
            if (! $this->option('allow-empty-db') && ! $this->assertCriticalDataPresent()) {
                $this->logRunActivity('project.build.blocked_empty_db', 'Project build blocked due to missing or empty critical data.', [
                    'options' => [
                        'allow_empty_db' => (bool) $this->option('allow-empty-db'),
                    ],
                ]);

                return self::FAILURE;
            }

            $clearSteps = [
                ['desc' => 'Cache leeren', 'cmd' => 'cache:clear'],
                ['desc' => 'Config-Cache leeren', 'cmd' => 'config:clear'],
                ['desc' => 'Route-Cache leeren', 'cmd' => 'route:clear'],
                ['desc' => 'View-Cache leeren', 'cmd' => 'view:clear'],
                ['desc' => 'Event-Cache leeren', 'cmd' => 'event:clear'],
                ['desc' => 'Optimierungs-Cache leeren', 'cmd' => 'optimize:clear'],
                ['desc' => 'Settings-Cache leeren', 'cmd' => 'settings:clear-cache'],
                ['desc' => 'Settings-Discovery-Cache leeren', 'cmd' => 'settings:clear-discovered'],
                ['desc' => 'Settings entdecken', 'cmd' => 'settings:discover'],
            ];

            foreach ($clearSteps as $step) {
                $this->runArtisanStep($step['desc'], $step['cmd']);
            }

            $this->runArtisanStep('Lang-Locale-Verzeichnisse sicherstellen', 'translations:ensure-lang-directories');

            $this->runArtisanStep('Translation-Code-Audit schreiben', 'translations:audit-code');
            $this->runArtisanStep('Translation-Audits in Datenbank synchronisieren', 'translations:sync-audits');
            $this->runArtisanStep('Sub-Language-Redundanz prüfen', 'translations:audit-sub-language-redundancy');
            $this->runArtisanStep('Translation-Dateien nach lang exportieren', 'translations:export-lang-files');

            $this->runArtisanStep('Translation-Lang-Audit schreiben', 'translations:audit-lang');
            $this->runArtisanStep('Translation-Compare-Audit schreiben', 'translations:audit-compare');
            $this->runArtisanStep('Translation-Lang-Ballast-Audit schreiben', 'translations:audit-lang-ballast');
            $this->runArtisanStep('Translation-Lang-Ballast-Apply-Preview schreiben', 'translations:lang-ballast:apply');
            $this->runArtisanStep(
                'Translation-Duplicate-Usage-Literals-Audit schreiben',
                'translations:audit-duplicate-usage-literals',
            );
            $this->runArtisanStep(
                'Translation-Frequent-Usage-Literals-Audit schreiben',
                'translations:audit-frequent-usage-literals',
            );
            $this->runArtisanStep(
                'Translation-Usage-Decision-Preview schreiben',
                'translations:usage-decisions:preview',
            );

            $this->runArtisanStep('Blade-Component-Tag-Reference schreiben', 'views:sync-component-tags');
            $this->runArtisanStep('HTML-/Blade-View-Struktur-Audit schreiben', 'html:check');
            $this->runArtisanStep('HTML-/Blade-View-Struktur-Audit in Datenbank synchronisieren', 'html:sync-view-audit');
            $this->runArtisanStep('HTML-/Blade-View-Usage-Audit schreiben', 'html:check-view-html-used');
            $this->warn('⚠️ Hinweis: Native HTML reference gelegentlich mit php artisan html:sync-native-tags aktualisieren.');

            $this->runOptionalProcess(['./audit.cp.bat'], 'Translation-Audit-Previews nach /tmp kopieren');

            if (! $this->option('no-assets')) {
                $this->runProcess(['npm', 'run', 'build'], 'Frontend-Assets bauen');
            }

            if (! $this->option('no-optimize')) {
                $this->runArtisanStep('Optimieren der Klassen- und Service-Container', 'optimize');
            }

            $this->runArtisanStep('App-Version nach public/version.txt schreiben', 'app:write-app-version');

            $this->info('✅ Projekt-Build abgeschlossen!');

            $this->logRunActivity('project.build.completed', 'Project build command completed.', [
                'options' => [
                    'no_assets' => (bool) $this->option('no-assets'),
                    'no_optimize' => (bool) $this->option('no-optimize'),
                    'allow_empty_db' => (bool) $this->option('allow-empty-db'),
                ],
            ]);

            return self::SUCCESS;
        } catch (Throwable $exception) {
            $this->error('❌ Projekt-Build fehlgeschlagen: ' . trim($exception->getMessage()));

            $this->logRunActivity('project.build.failed', 'Project build command failed.', [
                'error' => trim($exception->getMessage()),
                'options' => [
                    'no_assets' => (bool) $this->option('no-assets'),
                    'no_optimize' => (bool) $this->option('no-optimize'),
                    'allow_empty_db' => (bool) $this->option('allow-empty-db'),
                ],
            ]);

            return self::FAILURE;
        }
    }

    /**
     * Block build when critical tables are empty to avoid silent "empty app" runs.
     */
    private function assertCriticalDataPresent(): bool
    {
        $criticalTables = ['users', 'countries', 'languages'];
        $emptyTables = [];

        try {
            foreach ($criticalTables as $table) {
                if (! Schema::hasTable($table)) {
                    $this->error('❌ Build abgebrochen: Tabelle fehlt: ' . $table);
                    $this->warn('ℹ️ Führe zuerst Migrationen aus (z. B. php artisan migrate --seed).');

                    return false;
                }

                if ((int) DB::table($table)->count('*') === 0) {
                    $emptyTables[] = $table;
                }
            }
        } catch (Throwable $exception) {
            $this->error('❌ Build abgebrochen: Datenbankprüfung fehlgeschlagen.');
            $this->warn(trim($exception->getMessage()));

            return false;
        }

        if ($emptyTables === []) {
            return true;
        }

        $this->error('❌ Build abgebrochen: Kritische Tabellen sind leer: ' . implode(', ', $emptyTables));
        $this->warn('ℹ️ Erwarteter Zustand? Dann mit --allow-empty-db starten.');
        $this->warn('ℹ️ Sonst Daten wiederherstellen/seeden (z. B. php artisan db:seed --class=DatabaseSeeder).');

        return false;
    }

    private function runArtisanStep(string $description, string $command): void
    {
        $this->info('➤ ' . $description);

        $exitCode = $this->call($command);

        if ($exitCode !== self::SUCCESS) {
            throw new RuntimeException('Step failed: ' . $command . ' (exit code ' . $exitCode . ')');
        }
    }

    private function runProcess(array $command, string $description): void
    {
        $this->info('➤ ' . $description);

        $process = new Process($command, base_path());
        $process->setTimeout(null);

        $process->run(function (string $type, string $buffer): void {
            $this->output->write($buffer);
        });

        if (! $process->isSuccessful()) {
            $this->error('❌ Fehler bei: ' . implode(' ', $command));

            throw new RuntimeException($process->getErrorOutput() ?: $process->getOutput());
        }
    }

    private function runOptionalProcess(array $command, string $description): void
    {
        $this->info('➤ ' . $description);

        $scriptPath = base_path($command[0]);

        if (! is_file($scriptPath)) {
            $this->warn('⚠️ Übersprungen, Datei nicht gefunden: ' . $command[0]);

            return;
        }

        if (! is_executable($scriptPath)) {
            $this->warn('⚠️ Übersprungen, Datei nicht ausführbar: ' . $command[0]);

            return;
        }

        $process = new Process($command, base_path());
        $process->setTimeout(null);

        $process->run(function (string $type, string $buffer): void {
            $this->output->write($buffer);
        });

        if (! $process->isSuccessful()) {
            $this->warn('⚠️ Fehler bei optionalem Schritt: ' . implode(' ', $command));
            $this->warn(trim($process->getErrorOutput() ?: $process->getOutput()));
        }
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
