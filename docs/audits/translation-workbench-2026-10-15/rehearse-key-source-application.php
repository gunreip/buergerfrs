<?php

use Gunreip\TranslationWorkbench\Generation\CopiedSourceCapture;
use Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchScanner;
use Gunreip\TranslationWorkbench\Support\SourceKeyChangePreview;
use Illuminate\Config\Repository;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Facade;

$root = dirname(__DIR__, 3);
require $root.'/vendor/autoload.php';
umask(0077);
$read = static fn ($path) => json_decode(file_get_contents($path), true, flags: JSON_THROW_ON_ERROR);
$backupResult = $read(__DIR__.'/backup-restore-result-2026-10-19.json');
$manifestPath = $root.'/'.$backupResult['backup_manifest'];
if (hash_file('sha256', $manifestPath) !== $backupResult['backup_manifest_sha256']) {
    throw new RuntimeException('Backup manifest changed');
}
$manifest = $read($manifestPath);
$sourceRoot = realpath(dirname($manifestPath).'/source');
$snapshot = $read($root.'/storage/app/private/workbench-previews/reader-preview.json');
$baseline = $read(__DIR__.'/published-values-2026-10-19.json');
if ($snapshot['input_dump_sha256'] !== $baseline['dump_sha256'] || ! $sourceRoot) {
    throw new RuntimeException('Snapshot or backup mismatch');
}
$groups = [];
foreach ($snapshot['items']['ui.type']['blocked_sources'] as $finding) {
    $groups[$finding['path']][] = ['id' => $finding['finding_id'], 'source_line' => $finding['line'],
        'source_column' => $finding['column'], 'raw_expression' => $finding['expression']];
}
$work = dirname($manifestPath).'/source-apply-'.bin2hex(random_bytes(6));
mkdir($work, 0700);
$app = new Application($work);
$app->instance('config', new Repository(['translation-workbench' => ['file_patterns' => ['*.php', '*.blade.php']]]));
$app->instance('files', new Filesystem);
Facade::setFacadeApplication($app);
$scanner = $app->make(TranslationWorkbenchScanner::class);
$beforeAdapter = new CopiedSourceCapture;
$afterAdapter = new CopiedSourceCapture;
$files = [];
$checks = 0;
$verified = 0;
$transitions = [];
$movements = [];
$check = static function (bool $ok, string $message) use (&$checks): void {
    if (! $ok) {
        throw new RuntimeException($message);
    }
    $checks++;
};
foreach ($groups as $path => $findings) {
    $copy = realpath($sourceRoot.'/'.$path);
    if (! $copy || ! str_starts_with($copy, $sourceRoot.'/')) {
        throw new RuntimeException('Source outside verified backup');
    }
    $hash = $manifest['source_files'][$path]['sha256'];
    $bytes = file_get_contents($copy);
    $preview = (new SourceKeyChangePreview)->preview($bytes, $hash, $path, $findings, 'ui.type', 'rehearsal.correction.type');
    $destination = $work.'/'.$path;
    if (! is_dir(dirname($destination))) {
        mkdir(dirname($destination), 0700, true);
    }
    file_put_contents($destination, $bytes);
    $before = $scanner->scanFile($destination)->all();
    $check(hash_file('sha256', $destination) === $preview['before_sha256'], 'Working source changed before application');
    $temporary = tempnam(dirname($destination), '.rewrite-');
    try {
        if (file_put_contents($temporary, $preview['proposed_bytes']) !== strlen($preview['proposed_bytes']) || ! rename($temporary, $destination)) {
            throw new RuntimeException('Could not write working copy');
        }
    } finally {
        if (is_file($temporary)) {
            unlink($temporary);
        }
    }
    $afterBytes = file_get_contents($destination);
    $check(hash('sha256', $afterBytes) === $preview['after_sha256'], 'Applied bytes differ from preview');
    $after = [];
    foreach ($scanner->scanFile($destination) as $item) {
        $after[$item->meta['source_offset']][] = $item;
    }
    foreach ($before as $item) {
        $offset = $item->meta['source_offset'];
        $shift = 0;
        foreach ($preview['edits'] as $edit) {
            if ($edit['offset'] < $offset) {
                $shift += strlen($edit['after']) - $edit['length'];
            }
        }
        $newOffset = $offset + $shift;
        $candidate = isset($after[$newOffset]) ? array_shift($after[$newOffset]) : null;
        $check($candidate !== null && $candidate->kind === $item->kind && $candidate->functionName === $item->functionName, 'Rescan lost or changed a call');
        $old = $beforeAdapter->capture($item, $bytes, $hash, []);
        $new = $afterAdapter->capture($candidate, $afterBytes, $preview['after_sha256'], []);
        $matched = array_values(array_filter($findings, fn ($f) => (int) $f['source_line'] === $item->sourceLine && (int) $f['source_column'] === $item->sourceColumn));
        if ($matched !== []) {
            $check(count($matched) === 1 && $new['found_translation_key'] === 'rehearsal.correction.type'
                && $new['source_context']['state'] === 'confirmed_call', 'Corrected finding did not rescan to target key');
            $transitions[] = ['predecessor' => $matched[0]['id'], 'before_expression' => $old['raw_expression'], 'capture' => $new];
            $verified++;
        } else {
            $check($old['raw_expression'] === $new['raw_expression'] && $old['raw_source_code_literal'] === $new['raw_source_code_literal'], 'Unselected call changed');
            if ($old['source_line'] !== $new['source_line'] || $old['source_column'] !== $new['source_column']) {
                $movements[] = ['before' => $old, 'after' => $new];
            }
        }
    }
    $check(array_sum(array_map('count', $after)) === 0, 'Rescan created unexpected calls');
    $check(hash_file('sha256', $copy) === $hash, 'Saved source was modified');
    unset($preview['proposed_bytes']);
    $files[] = $preview;
}
$expected = array_sum(array_map('count', $groups));
$check($verified === $expected, 'Not all selected findings verified');
$report = ['recorded_at' => gmdate('c'), 'mode' => 'source_changes_applied_to_separate_copy',
    'database_connected' => false, 'production_source_written' => false, 'saved_source_unchanged' => true,
    'work_directory' => substr($work, strlen($root) + 1), 'files' => count($files), 'verified_findings' => $verified, 'position_only_movements' => count($movements), 'assertions' => $checks];
