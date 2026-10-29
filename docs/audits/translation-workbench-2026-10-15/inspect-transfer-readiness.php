<?php

// Read-only evidence inventory. No scanner run, importer, provider execution or publication.
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchOption;
use Gunreip\TranslationWorkbench\Generation\BaselineValueReader;
use Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchScanner;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;

$root = dirname(__DIR__, 3);
putenv('PGOPTIONS=-c default_transaction_read_only=on');
require $root.'/vendor/autoload.php';
$app = require $root.'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();
$pdo = DB::connection()->getPdo();
if ($pdo->getAttribute(PDO::ATTR_DRIVER_NAME) !== 'pgsql') {
    throw new RuntimeException('PostgreSQL required');
}
$pdo->exec('BEGIN TRANSACTION ISOLATION LEVEL REPEATABLE READ READ ONLY');
$report = ['captured_at' => date(DATE_ATOM), 'mode' => 'inventory_only_no_data_changes',
    'transaction' => ['isolation' => $pdo->query('SHOW transaction_isolation')->fetchColumn(),
        'read_only' => $pdo->query('SHOW transaction_read_only')->fetchColumn()],
    'tables' => [], 'scalar_evidence' => []];
$identifier = static fn ($name) => '"'.str_replace('"', '""', $name).'"';
try {
    $tables = $pdo->query("SELECT tablename FROM pg_tables WHERE schemaname='public' AND (tablename LIKE 'translation%' OR tablename LIKE 'dynamic_translation%') ORDER BY tablename")->fetchAll(PDO::FETCH_COLUMN);
    foreach ($tables as $table) {
        $quoted = $identifier($table);
        $stmt = $pdo->prepare("SELECT column_name,data_type FROM information_schema.columns WHERE table_schema='public' AND table_name=? ORDER BY ordinal_position");
        $stmt->execute([$table]);
        $columns = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        $item = ['rows' => (int) $pdo->query("SELECT count(*) FROM $quoted")->fetchColumn(), 'columns' => $columns];
        foreach (['status', 'review_status', 'value_type', 'locale', 'namespace', 'source', 'source_type', 'classification', 'origin', 'cardinality', 'locale_role'] as $column) {
            if (isset($columns[$column])) {
                $c = $identifier($column);
                $item['distributions'][$column] = $pdo->query("SELECT $c AS value,count(*) AS count FROM $quoted GROUP BY $c ORDER BY count(*) DESC")->fetchAll(PDO::FETCH_ASSOC);
            }
        }
        foreach (['value', 'native_label', 'literal_text', 'literal_text_suggested', 'suggested_key'] as $column) {
            if (isset($columns[$column])) {
                $c = $identifier($column);
                $item['content'][$column] = $pdo->query("SELECT count(*) FILTER(WHERE $c IS NULL) AS nulls,count(*) FILTER(WHERE $c='') AS empty_strings,max(length($c)) AS max_characters FROM $quoted")->fetch(PDO::FETCH_ASSOC);
            }
        }
        if (isset($columns['options'])) {
            $item['options_shapes'] = $pdo->query("SELECT jsonb_typeof(options::jsonb) AS type,count(*) AS count FROM $quoted GROUP BY 1")->fetchAll(PDO::FETCH_ASSOC);
        }
        if (isset($columns['value_key'])) {
            $item['option_key_shapes'] = $pdo->query("SELECT CASE WHEN value_key IS NULL THEN 'null' WHEN value_key ~ '^[0-9]+$' THEN 'numeric_text' WHEN position('.' in value_key)>0 THEN 'dotted_text' ELSE 'other_text' END AS shape,count(*) AS count FROM $quoted GROUP BY 1")->fetchAll(PDO::FETCH_ASSOC);
        }
        $report['tables'][$table] = $item;
    }
    $queries = [
        'legacy' => 'SELECT v.id,k.key AS key,v.locale,v.value,v.status FROM translation_values v JOIN translation_keys k ON k.id=v.translation_key_id',
        'workbench' => 'SELECT id,translation_key AS key,locale,value,status,value_type,namespace FROM translation_workbench_lang_values',
    ];
    foreach ($queries as $source => $sql) {
        foreach ($pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC) as $row) {
            // Values stay in memory for comparison; report stores hashes and source IDs, never credentials or full texts.
            $report['scalar_evidence'][] = ['source' => $source, 'id' => $row['id'], 'key' => $row['key'], 'locale' => $row['locale'],
                'status' => $row['status'], 'value_type' => $row['value_type'] ?? 'text',
                'namespace' => $row['namespace'] ?? null,
                'hash' => $row['value'] === null ? null : hash('sha256', $row['value'])];
        }
    }
    $report['dynamic_key_errors'] = [];
    foreach ($pdo->query('SELECT v.id,v.key_id,v.value_key,v.locale,v.value,v.status,k.translation_key FROM translation_workbench_dynamic_key_values v JOIN translation_workbench_keys k ON k.id=v.key_id')->fetchAll(PDO::FETCH_ASSOC) as $row) {
        try {
            $fullKey = TranslationWorkbenchOption::key((string) $row['translation_key'], (string) $row['value_key']);
            $report['scalar_evidence'][] = ['source' => 'dynamic_workbench', 'id' => $row['id'], 'key_id' => $row['key_id'], 'value_key' => $row['value_key'],
                'key' => $fullKey, 'locale' => $row['locale'], 'status' => $row['status'], 'value_type' => 'string', 'namespace' => null,
                'hash' => $row['value'] === null ? null : hash('sha256', $row['value'])];
        } catch (InvalidArgumentException $error) {
            $report['dynamic_key_errors'][] = ['id' => $row['id'], 'error' => $error->getMessage()];
        }
    }
    $report['languages'] = $pdo->query('SELECT locale,is_default,is_enabled_for_translation,is_enabled_for_app FROM translation_languages ORDER BY locale')->fetchAll(PDO::FETCH_ASSOC);
    $report['checks'] = [];
    foreach ([
        'active_findings' => "SELECT count(*) FROM translation_workbench_findings WHERE status='active'",
        'active_links_to_obsolete_findings' => "SELECT count(*) FROM translation_workbench_key_findings l JOIN translation_workbench_findings f ON f.id=l.finding_id WHERE l.status='active' AND f.status='obsolete'",
        'multiple_active_named_key_ids' => "SELECT count(*) FROM (SELECT f.id FROM translation_workbench_findings f JOIN translation_workbench_key_findings l ON l.finding_id=f.id AND l.status='active' JOIN translation_workbench_keys k ON k.id=l.key_id AND k.status<>'obsolete' WHERE f.status='active' AND nullif(k.translation_key,'') IS NOT NULL GROUP BY f.id HAVING count(DISTINCT k.id)>1) s",
    ] as $name => $sql) {
        $report['checks'][$name] = ['sql' => $sql, 'count' => (int) $pdo->query($sql)->fetchColumn()];
    }
} finally {
    $pdo->exec('ROLLBACK');
}
$report['config'] = [];
foreach (['paths', 'exclude_paths', 'exclude_directory_names', 'file_patterns', 'ignored_filename_contains', 'source_locale'] as $key) {
    $report['config'][$key] = config('translation-workbench.'.$key);
}
$report['source_files'] = [];
foreach ($app->make(TranslationWorkbenchScanner::class)->scannableFiles() as $file) {
    $path = $file->getRealPath();
    $report['source_files'][str_replace($root.'/', '', $path)] = hash_file('sha256', $path);
}
ksort($report['source_files']);
$loader = $app->make('translation.loader');
$roots = [];
foreach ($loader->paths() as $path) {
    $roots[] = ['path' => $path, 'namespace' => null];
}
foreach ($loader->namespaces() as $namespace => $path) {
    $roots[] = ['path' => $path, 'namespace' => $namespace];
}
foreach ($loader->jsonPaths() as $path) {
    $roots[] = ['path' => $path, 'namespace' => null];
}
$report['lang_roots'] = $roots;
$report['lang_files'] = [];
$reader = new BaselineValueReader;
foreach ($roots as $entry) {
    if (! is_dir($entry['path'])) {
        continue;
    }
    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($entry['path'], FilesystemIterator::SKIP_DOTS)) as $file) {
        if (! $file->isFile() || ! in_array($file->getExtension(), ['php', 'json'], true)) {
            continue;
        }
        $path = $file->getPathname();
        if (isset($report['lang_files'][$path])) {
            continue;
        }
        $relative = substr($path, strlen(rtrim($entry['path'], '/')) + 1);
        $parts = explode('/', $relative);
        $record = ['path' => str_replace($root.'/', '', $path), 'namespace' => $entry['namespace'], 'relative' => $relative,
            'sha256' => hash_file('sha256', $path), 'bytes' => $file->getSize()];
        try {
            if ($file->getExtension() !== 'php' || count($parts) !== 2 || $entry['namespace'] !== null) {
                throw new RuntimeException('Outside current prototype: JSON, namespace or nested file path');
            }
            $rows = $reader->phpFile($path, $parts[0], pathinfo($parts[1], PATHINFO_FILENAME), $record['sha256']);
            $record['reader'] = 'supported';
            $record['scalar_values'] = count($rows);
            foreach ($rows as $row) {
                $report['scalar_evidence'][] = ['source' => 'file', 'id' => $record['path'], 'key' => $row['key'], 'locale' => $row['locale'], 'status' => 'observed',
                    'value_type' => 'string', 'namespace' => null, 'hash' => hash('sha256', $row['value'])];
            }
        } catch (Throwable $error) {
            $record['reader'] = 'blocked';
            $record['reader_reason'] = $error->getMessage();
        }
        try {
            $reader->treeFromBytes(file_get_contents($path));
            $record['publisher_tree'] = 'supported_syntax';
        } catch (Throwable $error) {
            $record['publisher_tree'] = 'blocked';
            $record['publisher_reason'] = $error->getMessage();
        }
        $record['unchanged_after_read'] = hash_file('sha256', $path) === $record['sha256'];
        $report['lang_files'][$path] = $record;
    }
}
$report['lang_files'] = array_values($report['lang_files']);
// Compare exact full key and locale. The old 'namespace' column is a file group,
// as evidenced by TranslationWorkbenchLangValueImporter::rowsForFile(), not a package namespace.
$sets = ['legacy_ok' => [], 'workbench_active' => [], 'application_files' => [], 'dynamic_active' => []];
foreach ($report['scalar_evidence'] as $row) {
    if ($row['hash'] === null) {
        continue;
    }
    $set = match (true) {
        $row['source'] === 'legacy' && $row['status'] === 'ok' => 'legacy_ok',
        $row['source'] === 'dynamic_workbench' && $row['status'] === 'active' => 'dynamic_active',
        $row['source'] === 'workbench' && $row['status'] === 'active' => 'workbench_active',
        $row['source'] === 'file' && str_starts_with($row['id'], 'lang/') => 'application_files',
        default => null,
    };
    if ($set !== null) {
        $identity = json_encode([$row['locale'], $row['key']], JSON_THROW_ON_ERROR);
        $sets[$set][$identity][] = $row;
    }
}
foreach ([['legacy_ok', 'workbench_active'], ['workbench_active', 'application_files'], ['dynamic_active', 'application_files']] as [$left, $right]) {
    $comparison = ['left_only' => [], 'right_only' => [], 'different' => [], 'equal' => 0];
    foreach ($sets[$left] as $identity => $rows) {
        if (! isset($sets[$right][$identity])) {
            $comparison['left_only'][] = json_decode($identity);

            continue;
        }
        $a = array_unique(array_column($rows, 'hash'));
        $b = array_unique(array_column($sets[$right][$identity], 'hash'));
        sort($a);
        sort($b);
        if ($a === $b) {
            $comparison['equal']++;
        } else {
            $comparison['different'][] = ['identity' => json_decode($identity), 'left' => $rows, 'right' => $sets[$right][$identity]];
        }
    }
    foreach (array_diff_key($sets[$right], $sets[$left]) as $identity => $rows) {
        $comparison['right_only'][] = json_decode($identity);
    }
    $report['comparisons'][$left.'__'.$right] = $comparison;
}
$report['archived_directories_in_scan_scope'] = array_values(array_filter(array_keys($report['source_files']), static fn ($path) => array_intersect(explode('/', $path), ['_old', '-old']) !== []));
$report['source_files_changed_during_audit'] = [];
foreach ($report['source_files'] as $path => $hash) {
    if (! is_file($root.'/'.$path) || hash_file('sha256', $root.'/'.$path) !== $hash) {
        $report['source_files_changed_during_audit'][] = $path;
    }
}
$report['finished_at'] = date(DATE_ATOM);
file_put_contents(__DIR__.'/transfer-readiness-2026-10-19.json', json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)."\n");
echo json_encode(['tables' => count($report['tables']), 'source_files' => count($report['source_files']), 'lang_files' => count($report['lang_files']), 'mode' => $report['transaction']]).PHP_EOL;
