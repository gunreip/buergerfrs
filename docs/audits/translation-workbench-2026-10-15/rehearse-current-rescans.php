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
    $run([$bin.'/initdb', '-D', $cluster.'/data', '-U', 'rehearsal', '--auth-local=trust', '--auth-host=reject', '--no-locale', '--encoding=UTF8']);
    file_put_contents($cluster.'/data/postgresql.conf', "\nlisten_addresses=''\nunix_socket_directories='".$cluster."/socket'\nunix_socket_permissions=0700\n", FILE_APPEND);
    $run([$bin.'/pg_ctl', '-D', $cluster.'/data', '-l', $cluster.'/server.log', '-w', 'start']);
    $started = true;
    $run([$bin.'/createdb', '-h', $cluster.'/socket', '-U', 'rehearsal', $name]);
    $run([$bin.'/pg_restore', '-h', $cluster.'/socket', '-U', 'rehearsal', '--exit-on-error', '--no-owner', '--no-privileges', '-d', $name, $backup.'/generation.dump']);
    $db = new PDO('pgsql:host='.$cluster.'/socket;dbname='.$name.';user=rehearsal', options: [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $db->exec('SET search_path TO current_generation_v2,pg_catalog');
    $check($db->query('SHOW data_directory')->fetchColumn() === $cluster.'/data' && $db->query('SHOW listen_addresses')->fetchColumn() === '' && $db->query('SELECT current_database()')->fetchColumn() === $name, 'Wrong database');
    $stmt = $db->prepare('SELECT f.* FROM translation_workbench_findings f JOIN translation_workbench_source_files s ON s.id=f.source_file_id WHERE s.path=?');
    $stmt->execute([$path]);
    $old = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $fingerprint = static function () use ($db): array {
        $tables = $db->query("SELECT tablename FROM pg_tables WHERE schemaname='current_generation_v2' ORDER BY tablename")->fetchAll(PDO::FETCH_COLUMN);
        $hashes = [];
        foreach ($tables as $table) {
            $hashes[$table] = $db->query('SELECT md5(coalesce(string_agg(row_to_json(t)::text,chr(10) ORDER BY row_to_json(t)::text),\'\')) FROM "current_generation_v2"."'.$table.'" t')->fetchColumn();
        }

        return $hashes;
    };

    $check(count($old) === 1, 'Expected one source finding');
    $old = $old[0];
    $fileId = (int) $old['source_file_id'];
    $outside = static fn () => $db->query('SELECT md5(string_agg(row_to_json(f)::text,chr(10) ORDER BY id)) FROM translation_workbench_findings f WHERE source_file_id<>'.$fileId)->fetchColumn();
    $outsideBefore = $outside();
    $activeLink = static function (int $id) use ($db): ?int {
        $q = $db->prepare('SELECT key_id FROM translation_workbench_key_findings WHERE finding_id=? AND valid_to IS NULL');
        $q->execute([$id]);
        $value = $q->fetchColumn();

        return $value === false ? null : (int) $value;
    };
    $key = $activeLink((int) $old['id']);
    $check($key !== null, 'Expected a linked original key');
    $keysBefore = $db->query('SELECT md5(string_agg(row_to_json(k)::text,chr(10) ORDER BY id)) FROM translation_workbench_keys k')->fetchColumn();
    $valuesBefore = $db->query('SELECT md5(string_agg(row_to_json(v)::text,chr(10) ORDER BY locale,full_name)) FROM published_value_evidence v')->fetchColumn();
    $scan = static function (string $case, string $content) use ($absolute, $path, $db, $scanner, $service, $lang, &$report): array {
        file_put_contents($absolute, $content);
        $result = $service->scan($db, $scanner, $absolute, $path, $lang);
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
    $check($activeLink($m['findings'][0]) === $key && $activeLink((int) $old['id']) === null, 'Movement did not transfer key link');
    $check($new['raw_source_code_literal'] === $old['raw_source_code_literal'] && $new['raw_literal'] === $old['raw_literal'] && $new['suggested_literal'] === $old['suggested_literal'], 'Movement changed captured raw values');
    $copied = $moved."\n{{ __('ui.suggest.suggested') }}\n";
    $c = $scan('additional_copy', $copied);
    $check($c['confirmed'] === 1 && $c['created'] === 1 && $c['moved'] === 0 && count($c['findings']) === 2, 'Copy must become distinct finding');
    $copyIds = array_values(array_diff($c['findings'], $m['findings']));
    $check(count($copyIds) === 1 && $activeLink($copyIds[0]) === null && $activeLink($m['findings'][0]) === $key, 'Copy must remain separate without guessed assignment');
    $before = (int) $db->query('SELECT count(*) FROM translation_workbench_scan_runs')->fetchColumn();
    $beforeRejected = $fingerprint();
    file_put_contents($absolute, "\n".$copied);
    $blocked = false;
    try {
        $service->scan($db, $scanner, $absolute, $path, $lang);
    } catch (RuntimeException $e) {
        $blocked = $e->getMessage() === 'Ambiguous movement blocks reconciliation';
    }
    $check($blocked && (int) $db->query('SELECT count(*) FROM translation_workbench_scan_runs')->fetchColumn() === $before, 'Ambiguous duplicate movement must not guess or write');
    $check($fingerprint() === $beforeRejected, 'Ambiguous movement changed database rows');
    $d = $scan('removed', '{{-- all calls removed in this rehearsal --}}');
    $check($d['removed'] === 2 && $d['findings'] === [], 'Removal not archived');
    $check($activeLink($m['findings'][0]) === null, 'Removed finding kept active link');
    $r = $scan('reappeared', $original);
    $fresh = $db->query('SELECT * FROM translation_workbench_findings WHERE id='.$r['findings'][0])->fetch(PDO::FETCH_ASSOC);
    $check($r['created'] === 1 && $fresh['predecessor_id'] === null && ! in_array($r['findings'][0], [(int) $old['id'], ...$c['findings']], true), 'Reappearance must not reactivate obsolete finding');
    $check($activeLink($r['findings'][0]) === null, 'Reappearance inherited retired link');
    $changed = str_replace("__('ui.suggest.suggested')", "__('Changed example')", $original);
    $e = $scan('literal_changed', $changed);
    $check($e['created'] === 1 && $e['removed'] === 1 && $e['moved'] === 0, 'Changed literal is new capture, not movement');
    $before = (int) $db->query('SELECT count(*) FROM translation_workbench_scan_runs')->fetchColumn();
    $beforeRejected = $fingerprint();
    file_put_contents($absolute, '<?php __($unknown);');
    $blocked = false;
    try {
        $service->scan($db, $scanner, $absolute, $path, $lang);
    } catch (RuntimeException $error) {
        $blocked = $error->getMessage() === 'Unresolved capture blocks reconciliation';
    }
    $check($blocked && (int) $db->query('SELECT count(*) FROM translation_workbench_scan_runs')->fetchColumn() === $before, 'Unresolved source must not authorize removal');
    $blocked = false;
    try {
        $service->scan($db, $scanner, $absolute.'.missing', $path, $lang);
    } catch (RuntimeException $error) {
        $blocked = $error->getMessage() === 'Missing file does not authorize removal';
    }
    $check($blocked, 'Absent path must not imply confirmed deletion');
    $app['config']->set('translation-workbench.exclude_paths', [$path]);
    $blocked = false;
    try {
        $service->scan($db, $scanner, $absolute, $path, $lang);
    } catch (RuntimeException $error) {
        $blocked = $error->getMessage() === 'Excluded file does not authorize removal';
    }
    $check($blocked && (int) $db->query('SELECT count(*) FROM translation_workbench_scan_runs')->fetchColumn() === $before, 'Excluded path must not imply deletion');
    $check($fingerprint() === $beforeRejected, 'Rejected capture, missing or excluded file changed database rows');
    $app['config']->set('translation-workbench.exclude_paths', []);
    $repeat = $scan('changed_literal_unchanged', $changed);
    $check($repeat['findings'] === $e['findings'] && $repeat['created'] === 0 && $repeat['moved'] === 0 && $repeat['removed'] === 0, 'Literal rescan duplicated data');
    $literalChanged = str_replace("__('Changed example')", "__('Another literal')", $changed);
    $lc = $scan('literal_replaced', $literalChanged);
    $check($lc['created'] === 1 && $lc['removed'] === 1 && $lc['moved'] === 0, 'Different literal must not be treated as movement');
    $literal = $db->query('SELECT * FROM translation_workbench_findings WHERE id='.$lc['findings'][0])->fetch(PDO::FETCH_ASSOC);
    $check($literal['raw_literal'] === 'Another literal' && $literal['suggested_key'] !== null && $literal['predecessor_id'] === null, 'New literal evidence missing');
    $columnChanged = str_replace("__('Another literal')", "    __('Another literal')", $literalChanged);
    $cm = $scan('column_moved', $columnChanged);
    $columnRow = $db->query('SELECT * FROM translation_workbench_findings WHERE id='.$cm['findings'][0])->fetch(PDO::FETCH_ASSOC);
    $check($cm['moved'] === 1 && (int) $columnRow['source_line'] === (int) $literal['source_line'] && (int) $columnRow['source_column'] === (int) $literal['source_column'] + 4, 'Column movement not recorded');
    $check($columnRow['suggested_key'] === $literal['suggested_key'] && (int) $columnRow['predecessor_id'] === (int) $literal['id'], 'Literal movement changed suggestion or predecessor');
    $commented = $scan('commented_out', "{{-- {{ __('Another literal') }} --}}");
    $check($commented['findings'] === [] && $commented['removed'] === 1 && $commented['excluded'] === 1, 'Commented occurrence remains active');
    $empty = $scan('commented_out_again', "{{-- {{ __('Another literal') }} --}}");
    $check($empty['removed'] === 0 && $empty['created'] === 0, 'Repeated comment scan created or retired findings again');
    $back = $scan('literal_reappeared', $columnChanged);
    $backRow = $db->query('SELECT * FROM translation_workbench_findings WHERE id='.$back['findings'][0])->fetch(PDO::FETCH_ASSOC);
    $check($back['created'] === 1 && $backRow['predecessor_id'] === null && $back['findings'][0] !== $cm['findings'][0], 'Archived literal reactivated');
    $final = $scan('final_unchanged', $columnChanged);
    $check($final['findings'] === $back['findings'] && $final['created'] === 0 && $final['removed'] === 0, 'Final scan did not stabilize');
    $storedOld = $db->query('SELECT * FROM translation_workbench_findings WHERE id='.(int) $old['id'])->fetch(PDO::FETCH_ASSOC);
    foreach (['raw_expression', 'raw_source_code_literal', 'raw_literal', 'suggested_literal', 'suggested_key', 'source_line', 'source_column'] as $field) {
        $check($storedOld[$field] === $old[$field], 'Archived original changed: '.$field);
    }
    $check((int) $db->query('SELECT count(*) FROM translation_workbench_scan_runs')->fetchColumn() === 1 + count($report['cases']), 'Scan history does not account for each completed run');
    $check((int) $db->query('SELECT count(*) FROM translation_workbench_key_findings l JOIN translation_workbench_findings f ON f.id=l.finding_id WHERE l.valid_to IS NULL AND f.status=\'obsolete\'')->fetchColumn() === 0, 'Archived findings still have active links');
    $check($db->query('SELECT md5(string_agg(row_to_json(k)::text,chr(10) ORDER BY id)) FROM translation_workbench_keys k')->fetchColumn() === $keysBefore, 'Keys changed');
    $check($db->query('SELECT md5(string_agg(row_to_json(v)::text,chr(10) ORDER BY locale,full_name)) FROM published_value_evidence v')->fetchColumn() === $valuesBefore, 'Published values changed');
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
    $report['generation'] = $baseline['generation'];
    $report['limitations'] = ['Rehearsal adapter, not the production command', 'No automatic assignment for a new key occurrence', 'No explicit file deletion or rename reconciliation yet', 'Scan history records outcomes; no user-authored timeline events fabricated'];
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
