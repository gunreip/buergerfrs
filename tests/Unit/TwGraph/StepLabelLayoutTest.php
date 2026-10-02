<?php

use Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry;
use Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry;
use Gunreip\TranslationWorkbench\Support\TwGraph\StepLabelLayout;
use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

uses(TestCase::class);

it('sizes authored step lines consistently for geometry and rendering', function ($count, $direction) {
    $lines = array_map(fn ($n) => 'Authored line '.$n, range(1, $count));
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="step-lines" :dev="true">
            <x-translation-workbench::ui.tw-graph.strang.flow-step id="s" :direction="$direction"
                :anchor-start="['x' => '0rem', 'y' => '0rem']" :step-label="['text' => $lines]" />
        </x-translation-workbench::ui.tw-graph>
    BLADE, compact('lines', 'direction'));
    $gap = 3.25 + min(5, $count) - 1;
    $end = AnchorRegistry::get('step-lines', 's.anchorNode-end');
    $axis = in_array($direction, ['left-right', 'right-left']) ? 'x' : 'y';
    $sign = in_array($direction, ['right-left', 'top-bottom']) ? -1 : 1;
    expect(BoundsRegistry::evaluateRemExpression($end[$axis]))->toEqual($sign * (4 + $gap));
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xp = new DOMXPath($dom);
    expect($xp->query('//*[@data-tw-graph-path="s.label"]//*[contains(@class,"tw-graph-protocol-primitive-text-line")]')->length)->toBe($count);
    expect($xp->query('//*[@data-tw-graph-step-label-mismatch="s"]')->length)->toBe($count > 5 ? 1 : 0);
    $after = $xp->query('//*[@data-tw-graph-path="s.stem.after"]')->item(0);
    expect($after)->not->toBeNull();
    $bounds = json_decode($after->getAttribute('data-tw-graph-bounds'), true);
    $low = BoundsRegistry::evaluateRemExpression($bounds['rects'][0][$axis]);
    expect($low)->toEqual($sign === 1 ? 2 + $gap : -(4 + $gap));
    expect($html)->toContain('Authored line '.$count);
})->with([1, 2, 3, 4, 5, 6])->with(['bottom-top', 'top-bottom', 'left-right', 'right-left']);

it('keeps explicit gaps and custom offsets predictable', function () {
    $layout = StepLabelLayout::resolve(['text' => ['A', 'B'], 'offset' => '1rem']);
    expect(BoundsRegistry::evaluateRemExpression($layout['gap']))->toEqual(4.75);
    expect(StepLabelLayout::resolve(['text' => range(1, 6)], '9rem')['gap'])->toBe('9rem');
    expect(StepLabelLayout::resolve(['text' => ['A', '', 'B']])['count'])->toBe(2);
});

it('reports excess lines even with a manual gap and keeps the text when DEV is off', function ($dev) {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="manual-step" :dev="$dev">
            <x-translation-workbench::ui.tw-graph.strang.flow-step id="manual" label-gap="9rem"
                :step-label="['text' => ['A', 'B', 'C', 'D', 'E', 'Sixth line']]" />
        </x-translation-workbench::ui.tw-graph>
    BLADE, compact('dev'));
    expect($html)->toContain('Sixth line');
    expect(str_contains($html, 'data-tw-graph-step-label-mismatch="manual"'))->toBe($dev);
})->with([true, false]);

it('documents five lines and excess-line mismatches with source-backed individual examples', function () {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.segments.segments-step';
    $html = view($view)->render();
    $source = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView($view);
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xp = new DOMXPath($dom);
    foreach (['five-lines' => 5, 'six-lines' => 6, 'six-lines-manual' => 6] as $key => $count) {
        $id = 'literature.segments.step.'.$key;
        expect($source->example('segment-step-'.$key))->toContain("'id' => '".$id."'", 'segments.step');
        $canvas = '//*[@id="idea-to-paper-segments-step-'.$key.'"]';
        expect($xp->query($canvas.'//*[@data-tw-graph-step-label-mismatch="'.$id.'"]')->length)->toBe($count > 5 ? 1 : 0);
        expect($xp->query($canvas.'//*[@data-tw-graph-path="'.$id.'.label"]//*[contains(@class,"tw-graph-protocol-primitive-text-line")]')->length)->toBe($count);
    }
    expect($source->example('segment-step-six-lines-manual'))->toContain("'labelGap' => '9rem'");
    expect($source->example('segment-step-five-lines'))->not->toContain("'labelGap'");
    expect($html)->not->toContain('{-- segment-step-', 'data-tw-graph-layout-issues');
});

it('uses the canvas label offset consistently in flow-step anchors and segment geometry', function () {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="inherited-step-offset" label-gap="2rem">
            <x-translation-workbench::ui.tw-graph.strang.flow-step id="inherited"
                :anchor-start="['x' => '0rem', 'y' => '0rem']"
                :step-label="['text' => ['One', 'Two']]" />
        </x-translation-workbench::ui.tw-graph>
    BLADE);
    $end = AnchorRegistry::get('inherited-step-offset', 'inherited.anchorNode-end');
    expect(BoundsRegistry::evaluateRemExpression($end['y']))->toEqual(10.75);
    expect($html)->toContain('calc(2.75rem + (2rem * 2))', '--tw-graph-protocol-text-label-offset: 2rem');
});
