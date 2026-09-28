<?php

use Gunreip\TranslationWorkbench\Livewire\TwGraphDocumentation;
use Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry;
use Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry;
use Livewire\Livewire;
use Tests\TestCase;

uses(TestCase::class);

it('returns after the FOR increment to the condition without repeating initialization', function () {
    $html = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.for.flow-for-test')->render();
    $point = function (string $suffix): array {
        $anchor = AnchorRegistry::get('idea-to-paper-for-test', 'literature.for.1.'.$suffix);
        expect($anchor)->not->toBeNull($suffix);

        return array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($anchor[$axis]), ['x', 'y']);
    };
    expect($point('initialize-index.anchorNode-end')[1])->toBeGreaterThan($point('initialize.anchorNode-end')[1]);
    expect($point('loop.anchorNode-start'))->toBe($point('condition.anchorNode-end'));
    expect($point('inner-switch.anchorNode-start'))->toBe($point('loop.body.anchorNode-end'));
    expect($point('inner-switch.case.draft.return.anchorNode-end'))->toBe($point('inner-switch.case.published.anchorNode-end'));
    expect($point('inner-switch.case.published.return.anchorNode-end'))->toBe($point('inner-switch.case.default.anchorNode-end'));
    expect($point('inner-switch.anchorNode-end'))->toBe($point('inner-switch.case.default.anchorNode-end'));
    expect($point('advance.anchorNode-end')[1])->toBe($point('inner-switch.anchorNode-end')[1] - 8.0);
    expect($point('body-return.anchorNode-start'))->toBe($point('advance.anchorNode-end'));
    expect($point('body-return.anchorNode-end'))->toBe($point('initialize-index.anchorNode-end'));
    expect($point('body-return.anchorNode-end')[1])->toBeLessThan($point('condition.anchorNode-end')[1]);
    expect($point('body-return.anchorNode-end'))->not->toBe($point('initialize.anchorNode-end'));
    expect($point('continue.anchorNode-end')[1])->toBeGreaterThan($point('loop.anchorNode-end')[1]);

    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    expect($xpath->query('//pre/code')->length)->toBe(7);
    $graph = $xpath->query('//*[@id="idea-to-paper-for-test"]')->item(0);
    expect($graph->textContent)->toContain('index = 0', 'FOR index < count?', 'Read current item', 'SWITCH item.status', 'Edit draft', 'Display item', 'Record unknown status', 'index = index + 1', 'Show summary');
    expect($graph->textContent)->not->toContain('=>', '{--');
    $numbers = [];
    foreach ($xpath->query('//*[contains(concat(" ", normalize-space(@class), " "), " tw-graph-protocol-primitive-dev-node-counter ")]') as $counter) {
        $numbers[] = (int) trim($counter->textContent);
    }
    sort($numbers);
    expect($numbers)->toBe(range(1, 19));
});

it('retains FOR Test on disk but falls back to basic for its disabled tab', function () {
    expect(view()->exists('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.for.flow-for-test'))->toBeTrue();
    Livewire::test(TwGraphDocumentation::class)
        ->call('openExample', 'flow.for.flow-for-basic')
        ->set('tabs.flow_for', 'flow-for-test')
        ->assertSet('tabs.flow_for', 'flow-for-basic')
        ->assertSee('id="idea-to-paper-for-basic-left"', false)
        ->assertDontSee('id="idea-to-paper-for-test"', false)
        ->assertDontSee('FOR Test')
        ->set('tabs.flow_for', 'unknown-for-tab')
        ->assertSet('tabs.flow_for', 'flow-for-basic');
});

