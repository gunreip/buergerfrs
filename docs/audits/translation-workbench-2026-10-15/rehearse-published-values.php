<?php

use Symfony\Component\Process\Process;
use Tests\Support\TranslationWorkbench\PublishedValueBaseline;

$root = dirname(__DIR__, 3);
require $root.'/vendor/autoload.php';
umask(0077);
$read = static fn ($p) => json_decode(file_get_contents($p), true, flags: JSON_THROW_ON_ERROR);
$baseline = $read(__DIR__.'/existing-key-links-2026-10-19.json');
$backupResult = $read(__DIR__.'/backup-restore-result-2026-10-19.json');
$manifestPath = $root.'/'.$backupResult['backup_manifest'];
if (hash_file('sha256', $manifestPath) !== $backupResult['backup_manifest_sha256'] || hash_file('sha256', $root.'/'.$baseline['dump']) !== $baseline['dump_sha256']) {
    throw new RuntimeException('Verified input changed');
}
$manifest = $read($manifestPath);
$backup = dirname($manifestPath);
$cluster = realpath($manifest['restore_cluster']);
if (! $cluster || dirname($cluster) !== realpath(sys_get_temp_dir()) || ! preg_match('/^tw-restore-[a-f0-9]{16}$/D', basename($cluster)) || is_file($cluster.'/data/postmaster.pid')) {
    throw new RuntimeException('Expected stopped isolated test cluster');
}
$run = static function (array $args) use ($root): void {
    $p = new Process($args, $root, ['PGOPTIONS' => '', 'PGPASSWORD' => false, 'PGPASSFILE' => false]);
    $p->setTimeout(1800);
    $p->mustRun();
};
$bin = '/usr/lib/postgresql/18/bin';
$name = 'published_'.bin2hex(random_bytes(6));
$work = $backup.'/'.$name;
mkdir($work, 0700);
$service = new PublishedValueBaseline;
$evidence = $service->read($backup.'/source', $manifest['source_files']);
$report = ['state' => 'incomplete', 'production_connected' => false, 'assertions' => 0, 'input_dump_sha256' => $baseline['dump_sha256'],
    'backup_manifest_sha256' => $backupResult['backup_manifest_sha256'], 'files' => count($evidence['sources']),
    'excluded_files' => $evidence['excluded'], 'values' => count($evidence['values'])];
