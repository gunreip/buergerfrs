<?php

// Read-only evidence gathering. No scan, provider invocation, migration or publication.
$root = dirname(__DIR__, 3);
require $root.'/vendor/autoload.php';
$app = require $root.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$pdo = Illuminate\Support\Facades\DB::connection()->getPdo();
if ($pdo->getAttribute(PDO::ATTR_DRIVER_NAME) !== 'pgsql') {
    throw new RuntimeException('This audit requires PostgreSQL READ ONLY transactions.');
}
$pdo->exec('BEGIN TRANSACTION ISOLATION LEVEL REPEATABLE READ READ ONLY');
try {
    $query = function (string $sql, array $args = []) use ($pdo): array {
        $statement = $pdo->prepare($sql);
        $statement->execute($args);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    };
    $entries = $query('SELECT * FROM translation_workbench_entries ORDER BY id');
    $entries = array_column($entries, null, 'id');
    $values = $query('SELECT * FROM translation_workbench_values ORDER BY id');
    $dynamic = $query('SELECT * FROM translation_workbench_dynamic_values ORDER BY id');
    $out = ['captured_at' => date(DATE_ATOM), 'transaction' => 'REPEATABLE READ READ ONLY', 'unassigned_entries' => [], 'structured_values' => []];
    foreach ($values as $value) {
        $entry = $entries[$value['entry_id']];
        if (! $entry['translation_key'] && ! $entry['existing_key']) {
            $id = $entry['id'];
            if (! isset($out['unassigned_entries'][$id])) {
                $out['unassigned_entries'][$id] = [
                    'entry' => $entry,
                    'same_path_findings' => $query('SELECT f.id, f.source_line, f.raw_expression, f.status, f.meta, k.translation_key, k.status AS key_status, l.status AS link_status FROM translation_workbench_findings f JOIN translation_workbench_source_files s ON s.id=f.source_file_id LEFT JOIN translation_workbench_key_findings l ON l.finding_id=f.id LEFT JOIN translation_workbench_keys k ON k.id=l.key_id WHERE s.path=? ORDER BY f.source_line, f.id', [$entry['source_path']]),
                    'same_path_dynamic_sources' => $query('SELECT d.*, k.translation_key FROM translation_workbench_dynamic_sources d LEFT JOIN translation_workbench_keys k ON k.id=d.key_id WHERE d.source_path=? ORDER BY d.id', [$entry['source_path']]),
                    'values' => [],
                ];
            }
            $out['unassigned_entries'][$id]['values'][] = [
                'observation' => $value,
                'historical_translations' => array_values(array_filter($dynamic, fn ($row) => $row['entry_id'] === $id && $row['value_key'] === $value['value_key'])),
                // Text matches are search hints, never identity evidence.
                'same_text_english_values' => $query("SELECT id, translation_key, locale, value, status FROM translation_workbench_lang_values WHERE locale='en' AND value=? ORDER BY id", [$value['native_label']]),
            ];
            continue;
        }
        $decoded = json_decode($value['native_label'] ?? '', true);
        if (! is_array($decoded) || ! str_starts_with($value['value_key'], 'source:')) {
            continue;
        }
        $key = $entry['translation_key'] ?: $entry['existing_key'];
        $locale = substr($value['value_key'], 7);
        $leaves = [];
        $walk = function (array $node, string $prefix) use (&$walk, &$leaves, $query, $locale): void {
            foreach ($node as $part => $text) {
                $path = $prefix.'.'.$part;
                if (is_array($text)) {
                    $walk($text, $path);
                } else {
                    $targets = $query('SELECT id, translation_key, locale, value, status FROM translation_workbench_lang_values WHERE translation_key=? AND locale=? ORDER BY id', [$path, $locale]);
                    $leaves[] = ['key' => $path, 'value' => $text, 'targets' => $targets, 'exact_text_match' => count($targets) === 1 && $targets[0]['value'] === $text];
                }
            }
        };
        $walk($decoded, $key);
        $out['structured_values'][] = ['entry_id' => $entry['id'], 'entry_status' => $entry['status'], 'value_id' => $value['id'], 'key' => $key, 'leaves' => $leaves];
    }
    foreach ($out['unassigned_entries'] as &$group) {
        $entry = $group['entry'];
        $path = $root.'/'.$entry['source_path'];
        $content = is_file($path) ? file_get_contents($path) : false;
        $lines = $content === false ? [] : explode("\n", $content);
        $group['source_evidence'] = [
            'sha256' => $content === false ? null : hash('sha256', $content),
            'start_line' => max(1, $entry['source_line'] - 8),
            'excerpt' => array_slice($lines, max(0, $entry['source_line'] - 9), 17),
        ];
        $group['same_position_candidates'] = [];
        foreach ($group['same_path_findings'] as $finding) {
            if ($finding['source_line'] !== $entry['source_line'] || $finding['status'] !== 'active'
                || $finding['link_status'] !== 'active' || ! $finding['translation_key']
                || $finding['key_status'] === 'obsolete') {
                continue;
            }
            $group['same_position_candidates'][] = [
                'finding' => $finding,
                'dynamic_values' => $query('SELECT v.*, k.translation_key FROM translation_workbench_dynamic_key_values v JOIN translation_workbench_keys k ON k.id=v.key_id WHERE k.translation_key=? ORDER BY v.id', [$finding['translation_key']]),
            ];
        }
    }
    unset($group);
} finally {
    $pdo->exec('ROLLBACK');
}
file_put_contents(__DIR__.'/historical-value-evidence.json', json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR).PHP_EOL);
echo json_encode(['entries' => count($out['unassigned_entries']), 'containers' => count($out['structured_values'])]).PHP_EOL;
