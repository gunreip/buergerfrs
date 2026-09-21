<?php

use Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry;
use Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry;
use Tests\TestCase;

uses(TestCase::class);

it('routes every inner outcome through the final action before returning to its outer case', function (string $leaf, string $rootId) {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.switch-case.'.$leaf;
    $source = file_get_contents(app('view')->getFinder()->find($view));
    $html = view($view)->render();
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    expect($xpath->query('//pre/code')->length)->toBe(8);
    foreach (['', '-right'] as $suffix) {
        $graph = 'idea-to-paper-'.$leaf.$suffix;
        $get = fn ($key) => AnchorRegistry::get($graph, 'literature.switch.1.'.$rootId.$suffix.'.'.$key);
        $number = fn ($value) => BoundsRegistry::evaluateRemExpression($value);
        foreach ([
            ['outer.case.editable.anchorNode-end', 'inner.anchorNode-start'],
            ['inner-return.stem.anchorNode-end', 'outer.case.editable.anchorNode-return'],
        ] as [$from, $to]) {
            expect($get($from))->not->toBeNull();
            expect($get($to))->not->toBeNull();
            foreach (['x', 'y'] as $axis) {
                expect($number($get($from)[$axis]))->toBe($number($get($to)[$axis]));
            }
        }
        expect($source)->toContain('attach-to="literature.switch.1.'.$rootId.$suffix.'.inner.anchorNode-end"');
        expect($source)->toContain("'literature.switch.1.".$rootId.$suffix.".finish.anchorNode-end'");
        expect($number($get('finish.anchorNode-end')['x']))->toBe($number($get('inner.anchorNode-end')['x']));
        expect($number($get('inner-return.anchorNode-end')['y']))->toBeGreaterThan($number($get('finish.anchorNode-end')['y']));
        expect($get('outer.case.editable.return.anchorNode-end'))->toBeNull();
        expect($number($get('finish.anchorNode-end')['y']))->toBeGreaterThan($number($get('inner.anchorNode-end')['y']));
        expect($number($get('outer.case.editable.anchorNode-return')['y']))->toBeGreaterThanOrEqual($number($get('inner-return.anchorNode-end')['y']));
        expect($get('outer.anchorNode-end')['returnColor'])->toBe('rose');
        $canvas = $xpath->query('//*[@id="'.$graph.'"]')->item(0);
        expect($canvas->textContent)->toContain('Prepare editing', 'SWITCH ($format)', 'Save editing result', 'Display article', 'Continue process');
        expect($canvas->textContent)->not->toContain(':cases=', '=>', '{--');
    }
})->with([
    ['flow-switch-case-test', 'action-sequence'],
    ['flow-switch-case-action-sequence', 'action-sequence-saved'],
]);
