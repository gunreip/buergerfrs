<?php

// Isolated baseline rehearsal only; no Laravel bootstrap, .env, or production connection.
use Symfony\Component\Process\Process;
use Tests\Support\TranslationWorkbench\StaticCaptureImporter;

$root = dirname(__DIR__, 3);
require $root.'/vendor/autoload.php';
umask(0077);
$readJson = static fn (string $path): array => json_decode(file_get_contents($path), true, flags: JSON_THROW_ON_ERROR);
$backupResult = $readJson(__DIR__.'/backup-restore-result-2026-10-19.json');
$manifestPath = $root.'/'.$backupResult['backup_manifest'];
if (hash_file('sha256', $manifestPath) !== $backupResult['backup_manifest_sha256']) {
    throw new RuntimeException('Backup manifest changed');
}
$backupManifest = $readJson($manifestPath);
$backup = dirname($manifestPath);
$context = $readJson(__DIR__.'/copied-source-context-2026-10-19.json');
$capturePath = $root.'/'.$context['manifest'];
if (! str_starts_with((string) realpath($capturePath), realpath($backup).'/') || hash_file('sha256', $capturePath) !== $context['manifest_sha256']) {
    throw new RuntimeException('Capture is not the reviewed private manifest');
}
$capture = $readJson($capturePath);
if ($capture['backup_manifest_sha256'] !== $backupResult['backup_manifest_sha256']) {
    throw new RuntimeException('Capture and backup differ');
}
foreach (['files', 'language_files'] as $collection) {
    foreach ($capture[$collection] as $path => $info) {
        $hash = is_array($info) ? $info['sha256'] : $info;
        if (! isset($backupManifest['source_files'][$path]) || $backupManifest['source_files'][$path]['sha256'] !== $hash
            || hash_file('sha256', $backup.'/source/'.$path) !== $hash) {
            throw new RuntimeException('Saved source differs: '.$path);
        }
    }
}
$cluster = realpath($backupManifest['restore_cluster']);
if (! $cluster || dirname($cluster) !== realpath(sys_get_temp_dir()) || ! preg_match('/^tw-restore-[a-f0-9]{16}$/D', basename($cluster))
    || ! is_file($cluster.'/data/PG_VERSION') || is_file($cluster.'/data/postmaster.pid')) {
    throw new RuntimeException('Only the stopped isolated rehearsal cluster is allowed');
}
$run = static function (array $args) use ($root): void {
    $process = new Process($args, $root, ['PGOPTIONS' => '', 'PGPASSWORD' => false, 'PGPASSFILE' => false]);
    $process->setTimeout(1800);
    $process->mustRun();
};
$bin = '/usr/lib/postgresql/18/bin';
$dbName = 'capture_'.bin2hex(random_bytes(6));
$report = ['state' => 'incomplete', 'production_connected' => false, 'database' => $dbName,
    'mode' => 'isolated_static_capture_import', 'capture_manifest_sha256' => $context['manifest_sha256'],
    'schema_sha256' => hash_file('sha256', $root.'/packages/gunreip/laravel-translation-workbench/database/generations/v1/baseline.sql'),
    'importer_sha256' => hash_file('sha256', $root.'/tests/Support/TranslationWorkbench/StaticCaptureImporter.php')];
