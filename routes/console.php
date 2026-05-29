<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Stündlicher Healthcheck der Kern-Datenbanktabellen.
Schedule::command('project:db-health --fail-on-empty --quiet-ok')
    ->hourly()
    ->withoutOverlapping();

// DB-Backup alle 10 Minuten inkl. projektspezifischer Aufbewahrungslogik.
Schedule::command('project:db-backup')
    ->everyTenMinutes()
    ->withoutOverlapping();
