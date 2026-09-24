<?php

use Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry;
use Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry;
use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

uses(TestCase::class);

it('returns to its own condition after initialization and exposes only the false continuation', function ($side, $bridge, $trueBridge) {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="while-test" :dev="true">
            <x-translation-workbench::ui.tw-graph.strang.flow-step id="initialize"
                :anchor-start="['x' => '3rem', 'y' => '5rem']"
                :step-label="['text' => ['Initialize']]" :node-end="false" />
            <x-translation-workbench::ui.tw-graph.strang.flow-while id="loop"
                attach-to="initialize.anchorNode-end" :side="$side"
                :condition-label="['text' => ['WHILE?'], 'beforeLength' => '3rem', 'labelGap' => '4rem', 'afterLength' => '5rem']"
                stem-length="7rem" arc-radius="3rem" :true-bridge-length="$trueBridge" :action-label="['text' => ['Action'], 'beforeLength' => $bridge, 'afterLength' => '6rem']" />
            <x-translation-workbench::ui.tw-graph.strang.flow-step id="continue"
                attach-to="loop.anchorNode-end" :step-label="['text' => ['Finished']]" />
        </x-translation-workbench::ui.tw-graph>
    BLADE, compact('side', 'bridge', 'trueBridge'));
    $point = fn ($id) => array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression(AnchorRegistry::get('while-test', $id)[$axis]), ['x', 'y']);
    $start = $point('loop.anchorNode-start');
    expect($start)->toBe($point('initialize.anchorNode-end'));
    expect($point('loop.return.anchorNode-end'))->toBe($start);
    expect($point('loop.condition.anchorNode-end'))->toBe([$start[0], $start[1] + 12]);
    expect($point('loop.anchorNode-end'))->toBe([$start[0], $start[1] + 19]);
    $span = BoundsRegistry::evaluateRemExpression($bridge) + 6 + 12 + 6 + BoundsRegistry::evaluateRemExpression($trueBridge);
    expect($point('loop.body.anchorNode-end'))->toBe([$start[0] + ($side === 'left' ? -$span : $span), $start[1] + 12]);
    // The canvas includes the outer return even though all geometry uses lower-level segments.
    $bounds = BoundsRegistry::canvasMetrics('while-test');
    expect($bounds['widthRem'])->toBeGreaterThan($span);
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    // The incoming body bridge must retain even a length above the label helper's usual limit.
    $line = $xpath->query('//*[@data-tw-graph-path="loop.body.bridge.bridge-in"]')->item(0);
    expect($line)->not->toBeNull();
    preg_match('/--tw-graph-protocol-local-length:\s*([^;]+)/', $line->getAttribute('style'), $length);
    expect(BoundsRegistry::evaluateRemExpression($length[1] ?? ''))->toBe(BoundsRegistry::evaluateRemExpression($bridge));
    $trueEnd = $point('loop.true.bridge.anchorNode-end');
    expect($trueEnd)->toBe([$start[0] + ($side === 'left' ? -1 : 1) * (3 + BoundsRegistry::evaluateRemExpression($trueBridge)), $start[1] + 15]);
    $returnLine = $xpath->query('//*[@data-tw-graph-path="loop.return.bridge"]')->item(0);
    preg_match('/--tw-graph-protocol-local-length:\s*([^;]+)/', $returnLine->getAttribute('style'), $returnLength);
    expect(BoundsRegistry::evaluateRemExpression($returnLength[1]))->toBe($span - 6);
    expect($html)->toContain('loop.true.arc-in.end.joint-arrow', 'loop.true.bridge.node.end');
    expect($html)->not->toContain('loop.true.bridge.end.joint-arrow');
    expect($html)->toContain('loop.return.arc-out.node.end', 'TRUE', 'FALSE');
})->with(['left', 'right'])->with(['2rem', '20rem'])->with(['2rem', '5rem']);

it('rejects invalid while dimensions instead of replacing them silently', function ($length) {
    expect(fn () => Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="invalid-while">
            <x-translation-workbench::ui.tw-graph.strang.flow-while id="loop" :stem-length="$length" />
        </x-translation-workbench::ui.tw-graph>
    BLADE, ['length' => $length]))->toThrow(\Illuminate\View\ViewException::class, 'flow-while stemLength must be a positive rem length.');
})->with(['-1rem', '0rem', 'invalid']);

it('renders a handmade while preview with extracted source, tools and six language examples', function () {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.while.flow-while-test';
    $html = view($view)->render();
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    $graph = $xpath->query('//*[@id="idea-to-paper-while-basic"]')->item(0);
    expect($graph)->not->toBeNull();
    expect($graph->textContent)->toContain('WHILE groupIndex < groupCount?', 'WHILE itemIndex < itemCount?', 'itemIndex = itemIndex + 1', 'groupIndex = groupIndex + 1', 'Show summary');
    expect($graph->textContent)->not->toContain('{--', '=>', ':anchor-start=');
    expect($xpath->query('//pre/code')->length)->toBe(7);
    expect($html)->toContain('data-tw-graph-preview-tools', 'JavaScript', 'C++', 'Java', 'zero');
});

it('links the while preview and deep reference in both directions', function () {
    \Livewire\Livewire::test(\Gunreip\TranslationWorkbench\Livewire\TwGraphDocumentation::class)
        ->call('openExample', 'flow.while.flow-while-test')
        ->assertSet('tabs.flow_index', 'flow-while')
        ->assertSee('id="idea-to-paper-while-basic"', false)
        ->call('openReference', 'strang.flow-while', 'flow.while.flow-while-test')
        ->assertSet('tabs.reference_strang', 'reference-flow-while')
        ->assertSee('Return stem length')
        ->call('returnToExample')
        ->assertSet('tabs.flow_while', 'flow-while-test')
        ->assertSee('id="idea-to-paper-while-basic"', false);
});

