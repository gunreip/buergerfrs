<?php

// Explicit isolated import of the current scan. No application bootstrap or production credentials.
use Gunreip\TranslationWorkbench\Support\WorkingGenerationSchema;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;
use Tests\Support\TranslationWorkbench\CurrentScanTransferInput;
use Tests\Support\TranslationWorkbench\ExistingKeyImporter;
use Tests\Support\TranslationWorkbench\PublishedValueBaseline;
use Tests\Support\TranslationWorkbench\StaticCaptureImporter;

$root = dirname(__DIR__, 3);
require $root.'/vendor/autoload.php';
umask(0077);
$pointer = json_decode(file_get_contents($root.'/storage/app/private/workbench-previews/current-scan.json'), true, flags: JSON_THROW_ON_ERROR);
if (! preg_match('/^\d{8}-\d{6}-[a-f0-9]{12}$/D', $pointer['run'])) {
    throw new RuntimeException('Invalid scan pointer');
}
$destination = $root.'/storage/app/private/dev/workbench-rehearsal/current-generation-'.gmdate('Ymd-His').'-'.bin2hex(random_bytes(6));
$input = (new CurrentScanTransferInput)->prepare($root, $root.'/storage/app/private/workbench-previews/current-scan-'.$pointer['run'], $pointer['sha256'], $destination);
$cluster = sys_get_temp_dir().'/tw-current-generation-'.bin2hex(random_bytes(8));
mkdir($cluster, 0700);
mkdir($cluster.'/socket', 0700);
$bin = '/usr/lib/postgresql/18/bin';
$run = static function (array $command) use ($root): void {
    $p = new Process($command, $root, ['PGOPTIONS' => '', 'PGPASSWORD' => false, 'PGPASSFILE' => false]);
    $p->setTimeout(600);
    $p->mustRun();
};
$assertions = 0;
$check = static function (bool $condition, string $message) use (&$assertions): void {
    $assertions++;
    if (! $condition) {
        throw new RuntimeException($message);
    }
};
$reject = static function (callable $action, string $message) use ($check): void {
    try {
        $action();
    } catch (RuntimeException) {
        $check(true, $message);

        return;
    }
    $check(false, $message);
};
$started = false;
$db = null;
$report = [];
try {
    $run([$bin.'/initdb', '-D', $cluster.'/data', '-U', 'rehearsal', '--auth-local=trust', '--auth-host=reject', '--no-locale', '--encoding=UTF8']);
    file_put_contents($cluster.'/data/postgresql.conf', "\nlisten_addresses=''\nunix_socket_directories='".$cluster."/socket'\nunix_socket_permissions=0700\n", FILE_APPEND);
    $run([$bin.'/pg_ctl', '-D', $cluster.'/data', '-l', $cluster.'/server.log', '-w', 'start']);
    $started = true;
    $run([$bin.'/createdb', '-h', $cluster.'/socket', '-U', 'rehearsal', 'current_generation']);
    $connect = static fn (string $name): PDO => new PDO('pgsql:host='.$cluster.'/socket;dbname='.$name.';user=rehearsal', options: [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $db = $connect('current_generation');
    $check($db->query('SHOW listen_addresses')->fetchColumn() === '' && $db->query('SHOW data_directory')->fetchColumn() === $cluster.'/data', 'Wrong cluster');
    $schema = 'current_generation_v2';
    $generation = (string) Str::uuid();
    $service = new WorkingGenerationSchema;
    $installed = $service->install($db, 'current_generation', $schema, $generation);
    $db->exec('SET search_path TO '.$schema.',pg_catalog');
    $capture = new StaticCaptureImporter;
    $reject(fn () => $capture->import($db, $input['capture_manifest'], str_repeat('0', 64), $input['candidate_hash']), 'Changed manifest accepted');
    $captureTotals = $capture->import($db, $input['capture_manifest'], $input['capture_hash'], $input['candidate_hash']);
    $keys = new ExistingKeyImporter;
    $bad = $input['key_plan'];
    $bad['items'][count($bad['items']) - 1]['key'] = 'invented.wrong.key';
    $reject(fn () => $keys->import($db, $bad, $input['capture_hash']), 'Changed key evidence accepted');
    $check((int) $db->query('SELECT count(*) FROM translation_workbench_keys')->fetchColumn() === 0, 'Failed key import left partial keys');
    $keyTotals = $keys->import($db, $input['key_plan'], $input['capture_hash']);
    $values = new PublishedValueBaseline;
    $badValues = $input['published'];
    $badValues['values'][count($badValues['values']) - 1]['locale'] = 'invented';
    $reject(fn () => $values->import($db, $badValues, $input['backup_hash']), 'Changed locale accepted');
    $check((int) $db->query('SELECT count(*) FROM published_value_evidence')->fetchColumn() === 0, 'Failed value import left partial values');
    $valueTotals = $values->import($db, $input['published'], $input['backup_hash']);
    $fingerprint = static function (PDO $connection) use ($schema): array {
        $names = $connection->query("SELECT tablename FROM pg_tables WHERE schemaname='$schema' ORDER BY tablename")->fetchAll(PDO::FETCH_COLUMN);
        $hashes = [];
        foreach ($names as $name) {
            $hashes[$name] = $connection->query('SELECT md5(coalesce(string_agg(row_to_json(t)::text,chr(10) ORDER BY row_to_json(t)::text),\'\')) FROM "'.$schema.'"."'.$name.'" t')->fetchColumn();
        }

        return $hashes;
    };
    $before = $fingerprint($db);
    $check($capture->import($db, $input['capture_manifest'], $input['capture_hash'], $input['candidate_hash'])['replayed'], 'Capture replay failed');
    $check($keys->import($db, $input['key_plan'], $input['capture_hash'])['replayed'], 'Key replay failed');
    $check($values->import($db, $input['published'], $input['backup_hash'])['replayed'], 'Value replay failed');
    $check($before === $fingerprint($db), 'Replay changed generation data');
    $check($service->verify($db, 'current_generation', $schema, $generation) === $installed, 'Schema changed');
    $db->exec('SET search_path TO '.$schema.',pg_catalog');
    $query = $db->prepare('SELECT f.*,s.path,o.disposition,o.reason FROM capture_outcomes o LEFT JOIN translation_workbench_findings f ON f.id=o.finding_id LEFT JOIN translation_workbench_source_files s ON s.id=f.source_file_id WHERE o.capture_id=?');
    $stream = fopen($destination.'/candidates.jsonl', 'r');
    while (($line = fgets($stream)) !== false) {
        $row = json_decode($line, true, flags: JSON_THROW_ON_ERROR);
        $query->execute([$row['capture_id']]);
        $stored = $query->fetch(PDO::FETCH_ASSOC);
        $check($stored !== false, 'Capture missing');
        if ($stored['disposition'] === 'captured') {
            foreach (['raw_expression', 'raw_source_code_literal', 'raw_literal', 'suggested_literal', 'suggested_key'] as $field) {
                $check($stored[$field] === $row[$field], 'Immutable value changed: '.$field);
            }
            $check($stored['path'] === $row['source_path'] && (int) $stored['source_line'] === $row['source_line'] && (int) $stored['source_column'] === $row['source_column'], 'Source position changed');
        } else {
            $check($stored['id'] === null, 'Pending or excluded capture was activated');
        }
    }
    fclose($stream);
    $query = $db->prepare('SELECT value,path FROM published_value_evidence WHERE locale=? AND full_name=?');
    foreach ($input['published']['values'] as $row) {
        $query->execute([$row['locale'], $row['key']]);
        $stored = $query->fetch(PDO::FETCH_ASSOC);
        $check($stored !== false && $stored['value'] === $row['value'] && $stored['path'] === $row['path'], 'Published value readback differs');
    }
    $check((int) $db->query('SELECT count(*) FROM translation_workbench_scan_runs')->fetchColumn() === 1, 'Extra scan generated');
    $check((int) $db->query('SELECT count(*) FROM translation_workbench_events')->fetchColumn() === 0, 'Historical or invented user events imported');
    $check((int) $db->query('SELECT count(*) FROM translation_workbench_value_revisions')->fetchColumn() === 0, 'Baseline evidence turned into authored revisions');
    $blocked = (int) $db->query("SELECT count(*) FROM existing_key_observations WHERE state='blocked_structure'")->fetchColumn();
    $dump = $destination.'/generation.dump';
    $run([$bin.'/pg_dump', '-h', $cluster.'/socket', '-U', 'rehearsal', '-Fc', '-f', $dump, 'current_generation']);
    $run([$bin.'/createdb', '-h', $cluster.'/socket', '-U', 'rehearsal', 'restore_check']);
    $run([$bin.'/pg_restore', '-h', $cluster.'/socket', '-U', 'rehearsal', '--exit-on-error', '--no-owner', '--no-privileges', '-d', 'restore_check', $dump]);
    $restored = $connect('restore_check');
    $check($before === $fingerprint($restored), 'Restored generation differs');
    $check($service->verify($restored, 'restore_check', $schema, $generation) === $installed, 'Restored schema differs');
    // Restore stability must not weaken detection of disabled FK enforcement.
    $trigger = $restored->query("SELECT c.relname,t.tgname FROM pg_trigger t JOIN pg_class c ON c.oid=t.tgrelid JOIN pg_namespace n ON n.oid=c.relnamespace WHERE n.nspname='$schema' AND t.tgisinternal ORDER BY c.relname,t.tgname LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    $tableName = '"'.$schema.'"."'.str_replace('"', '""', $trigger['relname']).'"';
    $triggerName = '"'.str_replace('"', '""', $trigger['tgname']).'"';
    $restored->exec('ALTER TABLE '.$tableName.' DISABLE TRIGGER '.$triggerName);
    $reject(fn () => $service->verify($restored, 'restore_check', $schema, $generation), 'Disabled internal FK trigger was ignored');
    $restored->exec('ALTER TABLE '.$tableName.' ENABLE TRIGGER '.$triggerName);
    $check($service->verify($restored, 'restore_check', $schema, $generation) === $installed, 'Reenabled FK enforcement differs');
    $restored = null;
    $report = ['state' => 'current_generation_imported_and_verified', 'recorded_at' => gmdate(DATE_ATOM), 'source_run' => $pointer['run'],
        'source_manifest_sha256' => $pointer['sha256'], 'production_connected' => false, 'workbench_switched' => false,
        'generation' => $installed, 'capture' => $captureTotals, 'keys' => $keyTotals, 'blocked_key_findings' => $blocked,
        'published_values' => $valueTotals, 'unused_locale_values_not_imported' => $input['omitted_values'],
        'assertions' => $assertions, 'replay_unchanged' => true, 'dump_restore_verified' => true,
        'input_directory' => substr($destination, strlen($root) + 1), 'dump_sha256' => hash_file('sha256', $dump)];
} finally {
    $db = null;
    if ($started) {
        $run([$bin.'/pg_ctl', '-D', $cluster.'/data', '-m', 'fast', '-w', 'stop']);
    }
}
$report['cluster_stopped'] = true;
file_put_contents($destination.'/report.json', json_encode($report, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR)."\n");
$target = $root.'/storage/app/private/workbench-previews/current-generation.json';
file_put_contents($target.'.tmp', json_encode($report, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR)."\n");
chmod($target.'.tmp', 0644);
rename($target.'.tmp', $target);
echo json_encode($report, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR)."\n";
