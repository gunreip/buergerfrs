<?php

// Restores verified evidence into a disposable cluster. No Laravel bootstrap or production connection.
use Symfony\Component\Process\Process;
use Tests\Support\TranslationWorkbench\HistoricalScalarValueReview;
use Tests\Support\TranslationWorkbench\PublishedValueBaseline;
use Tests\Support\TranslationWorkbench\ReviewCaseLedger;

$root = dirname(__DIR__, 3);
require $root.'/vendor/autoload.php';
umask(0077);
$read = static fn ($path) => json_decode(file_get_contents($path), true, flags: JSON_THROW_ON_ERROR);
$backupReport = $read(__DIR__.'/backup-restore-result-2026-10-19.json');
$manifestPath = $root.'/'.$backupReport['backup_manifest'];
$staging = $read(__DIR__.'/evidence-staging-result-2026-10-19.json');
$mapping = $read(__DIR__.'/generation-mapping-2026-10-19.json');
if (hash_file('sha256', $manifestPath) !== $backupReport['backup_manifest_sha256']
    || hash_file('sha256', __DIR__.'/evidence-staging-result-2026-10-19.json') !== $mapping['staging_report_sha256']
    || $staging['backup_manifest_sha256'] !== $backupReport['backup_manifest_sha256']
    || $mapping['backup_manifest_sha256'] !== $staging['backup_manifest_sha256']
    || $mapping['staging_dump_sha256'] !== $staging['staging_dump_sha256']
    || $staging['state'] !== 'evidence_staged_and_verified'
    || hash_file('sha256', $root.'/'.$staging['staging_dump']) !== $staging['staging_dump_sha256']) {
    throw new RuntimeException('Verified historical evidence changed');
}
$manifest = $read($manifestPath);
$backup = dirname($manifestPath);
$published = (new PublishedValueBaseline)->read($backup.'/source', $manifest['source_files']);
$index = [];
foreach ($published['values'] as $row) {
    $index[json_encode([$row['locale'], $row['key']], JSON_THROW_ON_ERROR)] = $row;
}
$cluster = realpath(sys_get_temp_dir()).'/tw-restore-'.bin2hex(random_bytes(8));
mkdir($cluster, 0700);
mkdir($cluster.'/socket', 0700);
$bin = '/usr/lib/postgresql/18/bin';
$run = static function (array $command) use ($root): void {
    $process = new Process($command, $root, ['PGOPTIONS' => '', 'PGPASSWORD' => false, 'PGPASSFILE' => false]);
    $process->setTimeout(1800);
    $process->mustRun();
};
$started = false;
$report = ['state' => 'incomplete', 'production_connected' => false, 'recorded_at' => date(DATE_ATOM),
    'backup_manifest_sha256' => $backupReport['backup_manifest_sha256'], 'staging_dump_sha256' => $staging['staging_dump_sha256'],
    'active_imports' => 0, 'tables' => [], 'rows' => []];