$report['locales'] = [];
foreach ($evidence['values'] as $row) {
    $report['locales'][$row['locale']] = ($report['locales'][$row['locale']] ?? 0) + 1;
}
$check = static function (bool $ok, string $message) use (&$report): void {
    if (! $ok) {
        throw new RuntimeException($message);
    } $report['assertions']++;
};
$started = false;
try {
    $run([$bin.'/pg_ctl', '-D', $cluster.'/data', '-l', $cluster.'/server.log', '-w', 'start']);
    $started = true;
    $run([$bin.'/createdb', '-h', $cluster.'/socket', '-U', 'rehearsal', $name]);
    $run([$bin.'/pg_restore', '-h', $cluster.'/socket', '-U', 'rehearsal', '--exit-on-error', '--no-owner', '--no-privileges', '-d', $name, $root.'/'.$baseline['dump']]);
    $db = new PDO('pgsql:host='.$cluster.'/socket;dbname='.$name.';user=rehearsal', options: [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $check($db->query('SHOW data_directory')->fetchColumn() === $cluster.'/data' && $db->query('SHOW listen_addresses')->fetchColumn() === '' && $db->query('SELECT current_database()')->fetchColumn() === $name, 'Wrong test connection');
    $tables = $db->query("SELECT tablename FROM pg_tables WHERE schemaname='public' ORDER BY tablename")->fetchAll(PDO::FETCH_COLUMN);
    $fingerprint = static function () use ($db, $tables): array {
        $r = [];
        foreach ($tables as $t) {
            if (! preg_match('/^[a-z_]+$/D', $t)) {
                throw new RuntimeException('Unexpected table');
            }
            $r[$t] = $db->query('SELECT md5(string_agg(row_to_json(t)::text,chr(10) ORDER BY row_to_json(t)::text)) FROM '.$t.' t')->fetchColumn();
        }

        return $r;
    };
    $before = $fingerprint();
    $db->exec(file_get_contents($root.'/packages/gunreip/laravel-translation-workbench/database/generations/v1/published-values.sql'));
    $bad = $evidence;
    $bad['values'][count($bad['values']) - 1]['locale'] = 'invented';
    $blocked = false;
    try {
        $service->import($db, $bad, $backupResult['backup_manifest_sha256']);
    } catch (RuntimeException) {
        $blocked = true;
    }
    $check($blocked && (int) $db->query('SELECT count(*) FROM published_value_evidence')->fetchColumn() === 0 && (int) $db->query('SELECT count(*) FROM published_value_sources')->fetchColumn() === 0, 'Partial baseline survived');
    $totals = $service->import($db, $evidence, $backupResult['backup_manifest_sha256']);
    $check(! $totals['replayed'], 'First import replayed');
    $check($service->import($db, $evidence, $backupResult['backup_manifest_sha256'])['replayed'], 'Retry duplicated baseline');
    $check((int) $db->query('SELECT count(*) FROM published_value_evidence')->fetchColumn() === count($evidence['values']), 'Value count differs');
    $saved = $db->query('SELECT path,locale,full_name,value FROM published_value_evidence')->fetchAll(PDO::FETCH_ASSOC);
    $index = [];
    foreach ($saved as $row) {
        $index[json_encode([$row['locale'], $row['full_name']])] = [$row['path'], $row['value']];
    }
    $same = true;
    foreach ($evidence['values'] as $row) {
        $same = $same && ($index[json_encode([$row['locale'], $row['key']])] ?? null) === [$row['path'], $row['value']];
    }
    $check($same, 'Published bytes changed');
    $check((int) $db->query("SELECT count(*) FROM published_value_evidence v JOIN translation_workbench_key_names n ON n.key_id=v.key_id AND n.valid_to IS NULL WHERE v.full_name<>n.full_name OR n.namespace<>''")->fetchColumn() === 0, 'Baseline linked to wrong key');
    $check((int) $db->query("SELECT count(*) FROM published_value_evidence WHERE state<>'linked' AND key_id IS NOT NULL")->fetchColumn() === 0, 'Blocked or unassigned value linked');
    foreach (['published_value_baselines', 'published_value_sources', 'published_value_evidence'] as $t) {
        $blocked = false;
        try {
            $db->exec('DELETE FROM '.$t);
        } catch (PDOException $e) {
            $blocked = $e->getCode() === 'P0001';
        }$check($blocked, 'Baseline deletion allowed');
    }
    $check($before === $fingerprint(), 'Existing tables changed');
    $report['existing_tables_unchanged'] = count($tables);
    $report['totals'] = $totals;
    $report['missing_exact_locales'] = $db->query("SELECT k.locale,count(*) AS missing FROM (SELECT DISTINCT locale FROM published_value_sources) k CROSS JOIN translation_workbench_key_names n WHERE n.namespace='' AND n.valid_to IS NULL AND NOT EXISTS (SELECT 1 FROM published_value_evidence v WHERE v.key_id=n.key_id AND v.locale=k.locale) GROUP BY k.locale ORDER BY k.locale")->fetchAll(PDO::FETCH_ASSOC);
    $report['implementation_sha256'] = hash_file('sha256', $root.'/tests/Support/TranslationWorkbench/PublishedValueBaseline.php');
    $report['schema_sha256'] = hash_file('sha256', $root.'/packages/gunreip/laravel-translation-workbench/database/generations/v1/published-values.sql');
    $report['runner_sha256'] = hash_file('sha256', __FILE__);
    $report['dump'] = substr($work, strlen($root) + 1).'/database.dump';
    $run([$bin.'/pg_dump', '-h', $cluster.'/socket', '-U', 'rehearsal', '-Fc', '-f', $root.'/'.$report['dump'], $name]);
    $report['dump_sha256'] = hash_file('sha256', $root.'/'.$report['dump']);
    $report['state'] = 'published_baseline_verified';
} catch (Throwable $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }$report['state'] = 'failed';
    $report['error'] = $e->getMessage();
} finally {
    if ($started) {
        $run([$bin.'/pg_ctl', '-D', $cluster.'/data', '-m', 'fast', '-w', 'stop']);
        $report['cluster_stopped'] = true;
    }
    file_put_contents($work.'/result.json', json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)."\n");
}
echo json_encode(['state' => $report['state'], 'assertions' => $report['assertions'], 'error' => $report['error'] ?? null, 'report' => substr($work, strlen($root) + 1).'/result.json'], JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR)."\n";
exit($report['state'] === 'published_baseline_verified' ? 0 : 1);
