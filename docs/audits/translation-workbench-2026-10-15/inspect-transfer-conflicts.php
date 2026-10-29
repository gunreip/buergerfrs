<?php

// Targeted read-only text evidence; no scanner, importer, publication or provider execution.
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchOption;
use Gunreip\TranslationWorkbench\Generation\BaselineValueReader;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;

$category = $argv[1] ?? 'value_conflict';
if (! in_array($category, ['value_conflict', 'missing_value_evidence', 'database_only'], true)) {
    throw new InvalidArgumentException('Supported categories: value_conflict, missing_value_evidence, database_only');
}
$root = dirname(__DIR__, 3);
$input = __DIR__.'/transfer-checklist-2026-10-19.json';
$checklist = json_decode(file_get_contents($input), true, flags: JSON_THROW_ON_ERROR);
putenv('PGOPTIONS=-c default_transaction_read_only=on');
require $root.'/vendor/autoload.php';
$app = require $root.'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();
$pdo = DB::connection()->getPdo();
if ($pdo->getAttribute(PDO::ATTR_DRIVER_NAME) !== 'pgsql') {
    throw new RuntimeException('PostgreSQL required');
}
$pdo->exec('BEGIN TRANSACTION ISOLATION LEVEL REPEATABLE READ READ ONLY');
$query = static function (string $sql, array $params) use ($pdo): array {
    $statement = $pdo->prepare($sql);
    $statement->execute($params);

    return $statement->fetchAll(PDO::FETCH_ASSOC);
};
$report = ['captured_at' => date(DATE_ATOM), 'mode' => 'read_only_targeted_evidence', 'category' => $category,
    'checklist_sha256' => hash_file('sha256', $input), 'snapshot_captured_at' => $checklist['source_captured_at'],
    'transaction_read_only' => $pdo->query('SHOW transaction_read_only')->fetchColumn(), 'items' => []];
