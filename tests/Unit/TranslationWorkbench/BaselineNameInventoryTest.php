<?php

use Gunreip\TranslationWorkbench\Support\BaselineKeyCorrectionPreview;
use Gunreip\TranslationWorkbench\Support\BaselineNameInventory;

it('fingerprints a complete inventory independently of row and object key order', function () {
    $service = new BaselineNameInventory;
    $first = ['namespace' => '', 'full_name' => 'ui.button.save'];
    $second = ['namespace' => 'package', 'full_name' => 'ui.button.cancel'];
    $expected = $service->create([$first, $second]);
    expect($service->create([array_reverse($second, true), array_reverse($first, true)]))->toBe($expected);
    $service->validate($expected);
    expect($expected['count'])->toBe(2);
});

it('rejects missing duplicated or damaged inventory evidence', function () {
    $service = new BaselineNameInventory;
    $row = ['namespace' => '', 'full_name' => 'ui.button.save'];
    expect(fn () => $service->create([]))->toThrow(RuntimeException::class)
        ->and(fn () => $service->create([$row, $row]))->toThrow(RuntimeException::class)
        ->and(fn () => $service->create([['full_name' => 'ui.button.save']]))->toThrow(RuntimeException::class);
    $inventory = $service->create([$row]);
    expect(fn () => $service->validate([...$inventory, 'count' => 2]))->toThrow(RuntimeException::class)
        ->and(fn () => $service->validate([...$inventory, 'sha256' => str_repeat('0', 64)]))->toThrow(RuntimeException::class);
});

it('protects partial snapshots against external names without importing those keys', function (string $name, string $namespace, ?string $relation) {
    $snapshot = ['selection' => ['scope' => 'one_key'], 'items' => [
        'ui.type' => ['locales' => ['en' => ['published' => ['state' => 'blocked_structure', 'value' => 'Type']]],
            'blocked_sources' => [['finding_id' => 1]]],
    ], 'name_inventory' => (new BaselineNameInventory)->create([['namespace' => $namespace, 'full_name' => $name]])];
    $result = (new BaselineKeyCorrectionPreview)->inspect($snapshot, 'ui.type', 'ui.controls.type');
    expect($result['status'])->toBe($relation ? 'conflict' : 'no_snapshot_conflict')
        ->and($result['conflicts'])->toBe($relation ? [['key' => $name, 'relation' => $relation]] : [])
        ->and(array_keys($snapshot['items']))->toBe(['ui.type']);
})->with([
    ['ui.controls.type', '', 'occupied'],
    ['ui.controls', '', 'scalar_parent'],
    ['ui.controls.type.caption', '', 'array_parent'],
    ['ui.controls.types', '', null],
    ['ui.controls.type', 'package', null],
]);

it('does not interpret a missing partial inventory as an empty namespace', function () {
    $snapshot = ['selection' => [], 'items' => [
        'ui.type' => ['locales' => ['en' => ['published' => ['state' => 'blocked_structure']]], 'blocked_sources' => []],
    ]];
    expect((new BaselineKeyCorrectionPreview)->inspect($snapshot, 'ui.type', 'ui.controls.type')['status'])->toBe('name_inventory_unavailable');
});
