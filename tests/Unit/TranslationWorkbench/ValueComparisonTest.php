<?php

use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchValueComparison;

it('compares exact texts while preserving duplicates normalized locales and original rows', function () {
    $row = fn ($id, $key, $locale, $value, $status = 'active') => ['id' => $id, 'translation_key' => $key, 'locale' => $locale, 'value' => $value, 'status' => $status];
    $legacy = [
        $row(1, 'ui.same', 'de_AT', 'Same', 'ok'),
        $row(2, 'ui.empty', 'de', null, 'missing'),
        $row(3, 'ui.spaces', 'de', 'Text ', 'ok'),
        $row(4, 'ui.duplicate', 'de', 'A', 'ok'),
        $row(5, 'ui.duplicate', 'de', 'A', 'ok'),
        $row(6, 'ui.legacy', 'de', 'Only', 'obsolete'),
        $row(7, 'ui.legacy', 'de', 'Only', 'obsolete'),
        $row(8, null, 'de', 'Orphan'),
    ];
    $workbench = [
        $row(1, 'ui.same', 'de-at', 'Same', 'obsolete'),
        $row(2, 'ui.empty', 'de', '', 'missing'),
        $row(3, 'ui.spaces', 'de', 'Text'),
        $row(4, 'ui.duplicate', 'de', 'A'),
        $row(5, 'ui.workbench', 'de', 'New'),
    ];
    $report = (new TranslationWorkbenchValueComparison)->compare($legacy, $workbench);
    expect($report['counts'])->toBe(['identical' => 1, 'different' => 2, 'legacy_only' => 1, 'workbench_only' => 1, 'ambiguous' => 1])
        ->and($report['status_differences'])->toBe(1)
        ->and($report['multiple_row_pairs'])->toBe(2)
        ->and($report['unassigned'])->toBe([['side' => 'legacy', 'row' => $legacy[7]]]);
    $same = array_values(array_filter($report['comparisons'], fn ($group) => $group['key'] === 'ui.same'))[0];
    expect($same['locale'])->toBe('de-at')->and($same['legacy'][0])->toBe($legacy[0]);
});

it('reports empty inventories without selecting a winner', function () {
    $report = (new TranslationWorkbenchValueComparison)->compare([], []);
    expect(array_sum($report['counts']))->toBe(0)->and($report['comparisons'])->toBe([])->and($report['unassigned'])->toBe([]);
});
