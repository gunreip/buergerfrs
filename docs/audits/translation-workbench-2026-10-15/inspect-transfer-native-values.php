<?php

// Read-only native-label evidence. Native labels have no explicit translation locale.
use Gunreip\TranslationWorkbench\Generation\BaselineValueReader;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;

$root = dirname(__DIR__, 3);
putenv('PGOPTIONS=-c default_transaction_read_only=on');
require $root.'/vendor/autoload.php';
$app = require $root.'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();
$pdo = DB::connection()->getPdo();
if ($pdo->getAttribute(PDO::ATTR_DRIVER_NAME) !== 'pgsql') {
    throw new RuntimeException('PostgreSQL required');
}
$pdo->exec('BEGIN TRANSACTION ISOLATION LEVEL REPEATABLE READ READ ONLY');
$report = ['captured_at' => date(DATE_ATOM), 'mode' => 'read_only_native_label_evidence',
    'read_only' => $pdo->query('SHOW transaction_read_only')->fetchColumn()];
try {
    $report['native_rows'] = $pdo->query('SELECT v.id,v.entry_id,v.value_key,v.native_label,v.source_type,v.source_reference,v.status,v.first_seen_at,v.last_seen_at,v.meta,
        e.status AS entry_status,e.kind AS entry_kind,e.translation_key,e.existing_key,e.suggested_key,e.source_path,e.source_line,e.literal_text AS entry_literal_text,e.literal_text_suggested AS entry_suggested_literal
        FROM translation_workbench_values v LEFT JOIN translation_workbench_entries e ON e.id=v.entry_id ORDER BY v.id')->fetchAll(PDO::FETCH_ASSOC);
    $report['active_literal_findings'] = $pdo->query("SELECT f.id,f.literal_text,f.status,f.kind,s.path FROM translation_workbench_findings f
        JOIN translation_workbench_source_files s ON s.id=f.source_file_id WHERE f.status='active' AND f.literal_text IS NOT NULL ORDER BY f.id")->fetchAll(PDO::FETCH_ASSOC);
    $report['dynamic_source_labels'] = $pdo->query('SELECT v.id,v.dynamic_source_id,v.value_key,v.native_label,v.status,s.status AS source_status,s.source_path,s.finding_id,s.key_id
        FROM translation_workbench_dynamic_source_values v LEFT JOIN translation_workbench_dynamic_sources s ON s.id=v.dynamic_source_id ORDER BY v.id')->fetchAll(PDO::FETCH_ASSOC);
    $report['lang_values'] = $pdo->query('SELECT id,translation_key,locale,value,status FROM translation_workbench_lang_values WHERE value IS NOT NULL ORDER BY id')->fetchAll(PDO::FETCH_ASSOC);
    $report['dynamic_translations'] = $pdo->query('SELECT id,key_id,value_key,locale,value,status FROM translation_workbench_dynamic_key_values WHERE value IS NOT NULL ORDER BY id')->fetchAll(PDO::FETCH_ASSOC);
} finally {
    $pdo->exec('ROLLBACK');
}
$report['referenced_lang_files'] = [];
foreach ($report['native_rows'] as $row) {
    if ($row['source_type'] !== 'lang_file' || ! preg_match('~^(lang/([A-Za-z0-9_-]+)/([A-Za-z0-9_-]+)\.php):(.+)$~D', $row['source_reference'] ?? '', $match)) {
        continue;
    }
    $path = $match[1];
    if (isset($report['referenced_lang_files'][$path])) {
        continue;
    }
    $file = ['exists' => is_file($root.'/'.$path)];
    if ($file['exists']) {
        $file['sha256'] = hash_file('sha256', $root.'/'.$path);
        try {
            $file['values'] = (new BaselineValueReader)->phpFile($root.'/'.$path, $match[2], $match[3], $file['sha256']);
        } catch (Throwable $e) {
            $file['reader_error'] = $e->getMessage();
        }
        $file['unchanged_after_read'] = hash_file('sha256', $root.'/'.$path) === $file['sha256'];
    }
    $report['referenced_lang_files'][$path] = $file;
}
// Comparisons are exact-text witnesses, never assignment or translation approval.
$report['comparison'] = [];
foreach ($report['native_rows'] as $row) {
    $label = $row['native_label'];
    $findings = $label === null ? [] : array_values(array_filter($report['active_literal_findings'], fn ($f) => $f['literal_text'] === $label));
    $sources = $label === null ? [] : array_values(array_filter($report['dynamic_source_labels'], fn ($v) => $v['native_label'] === $label));
    $values = $label === null ? [] : array_values(array_filter($report['lang_values'], fn ($v) => $v['status'] === 'active' && $v['value'] === $label));
    $options = $label === null ? [] : array_values(array_filter($report['dynamic_translations'], fn ($v) => $v['status'] === 'active' && $v['value'] === $label));
    $report['comparison'][] = ['native_id' => $row['id'], 'native_label_sha256' => $label === null ? null : hash('sha256', $label),
        'active_literal_finding_ids' => array_column($findings, 'id'),
        'same_path_active_literal_finding_ids' => array_column(array_filter($findings, fn ($f) => $f['path'] === $row['source_path']), 'id'),
        'dynamic_source_value_ids' => array_column($sources, 'id'),
        'active_lang_value_ids' => array_column($values, 'id'), 'active_dynamic_translation_ids' => array_column($options, 'id'),
        'decision' => null];
}
$report['finished_at'] = date(DATE_ATOM);
echo json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR).PHP_EOL;
