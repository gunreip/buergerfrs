<?php

// Explicit read-only production capture; all restore writes go to a new private cluster.
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Process\Process;

$root = dirname(__DIR__, 3);
umask(0077);
require $root.'/vendor/autoload.php';
$run = static function (array $command, array $environment = [], int $timeout = 1800) use ($root): string {
    $process = new Process($command, $root, $environment);
    $process->setTimeout($timeout);
    $process->mustRun();

    return $process->getOutput();
};
$backup = $root.'/storage/app/private/dev/workbench-rehearsal/'.date('Ymd-His').'-'.bin2hex(random_bytes(4));
if (! str_contains($run(['git', 'check-ignore', 'storage/app/private/dev/workbench-rehearsal/probe']), 'workbench-rehearsal')) {
    throw new RuntimeException('Backup must be excluded from Git');
}
mkdir($backup, 0700, true);
$manifest = ['started_at' => date(DATE_ATOM), 'state' => 'incomplete', 'production_writes' => false,
    'source_scope' => 'Git tracked plus nonignored untracked files; no .env, uploads, vendor or node_modules backup',
    'backup_directory' => $backup];
$save = static function () use (&$manifest, $backup): void {
    file_put_contents($backup.'/manifest.json', json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR).PHP_EOL);
};
$files = static function () use ($run, $root): array {
    $paths = array_unique(array_filter(explode("\0", $run(['git', 'ls-files', '-z', '--cached', '--others', '--exclude-standard']))));
    sort($paths);
    $result = [];
    foreach ($paths as $path) {
        if (str_starts_with($path, '/') || in_array('..', explode('/', $path), true) || preg_match('~(^|/)\.env($|\.)~', $path)) {
            continue;
        }
        if (is_link($root.'/'.$path)) {
            $result[$path] = ['link' => readlink($root.'/'.$path)];
        } elseif (is_file($root.'/'.$path)) {
            $result[$path] = ['sha256' => hash_file('sha256', $root.'/'.$path), 'bytes' => filesize($root.'/'.$path)];
        }
    }

    return $result;
};
$quote = static fn (string $name): string => '"'.str_replace('"', '""', $name).'"';
$fingerprint = static function (PDO $pdo) use ($quote): array {
    $result = [];
    $tables = $pdo->query("SELECT n.nspname,c.relname,c.relkind FROM pg_class c JOIN pg_namespace n ON n.oid=c.relnamespace
        WHERE n.nspname !~ '^pg_' AND n.nspname<>'information_schema' AND c.relkind IN ('r','m','f') ORDER BY 1,2")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($tables as $table) {
        if ($table['relkind'] === 'f') {
            throw new RuntimeException('Foreign table requires separate backup coverage');
        }
        $name = $quote($table['nspname']).'.'.$quote($table['relname']);
        // Canonical per-row JSON hashes sorted as a multiset, preserving duplicates and NULLs.
        $stmt = $pdo->query('SELECT md5(row_to_json(t)::text) AS h FROM '.$name.' t ORDER BY md5(row_to_json(t)::text) COLLATE "C"');
        $hash = hash_init('sha256');
        $count = 0;
        while (($row = $stmt->fetchColumn()) !== false) {
            hash_update($hash, $row."\n");
            $count++;
        }
        $result[$name] = ['rows' => $count, 'row_multiset_sha256' => hash_final($hash)];
    }

    return $result;
};
$structure = static function (PDO $pdo) use ($quote): array {
    $columns = $pdo->query("SELECT table_schema,table_name,column_name,ordinal_position,data_type,udt_name,is_nullable,column_default,is_identity,identity_generation
        FROM information_schema.columns WHERE table_schema !~ '^pg_' AND table_schema<>'information_schema'
        ORDER BY table_schema,table_name,ordinal_position")->fetchAll(PDO::FETCH_ASSOC);
    $sequences = [];
    foreach ($pdo->query("SELECT schemaname,sequencename FROM pg_sequences WHERE schemaname !~ '^pg_' ORDER BY 1,2")->fetchAll(PDO::FETCH_ASSOC) as $seq) {
        $name = $quote($seq['schemaname']).'.'.$quote($seq['sequencename']);
        $sequences[$name] = $pdo->query('SELECT last_value,is_called FROM '.$name)->fetch(PDO::FETCH_ASSOC);
    }

    return compact('columns', 'sequences');
};
$source = null;
$cluster = null;
$started = false;
$passfile = $backup.'/.pgpass';
$bin = '/usr/lib/postgresql/18/bin';
try {
    $manifest['git_head'] = trim($run(['git', 'rev-parse', 'HEAD']));
    $beforeFiles = $files();
    mkdir($backup.'/source', 0700);
    foreach ($beforeFiles as $path => $info) {
        $target = $backup.'/source/'.$path;
        if (! is_dir(dirname($target))) {
            mkdir(dirname($target), 0700, true);
        }
        if (isset($info['link'])) {
            symlink($info['link'], $target);
        } else {
            if (! copy($root.'/'.$path, $target) || hash_file('sha256', $target) !== $info['sha256']) {
                throw new RuntimeException('Source changed while copying: '.$path);
            }
        }
    }
    $manifest['source_files'] = $beforeFiles;
    putenv('PGOPTIONS=-c default_transaction_read_only=on');
    $app = require $root.'/bootstrap/app.php';
    $app->make(Kernel::class)->bootstrap();
    $connection = DB::connection();
    $source = $connection->getPdo();
    if ($source->getAttribute(PDO::ATTR_DRIVER_NAME) !== 'pgsql') {
        throw new RuntimeException('PostgreSQL required');
    }
    $cfg = $connection->getConfig();
    $source->exec('BEGIN TRANSACTION ISOLATION LEVEL REPEATABLE READ READ ONLY');
    $manifest['source_read_only'] = $source->query('SHOW transaction_read_only')->fetchColumn();
    $manifest['server_version'] = $source->query('SHOW server_version')->fetchColumn();
    $manifest['database_size_bytes'] = (int) $source->query('SELECT pg_database_size(current_database())')->fetchColumn();
    echo 'Read-only snapshot opened; database size: '.$manifest['database_size_bytes']." bytes\n";
    $expectedStructure = $structure($source);
    $snapshot = $source->query('SELECT pg_export_snapshot()')->fetchColumn();
    $escape = static fn ($v) => str_replace(['\\', ':'], ['\\\\', '\\:'], (string) $v);
    file_put_contents($passfile, '*:*:*:'.$escape($cfg['username']).':'.$escape($cfg['password'])."\n");
    chmod($passfile, 0600);
    $run([$bin.'/pg_dump', '--format=custom', '--snapshot='.$snapshot, '--file='.$backup.'/database.dump',
        '--host='.(string) $cfg['host'], '--port='.(string) $cfg['port'], '--username='.(string) $cfg['username'], '--dbname='.(string) $cfg['database'], '--no-password'],
        ['PGPASSFILE' => $passfile, 'PGPASSWORD' => false, 'PGOPTIONS' => '-c default_transaction_read_only=on']);
    unlink($passfile);
    echo "Dump captured; comparing database content.\n";
    $expected = $fingerprint($source);
    $source->exec('ROLLBACK');
    $source->exec('BEGIN TRANSACTION ISOLATION LEVEL REPEATABLE READ READ ONLY');
    $after = $fingerprint($source);
    $afterStructure = $structure($source);
    $source->exec('ROLLBACK');
    $manifest['production_table_content_stable'] = $expected === $after && $expectedStructure === $afterStructure;
    $manifest['source_files_stable'] = $beforeFiles === $files();
    $manifest['database_tables'] = $expected;
    $manifest['database_structure'] = $expectedStructure;
    $manifest['dump_sha256'] = hash_file('sha256', $backup.'/database.dump');
    $manifest['dump_bytes'] = filesize($backup.'/database.dump');
    $save();
    // A dump remains a valid DB snapshot even when later writes occurred; do not claim joint stability.
    if (! $manifest['production_table_content_stable'] || ! $manifest['source_files_stable']) {
        throw new RuntimeException('Baseline changed during capture; backup retained but not accepted as stable rehearsal baseline');
    }
    $cluster = sys_get_temp_dir().'/tw-restore-'.bin2hex(random_bytes(8));
    mkdir($cluster, 0700);
    mkdir($cluster.'/socket', 0700);
    $env = ['PGOPTIONS' => '', 'PGPASSFILE' => false, 'PGPASSWORD' => false];
    $run([$bin.'/initdb', '-D', $cluster.'/data', '-U', 'rehearsal', '--auth-local=trust', '--auth-host=reject', '--no-locale', '--encoding=UTF8'], $env);
    file_put_contents($cluster.'/data/postgresql.conf', "\nlisten_addresses = ''\nunix_socket_directories = '".$cluster."/socket'\nunix_socket_permissions = 0700\n", FILE_APPEND);
    $run([$bin.'/pg_ctl', '-D', $cluster.'/data', '-l', $cluster.'/server.log', '-w', 'start'], $env);
    $started = true;
    $run([$bin.'/createdb', '-h', $cluster.'/socket', '-U', 'rehearsal', 'restored_baseline'], $env);
    echo "Restoring into isolated cluster.\n";
    $run([$bin.'/pg_restore', '--exit-on-error', '--no-owner', '--no-privileges', '-h', $cluster.'/socket', '-U', 'rehearsal', '-d', 'restored_baseline', $backup.'/database.dump'], $env);
    $restored = new PDO('pgsql:host='.$cluster.'/socket;dbname=restored_baseline;user=rehearsal', options: [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $restored->exec('BEGIN TRANSACTION ISOLATION LEVEL REPEATABLE READ READ ONLY');
    $actual = $fingerprint($restored);
    $actualStructure = $structure($restored);
    $restored->exec('ROLLBACK');
    $manifest['restore_tables_match'] = $actual === $expected && $actualStructure === $expectedStructure;
    $manifest['restore_cluster'] = $cluster;
    $manifest['restore_ownership'] = 'Isolated rehearsal role; production owners/ACLs not applied, no global roles dump';
    if (! $manifest['restore_tables_match']) {
        throw new RuntimeException('Restored table content differs');
    }
    $manifest['state'] = 'backup_and_restore_verified';
    $manifest['finished_at'] = date(DATE_ATOM);
    echo 'Verified '.count($expected).' tables, '.array_sum(array_column($expected, 'rows'))." rows.\n";
} catch (Throwable $error) {
    $manifest['state'] = 'failed';
    $manifest['error'] = $error->getMessage();
    fwrite(STDERR, "Rehearsal failed; private manifest contains details.\n");
} finally {
    if ($source && $source->inTransaction()) {
        $source->rollBack();
    }
    if (is_file($passfile)) {
        unlink($passfile);
    }
    if ($started) {
        $run([$bin.'/pg_ctl', '-D', $cluster.'/data', '-m', 'fast', '-w', 'stop'], ['PGOPTIONS' => '']);
        $manifest['restore_cluster_stopped'] = true;
    }
    $save();
}
echo json_encode(['state' => $manifest['state'], 'manifest' => $backup.'/manifest.json'], JSON_THROW_ON_ERROR).PHP_EOL;
exit($manifest['state'] === 'backup_and_restore_verified' ? 0 : 1);