it('renders independent saved FOR examples with mirrored bodies and returns', function (string $variant, array $labels) {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.for.flow-for-'.$variant;
    $source = file_get_contents(app('view')->getFinder()->find($view));
    expect($source)->not->toContain('@foreach', '@include', 'literature.for.1.');
    $html = view($view)->render();
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    expect($xpath->query('//pre/code')->length)->toBe(8);
    $offsets = [];
    foreach (['left', 'right'] as $side) {
        $graphId = 'idea-to-paper-for-'.$variant.'-'.$side;
        $prefix = 'literature.for.'.($variant === 'multiple-actions' ? 'multiple' : $variant).'.'.$side.'.';
        $point = function ($suffix) use ($graphId, $prefix) {
            $anchor = AnchorRegistry::get($graphId, $prefix.$suffix);
            expect($anchor)->not->toBeNull($suffix);

            return array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($anchor[$axis]), ['x', 'y']);
        };
        expect($point('body-return.anchorNode-end'))->toBe($point('initialize-index.anchorNode-end'));
        expect($point('body-return.anchorNode-start'))->toBe($point('advance.anchorNode-end'));
        if ($variant === 'if') {
            expect($point('inner-if.true.anchorNode-return'))->toBe($point('inner-if.anchorNode-end'));
            expect($point('inner-if.false.anchorNode-return'))->toBe($point('inner-if.anchorNode-end'));
            expect($point('advance.anchorNode-end')[1])->toBe($point('inner-if.anchorNode-end')[1] - 8.0);
        }
        $offsets[$side] = $point('loop.body.anchorNode-end')[0] - $point('loop.anchorNode-start')[0];
        $graph = $xpath->query('//*[@id="'.$graphId.'"]')->item(0);
        expect($graph->textContent)->toContain(...$labels);
        expect($graph->textContent)->not->toContain('=>', '{--');
        $numbers = [];
        foreach ($xpath->query('.//*[contains(concat(" ", normalize-space(@class), " "), " tw-graph-protocol-primitive-dev-node-counter ")]', $graph) as $counter) {
            $numbers[] = (int) trim($counter->textContent);
        }
        sort($numbers);
        expect($numbers)->toBe(range(1, match ($variant) { 'multiple-actions' => 13, 'if' => 16, 'switch' => 19, default => 12 }));
    }
    expect($offsets['left'])->toBeLessThan(0);
    expect($offsets['right'])->toBe(-$offsets['left']);
})->with([
    ['basic', ['index = 0', 'FOR index < count?', 'index = index + 1']],
    ['descending', ['index = count - 1', 'FOR index >= 0?', 'index = index - 2']],
    ['multiple-actions', ['index = 0', 'FOR index < count?', 'Process items[index]', 'Record result for items[index]', 'index = index + 1']],
    ['switch', ['index = 0', 'SWITCH item.status', 'Edit draft', 'Display item', 'Record unknown status']],
    ['if', ['index = 0', 'FOR index < count?', 'IF item enabled?', 'Process item', 'Record skipped item']],
]);

it('keeps saved FOR examples independent during navigation', function () {
    Livewire::test(TwGraphDocumentation::class)
        ->call('openExample', 'flow.for.flow-for-basic')
        ->assertSet('tabs.flow_for', 'flow-for-basic')
        ->assertSee('id="idea-to-paper-for-basic-left"', false)
        ->assertSee('id="idea-to-paper-for-basic-right"', false)
        ->assertDontSee('id="idea-to-paper-for-test"', false)
        ->call('openReference', 'strang.flow-step', 'flow.for.flow-for-basic')
        ->call('returnToExample')
        ->assertSet('tabs.flow_for', 'flow-for-basic')
        ->set('tabs.flow_for', 'flow-for-test')
        ->assertSet('tabs.flow_for', 'flow-for-basic')
        ->assertDontSee('id="idea-to-paper-for-test"', false)
        ->assertSee('id="idea-to-paper-for-basic-left"', false)
        ->call('openExample', 'flow.for.flow-for-descending')
        ->assertSet('tabs.flow_for', 'flow-for-descending')
        ->assertSee('id="idea-to-paper-for-descending-left"', false)
        ->assertSee('id="idea-to-paper-for-descending-right"', false)
        ->assertDontSee('id="idea-to-paper-for-test"', false)
        ->call('openReference', 'strang.flow-step', 'flow.for.flow-for-descending')
        ->call('returnToExample')
        ->assertSet('tabs.flow_for', 'flow-for-descending')
        ->call('openExample', 'flow.for.flow-for-multiple-actions')
        ->assertSee('id="idea-to-paper-for-multiple-actions-left"', false)
        ->assertSee('id="idea-to-paper-for-multiple-actions-right"', false)
        ->assertDontSee('id="idea-to-paper-for-test"', false)
        ->call('openReference', 'strang.flow-step', 'flow.for.flow-for-multiple-actions')
        ->call('returnToExample')
        ->assertSet('tabs.flow_for', 'flow-for-multiple-actions')
        ->call('openExample', 'flow.for.flow-for-if')
        ->assertSee('id="idea-to-paper-for-if-left"', false)
        ->assertSee('id="idea-to-paper-for-if-right"', false)
        ->assertDontSee('id="idea-to-paper-for-test"', false)
        ->call('openReference', 'strang.flow-if-else', 'flow.for.flow-for-if')
        ->call('returnToExample')
        ->assertSet('tabs.flow_for', 'flow-for-if');
});

