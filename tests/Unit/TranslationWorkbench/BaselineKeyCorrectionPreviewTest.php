<?php

use Gunreip\TranslationWorkbench\Support\BaselineKeyCorrectionPreview;

function correctionSnapshot(): array
{
    return ['items' => ['ui.type' => ['locales' => ['en' => ['published' => ['state' => 'blocked_structure', 'value' => 'Type']]],
        'blocked_sources' => [['finding_id' => 1], ['finding_id' => 2]]]]];
}

it('checks explicit targets without changing captured evidence', function () {
    $data = correctionSnapshot();
    $before = $data;
    $preview = new BaselineKeyCorrectionPreview;
    $result = $preview->inspect($data, 'ui.type', 'ui.type.type');
    expect($result['status'])->toBe('no_snapshot_conflict')
        ->and($result['source_count'])->toBe(2)->and($result['locales'])->toBe(['en'])
        ->and($data)->toBe($before);
    expect($preview->inspect(null, 'ui.type', 'ui.type.type')['status'])->toBe('unavailable')
        ->and($preview->inspect($data, 'unknown', 'ui.type.type')['status'])->toBe('unavailable')
        ->and($preview->inspect($data, 'ui.type', 'ui.type')['status'])->toBe('unchanged');
});

it('rejects invalid replacement structures', function (string $target) {
    expect((new BaselineKeyCorrectionPreview)->inspect(correctionSnapshot(), 'ui.type', $target)['status'])->toBe('invalid');
})->with(['', 'ui.other', 'ui..type', 'ui.type.', 'ui.type.bad key', 'ui.type.<script>']);

it('reports exact and bidirectional scalar array conflicts with segment boundaries', function (string $existing, ?string $relation) {
    $data = correctionSnapshot();
    $data['items'][$existing] = ['locales' => []];
    $result = (new BaselineKeyCorrectionPreview)->inspect($data, 'ui.type', 'ui.controls.type');
    expect($result['status'])->toBe($relation === null ? 'no_snapshot_conflict' : 'conflict')
        ->and($result['conflicts'])->toBe($relation === null ? [] : [['key' => $existing, 'relation' => $relation]]);
})->with([
    ['ui.controls.type', 'occupied'], ['ui.controls', 'scalar_parent'],
    ['ui.controls.type.caption', 'array_parent'], ['ui.controls.types', null],
]);

it('includes regional evidence outside the preview chain in the proposed impact', function () {
    $data = correctionSnapshot();
    $data['items']['ui.type']['locales']['de-ch'] = ['published' => ['state' => 'blocked_structure', 'value' => '']];
    $data['items']['ui.type']['locales']['de-li'] = ['published' => null];
    $data['items']['regional.existing.key'] = ['locales' => ['de-ch' => ['published' => ['state' => 'unassigned_key', 'value' => 'Regional']]]];
    $preview = new BaselineKeyCorrectionPreview;
    expect($preview->inspect($data, 'ui.type', 'ui.type.type')['locales'])->toBe(['en', 'de-ch'])
        ->and($preview->inspect($data, 'ui.type', 'regional.existing.key')['status'])->toBe('conflict');
});
