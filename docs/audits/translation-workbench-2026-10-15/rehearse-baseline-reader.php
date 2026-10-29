<?php

use Gunreip\TranslationWorkbench\Generation\RevisionReviewContext;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;
use Tests\Support\TranslationWorkbench\BaselineTranslationReader;

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
$name = 'readmodel_'.bin2hex(random_bytes(6));
$work = $backup.'/'.$name;
mkdir($work, 0700);
$report = ['state' => 'incomplete', 'production_connected' => false, 'assertions' => 0, 'input_dump_sha256' => $baseline['dump_sha256']];
$check = static function (bool $ok, string $message) use (&$report): void {
    if (! $ok) {
        throw new RuntimeException($message);
    } $report['assertions']++;
};
$started = false;
try {
    $run([$bin.'/pg_ctl', '-D', $cluster.'/data', '-l', $cluster.'/server.log', '-w', 'start']);
    $started = true;
    $restore = static function ($database) use ($run, $bin, $cluster, $root, $baseline): PDO {
        $run([$bin.'/createdb', '-h', $cluster.'/socket', '-U', 'rehearsal', $database]);
        $run([$bin.'/pg_restore', '-h', $cluster.'/socket', '-U', 'rehearsal', '--exit-on-error', '--no-owner', '--no-privileges', '-d', $database, $root.'/'.$baseline['dump']]);

        return new PDO('pgsql:host='.$cluster.'/socket;dbname='.$database.';user=rehearsal', options: [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    };
    $db = $restore($name);
    $check($db->query('SHOW data_directory')->fetchColumn() === $cluster.'/data' && $db->query('SHOW listen_addresses')->fetchColumn() === '', 'Wrong cluster');
    $tables = $db->query("SELECT tablename FROM pg_tables WHERE schemaname='public' ORDER BY tablename")->fetchAll(PDO::FETCH_COLUMN);
    $fingerprint = static function () use ($db, $tables): array {
        $r = [];
        foreach ($tables as $t) {
            if (! preg_match('/^[a-z_]+$/D', $t)) {
                throw new RuntimeException('Unexpected table');
            }
            $r[$t] = $db->query('SELECT md5(string_agg(row_to_json(t)::text,chr(10) ORDER BY row_to_json(t)::text)) FROM '.$t.' t')->fetchColumn();
        }

        return $r;
    };
    $before = $fingerprint();
    $hash = $db->query('SELECT hash FROM published_value_baselines')->fetchColumn();
    $reader = new BaselineTranslationReader($db);
    $keys = $db->query("SELECT full_name FROM translation_workbench_key_names WHERE namespace='' AND valid_to IS NULL ORDER BY full_name")->fetchAll(PDO::FETCH_COLUMN);
    $fallbacks = 0;
    foreach ($keys as $key) {
        $state = $reader->read($hash, $key, ['de-at', 'de', 'en']);
        $check($state['effective_published'] !== null && $state['locales']['de']['published'] !== null && $state['locales']['en']['published'] !== null, 'Missing source/de baseline');
        $check($state['locales']['de-at']['editing'] === null && $state['locales']['de']['editing'] === null, 'Baseline invented a draft');
        $fallbacks += (int) $state['effective_published']['fallback'];
        $expected = $state['locales']['de-at']['published'] ?? $state['locales']['de']['published'];
        $check($state['effective_published']['value'] === $expected['value'], 'Fallback order differs');
    }
    $state = $reader->read($hash, 'ui.type', ['de-at', 'de', 'en']);
    $check($state['key_id'] === null && $state['locales']['en']['published']['state'] === 'blocked_structure', 'Blocked baseline hidden or assigned');
    $state = $reader->read($hash, 'unknown.nonexistent.key', ['de-at', 'de', 'en']);
    $check($state['effective_published'] === null && $state['key_id'] === null, 'Missing key invented a translation');
    foreach ([[], ['de', 'de'], ['de', null]] as $chain) {
        $rejected = false;
        try {
            $reader->read($hash, $keys[0], $chain);
        } catch (RuntimeException) {
            $rejected = true;
        }$check($rejected, 'Invalid chain accepted');
    }
    $check($fingerprint() === $before, 'Read changed baseline');
    $report['verified_keys'] = count($keys);
    $report['de_at_fallbacks'] = $fallbacks;
    $report['unchanged_tables'] = count($tables);
    // Separate disposable fixture database: synthetic actors/reviews never enter the preserved baseline.
    $fixture = $restore($name.'_fixture');
    $q = static function ($sql, $params = []) use ($fixture) {
        $s = $fixture->prepare($sql);
        $s->execute($params);

        return $s;
    };
    $key = $q("SELECT key_id,full_name FROM translation_workbench_key_names WHERE namespace='' AND valid_to IS NULL ORDER BY id LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    $slot = $q("INSERT INTO translation_workbench_value_slots(key_id,locale) VALUES (?,'de-at') RETURNING id", [$key['key_id']])->fetchColumn();
    $revision = $q("INSERT INTO translation_workbench_value_revisions(slot_id,revision_number,value,actor_id,reason) VALUES (?,1,'Synthetic draft',999999,'Disposable reader fixture') RETURNING id", [$slot])->fetchColumn();
    $q('UPDATE translation_workbench_value_slots SET current_revision_id=? WHERE id=?', [$revision, $slot]);
    $r = new BaselineTranslationReader($fixture);
    $original = $r->read($hash, $key['full_name'], ['de-at', 'de', 'en']);
    $check($original['locales']['de-at']['editing']['approval'] === 'unreviewed' && $original['effective_published']['value'] !== 'Synthetic draft', 'Draft replaced publication');
    $snapshot = RevisionReviewContext::snapshot($fixture, (int) $revision);
    $review = static function ($decision, $snapshot) use ($q, $revision) {
        $uuid = Str::uuid()->toString();
        $q("INSERT INTO translation_workbench_operations(id,kind,request_fingerprint,result) VALUES (?,'test_fixture',?,'{}')", [$uuid, hash('sha256', $uuid)]);
        $q("INSERT INTO translation_workbench_reviews(operation_id,revision_id,key_name_id,decision,context_fingerprint,context_snapshot,actor_id,reason) VALUES (?,?,?,?,?,?::jsonb,999999,'Disposable reader fixture')", [$uuid, $revision, $snapshot['context']['key_name_id'], $decision, $snapshot['context_fingerprint'], json_encode($snapshot['context'], JSON_THROW_ON_ERROR)]);
    };
    $review('approved', $snapshot);
    $state = $r->read($hash, $key['full_name'], ['de-at', 'de', 'en']);
    $check($state['locales']['de-at']['editing']['approval'] === 'approved' && $state['effective_published'] === $original['effective_published'], 'Approved draft published prematurely');
    $review('revoked', $snapshot);
    $check($r->read($hash, $key['full_name'], ['de-at', 'de', 'en'])['locales']['de-at']['editing']['approval'] === 'revoked', 'Revocation ignored');
    $review('approved', $snapshot);
    $q('UPDATE translation_workbench_key_findings SET valid_to=now() WHERE key_id=? AND valid_to IS NULL', [$key['key_id']]);
    $check($r->read($hash, $key['full_name'], ['de-at', 'de', 'en'])['locales']['de-at']['editing']['approval'] === 'stale', 'Changed usage did not invalidate review');
    // Empty/zero are real published strings: fixtures inserted under unused names, never patched into the saved evidence.
    $source = $q("SELECT path FROM published_value_sources WHERE baseline_hash=? AND locale='de-at' LIMIT 1", [$hash])->fetchColumn();
    foreach (['' => 'fixture.empty.value', '0' => 'fixture.zero.value'] as $value => $nameKey) {
        $q("INSERT INTO published_value_evidence VALUES (?,?,'de-at',?,?,'unassigned_key',NULL)", [$hash, $source, $nameKey, (string) $value]);
        $state = $r->read($hash, $nameKey, ['de-at', 'de', 'en']);
        $check($state['effective_published']['value'] === (string) $value && ! $state['effective_published']['fallback'], 'Empty/zero incorrectly fell back');
    }
    $check($fingerprint() === $before, 'Fixture affected preserved baseline');
    $report['synthetic_fixture_database'] = $name.'_fixture';
    $report['fixture_exported'] = false;
    $report['reader_sha256'] = hash_file('sha256', $root.'/tests/Support/TranslationWorkbench/BaselineTranslationReader.php');
    $report['review_context_sha256'] = hash_file('sha256', $root.'/packages/gunreip/laravel-translation-workbench/src/Generation/RevisionReviewContext.php');
    $report['runner_sha256'] = hash_file('sha256', __FILE__);
    $report['state'] = 'baseline_reader_verified';
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
exit($report['state'] === 'baseline_reader_verified' ? 0 : 1);
