<?php

use Symfony\Component\Process\Process;
use Tests\Support\TranslationWorkbench\CaptureSourceRole;

$root = dirname(__DIR__, 3);
require $root.'/vendor/autoload.php';
umask(0077);
$read = static fn ($p) => json_decode(file_get_contents($p), true, flags: JSON_THROW_ON_ERROR);
$baseline = $read(__DIR__.'/static-capture-import-2026-10-19.json');
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
$name = 'roles_'.bin2hex(random_bytes(6));
$work = $backup.'/'.$name;
mkdir($work, 0700);
$context = $read(__DIR__.'/copied-source-context-2026-10-19.json');
$reviewPath = __DIR__.'/pending-capture-review-2026-10-19.json';
$review = $read($reviewPath);
$candidatePath = dirname($root.'/'.$context['manifest']).'/candidates.jsonl';
if (hash_file('sha256', $candidatePath) !== $context['candidate_file_sha256']
    || $review['candidate_file_sha256'] !== $context['candidate_file_sha256']
    || $review['capture_manifest_sha256'] !== $context['manifest_sha256']) {
    throw new RuntimeException('Unverified role evidence');
}
$reviews = [];
foreach ($review['items'] as $item) {
    if (isset($reviews[$item['capture_id']])) {
        throw new RuntimeException('Duplicate review');
    }
    $reviews[$item['capture_id']] = $item;
}
$report = ['state' => 'incomplete', 'production_connected' => false, 'assertions' => 0,
    'input_dump_sha256' => $baseline['dump_sha256'], 'review_sha256' => hash_file('sha256', $reviewPath), 'counts' => []];
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
    $fingerprint = static fn () => $db->query('SELECT md5(string_agg(row_to_json(f)::text,chr(10) ORDER BY id)) FROM translation_workbench_findings f')->fetchColumn();
    $before = $fingerprint();
    $outcomes = [];
    foreach ($db->query('SELECT * FROM capture_outcomes')->fetchAll(PDO::FETCH_ASSOC) as $outcome) {
        $outcomes[$outcome['capture_id']] = $outcome;
    }
    $db->beginTransaction();
    $db->exec(file_get_contents($root.'/packages/gunreip/laravel-translation-workbench/database/generations/v1/source-roles.sql'));
    $insert = $db->prepare('INSERT INTO capture_source_roles VALUES (?,?,?,?,?,?,?)');
    $classifier = new CaptureSourceRole;
    $seen = [];
    foreach (file($candidatePath, FILE_IGNORE_NEW_LINES) as $line) {
        $capture = json_decode($line, true, flags: JSON_THROW_ON_ERROR);
        $id = $capture['capture_id'];
        if (isset($seen[$id]) || ! isset($outcomes[$id])) {
            throw new RuntimeException('Duplicate or missing outcome');
        }
        $seen[$id] = true;
        $role = $classifier->classify($capture, $reviews[$id] ?? null);
        unset($reviews[$id]);
        $outcome = $outcomes[$id];
        if ($outcome['manifest_sha256'] !== $context['manifest_sha256'] || $outcome['disposition'] !== $role['state']) {
            throw new RuntimeException('Original import outcome differs');
        }
        $insert->execute([$context['manifest_sha256'], $id, $report['review_sha256'], $role['role'], $role['state'], json_encode($role['evidence'], JSON_THROW_ON_ERROR), $outcome['finding_id']]);
        $key = $role['role'].':'.$role['state'];
        $report['counts'][$key] = ($report['counts'][$key] ?? 0) + 1;
    }
    $check($reviews === [] && count($seen) === count($outcomes), 'Incomplete coverage');
    $db->commit();
    $check($fingerprint() === $before, 'Original finding evidence changed');
    $check((int) $db->query("SELECT count(*) FROM capture_source_roles WHERE role IN ('value_usage','option_source') AND finding_id IS NOT NULL")->fetchColumn() === 0, 'Usage or options became scalar findings');
    foreach (['keys', 'key_findings', 'value_revisions', 'reviews', 'events'] as $table) {
        $check((int) $db->query('SELECT count(*) FROM translation_workbench_'.$table)->fetchColumn() === 0, 'Unexpected writes to '.$table);
    }
    // Prove the database rejects an accidental usage-to-finding assignment and later rewrites.
    $db->beginTransaction();
    $db->exec('ALTER TABLE capture_source_roles DISABLE TRIGGER immutable_source_role');
    $blocked = false;
    try {
        $db->exec("UPDATE capture_source_roles SET state='captured',finding_id=(SELECT min(id) FROM translation_workbench_findings) WHERE role='value_usage'");
    } catch (PDOException $e) {
        $blocked = $e->getCode() === '23514';
    }
    $db->rollBack();
    $check($blocked, 'Usage constraint did not reject a scalar finding');
    $blocked = false;
    try {
        $db->exec("UPDATE capture_source_roles SET evidence='{}' WHERE role='value_usage'");
    } catch (PDOException $e) {
        $blocked = $e->getCode() === 'P0001';
    }
    $check($blocked, 'Evidence rewrite was accepted');
    $report['findings_unchanged'] = true;
    $report['implementation_sha256'] = hash_file('sha256', $root.'/tests/Support/TranslationWorkbench/CaptureSourceRole.php');
    $report['schema_sha256'] = hash_file('sha256', $root.'/packages/gunreip/laravel-translation-workbench/database/generations/v1/source-roles.sql');
    $report['runner_sha256'] = hash_file('sha256', __FILE__);
    $report['dump'] = substr($work, strlen($root) + 1).'/database.dump';
    $run([$bin.'/pg_dump', '-h', $cluster.'/socket', '-U', 'rehearsal', '-Fc', '-f', $root.'/'.$report['dump'], $name]);
    $report['dump_sha256'] = hash_file('sha256', $root.'/'.$report['dump']);
    $report['state'] = 'source_roles_verified';
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
echo json_encode(['state' => $report['state'], 'assertions' => $report['assertions'], 'error' => $report['error'] ?? null, 'report' => substr($work, strlen($root) + 1).'/result.json'], JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR)."\n";
exit($report['state'] === 'source_roles_verified' ? 0 : 1);