it('rejects an unresolved while attachment rather than falling back to the origin', function () {
    expect(fn () => Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="missing-while-target">
            <x-translation-workbench::ui.tw-graph.strang.flow-while id="loop" attach-to="missing.anchorNode-end" />
        </x-translation-workbench::ui.tw-graph>
    BLADE))->toThrow(\Illuminate\View\ViewException::class, 'flow-while attachTo does not resolve to an anchor.');
});

it('rejects removed global label lengths rather than ignoring them', function ($prop) {
    $markup = '<x-translation-workbench::ui.tw-graph graph-id="removed-while-prop">'
        .'<x-translation-workbench::ui.tw-graph.strang.flow-while id="loop" '.$prop.'="9rem" />'
        .'</x-translation-workbench::ui.tw-graph>';
    expect(fn () => Blade::render($markup))->toThrow(\Illuminate\View\ViewException::class, 'was removed; configure condition-label or action-label instead.');
})->with(['before-length', 'after-length', 'label-gap', 'bridge-length', 'bridge-out-length', 'beforeLength', 'afterLength', 'labelGap', 'bridgeLength', 'bridgeOutLength']);

it('keeps independently configured condition and action lengths in their own geometry', function () {
    $points = [];
    foreach ([['3rem', '4rem', '5rem', '2rem', '6rem'], ['8rem', '6rem', '7rem', '9rem', '1rem']] as $values) {
        [$before, $gap, $after, $actionBefore, $actionAfter] = $values;
        $condition = ['text' => ['Condition'], 'beforeLength' => $before, 'labelGap' => $gap, 'afterLength' => $after];
        $action = ['text' => ['Action'], 'beforeLength' => $actionBefore, 'afterLength' => $actionAfter];
        Blade::render(<<<'BLADE'
            <x-translation-workbench::ui.tw-graph graph-id="while-config">
                <x-translation-workbench::ui.tw-graph.strang.flow-while id="loop"
                    :anchor-start="['x' => '0rem', 'y' => '5rem']"
                    :condition-label="$condition" :action-label="$action" />
            </x-translation-workbench::ui.tw-graph>
        BLADE, compact('condition', 'action'));
        $point = fn ($id) => array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression(AnchorRegistry::get('while-config', $id)[$axis]), ['x', 'y']);
        $height = array_sum(array_map(fn ($value) => BoundsRegistry::evaluateRemExpression($value), [$before, $gap, $after]));
        $width = 19.5 + BoundsRegistry::evaluateRemExpression($actionBefore) + BoundsRegistry::evaluateRemExpression($actionAfter);
        expect($point('loop.condition.anchorNode-end'))->toBe([0.0, 5 + $height]);
        expect($point('loop.body.anchorNode-end'))->toBe([-$width, 5 + $height]);
        expect($point('loop.return.anchorNode-end'))->toBe([0.0, 5.0]);
        expect($point('loop.anchorNode-end'))->toBe([0.0, 9 + $height]);
    }
});

it('uses the closed path independently of WHILE and rejects incompatible return anchors', function () {
    Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="closed-path">
            <x-translation-workbench::ui.tw-graph.paths.loop id="route"
                :anchor-start="['x' => '2rem', 'y' => '15rem']"
                :anchor-return="['x' => '2rem', 'y' => '5rem']"
                :bridge-label="['text' => ['Work']]" />
        </x-translation-workbench::ui.tw-graph>
    BLADE);
    expect(AnchorRegistry::get('closed-path', 'route.anchorNode-start')['y'])->toBe('15rem');
    expect(AnchorRegistry::get('closed-path', 'route.return.anchorNode-end')['y'])->toBe('5rem');
    expect(fn () => Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="bad-closed-path">
            <x-translation-workbench::ui.tw-graph.paths.loop id="route"
                :anchor-start="['x' => '2rem', 'y' => '15rem']"
                :anchor-return="['x' => '3rem', 'y' => '5rem']" />
        </x-translation-workbench::ui.tw-graph>
    BLADE))->toThrow(\Illuminate\View\ViewException::class, 'same X axis');
});

it('documents two independently authored mirrored basic loops', function () {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.while.flow-while-basic';
    $source = file_get_contents(app('view')->getFinder()->find($view));
    expect(substr_count($source, '<x-translation-workbench::ui.tw-graph.strang.flow-while'))->toBe(2);
    expect($source)->not->toContain('@foreach', 'flow-while-test-proposals');
    $html = view($view)->render();
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    expect($xpath->query('//pre/code')->length)->toBe(8);
    $points = [];
    foreach (['left', 'right'] as $side) {
        $graph = 'idea-to-paper-while-basic-'.$side;
        $root = 'literature.while.basic.'.$side.'.loop';
        $canvas = $xpath->query('//*[@id="'.$graph.'"]')->item(0);
        expect($canvas)->not->toBeNull();
        expect($canvas->textContent)->toContain('WHILE pending items?', 'TRUE', 'FALSE', 'Show summary');
        expect($canvas->textContent)->not->toContain('{--', '=>', ':action-label=');
        foreach (['anchorNode-start', 'body.anchorNode-end', 'return.anchorNode-end', 'anchorNode-end'] as $key) {
            $anchor = AnchorRegistry::get($graph, $root.'.'.$key);
            $points[$side][$key] = array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($anchor[$axis]), ['x', 'y']);
        }
        expect($points[$side]['return.anchorNode-end'])->toBe($points[$side]['anchorNode-start']);
    }
    foreach ($points['left'] as $key => [$x, $y]) {
        expect($points['right'][$key])->toBe([-$x, $y]);
    }
});

