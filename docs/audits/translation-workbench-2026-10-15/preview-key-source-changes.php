<?php

use Gunreip\TranslationWorkbench\Support\SourceKeyChangePreview;

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
$files = [];
foreach ($groups as $path => $findings) {
    $copy = realpath($sourceRoot.'/'.$path);
    if (! $copy || ! str_starts_with($copy, $sourceRoot.'/')) {
        throw new RuntimeException('Source outside verified backup');
    }
    $hash = $manifest['source_files'][$path]['sha256'];
    $preview = (new SourceKeyChangePreview)->preview(file_get_contents($copy), $hash, $path, $findings, 'ui.type', 'rehearsal.correction.type');
    if (hash_file('sha256', $copy) !== $hash) {
        throw new RuntimeException('Preview changed copied source');
    }
    unset($preview['proposed_bytes']);
    $files[] = $preview;
}
$report = ['recorded_at' => gmdate('c'), 'mode' => 'source_change_preview_only', 'database_connected' => false,
    'source_files_written' => false, 'source' => 'ui.type', 'synthetic_target' => 'rehearsal.correction.type',
    'files' => count($files), 'findings' => array_sum(array_map(fn ($file) => count($file['edits']), $files))];
file_put_contents(__DIR__.'/source-key-change-preview-2026-10-20.json', json_encode([...$report, 'changes' => $files], JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR)."\n");
echo json_encode($report, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR)."\n";
