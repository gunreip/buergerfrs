<?php

// Additive deployment of the verified generation; never replaces public Workbench tables.
use Gunreip\TranslationWorkbench\Support\WorkingGenerationSchema;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Process\Process;

$root = dirname(__DIR__, 3);
require $root.'/vendor/autoload.php';
$app = require $root.'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();
$read = static fn ($path) => json_decode(file_get_contents($path), true, flags: JSON_THROW_ON_ERROR);
$base = storage_path('app/private/workbench-previews');
$baseline = $read($base.'/current-generation.json');
$backup = $root.'/'.$baseline['input_directory'];
if ($baseline['state'] !== 'current_generation_imported_and_verified' || hash_file('sha256', $backup.'/generation.dump') !== $baseline['dump_sha256']) {
    throw new RuntimeException('Verified generation backup required');
}
$connection = DB::connection();
if ($connection->getDriverName() !== 'pgsql') {
    throw new RuntimeException('PostgreSQL required');
}
$db = $connection->getPdo();
$captureManifest = $read($backup.'/capture-manifest.json');
if (hash_file('sha256', $backup.'/candidates.jsonl') !== $captureManifest['candidate_file_sha256']) {
    throw new RuntimeException('Capture evidence changed');
}
$schema = 'current_generation_v2';
if ($baseline['generation']['schema'] !== $schema) {
    throw new RuntimeException('Unexpected schema');
}
$db->query("SELECT pg_advisory_lock(hashtextextended('working-generation-ui-install',0))");
try {
    $exists = $db->query("SELECT count(*) FROM pg_namespace WHERE nspname='current_generation_v2'")->fetchColumn();
    if ((int) $exists === 0) {
        $config = $connection->getConfig();
        $process = new Process(['/usr/lib/postgresql/18/bin/pg_restore', '--exit-on-error', '--single-transaction', '--no-owner', '--no-privileges',
            '--dbname', $config['database'], $backup.'/generation.dump'], $root, [
                'PGHOST' => $config['host'], 'PGPORT' => (string) $config['port'], 'PGUSER' => $config['username'],
                'PGPASSWORD' => $config['password'], 'PGOPTIONS' => '', 'PGPASSFILE' => false,
            ]);
        $process->setTimeout(600);
        $process->mustRun();
    }
    $verified = (new WorkingGenerationSchema)->verify($db, $connection->getDatabaseName(), $schema, $baseline['generation']['generation']);
    $reviewPointer = $read($base.'/rescan-review.json');
    $manifestBytes = file_get_contents($base.'/rescan-review-'.$reviewPointer['run'].'/manifest.json');
    if (hash('sha256', $manifestBytes) !== $reviewPointer['sha256']) {
        throw new RuntimeException('Review export changed');
    }
    $review = json_decode($manifestBytes, true, flags: JSON_THROW_ON_ERROR);
    if ($review['generation'] !== $verified['generation']) {
        throw new RuntimeException('Review generation differs');
    }
    $positions = [];
    foreach ($review['files'] as $file) {
        $bytes = file_get_contents($base.'/rescan-review-'.$reviewPointer['run'].'/'.$file['id'].'.json');
        if (hash('sha256', $bytes) !== $file['sha256']) {
            throw new RuntimeException('Review detail changed');
        }
        $detail = json_decode($bytes, true, flags: JSON_THROW_ON_ERROR);
        foreach ($detail['issues'] as $issue) {
            foreach ($detail['captures'] as $capture) {
                if ($capture['source_line'] === ($issue['line'] ?? null) && $capture['source_column'] === ($issue['column'] ?? null)) {
                    $positions[hash('sha256', json_encode([$capture['source_path'], $capture['source_offset'], $capture['raw_expression']]))] = $capture;
                }
            }
        }
    }
    $cases = [];
    $stream = fopen($backup.'/candidates.jsonl', 'r');
    while (($line = fgets($stream)) !== false) {
        $capture = json_decode($line, true, flags: JSON_THROW_ON_ERROR);
        $position = hash('sha256', json_encode([$capture['source_path'], $capture['source_offset'], $capture['raw_expression']]));
        if (! isset($positions[$position]) || $capture['source_sha256'] !== $positions[$position]['source_sha256']) {
            continue;
        }
        $query = $db->prepare('SELECT manifest_sha256 FROM current_generation_v2.capture_outcomes WHERE capture_id=? AND disposition=\'pending\'');
        $query->execute([$capture['capture_id']]);
        $manifests = $query->fetchAll(PDO::FETCH_COLUMN);
        if (count($manifests) !== 1 || $manifests[0] !== hash_file('sha256', $backup.'/capture-manifest.json')) {
            throw new RuntimeException('Pending capture is not unique in the database');
        }
        $cases[$capture['capture_id']] = [...$positions[$position], 'capture_id' => $capture['capture_id'], 'manifest_sha256' => $manifests[0]];
    }
    fclose($stream);
    if (count($cases) !== count($positions)) {
        throw new RuntimeException('Incomplete mapping of review cases');
    }
    $bytes = json_encode(['generation' => $verified, 'database' => $connection->getDatabaseName(), 'cases' => $cases], JSON_THROW_ON_ERROR);
    $name = 'working-generation-ui-'.gmdate('Ymd-His').'-'.bin2hex(random_bytes(6)).'.json';
    file_put_contents($base.'/'.$name, $bytes);
    chmod($base.'/'.$name, 0644);
    file_put_contents($base.'/working-generation-ui.tmp', json_encode(['file' => $name, 'sha256' => hash('sha256', $bytes)], JSON_THROW_ON_ERROR));
    chmod($base.'/working-generation-ui.tmp', 0644);
    rename($base.'/working-generation-ui.tmp', $base.'/working-generation-ui.json');
    echo json_encode(['schema' => $schema, 'cases' => count($cases), 'old_tables_replaced' => false], JSON_THROW_ON_ERROR).PHP_EOL;
} finally {
    $db->query("SELECT pg_advisory_unlock(hashtextextended('working-generation-ui-install',0))");
}