it('loads WHILE basic lazily and returns to it from the reference', function () {
    \Livewire\Livewire::test(\Gunreip\TranslationWorkbench\Livewire\TwGraphDocumentation::class)
        ->assertDontSee('id="idea-to-paper-while-basic-left"', false)
        ->call('openExample', 'flow.while.flow-while-basic')
        ->assertSet('tabs.flow_while', 'flow-while-basic')
        ->assertSee('id="idea-to-paper-while-basic-left"', false)
        ->assertSee('id="idea-to-paper-while-basic-right"', false)
        ->assertDontSee('id="flow-while-test-proposals"', false)
        ->call('openReference', 'strang.flow-while', 'flow.while.flow-while-basic')
        ->assertSet('tabs.reference_strang', 'reference-flow-while')
        ->call('returnToExample')
        ->assertSet('tabs.flow_while', 'flow-while-basic')
        ->set('tabs.flow_while', 'flow-while-test')
        ->assertSee('id="flow-while-test-proposals"', false)
        ->assertDontSee('id="idea-to-paper-while-basic-right"', false);
});

it('returns only after the independent advance action in the multiple-actions example', function () {
    $html = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.while.flow-while-multiple-actions')->render();
    $graph = 'idea-to-paper-while-multiple-left';
    $point = fn ($id) => array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression(AnchorRegistry::get($graph, 'literature.while.multiple.left.'.$id)[$axis]), ['x', 'y']);
    $body = $point('loop.body.anchorNode-end');
    $advance = $point('advance.anchorNode-end');
    expect($advance)->toBe([$body[0], $body[1] - 8]);
    expect($point('body-return.anchorNode-start'))->toBe($advance);
    expect($point('body-return.anchorNode-end'))->toBe($point('loop.anchorNode-start'));
    expect($point('loop.anchorNode-return'))->toBe($point('loop.anchorNode-start'));
    expect(AnchorRegistry::get($graph, 'literature.while.multiple.left.loop.return.anchorNode-end'))->toBeNull();
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    expect($xpath->query('//*[@data-tw-graph-path="literature.while.multiple.left.loop.return.stem"]')->length)->toBe(0);
    $arrow = $xpath->query('//*[@data-tw-graph-path="literature.while.multiple.left.advance.stem.after.end.joint-arrow"]')->item(0);
    expect($arrow)->not->toBeNull();
    expect($arrow->getAttribute('class'))->toContain('joint-arrow-bottom');
    expect($xpath->query('//*[@data-tw-graph-path="literature.while.multiple.left.advance.stem.after.node.end" and contains(@class, "primitive-node")]')->length)->toBe(0);
    $returnStem = $xpath->query('//*[@data-tw-graph-path="literature.while.multiple.left.body-return.stem"]')->item(0);
    expect($returnStem)->not->toBeNull();
    preg_match('/--tw-graph-protocol-local-length:\s*([^;]+)/', $returnStem->getAttribute('style'), $length);
    expect(BoundsRegistry::evaluateRemExpression($length[1]))->toBe($advance[1] - $point('loop.anchorNode-start')[1]);
});

it('rejects a return that would need to stretch the authored loop or cross its axis', function ($start) {
    expect(fn () => Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="bad-loop-return">
            <x-translation-workbench::ui.tw-graph.paths.loop-return id="return"
                :anchor-start="$start" :anchor-return="['x' => '0rem', 'y' => '5rem']"
                side="left" arc-radius="2rem" />
        </x-translation-workbench::ui.tw-graph>
    BLADE, ['start' => $start]))->toThrow(\Illuminate\View\ViewException::class, 'cannot fit the supplied anchors');
})->with([
    [['x' => '-20rem', 'y' => '4rem']],
    [['x' => '-3rem', 'y' => '10rem']],
    [['x' => '20rem', 'y' => '10rem']],
]);

it('saves two handmade mirrored multiple-action loops with the return after each advance', function () {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.while.flow-while-multiple-actions';
    $source = file_get_contents(app('view')->getFinder()->find($view));
    expect($source)->not->toContain('@foreach', 'flow-while-test-proposals');
    expect(substr_count($source, '<x-translation-workbench::ui.tw-graph.strang.flow-while'))->toBe(2);
    expect(substr_count($source, '<x-translation-workbench::ui.tw-graph.paths.loop-return'))->toBe(2);
    $html = view($view)->render();
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    expect($xpath->query('//pre/code')->length)->toBe(8);
    $points = [];
    foreach (['left', 'right'] as $side) {
        $graph = 'idea-to-paper-while-multiple-'.$side;
        $root = 'literature.while.multiple.'.$side;
        $canvas = $xpath->query('//*[@id="'.$graph.'"]')->item(0);
        expect($canvas)->not->toBeNull();
        expect($canvas->textContent)->toContain('Process current item', 'index = index + 1', 'Show summary');
        expect($canvas->textContent)->not->toContain('{--', '=>', ':action-label=');
        foreach (['loop.anchorNode-start', 'loop.body.anchorNode-end', 'loop.anchorNode-end', 'advance.anchorNode-end', 'body-return.anchorNode-start', 'body-return.anchorNode-end'] as $key) {
            $anchor = AnchorRegistry::get($graph, $root.'.'.$key);
            $points[$side][$key] = array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($anchor[$axis]), ['x', 'y']);
        }
        expect($points[$side]['body-return.anchorNode-start'])->toBe($points[$side]['advance.anchorNode-end']);
        expect($points[$side]['body-return.anchorNode-end'])->toBe($points[$side]['loop.anchorNode-start']);
        expect(AnchorRegistry::get($graph, $root.'.loop.return.anchorNode-end'))->toBeNull();
        $arrow = $xpath->query('//*[@data-tw-graph-path="'.$root.'.advance.stem.after.end.joint-arrow"]')->item(0);
        expect($arrow)->not->toBeNull();
        expect($arrow->getAttribute('class'))->toContain('joint-arrow-bottom');
    }
    foreach ($points['left'] as $key => [$x, $y]) {
        // Each saved variant remains independently editable, including its vertical spacing.
        expect($points['right'][$key][0])->toBe(-$x);
    }
});

