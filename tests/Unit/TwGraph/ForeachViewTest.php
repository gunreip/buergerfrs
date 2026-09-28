<?php

use Gunreip\TranslationWorkbench\Livewire\TwGraphDocumentation;
use Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry;
use Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry;
use Livewire\Livewire;
use Tests\TestCase;

uses(TestCase::class);

it('renders handmade mirrored FOREACH examples with independent iteration returns', function (string $variant, int $count) {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.foreach.flow-foreach-'.$variant;
    $source = file_get_contents(app('view')->getFinder()->find($view));
    expect($source)->not->toContain('@foreach', '@include', 'literature.for.', 'index =', 'Index =');
    $html = view($view)->render();
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    expect($xpath->query('//pre/code')->length)->toBe(8);
    $points = [];
    foreach (['left', 'right'] as $side) {
        $graphId = 'idea-to-paper-foreach-'.$variant.'-'.$side;
        $root = 'literature.foreach.'.$variant.'.'.$side.'.';
        $point = function (string $suffix) use ($graphId, $root): array {
            $anchor = AnchorRegistry::get($graphId, $root.$suffix);
            expect($anchor)->not->toBeNull($suffix);

            return array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($anchor[$axis]), ['x', 'y']);
        };
        $condition = $variant === 'nested' ? 'loop.condition' : 'condition';
        $return = $variant === 'nested' ? 'body-return' : 'loop.return';
        expect($point('loop.anchorNode-start'))->toBe($point($condition.'.anchorNode-end'));
        expect($point($return.'.anchorNode-end'))->toBe($point('iterator.anchorNode-end'));
        expect($point($return.'.anchorNode-end'))->not->toBe($point('initialize.anchorNode-end'));
        expect($point('continue.anchorNode-end')[1])->toBeGreaterThan($point('loop.anchorNode-end')[1]);
        if ($variant === 'nested') {
            expect($point('inner-entry.anchorNode-start'))->toBe($point('loop.body.anchorNode-end'));
            expect($point('inner-entry.anchorNode-end'))->toBe($point('inner-loop.anchorNode-return'));
            expect($point('inner-loop.anchorNode-start'))->toBe($point('inner-loop.condition.anchorNode-end'));
            expect($point('inner-return.anchorNode-start'))->toBe($point('record-item.anchorNode-end'));
            expect($point('inner-return.anchorNode-end'))->toBe($point('inner-loop.anchorNode-return'));
            expect($point('inner-return.anchorNode-end'))->not->toBe($point($return.'.anchorNode-end'));
            expect($point('finalize-group.anchorNode-end')[1])->toBeGreaterThan($point('inner-loop.anchorNode-end')[1]);
        } else {
            expect($point($return.'.anchorNode-start'))->toBe($point('loop.body.anchorNode-end'));
        }
        $points[$side] = $point('loop.body.anchorNode-end');
        $graph = $xpath->query('//*[@id="'.$graphId.'"]')->item(0);
        expect($graph)->not->toBeNull();
        expect($graph->textContent)->toContain('FOREACH next', 'Show summary');
        expect($graph->textContent)->not->toContain('=>', '{--', 'index =');
        $numbers = [];
        foreach ($xpath->query('.//*[contains(concat(" ", normalize-space(@class), " "), " tw-graph-protocol-primitive-dev-node-counter ")]', $graph) as $counter) {
            $numbers[] = (int) trim($counter->textContent);
        }
        sort($numbers);
        expect($numbers)->toBe(range(1, $count));
    }
    expect($points['right'])->toBe([-$points['left'][0], $points['left'][1]]);
})->with([['collection', 11], ['key-value', 11], ['nested', 29]]);

it('loads FOREACH lazily and restores its documentation reference', function (string $variant) {
    Livewire::test(TwGraphDocumentation::class)
        ->assertDontSee('id="idea-to-paper-foreach-'.$variant.'-left"', false)
        ->call('openExample', 'flow.foreach.flow-foreach-'.$variant)
        ->assertSet('tabs.flow_index', 'flow-foreach')
        ->assertSet('tabs.flow_foreach', 'flow-foreach-'.$variant)
        ->assertSee('id="idea-to-paper-foreach-'.$variant.'-left"', false)
        ->assertSee('id="idea-to-paper-foreach-'.$variant.'-right"', false)
        ->assertDontSee('id="idea-to-paper-for-test"', false)
        ->call('openReference', 'strang.flow-step', 'flow.foreach.flow-foreach-'.$variant)
        ->assertDontSee('id="idea-to-paper-foreach-'.$variant.'-left"', false)
        ->call('returnToExample')
        ->assertSet('tabs.flow_foreach', 'flow-foreach-'.$variant)
        ->assertSee('id="idea-to-paper-foreach-'.$variant.'-right"', false);
})->with(['collection', 'key-value', 'nested']);
