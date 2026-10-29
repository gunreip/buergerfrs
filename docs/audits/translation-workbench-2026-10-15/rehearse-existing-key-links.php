<?php

use Gunreip\TranslationWorkbench\Generation\BaselineValueReader;
use Symfony\Component\Process\Process;
use Tests\Support\TranslationWorkbench\ExistingKeyImporter;
use Tests\Support\TranslationWorkbench\ExistingKeyPlan;

$root = dirname(__DIR__, 3);
require $root.'/vendor/autoload.php';
umask(0077);
$read = static fn ($p) => json_decode(file_get_contents($p), true, flags: JSON_THROW_ON_ERROR);
$baseline = $read(__DIR__.'/source-argument-persistence-2026-10-19.json');
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
$name = 'keylinks_'.bin2hex(random_bytes(6));
$work = $backup.'/'.$name;
mkdir($work, 0700);
$context = $read(__DIR__.'/copied-source-context-2026-10-19.json');
$capturePath = $root.'/'.$context['manifest'];
if (hash_file('sha256', $capturePath) !== $context['manifest_sha256']) {
    throw new RuntimeException('Capture manifest changed');
}
$capture = $read($capturePath);
$candidatesPath = dirname($capturePath).'/candidates.jsonl';
if (hash_file('sha256', $candidatesPath) !== $context['candidate_file_sha256']) {
    throw new RuntimeException('Capture file changed');
}
$captures = array_map(fn ($line) => json_decode($line, true, flags: JSON_THROW_ON_ERROR), file($candidatesPath, FILE_IGNORE_NEW_LINES));
$language = [];
$reader = new BaselineValueReader;
foreach ($capture['language_files'] as $path => $hash) {
    foreach ($reader->phpFile($backup.'/source/'.$path, 'en', pathinfo($path, PATHINFO_FILENAME), $hash) as $row) {
        $language[] = [...$row, 'path' => $path, 'sha256' => $hash];
    }
}
$plan = (new ExistingKeyPlan)->build($captures, $language);
file_put_contents($work.'/plan.json', json_encode($plan, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR));
$report = ['state' => 'incomplete', 'production_connected' => false, 'assertions' => 0,
    'input_dump_sha256' => $baseline['dump_sha256'], 'capture_manifest_sha256' => $context['manifest_sha256'],
    'plan' => substr($work, strlen($root) + 1).'/plan.json', 'plan_sha256' => hash_file('sha256', $work.'/plan.json')];
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
    $check($db->query('SHOW data_directory')->fetchColumn() === $cluster.'/data' && $db->query('SHOW listen_addresses')->fetchColumn() === '', 'Wrong test cluster');
    $tables = $db->query("SELECT tablename FROM pg_tables WHERE schemaname='public' AND tablename NOT IN ('translation_workbench_keys','translation_workbench_key_names','translation_workbench_key_findings') ORDER BY tablename")->fetchAll(PDO::FETCH_COLUMN);
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
    $db->exec(file_get_contents($root.'/packages/gunreip/laravel-translation-workbench/database/generations/v1/existing-keys.sql'));
    $service = new ExistingKeyImporter;
    $bad = $plan;
    $bad['items'][count($bad['items']) - 1]['key'] = 'invented.wrong.key';
    $rejected = false;
    try {
        $service->import($db, $bad, $context['manifest_sha256']);
    } catch (RuntimeException) {
        $rejected = true;
    }
    $check($rejected && (int) $db->query('SELECT count(*) FROM translation_workbench_keys')->fetchColumn() === 0 && (int) $db->query('SELECT count(*) FROM existing_key_imports')->fetchColumn() === 0, 'Partial assignment survived failure');
    $result = $service->import($db, $plan, $context['manifest_sha256']);
    $check($result['keys'] === $plan['eligible_keys'] && $result['links'] === $plan['eligible_findings'], 'Assignment totals differ');
    $check($service->import($db, $plan, $context['manifest_sha256'])['replayed'], 'Retry did not reuse import');
    $check((int) $db->query('SELECT count(*) FROM translation_workbench_key_findings')->fetchColumn() === $result['links'], 'Retry duplicates');
    $check((int) $db->query('SELECT count(*) FROM translation_workbench_key_findings l JOIN translation_workbench_findings f ON f.id=l.finding_id JOIN translation_workbench_key_names n ON n.key_id=l.key_id WHERE n.full_name<>f.raw_source_code_literal OR f.raw_literal IS NOT NULL OR f.suggested_key IS NOT NULL')->fetchColumn() === 0, 'Guessed key or literal assignment');
    $check((int) $db->query("SELECT count(*) FROM existing_key_observations WHERE state='blocked_structure' AND (key_id IS NOT NULL OR link_id IS NOT NULL)")->fetchColumn() === 0, 'Blocked key assigned');
    $check((int) $db->query('SELECT count(DISTINCT finding_id) FROM existing_key_observations')->fetchColumn() === count($plan['items']), 'Findings merged or omitted');
    $check($fingerprint() === $before, 'Raw evidence, reviews or values changed');
    foreach (['existing_key_imports', 'existing_key_observations'] as $t) {
        $blocked = false;
        try {
            $db->exec('DELETE FROM '.$t);
        } catch (PDOException $e) {
            $blocked = $e->getCode() === 'P0001';
        }
        $check($blocked, 'Provenance deletion accepted');
    }
    $report['keys'] = $result['keys'];
    $report['links'] = $result['links'];
    $report['observed_findings'] = count($plan['items']);
    $report['blocked_findings'] = count($plan['items']) - $result['links'];
    $report['blocked_keys'] = $plan['blocked_keys'];
    $report['unchanged_tables'] = count($tables);
    $report['implementation_sha256'] = hash_file('sha256', $root.'/tests/Support/TranslationWorkbench/ExistingKeyImporter.php');
    $report['planner_sha256'] = hash_file('sha256', $root.'/tests/Support/TranslationWorkbench/ExistingKeyPlan.php');
    $report['runner_sha256'] = hash_file('sha256', __FILE__);
    $report['dump'] = substr($work, strlen($root) + 1).'/database.dump';
    $run([$bin.'/pg_dump', '-h', $cluster.'/socket', '-U', 'rehearsal', '-Fc', '-f', $root.'/'.$report['dump'], $name]);
    $report['dump_sha256'] = hash_file('sha256', $root.'/'.$report['dump']);
    $report['state'] = 'existing_key_links_verified';
} catch (Throwable $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    } $report['state'] = 'failed';
    $report['error'] = $e->getMessage();
} finally {
    if ($started) {
        $run([$bin.'/pg_ctl', '-D', $cluster.'/data', '-m', 'fast', '-w', 'stop']);
        $report['cluster_stopped'] = true;
    }
    file_put_contents($work.'/result.json', json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)."\n");
}
echo json_encode(['state' => $report['state'], 'assertions' => $report['assertions'], 'error' => $report['error'] ?? null, 'report' => substr($work, strlen($root) + 1).'/result.json'], JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR)."\n";
exit($report['state'] === 'existing_key_links_verified' ? 0 : 1);
