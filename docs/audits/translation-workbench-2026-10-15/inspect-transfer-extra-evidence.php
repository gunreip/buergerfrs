<?php

// Read-only inventory of old dynamic formats and published package translation overrides.
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchOption;
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
$report = ['captured_at' => date(DATE_ATOM), 'mode' => 'read_only_extra_transfer_evidence',
    'read_only' => $pdo->query('SHOW transaction_read_only')->fetchColumn()];
try {
    $queries = [
        'old_option_values' => 'SELECT v.id,v.entry_id,v.value_key,v.locale,v.value,v.status,e.translation_key,e.existing_key,e.suggested_key,e.status AS entry_status,e.source_path,e.source_line FROM translation_workbench_dynamic_values v LEFT JOIN translation_workbench_entries e ON e.id=v.entry_id ORDER BY v.id',
        'current_option_values' => 'SELECT v.id,v.key_id,v.value_key,v.locale,v.value,v.status,k.translation_key FROM translation_workbench_dynamic_key_values v LEFT JOIN translation_workbench_keys k ON k.id=v.key_id ORDER BY v.id',
        'sources' => 'SELECT id,key_id,finding_id,option_discovery_id,status,source_type,source_reference,source_path,source_line,source_expression FROM translation_workbench_dynamic_sources ORDER BY id',
        'source_values' => 'SELECT id,dynamic_source_id,value_key,native_label,origin,translation_key,status FROM translation_workbench_dynamic_source_values ORDER BY id',
        'candidates' => 'SELECT id,dynamic_source_id,key_id,finding_id,source_expression,candidate_source_type,candidate_reference,candidate_values_count,candidate_values,review_status,status FROM translation_workbench_dynamic_source_candidates ORDER BY id',
        'observations' => 'SELECT id,matched_entry_id,source_path,source_line,options_variable,source_type,source_reference,options_count,options,status FROM translation_workbench_option_discoveries ORDER BY id',
    ];
    foreach ($queries as $name => $sql) {
        $report[$name] = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
} finally {
    $pdo->exec('ROLLBACK');
}
$reader = new BaselineValueReader;
$report['old_option_comparison'] = [];
foreach ($report['old_option_values'] as $old) {
    $matches = array_values(array_filter($report['current_option_values'], fn ($new) => $new['status'] === 'active'
        && $old['translation_key'] !== null && $new['translation_key'] === $old['translation_key']
        && $new['locale'] === $old['locale'] && $new['value_key'] === $old['value_key']));
    $textCandidates = array_values(array_filter($report['current_option_values'], fn ($new) => $new['status'] === 'active'
        && $new['locale'] === $old['locale'] && $new['value_key'] === $old['value_key'] && $new['value'] === $old['value']));
    $fileEvidence = [];
    $error = null;
    try {
        $fullKey = TranslationWorkbenchOption::key((string) $old['translation_key'], (string) $old['value_key']);
        $group = explode('.', $fullKey, 2)[0];
        if (! preg_match('/^[A-Za-z0-9_-]+$/D', $old['locale'])) {
            throw new RuntimeException('Unsupported locale');
        }
        $relative = 'lang/'.$old['locale'].'/'.$group.'.php';
        if (is_file($root.'/'.$relative)) {
            $hash = hash_file('sha256', $root.'/'.$relative);
            foreach ($reader->phpFile($root.'/'.$relative, $old['locale'], $group, $hash) as $row) {
                if ($row['key'] === $fullKey) {
                    $fileEvidence[] = [...$row, 'file_sha256' => $hash, 'unchanged_after_read' => hash_file('sha256', $root.'/'.$relative) === $hash];
                }
            }
        }
    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
    $report['old_option_comparison'][] = ['old_id' => $old['id'], 'exact_current_candidates' => $matches, 'text_only_candidates_not_assignment' => $textCandidates,
        'file_evidence' => $fileEvidence, 'mapping_error' => $error, 'decision' => null];
}
$report['package_files'] = [];
foreach (glob($root.'/lang/vendor/*/*/*.php') ?: [] as $path) {
    $relative = substr($path, strlen($root) + 1);
    [, , $namespace, $locale, $filename] = explode('/', $relative);
    $group = pathinfo($filename, PATHINFO_FILENAME);
    $file = ['path' => $relative, 'namespace_candidate' => $namespace, 'locale' => $locale,
        'group' => $group, 'sha256' => hash_file('sha256', $path)];
    try {
        $file['values'] = $reader->phpFile($path, $locale, $group, $file['sha256']);
        $file['reader'] = 'supported';
    } catch (Throwable $e) {
        $file['reader'] = 'blocked';
        $file['reason'] = $e->getMessage();
    }
    $file['unchanged_after_read'] = hash_file('sha256', $path) === $file['sha256'];
    // Only the backup package path confirmed in installed provider source, not a general namespace guess.
    if ($namespace === 'backup') {
        $vendorPath = 'vendor/spatie/laravel-backup/resources/lang/'.$locale.'/'.$filename;
        if (is_file($root.'/'.$vendorPath)) {
            $vendorHash = hash_file('sha256', $root.'/'.$vendorPath);
            $file['vendor_reference'] = ['path' => $vendorPath, 'sha256' => $vendorHash,
                'values' => $reader->phpFile($root.'/'.$vendorPath, $locale, $group, $vendorHash),
                'unchanged_after_read' => hash_file('sha256', $root.'/'.$vendorPath) === $vendorHash];
        }
    }
    $report['package_files'][] = $file;
}
$report['finished_at'] = date(DATE_ATOM);
echo json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR).PHP_EOL;
