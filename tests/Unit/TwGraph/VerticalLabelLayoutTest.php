<?php

use Gunreip\TranslationWorkbench\Support\TwGraph\DataDriven\TimelineChainGraphData\VerticalLabelLayout;
use Gunreip\TranslationWorkbench\Support\TwGraph\CalculatedLengths;
use Tests\TestCase;

uses(TestCase::class);

it('extends only the measured trunk stem while preserving labels and compression', function () {
    $preview = ['graph' => ['graph_id' => 'test', 'stem_length' => '4rem'], 'trunk' => [
        'path_count' => 2, 'path_lengths' => [1 => ['component' => 'stem-compressed', 'labels' => ['left' => 'Text']], 2 => '6rem'],
    ]];
    $result = VerticalLabelLayout::apply($preview, ['trunk.path_lengths.1' => 2, 'trunk.path_lengths.2' => -3]);
    expect($result['trunk']['path_lengths'][1])->toBe(['component' => 'stem-compressed', 'labels' => ['left' => 'Text'], 'length' => '6rem'])
        ->and($result['trunk']['path_lengths'][2])->toBe('6rem')
        ->and($preview['trunk']['path_lengths'][1])->not->toHaveKey('length');
    $lengths = new CalculatedLengths;
    VerticalLabelLayout::recordLengths($result, ['trunk.path_lengths.1' => 2], $lengths);
    expect($lengths->get('test.strang.trunk.1.paths.trunk.path.1.stem.after'))->toMatchArray([
        'kind' => 'calculated', 'default' => '1rem', 'inputs' => ['additional' => '0.5rem'],
    ]);
});


it('accepts a measured trunk correction once and ignores stale passes', function () {
    $component = new \Gunreip\TranslationWorkbench\Livewire\TwGraphDataDrivenDatasets;
    $component->timelineChainId = 10;
    $component->reloadTick = 2;
    $component->extendMeasuredLabelLayout(10, 2, 'trunk.path_lengths.3', 1.5, 0);
    $component->extendMeasuredLabelLayout(10, 2, 'trunk.path_lengths.3', 1.5, 0);
    $component->extendMeasuredLabelLayout(10, 1, 'trunk.path_lengths.3', 2, 1);
    expect($component->horizontalLabelLayout['increments'])->toBe(['trunk.path_lengths.3' => 1.5])
        ->and($component->horizontalLabelLayout['passes'])->toBe(1);
});


it('maps only explicit trunk attachments to the separating stem order', function () {
    $preview = ['graph' => ['graph_id' => 'graph'], 'merges' => [
        ['component' => 'tw-graph.strang.merge-left', 'attach_to' => 'strang.trunk.path.2.end'],
    ], 'branches' => [
        ['id' => 'branch', 'component' => 'tw-graph.strang.branch-right', 'attach_to' => 'strang.trunk.path.7.end'],
        ['component' => 'tw-graph.strang.branch-left', 'attach_to' => 'other.end'],
    ]];
    expect(VerticalLabelLayout::lineAttachments($preview))->toBe([
        ['prefix' => 'graph.strang.merge-left.1', 'row' => 3], ['prefix' => 'branch', 'row' => 8],
    ]);
});

it('extends side-strang public props while preserving continuation content and defaults', function () {
    $preview = ['graph' => ['graph_id' => 'graph', 'stem_length' => '4rem', 'line_length' => '4rem', 'arc_radius' => '2.75rem'],
        'trunk' => ['path_count' => 0],
        'branches' => [['component' => 'tw-graph.strang.branch-left', 'side' => 'left', 'step' => ['stepLabel' => ['text' => 'Action']],
            'stem_continuation' => [1 => ['left' => 'First'], 2 => ['compressed' => true, 'right' => 'Second']]]],
        'rekeys' => [['component' => 'tw-graph.strang.rekey-target-right', 'kind' => 'target', 'side' => 'right', 'stem_continuation' => [1 => ['right' => 'Value']]]],
        'merges' => [['component' => 'tw-graph.strang.merge-left', 'side' => 'left', 'extension_count' => 1, 'extension_stem_continuations' => [1 => [1 => ['left' => 'Origin']]]]],
    ];
    $result = VerticalLabelLayout::apply($preview, ['branches.0.step.afterLength' => 1, 'branches.0.stem_continuation.2' => 2,
        'rekeys.0.stem_continuation.1' => 3, 'merges.0.extension_stem_continuations.1.1' => 2]);
    expect($result['branches'][0]['step'])->toBe(['stepLabel' => ['text' => 'Action'], 'afterLength' => '3.5rem'])
        ->and($result['branches'][0]['stem_continuation'][2])->toBe(['compressed' => true, 'right' => 'Second', 'length' => '6rem'])
        ->and($result['rekeys'][0]['stem_continuation'][1])->toBe(['right' => 'Value', 'length' => '7rem'])
        ->and($result['merges'][0]['extension_stem_continuations'][1][1])->toBe(['left' => 'Origin', 'length' => '6rem'])
        ->and($preview['branches'][0]['step'])->not->toHaveKey('afterLength');
});

