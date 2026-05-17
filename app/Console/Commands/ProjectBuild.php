<?php

// php artisan project:build
// php artisan project:build --no-assets
// php artisan project:build --no-optimize

namespace App\Console\Commands;

use Illuminate\Console\Command;
use RuntimeException;
use Symfony\Component\Process\Process;

class ProjectBuild extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'project:build
        {--no-assets : Überspringt npm run build}
        {--no-optimize : Überspringt php artisan optimize}';

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

        $this->runArtisanStep('Translation-Code-Audit schreiben', 'translations:audit-code');
        $this->runArtisanStep('Translation-Lang-Audit schreiben', 'translations:audit-lang');
        $this->runArtisanStep('Translation-Compare-Audit schreiben', 'translations:audit-compare');
        $this->runArtisanStep('Translation-Audits in Datenbank synchronisieren', 'translations:sync-audits');

        $this->runArtisanStep('HTML-/Blade-View-Struktur-Audit schreiben', 'html:check');
        $this->warn('⚠️ Hinweis: Native HTML reference gelegentlich mit php artisan html:sync-native-tags aktualisieren.');

        $this->runOptionalProcess(['./audit.cp.bat'], 'Translation-Audit-Previews nach /tmp kopieren');

        if (! $this->option('no-assets')) {
            $this->runProcess(['npm', 'run', 'build'], 'Frontend-Assets bauen');
        }

        if (! $this->option('no-optimize')) {
            $this->runArtisanStep('Optimieren der Klassen- und Service-Container', 'optimize');
        }

        $this->info('✅ Projekt-Build abgeschlossen!');

        return self::SUCCESS;
    }

    private function runArtisanStep(string $description, string $command): void
    {
        $this->info('➤ ' . $description);

        $this->call($command);
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
}
