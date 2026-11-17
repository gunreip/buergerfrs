<?php

use Illuminate\Support\Str;

// Included by source-replacement.php: uses only its isolated PostgreSQL database and source tree.
$scanWriteProfile = [];
$profileApply = function (string $name, array $request, ?callable $fault = null) use ($db, $rescan, &$scanWriteProfile) {
    $db->preparedStatements = [];
    $db->measureStatements = true;
    $start = hrtime(true);
    try {
        return $rescan->apply((string) Str::uuid(), $request, $fault, actor: 7);
    } finally {
        $db->measureStatements = false;
        $count = static fn (string $prefix) => array_sum(array_filter($db->preparedStatements,
            static fn ($sql) => str_starts_with($sql, $prefix), ARRAY_FILTER_USE_KEY));
        $scanWriteProfile[$name] = [
            'apply_ms' => round((hrtime(true) - $start) / 1e6, 1),
            'prepared_statements' => array_sum($db->preparedStatements),
            'confirmation_inserts' => $count('INSERT INTO translation_workbench_scan_findings'),
            'source_lookups' => $count('SELECT id FROM translation_workbench_source_files'),
            'key_link_lookups' => array_sum(array_filter($db->preparedStatements,
                static fn ($sql) => str_starts_with($sql, 'SELECT ') && str_contains($sql, 'FROM translation_workbench_key_findings'), ARRAY_FILTER_USE_KEY)),
        ];
    }
};
$scope = ['batch-one.php', 'batch-two.php'];
foreach ($scope as $path) {
    $lines = array_map(static fn ($i) => "__('Batch literal {$path} {$i}');", range(1, 501));
    file_put_contents($dir.'/'.$path, "<?php\n".implode("\n", $lines)."\n");
}
$created = $profileApply('new', $rescan->prepare($scope));
$check(count($created['created']) === 1002, 'Batch fixture must create all independent findings');
$check($scanWriteProfile['new']['source_lookups'] === 2 && $scanWriteProfile['new']['confirmation_inserts'] === 3, 'New findings repeated per-finding source lookups or confirmation writes');
$unchanged = $snapshot();
$confirmed = $profileApply('unchanged', $rescan->prepare($scope));
$check($confirmed['confirmed'] === 1002 && $confirmed['created'] === [] && $snapshot() === $unchanged, 'Batched confirmations changed findings');
$check((int) $q('SELECT count(*) FROM translation_workbench_scan_findings WHERE scan_run_id=?', [$confirmed['scan']])->fetchColumn() === 1002, 'Batched confirmations lost or duplicated references');
$check($scanWriteProfile['unchanged']['confirmation_inserts'] === 3 && $scanWriteProfile['unchanged']['source_lookups'] === 0, 'Unchanged scan did not use bounded confirmation batches');
// Link two predecessors; the remaining unassigned findings must remain unassigned when moved.
$q('INSERT INTO translation_workbench_key_findings(finding_id,key_id) VALUES (?,?),(?,?)', [$created['created'][0], $batchKey, $created['created'][1], $batchKey]);
file_put_contents($dir.'/batch-one.php', "\n".file_get_contents($dir.'/batch-one.php'));
$moved = $profileApply('moved', $rescan->prepare($scope));
$check(count($moved['moved']) === 501 && $moved['confirmed'] === 501, 'Batch movement counts changed');
$check($scanWriteProfile['moved']['source_lookups'] === 1 && $scanWriteProfile['moved']['key_link_lookups'] === 2, 'Moved findings did not batch predecessor lookups');
$check((int) $q('SELECT count(*) FROM translation_workbench_scan_findings WHERE scan_run_id=?', [$moved['scan']])->fetchColumn() === 1002, 'Moved scan lost confirmation references');
foreach (array_slice($moved['moved'], 0, 3) as $i => $move) {
    $key = $q('SELECT key_id FROM translation_workbench_key_findings WHERE finding_id=? AND valid_to IS NULL', [$move['successor']])->fetchColumn();
    $check($i < 2 ? (int) $key === $batchKey : $key === false, 'Batch movement changed inherited key assignment');
}
// Failure after the final write must roll back all buffered/batched confirmations and the scan receipt.
$beforeScans = $q('SELECT count(*) FROM translation_workbench_scan_runs')->fetchColumn();
$beforeLinks = $q('SELECT count(*) FROM translation_workbench_scan_findings')->fetchColumn();
$reject(fn () => $profileApply('rollback', $rescan->prepare($scope), function ($path) {
    if ($path === 'batch-two.php') {
        throw new RuntimeException('Injected batch failure');
    }
}), 'Injected batch failure');
$check($q('SELECT count(*) FROM translation_workbench_scan_runs')->fetchColumn() === $beforeScans
    && $q('SELECT count(*) FROM translation_workbench_scan_findings')->fetchColumn() === $beforeLinks, 'Batch failure left partial scan evidence');
