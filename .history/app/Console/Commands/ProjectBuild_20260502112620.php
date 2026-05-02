<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ProjectBuild extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'project:build';

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
        $steps = [
            ['desc' => 'Cache leeren', 'cmd' => 'cache:clear'],
            ['desc' => 'Config-Cache leeren', 'cmd' => 'config:clear'],
            ['desc' => 'Route-Cache leeren', 'cmd' => 'route:clear'],
            ['desc' => 'View-Cache leeren', 'cmd' => 'view:clear'],
            ['desc' => 'Event-Cache leeren', 'cmd' => 'event:clear'],
            ['desc' => 'Optimieren der Klassen- und Service-Container', 'cmd' => 'optimize:clear'],
            ['desc' => 'Optimieren der Klassen- und Service-Container', 'cmd' => 'optimize'],
            ['desc' => 'Einstellungen-Cache leeren', 'cmd' => 'settings:clear-cache'],
            ['desc' => 'Einstellungen entdecken', 'cmd' => 'settings:discover'],
            optimize:clear
            // Weitere Schritte können hier ergänzt werden
        ];

        foreach ($steps as $step) {
            $this->info('➤ ' . $step['desc']);
            $this->call($step['cmd']);
        }

        $this->info('✅ Projekt-Build abgeschlossen!');
        return self::SUCCESS;
    }
}