it('loads the saved multiple-action loops lazily and restores them from deep reference', function () {
    \Livewire\Livewire::test(\Gunreip\TranslationWorkbench\Livewire\TwGraphDocumentation::class)
        ->call('openExample', 'flow.while.flow-while-multiple-actions')
        ->assertSet('tabs.flow_while', 'flow-while-multiple-actions')
        ->assertSee('id="idea-to-paper-while-multiple-left"', false)
        ->assertSee('id="idea-to-paper-while-multiple-right"', false)
        ->assertDontSee('id="flow-while-test-proposals"', false)
        ->call('openReference', 'strang.flow-while', 'flow.while.flow-while-multiple-actions')
        ->assertSet('tabs.reference_strang', 'reference-flow-while')
        ->call('returnToExample')
        ->assertSet('tabs.flow_while', 'flow-while-multiple-actions')
        ->set('tabs.flow_while', 'flow-while-test')
        ->assertSee('id="flow-while-test-proposals"', false)
        ->assertDontSee('id="idea-to-paper-while-multiple-right"', false);
});

it('merges both nested IF lanes before advancing and returns to the outer WHILE condition', function () {
    $html = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.while.flow-while-if')->render();
    $graph = 'idea-to-paper-while-if-left';
    $point = fn ($id) => array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression(AnchorRegistry::get($graph, 'literature.while.if.left.'.$id)[$axis]), ['x', 'y']);
    $body = $point('loop.body.anchorNode-end');
    $decision = $point('inner-if.question.anchorNode-end');
    expect($decision[0])->toBe($body[0]);
    expect($decision[1])->toBeLessThan($body[1]);
    $merge = $point('inner-if.anchorNode-end');
    expect($point('inner-if.true.stem.anchorNode-end'))->toBe($merge);
    expect($point('inner-if.false.anchorNode-end'))->toBe($merge);
    $advance = $point('advance.anchorNode-end');
    expect($advance[0])->toBe($merge[0]);
    expect($advance[1])->toBeLessThan($merge[1]);
    expect($point('body-return.anchorNode-start'))->toBe($advance);
    expect($point('body-return.anchorNode-end'))->toBe($point('loop.anchorNode-start'));
    expect($point('body-return.anchorNode-end'))->not->toBe($decision);
    expect($point('loop.anchorNode-end')[0])->toBe($point('loop.anchorNode-start')[0]);
    expect(AnchorRegistry::get($graph, 'literature.while.if.left.loop.return.anchorNode-end'))->toBeNull();
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    $canvas = $xpath->query('//*[@id="idea-to-paper-while-if-left"]')->item(0);
    expect($canvas->textContent)->toContain('Read current item', 'IF item enabled?', 'Process item', 'Record skipped item', 'index = index + 1');
});

it('colors only the WHILE action route and places TRUE at the selected existing anchor', function ($side, $anchor) {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="while-label-options">
            <x-translation-workbench::ui.tw-graph.strang.flow-while id="loop" color="cyan" :side="$side"
                :action-label="['text' => ['Action'], 'color' => 'green']"
                :true-label="['text' => ['TRUE'], 'anchor' => $anchor, 'side' => 'right', 'color' => 'green']" />
        </x-translation-workbench::ui.tw-graph>
    BLADE, compact('side', 'anchor'));
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    $element = fn ($suffix) => $xpath->query('//*[@data-tw-graph-path="loop.'.$suffix.'"]')->item(0);
    $green = \Gunreip\TranslationWorkbench\Support\TranslationWorkbenchColorPalette::rgb('green');
    $cyan = \Gunreip\TranslationWorkbench\Support\TranslationWorkbenchColorPalette::rgb('cyan');
    foreach (['true.arc-in', 'true.bridge', 'body.bridge.bridge-in', 'body.bridge.bridge-out', 'body.arc-out'] as $suffix) {
        expect($element($suffix)->getAttribute('style'))->toContain('--tw-graph-protocol-local-color-rgb: '.$green.';');
    }
    foreach (['condition.stem.after', 'false.stem', 'return.stem'] as $suffix) {
        expect($element($suffix)->getAttribute('style'))->toContain('--tw-graph-protocol-local-color-rgb: '.$cyan.';');
    }
    expect(AnchorRegistry::get('while-label-options', 'loop.body.anchorNode-end')['color'])->toBe('green');
    $label = $element('true.label');
    expect($label->getAttribute('class'))->toContain('text-label-right');
    $expected = AnchorRegistry::get('while-label-options', $anchor === 'condition' ? 'loop.condition.anchorNode-end' : 'loop.true.bridge.anchorNode-end');
    foreach (['x', 'y'] as $axis) {
        preg_match('/--tw-graph-protocol-anchor-'.$axis.':\s*([^;]+)/', $label->getAttribute('style'), $coordinate);
        expect(BoundsRegistry::evaluateRemExpression($coordinate[1]))->toBe(BoundsRegistry::evaluateRemExpression($expected[$axis]));
    }
    expect($element('true.bridge.end.joint-arrow') !== null)->toBe($anchor === 'condition');
    expect($xpath->query('//*[@data-tw-graph-path="loop.true.bridge.node.end" and contains(@class, "primitive-node")]')->length > 0)->toBe($anchor === 'bridge');
})->with(['left', 'right'])->with(['bridge', 'condition']);

