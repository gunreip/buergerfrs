<?php

use Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry;

it('stores anchors with coordinates and allowed metadata', function (): void {
    AnchorRegistry::forgetGraph('tw-graph-test');

    AnchorRegistry::put('tw-graph-test', 'trunk.center.1.stem-2.anchorNode-end', [
        'x' => 0,
        'y' => '12rem',
        'source' => 'trunk.center.1.stem-2',
        'sourceType' => 'stem',
        'sourceAnchor' => 'anchorEnd',
        'direction' => 'bottom-top',
        'devCounterNext' => 3,
        'ignored' => 'not stored',
    ]);

    expect(AnchorRegistry::get('tw-graph-test', 'trunk.center.1.stem-2.anchorNode-end'))->toBe([
        'x' => '0',
        'y' => '12rem',
        'source' => 'trunk.center.1.stem-2',
        'sourceType' => 'stem',
        'sourceAnchor' => 'anchorEnd',
        'direction' => 'bottom-top',
        'devCounterNext' => '3',
    ]);
});

it('resolves normalized aliases for registered anchor keys', function (): void {
    AnchorRegistry::forgetGraph('tw-graph-test');

    AnchorRegistry::put('tw-graph-test', 'strang.merge-left.1.main.path.merge.bridge1', [
        'x' => '-12rem',
        'y' => '8rem',
    ]);

    expect(AnchorRegistry::get('tw-graph-test', 'strang.left.1.merge.bridge1'))->toBe([
        'x' => '-12rem',
        'y' => '8rem',
    ]);
});

it('forgets all anchors for one graph without touching another graph', function (): void {
    AnchorRegistry::forgetGraph('tw-graph-a');
    AnchorRegistry::forgetGraph('tw-graph-b');

    AnchorRegistry::put('tw-graph-a', 'node', ['x' => '1rem', 'y' => '2rem']);
    AnchorRegistry::put('tw-graph-b', 'node', ['x' => '3rem', 'y' => '4rem']);

    AnchorRegistry::forgetGraph('tw-graph-a');

    expect(AnchorRegistry::get('tw-graph-a', 'node'))->toBeNull()
        ->and(AnchorRegistry::get('tw-graph-b', 'node'))->toBe(['x' => '3rem', 'y' => '4rem']);
});

it('stores missing anchor coordinates as zero rem fallbacks', function (): void {
    AnchorRegistry::forgetGraph('tw-graph-test');

    AnchorRegistry::put('tw-graph-test', 'empty-anchor', []);

    expect(AnchorRegistry::get('tw-graph-test', 'empty-anchor'))->toBe([
        'x' => '0rem',
        'y' => '0rem',
    ]);
});