$started = false;
$db = null;
try {
    $run([$bin.'/pg_ctl', '-D', $cluster.'/data', '-l', $cluster.'/server.log', '-w', 'start']);
    $started = true;
    $run([$bin.'/createdb', '-h', $cluster.'/socket', '-U', 'rehearsal', $dbName]);
    $db = new PDO('pgsql:host='.$cluster.'/socket;dbname='.$dbName.';user=rehearsal', options: [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    if ($db->query('SHOW data_directory')->fetchColumn() !== $cluster.'/data' || $db->query('SHOW listen_addresses')->fetchColumn() !== ''
        || $db->query('SELECT current_database()')->fetchColumn() !== $dbName) {
        throw new RuntimeException('Unexpected target database');
    }
    $db->exec(file_get_contents($root.'/packages/gunreip/laravel-translation-workbench/database/generations/v1/baseline.sql'));
    $importer = new StaticCaptureImporter;
    $importer->installLedger($db);
    $report['first_import'] = $importer->import($db, $capturePath, $context['manifest_sha256'], $context['candidate_file_sha256']);
    $report['retry'] = $importer->import($db, $capturePath, $context['manifest_sha256'], $context['candidate_file_sha256']);
    if ($report['first_import']['replayed'] || ! $report['retry']['replayed']) {
        throw new RuntimeException('Retry not idempotent');
    }
    $stored = $db->query('SELECT o.capture_id,o.disposition,f.*,s.path FROM capture_outcomes o LEFT JOIN translation_workbench_findings f ON f.id=o.finding_id LEFT JOIN translation_workbench_source_files s ON s.id=f.source_file_id')->fetchAll(PDO::FETCH_ASSOC);
    $byId = array_column($stored, null, 'capture_id');
    $verified = 0;
    $stream = fopen(dirname($capturePath).'/candidates.jsonl', 'r');
    while (($line = fgets($stream)) !== false) {
        $row = json_decode($line, true, flags: JSON_THROW_ON_ERROR);
        $saved = $byId[$row['capture_id']] ?? throw new RuntimeException('Missing disposition');
        if ($saved['disposition'] === 'captured') {
            foreach (['raw_expression', 'raw_source_code_literal', 'raw_literal', 'suggested_literal', 'suggested_key'] as $field) {
                if ($saved[$field] !== $row[$field]) {
                    throw new RuntimeException('Stored capture differs: '.$field);
                }
            }
            if ($saved['path'] !== $row['source_path'] || (int) $saved['source_line'] !== $row['source_line'] || (int) $saved['source_column'] !== $row['source_column']) {
                throw new RuntimeException('Stored position differs');
            }
            $verified++;
        }
        unset($byId[$row['capture_id']]);
    }
    fclose($stream);
    if ($byId !== [] || $verified !== $report['first_import']['captured']) {
        throw new RuntimeException('Loss accounting differs');
    }
    $report['verified_captures'] = $verified;
    $count = static fn (string $table): int => (int) $db->query('SELECT count(*) FROM '.$table)->fetchColumn();
    $report['empty_tables'] = [];
    foreach (['translation_workbench_keys', 'translation_workbench_key_findings', 'translation_workbench_value_revisions',
        'translation_workbench_reviews', 'translation_workbench_events', 'translation_workbench_operations', 'translation_workbench_publication_runs'] as $table) {
        $report['empty_tables'][$table] = $count($table);
        if ($report['empty_tables'][$table] !== 0) {
            throw new RuntimeException('Unexpected active downstream data');
        }
    }
    if ($count('translation_workbench_scan_runs') !== 1 || $count('translation_workbench_scan_findings') !== $verified || $count('capture_outcomes') !== array_sum(array_intersect_key($report['first_import'], array_flip(['captured', 'pending', 'excluded'])))) {
        throw new RuntimeException('Retry added data or missing scan links');
    }
    // SQL guard must reject changes to the immutable raw fields.
    $db->beginTransaction();
    $rejected = false;
    try {
        $db->exec("UPDATE translation_workbench_findings SET suggested_key='changed' WHERE id=(SELECT min(id) FROM translation_workbench_findings)");
    } catch (PDOException $e) {
        if ($e->getCode() !== 'P0001') {
            throw $e;
        }
        $rejected = true;
    } finally {
        $db->rollBack();
    }
    if (! $rejected) {
        throw new RuntimeException('Immutable field accepted mutation');
    }
    $report['immutable_field_rejected'] = true;
    // Fail after the first insert, and prove the entire second scan is rolled back.
    $fixtureDir = $backup.'/'.$dbName.'-rollback-fixture';
    mkdir($fixtureDir, 0700);
    $stream = fopen(dirname($capturePath).'/candidates.jsonl', 'r');
    do {
        $row = json_decode(fgets($stream), true, flags: JSON_THROW_ON_ERROR);
    } while ($row['reason'] !== 'literal_candidate');
    fclose($stream);
    $row['source_line'] += 100000;
    $fixture = json_encode($row, JSON_THROW_ON_ERROR)."\n";
    file_put_contents($fixtureDir.'/candidates.jsonl', $fixture.$fixture);
    $fake = $capture;
    $fake['candidate_file_sha256'] = hash_file('sha256', $fixtureDir.'/candidates.jsonl');
    file_put_contents($fixtureDir.'/manifest.json', json_encode($fake, JSON_THROW_ON_ERROR));
    $failed = false;
    try {
        $importer->import($db, $fixtureDir.'/manifest.json', hash_file('sha256', $fixtureDir.'/manifest.json'), $fake['candidate_file_sha256']);
    } catch (RuntimeException $e) {
        if ($e->getMessage() !== 'Invalid or duplicate capture provenance') {
            throw $e;
        }
        $failed = true;
    }
    if (! $failed || $count('translation_workbench_findings') !== $verified || $count('translation_workbench_scan_runs') !== 1 || $count('capture_imports') !== 1) {
        throw new RuntimeException('Failed import left partial data');
    }
    $report['partial_failure_rolled_back'] = true;
    $dump = $backup.'/'.$dbName.'.dump';
    $run([$bin.'/pg_dump', '-h', $cluster.'/socket', '-U', 'rehearsal', '-Fc', '-f', $dump, $dbName]);
    $run([$bin.'/pg_restore', '--list', $dump]);
    $report['dump'] = substr($dump, strlen($root) + 1);
    $report['dump_sha256'] = hash_file('sha256', $dump);
    $report['state'] = 'isolated_capture_verified';
} catch (Throwable $error) {
    $report['state'] = 'failed';
    $report['error'] = $error->getMessage();
    fwrite(STDERR, "Import failed; inspect private report.\n");
} finally {
    if ($db && $db->inTransaction()) {
        $db->rollBack();
    }
    if ($started) {
        $run([$bin.'/pg_ctl', '-D', $cluster.'/data', '-m', 'fast', '-w', 'stop']);
        $report['cluster_stopped'] = true;
    }
    file_put_contents($backup.'/'.$dbName.'-result.json', json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)."\n");
}
echo json_encode(['state' => $report['state'], 'report' => substr($backup, strlen($root) + 1).'/'.$dbName.'-result.json', 'totals' => $report['first_import'] ?? null], JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR)."\n";
exit($report['state'] === 'isolated_capture_verified' ? 0 : 1);