it('rejects an unknown TRUE label anchor rather than silently moving it', function () {
    expect(fn () => Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="invalid-label-anchor">
            <x-translation-workbench::ui.tw-graph.strang.flow-while id="loop" :true-label="['anchor' => 'unknown']" />
        </x-translation-workbench::ui.tw-graph>
    BLADE))->toThrow(\Illuminate\View\ViewException::class, 'true-label.anchor must be bridge or condition');
});

it('passes the explicit FALSE color to the exit stem and Dot without changing the condition', function ($side) {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="while-exit-color" :dev="true">
            <x-translation-workbench::ui.tw-graph.strang.flow-while id="loop" color="cyan" :side="$side"
                :false-label="['text' => ['FALSE'], 'color' => 'red']" />
        </x-translation-workbench::ui.tw-graph>
    BLADE, compact('side'));
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    $red = \Gunreip\TranslationWorkbench\Support\TranslationWorkbenchColorPalette::rgb('red');
    $cyan = \Gunreip\TranslationWorkbench\Support\TranslationWorkbenchColorPalette::rgb('cyan');
    foreach (['false.stem', 'false.stem.node.end'] as $suffix) {
        $node = $xpath->query('//*[@data-tw-graph-path="loop.'.$suffix.'"]')->item(0);
        expect($node)->not->toBeNull();
        expect($node->getAttribute('style'))->toContain('--tw-graph-protocol-local-color-rgb: '.$red.';');
    }
    $condition = $xpath->query('//*[@data-tw-graph-path="loop.condition.stem.after"]')->item(0);
    expect($condition->getAttribute('style'))->toContain('--tw-graph-protocol-local-color-rgb: '.$cyan.';');
    expect(AnchorRegistry::get('while-exit-color', 'loop.anchorNode-end')['color'])->toBe('red');
})->with(['left', 'right']);

it('saves independent left and right WHILE with IF examples with closed body returns', function () {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.while.flow-while-if';
    $source = file_get_contents(app('view')->getFinder()->find($view));
    expect($source)->not->toContain('@foreach', 'flow-while-test-proposals');
    expect(substr_count($source, '<x-translation-workbench::ui.tw-graph.strang.flow-if-else'))->toBe(2);
    $html = view($view)->render();
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    expect($xpath->query('//pre/code')->length)->toBe(8);
    $bodyX = [];
    foreach (['left', 'right'] as $side) {
        $graph = 'idea-to-paper-while-if-'.$side;
        $root = 'literature.while.if.'.$side;
        $point = function ($suffix) use ($graph, $root) {
            $anchor = AnchorRegistry::get($graph, $root.'.'.$suffix);
            expect($anchor)->not->toBeNull();

            return array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($anchor[$axis]), ['x', 'y']);
        };
        $canvas = $xpath->query('//*[@id="'.$graph.'"]')->item(0);
        expect($canvas)->not->toBeNull();
        expect($canvas->textContent)->toContain('IF item enabled?', 'Process item', 'Record skipped item', 'Show summary');
        expect($canvas->textContent)->not->toContain('{--', ':action-label=');
        $merge = $point('inner-if.anchorNode-end');
        expect($point('inner-if.true.stem.anchorNode-end'))->toBe($merge);
        expect($point('inner-if.false.anchorNode-end'))->toBe($merge);
        expect($point('body-return.anchorNode-start'))->toBe($point('advance.anchorNode-end'));
        expect($point('body-return.anchorNode-end'))->toBe($point('loop.anchorNode-start'));
        $bodyX[$side] = $point('loop.body.anchorNode-end')[0];
    }
    expect($bodyX['left'])->toBeLessThan(0);
    expect($bodyX['right'])->toBe(-$bodyX['left']);
});

it('loads WHILE with IF lazily and restores it from deep reference', function () {
    \Livewire\Livewire::test(\Gunreip\TranslationWorkbench\Livewire\TwGraphDocumentation::class)
        ->call('openExample', 'flow.while.flow-while-if')
        ->assertSet('tabs.flow_while', 'flow-while-if')
        ->assertSee('id="idea-to-paper-while-if-left"', false)
        ->assertSee('id="idea-to-paper-while-if-right"', false)
        ->assertDontSee('id="flow-while-test-proposals"', false)
        ->call('openReference', 'strang.flow-while', 'flow.while.flow-while-if')
        ->call('returnToExample')
        ->assertSet('tabs.flow_while', 'flow-while-if')
        ->set('tabs.flow_while', 'flow-while-test')
        ->assertSee('id="flow-while-test-proposals"', false)
        ->assertDontSee('id="idea-to-paper-while-if-right"', false);
});

