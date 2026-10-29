<?php

use Gunreip\TranslationWorkbench\Generation\CompleteRescanWriter;
use Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchScanner;
use Illuminate\Config\Repository;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

$root = dirname(__DIR__, 3);
require $root.'/vendor/autoload.php';
umask(0077);
$read = static fn ($p) => json_decode(file_get_contents($p), true, flags: JSON_THROW_ON_ERROR);
if (($argv[1] ?? null) === 'worker') {
    $jobPath = realpath($argv[2] ?? '');
    if (! $jobPath || ! str_starts_with($jobPath, $root.'/storage/app/private/dev/workbench-rehearsal/') || basename($jobPath) !== 'job.json') {
        throw new RuntimeException('Private rehearsal job required');
    }
    $job = $read($jobPath);
    $work = dirname($jobPath);
    $cluster = realpath($job['cluster']);
    $label = $argv[3] ?? '';
    if (! $cluster || dirname($cluster) !== realpath(sys_get_temp_dir()) || ! preg_match('/^tw-current-rescan-[a-f0-9]{16}$/D', basename($cluster))
        || ! preg_match('/^[a-z]+$/D', $label) || ! preg_match('/^rescan_[a-f0-9]{12}$/D', $job['database'])) {
        throw new RuntimeException('Invalid isolated worker target');
    }
    $app = new Application($work.'/source');
    $app->instance('config', new Repository(['translation-workbench' => $job['scope']]));
    $app->instance('files', new Filesystem);
    Facade::setFacadeApplication($app);
    $db = new PDO('pgsql:host='.$cluster.'/socket;dbname='.$job['database'].';user=rehearsal', options: [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    if ($db->query('SHOW data_directory')->fetchColumn() !== $cluster.'/data' || $db->query('SHOW listen_addresses')->fetchColumn() !== '') {
        throw new RuntimeException('Wrong worker cluster');
    }
    $db->exec("SET search_path TO current_generation_v2,pg_catalog; SET lock_timeout='20s'; SET statement_timeout='25s'");
    file_put_contents($work.'/'.$label.'.pid', (string) $db->query('SELECT pg_backend_pid()')->fetchColumn());
    $writer = new CompleteRescanWriter($db, $app->make(TranslationWorkbenchScanner::class));
    $hold = ($argv[4] ?? '') === 'hold';
    try {
        $result = $writer->apply($job['operations'][$label], $job['request'], $hold ? static function ($path, $result) use ($work, $label) {
            if ($result['moved'] === []) {
                return;
            }
            file_put_contents($work.'/'.$label.'.held', 'mutation executed, transaction open');
            $deadline = microtime(true) + 20;
            while (! is_file($work.'/'.$label.'.release')) {
                if (microtime(true) > $deadline) {
                    throw new RuntimeException('Barrier timeout');
                }
                usleep(10000);
            }
        } : null);
        file_put_contents($work.'/'.$label.'.result', json_encode(['state' => 'completed', 'result' => $result], JSON_THROW_ON_ERROR));
        exit(0);
    } catch (Throwable $error) {
        file_put_contents($work.'/'.$label.'.result', json_encode(['state' => 'rejected', 'error' => $error->getMessage()], JSON_THROW_ON_ERROR));
        exit(2);
    }
}
$baseline = $read($root.'/storage/app/private/workbench-previews/current-generation.json');
$backup = $root.'/'.$baseline['input_directory'];
if ($baseline['state'] !== 'current_generation_imported_and_verified'
    || hash_file('sha256', $backup.'/generation.dump') !== $baseline['dump_sha256']) {
    throw new RuntimeException('Verified current generation required');
}
$manifest = $read($backup.'/backup-manifest.json');
$cluster = sys_get_temp_dir().'/tw-current-rescan-'.bin2hex(random_bytes(8));
mkdir($cluster, 0700);
mkdir($cluster.'/socket', 0700);
$run = static function (array $args) use ($root): void {
    $p = new Process($args, $root, ['PGOPTIONS' => '', 'PGPASSWORD' => false, 'PGPASSFILE' => false]);
    $p->setTimeout(1800);
    $p->mustRun();
};
$bin = '/usr/lib/postgresql/18/bin';
$name = 'rescan_'.bin2hex(random_bytes(6));
$work = $backup.'/'.$name;
mkdir($work, 0700);
$path = 'packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/findings/table-work-findings/cell-literal.blade.php';
$original = file_get_contents($backup.'/source/'.$path);
foreach ([$path => $manifest['source_files'][$path]] as $sourcePath => $proof) {
    $bytes = file_get_contents($backup.'/source/'.$sourcePath);
    if (hash('sha256', $bytes) !== $proof['sha256']) {
        throw new RuntimeException('Saved source changed');
    }
    $target = $work.'/source/'.$sourcePath;
    if (! is_dir(dirname($target))) {
        mkdir(dirname($target), 0700, true);
    }
    file_put_contents($target, $bytes);
}
$absolute = $work.'/source/'.$path;
$app = new Application($work.'/source');
$app->instance('config', new Repository(['translation-workbench' => []]));
$app->instance('files', new Filesystem);
Facade::setFacadeApplication($app);
$lang = [];
$captures = fopen($backup.'/candidates.jsonl', 'r');
while (($line = fgets($captures)) !== false) {
    $capture = json_decode($line, true, flags: JSON_THROW_ON_ERROR);
    if ($capture['reason'] === 'key_with_exact_lang_evidence') {
        $lang[$capture['found_translation_key']] = ['value' => $capture['suggested_literal'], 'provenance' => $capture['provenance']['suggested_literal']];
    }
}
fclose($captures);
$scanner = $app->make(TranslationWorkbenchScanner::class);

$report = ['state' => 'incomplete', 'production_connected' => false, 'input_dump_sha256' => $baseline['dump_sha256'], 'source_path' => $path, 'cases' => [], 'assertions' => 0];
$check = static function (bool $ok, string $message) use (&$report): void {
    if (! $ok) {
        throw new RuntimeException($message);
    }
    $report['assertions']++;
};
$started = false;
try {
    $run([$bin.'/initdb', '-D', $cluster.'/data', '-U', 'rehearsal', '--auth-local=trust', '--auth-host=reject', '--no-locale', '--encoding=UTF8']);
    file_put_contents($cluster.'/data/postgresql.conf', "\nlisten_addresses=''\nunix_socket_directories='".$cluster."/socket'\nunix_socket_permissions=0700\n", FILE_APPEND);
    $run([$bin.'/pg_ctl', '-D', $cluster.'/data', '-l', $cluster.'/server.log', '-w', 'start']);
    $started = true;
    $run([$bin.'/createdb', '-h', $cluster.'/socket', '-U', 'rehearsal', $name]);
    $run([$bin.'/pg_restore', '-h', $cluster.'/socket', '-U', 'rehearsal', '--exit-on-error', '--no-owner', '--no-privileges', '-d', $name, $backup.'/generation.dump']);
    $db = new PDO('pgsql:host='.$cluster.'/socket;dbname='.$name.';user=rehearsal', options: [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $db->exec('SET search_path TO current_generation_v2,pg_catalog');
    $check($db->query('SHOW data_directory')->fetchColumn() === $cluster.'/data' && $db->query('SHOW listen_addresses')->fetchColumn() === '' && $db->query('SELECT current_database()')->fetchColumn() === $name, 'Wrong database');
    $fingerprint = static function () use ($db): array {
        $tables = $db->query("SELECT tablename FROM pg_tables WHERE schemaname='current_generation_v2' ORDER BY tablename")->fetchAll(PDO::FETCH_COLUMN);
        $hashes = [];
        foreach ($tables as $table) {
            $hashes[$table] = $db->query('SELECT md5(coalesce(string_agg(row_to_json(t)::text,chr(10) ORDER BY row_to_json(t)::text),\'\')) FROM "current_generation_v2"."'.$table.'" t')->fetchColumn();
        }

        return $hashes;
    };
    $scope = ['paths' => [dirname($path)]];
    $app['config']->set('translation-workbench', $scope);
    $writer = new CompleteRescanWriter($db, $scanner);
    $wait = static function (callable $predicate, string $message): void {
        $end = microtime(true) + 15;
        while (! $predicate()) {
            if (microtime(true) > $end) {
                throw new RuntimeException($message);
            }
            usleep(10000);
        }
    };
    $workers = [];
    $start = static function (string $label, bool $hold = false) use ($work, $root, &$workers): Process {
        $p = new Process([PHP_BINARY, __FILE__, 'worker', $work.'/job.json', $label, $hold ? 'hold' : 'run'], $root,
            ['PGOPTIONS' => '', 'PGPASSWORD' => false, 'PGPASSFILE' => false]);
        $p->setTimeout(30);
        $p->start();
        $workers[] = $p;

        return $p;
    };
    $finish = static function (Process $p, string $label) use ($work, $read): array {
        $p->wait();
        if (! is_file($work.'/'.$label.'.result')) {
            throw new RuntimeException('Worker failed: '.$p->getErrorOutput());
        }

        return $read($work.'/'.$label.'.result');
    };
    $blocked = static function (string $label) use ($work, $db): bool {
        if (! is_file($work.'/'.$label.'.pid')) {
            return false;
        }
        $q = $db->prepare("SELECT count(*) FROM pg_stat_activity WHERE pid=? AND wait_event_type='Lock'");
        $q->execute([(int) file_get_contents($work.'/'.$label.'.pid')]);

        return (int) $q->fetchColumn() === 1;
    };
    $scanCount = static fn () => (int) $db->query('SELECT count(*) FROM translation_workbench_scan_runs')->fetchColumn();
    $keyValues = static fn () => array_intersect_key($fingerprint(), array_flip(['translation_workbench_keys', 'translation_workbench_key_names', 'published_value_evidence']));
    $stable = $keyValues();
    foreach (['same_operation', 'different_operations', 'process_killed'] as $index => $case) {
        echo $case."...\n";
        file_put_contents($absolute, str_repeat("\n", $index + 2).$original);
        $request = $writer->prepare([dirname($path)], $lang);
        $firstId = (string) Str::uuid();
        $first = ['alpha', 'gamma', 'epsilon'][$index];
        $second = ['beta', 'delta', 'zeta'][$index];
        $job = ['cluster' => $cluster, 'database' => $name, 'scope' => $scope, 'request' => $request,
            'operations' => [$first => $firstId, $second => $case === 'different_operations' ? (string) Str::uuid() : $firstId]];
        file_put_contents($work.'/job.json', json_encode($job, JSON_THROW_ON_ERROR));
        $before = $fingerprint();
        $count = $scanCount();
        $p = $start($first, true);
        $wait(fn () => is_file($work.'/'.$first.'.held'), 'First worker did not reach mutation barrier');
        $check($fingerprint() === $before, 'Uncommitted mutation visible externally');
        if ($case === 'process_killed') {
            $pid = (int) file_get_contents($work.'/'.$first.'.pid');
            $p->signal(9); // Kill only the child created by this test, after DB writes and before commit.
            $p->wait();
            $wait(static function () use ($db, $pid): bool {
                $q = $db->prepare('SELECT count(*) FROM pg_stat_activity WHERE pid=?');
                $q->execute([$pid]);

                return (int) $q->fetchColumn() === 0;
            }, 'Killed worker backend did not release transaction');
            $check($fingerprint() === $before && $scanCount() === $count, 'Killed process left partial rows');
            $check(! is_file($work.'/'.$first.'.result'), 'Killed worker unexpectedly completed');
            $secondResult = $finish($start($second), $second);
            $check($secondResult['state'] === 'completed', 'Restart after process kill failed');
            $firstResult = ['state' => 'killed_before_commit'];
        } else {
            $q = $start($second);
            $wait(fn () => $blocked($second), 'Second worker did not wait on database lock');
            $check(! is_file($work.'/'.$second.'.result'), 'Second worker completed while first held lock');
            file_put_contents($work.'/'.$first.'.release', 'continue');
            $firstResult = $finish($p, $first);
            $secondResult = $finish($q, $second);
            $check($firstResult['state'] === 'completed', 'First operation failed');
            if ($case === 'same_operation') {
                $check($secondResult == $firstResult, 'Same concurrent operation did not replay result');
            } else {
                $check($secondResult['state'] === 'rejected' && $secondResult['error'] === 'Stale rescan database state', 'Competing stale operation was not rejected');
            }
        }
        $check($scanCount() === $count + 1, 'Expected exactly one new scan');
        $completed = $case === 'process_killed' ? $secondResult['result'] : $firstResult['result'];
        $check(count($completed['moved']) === 1 && $completed['created'] === [], 'Movement not applied exactly once');
        $move = $completed['moved'][0];
        $check((int) $db->query('SELECT count(*) FROM translation_workbench_findings WHERE predecessor_id='.(int) $move['predecessor'])->fetchColumn() === 1, 'Duplicate successors');
        $check((int) $db->query('SELECT count(*) FROM translation_workbench_key_findings WHERE finding_id='.(int) $move['successor'].' AND valid_to IS NULL')->fetchColumn() === 1, 'Missing successor key link');
        $after = $fingerprint();
        $check($writer->apply($firstId, $request) == $completed && $fingerprint() === $after, 'Retry after child completion changed rows');
        $report['cases'][$case] = ['first' => $firstResult, 'second' => $secondResult, 'exactly_one_new_scan' => true];
    }
    $check($stable === $keyValues(), 'Keys or published values changed');
    $check(hash_file('sha256', $backup.'/source/'.$path) === hash('sha256', $original), 'Saved source changed');
    $report['implementation_sha256'] = hash_file('sha256', $root.'/packages/gunreip/laravel-translation-workbench/src/Generation/CompleteRescanWriter.php');
    $report['runner_sha256'] = hash_file('sha256', __FILE__);

    $report['dump'] = substr($work, strlen($root) + 1).'/database.dump';
    $run([$bin.'/pg_dump', '-h', $cluster.'/socket', '-U', 'rehearsal', '-Fc', '-f', $root.'/'.$report['dump'], $name]);
    $report['dump_sha256'] = hash_file('sha256', $root.'/'.$report['dump']);
    $report['state'] = 'concurrent_rescans_verified';
    $report['generation'] = $baseline['generation'];
    $report['limitations'] = ['Isolated writer only; no production command connected', 'Concurrency exercised on scoped scan with real writes, not timed full-project load test', 'UI review decisions and new key assignment still pending'];
} catch (Throwable $e) {
    $report['state'] = 'failed';
    $report['error'] = $e->getMessage();
} finally {
    foreach ($workers ?? [] as $worker) {
        if ($worker->isRunning()) {
            $worker->stop(0, 9);
        }
    }
    if ($started) {
        $run([$bin.'/pg_ctl', '-D', $cluster.'/data', '-m', 'fast', '-w', 'stop']);
        $report['cluster_stopped'] = true;
    }
    file_put_contents($work.'/result.json', json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)."\n");
}
echo json_encode(['state' => $report['state'], 'assertions' => $report['assertions'], 'error' => $report['error'] ?? null, 'report' => substr($work, strlen($root) + 1).'/result.json'], JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR)."\n";
exit($report['state'] === 'concurrent_rescans_verified' ? 0 : 1);
