<?php

use Gunreip\TranslationWorkbench\Support\BaselineNameInventory;
use Gunreip\TranslationWorkbench\Support\KeyCorrectionDatabase;
use Gunreip\TranslationWorkbench\Support\KeyCorrectionProposal;
use Symfony\Component\Process\Process;

$root = dirname(__DIR__, 3);
require $root.'/vendor/autoload.php';
umask(0077);
$read = static fn ($p) => json_decode(file_get_contents($p), true, flags: JSON_THROW_ON_ERROR);
$baseline = $read(__DIR__.'/published-values-2026-10-19.json');
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
$name = 'proposal_'.bin2hex(random_bytes(6));
$work = $backup.'/'.$name;
mkdir($work, 0700);
$started = false;
$checks = 0;
$check = static function (bool $ok, string $message) use (&$checks): void {
    if (! $ok) {
        throw new RuntimeException($message);
    }
    $checks++;
};
try {
    $run([$bin.'/pg_ctl', '-D', $cluster.'/data', '-l', $cluster.'/server.log', '-w', 'start']);
    $started = true;
    $run([$bin.'/createdb', '-h', $cluster.'/socket', '-U', 'rehearsal', $name]);
    $run([$bin.'/pg_restore', '-h', $cluster.'/socket', '-U', 'rehearsal', '--exit-on-error', '--no-owner', '--no-privileges', '-d', $name, $root.'/'.$baseline['dump']]);
    $db = new PDO('pgsql:host='.$cluster.'/socket;dbname='.$name.';user=rehearsal', options: [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $check($db->query('SHOW data_directory')->fetchColumn() === $cluster.'/data' && $db->query('SHOW listen_addresses')->fetchColumn() === '', 'Wrong cluster');
    $tables = $db->query("SELECT tablename FROM pg_tables WHERE schemaname='public' ORDER BY tablename")->fetchAll(PDO::FETCH_COLUMN);
    $fingerprint = static function () use ($db, $tables): array {
        $result = [];
        foreach ($tables as $table) {
            if (! preg_match('/^[a-z_]+$/D', $table)) {
                throw new RuntimeException('Unexpected table');
            }
            $result[$table] = $db->query('SELECT md5(string_agg(row_to_json(t)::text,chr(10) ORDER BY row_to_json(t)::text)) FROM '.$table.' t')->fetchColumn();
        }

        return $result;
    };
    $before = $fingerprint();
    $db->exec(file_get_contents($root.'/packages/gunreip/laravel-translation-workbench/database/generations/v1/key-correction-proposals.sql'));
    $hash = $db->query('SELECT hash FROM published_value_baselines')->fetchColumn();
    $db->exec(file_get_contents($root.'/packages/gunreip/laravel-translation-workbench/database/generations/v2/name-inventory.sql'));
    $db->beginTransaction();
    $nameInventoryService = new BaselineNameInventory;
    $nameInventory = $nameInventoryService->capture($db, 'public', $hash);
    $nameInventoryService->store($db, 'public', $hash, $hash, $nameInventory);
    $db->commit();
    $service = new KeyCorrectionProposal(new KeyCorrectionDatabase($db, $name, 'public', $hash, $backupResult['backup_manifest_sha256']));
    $source = 'ui.type';
    $target = 'rehearsal.correction.type'; // Synthetic proposal only; not an agreed replacement.
    $operation = '00000000-0000-4000-8000-000000000001';
    $preview = $service->preview($hash, $source, $target);
    $check(count($preview['evidence']['sources']) === 387, 'Wrong source impact');
    $check(count($preview['evidence']['published_values']) === 2, 'Wrong value impact');
    $reject = static function (callable $action, string $message) use ($check): void {
        $caught = false;
        try {
            $action();
        } catch (RuntimeException) {
            $caught = true;
        }
        $check($caught, $message);
    };
    $reject(fn () => $service->record($operation, $hash, $source, $target, str_repeat('0', 64), 999999, 'Fixture only'), 'Stale context accepted');
    $reject(fn () => $service->record($operation, $hash, $source, $target, $preview['fingerprint'], 999999, ''), 'Missing reason accepted');
    $reject(fn () => $service->record($operation, $hash, $source, $target, $preview['fingerprint'], 0, 'Fixture only'), 'Missing actor accepted');
    $reject(fn () => $service->preview($hash, $source, 'ui.type'), 'Invalid target accepted');
    $reject(fn () => $service->preview($hash, $source, 'ui.states.all'), 'Occupied target accepted');
    $check((int) $db->query('SELECT count(*) FROM baseline_key_correction_proposals')->fetchColumn() === 0, 'Failed operations left records');
    $result = $service->record($operation, $hash, $source, $target, $preview['fingerprint'], 999999, 'Synthetic rehearsal proposal; do not apply');
    $check(! $result['replayed'], 'Initial insert replayed');
    $check($service->record($operation, $hash, $source, $target, $preview['fingerprint'], 999999, 'Synthetic rehearsal proposal; do not apply')['replayed'], 'Retry not recognized');
    $reject(fn () => $service->record($operation, $hash, $source, 'rehearsal.other.type', $preview['fingerprint'], 999999, 'Changed request'), 'Changed retry accepted');
    $check((int) $db->query('SELECT count(*) FROM baseline_key_correction_proposals')->fetchColumn() === 1, 'Duplicate proposal');
    $stored = $db->query('SELECT context FROM baseline_key_correction_proposals')->fetchColumn();
    $check(json_decode($stored, true, flags: JSON_THROW_ON_ERROR) == $preview['evidence'], 'Recorded evidence differs');
    $reject(fn () => $db->exec('DELETE FROM baseline_key_correction_proposals'), 'Proposal deletion accepted');
    $reject(fn () => $db->exec("UPDATE baseline_key_correction_proposals SET target_key='changed.other.name'"), 'Proposal modification accepted');
    $check($fingerprint() === $before, 'Baseline tables changed');
    $report = ['state' => 'isolated_proposal_recording_verified', 'production_connected' => false, 'assertions' => $checks,
        'unchanged_tables' => count($tables), 'source_occurrences' => count($preview['evidence']['sources']),
        'published_values' => count($preview['evidence']['published_values']), 'synthetic_actor' => 999999,
        'synthetic_target' => $target, 'correction_applied' => false, 'input_dump_sha256' => $baseline['dump_sha256']];
} finally {
    if ($started) {
        $run([$bin.'/pg_ctl', '-D', $cluster.'/data', '-m', 'fast', '-w', 'stop']);
    }
}
$report['cluster_stopped'] = true;
file_put_contents(__DIR__.'/key-correction-proposal-2026-10-19.json', json_encode($report, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR)."\n");
echo json_encode($report, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR)."\n";