$bytes = file_get_contents($dir.'/batch-two.php');
try {
    $reject(fn () => $profileApply('concurrent_change', $rescan->prepare($scope), function ($path) use ($dir) {
        if ($path === 'batch-two.php') {
            file_put_contents($dir.'/batch-two.php', "\n", FILE_APPEND);
        }
    }), 'Source changed during rescan application');
    $check($q('SELECT count(*) FROM translation_workbench_scan_runs')->fetchColumn() === $beforeScans
        && $q('SELECT count(*) FROM translation_workbench_scan_findings')->fetchColumn() === $beforeLinks, 'Concurrent source change left batched writes committed');
} finally {
    file_put_contents($dir.'/batch-two.php', $bytes);
}
file_put_contents($dir.'/batch-two.php', "<?php\n");
$removed = $profileApply('removed', $rescan->prepare($scope));
$check(count($removed['removed']) === 501 && $removed['confirmed'] === 501 && $removed['created'] === [], 'Batched archival changed scan classification');
$check((int) $q("SELECT count(*) FROM translation_workbench_findings f JOIN translation_workbench_source_files s ON s.id=f.source_file_id WHERE s.path='batch-two.php' AND f.status='obsolete' AND f.retired_reason='Absent from completed scan inventory'")->fetchColumn() === 501, 'Batched archival lost retired findings or reasons');
$check($scanWriteProfile['removed']['key_link_lookups'] === 0, 'Removal fetched unused predecessor links');

// Historical confirmed listings must exclude captures created by that same run.
$scanEditor = new \Gunreip\TranslationWorkbench\Generation\GenerationEditor($db);
$confirmedPage = $scanEditor->scanPage($confirmed['scan'], 'confirmed', 2);
$check($confirmedPage->total() === 1002 && count($confirmedPage->items()) === 25, 'Confirmed scan pagination lost unchanged findings');
$check($scanEditor->scanPage($moved['scan'], 'confirmed', 1)->total() === 501, 'Confirmed category includes moved successors');
$check($scanEditor->scanCategoryCounts($confirmed['scan'])['confirmed'] === 1002, 'Scan category counts do not match confirmed results');

// Search the complete category before pagination, including literal wildcard characters.
foreach (['created' => $created['scan'], 'confirmed' => $confirmed['scan'], 'removed' => $removed['scan']] as $category => $runId) {
    $findingId = $created['created'][900];
    $filtered = $scanEditor->scanPage($runId, $category, 99, '#'.$findingId);
    $check($filtered->total() === 1 && (int) $filtered->items()[0]['id'] === $findingId && $filtered->currentPage() === 1, 'Finding-ID search must filter before pagination: '.$category);
}
$check($scanEditor->scanPage($confirmed['scan'], 'confirmed', 1, 'BATCH-TWO.PHP')->total() === 501, 'Source path search must be case insensitive');
$check($scanEditor->scanPage($confirmed['scan'], 'confirmed', 1, '%')->total() === 0, 'Search percent must not become a SQL wildcard');
$check($scanEditor->scanPage($confirmed['scan'], 'confirmed', 1, 'no-such-source')->total() === 0, 'Empty matches must have a zero total');
$assignedName = $q('SELECT full_name FROM translation_workbench_key_names WHERE key_id=? AND valid_to IS NULL', [$batchKey])->fetchColumn();
$check($scanEditor->scanPage($moved['scan'], 'moved', 1, $assignedName)->total() === 2, 'Moved finding search must include assigned translation keys without duplicating rows');
foreach (['created' => $created['scan'], 'confirmed' => $confirmed['scan'], 'moved' => $moved['scan'], 'removed' => $removed['scan']] as $category => $runId) {
    $sized = $scanEditor->scanPage($runId, $category, 2, '', 10);
    $check(count($sized->items()) === 10 && $sized->perPage() === 10 && $sized->currentPage() === 2, 'Scan page size not applied: '.$category);
}
$check($scanEditor->scanPage($confirmed['scan'], 'confirmed', 1, '', 99999)->perPage() === 25, 'Invalid page size must be bounded');

