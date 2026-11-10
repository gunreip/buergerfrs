<?php

// Uses the isolated database from source-replacement.php; no production data is read or changed.
$missingSource = (int) $q("INSERT INTO translation_workbench_source_files(path) VALUES ('legacy-capture.php') RETURNING id")->fetchColumn();
$missingLine = 0;
$missingFixture = function (?string $source, ?string $literal, ?string $suggested) use ($q, $missingSource, $scan, &$missingLine) {
    return (int) $q("INSERT INTO translation_workbench_findings(source_file_id,captured_scan_id,source_line,source_column,raw_expression,raw_source_code_literal,raw_literal,suggested_literal,suggested_key,provenance)
        VALUES (?,?,?,1,?,?,?,?,?,'{}') RETURNING id", [$missingSource, $scan, ++$missingLine, "__('".($source ?? 'dynamic')."')", $source, $literal, $suggested, 'legacy.capture.'.$missingLine])->fetchColumn();
};
$eligible = [];
for ($i = 0; $i < 27; $i++) {
    $eligible[] = $missingFixture('ui.legacy.value', null, null);
}
$excluded = [
    $missingFixture('Save', 'Save', null),
    $missingFixture('ui.legacy.value', null, 'Save'),
    $missingFixture(null, null, null),
    $missingFixture('ui.legacy.archived', null, null),
];
$outside = $missingFixture('ui.outside.scope', null, null);
$q("UPDATE translation_workbench_findings SET status='obsolete',retired_at=now(),retired_reason='Fixture archival' WHERE id=?", [$excluded[3]]);
$missingRun = (string) \Illuminate\Support\Str::uuid();
$q("INSERT INTO translation_workbench_scan_runs VALUES (?,'completed',?::jsonb,now())", [$missingRun, json_encode(['reviews' => ['legacy-capture.php' => ['preserved' => [...$eligible, ...$excluded]]]])]);
$q("INSERT INTO translation_workbench_operations(id,kind,request_fingerprint,result) VALUES (?,'complete_rescan',?,'{}')", [$missingRun, str_repeat('a', 64)]);
// One finding is both confirmed and preserved: it must still be listed only once.
$q('INSERT INTO translation_workbench_scan_findings VALUES (?,?)', [$missingRun, $eligible[0]]);
$beforeMissingRead = $snapshot();
$page = $editor->scanPage($missingRun, 'missing_immutable', 1);
$check($page->total() === 27 && count($page->items()) === 25, 'Missing immutable view included normal NULL fields or duplicated references');
$check(array_map('intval', array_column($page->items(), 'id')) === array_slice($eligible, 0, 25), 'Missing immutable view lost confirmed or preserved findings');
$last = $editor->scanPage($missingRun, 'missing_immutable', 999);
$check($last->currentPage() === 2 && count($last->items()) === 2, 'Missing immutable pagination did not clamp');
$check($beforeMissingRead === $snapshot(), 'Missing immutable review changed immutable capture data');
$reject(fn () => $editor->scanPage('invalid', 'missing_immutable', 1), 'Scan not found');
$reject(fn () => $editor->scanPage((string) \Illuminate\Support\Str::uuid(), 'missing_immutable', 1), 'Scan not found');
$check($editor->scanPage($missingRun, 'missing_immutable', 1, '#'.$eligible[26])->total() === 1, 'Missing information search must reach findings after the first page');
$check($editor->scanPage($missingRun, 'missing_immutable', 1, 'UI.LEGACY.VALUE')->total() === 27, 'Captured translation-key search must be case insensitive');
$sizedMissing = $editor->scanPage($missingRun, 'missing_immutable', 3, 'ui.legacy', 10);
$check($sizedMissing->total() === 27 && count($sizedMissing->items()) === 7 && $sizedMissing->perPage() === 10, 'Filtered missing information page size or offset incorrect');
