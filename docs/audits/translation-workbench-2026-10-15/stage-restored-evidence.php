<?php

// Copy-only evidence staging. Never bootstraps Laravel or reads production DB configuration.
use Symfony\Component\Process\Process;

$root = dirname(__DIR__, 3);
require $root.'/vendor/autoload.php';
umask(0077);
$resultPath = __DIR__.'/backup-restore-result-2026-10-19.json';
$result = json_decode(file_get_contents($resultPath), true, flags: JSON_THROW_ON_ERROR);
$manifestPath = $root.'/'.$result['backup_manifest'];
if (hash_file('sha256', $manifestPath) !== $result['backup_manifest_sha256']) {
    throw new RuntimeException('Backup manifest changed');
}
$manifest = json_decode(file_get_contents($manifestPath), true, flags: JSON_THROW_ON_ERROR);
$backup = dirname($manifestPath);
$cluster = realpath($manifest['restore_cluster']);
if ($manifest['state'] !== 'backup_and_restore_verified' || ! $cluster || dirname($cluster) !== realpath(sys_get_temp_dir())
    || ! preg_match('/^tw-restore-[a-f0-9]{16}$/D', basename($cluster)) || ! is_file($cluster.'/data/PG_VERSION')
    || is_file($cluster.'/data/postmaster.pid') || hash_file('sha256', $backup.'/database.dump') !== $manifest['dump_sha256']) {
    throw new RuntimeException('Only the verified stopped rehearsal cluster is allowed');
}
$run = static function (array $command) use ($root): void {
    $p = new Process($command, $root, ['PGOPTIONS' => '', 'PGPASSWORD' => false, 'PGPASSFILE' => false]);
    $p->setTimeout(1800);
    $p->mustRun();
};
$bin = '/usr/lib/postgresql/18/bin';
$report = ['started_at' => date(DATE_ATOM), 'mode' => 'isolated_evidence_staging', 'production_connected' => false,
    'backup_manifest_sha256' => $result['backup_manifest_sha256'], 'state' => 'incomplete'];
$source = $target = null;
$started = false;
$batch = $result['backup_manifest_sha256'];
$archiveOnly = ['translation_audit_events', 'translation_workbench_events', 'translation_workbench_timeline_events',
    'translation_workbench_timeline_chains', 'translation_workbench_pipeline_runs', 'translation_workbench_pipeline_run_steps', 'translation_workbench_event_types'];