try {
    $run([$bin.'/initdb', '-D', $cluster.'/data', '-U', 'rehearsal', '--auth-local=trust', '--auth-host=reject', '--no-locale', '--encoding=UTF8']);
    file_put_contents($cluster.'/data/postgresql.conf', "\nlisten_addresses=''\nunix_socket_directories='".$cluster."/socket'\n", FILE_APPEND);
    $run([$bin.'/pg_ctl', '-D', $cluster.'/data', '-l', $cluster.'/server.log', '-w', 'start']);
    $started = true;
    $run([$bin.'/createdb', '-h', $cluster.'/socket', '-U', 'rehearsal', 'historical_review']);
    $run([$bin.'/pg_restore', '-h', $cluster.'/socket', '-U', 'rehearsal', '--exit-on-error', '--no-owner', '--no-privileges',
        '-d', 'historical_review', $root.'/'.$staging['staging_dump']]);
    $db = new PDO('pgsql:host='.$cluster.'/socket;dbname=historical_review;user=rehearsal', options: [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    if ($db->query('SHOW data_directory')->fetchColumn() !== $cluster.'/data' || $db->query('SHOW listen_addresses')->fetchColumn() !== '') {
        throw new RuntimeException('Wrong historical review cluster');
    }
    $db->exec('BEGIN TRANSACTION ISOLATION LEVEL REPEATABLE READ READ ONLY');
    $report['read_only'] = $db->query('SHOW transaction_read_only')->fetchColumn();
    $load = static function (string $table) use ($db, $backupReport, $staging): array {
        $query = $db->prepare('SELECT source_id,raw_payload,payload_sha256 FROM evidence_records WHERE batch_id=? AND source_table=? ORDER BY source_id');
        $query->execute([$backupReport['backup_manifest_sha256'], $table]);
        $rows = [];
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            if (hash('sha256', $row['raw_payload']) !== $row['payload_sha256']) {
                throw new RuntimeException('Historical payload changed');
            }
            $payload = json_decode($row['raw_payload'], true, flags: JSON_THROW_ON_ERROR);
            if ((string) $payload['id'] !== $row['source_id']) {
                throw new RuntimeException('Historical identity differs');
            }
            $rows[$row['source_id']] = $payload;
        }
        if (count($rows) !== $staging['collections'][$table]['source_rows']) {
            throw new RuntimeException('Historical collection incomplete');
        }

        return $rows;
    };
    $keys = $load('translation_keys');
    $originalValues = [];
    $service = new HistoricalScalarValueReview;
    foreach (['translation_values', 'translation_workbench_lang_values'] as $table) {
        $rows = $load($table);
        $counts = [];
        foreach ($rows as $id => $row) {
            $legacy = $table === 'translation_values';
            $key = $legacy ? ($keys[$row['translation_key_id']] ?? null) : null;
            $result = $service->classify([
                'source_table' => $table, 'source_id' => $id, 'source_key_id' => $row['translation_key_id'] ?? null,
                'key' => $legacy ? ($key['key'] ?? null) : $row['translation_key'],
                'namespace' => ($legacy ? ($key['namespace'] ?? null) : ($row['namespace'] ?? null)) ?? '',
                'locale' => $row['locale'], 'status' => $row['status'],
                'source_path' => $row['source_path'] ?? null, 'source_kind' => $row['source'] ?? ($row['meta']['source'] ?? null), 'value_type' => $row['value_type'] ?? 'text', 'value' => $row['value'],
            ], $index);
            $counts[$result['comparison']] = ($counts[$result['comparison']] ?? 0) + 1;
            $report['rows'][] = $result;
            $originalValues[$table.':'.$id] = $row['value'];
        }
        ksort($counts);
        $report['tables'][$table] = ['rows' => count($rows), 'comparisons' => $counts];
    }
    $report['deferred_value_tables'] = array_filter($staging['collections'],
        static fn ($table) => str_contains($table, 'value') && ! in_array($table, ['translation_values', 'translation_workbench_lang_values'], true), ARRAY_FILTER_USE_KEY);
    $db->rollBack();
    $report['state'] = 'historical_scalar_evidence_reviewed';
    if (in_array('--persist-review-cases', $argv, true)) {
        // Write only to a separate schema in this disposable cluster; evidence stays untouched.
        $db->exec('CREATE SCHEMA review_generation; SET search_path TO review_generation');
        $db->exec(file_get_contents($root.'/packages/gunreip/laravel-translation-workbench/database/generations/v2/review-cases.sql'));
        $ledger = new ReviewCaseLedger($db);
        $actions = [
            'exact_published_match' => ['No automatic import; retain historical provenance.', false],
            'no_scalar_value' => ['No string value to transfer; retain evidence without fabricating a translation.', false],
            'different_from_published' => ['Compare historical and published wording; retain published, propose historical wording, archive evidence, or request clarification.', true],
            'no_exact_published_evidence' => ['Check exact key, locale and publication scope; decide whether to propose a translation, retain archive only, or clarify.', true],
        ];
        foreach (['initial', 'repeat'] as $pass) {
            foreach ($report['rows'] as $row) {
                [$action, $needsReview] = $actions[$row['comparison']] ?? ['Clarify identity or value type before proposing a transfer.', true];
                $subject = 'historical:'.$report['backup_manifest_sha256'].':'.$row['source_table'].':'.$row['source_id'];
                $ledger->observe($subject, $pass.':'.$report['staging_dump_sha256'], $row, $row['comparison'], $action, $needsReview);
            }
        }
        $cases = $ledger->cases();
        $states = array_count_values(array_column($cases, 'state'));
        $versionCount = (int) $db->query('SELECT count(*) FROM workbench_review_versions')->fetchColumn();
        $observations = (int) $db->query('SELECT count(*) FROM workbench_review_observations')->fetchColumn();
        if (count($cases) !== count($report['rows']) || $versionCount !== count($cases) || $observations !== 2 * count($cases)) {
            throw new RuntimeException('Review case replay duplicated or lost evidence');
        }
        $dumpPath = 'storage/app/private/dev/workbench-rehearsal/historical-review-cases-'.bin2hex(random_bytes(8)).'.dump';
        $run([$bin.'/pg_dump', '-h', $cluster.'/socket', '-U', 'rehearsal', '-Fc', '--schema=review_generation',
            '-f', $root.'/'.$dumpPath, 'historical_review']);
        $report['review_queue'] = [
            'mode' => 'isolated_database_rehearsal', 'cases' => count($cases), 'states' => $states,
            'versions' => $versionCount, 'observations' => $observations, 'decisions' => 0,
            'dump' => $dumpPath, 'dump_sha256' => hash_file('sha256', $root.'/'.$dumpPath),
            'live_ui_connected' => false, 'production_scanner_connected' => false,
        ];
        $openKeys = [];
        foreach ($cases as $case) {
            if ($case['state'] === 'needs_review') {
                $openKeys[json_decode($case['evidence'], true, flags: JSON_THROW_ON_ERROR)['key']] = true;
            }
        }
        $previewRows = [];
        foreach ($cases as $case) {
            $caseEvidence = json_decode($case['evidence'], true, flags: JSON_THROW_ON_ERROR);
            if (! isset($openKeys[$caseEvidence['key']])) {
                continue;
            }
            $evidence = json_decode($case['evidence'], true, flags: JSON_THROW_ON_ERROR);
            $value = $originalValues[$evidence['source_table'].':'.$evidence['source_id']];
            $publishedRow = $evidence['published_sha256'] === null ? null : ($index[json_encode([$evidence['locale'], $evidence['key']], JSON_THROW_ON_ERROR)] ?? null);
            if ((is_string($value) ? hash('sha256', $value) : null) !== $evidence['value_sha256']
                || ($publishedRow === null ? null : hash('sha256', $publishedRow['value'])) !== $evidence['published_sha256']) {
                throw new RuntimeException('Review original value does not match stored evidence');
            }
            $previewRows[] = [
                'id' => $case['id'], 'version' => $case['version'], 'state' => $case['state'],
                'subject' => $case['subject'], 'reason' => $case['reason'],
                'required_action' => $case['required_action'], 'evidence' => $evidence,
                'historical_value' => $value, 'published_value' => $publishedRow['value'] ?? null,
            ];
        }
        $reviewPreview = [
            'mode' => 'isolated_review_preview', 'recorded_at' => $report['recorded_at'],
            'backup_manifest_sha256' => $report['backup_manifest_sha256'],
            'dump_sha256' => $report['review_queue']['dump_sha256'],
            'total_cases' => count($cases), 'open_cases' => $states['needs_review'] ?? 0, 'rows' => $previewRows,
            'includes_key_context' => true,
            'published_values' => array_values(array_filter($published['values'], fn ($row) => isset($openKeys[$row['key']]))),
        ];
    }
} finally {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    $db = null;
    if ($started) {
        $run([$bin.'/pg_ctl', '-D', $cluster.'/data', '-m', 'fast', '-w', 'stop']);
    }
}
$report['cluster_stopped'] = true;
if (isset($reviewPreview)) {
    $reviewPreview['cluster_stopped'] = true;
    $previewPath = $root.'/storage/app/private/workbench-previews/historical-review.json';
    $temporary = $previewPath.'.'.bin2hex(random_bytes(6)).'.tmp';
    file_put_contents($temporary, json_encode($reviewPreview, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)."\n");
    chmod($temporary, 0644);
    rename($temporary, $previewPath);
}
$path = __DIR__.'/historical-scalar-review-2026-10-20.json';
file_put_contents($path, json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)."\n");
echo json_encode(array_diff_key($report, ['rows' => true]), JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR)."\n";
