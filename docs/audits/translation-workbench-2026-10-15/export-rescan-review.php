<?php

use Tests\Support\TranslationWorkbench\OptionListReviewBinding;

// Explicit read-only publication of an isolated, completed rescan plan for the UI.
$root = dirname(__DIR__, 3);
require $root.'/vendor/autoload.php';
$inventoryPath = __DIR__.'/option-producer-source-inventory-2026-10-21.json';
$bindings = require __DIR__.'/option-list-review-bindings.php';
$binder = new OptionListReviewBinding;
$reportPath = realpath($argv[1] ?? '');
if (! $reportPath || ! str_starts_with($reportPath, $root.'/storage/app/private/dev/workbench-rehearsal/') || basename($reportPath) !== 'result.json') {
    throw new RuntimeException('Supply a private completed rehearsal result.json');
}
$read = static fn ($path) => json_decode(file_get_contents($path), true, flags: JSON_THROW_ON_ERROR);
$report = $read($reportPath);
$baseline = $read($root.'/storage/app/private/workbench-previews/current-generation.json');
$planPath = dirname($reportPath).'/full-plan.json';
if ($report['state'] !== 'complete_rescan_plan_verified' || $report['production_connected'] !== false
    || $report['database_unchanged'] !== true || $report['generation'] !== $baseline['generation']
    || hash_file('sha256', $planPath) !== ($report['full_plan_sha256'] ?? null)) {
    throw new RuntimeException('Rescan plan is not verified against the current isolated generation');
}
$plan = $read($planPath);
$inventory = $read($inventoryPath);
$readSource = static fn ($path) => @file_get_contents($root.'/'.$path);
$run = gmdate('Ymd-His').'-'.bin2hex(random_bytes(6));
$base = $root.'/storage/app/private/workbench-previews';
$directory = $base.'/rescan-review-'.$run;
mkdir($directory, 0755);
chmod($directory, 0755);
$write = static function ($path, array $value): string {
    $bytes = json_encode($value, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);
    file_put_contents($path, $bytes);
    chmod($path, 0644);

    return hash('sha256', $bytes);
};
$files = [];
foreach ($plan['files'] as $path => $file) {
    if (! in_array($file['state'], ['review', 'relocation_review'], true)) {
        continue;
    }
    $id = hash('sha256', $path);
    $detail = ['id' => $id, 'path' => $path, 'state' => $file['state'], 'issues' => $file['issues'] ?? [],
        'preserved' => $file['preserved'] ?? [], 'captures' => $file['captures'] ?? [],
        'relocations' => array_values(array_filter($plan['relocations'], fn ($r) => $r['source'] === $path || in_array($path, array_column($r['candidates'], 'path'), true)))];
    foreach ($detail['captures'] as &$capture) {
        if (isset($capture['option_source'])) {
            $capture['option_list_review'] = $binder->bind($capture, $inventory, $bindings, $readSource);
        }
    }
    unset($capture);
    $files[] = ['id' => $id, 'path' => $path, 'state' => $file['state'], 'issues' => count($detail['issues']),
        'preserved' => count($detail['preserved']), 'sha256' => $write($directory.'/'.$id.'.json', $detail)];
}
$manifest = ['mode' => 'isolated_rescan_review', 'run' => $run, 'exported_at' => gmdate(DATE_ATOM),
    'source_run' => $baseline['source_run'], 'generation' => $baseline['generation']['generation'],
    'production_connected' => false, 'workbench_switched' => false, 'summary' => $report['full_scan'],
    'plan_sha256' => $report['full_plan_sha256'], 'report_sha256' => hash_file('sha256', $reportPath), 'files' => $files];
$manifest['option_inventory_sha256'] = hash_file('sha256', $inventoryPath);
$manifest['option_bindings_sha256'] = hash_file('sha256', __DIR__.'/option-list-review-bindings.php');
$hash = $write($directory.'/manifest.json', $manifest);
$temp = $base.'/rescan-review-'.$run.'.tmp';
$write($temp, ['run' => $run, 'sha256' => $hash]);
rename($temp, $base.'/rescan-review.json');
echo json_encode(['run' => $run, 'review_files' => count($files), 'production_connected' => false], JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR)."\n";