it('keeps saved nested FOR counters, initializations and return levels independent', function (string $variant, int $count) {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.for.flow-for-'.$variant;
    $source = file_get_contents(app('view')->getFinder()->find($view));
    expect($source)->not->toContain('@foreach', '@include', 'strang.flow-while', 'literature.while.');
    $html = view($view)->render();
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    expect($xpath->query('//pre/code')->length)->toBe(8);
    $points = [];
    foreach (['left', 'right'] as $side) {
        $graphId = 'idea-to-paper-for-'.$variant.'-'.$side;
        $root = 'literature.for.'.$variant.'.'.$side.'.';
        $point = function ($suffix) use ($graphId, $root) {
            $anchor = AnchorRegistry::get($graphId, $root.$suffix);
            expect($anchor)->not->toBeNull($suffix);

            return array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($anchor[$axis]), ['x', 'y']);
        };
        expect($point('loop.anchorNode-start'))->toBe($point('loop.condition.anchorNode-end'));
        expect($point('body-return.anchorNode-end'))->toBe($point('initialize-index.anchorNode-end'));
        expect($point('body-return.anchorNode-end'))->not->toBe($point('initialize.anchorNode-end'));
        expect($point('body-return.anchorNode-end'))->toBe($point('loop.anchorNode-return'));
        // The explicit outer-resume arc turns the horizontal increment into the return stem.
        $advance = $point('advance.anchorNode-end');
        expect($point('body-return.anchorNode-start'))->toBe([
            $advance[0] + ($variant === 'mixed' ? -1 : 1) * ($side === 'left' ? 2.75 : -2.75),
            $advance[1] - 2.75,
        ]);
        expect($point('inner-entry.anchorNode-start'))->toBe($point('loop.body.anchorNode-end'));
        expect($point('inner-entry.anchorNode-end'))->toBe($point('inner-loop.anchorNode-return'));
        expect($point('inner-loop.anchorNode-start'))->toBe($point('inner-loop.condition.anchorNode-end'));
        expect($point('inner-return.anchorNode-start'))->toBe($point('inner-advance.anchorNode-end'));
        expect($point('inner-return.anchorNode-end'))->toBe($point('inner-loop.anchorNode-return'));
        expect($point('inner-return.anchorNode-end'))->not->toBe($point('loop.anchorNode-return'));
        if ($variant === 'independent') {
            expect($point('notification-return.anchorNode-start'))->toBe($point('notification-advance.anchorNode-end'));
            expect($point('notification-return.anchorNode-end'))->toBe($point('notification-loop.anchorNode-return'));
            expect($point('notification-return.anchorNode-end'))->not->toBe($point('inner-loop.anchorNode-return'));
        }
        if ($variant === 'action-sequence') {
            expect($point('finalize.anchorNode-end')[1])->toBeGreaterThan($point('inner-loop.anchorNode-end')[1]);
            expect($point('body-return.anchorNode-start')[1])->toBe($point('finalize.anchorNode-end')[1]);
        }
        foreach (['loop.body.anchorNode-end', 'inner-loop.body.anchorNode-end', 'advance.anchorNode-end'] as $key) {
            $points[$side][$key] = $point($key);
        }
        $graph = $xpath->query('//*[@id="'.$graphId.'"]')->item(0);
        expect($graph)->not->toBeNull();
        expect($graph->textContent)->toContain('FOR groupIndex', 'FOR itemIndex', 'Show summary');
        expect($graph->textContent)->not->toContain('=>', '{--');
        $numbers = [];
        foreach ($xpath->query('.//*[contains(concat(" ", normalize-space(@class), " "), " tw-graph-protocol-primitive-dev-node-counter ")]', $graph) as $counter) {
            $numbers[] = (int) trim($counter->textContent);
        }
        sort($numbers);
        expect($numbers)->toBe(range(1, $count));
    }
    foreach ($points['left'] as $key => [$x, $y]) {
        expect($points['right'][$key])->toBe([-$x, $y]);
    }
})->with([
    ['nested', 29], ['independent', 40], ['mixed', 29], ['action-sequence', 30],
]);

it('loads each new FOR variant lazily and restores its deep reference link', function (string $variant) {
    Livewire::test(TwGraphDocumentation::class)
        ->call('openExample', 'flow.for.flow-for-'.$variant)
        ->assertSet('tabs.flow_for', 'flow-for-'.$variant)
        ->assertSee('id="idea-to-paper-for-'.$variant.'-left"', false)
        ->assertSee('id="idea-to-paper-for-'.$variant.'-right"', false)
        ->assertDontSee('id="idea-to-paper-for-test"', false)
        ->call('openReference', 'strang.flow-step', 'flow.for.flow-for-'.$variant)
        ->assertDontSee('id="idea-to-paper-for-'.$variant.'-left"', false)
        ->call('returnToExample')
        ->assertSet('tabs.flow_for', 'flow-for-'.$variant)
        ->assertSee('id="idea-to-paper-for-'.$variant.'-right"', false);
})->with(['switch', 'nested', 'independent', 'mixed', 'action-sequence']);
