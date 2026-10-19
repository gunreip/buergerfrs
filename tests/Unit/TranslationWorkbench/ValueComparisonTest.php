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

it('separates placeholders obsolete texts and real conflicts without hiding one-sided duplicates', function () {
    $row = fn ($id, $key, $value, $status) => ['id' => $id, 'translation_key' => $key, 'locale' => 'de', 'value' => $value, 'status' => $status];
    $legacy = [
        $row(1, 'empty', null, 'missing'), $row(2, 'old', 'Old', 'obsolete'),
        $row(3, 'only', 'Legacy', 'ok'), $row(4, 'changed', 'Before', 'ok'),
        $row(5, 'placeholder', null, 'ok'), $row(6, 'target-empty', 'Keep', 'ok'),
        $row(7, 'null-empty', null, 'missing'), $row(8, 'same', 'Same', 'ok'),
        $row(9, 'same-status', 'Same', 'ok'), $row(10, 'duplicate', 'Text', 'ok'),
        $row(11, 'duplicate', null, 'missing'), $row(12, 'whitespace', ' ', 'ok'),
    ];
    $workbench = [
        $row(1, 'changed', 'After', 'active'), $row(2, 'placeholder', 'Stored', 'active'),
        $row(3, 'target-empty', null, 'active'), $row(4, 'null-empty', '', 'missing'),
        $row(5, 'same', 'Same', 'obsolete'), $row(6, 'same-status', 'Same', 'active'),
        $row(7, 'foundation', 'New', 'active'),
    ];
    $report = (new TranslationWorkbenchValueComparison)->compare($legacy, $workbench);
    $categories = array_column($report['comparisons'], 'review_category', 'key');
    expect($categories)->toMatchArray([
        'empty' => 'legacy_only_without_text', 'old' => 'legacy_only_obsolete_text',
        'only' => 'legacy_only_text_requires_review', 'changed' => 'different_text_requires_review',
        'placeholder' => 'legacy_without_text', 'target-empty' => 'workbench_without_text',
        'null-empty' => 'null_empty_difference', 'same' => 'same_value_different_status',
        'same-status' => 'same_value_same_status', 'duplicate' => 'multiple_rows_require_review',
        'whitespace' => 'legacy_only_text_requires_review', 'foundation' => 'workbench_only',
    ])->and(array_sum($report['review_counts']))->toBe(count($report['comparisons']))
        ->and($report['review_counts']['multiple_rows_require_review'])->toBe(1);
});
