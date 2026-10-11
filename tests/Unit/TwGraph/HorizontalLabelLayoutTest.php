<?php

use Gunreip\TranslationWorkbench\Support\TwGraph\DataDriven\TimelineChainGraphData\HorizontalLabelLayout;
use Gunreip\TranslationWorkbench\Livewire\TwGraphDataDrivenDatasets;
use Tests\TestCase;

uses(TestCase::class);

it('adds measured horizontal clearance to defaults without changing stems or unrelated props', function () {
    $preview = ['graph' => ['graph_id' => 'graph', 'bridge_length' => '20rem'],
        'merges' => [['component' => 'tw-graph.strang.merge-left', 'side' => 'left', 'stem_length' => '5.75rem',
            'extension_count' => 1, 'extension_bridge_length' => '20rem']]];
    $result = HorizontalLabelLayout::apply($preview, ['merges.0.bridge_length' => 3, 'merges.0.extension_bridge_continuations.1' => 7,
        'merges.0.stem_length' => 100]);
    expect($result['merges'][0]['bridge_length'])->toBe('23rem')
        ->and($result['merges'][0]['extension_bridge_continuations'][1])->toBe('27rem')
        ->and($result['merges'][0]['stem_length'])->toBe('5.75rem')
        ->and($preview['merges'][0])->not->toHaveKey('bridge_length');
});

it('ignores stale browser measurements after dataset selection or refresh', function () {
    $component = new TwGraphDataDrivenDatasets;
    $component->timelineChainId = 10;
    $component->reloadTick = 3;
    $component->extendMeasuredLabelLayout(11, 3, 'merges.0.bridge_length', 4, 0);
    $component->extendMeasuredLabelLayout(10, 2, 'merges.0.bridge_length', 4, 0);
    expect($component->horizontalLabelLayout)->toBe([]);
    $component->extendMeasuredLabelLayout(10, 3, 'merges.0.bridge_length', 4, 0);
    $component->extendMeasuredLabelLayout(10, 3, 'merges.0.bridge_length', 4, 0); // Duplicate measurement must not be applied twice.
    expect($component->horizontalLabelLayout['increments'])->toBe(['merges.0.bridge_length' => 4.0]);
});

it('inherits shared stem defaults and reserves three rem horizontal clearance', function () {
    $defaults = require base_path('packages/gunreip/laravel-translation-workbench/config/defaults/tw-graph-data-driven.php');
    config()->set('tw-graph-data-driven-defaults', $defaults);
    config()->set('tw-graph-defaults.stem_length', '4rem');
    expect($defaults)->not->toHaveKey('stem_length');
    expect(\Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::dataDrivenString('stem_length', '4rem'))->toBe('4rem')
        ->and($defaults['merge_layout']['vertical_stagger_enabled'])->toBeFalse()
        ->and($defaults['horizontal_label_gap'])->toBe('3rem')
        ->and($defaults['line_label_gap'])->toBe('1rem')
        ->and($defaults['horizontal_line_label_gap'])->toBe('3rem')
        ->and($defaults['vertical_label_gap'])->toBe('1rem');
    config()->set('tw-graph-defaults.stem_length', '6rem');
    expect(\Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::dataDrivenString('stem_length', '4rem'))->toBe('6rem');
});

it('registers rekey source and target terminal geometry with their movable owner', function () {
    foreach (['left', 'right'] as $side) {
        $preview = ['graph' => ['graph_id' => 'graph', 'bridge_length' => '4rem'], 'rekeys' => [
            ['id' => 'source', 'component' => 'tw-graph.strang.rekey-source-'.$side, 'side' => $side, 'kind' => 'source'],
            ['id' => 'target', 'component' => 'tw-graph.strang.rekey-target-'.$side, 'side' => $side, 'kind' => 'target'],
        ]];
        $targets = HorizontalLabelLayout::targets($preview);
        expect($targets[0]['terminals'])->toBe(['source.paths.rekey-source.start'])
            ->and($targets[1]['terminals'])->toBe(['target.path.rekey-target.end']);
    }
});