$quote = static fn (string $v): string => '"'.str_replace('"', '""', $v).'"';
try {
    $run([$bin.'/pg_ctl', '-D', $cluster.'/data', '-l', $cluster.'/server.log', '-w', 'start']);
    $started = true;
    $source = new PDO('pgsql:host='.$cluster.'/socket;dbname=restored_baseline;user=rehearsal', options: [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    if ($source->query('SHOW data_directory')->fetchColumn() !== $cluster.'/data' || $source->query('SHOW listen_addresses')->fetchColumn() !== '') {
        throw new RuntimeException('Unexpected database server');
    }
    $source->exec('BEGIN TRANSACTION ISOLATION LEVEL REPEATABLE READ READ ONLY');
    $report['source_read_only'] = $source->query('SHOW transaction_read_only')->fetchColumn();
    // Use a fresh database every run. The restored archive is never the target.
    $dbName = 'evidence_'.bin2hex(random_bytes(6));
    $run([$bin.'/createdb', '-h', $cluster.'/socket', '-U', 'rehearsal', $dbName]);
    $target = new PDO('pgsql:host='.$cluster.'/socket;dbname='.$dbName.';user=rehearsal', options: [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    if ($target->query('SHOW data_directory')->fetchColumn() !== $cluster.'/data' || $target->query('SELECT current_database()')->fetchColumn() === 'restored_baseline') {
        throw new RuntimeException('Unexpected staging target');
    }
    $target->exec(file_get_contents($root.'/tests/Support/TranslationWorkbench/postgres/evidence-staging.sql'));
    $report['staging_database'] = $dbName;
    $target->beginTransaction();
    $stmt = $target->prepare('INSERT INTO evidence_batches(id,backup_manifest_sha256,source_dump_sha256) VALUES (?,?,?)');
    $stmt->execute([$batch, $batch, $manifest['dump_sha256']]);
    $insertCollection = $target->prepare('INSERT INTO evidence_collections VALUES (?,?,?,?,?)');
    $insert = $target->prepare('INSERT INTO evidence_records(batch_id,source_table,source_id,raw_payload,payload_sha256) VALUES (?,?,?,?,?)');
    $report['collections'] = [];
    foreach ($manifest['database_tables'] as $quotedTable => $expected) {
        if (! preg_match('/^"public"\."(translation[a-z0-9_]+|dynamic_translation[a-z0-9_]+)"$/D', $quotedTable, $m)) {
            continue;
        }
        $table = $m[1];
        $disposition = in_array($table, $archiveOnly, true) ? 'retained_in_restored_archive' : 'copied_as_evidence';
        $hash = hash_init('sha256');
        $count = 0;
        $q = $source->query('SELECT md5(row_to_json(t)::text) AS h FROM public.'.$quote($table).' t ORDER BY md5(row_to_json(t)::text) COLLATE "C"');
        while (($h = $q->fetchColumn()) !== false) {
            hash_update($hash, $h."\n");
            $count++;
        }
        if ($count !== $expected['rows'] || hash_final($hash) !== $expected['row_multiset_sha256']) {
            throw new RuntimeException('Archive drift: '.$table);
        }
        $insertCollection->execute([$batch, $table, $disposition, $count, $expected['row_multiset_sha256']]);
        if ($disposition === 'copied_as_evidence') {
            $rows = $source->query('SELECT id::text AS source_id,row_to_json(t)::text AS payload FROM public.'.$quote($table).' t ORDER BY id');
            while ($row = $rows->fetch(PDO::FETCH_ASSOC)) {
                $insert->execute([$batch, $table, $row['source_id'], $row['payload'], hash('sha256', $row['payload'])]);
            }
        }
        $report['collections'][$table] = ['disposition' => $disposition, 'source_rows' => $count];
        echo $table.': '.$count.' / '.$disposition.PHP_EOL;
    }
    $insertFile = $target->prepare('INSERT INTO evidence_files VALUES (?,?,?,?)');
    foreach ($manifest['source_files'] as $path => $info) {
        if (! isset($info['sha256']) || hash_file('sha256', $backup.'/source/'.$path) !== $info['sha256']) {
            throw new RuntimeException('Unsupported or changed source file: '.$path);
        }
        $insertFile->execute([$batch, $path, $info['sha256'], $info['bytes']]);
    }
    // Compare the actual staged payloads to the original table multiset, not just inserted counts.
    $report['records'] = 0;
    foreach ($report['collections'] as $table => $info) {
        if ($info['disposition'] !== 'copied_as_evidence') {
            continue;
        }
        $q = $target->prepare('SELECT raw_payload,payload_sha256 FROM evidence_records WHERE batch_id=? AND source_table=? ORDER BY md5(raw_payload) COLLATE "C"');
        $q->execute([$batch, $table]);
        $hash = hash_init('sha256');
        $count = 0;
        while ($r = $q->fetch(PDO::FETCH_ASSOC)) {
            if (hash('sha256', $r['raw_payload']) !== $r['payload_sha256']) {
                throw new RuntimeException('Staged payload hash differs');
            }
            hash_update($hash, md5($r['raw_payload'])."\n");
            $count++;
        }
        if ($count !== $info['source_rows'] || hash_final($hash) !== $manifest['database_tables']['"public"."'.$table.'"']['row_multiset_sha256']) {
            throw new RuntimeException('Staged collection differs: '.$table);
        }
        $report['records'] += $count;
    }
    $target->commit();
    $source->exec('ROLLBACK');
    // Exercise rejected mutations and duplicate provenance using savepoints.
    $target->beginTransaction();
    $checks = [
        'UPDATE evidence_records SET raw_payload=raw_payload WHERE id=(SELECT min(id) FROM evidence_records)',
        'DELETE FROM evidence_files WHERE source_path=(SELECT min(source_path) FROM evidence_files)',
        'INSERT INTO evidence_records(batch_id,source_table,source_id,raw_payload,payload_sha256) SELECT batch_id,source_table,source_id,raw_payload,payload_sha256 FROM evidence_records ORDER BY id LIMIT 1',
    ];
    foreach ($checks as $sql) {
        $target->exec('SAVEPOINT rejected_change');
        $failed = false;
        try {
            $target->exec($sql);
        } catch (PDOException $e) {
            if (! in_array($e->getCode(), ['P0001', '23505'], true)) {
                throw $e;
            }
            $failed = true;
        }
        $target->exec('ROLLBACK TO SAVEPOINT rejected_change');
        if (! $failed) {
            throw new RuntimeException('Evidence mutation unexpectedly accepted');
        }
    }
    $target->rollBack();
    $stagingDump = $backup.'/'.$dbName.'.dump';
    $run([$bin.'/pg_dump', '-h', $cluster.'/socket', '-U', 'rehearsal', '-Fc', '-f', $stagingDump, $dbName]);
    $run([$bin.'/pg_restore', '--list', $stagingDump]);
    $report['staging_dump'] = basename($stagingDump);
    $report['staging_dump_sha256'] = hash_file('sha256', $stagingDump);
    $report['staging_dump_bytes'] = filesize($stagingDump);
    $report['files'] = count($manifest['source_files']);
    $report['rejected_mutation_checks'] = count($checks);
    $report['state'] = 'evidence_staged_and_verified';
    $report['active_generation_created'] = false;
    $report['finished_at'] = date(DATE_ATOM);
} catch (Throwable $e) {
    $report['state'] = 'failed';
    $report['error'] = $e->getMessage();
    fwrite(STDERR, "Staging failed; see private report.\n");
} finally {
    foreach ([$source, $target] as $pdo) {
        if ($pdo && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
    }
    if ($started) {
        $run([$bin.'/pg_ctl', '-D', $cluster.'/data', '-m', 'fast', '-w', 'stop']);
        $report['cluster_stopped'] = true;
    }
    file_put_contents($backup.'/evidence-staging-result.json', json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR).PHP_EOL);
}
echo json_encode(['state' => $report['state'], 'records' => $report['records'] ?? null], JSON_THROW_ON_ERROR).PHP_EOL;
exit($report['state'] === 'evidence_staged_and_verified' ? 0 : 1);
