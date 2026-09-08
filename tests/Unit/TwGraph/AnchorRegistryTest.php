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