it('keeps the first unlabelled extension tail available after measured length adjustments', function () {
    foreach ([[], ['length' => '6rem']] as $entry) {
        $preview = ['graph' => ['graph_id' => 'graph', 'bridge_length' => '20rem'], 'merges' => [[
            'component' => 'tw-graph.strang.merge-left', 'side' => 'left', 'extension_count' => 2,
            'extension_stem_continuations' => [1 => [1 => $entry], 2 => [1 => []]],
        ]]];
        $targets = HorizontalLabelLayout::targets($preview);
        expect($targets[1]['compactStem'])->toBe('merges.0.extension_stem_continuations.1.1')
            ->and($targets[2]['compactStem'])->toBeNull();
        $preview['merges'][0]['extension_stem_continuations'][1][1]['left'] = 'Label';
        expect(HorizontalLabelLayout::targets($preview)[1]['compactStem'])->toBeNull();
    }
});

it('reduces only measured main merge extras and rejects stale measurements', function () {
    $component = new TwGraphDataDrivenDatasets;
    $component->timelineChainId = 10;
    $component->reloadTick = 2;
    $component->extendMeasuredLabelLayout(10, 2, 'merges.0.bridge_length', 10, 0, 'horizontal');
    $component->extendMeasuredLabelLayout(10, 2, 'merges.0.extension_bridge_continuations.1', 3, 1, 'horizontal');
    $before = $component->horizontalLabelLayout;
    $component->reduceMeasuredMergeBridges(11, 2, ['merges.0.bridge_length' => 4], 2);
    $component->reduceMeasuredMergeBridges(10, 1, ['merges.0.bridge_length' => 4], 2);
    expect($component->horizontalLabelLayout)->toBe($before);
    $component->reduceMeasuredMergeBridges(10, 2, ['merges.0.bridge_length' => 4], 2);
    expect($component->horizontalLabelLayout['increments'])->toBe([
        'merges.0.bridge_length' => 6.0, 'merges.0.extension_bridge_continuations.1' => 3.0,
    ])->and($component->horizontalLabelLayout['compactedBridges'])->toBe(['merges.0.bridge_length' => true]);
    $component->reduceMeasuredMergeBridges(10, 2, ['merges.0.bridge_length' => 4], 2);
    expect($component->horizontalLabelLayout['passes'])->toBe(3);
});

