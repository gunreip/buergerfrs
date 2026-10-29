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
foreach ($manifest['source_files'] as $sourcePath => $proof) {
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
    $before = $fingerprint();
    $originalTables = $before;
    $outside = static function () use ($db, $path): string {
        $q = $db->prepare("SELECT md5(coalesce(string_agg(row_to_json(f)::text,chr(10) ORDER BY f.id),'')) FROM translation_workbench_findings f JOIN translation_workbench_source_files s ON s.id=f.source_file_id WHERE s.path<>?");
        $q->execute([$path]);

        return $q->fetchColumn();
    };
    $outsideBefore = $outside();
    $scope = $read($root.'/storage/app/private/workbench-previews/current-scan-'.$baseline['source_run'].'/manifest.json')['scope'];
    $app['config']->set('translation-workbench', $scope);
    $writer = new CompleteRescanWriter($db, $scanner);
    $reject = static function (callable $action, string $expected) use ($check): void {
        try {
            $action();
        } catch (RuntimeException $e) {
            $check($e->getMessage() === $expected, 'Unexpected rejection: '.$e->getMessage());

            return;
        }
        throw new RuntimeException('Expected rejection: '.$expected);
    };
    echo "Preparing complete scan...\n";
    $request = $writer->prepare($scope['paths'], $lang);
    $operation = (string) Str::uuid();
    echo "Applying complete scan...\n";
    $full = $writer->apply($operation, $request);
    $check($full['created'] === [] && $full['moved'] === [] && $full['removed'] === [], 'Unchanged scan altered findings');
    // Pure @js serialization is excluded; the current scanner leaves 32 review files.
    $check($full['confirmed'] === 18914 && count($full['review_files']) === 32, 'Unexpected baseline accounting');
    $saved = json_decode($db->query("SELECT scope_snapshot FROM translation_workbench_scan_runs WHERE id='$operation'")->fetchColumn(), true, flags: JSON_THROW_ON_ERROR);
    $check(count($saved['reviews']) === 32 && $saved['inventory'] == $request['plan']['inventory'], 'Review and inventory evidence missing');
    $after = $fingerprint();
    $check($writer->apply($operation, $request) == $full && $fingerprint() === $after, 'Repeated operation changed data');
    $tampered = $request;
    $tampered['paths'] = [];
    $reject(fn () => $writer->apply($operation, $tampered), 'Operation request changed');
    $check($fingerprint() === $after, 'Changed replay wrote data');
    $report['full_scan'] = $full;
    echo "Checking interruption and restart...\n";
    $directory = dirname($path);
    file_put_contents($absolute, "\n\n".$original);
    $request = $writer->prepare([$directory], $lang);
    $operation = (string) Str::uuid();
    $before = $fingerprint();
    $reject(fn () => $writer->apply($operation, $request, static function ($changedPath, $result) use ($path) {
        if ($changedPath === $path && $result['moved'] !== []) {
            throw new RuntimeException('Injected interruption');
        }
    }), 'Injected interruption');
    $check($fingerprint() === $before, 'Interrupted scan left partial rows');
    $moved = $writer->apply($operation, $request);
    $check(count($moved['moved']) === 1 && $moved['created'] === [], 'Restart did not move finding exactly once');
    $successor = $moved['moved'][0]['successor'];
    $predecessor = $moved['moved'][0]['predecessor'];
    $check((int) $db->query('SELECT predecessor_id FROM translation_workbench_findings WHERE id='.$successor)->fetchColumn() === (int) $predecessor, 'Predecessor lost');
    $check((int) $db->query('SELECT count(*) FROM translation_workbench_key_findings WHERE finding_id='.$successor.' AND valid_to IS NULL')->fetchColumn() === 1, 'Active key link lost');
    $after = $fingerprint();
    $check($writer->apply($operation, $request) == $moved && $fingerprint() === $after, 'Retry duplicated movement');
    $report['movement'] = $moved;
    $request = $writer->prepare([$directory], $lang);
    $before = $fingerprint();
    file_put_contents($absolute, "\n\n\n".$original);
    $reject(fn () => $writer->apply((string) Str::uuid(), $request), 'Stale rescan source plan');
    $check($fingerprint() === $before, 'Stale source changed rows');
    file_put_contents($absolute, "\n\n".$original);
    $operation = (string) Str::uuid();
    $reject(fn () => $writer->apply($operation, $request, static function ($changedPath) use ($path, $absolute, $original) {
        if ($changedPath === $path) {
            file_put_contents($absolute, "\n\n\n".$original);
        }
    }), 'Source changed during rescan application');
    $check($fingerprint() === $before, 'Concurrent source edit left partial rows');
    file_put_contents($absolute, "\n\n".$original);
    $writer->apply($operation, $request);
    $after = $fingerprint();
    $reject(fn () => $writer->apply((string) Str::uuid(), $request), 'Stale rescan database state');
    $check($fingerprint() === $after, 'Stale database plan wrote data');
    echo "Checking review preservation and file deletion...\n";
    file_put_contents($absolute, '<?php __($unknown);');
    $request = $writer->prepare([$directory], $lang);
    $review = $writer->apply((string) Str::uuid(), $request);
    $check(in_array($path, $review['review_files'], true) && $review['removed'] === [], 'Unresolved file retired findings');
    $check($db->query('SELECT status FROM translation_workbench_findings WHERE id='.$successor)->fetchColumn() === 'active', 'Review changed finding status');
    unlink($absolute); // Private owned copy only.
    $request = $writer->prepare([$directory], $lang);
    $deleted = $writer->apply((string) Str::uuid(), $request);
    $check($deleted['removed'] === [$successor], 'Confirmed missing file did not retire finding');
    $check((int) $db->query('SELECT count(*) FROM translation_workbench_key_findings WHERE finding_id='.$successor.' AND valid_to IS NULL')->fetchColumn() === 0, 'Removed finding retained active link');
    file_put_contents($absolute, $original);
    $request = $writer->prepare([$directory], $lang);
    $new = $writer->apply((string) Str::uuid(), $request);
    $check(count($new['created']) === 1 && $new['created'][0] !== $successor && $new['moved'] === [], 'Reappearance reactivated archived finding');
    $check($db->query('SELECT predecessor_id FROM translation_workbench_findings WHERE id='.$new['created'][0])->fetchColumn() === null, 'Reappearance guessed ancestry');
    $report['review'] = $review;
    $report['deleted'] = $deleted;
    $report['reappeared'] = $new;
    $check(hash_file('sha256', $backup.'/source/'.$path) === hash('sha256', $original), 'Saved source changed');
    $check((int) $db->query('SELECT count(*) FROM translation_workbench_events')->fetchColumn() === 0, 'Invented user timeline events');
    $after = $fingerprint();
    foreach (['translation_workbench_keys', 'translation_workbench_key_names', 'published_value_evidence'] as $table) {
        $check($after[$table] === $originalTables[$table], 'Existing keys or values changed: '.$table);
    }
    $check($outside() === $outsideBefore, 'Unrelated findings changed');
    $report['production_connected'] = false;
    $report['implementation_sha256'] = hash_file('sha256', $root.'/packages/gunreip/laravel-translation-workbench/src/Generation/CompleteRescanWriter.php');
    $report['runner_sha256'] = hash_file('sha256', __FILE__);

    $report['dump'] = substr($work, strlen($root) + 1).'/database.dump';
    $run([$bin.'/pg_dump', '-h', $cluster.'/socket', '-U', 'rehearsal', '-Fc', '-f', $root.'/'.$report['dump'], $name]);
    $report['dump_sha256'] = hash_file('sha256', $root.'/'.$report['dump']);
    $report['state'] = 'complete_rescan_application_verified';
    $report['generation'] = $baseline['generation'];
    $report['limitations'] = ['Isolated rehearsal writer, not production command', 'Review cases persisted in scan snapshot, not yet editable in UI', 'New key occurrences remain unassigned', 'Real concurrent process execution not tested'];
} catch (Throwable $e) {
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
exit($report['state'] === 'complete_rescan_application_verified' ? 0 : 1);