it('assigns separate branch ends to their branch attachment on either side', function () {
    foreach (['left', 'right'] as $side) {
        $preview = ['graph' => ['graph_id' => 'graph'], 'branches' => [[
            'id' => 'branch.'.$side,
            'component' => 'tw-graph.strang.branch-'.$side,
            'side' => $side,
            'component_counter' => 3,
            'attach_to' => 'strang.trunk.path.2.end',
        ]]];

        expect(VerticalLabelLayout::lineAttachments($preview))->toBe([
            ['prefix' => 'branch.'.$side, 'row' => 3],
            ['prefix' => 'graph.strang.branch-end.'.$side.'.3', 'row' => 3],
        ]);
    }
});

it('rechecks only line clearance once per horizontal placement and preserves label spacing', function () {
    $component = new \Gunreip\TranslationWorkbench\Livewire\TwGraphDataDrivenDatasets;
    $component->timelineChainId = 10;
    $component->reloadTick = 2;
    $component->extendMeasuredLabelLayout(10, 2, 'trunk.path_lengths.2', 1, 0, 'label');
    $component->extendMeasuredLabelLayout(10, 2, 'trunk.path_lengths.2', 3, 1, 'line');
    $component->extendMeasuredLabelLayout(10, 2, 'branches.0.bridge_length', 5, 2, 'line');
    $before = $component->horizontalLabelLayout;
    $component->recheckMeasuredTrunkClearance(11, 2, 3);
    $component->recheckMeasuredTrunkClearance(10, 1, 3);
    $component->recheckMeasuredTrunkClearance(10, 2, 2);
    expect($component->horizontalLabelLayout)->toBe($before);
    $component->recheckMeasuredTrunkClearance(10, 2, 3);
    expect($component->horizontalLabelLayout['increments'])->toBe([
        'trunk.path_lengths.2' => 1.0, 'branches.0.bridge_length' => 5.0,
    ])->and($component->horizontalLabelLayout['passes'])->toBe(4);
    // A still-needed correction must survive: do not repeatedly remove and restore it.
    $component->extendMeasuredLabelLayout(10, 2, 'trunk.path_lengths.2', 2, 4, 'line');
    $component->recheckMeasuredTrunkClearance(10, 2, 5);
    expect($component->horizontalLabelLayout['increments']['trunk.path_lengths.2'])->toBe(3.0)
        ->and($component->horizontalLabelLayout['passes'])->toBe(5);
    $component->extendMeasuredLabelLayout(10, 2, 'branches.0.bridge_length', 1, 5, 'horizontal');
    $component->recheckMeasuredTrunkClearance(10, 2, 6);
    expect($component->horizontalLabelLayout['increments']['trunk.path_lengths.2'])->toBe(1.0);
});

it('applies measured entry clearance through the existing branch prop', function () {
    $preview = ['graph' => ['graph_id' => 'graph', 'stem_length' => '4rem', 'arc_radius' => '2.75rem'],
        'trunk' => ['path_count' => 0],
        'branches' => [['component' => 'tw-graph.strang.branch-left', 'side' => 'left']]];
    $result = \Gunreip\TranslationWorkbench\Support\TwGraph\DataDriven\TimelineChainGraphData\VerticalLabelLayout::apply(
        $preview, ['branches.0.entry_stem_length' => 0.2969]);
    expect($result['branches'][0]['entry_stem_length'])->toBe('0.2969rem')
        ->and($result['trunk'])->toBe($preview['trunk']);
});

it('resolves branch attachments through the rendered merge end on either side', function () {
    $preview = ['graph' => ['graph_id' => 'graph'], 'merges' => [
        ['id' => 'merge.left', 'side' => 'left', 'attach_to' => 'strang.trunk.path.1.end'],
        ['id' => 'merge.right', 'side' => 'right', 'attach_to' => 'strang.trunk.path.3.end'],
    ], 'branches' => [
        ['id' => 'branch.left', 'side' => 'left', 'attach_to' => 'strang.merge-left.end'],
        ['id' => 'branch.right', 'side' => 'right', 'attach_to' => 'strang.merge-right.end'],
    ]];
    $attachments = collect(VerticalLabelLayout::lineAttachments($preview))->keyBy('prefix');
    expect($attachments['branch.left']['row'])->toBe(2)
        ->and($attachments['branch.right']['row'])->toBe(4)
        ->and($attachments['graph.strang.branch-end.left.1']['row'])->toBe(2)
        ->and($attachments['graph.strang.branch-end.right.2']['row'])->toBe(4);

    $preview['merges'][] = ['id' => 'merge.left.last', 'side' => 'left', 'attach_to' => 'strang.trunk.path.5.end'];
    $attachments = collect(VerticalLabelLayout::lineAttachments($preview))->keyBy('prefix');
    expect($attachments['branch.left']['row'])->toBe(6);
});
