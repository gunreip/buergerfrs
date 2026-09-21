<?php

declare(strict_types=1);

use Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry;
use Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry;
use Tests\TestCase;

uses(TestCase::class);

it('keeps the saved grouped nested switch examples handmade and connected on both sides', function (int $variant, int $outerSign, string $mixedSuffix): void {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.switch-case.flow-switch-case-nested-'.$variant;
    $source = file_get_contents(app('view')->getFinder()->find($view));
    expect($source)->not->toContain('@foreach', '@include');
    expect(substr_count($source, '<x-translation-workbench::ui.tw-graph.strang.flow-switch-case'))->toBe(4);
    $html = view($view)->render();
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    expect($xpath->query('//pre/code')->length)->toBe(8);
    foreach (['' => $outerSign, $mixedSuffix => -$outerSign] as $suffix => $sign) {
        $graph = 'idea-to-paper-flow-switch-case-nested-'.$variant.$suffix;
        $canvas = $xpath->query('//*[@id="'.$graph.'"]')->item(0);
        expect($canvas)->not->toBeNull();
        expect($canvas->textContent)->toContain('SWITCH ($status)', 'SWITCH ($format)', 'Continue process');
        expect($canvas->textContent)->not->toContain(':cases=', '=>', '{--');
        $get = fn ($key) => AnchorRegistry::get($graph, 'literature.switch.1.grouped-nested-'.$variant.$suffix.'.'.$key);
        $number = fn ($value) => BoundsRegistry::evaluateRemExpression($value);
        foreach (['x', 'y'] as $axis) {
            expect($number($get('inner.anchorNode-start')[$axis]))->toBe($number($get('outer.case.editable.anchorNode-end')[$axis]));
            expect($number($get('inner-return.stem.anchorNode-end')[$axis]))->toBe($number($get('outer.case.editable.anchorNode-return')[$axis]));
        }
        expect($get('outer.case.editable.return.anchorNode-end'))->toBeNull();
        expect($get('outer.case.editable.fusion.anchorNode-end'))->not->toBeNull();
        expect($sign * ($number($get('inner.anchorNode-end')['x']) - $number($get('inner.anchorNode-start')['x'])))->toBeGreaterThan(0);
        expect($number($get('outer.case.editable.anchorNode-return')['y']) - $number($get('inner-return.anchorNode-end')['y']))->toBeGreaterThanOrEqual(0);
        expect($outerSign * ($number($get('inner.anchorNode-end')['x']) - $number($get('outer.anchorNode-start')['x'])))->toBeGreaterThan(0);
        expect($get('outer.anchorNode-end')['returnColor'])->toBe('amber');
    }
})->with([[1, -1, '-inner-right'], [2, 1, '-inner-left']]);

it('opens the saved grouped nested switch through lazy navigation and its deep reference', function (int $variant, string $mixedSuffix) {
    \Livewire\Livewire::test(\Gunreip\TranslationWorkbench\Livewire\TwGraphDocumentation::class)
        ->call('openExample', 'flow.switch-case.flow-switch-case-nested-'.$variant)
        ->assertSet('tabs.flow_switch_case', 'flow-switch-case-nested-'.$variant)
        ->assertSee('id="idea-to-paper-flow-switch-case-nested-'.$variant.$mixedSuffix.'"', false)
        ->assertDontSee('id="idea-to-paper-flow-switch-case-test"', false)
        ->call('openReference', 'strang.flow-switch-case', 'flow.switch-case.flow-switch-case-nested-'.$variant)
        ->assertSee('Back to example')
        ->call('returnToExample')
        ->assertSet('tabs.flow_switch_case', 'flow-switch-case-nested-'.$variant);
})->with([[1, '-inner-right'], [2, '-inner-left']]);
