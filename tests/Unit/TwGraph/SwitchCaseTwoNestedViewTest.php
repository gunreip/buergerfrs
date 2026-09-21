<?php

use Gunreip\TranslationWorkbench\Livewire\TwGraphDocumentation;
use Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry;
use Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry;
use Livewire\Livewire;
use Tests\TestCase;

uses(TestCase::class);

it('keeps two independent nested returns connected when only the published switch is mirrored', function (int $variant, int $outerSign) {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.switch-case.flow-switch-case-two-nested-'.$variant;
    $source = file_get_contents(app('view')->getFinder()->find($view));
    expect($source)->not->toContain('@foreach', '@include');
    expect(substr_count($source, '<x-translation-workbench::ui.tw-graph.strang.flow-switch-case'))->toBe(6);
    $dom = new DOMDocument;
    @$dom->loadHTML(view($view)->render());
    $xpath = new DOMXPath($dom);
    expect($xpath->query('//pre/code')->length)->toBe(8);
    foreach (['' => -$outerSign, '-mixed' => $outerSign] as $suffix => $publishedSign) {
        $graph = 'idea-to-paper-flow-switch-case-two-nested-'.$variant.$suffix;
        $canvas = $xpath->query('//*[@id="'.$graph.'"]')->item(0);
        expect($canvas)->not->toBeNull();
        expect($canvas->textContent)->toContain('SWITCH ($status)', 'SWITCH ($format)', 'SWITCH ($channel)');
        expect($canvas->textContent)->not->toContain(':cases=', '=>', '{--');
        $get = fn ($key) => AnchorRegistry::get($graph, 'literature.switch.1.two-nested-'.$variant.$suffix.'.'.$key);
        $number = fn ($value) => BoundsRegistry::evaluateRemExpression($value);
        foreach (['editable' => ['inner', -$outerSign], 'published' => ['inner-published', $publishedSign]] as $case => [$inner, $sign]) {
            foreach (['x', 'y'] as $axis) {
                expect($number($get($inner.'.anchorNode-start')[$axis]))->toBe($number($get('outer.case.'.$case.'.anchorNode-end')[$axis]));
                expect($number($get($inner.'-return.stem.anchorNode-end')[$axis]))->toBe($number($get('outer.case.'.$case.'.anchorNode-return')[$axis]));
            }
            expect($get('outer.case.'.$case.'.return.anchorNode-end'))->toBeNull();
            expect($sign * ($number($get($inner.'.anchorNode-end')['x']) - $number($get($inner.'.anchorNode-start')['x'])))->toBeGreaterThan(0);
            expect($outerSign * ($number($get($inner.'.anchorNode-end')['x']) - $number($get('outer.anchorNode-start')['x'])))->toBeGreaterThan(0);
            expect($number($get('outer.case.'.$case.'.anchorNode-return')['y']))->toBeGreaterThanOrEqual($number($get($inner.'-return.anchorNode-end')['y']));
        }
        expect($number($get('outer.case.published.anchorNode-return')['y']))->toBeGreaterThan($number($get('outer.case.editable.anchorNode-return')['y']));
        expect($get('outer.anchorNode-end')['returnColor'])->toBe('green');
    }
})->with([[1, -1], [2, 1]]);

it('opens the two nested examples lazily and returns from their reference', function (int $variant) {
    Livewire::test(TwGraphDocumentation::class)
        ->call('openExample', 'flow.switch-case.flow-switch-case-two-nested-'.$variant)
        ->assertSet('tabs.flow_switch_case', 'flow-switch-case-two-nested-'.$variant)
        ->assertSee('id="idea-to-paper-flow-switch-case-two-nested-'.$variant.'-mixed"', false)
        ->assertDontSee('id="idea-to-paper-flow-switch-case-test"', false)
        ->call('openReference', 'strang.flow-switch-case', 'flow.switch-case.flow-switch-case-two-nested-'.$variant)
        ->assertSee('Back to example')
        ->call('returnToExample')
        ->assertSet('tabs.flow_switch_case', 'flow-switch-case-two-nested-'.$variant);
})->with([1, 2]);