it('keeps two independent inner WHILE returns separate and advances the outer index after the second FALSE', function () {
    $html = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.while.flow-while-independent')->render();
    $graph = 'idea-to-paper-while-independent-left';
    $point = function ($suffix) use ($graph) {
        $anchor = AnchorRegistry::get($graph, 'literature.while.independent.left.'.$suffix);
        expect($anchor)->not->toBeNull();

        return array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($anchor[$axis]), ['x', 'y']);
    };
    expect($point('inner-entry.anchorNode-start'))->toBe($point('loop.body.anchorNode-end'));
    expect($point('inner-entry.anchorNode-end'))->toBe($point('inner-loop.anchorNode-start'));
    expect($point('inner-loop.anchorNode-start')[1])->toBeLessThan($point('loop.body.anchorNode-end')[1]);
    expect($point('inner-return.anchorNode-start'))->toBe($point('inner-advance.anchorNode-end'));
    expect($point('inner-return.anchorNode-end'))->toBe($point('inner-loop.anchorNode-start'));
    expect($point('body-return.anchorNode-end'))->toBe($point('loop.anchorNode-start'));
    expect($point('inner-return.anchorNode-end'))->not->toBe($point('body-return.anchorNode-end'));
    expect($point('notification-return.anchorNode-start'))->toBe($point('notification-advance.anchorNode-end'));
    expect($point('notification-return.anchorNode-end'))->toBe($point('notification-loop.anchorNode-start'));
    expect($point('notification-return.anchorNode-end'))->not->toBe($point('inner-return.anchorNode-end'));
    expect($point('notification-loop.anchorNode-start'))->toBe($point('notification-initialize.anchorNode-end'));
    expect($point('notification-loop.anchorNode-start')[1])->toBeGreaterThan($point('inner-loop.anchorNode-end')[1]);
    $exit = $point('notification-loop.anchorNode-end');
    $return = $point('body-return.anchorNode-start');
    expect($return[0])->toBeGreaterThan($exit[0])->toBeLessThan($point('loop.anchorNode-start')[0]);
    expect($return[1])->toBe($exit[1]);
    foreach (['loop', 'inner-loop', 'notification-loop'] as $loop) {
        expect(AnchorRegistry::get($graph, 'literature.while.independent.left.'.$loop.'.return.anchorNode-end'))->toBeNull();
    }
    expect($html)->toContain('WHILE groupIndex', 'WHILE itemIndex', 'itemIndex = itemIndex + 1', 'groupIndex = groupIndex + 1');
});

it('inherits the shared canvas arc radius across WHILE and return paths and honors explicit overrides', function ($canvasRadius, $localRadius, $expected) {
    $template = <<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="while-radius" :arc-radius="$canvasRadius">
            <x-translation-workbench::ui.tw-graph.strang.flow-while id="loop" :arc-radius="$localRadius" />
            <x-translation-workbench::ui.tw-graph.paths.loop id="path"
                :anchor-start="['x' => '0rem', 'y' => '12rem']"
                :anchor-return="['x' => '0rem', 'y' => '0rem']" :arc-radius="$localRadius" />
            <x-translation-workbench::ui.tw-graph.paths.loop-return id="return"
                :anchor-start="['x' => '-20rem', 'y' => '12rem']"
                :anchor-return="['x' => '0rem', 'y' => '0rem']" :arc-radius="$localRadius" />
        </x-translation-workbench::ui.tw-graph>
    BLADE;
    // Omitted props exercise Blade inheritance; a supplied null is an explicit local value.
    if ($localRadius === null) {
        $template = str_replace(' :arc-radius="$localRadius"', '', $template);
    }
    $html = Blade::render($template, compact('canvasRadius', 'localRadius'));
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    foreach (['loop.true.arc-in', 'loop.return.arc-in', 'path.true.arc-in', 'path.return.arc-in', 'return.arc-in'] as $id) {
        $arc = $xpath->query('//*[@data-tw-graph-path="'.$id.'"]')->item(0);
        expect($arc)->not->toBeNull();
        expect($arc->getAttribute('style'))->toContain('--tw-graph-protocol-local-arc-radius: '.$expected.';');
    }
})->with([
    'shared default' => [null, null, '2.75rem'],
    'canvas override' => ['3.5rem', null, '3.5rem'],
    'explicit component override' => ['3.5rem', '2rem', '2rem'],
]);

it('saves handmade mirrored nested WHILE examples with independent entry and return targets', function () {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.while.flow-while-nested';
    $source = file_get_contents(app('view')->getFinder()->find($view));
    expect($source)->not->toContain('@foreach', 'flow-while-test-proposals');
    expect(substr_count($source, '<x-translation-workbench::ui.tw-graph.strang.flow-while'))->toBe(4);
    $html = view($view)->render();
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    expect($xpath->query('//pre/code')->length)->toBe(8);
    $points = [];
    foreach (['left', 'right'] as $side) {
        $graph = 'idea-to-paper-while-nested-'.$side;
        $root = 'literature.while.nested.'.$side;
        $point = function ($suffix) use ($graph, $root) {
            $anchor = AnchorRegistry::get($graph, $root.'.'.$suffix);
            expect($anchor)->not->toBeNull();

            return array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($anchor[$axis]), ['x', 'y']);
        };
        expect($point('inner-entry.anchorNode-start'))->toBe($point('loop.body.anchorNode-end'));
        expect($point('inner-entry.anchorNode-end'))->toBe($point('inner-loop.anchorNode-start'));
        expect($point('inner-return.anchorNode-start'))->toBe($point('inner-advance.anchorNode-end'));
        expect($point('inner-return.anchorNode-end'))->toBe($point('inner-loop.anchorNode-start'));
        expect($point('body-return.anchorNode-end'))->toBe($point('loop.anchorNode-start'));
        expect($point('inner-return.anchorNode-end'))->not->toBe($point('body-return.anchorNode-end'));
        foreach (['loop.body.anchorNode-end', 'inner-loop.anchorNode-start', 'inner-loop.body.anchorNode-end', 'advance.anchorNode-end', 'body-return.anchorNode-start'] as $key) {
            $points[$side][$key] = $point($key);
        }
        $canvas = $xpath->query('//*[@id="'.$graph.'"]')->item(0);
        expect($canvas)->not->toBeNull();
        expect($canvas->textContent)->toContain('WHILE groupIndex', 'WHILE itemIndex', 'Show summary');
        expect($canvas->textContent)->not->toContain('{--', ':action-label=');
    }
    foreach ($points['left'] as $key => [$x, $y]) {
        expect($points['right'][$key][0])->toBe(-$x);
    }
});