// A missing file can have several plausible successors. Its former assignment must not block the key group.
$relocationScope = ['relocation-cases'];
mkdir($dir.'/relocation-cases');
$oldPath = $dir.'/relocation-cases/old.php';
file_put_contents($oldPath, "<?php\n__('Decision');\n__('Removed label');\n");
$initial = $rescan->apply((string) Str::uuid(), $rescan->prepare($relocationScope), actor: 7);
$oldFinding = $initial['created'][0];
$q('INSERT INTO translation_workbench_key_findings(finding_id,key_id) VALUES (?,?)', [$oldFinding, $batchKey]);
unlink($oldPath);
foreach (['first', 'second'] as $name) {
    file_put_contents($dir.'/relocation-cases/'.$name.'.php', "<?php\n__('Decision');\n");
}
$request = $rescan->prepare($relocationScope);
$check(count($request['plan']['relocations']) === 1 && count($request['plan']['relocations'][0]['candidates']) === 2, 'Retain both possible destinations as evidence');
$reconciled = $rescan->apply((string) Str::uuid(), $request, actor: 7);
$check($reconciled['removed'] === $initial['created'] && count($reconciled['created']) === 2 && !$reconciled['moved'], 'Archive missing occurrences and capture both actual occurrences independently');
$check(!$q('SELECT 1 FROM translation_workbench_key_findings WHERE finding_id=? AND valid_to IS NULL', [$oldFinding])->fetchColumn(), 'Missing occurrence still has a current key assignment');
$repeat = $rescan->apply((string) Str::uuid(), $rescan->prepare($relocationScope), actor: 7);
$check($repeat['confirmed'] === 2 && !$repeat['created'] && !$repeat['removed'] && !$repeat['moved'], 'Unchanged rescan must confirm both occurrences without duplicates');

$survivingUsage = $q('SELECT id AS link,finding_id AS finding FROM translation_workbench_key_findings WHERE key_id=? AND valid_to IS NULL ORDER BY id LIMIT 1', [$batchKey])->fetch(PDO::FETCH_ASSOC);
$retiredUsage = $q('SELECT id AS link,finding_id AS finding FROM translation_workbench_key_findings WHERE key_id=? AND finding_id=?', [$batchKey, $oldFinding])->fetch(PDO::FETCH_ASSOC);
$reviewed = ['key_id' => $batchKey, 'usages' => [$survivingUsage, $retiredUsage]];
$current = ['key_id' => $batchKey, 'usages' => [$survivingUsage]];
$check(\Gunreip\TranslationWorkbench\Generation\ReviewUsageContinuity::contextMatches($db, $reviewed, $current), 'A receipted scan removal must preserve approval for surviving reviewed usages');
$check(!\Gunreip\TranslationWorkbench\Generation\ReviewUsageContinuity::contextMatches($db, $reviewed, ['key_id' => $batchKey, 'usages' => []]), 'An unexplained missing usage must still invalidate approval');

// Preserve two distinct occurrences when only the first call is reformatted.
$formatScope = ['format.php'];
file_put_contents($dir.'/format.php', "<?php\n__('Already current');\n\n\n__('Already current');\n");
$original = $rescan->apply((string) Str::uuid(), $rescan->prepare($formatScope), actor: 7);
[$first, $sibling] = $original['created'];
$link = $q('INSERT INTO translation_workbench_key_findings(finding_id,key_id) VALUES (?,?) RETURNING id', [$first, $batchKey])->fetchColumn();
file_put_contents($dir.'/format.php', "<?php\n__(\n  'Already current',\n);\n__('Already current');\n");
$formatted = $rescan->apply((string) Str::uuid(), $rescan->prepare($formatScope), actor: 7);
$check(count($formatted['moved']) === 1 && $formatted['moved'][0]['predecessor'] === $first
    && $formatted['moved'][0]['evidence']['method'] === 'formatting_only' && !$formatted['created'] && !$formatted['removed'], 'Reformat must create just one linked successor');
$next = $formatted['moved'][0]['successor'];
$nextLink = $q('SELECT id FROM translation_workbench_key_findings WHERE finding_id=? AND key_id=? AND valid_to IS NULL', [$next,$batchKey])->fetchColumn();
$check(\Gunreip\TranslationWorkbench\Generation\ReviewUsageContinuity::contextMatches($db,
    ['key_id' => $batchKey,'usages' => [['finding' => $first,'link' => $link]]],
    ['key_id' => $batchKey,'usages' => [['finding' => $next,'link' => $nextLink]]]), 'Reformatted successor must retain the reviewed usage');
$again = $rescan->apply((string) Str::uuid(), $rescan->prepare($formatScope), actor: 7);
$check($again['confirmed'] === 2 && !$again['moved'] && !$again['created'] && !$again['removed']
    && $q('SELECT status FROM translation_workbench_findings WHERE id=?', [$sibling])->fetchColumn() === 'active', 'Second scan must confirm the successor and the untouched sibling');
