<?php

// Existing repair service, restricted to seven independently matched moved findings.
require dirname(__DIR__, 3).'/vendor/autoload.php';
$app = require dirname(__DIR__, 3).'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchSourceRepair;
use Illuminate\Support\Facades\DB;

$ids = [9010, 9018, 5407, 5409, 5415, 5416, 5424];
$paths = [
    'packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/findings/table-work-filters.blade.php',
    'packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/findings/table-work-findings/header.blade.php',
];
try {
    if ($argc === 1) {
        $result = DB::transaction(function () use ($ids, $paths) {
            DB::statement('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ READ ONLY');
            $plan = app(TranslationWorkbenchSourceRepair::class)->plan($paths);
            $plan['actions'] = array_values(array_filter($plan['actions'], fn ($a) => $a['type'] === 'move' && in_array($a['id'], $ids, true)));
            return $plan;
        });
    } else {
        if ($argc !== 3 || $argv[1] !== '--apply') { throw new InvalidArgumentException('Use --apply PLAN.json.'); }
        $plan = json_decode(file_get_contents($argv[2]), true, 512, JSON_THROW_ON_ERROR);
        if ($plan['paths'] !== $paths || count($plan['actions']) !== 7) { throw new RuntimeException('Unexpected scope.'); }
        foreach ($plan['actions'] as $action) {
            if ($action['type'] !== 'move' || ! in_array($action['id'], $ids, true)) { throw new RuntimeException('Unexpected action.'); }
        }
        $result = app(TranslationWorkbenchSourceRepair::class)->apply($plan);
    }
    echo json_encode($result, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR).PHP_EOL;
} catch (Throwable $e) { fwrite(STDERR, $e->getMessage().PHP_EOL); exit(1); }