it('loads saved nested WHILE examples lazily and restores them from deep reference', function () {
    \Livewire\Livewire::test(\Gunreip\TranslationWorkbench\Livewire\TwGraphDocumentation::class)
        ->call('openExample', 'flow.while.flow-while-nested')
        ->assertSet('tabs.flow_while', 'flow-while-nested')
        ->assertSee('id="idea-to-paper-while-nested-left"', false)
        ->assertSee('id="idea-to-paper-while-nested-right"', false)
        ->assertDontSee('id="flow-while-test-proposals"', false)
        ->call('openReference', 'strang.flow-while', 'flow.while.flow-while-nested')
        ->call('returnToExample')
        ->assertSet('tabs.flow_while', 'flow-while-nested')
        ->set('tabs.flow_while', 'flow-while-test')
        ->assertSee('id="flow-while-test-proposals"', false)
        ->assertDontSee('id="idea-to-paper-while-nested-right"', false);
});

it('saves handmade mirrored independent inner WHILE examples with independent entry and return targets', function () {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.while.flow-while-independent';
    $source = file_get_contents(app('view')->getFinder()->find($view));
    expect($source)->not->toContain('@foreach', 'flow-while-test-proposals');
    expect(substr_count($source, '<x-translation-workbench::ui.tw-graph.strang.flow-while'))->toBe(6);
    $html = view($view)->render();
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    expect($xpath->query('//pre/code')->length)->toBe(8);
    $points = [];
    foreach (['left', 'right'] as $side) {
        $graph = 'idea-to-paper-while-independent-'.$side;
        $root = 'literature.while.independent.'.$side;
        $point = function ($suffix) use ($graph, $root) {
            $anchor = AnchorRegistry::get($graph, $root.'.'.$suffix);
            expect($anchor)->not->toBeNull();

            return array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($anchor[$axis]), ['x', 'y']);
        };
        expect($point('inner-entry.anchorNode-start'))->toBe($point('loop.body.anchorNode-end'));
        expect($point('inner-entry.anchorNode-end'))->toBe($point('inner-loop.anchorNode-start'));
        expect($point('inner-return.anchorNode-start'))->toBe($point('inner-advance.anchorNode-end'));
        expect($point('inner-return.anchorNode-end'))->toBe($point('inner-loop.anchorNode-start'));
        expect($point('body-return.anchorNode-end'))->toBe($point('loop.anchorNode-start'));
        expect($point('inner-return.anchorNode-end'))->not->toBe($point('body-return.anchorNode-end'));
        foreach (['loop.body.anchorNode-end', 'inner-loop.anchorNode-start', 'inner-loop.body.anchorNode-end', 'advance.anchorNode-end', 'body-return.anchorNode-start'] as $key) {
            $points[$side][$key] = $point($key);
        }
        $canvas = $xpath->query('//*[@id="'.$graph.'"]')->item(0);
        expect($canvas)->not->toBeNull();
        expect($canvas->textContent)->toContain('WHILE groupIndex', 'WHILE itemIndex', 'Show summary');
        expect($canvas->textContent)->not->toContain('{--', ':action-label=');
    }
    foreach ($points['left'] as $key => [$x, $y]) {
        expect($points['right'][$key][0])->toBe(-$x);
    }
});

it('loads saved independent inner WHILE examples lazily and restores them from deep reference', function () {
    \Livewire\Livewire::test(\Gunreip\TranslationWorkbench\Livewire\TwGraphDocumentation::class)
        ->call('openExample', 'flow.while.flow-while-independent')
        ->assertSet('tabs.flow_while', 'flow-while-independent')
        ->assertSee('id="idea-to-paper-while-independent-left"', false)
        ->assertSee('id="idea-to-paper-while-independent-right"', false)
        ->assertDontSee('id="flow-while-test-proposals"', false)
        ->call('openReference', 'strang.flow-while', 'flow.while.flow-while-independent')
        ->call('returnToExample')
        ->assertSet('tabs.flow_while', 'flow-while-independent')
        ->set('tabs.flow_while', 'flow-while-test')
        ->assertSee('id="flow-while-test-proposals"', false)
        ->assertDontSee('id="idea-to-paper-while-independent-right"', false);
});

it('numbers every visible DEV counter once without gaps across composed WHILE examples', function ($view, $count) {
    $html = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.while.'.$view)->render();
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    foreach ($xpath->query('//*[@data-tw-graph-bounds-model]') as $graph) {
        $numbers = [];
        foreach ($xpath->query('.//*[contains(concat(" ", normalize-space(@class), " "), " tw-graph-protocol-primitive-dev-node-counter ")]', $graph) as $node) {
            $value = trim($node->textContent);
            expect(ctype_digit($value))->toBeTrue();
            $numbers[] = (int) $value;
        }
        sort($numbers);
        expect($numbers)->toBe(range(1, $count));
    }
})->with([
    ['flow-while-test', 30],
    ['flow-while-nested', 29],
    ['flow-while-action-sequence', 30],
    ['flow-while-mixed', 29],
    ['flow-while-independent', 40],
]);

