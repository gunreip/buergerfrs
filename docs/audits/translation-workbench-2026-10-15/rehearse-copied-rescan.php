<?php

use Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchScanner;
use Illuminate\Config\Repository;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Facade;
use Symfony\Component\Process\Process;
use Tests\Support\TranslationWorkbench\CopiedSourceRescan;

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
$name = 'rescan_'.bin2hex(random_bytes(6));
$work = $backup.'/'.$name;
mkdir($work, 0700);
$path = 'packages/gunreip/laravel-translation-workbench/resources/views/components/ui/tw-graph/code-box.blade.php';
$original = file_get_contents($backup.'/source/'.$path);
if (hash('sha256', $original) !== $manifest['source_files'][$path]['sha256']) {
    throw new RuntimeException('Original source changed');
}
$absolute = $work.'/source/'.$path;
mkdir(dirname($absolute), 0700, true);
file_put_contents($absolute, $original);
$app = new Application($work.'/source');
$app->instance('config', new Repository(['translation-workbench' => []]));
$app->instance('files', new Filesystem);
Facade::setFacadeApplication($app);
$scanner = $app->make(TranslationWorkbenchScanner::class);
$service = new CopiedSourceRescan;
$report = ['state' => 'incomplete', 'production_connected' => false, 'input_dump_sha256' => $baseline['dump_sha256'], 'source_path' => $path, 'cases' => [], 'assertions' => 0];
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
    $stmt = $db->prepare('SELECT f.* FROM translation_workbench_findings f JOIN translation_workbench_source_files s ON s.id=f.source_file_id WHERE s.path=?');
    $stmt->execute([$path]);
    $old = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $check(count($old) === 1, 'Expected one source finding');
    $old = $old[0];
    $fileId = (int) $old['source_file_id'];
    $outside = static fn () => $db->query('SELECT md5(string_agg(row_to_json(f)::text,chr(10) ORDER BY id)) FROM translation_workbench_findings f WHERE source_file_id<>'.$fileId)->fetchColumn();
    $outsideBefore = $outside();
    $scan = static function (string $case, string $content) use ($absolute, $path, $db, $scanner, $service, &$report): array {
        file_put_contents($absolute, $content);
        $result = $service->scan($db, $scanner, $absolute, $path);
        $report['cases'][$case] = [...$result, 'source_sha256' => hash('sha256', $content)];

        return $result;
    };
    $a = $scan('unchanged', $original);
    $b = $scan('unchanged_again', $original);
    $check($a['findings'] === [(int) $old['id']] && $b['findings'] === $a['findings'] && $a['scan'] !== $b['scan'], 'Unchanged scan must confirm same finding with new scan');
    $moved = "\n\n".$original;
    $m = $scan('moved', $moved);
    $check($m['moved'] === 1 && $m['findings'][0] !== (int) $old['id'], 'Movement requires new finding');
    $new = $db->query('SELECT * FROM translation_workbench_findings WHERE id='.$m['findings'][0])->fetch(PDO::FETCH_ASSOC);
    $check((int) $new['predecessor_id'] === (int) $old['id'] && (int) $new['source_line'] === (int) $old['source_line'] + 2 && $new['suggested_key'] === $old['suggested_key'], 'Movement provenance or immutable suggestion lost');
    $check($db->query('SELECT status FROM translation_workbench_findings WHERE id='.(int) $old['id'])->fetchColumn() === 'obsolete', 'Predecessor not archived');
    $copied = $moved."\n{{ __('Code example') }}\n";
    $c = $scan('additional_copy', $copied);
    $check($c['confirmed'] === 1 && $c['created'] === 1 && $c['moved'] === 0 && count($c['findings']) === 2, 'Copy must become distinct finding');
    $before = (int) $db->query('SELECT count(*) FROM translation_workbench_scan_runs')->fetchColumn();
    file_put_contents($absolute, "\n".$copied);
    $blocked = false;
    try {
        $service->scan($db, $scanner, $absolute, $path);
    } catch (RuntimeException $e) {
        $blocked = $e->getMessage() === 'Ambiguous movement blocks reconciliation';
    }
    $check($blocked && (int) $db->query('SELECT count(*) FROM translation_workbench_scan_runs')->fetchColumn() === $before, 'Ambiguous duplicate movement must not guess or write');
    $d = $scan('removed', '{{-- all calls removed in this rehearsal --}}');
    $check($d['removed'] === 2 && $d['findings'] === [], 'Removal not archived');
    $r = $scan('reappeared', $original);
    $fresh = $db->query('SELECT * FROM translation_workbench_findings WHERE id='.$r['findings'][0])->fetch(PDO::FETCH_ASSOC);
    $check($r['created'] === 1 && $fresh['predecessor_id'] === null && ! in_array($r['findings'][0], [(int) $old['id'], ...$c['findings']], true), 'Reappearance must not reactivate obsolete finding');
    $changed = str_replace("__('Code example')", "__('Changed example')", $original);
    $e = $scan('literal_changed', $changed);
    $check($e['created'] === 1 && $e['removed'] === 1 && $e['moved'] === 0, 'Changed literal is new capture, not movement');
    $before = (int) $db->query('SELECT count(*) FROM translation_workbench_scan_runs')->fetchColumn();
    file_put_contents($absolute, '<?php __($unknown);');
    $blocked = false;
    try {
        $service->scan($db, $scanner, $absolute, $path);
    } catch (RuntimeException $error) {
        $blocked = $error->getMessage() === 'Unresolved capture blocks reconciliation';
    }
    $check($blocked && (int) $db->query('SELECT count(*) FROM translation_workbench_scan_runs')->fetchColumn() === $before, 'Unresolved source must not authorize removal');
    $blocked = false;
    try {
        $service->scan($db, $scanner, $absolute.'.missing', $path);
    } catch (RuntimeException $error) {
        $blocked = $error->getMessage() === 'Missing file does not authorize removal';
    }
    $check($blocked, 'Absent path must not imply confirmed deletion');
    $app['config']->set('translation-workbench.exclude_paths', [$path]);
    $blocked = false;
    try {
        $service->scan($db, $scanner, $absolute, $path);
    } catch (RuntimeException $error) {
        $blocked = $error->getMessage() === 'Excluded file does not authorize removal';
    }
    $check($blocked && (int) $db->query('SELECT count(*) FROM translation_workbench_scan_runs')->fetchColumn() === $before, 'Excluded path must not imply deletion');
    $app['config']->set('translation-workbench.exclude_paths', []);
    $check($outside() === $outsideBefore, 'Out-of-scope findings changed');
    $check(hash_file('sha256', $backup.'/source/'.$path) === hash('sha256', $original), 'Saved source changed');
    $report['rejected_cases'] = ['ambiguous_duplicate_movement', 'dynamic_unresolved_capture', 'missing_file', 'excluded_file'];
    $report['implementation_sha256'] = hash_file('sha256', $root.'/tests/Support/TranslationWorkbench/CopiedSourceRescan.php');
    $report['runner_sha256'] = hash_file('sha256', __FILE__);
    $report['outside_findings_unchanged'] = true;
    $report['dump'] = substr($work, strlen($root) + 1).'/database.dump';
    $run([$bin.'/pg_dump', '-h', $cluster.'/socket', '-U', 'rehearsal', '-Fc', '-f', $root.'/'.$report['dump'], $name]);
    $report['dump_sha256'] = hash_file('sha256', $root.'/'.$report['dump']);
    $report['state'] = 'rescan_rehearsal_verified';
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
exit($report['state'] === 'rescan_rehearsal_verified' ? 0 : 1);
