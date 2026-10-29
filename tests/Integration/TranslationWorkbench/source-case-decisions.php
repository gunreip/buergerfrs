<?php

use Gunreip\TranslationWorkbench\Support\GenerationCaseDecisions;
use Gunreip\TranslationWorkbench\Support\WorkingGenerationSchema;
use Illuminate\Foundation\Application;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

$root = dirname(__DIR__, 3);
require $root.'/vendor/autoload.php';
new Application($root);
$bin = '/usr/lib/postgresql/18/bin';
$dir = sys_get_temp_dir().'/tw-decisions-'.bin2hex(random_bytes(8));
mkdir($dir, 0700);
mkdir($dir.'/socket', 0700);
$run = static fn ($cmd) => (new Process($cmd))->mustRun();
$started = false;
$fixture = 'storage/framework/testing/decision-'.bin2hex(random_bytes(6)).'.php';
file_put_contents($root.'/'.$fixture, '<?php echo "fixture";');
$checks = 0;
$check = static function ($ok) use (&$checks) {
    if (! $ok) {
        throw new RuntimeException('Decision check failed');
    } $checks++;
};
try {
    $run([$bin.'/initdb', '-D', $dir.'/data', '-U', 'tester', '--auth-local=trust', '--auth-host=reject', '--no-locale', '--encoding=UTF8']);
    file_put_contents($dir.'/data/postgresql.conf', "\nlisten_addresses=''\nunix_socket_directories='".$dir."/socket'\n", FILE_APPEND);
    $run([$bin.'/pg_ctl', '-D', $dir.'/data', '-l', $dir.'/log', '-w', 'start']);
    $started = true;
    $run([$bin.'/createdb', '-h', $dir.'/socket', '-U', 'tester', 'decisions']);
    $connect = static fn () => new PDO('pgsql:host='.$dir.'/socket;dbname=decisions;user=tester', options: [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $db = $connect();
    (new WorkingGenerationSchema)->install($db, 'decisions', 'current_generation_v2', (string) Str::uuid());
    $db->exec('SET search_path TO current_generation_v2,pg_catalog');
    $scan = (string) Str::uuid();
    $db->prepare("INSERT INTO translation_workbench_scan_runs VALUES (?,'completed','{}',now())")->execute([$scan]);
    $manifest = str_repeat('a', 64);
    $db->prepare("INSERT INTO capture_imports VALUES (?,?,?,'{}')")->execute([$manifest, str_repeat('b', 64), $scan]);
    $db->prepare("INSERT INTO capture_outcomes VALUES (?,?,'pending','special_call_requires_context',NULL)")->execute([$manifest, str_repeat('c', 64)]);
    $case = ['capture_id' => str_repeat('c', 64), 'manifest_sha256' => $manifest, 'source_path' => $fixture,
        'source_sha256' => hash_file('sha256', $root.'/'.$fixture), 'raw_expression' => 'fixture'];
    $service = new GenerationCaseDecisions;
    $first = $service->save($db, $case, 'database_value', 'Reviewed database source', 7, 0);
    $check($service->decisions($connect())[$case['capture_id']]['decision'] === 'database_value');
    // Same capture after a later completed scan retains its decision.
    $db->prepare("INSERT INTO translation_workbench_scan_runs VALUES (?,'completed','{}',now())")->execute([(string) Str::uuid()]);
    $check($service->decisions($connect())[$case['capture_id']]['id'] === $first['id']);
    try {
        $service->save($db, $case, 'translate', 'Stale editor', 8, 0);
        $check(false);
    } catch (RuntimeException $e) {
        $check(str_contains($e->getMessage(), 'Another decision'));
    }
    $second = $service->save($db, $case, 'translate', 'Explicitly revised decision', 7, (int) $first['id']);
    $check($service->decisions($connect())[$case['capture_id']]['decision'] === 'translate');
    $service->save($db, $case, 'translate', 'Explicitly revised decision', 7, (int) $second['id']);
    $check((int) $db->query('SELECT count(*) FROM translation_workbench_events')->fetchColumn() === 2);
    file_put_contents($root.'/'.$fixture, 'changed');
    try {
        $service->save($db, $case, 'database_value', 'Stale source', 7, (int) $second['id']);
        $check(false);
    } catch (RuntimeException $e) {
        $check(str_contains($e->getMessage(), 'Source changed'));
    }
    $check((int) $db->query('SELECT count(*) FROM translation_workbench_events')->fetchColumn() === 2);
    $check((int) $db->query('SELECT count(*) FROM translation_workbench_keys')->fetchColumn() === 0);
    $check((int) $db->query('SELECT count(*) FROM translation_workbench_value_revisions')->fetchColumn() === 0);
    $check($db->query('SELECT disposition FROM capture_outcomes')->fetchColumn() === 'pending');
    echo json_encode(['checks' => $checks, 'state' => 'passed', 'production_connected' => false]).PHP_EOL;
} finally {
    if ($started) {
        $run([$bin.'/pg_ctl', '-D', $dir.'/data', '-m', 'immediate', '-w', 'stop']);
    }
    @unlink($root.'/'.$fixture);
}