it('renders mirrored mixed-side loops with separate returns and two explicit crossings per graph', function () {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.while.flow-while-mixed';
    $html = view($view)->render();
    $source = file_get_contents(app('view')->getFinder()->find($view));
    expect(substr_count($source, "'over' =>"))->toBe(4);
    $points = [];
    foreach ([['left', 'idea-to-paper-while-mixed-left', 'literature.while.mixed.left', -1], ['right', 'idea-to-paper-while-mixed-right', 'literature.while.mixed.right', 1]] as [$side, $graph, $root, $sign]) {
        $point = function ($suffix) use ($graph, $root) {
            $anchor = AnchorRegistry::get($graph, $root.'.'.$suffix);
            expect($anchor)->not->toBeNull();

            return array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($anchor[$axis]), ['x', 'y']);
        };
        $outer = $point('loop.anchorNode-start');
        $body = $point('loop.body.anchorNode-end');
        $inner = $point('inner-loop.anchorNode-start');
        $innerBody = $point('inner-loop.body.anchorNode-end');
        expect(($body[0] - $outer[0]) * $sign)->toBeGreaterThan(0);
        expect(($innerBody[0] - $inner[0]) * $sign)->toBeLessThan(0);
        expect($point('inner-entry.anchorNode-end'))->toBe($inner);
        expect($point('inner-return.anchorNode-end'))->toBe($inner);
        expect($point('body-return.anchorNode-end'))->toBe($outer);
        foreach (['loop.body.anchorNode-end', 'inner-loop.body.anchorNode-end', 'body-return.anchorNode-start'] as $key) {
            $points[$side][$key] = $point($key);
        }
    }
    foreach ($points['left'] as $key => [$x, $y]) {
        expect($points['right'][$key])->toBe([-$x, $y]);
    }
});

it('loads saved mixed-side WHILE examples lazily and restores them from deep reference', function () {
    \Livewire\Livewire::test(\Gunreip\TranslationWorkbench\Livewire\TwGraphDocumentation::class)
        ->call('openExample', 'flow.while.flow-while-mixed')
        ->assertSet('tabs.flow_while', 'flow-while-mixed')
        ->assertSee('id="idea-to-paper-while-mixed-left"', false)
        ->assertSee('id="idea-to-paper-while-mixed-right"', false)
        ->assertDontSee('id="flow-while-test-proposals"', false)
        ->call('openReference', 'strang.flow-while', 'flow.while.flow-while-mixed')
        ->call('returnToExample')
        ->assertSet('tabs.flow_while', 'flow-while-mixed')
        ->set('tabs.flow_while', 'flow-while-test')
        ->assertSee('id="flow-while-test-proposals"', false)
        ->assertDontSee('.../flow/while/flow-while-mixed.blade.php', false);
});

it('finalizes a group after inner FALSE and returns only after advancing the outer index', function ($view, $graph, $root) {
    $html = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.while.'.$view)->render();
    $point = function ($suffix) use ($graph, $root) {
        $anchor = AnchorRegistry::get($graph, $root.'.'.$suffix);
        expect($anchor)->not->toBeNull();

        return array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($anchor[$axis]), ['x', 'y']);
    };
    expect($point('inner-entry.anchorNode-start'))->toBe($point('loop.body.anchorNode-end'));
    expect($point('inner-entry.anchorNode-end'))->toBe($point('inner-loop.anchorNode-start'));
    expect($point('inner-return.anchorNode-end'))->toBe($point('inner-loop.anchorNode-start'));
    $final = $point('finalize.anchorNode-end');
    $exit = $point('inner-loop.anchorNode-end');
    expect($final[0])->toBe($exit[0]);
    expect($final[1])->toBeGreaterThan($exit[1]);
    expect($point('body-return.anchorNode-start')[1])->toBe($final[1]);
    expect($point('body-return.anchorNode-end'))->toBe($point('loop.anchorNode-start'));
    expect($html)->toContain('Prepare group', 'Finalize group', 'groupIndex = groupIndex + 1');
    $source = file_get_contents(app('view')->getFinder()->find('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.while.'.$view));
    expect($source)->toContain('id="'.$root.'.finalize"' . "\n" . '                        attach-to="'.$root.'.inner-loop.anchorNode-end"');
})->with([
    ['flow-while-test', 'idea-to-paper-while-basic', 'literature.while.1'],
    ['flow-while-action-sequence', 'idea-to-paper-while-action-sequence-left', 'literature.while.action-sequence.left'],
    ['flow-while-action-sequence', 'idea-to-paper-while-action-sequence-right', 'literature.while.action-sequence.right'],
]);

it('loads saved action-sequence WHILE examples lazily and restores them from deep reference', function () {
    \Livewire\Livewire::test(\Gunreip\TranslationWorkbench\Livewire\TwGraphDocumentation::class)
        ->call('openExample', 'flow.while.flow-while-action-sequence')
        ->assertSet('tabs.flow_while', 'flow-while-action-sequence')
        ->assertSee('id="idea-to-paper-while-action-sequence-left"', false)
        ->assertSee('id="idea-to-paper-while-action-sequence-right"', false)
        ->assertDontSee('id="flow-while-test-proposals"', false)
        ->call('openReference', 'strang.flow-while', 'flow.while.flow-while-action-sequence')
        ->call('returnToExample')
        ->assertSet('tabs.flow_while', 'flow-while-action-sequence')
        ->set('tabs.flow_while', 'flow-while-test')
        ->assertSee('id="flow-while-test-proposals"', false)
        ->assertDontSee('id="idea-to-paper-while-action-sequence-right"', false);
});
