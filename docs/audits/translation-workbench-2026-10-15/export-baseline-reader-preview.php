<?php

use Symfony\Component\Process\Process;
use Tests\Support\TranslationWorkbench\BaselineTranslationReader;
use Tests\Support\TranslationWorkbench\UnassignedValueContext;

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
$name = 'preview_'.bin2hex(random_bytes(6));
$work = $backup.'/'.$name;
mkdir($work, 0700);
$started = false;
try {
    $run([$bin.'/pg_ctl', '-D', $cluster.'/data', '-l', $cluster.'/server.log', '-w', 'start']);
    $started = true;
    $run([$bin.'/createdb', '-h', $cluster.'/socket', '-U', 'rehearsal', $name]);
    $run([$bin.'/pg_restore', '-h', $cluster.'/socket', '-U', 'rehearsal', '--exit-on-error', '--no-owner', '--no-privileges', '-d', $name, $root.'/'.$baseline['dump']]);
    $db = new PDO('pgsql:host='.$cluster.'/socket;dbname='.$name.';user=rehearsal', options: [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    if ($db->query('SHOW data_directory')->fetchColumn() !== $cluster.'/data' || $db->query('SHOW listen_addresses')->fetchColumn() !== '') {
        throw new RuntimeException('Wrong database');
    }
    $hash = $db->query('SELECT hash FROM published_value_baselines')->fetchColumn();
    $reader = new BaselineTranslationReader($db);
    $keys = $db->query('SELECT DISTINCT full_name FROM published_value_evidence ORDER BY full_name')->fetchAll(PDO::FETCH_COLUMN);
    $optionSources = array_map(fn ($raw) => json_decode($raw, true, flags: JSON_THROW_ON_ERROR),
        $db->query("SELECT evidence FROM source_argument_evidence WHERE kind='option_arguments' ORDER BY capture_id")->fetchAll(PDO::FETCH_COLUMN));
    $classifier = new UnassignedValueContext;
    $contexts = [];
    $unassigned = [];
    $counts = [];
    foreach ($db->query("SELECT full_name,locale,path,value FROM published_value_evidence WHERE state='unassigned_key' ORDER BY full_name,locale")->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $contexts[$row['full_name']] ??= $classifier->classify($row['full_name'], $optionSources);
        $classification = $contexts[$row['full_name']];
        $counts[$classification['kind']] = ($counts[$classification['kind']] ?? 0) + 1;
        $unassigned[] = [...$row, 'context' => $classification];
    }

    // Keep source occurrences distinct; these IDs belong only to the isolated generation.
    $blockedSources = [];
    foreach ($db->query("SELECT f.raw_source_code_literal AS key, f.id AS finding_id,
        sf.path, f.source_line AS line, f.source_column AS column, f.raw_expression AS expression
        FROM existing_key_observations o
        JOIN translation_workbench_findings f ON f.id=o.finding_id
        JOIN translation_workbench_source_files sf ON sf.id=f.source_file_id
        WHERE o.state='blocked_structure'
        ORDER BY f.raw_source_code_literal,sf.path,f.source_line,f.source_column,f.id")->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $key = $row['key'];
        unset($row['key']);
        $row['finding_id'] = (int) $row['finding_id'];
        $row['line'] = (int) $row['line'];
        $row['column'] = (int) $row['column'];
        $row['sha256'] = $manifest['source_files'][$row['path']]['sha256'] ?? null;
        if ($row['sha256'] === null) {
            throw new RuntimeException('Blocked source is absent from the verified backup manifest');
        }
        $blockedSources[$key][] = $row;
    }
    $links = $read(__DIR__.'/existing-key-links-2026-10-19.json');
    if (count($blockedSources) !== count($links['blocked_keys'])
        || array_sum(array_map('count', $blockedSources)) !== $links['blocked_findings']) {
        throw new RuntimeException('Blocked source coverage differs from verified key import');
    }

    $localeQuery = $db->prepare('SELECT DISTINCT locale FROM published_value_sources WHERE baseline_hash=? ORDER BY locale');
    $localeQuery->execute([$hash]);
    $exportedLocales = $localeQuery->fetchAll(PDO::FETCH_COLUMN);

    $export = ['schema' => 1, 'mode' => 'isolated_baseline_preview', 'exported_at' => gmdate('Y-m-d H:i:s').' UTC',
        'input_dump_sha256' => $baseline['dump_sha256'], 'baseline_hash' => $hash, 'exported_locales' => $exportedLocales,
        'chains' => ['de-at' => ['de-at', 'de', 'en'], 'de' => ['de', 'en'], 'en' => ['en']], 'items' => []];
    foreach ($keys as $key) {
        $item = $reader->read($hash, $key, ['de-at', 'de', 'en']);
        // Additional locales are read independently, not added to the fallback chain.
        foreach (array_diff($exportedLocales, array_keys($item['locales'])) as $locale) {
            $exact = $reader->read($hash, $key, [$locale]);
            $item['locales'][$locale] = $exact['locales'][$locale];
        }
        $export['items'][$key] = [...$item, 'unassigned_context' => $contexts[$key] ?? null,
            'blocked_sources' => $blockedSources[$key] ?? []];
    }
    $export['unassigned_counts'] = $counts;
    $analysis = ['mode' => 'unassigned_source_context', 'production_connected' => false, 'database_writes' => 0,
        'input_dump_sha256' => $baseline['dump_sha256'], 'counts' => $counts, 'values' => $unassigned,
        'classifier_sha256' => hash_file('sha256', $root.'/tests/Support/TranslationWorkbench/UnassignedValueContext.php')];
    file_put_contents($work.'/unassigned-context.json', json_encode($analysis, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)."\n");
    $bytes = json_encode($export, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)."\n";
    $directory = $root.'/storage/app/private/workbench-previews';
    if (! is_dir($directory)) {
        mkdir($directory, 0755, true);
        chmod($directory, 0755);
    }
    $temporary = tempnam($directory, 'reader-');
    file_put_contents($temporary, $bytes);
    chmod($temporary, 0644);
    if (! rename($temporary, $directory.'/reader-preview.json')) {
        throw new RuntimeException('Cannot install snapshot');
    }
    echo json_encode(['analysis' => substr($work, strlen($root) + 1).'/unassigned-context.json', 'counts' => $counts, 'keys' => count($keys), 'bytes' => strlen($bytes), 'snapshot' => 'storage/app/private/workbench-previews/reader-preview.json', 'sha256' => hash('sha256', $bytes)], JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR)."\n";
} finally {
    if ($started) {
        $run([$bin.'/pg_ctl', '-D', $cluster.'/data', '-m', 'fast', '-w', 'stop']);
    }
}