try {
    $dynamic = [];
    foreach ($query('SELECT v.id,v.key_id,v.value_key,v.locale,v.value,v.status,v.updated_at,k.translation_key FROM translation_workbench_dynamic_key_values v JOIN translation_workbench_keys k ON k.id=v.key_id', []) as $option) {
        try {
            $optionKey = TranslationWorkbenchOption::key((string) $option['translation_key'], (string) $option['value_key']);
            $dynamic[$option['locale']][$optionKey][] = $option;
        } catch (InvalidArgumentException) {
            // Not a valid destination for the targeted exact keys; inventory audit records these separately.
        }
    }
    foreach ($checklist['items'] as $item) {
        if ($item['state'] !== $category) {
            continue;
        }
        $key = $item['key'];
        $locale = $item['locale'];
        $rows = [];
        foreach ($query('SELECT v.id,v.value,v.status,v.updated_at,k.id AS key_id FROM translation_values v JOIN translation_keys k ON k.id=v.translation_key_id WHERE k.key=? AND v.locale=? ORDER BY v.id', [$key, $locale]) as $row) {
            $rows[] = ['source' => 'legacy', 'current_candidate' => $row['status'] === 'ok', ...$row];
        }
        foreach ($query('SELECT id,value,status,value_type,source_path,source_hash,updated_at FROM translation_workbench_lang_values WHERE translation_key=? AND locale=? ORDER BY id', [$key, $locale]) as $row) {
            $rows[] = ['source' => 'workbench', 'current_candidate' => $row['status'] === 'active', ...$row];
        }
        $optionParents = [];
        foreach ($dynamic[$locale][$key] ?? [] as $option) {
            $rows[] = ['source' => 'dynamic_workbench', 'current_candidate' => $option['status'] === 'active', ...$option];
            if ($option['status'] === 'active') {
                $optionParents[] = $option['translation_key'];
            }
        }
        if (! preg_match('/^[A-Za-z0-9_-]+$/D', $locale) || ! preg_match('/^[A-Za-z0-9_-]+(?:\.[A-Za-z0-9_%~-]+)+$/D', $key)) {
            throw new RuntimeException('Unsupported target identity');
        }
        $group = explode('.', $key, 2)[0];
        $relative = 'lang/'.$locale.'/'.$group.'.php';
        $path = $root.'/'.$relative;
        $file = ['path' => $relative, 'exists' => is_file($path)];
        if ($file['exists']) {
            $file['sha256'] = hash_file('sha256', $path);
            foreach ((new BaselineValueReader)->phpFile($path, $locale, $group, $file['sha256']) as $value) {
                if ($value['key'] === $key) {
                    $rows[] = ['source' => 'file', 'id' => $relative, 'status' => 'observed', 'current_candidate' => true, 'value' => $value['value']];
                }
            }
            $file['unchanged_after_read'] = hash_file('sha256', $path) === $file['sha256'];
        }
        foreach ($rows as &$row) {
            $row['hash'] = $row['value'] === null ? null : hash('sha256', $row['value']);
        }
        unset($row);
        $current = array_values(array_filter($rows, fn ($r) => $r['current_candidate']));
        $signature = static function (array $evidence): array {
            $result = array_map(fn ($r) => json_encode([$r['source'], (string) $r['id'], $r['status'], $r['hash']], JSON_THROW_ON_ERROR), $evidence);
            sort($result);

            return $result;
        };
        $hashes = array_unique(array_column($current, 'hash'), SORT_REGULAR);
        $findings = $query("SELECT f.id,f.status,f.source_line,f.source_column,f.found_translation_key,f.existing_key,s.path
            FROM translation_workbench_findings f JOIN translation_workbench_source_files s ON s.id=f.source_file_id
            WHERE f.status='active' AND (f.found_translation_key=? OR f.existing_key=? OR EXISTS
                (SELECT 1 FROM translation_workbench_key_findings l JOIN translation_workbench_keys k ON k.id=l.key_id
                WHERE l.finding_id=f.id AND l.status='active' AND k.translation_key=?)) ORDER BY f.id", [$key, $key, $key]);
        $parentFindings = [];
        foreach (array_unique($optionParents) as $parent) {
            $parentFindings[$parent] = $query("SELECT DISTINCT f.id,f.status,f.source_line,s.path
                FROM translation_workbench_findings f JOIN translation_workbench_source_files s ON s.id=f.source_file_id
                WHERE f.status='active' AND (f.found_translation_key=? OR f.existing_key=? OR EXISTS
                    (SELECT 1 FROM translation_workbench_key_findings l JOIN translation_workbench_keys k ON k.id=l.key_id
                    WHERE l.finding_id=f.id AND l.status='active' AND k.translation_key=?)) ORDER BY f.id", [$parent, $parent, $parent]);
        }
        $relatedFiles = [];
        $comparisonLocales = ['de', 'en'];
        foreach (glob($root.'/lang/*', GLOB_ONLYDIR) ?: [] as $folder) {
            $candidateLocale = basename($folder);
            if (strtolower(str_replace('_', '-', $candidateLocale)) === strtolower(str_replace('_', '-', $locale))) {
                $comparisonLocales[] = $candidateLocale;
            }
        }
        foreach (array_unique($comparisonLocales) as $comparisonLocale) {
            if ($comparisonLocale === $locale) {
                continue;
            }
            $comparisonPath = 'lang/'.$comparisonLocale.'/'.$group.'.php';
            if (! is_file($root.'/'.$comparisonPath)) {
                continue;
            }
            $comparisonHash = hash_file('sha256', $root.'/'.$comparisonPath);
            foreach ((new BaselineValueReader)->phpFile($root.'/'.$comparisonPath, $comparisonLocale, $group, $comparisonHash) as $value) {
                if ($value['key'] === $key) {
                    $relatedFiles[] = ['locale' => $comparisonLocale, 'path' => $comparisonPath,
                        'value' => $value['value'], 'hash' => hash('sha256', $value['value']), 'file_sha256' => $comparisonHash,
                        'unchanged_after_read' => hash_file('sha256', $root.'/'.$comparisonPath) === $comparisonHash,
                        'comparison_only_not_runtime_resolution' => true];
                }
            }
        }
        $report['items'][] = ['review_id' => $item['review_id'], 'locale' => $locale, 'key' => $key,
            'same_evidence_as_snapshot' => $signature($current) === $signature($item['evidence']),
            'current_distinct_hashes' => count($hashes),
            'current_null_records' => count(array_filter($current, fn ($r) => $r['value'] === null)),
            'current_nonnull_records' => count(array_filter($current, fn ($r) => $r['value'] !== null)), 'file' => $file, 'evidence' => $rows,
            'active_finding_references' => $findings, 'option_parent_finding_references' => $parentFindings, 'related_file_evidence' => $relatedFiles, 'decision' => null];
    }
} finally {
    $pdo->exec('ROLLBACK');
}
$report['finished_at'] = date(DATE_ATOM);
echo json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR).PHP_EOL;
