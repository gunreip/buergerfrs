<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('project:db-health --fail-on-empty --quiet-ok')
    ->hourly()
    ->withoutOverlapping();

Schedule::command('backup:run --only-db')
    ->dailyAt('03:10')
    ->withoutOverlapping();

Schedule::command('backup:clean')
    ->dailyAt('03:40')
    ->withoutOverlapping();
