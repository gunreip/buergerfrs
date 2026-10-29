<?php

use Gunreip\TranslationWorkbench\Generation\CompleteRescanWriter;
use Gunreip\TranslationWorkbench\Generation\RescanCandidateRules;
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
        $q = $db->prepare("SELECT md5(coalesce(string_agg(row_to_json(f)::text,chr(10) ORDER BY f.id),'')) FROM translation_workbench_findings f JOIN translation_workbench_source_files s ON s.id=f.source_file_id WHERE s.path<>? AND s.path NOT LIKE 'resources/views/rescan-rule-cases/%'");
        $q->execute([$path]);

        return $q->fetchColumn();
    };
    $outsideBefore = $outside();
    $scope = $read($root.'/storage/app/private/workbench-previews/current-scan-'.$baseline['source_run'].'/manifest.json')['scope'];
    $app['config']->set('translation-workbench', $scope);
    $writer = new CompleteRescanWriter($db, $scanner);
    $rules = new RescanCandidateRules;
    $key = (int) $db->query("SELECT key_id FROM translation_workbench_key_names WHERE full_name='ui.suggest.suggested' AND valid_to IS NULL")->fetchColumn();
    $fixture = 'resources/views/rescan-rule-cases';
    mkdir($work.'/source/'.$fixture, 0700, true);
    $write = static function (string $file, string $text) use ($work, $fixture): void {
        file_put_contents($work.'/source/'.$fixture.'/'.$file.'.blade.php', $text);
    };
    $scan = static function (string $case) use ($writer, $fixture, $lang, &$report, $work): array {
        $request = $writer->prepare([$fixture], $lang);
        $id = (string) Str::uuid();
        $result = $writer->apply($id, $request);
        file_put_contents($work.'/'.$case.'.json', json_encode($result, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));
        $report['cases'][$case] = ['scan' => $id, 'created' => $result['created'], 'removed' => $result['removed'], 'key_assignments' => $result['key_assignments']];

        return $result;
    };
    $write('key-one', "{{ __('ui.suggest.suggested') }}");
    $first = $scan('exact_existing_key');
    $id = $first['created'][0];
    $check($first['key_assignments'][$id] === ['state' => 'existing_key_observed', 'key_id' => $key], 'Exact existing key not linked');
    $write('key-two', "{{ __('ui.suggest.suggested') }}");
    $second = $scan('additional_key_occurrence');
    $check(count($second['created']) === 1 && $second['created'][0] !== $id && $second['key_assignments'][$second['created'][0]]['key_id'] === $key, 'Second source occurrence not independently linked');
    $same = $scan('unchanged_key_occurrences');
    $check($same['created'] === [] && $same['confirmed'] === 2, 'Key rescan created duplicates');
    $capture = json_decode($db->query('SELECT provenance FROM translation_workbench_findings WHERE id='.$id)->fetchColumn(), true)['capture'];
    $bad = $capture;
    $bad['provenance']['suggested_literal']['sha256'] = str_repeat('0', 64);
    $check($rules->existingKey($db, $bad)['key_id'] === null, 'Changed proof linked a key');
    $bad = $capture;
    $bad['found_translation_key'] = 'ui.type';
    $bad['raw_source_code_literal'] = 'ui.type';
    $check($rules->existingKey($db, $bad)['state'] === 'key_structure_review', 'Short key silently repaired');
    $bad = $capture;
    $bad['found_translation_key'] = 'ui.missing.name';
    $bad['raw_source_code_literal'] = 'ui.missing.name';
    $bad['provenance']['suggested_literal']['key'] = 'ui.missing.name';
    $check($rules->existingKey($db, $bad)['key_id'] === null, 'Matching translation value used to invent key assignment');
    $literal = 'Rescan shared candidate evidence';
    $shared = static function (array $result) use ($literal): array {
        return array_values(array_filter($result['shared_candidates'], fn ($g) => $g['literal'] === $literal));
    };
    $write('literal-one', "{{ __('".$literal."') }}");
    $one = $scan('one_literal');
    $check($shared($one) === [] && $one['key_assignments'][$one['created'][0]]['state'] === 'literal', 'Single literal marked or assigned');
    $write('literal-two', "{{ __('".$literal."') }}");
    $two = $scan('two_distinct_suggestions');
    $group = $shared($two);
    $check(count($group) === 1 && count($group[0]['findings']) === 2 && count($group[0]['suggested_keys']) === 2, 'Two literal rule not applied');
    $write('literal-three', "{{ __('".$literal."') }}");
    $three = $scan('three_literals');
    $check(count($shared($three)[0]['findings']) === 3, 'Third finding missing from shared group');
    $write('literal-three', '{{-- removed --}}');
    $twoAgain = $scan('back_to_two');
    $check(count($shared($twoAgain)[0]['findings']) === 2, 'Archived finding retained in group');
    $write('literal-two', '{{-- removed --}}');
    $oneAgain = $scan('back_to_one');
    $check($shared($oneAgain) === [], 'Shared flag remained below threshold');
    $write('literal-one', "{{ __('".$literal."') }}\n{{ __('".$literal."') }}");
    $duplicates = $scan('same_suggested_key_twice');
    $check($shared($duplicates) === [] && count($duplicates['created']) === 1, 'Same suggestion incorrectly counted as two suggestions');
    $write('literal-one', '{{-- removed --}}');
    $none = $scan('no_active_literals');
    $check($shared($none) === [], 'Archived group still shown');
    $write('literal-two', "{{ __('".$literal."') }}");
    $back = $scan('literal_reappears');
    $check(count($back['created']) === 1 && ! in_array($back['created'][0], $group[0]['findings'], true) && $shared($back) === [], 'Archived finding reactivated');
    $write('literal-three', "{{ __('".strtolower($literal)."') }}");
    $different = $scan('different_case');
    $check($shared($different) === [], 'Case variants grouped by normalization');
    $write('literal-value', "{{ __('".addslashes($capture['suggested_literal'])."') }}");
    $equalValue = $scan('literal_equal_to_key_value');
    $check(count($equalValue['created']) === 1 && $equalValue['key_assignments'][$equalValue['created'][0]]['key_id'] === null, 'Literal assigned through translation text');
    $write('key-third', "{{ __('ui.suggest.suggested') }}");
    $request = $writer->prepare([$fixture], $lang);
    $operation = (string) Str::uuid();
    $beforeMutation = $fingerprint();
    $languagePath = $work.'/source/'.$capture['provenance']['suggested_literal']['path'];
    $languageBytes = file_get_contents($languagePath);
    $rejected = false;
    try {
        $writer->apply($operation, $request, static function ($path) use ($fixture, $languagePath, $languageBytes) {
            if ($path === $fixture.'/key-third.blade.php') {
                file_put_contents($languagePath, $languageBytes."\n// changed during test\n");
            }
        });
    } catch (RuntimeException $error) {
        $rejected = $error->getMessage() === 'Key evidence changed during rescan application';
    } finally {
        file_put_contents($languagePath, $languageBytes);
    }
    $check($rejected && $fingerprint() === $beforeMutation, 'Changed locale evidence did not roll back all rows');
    $retry = $writer->apply($operation, $request);
    $check(count($retry['created']) === 1 && $retry['key_assignments'][$retry['created'][0]]['key_id'] === $key, 'Retry with restored evidence failed');
    $afterRetry = $fingerprint();
    $check($writer->apply($operation, $request) == $retry && $fingerprint() === $afterRetry, 'Rule replay changed database rows');
    $report['evidence_mutation_rolled_back'] = true;
    $after = $fingerprint();
    foreach (['translation_workbench_keys', 'translation_workbench_key_names', 'published_value_evidence'] as $table) {
        $check($after[$table] === $originalTables[$table], 'Key/value data changed');
    }
    $check($outside() === $outsideBefore, 'Existing findings changed');
    $report['shared_rule'] = 'active exact raw literal, at least two findings and two distinct suggested keys';
    $report['implementation_sha256'] = hash_file('sha256', $root.'/packages/gunreip/laravel-translation-workbench/src/Generation/CompleteRescanWriter.php');
    $report['runner_sha256'] = hash_file('sha256', __FILE__);

    $report['dump'] = substr($work, strlen($root) + 1).'/database.dump';
    $run([$bin.'/pg_dump', '-h', $cluster.'/socket', '-U', 'rehearsal', '-Fc', '-f', $root.'/'.$report['dump'], $name]);
    $report['dump_sha256'] = hash_file('sha256', $root.'/'.$report['dump']);
    $report['state'] = 'rescan_candidate_rules_verified';
    $report['generation'] = $baseline['generation'];
    $report['limitations'] = ['Isolated writer only', 'Shared candidates are derived and recorded per operation, not yet displayed in UI', 'New unknown keys require review; no key creation or value approval'];
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
exit($report['state'] === 'rescan_candidate_rules_verified' ? 0 : 1);
