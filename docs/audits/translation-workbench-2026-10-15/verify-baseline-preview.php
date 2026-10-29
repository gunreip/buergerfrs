<?php

use Gunreip\TranslationWorkbench\Support\BaselineReaderPreview;
use Tests\Support\TranslationWorkbench\PublishedValueBaseline;

// Offline verification only: no application bootstrap, database connection or language-file execution.
$root = dirname(__DIR__, 3);
require $root.'/vendor/autoload.php';
$read = static fn (string $path) => json_decode(file_get_contents($path), true, flags: JSON_THROW_ON_ERROR);
$backup = $read(__DIR__.'/backup-restore-result-2026-10-19.json');
$manifestPath = $root.'/'.$backup['backup_manifest'];
$checks = 0;
$check = static function (bool $condition, string $message) use (&$checks): void {
    if (! $condition) {
        throw new RuntimeException($message);
    }
    $checks++;
};
$check(hash_file('sha256', $manifestPath) === $backup['backup_manifest_sha256'], 'Backup manifest changed');
$manifest = $read($manifestPath);
$baseline = $read(__DIR__.'/published-values-2026-10-19.json');
$path = $root.'/storage/app/private/workbench-previews/reader-preview.json';
$data = $read($path);
$check($data['schema'] === 1 && $data['mode'] === 'isolated_baseline_preview', 'Unexpected snapshot schema');
$check($data['input_dump_sha256'] === $baseline['dump_sha256'], 'Wrong baseline snapshot');
$evidence = (new PublishedValueBaseline)->read(dirname($manifestPath).'/source', $manifest['source_files']);
$values = [];
$allKeys = [];
foreach ($evidence['values'] as $row) {
    $values[$row['key']][$row['locale']] = $row;
    $allKeys[$row['key']] = true;
}
$expectedKeys = array_keys($allKeys);
$actualKeys = array_keys($data['items']);
sort($expectedKeys);
sort($actualKeys);
$check($expectedKeys === $actualKeys, 'Snapshot key coverage differs from saved language files');
$expectedLocales = array_values(array_unique(array_column($evidence['sources'], 'locale')));
sort($expectedLocales);
$check(($data['exported_locales'] ?? []) === $expectedLocales, 'Exported locales are incomplete');
$links = $read(__DIR__.'/existing-key-links-2026-10-19.json');
$check(hash_file('sha256', $root.'/'.$links['plan']) === $links['plan_sha256'], 'Key import plan changed');
$plan = $read($root.'/'.$links['plan']);
$expectedSources = [];
foreach ($plan['items'] as $item) {
    if ($item['state'] === 'blocked_structure') {
        $expectedSources[] = json_encode([$item['key'], $item['source_path'], $item['source_line']]);
    }
}
$actualSources = [];
$seenFindings = [];
foreach ($data['items'] as $key => $item) {
    foreach ($item['blocked_sources'] ?? [] as $source) {
        $check(! isset($seenFindings[$source['finding_id']]), 'Duplicate blocked finding');
        $seenFindings[$source['finding_id']] = true;
        $check($source['column'] > 0 && is_string($source['expression']), 'Invalid source detail');
        $check(($source['sha256'] ?? null) === ($manifest['source_files'][$source['path']]['sha256'] ?? false), 'Source checksum differs from backup manifest');
        $actualSources[] = json_encode([$key, $source['path'], $source['line']]);
    }
}
sort($expectedSources);
sort($actualSources);
$check($expectedSources === $actualSources, 'Blocked source evidence differs from import plan');
$service = new BaselineReaderPreview;
$states = [];
$chains = [];
foreach ($data['items'] as $key => $item) {
    $check($item['key'] === $key, 'Item key mismatch');
    $itemLocales = array_keys($item['locales']);
    sort($itemLocales);
    $check($itemLocales === $expectedLocales, 'Item locale coverage incomplete');
    foreach ($item['locales'] as $locale => $row) {
        $expected = $values[$key][$locale] ?? null;
        $published = $row['published'];
        $check(($expected === null) === ($published === null), "Presence differs: $key / $locale");
        if ($expected !== null) {
            $check($expected['value'] === $published['value'] && $expected['path'] === $published['path']
                && $published['sha256'] === $evidence['sources'][$expected['path']]['sha256'], "File evidence differs: $key / $locale");
            $check(in_array($published['state'], ['linked', 'blocked_structure', 'unassigned_key'], true), 'Unknown state');
            $check(($published['state'] === 'linked') === ($published['key_id'] !== null), 'Key link/state mismatch');
            if ($published['state'] === 'linked') {
                $check($published['key_id'] === $item['key_id'], 'Item key ID differs from value key ID');
            }
            $states[$published['state']] = ($states[$published['state']] ?? 0) + 1;
        }
        $check($row['editing'] === null, 'Unexpected draft in this baseline snapshot');
    }
    foreach ($data['chains'] as $name => $chain) {
        $expected = null;
        foreach ($chain as $position => $locale) {
            $check(array_key_exists($locale, $item['locales']), "Snapshot locale missing: $locale");
            if ($expected === null && isset($values[$key][$locale])) {
                $expected = [...$values[$key][$locale], 'fallback' => $position > 0];
            }
        }
        $result = $service->selection($data, $key, $name)['result'];
        $check(($expected === null) === ($result === null), 'Fallback presence differs');
        if ($expected !== null) {
            $check($result['value'] === $expected['value'] && $result['locale'] === $expected['locale']
                && $result['fallback'] === $expected['fallback'], 'Fallback result differs');
        }
        $kind = $result === null ? 'missing' : ($result['fallback'] ? 'fallback' : 'exact');
        $chains[$name][$kind] = ($chains[$name][$kind] ?? 0) + 1;
    }
}
$result = ['state' => 'preview_verified_against_saved_language_files', 'production_connected' => false,
    'snapshot_sha256' => hash_file('sha256', $path), 'keys' => count($data['items']),
    'checks' => $checks, 'exported_locales' => $expectedLocales, 'visible_value_states' => $states, 'chains' => $chains];
file_put_contents(__DIR__.'/baseline-preview-verification-2026-10-19.json', json_encode($result, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR)."\n");
echo json_encode($result, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR)."\n";