file_put_contents($work.'/movements.json', json_encode($movements, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR)."\n");
$report['movements_sha256'] = hash_file('sha256', $work.'/movements.json');
file_put_contents($work.'/transitions.json', json_encode($transitions, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR)."\n");
$report['transitions_sha256'] = hash_file('sha256', $work.'/transitions.json');
file_put_contents($work.'/changes.json', json_encode($files, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR)."\n");
file_put_contents(__DIR__.'/source-key-application-2026-10-20.json', json_encode($report, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR)."\n");
$uiData = ['schema' => 1, 'mode' => 'isolated_source_change_preview', 'recorded_at' => $report['recorded_at'],
    'input_dump_sha256' => $snapshot['input_dump_sha256'], 'source_key' => 'ui.type', 'synthetic_target' => 'rehearsal.correction.type',
    'files' => $files, 'movements' => array_map(static fn ($m) => [
        'path' => $m['before']['source_path'], 'before_line' => $m['before']['source_line'], 'before_column' => $m['before']['source_column'],
        'after_line' => $m['after']['source_line'], 'after_column' => $m['after']['source_column'], 'expression' => $m['before']['raw_expression'],
    ], $movements)];
$uiDirectory = $root.'/storage/app/private/workbench-previews';
if (! is_dir($uiDirectory)) {
    mkdir($uiDirectory, 0755, true);
    chmod($uiDirectory, 0755);
}
$uiTemp = tempnam($uiDirectory, '.source-changes-');
try {
    $uiBytes = json_encode($uiData, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR)."\n";
    if (file_put_contents($uiTemp, $uiBytes) !== strlen($uiBytes) || ! chmod($uiTemp, 0644)
        || ! rename($uiTemp, $uiDirectory.'/source-changes.json')) {
        throw new RuntimeException('Cannot publish source preview snapshot');
    }
} finally {
    if (is_file($uiTemp)) {
        unlink($uiTemp);
    }
}
echo json_encode($report, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR)."\n";
