<?php

use Symfony\Component\Process\Process;
use Tests\Support\TranslationWorkbench\SourceArgumentImporter;

$root = dirname(__DIR__, 3);
require $root.'/vendor/autoload.php';
umask(0077);
$read = static fn ($p) => json_decode(file_get_contents($p), true, flags: JSON_THROW_ON_ERROR);
$baseline = $read(__DIR__.'/source-roles-2026-10-19.json');
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
$name = 'arguments_'.bin2hex(random_bytes(6));
$work = $backup.'/'.$name;
mkdir($work, 0700);
$reportPath = __DIR__.'/source-arguments-2026-10-19.json';
$json = file_get_contents($reportPath);
$input = json_decode($json, true, flags: JSON_THROW_ON_ERROR);
$inputHash = hash('sha256', $json);
if ($input['review_sha256'] !== $baseline['review_sha256']) {
    throw new RuntimeException('Review mismatch');
}
// Bind every source file to the saved snapshot, not the changing working tree.
$verifySource = static function ($path, $hash) use ($backup, $manifest): void {
    if (($manifest['source_files'][$path]['sha256'] ?? null) !== $hash || hash_file('sha256', $backup.'/source/'.$path) !== $hash) {
        throw new RuntimeException('Source provenance mismatch');
    }
};
foreach ($input['items'] as $item) {
    $verifySource($item['source_path'], $item['source_sha256']);
    if (isset($item['helper'])) {
        $verifySource($item['helper']['path'], $item['helper']['sha256']);
    }
}
foreach ($input['producers'] as $producer) {
    $verifySource($producer['path'], $producer['sha256']);
}
$report = ['state' => 'incomplete', 'production_connected' => false, 'assertions' => 0,
    'input_dump_sha256' => $baseline['dump_sha256'], 'argument_report_sha256' => $inputHash];
