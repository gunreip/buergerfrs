<?php

use Gunreip\TranslationWorkbench\Generation\CompleteRescanPlan;
use Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchScanner;
use Illuminate\Config\Repository;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Facade;
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
$service = new CompleteRescanPlan;
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
    $old = $db->query("SELECT f.*,s.path FROM translation_workbench_findings f JOIN translation_workbench_source_files s ON s.id=f.source_file_id WHERE f.status='active'")->fetchAll(PDO::FETCH_ASSOC);
    $fingerprint = static function () use ($db): array {
        $tables = $db->query("SELECT tablename FROM pg_tables WHERE schemaname='current_generation_v2' ORDER BY tablename")->fetchAll(PDO::FETCH_COLUMN);
        $hashes = [];
        foreach ($tables as $table) {
            $hashes[$table] = $db->query('SELECT md5(coalesce(string_agg(row_to_json(t)::text,chr(10) ORDER BY row_to_json(t)::text),\'\')) FROM "current_generation_v2"."'.$table.'" t')->fetchColumn();
        }

        return $hashes;
    };
    $before = $fingerprint();
    $scope = $read($root.'/storage/app/private/workbench-previews/current-scan-'.$baseline['source_run'].'/manifest.json')['scope'];
    $app['config']->set('translation-workbench', $scope);
    $plan = $service->build($scanner, $scope['paths'], $old, $lang);
    foreach ($plan['files'] as $file) {
        if ($file['state'] === 'ready') {
            $check($file['created'] === [] && $file['moved'] === [] && $file['removed'] === [], 'Unchanged full scan proposed changes');
        }
    }
    $report['full_scan'] = ['states' => $plan['states'], 'inventory_files' => count($plan['inventory']['hashes']),
        'confirmed' => array_sum(array_map(fn ($f) => count($f['confirmed'] ?? []), $plan['files'])),
        'preserved_for_review' => array_sum(array_map(fn ($f) => count($f['preserved'] ?? []), $plan['files']))];
    file_put_contents($work.'/full-plan.json', json_encode($plan, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));
    $directory = dirname($path);
    $runPlan = static function (string $case, array $paths) use ($service, $scanner, $old, $lang, &$report, $work): array {
        $plan = $service->build($scanner, $paths, $old, $lang);
        file_put_contents($work.'/'.$case.'.json', json_encode($plan, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));
        $report['cases'][$case] = ['states' => $plan['states'], 'relocations' => $plan['relocations']];

        return $plan;
    };
    file_put_contents($absolute, "\n\n".$original);
    $p = $runPlan('moved', [$directory]);
    $check(count($p['files'][$path]['moved']) === 1 && $p['files'][$path]['removed'] === [], 'Movement not planned');
    file_put_contents($absolute, '<?php __($unknown);');
    $p = $runPlan('unresolved', [$directory]);
    $check($p['files'][$path]['state'] === 'review' && count($p['files'][$path]['preserved']) === 1, 'Unresolved file not preserved');
    $check(($p['states']['ready'] ?? 0) > 0, 'Unresolved file blocked unrelated files');
    file_put_contents($absolute, '{{-- all calls removed --}}');
    $p = $runPlan('zero_hits', [$directory]);
    $check($p['files'][$path]['state'] === 'ready' && count($p['files'][$path]['removed']) === 1, 'Zero-hit file ignored');
    unlink($absolute); // Owned test copy only, original and saved baseline remain intact.
    $p = $runPlan('deleted_file', [$directory]);
    $check($p['files'][$path]['state'] === 'missing_confirmed' && count($p['files'][$path]['removed']) === 1, 'Deleted file not evidenced by completed inventory');
    $p = $runPlan('missing_explicit_path', [$path]);
    $check($p['files'][$path]['state'] === 'outside_scope', 'Missing explicit path authorized deletion');
    $app['config']->set('translation-workbench.exclude_paths', [$path]);
    $p = $runPlan('excluded_deleted_file', [$directory]);
    $check($p['files'][$path]['state'] === 'outside_scope', 'Exclusion authorized deletion');
    $app['config']->set('translation-workbench.exclude_paths', $scope['exclude_paths']);
    $renamed = $directory.'/renamed-label.blade.php';
    file_put_contents($work.'/source/'.$renamed, $original);
    $p = $runPlan('possible_rename', [$directory]);
    $check($p['files'][$path]['state'] === 'relocation_review' && $p['files'][$renamed]['state'] === 'relocation_review' && count($p['relocations']) === 1, 'Possible rename not held for review');
    file_put_contents($work.'/source/'.$directory.'/second-label.blade.php', $original);
    $p = $runPlan('ambiguous_rename', [$directory]);
    $check(count($p['relocations'][0]['candidates']) === 2, 'Ambiguous destinations collapsed');
    file_put_contents($absolute, $original);
    $p = $runPlan('additional_file_copy', [$directory]);
    $check($p['relocations'] === [] && count($p['files'][$path]['confirmed']) === 1 && count($p['files'][$renamed]['created']) === 1, 'Copy mistaken for relocation');
    $check($before === $fingerprint(), 'Read-only planning changed database rows');
    $check(hash_file('sha256', $backup.'/source/'.$path) === hash('sha256', $original), 'Saved source changed');
    $report['database_unchanged'] = true;
    $report['implementation_sha256'] = hash_file('sha256', $root.'/packages/gunreip/laravel-translation-workbench/src/Generation/CompleteRescanPlan.php');
    $report['full_plan_sha256'] = hash_file('sha256', $work.'/full-plan.json');
    $report['runner_sha256'] = hash_file('sha256', __FILE__);

    $report['dump'] = substr($work, strlen($root) + 1).'/database.dump';
    $run([$bin.'/pg_dump', '-h', $cluster.'/socket', '-U', 'rehearsal', '-Fc', '-f', $root.'/'.$report['dump'], $name]);
    $report['dump_sha256'] = hash_file('sha256', $root.'/'.$report['dump']);
    $report['state'] = 'complete_rescan_plan_verified';
    $report['generation'] = $baseline['generation'];
    $report['limitations'] = ['Read-only plan; no production command connected', 'Cross-file relocation requires a decision', 'Review files preserve all prior findings until resolved', 'No complete-plan persistence or concurrent application tested'];
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
exit($report['state'] === 'complete_rescan_plan_verified' ? 0 : 1);