it('never reduces authored lengths or extension bridges through main bridge compaction', function () {
    $component = new TwGraphDataDrivenDatasets;
    $component->timelineChainId = 10;
    $component->extendMeasuredLabelLayout(10, 0, 'merges.0.bridge_length', 2, 0);
    $state = $component->horizontalLabelLayout;
    foreach ([['merges.0.bridge_length' => 3], ['merges.0.bridge_length' => -1], ['merges.0.extension_bridge_continuations.1' => 1]] as $reductions) {
        expect(fn () => $component->reduceMeasuredMergeBridges(10, 0, $reductions, 1))
            ->toThrow(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        expect($component->horizontalLabelLayout)->toBe($state);
    }
});



it('applies coupled merge changes atomically and validates both axes before mutation', function () {
    $component = new TwGraphDataDrivenDatasets;
    $component->timelineChainId = 10;
    $component->extendMeasuredLabelLayout(10, 0, 'merges.0.bridge_length', 10, 0);
    $candidate = ['bridge' => 'merges.0.bridge_length', 'stem' => 'merges.0.stem_continuation.1', 'reduction' => 8, 'addition' => 3, 'extensions' => [['stem' => 'merges.0.extension_stem_continuations.1.1', 'addition' => 3]]];
    $before = $component->horizontalLabelLayout;
    expect(fn () => $component->compactMeasuredMerge(10, 0, array_replace($candidate, ['stem' => 'merges.1.stem_continuation.1']), 1))->toThrow(\Symfony\Component\HttpKernel\Exception\HttpException::class);
    expect($component->horizontalLabelLayout)->toBe($before);
    $component->compactMeasuredMerge(10, 0, $candidate, 0);
    expect($component->horizontalLabelLayout)->toBe($before);
    $component->compactMeasuredMerge(10, 0, $candidate, 1);
    expect($component->horizontalLabelLayout['increments'])->toBe(['merges.0.bridge_length' => 2.0, 'merges.0.stem_continuation.1' => 3.0, 'merges.0.extension_stem_continuations.1.1' => 3.0])
        ->and($component->horizontalLabelLayout['passes'])->toBe(2);
});

it('offers coupled compaction only through the existing unlabelled final continuation', function () {
    $preview = ['graph' => ['graph_id' => 'graph', 'bridge_length' => '20rem'], 'merges' => [[
        'component' => 'tw-graph.strang.merge-left', 'side' => 'left', 'stem_continuation' => [1 => []],
    ]]];
    expect(HorizontalLabelLayout::targets($preview)[0]['coupledStem'])->toBe([
        'key' => 'merges.0.stem_continuation.1', 'element' => 'graph.strang.merge-left.1.paths.merge.stem2',
    ]);
    $preview['merges'][0]['stem_continuation'][1]['left'] = 'Label';
    expect(HorizontalLabelLayout::targets($preview)[0]['coupledStem'])->toBeNull();
});

it('applies one extension optimization batch without touching earlier lanes or restarting collision passes', function () {
    $component = new TwGraphDataDrivenDatasets;
    $component->timelineChainId = 10;
    $component->horizontalLabelLayout = ['scope' => '10:0', 'passes' => 40, 'increments' => [
        'merges.0.bridge_length' => 18.0, 'merges.0.extension_bridge_continuations.1' => 10.0,
        'merges.0.extension_bridge_continuations.2' => 10.0, 'merges.0.extension_bridge_continuations.3' => 14.0,
    ]];
    $before = $component->horizontalLabelLayout;
    $candidate = ['bridge' => 'merges.0.extension_bridge_continuations.3', 'reduction' => 13.9,
        'stem' => 'merges.0.extension_stem_continuations.3.11', 'addition' => 14.6];
    expect(fn () => $component->compactMeasuredExtensions(10, 0, [$candidate, $candidate], 40))
        ->toThrow(\Symfony\Component\HttpKernel\Exception\HttpException::class);
    expect($component->horizontalLabelLayout)->toBe($before);
    $component->compactMeasuredExtensions(10, 0, [$candidate], 39);
    expect($component->horizontalLabelLayout)->toBe($before);
    $component->compactMeasuredExtensions(10, 0, [$candidate], 40);
    expect($component->horizontalLabelLayout['increments']['merges.0.bridge_length'])->toBe(18.0)
        ->and($component->horizontalLabelLayout['increments']['merges.0.extension_bridge_continuations.1'])->toBe(10.0)
        ->and($component->horizontalLabelLayout['increments']['merges.0.extension_bridge_continuations.2'])->toBe(10.0)
        ->and($component->horizontalLabelLayout['increments']['merges.0.extension_stem_continuations.3.11'])->toBe(14.6)
        ->and($component->horizontalLabelLayout['passes'])->toBe(40);
    $after = $component->horizontalLabelLayout;
    $component->compactMeasuredExtensions(10, 0, [$candidate], 40);
    expect($component->horizontalLabelLayout)->toBe($after);
});

it('exposes only the enabled extension and its existing unlabelled tail for compaction', function () {
    config()->set('tw-graph-data-driven-defaults.merge_extension_compaction_indices', [3]);
    $preview = ['graph' => ['graph_id' => 'graph', 'bridge_length' => '20rem'], 'merges' => [[
        'component' => 'tw-graph.strang.merge-left', 'side' => 'left', 'extension_count' => 4,
        'extension_stem_continuations' => [1 => [1 => []], 2 => [1 => []], 3 => [1 => []], 4 => [1 => []]],
    ]]];
    $targets = HorizontalLabelLayout::targets($preview);
    expect($targets[1]['extensionCompactionStem'])->toBeNull()
        ->and($targets[2]['extensionCompactionStem'])->toBeNull()
        ->and($targets[4]['extensionCompactionStem'])->toBeNull()
        ->and($targets[3]['extensionCompactionStem'])->toBe([
            'key' => 'merges.0.extension_stem_continuations.3.1',
            'element' => 'graph.strang.merge-left.1.extension.3.paths.merge-extension.stem2',
        ]);
    $preview['merges'][0]['extension_stem_continuations'][3][1]['left'] = 'Existing label';
    expect(HorizontalLabelLayout::targets($preview)[3]['extensionCompactionStem'])->toBeNull();
});

it('enables extension four through the same tail metadata on both sides', function () {
    foreach (['left', 'right'] as $side) {
        $preview = ['graph' => ['graph_id' => 'graph', 'bridge_length' => '20rem'], 'merges' => [[
            'component' => "tw-graph.strang.merge-{$side}", 'side' => $side, 'extension_count' => 5,
            'extension_stem_continuations' => [3 => [1 => []], 4 => [1 => []], 5 => [1 => []]],
        ]]];
        $targets = HorizontalLabelLayout::targets($preview);
        expect($targets[4]['extensionCompactionStem'])->toBe([
            'key' => 'merges.0.extension_stem_continuations.4.1',
            'element' => "graph.strang.merge-{$side}.1.extension.4.paths.merge-extension.stem2",
        ])->and($targets[5]['extensionCompactionStem'])->toBeNull();
    }
});

it('compacts extension four without changing the completed earlier extension increments', function () {
    $component = new TwGraphDataDrivenDatasets;
    $component->timelineChainId = 10;
    $earlier = ['merges.0.bridge_length' => 18.0,
        'merges.0.extension_bridge_continuations.3' => 0.0157,
        'merges.0.extension_stem_continuations.3.11' => 14.5938];
    $component->horizontalLabelLayout = ['scope' => '10:0', 'passes' => 40,
        'increments' => $earlier + ['merges.0.extension_bridge_continuations.4' => 12.0]];
    $component->compactMeasuredExtensions(10, 0, [[
        'bridge' => 'merges.0.extension_bridge_continuations.4', 'reduction' => 8,
        'stem' => 'merges.0.extension_stem_continuations.4.1', 'addition' => 0,
    ]], 40);
    expect(array_intersect_key($component->horizontalLabelLayout['increments'], $earlier))->toBe($earlier)
        ->and($component->horizontalLabelLayout['increments']['merges.0.extension_bridge_continuations.4'])->toBe(4.0)
        ->and($component->horizontalLabelLayout['increments'])->not->toHaveKey('merges.0.extension_stem_continuations.4.1');
});

it('finishes a verified main merge compaction at the collision pass limit only once', function () {
    $component = new TwGraphDataDrivenDatasets;
    $component->timelineChainId = 10;
    $component->horizontalLabelLayout = ['scope' => '10:0', 'passes' => 40,
        'increments' => ['merges.1.bridge_length' => 12.0]];
    $candidate = ['bridge' => 'merges.1.bridge_length', 'stem' => 'merges.1.stem_continuation.1',
        'reduction' => 8, 'addition' => 3];
    $component->compactMeasuredMerge(10, 0, $candidate, 40);
    expect($component->horizontalLabelLayout['increments']['merges.1.bridge_length'])->toBe(4.0)
        ->and($component->horizontalLabelLayout['passes'])->toBe(40)
        ->and($component->horizontalLabelLayout['compactionRevision'])->toBe(1);
    $after = $component->horizontalLabelLayout;
    $component->compactMeasuredMerge(10, 0, $candidate, 40);
    expect($component->horizontalLabelLayout)->toBe($after);
});

it('bounds correction work by distinct affected props rather than repeated attempts', function () {
    expect(TwGraphDataDrivenDatasets::measuredLayoutPassLimit([]))->toBe(40);
    $increments = [];
    for ($i = 1; $i <= 15; $i++) $increments["trunk.path_lengths.$i"] = 1;
    expect(TwGraphDataDrivenDatasets::measuredLayoutPassLimit(['increments' => $increments]))->toBe(60);
    $increments['trunk.path_lengths.1'] += 100;
    expect(TwGraphDataDrivenDatasets::measuredLayoutPassLimit(['increments' => $increments]))->toBe(60);
});