$check = static function (bool $ok, string $message) use (&$report): void {
    if (! $ok) {
        throw new RuntimeException($message);
    }
    $report['assertions']++;
};
$started = false;
try {
    $run([$bin.'/pg_ctl', '-D', $cluster.'/data', '-l', $cluster.'/server.log', '-w', 'start']);
    $started = true;
    $run([$bin.'/createdb', '-h', $cluster.'/socket', '-U', 'rehearsal', $name]);
    $run([$bin.'/pg_restore', '-h', $cluster.'/socket', '-U', 'rehearsal', '--exit-on-error', '--no-owner', '--no-privileges', '-d', $name, $root.'/'.$baseline['dump']]);
    $db = new PDO('pgsql:host='.$cluster.'/socket;dbname='.$name.';user=rehearsal', options: [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $check($db->query('SHOW data_directory')->fetchColumn() === $cluster.'/data' && $db->query('SHOW listen_addresses')->fetchColumn() === '' && $db->query('SELECT current_database()')->fetchColumn() === $name, 'Wrong database');
    $tables = $db->query("SELECT tablename FROM pg_tables WHERE schemaname='public' ORDER BY tablename")->fetchAll(PDO::FETCH_COLUMN);
    $fingerprint = static function () use ($db, $tables): array {
        $hashes = [];
        foreach ($tables as $table) {
            if (! preg_match('/^[a-z_]+$/D', $table)) {
                throw new RuntimeException('Unexpected table name');
            }
            $hashes[$table] = $db->query('SELECT md5(string_agg(row_to_json(t)::text,chr(10) ORDER BY row_to_json(t)::text)) FROM '.$table.' t')->fetchColumn();
        }

        return $hashes;
    };
    $before = $fingerprint();
    $db->exec(file_get_contents($root.'/packages/gunreip/laravel-translation-workbench/database/generations/v1/source-arguments.sql'));
    $service = new SourceArgumentImporter;
    $result = $service->import($db, $json, $inputHash);
    $check($result === ['replayed' => false, 'items' => 59, 'producers' => 6], 'Incomplete import');
    $repeat = $service->import($db, $json, $inputHash);
    $check($repeat['replayed'] && (int) $db->query('SELECT count(*) FROM source_argument_evidence')->fetchColumn() === 59, 'Retry duplicated evidence');
    $saved = $db->query('SELECT capture_id,evidence FROM source_argument_evidence')->fetchAll(PDO::FETCH_KEY_PAIR);
    foreach ($input['items'] as $item) {
        $check(json_decode($saved[$item['capture_id']], true, flags: JSON_THROW_ON_ERROR) == $item, 'Argument roundtrip differs');
    }
    $saved = $db->query('SELECT name,evidence FROM source_argument_producers')->fetchAll(PDO::FETCH_KEY_PAIR);
    foreach ($input['producers'] as $producer => $evidence) {
        $check(json_decode($saved[$producer], true, flags: JSON_THROW_ON_ERROR) == $evidence, 'Producer roundtrip differs');
    }
    $rejected = static function (array $changed, string $label) use ($service, $db, $check): void {
        $raw = json_encode($changed, JSON_THROW_ON_ERROR);
        $blocked = false;
        try {
            $service->import($db, $raw, hash('sha256', $raw));
        } catch (Throwable) {
            $blocked = true;
        }
        $check($blocked && (int) $db->query('SELECT count(*) FROM source_argument_batches')->fetchColumn() === 1
            && (int) $db->query('SELECT count(*) FROM source_argument_evidence')->fetchColumn() === 59, $label);
    };
    $changed = $input;
    $changed['items'][] = $input['items'][0];
    $rejected($changed, 'Duplicate capture did not roll back');
    $changed = $input;
    $removed = array_pop($changed['items']);
    $changed['counts'][$removed['evidence_kind']]--;
    $rejected($changed, 'Missing source accepted despite internally matching counts');
    $changed = $input;
    $changed['review_sha256'] = str_repeat('0', 64);
    $rejected($changed, 'Foreign review accepted');
    $changed = $input;
    $changed['items'][count($changed['items']) - 1]['decision'] = 'approved';
    $rejected($changed, 'Decision accepted or partial import survived');
    $changed = $input;
    $changed['items'][0]['capture_id'] = $db->query("SELECT capture_id FROM capture_source_roles WHERE role='value_usage' LIMIT 1")->fetchColumn();
    $rejected($changed, 'Usage accepted as translation source');
    $changed = $input;
    foreach ($changed['items'] as &$item) {
        if ($item['evidence_kind'] === 'producer_observations') {
            $item['producer'] = 'missing';
            break;
        }
    }
    unset($item);
    $rejected($changed, 'Missing producer accepted');
    $changed = $input;
    foreach ($changed['items'] as &$item) {
        if ($item['evidence_kind'] === 'option_arguments') {
            $item['arguments']['identity']['literal'] = 'invented';
            break;
        }
    }
    unset($item);
    $rejected($changed, 'Dynamic identity imported as literal');
    foreach (['source_argument_batches', 'source_argument_producers', 'source_argument_evidence'] as $table) {
        foreach (['UPDATE '.$table.' SET report_sha256=report_sha256', 'DELETE FROM '.$table] as $sql) {
            $blocked = false;
            try {
                $db->exec($sql);
            } catch (PDOException $e) {
                $blocked = $e->getCode() === 'P0001';
            }
            $check($blocked, 'Evidence rewrite accepted');
        }
    }
    $check($before === $fingerprint(), 'Existing tables changed');
    $report['existing_tables_unchanged'] = count($tables);
    $report['items'] = 59;
    $report['producers'] = 6;
    $report['counts'] = $input['counts'];
    $report['implementation_sha256'] = hash_file('sha256', $root.'/tests/Support/TranslationWorkbench/SourceArgumentImporter.php');
    $report['schema_sha256'] = hash_file('sha256', $root.'/packages/gunreip/laravel-translation-workbench/database/generations/v1/source-arguments.sql');
    $report['runner_sha256'] = hash_file('sha256', __FILE__);
    $report['dump'] = substr($work, strlen($root) + 1).'/database.dump';
    $run([$bin.'/pg_dump', '-h', $cluster.'/socket', '-U', 'rehearsal', '-Fc', '-f', $root.'/'.$report['dump'], $name]);
    $report['dump_sha256'] = hash_file('sha256', $root.'/'.$report['dump']);
    $report['state'] = 'argument_persistence_verified';
} catch (Throwable $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    $report['state'] = 'failed';
    $report['error'] = $e->getMessage();
} finally {
    if ($started) {
        $run([$bin.'/pg_ctl', '-D', $cluster.'/data', '-m', 'fast', '-w', 'stop']);
        $report['cluster_stopped'] = true;
    }
    file_put_contents($work.'/result.json', json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)."\n");
}
echo json_encode(['state' => $report['state'], 'assertions' => $report['assertions'], 'error' => $report['error'] ?? null,
    'report' => substr($work, strlen($root) + 1).'/result.json'], JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR)."\n";
exit($report['state'] === 'argument_persistence_verified' ? 0 : 1);
