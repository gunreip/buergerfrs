<?php

use Gunreip\TranslationWorkbench\Support\TwGraph\CalculatedLengths;
use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

uses(TestCase::class);

it('uses explicit provenance instead of treating every CSS calculation as calculated', function () {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph :dev="true" graph-id="calculated-test">
            <x-translation-workbench::ui.tw-graph.primitives.line id="authored" length="calc(2rem + 2rem)" />
            <x-translation-workbench::ui.tw-graph.paths.loop-return id="route"
                :anchor-start="['x' => '-20rem', 'y' => '10rem', 'source' => 'previous.branch']"
                :anchor-return="['x' => '0rem', 'y' => '2rem']" side="left" arc-radius="2rem" />
        </x-translation-workbench::ui.tw-graph>
    BLADE);
    expect($html)->toContain('data-tw-graph-calculated-marker="route.stem"', 'data-tw-graph-calculated-marker="route.bridge"', '8rem', '16rem', 'paths.loop-return')
        ->not->toContain('data-tw-graph-calculated-marker="authored"');
    preg_match('/data-tw-graph-calculated-marker="route.stem".*?data-copy-text="([^"]*)"/s', $html, $marker);
    expect(html_entity_decode($marker[1], ENT_QUOTES))->toBe(implode("\n", [
        'Vertical distance between the supplied anchors.',
        'paths.loop-return',
        'Length: Calculated',
        'ID: route',
        'Element: route.stem',
        'Property: remainingStemLength',
        'Source: previous.branch',
        'Result: 8rem',
    ]));
});

it('distinguishes authored WHILE lengths from defaults and calculated returns', function () {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph :dev="true" graph-id="while-lengths">
            <x-translation-workbench::ui.tw-graph.strang.flow-while id="loop"
                true-bridge-length="4rem"
                :condition-label="['text' => ['WHILE?'], 'afterLength' => '14rem']"
                :action-label="['text' => ['Action'], 'afterLength' => '6rem']" />
        </x-translation-workbench::ui.tw-graph>
    BLADE);
    foreach ([
        'loop.true.bridge' => ['prop', 'true-bridge-length', '4rem'],
        'loop.condition.stem.after' => ['prop', 'condition-label.afterLength', '14rem'],
        'loop.condition.stem.before' => ['default', 'condition-label.beforeLength', '2rem'],
        'loop.body.bridge.bridge-out' => ['prop', 'action-label.afterLength', '6rem'],
        'loop.body.bridge.bridge-in' => ['default', 'action-label.beforeLength', '4rem'],
    ] as $id => [$kind, $property, $result]) {
        expect($html)->toContain('data-tw-graph-calculated-marker="'.$id.'" data-tw-graph-length-kind="'.$kind.'"');
        preg_match('/data-tw-graph-calculated-marker="'.preg_quote($id, '/').'".*?data-copy-text="([^"]*)"/s', $html, $marker);
        expect(html_entity_decode($marker[1], ENT_QUOTES))->toContain('Property: '.$property, 'Result: '.$result);
    }
    expect($html)->toContain('data-tw-graph-calculated-marker="loop.return.stem" data-tw-graph-length-kind="calculated"');
});

it('keeps provenance and marker visibility scoped to their canvas', function () {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph :dev="false" graph-id="no-dev">
            <x-translation-workbench::ui.tw-graph.paths.loop-return id="route"
                :anchor-start="['x' => '-20rem', 'y' => '10rem']"
                :anchor-return="['x' => '0rem', 'y' => '2rem']" side="left" />
        </x-translation-workbench::ui.tw-graph>
        <x-translation-workbench::ui.tw-graph :dev="true" graph-id="next-canvas">
            <x-translation-workbench::ui.tw-graph.primitives.line id="route.stem" length="4rem" />
        </x-translation-workbench::ui.tw-graph>
    BLADE);
    expect($html)->not->toContain('data-tw-graph-calculated-marker=');
});

it('shows compact owner and element references in calculated IF markers', function () {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph :dev="true" graph-id="if-calc">
            <x-translation-workbench::ui.tw-graph.strang.flow-if-else id="decision"
                :if-start="['text' => ['TRUE'], 'return' => false]"
                :if-end="['return' => false, 'returnOffset' => '-6rem']" />
        </x-translation-workbench::ui.tw-graph>
    BLADE);
    preg_match('/data-tw-graph-calculated-marker="decision.false.bridge1".*?data-copy-text="([^"]*)"/s', $html, $marker);
    expect(html_entity_decode($marker[1], ENT_QUOTES))
        ->toContain('ID: decision', 'Element: decision.false.bridge1', 'Property: if-end / bridge-length', 'Result:')
        ->not->toContain('Inputs:', 'Formula:', 'Root-ID:', 'Source:');
});

it('records parts end authored and default lengths', function () {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph :dev="true">
            <x-translation-workbench::ui.tw-graph.parts.end id="authored-end" length="12.8rem" />
            <x-translation-workbench::ui.tw-graph.parts.end id="default-end" />
            <x-translation-workbench::ui.tw-graph.strang.flow-while id="default-loop" />
        </x-translation-workbench::ui.tw-graph>
    BLADE);
    expect($html)->toContain(
        'data-tw-graph-calculated-marker="authored-end" data-tw-graph-length-kind="prop"',
        'data-tw-graph-calculated-marker="default-end" data-tw-graph-length-kind="default"',
        'data-tw-graph-calculated-marker="default-loop.true.bridge" data-tw-graph-length-kind="default"',
    );
});

it('records step lengths and IF question ownership without losing inherited provenance', function () {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph :dev="true">
            <x-translation-workbench::ui.tw-graph.strang.flow-step id="cleanup" before-length="4rem" />
            <x-translation-workbench::ui.tw-graph.strang.flow-if-else id="selection" before-length="3rem" />
        </x-translation-workbench::ui.tw-graph>
    BLADE);
    foreach ([
        'cleanup.stem.before' => ['prop', 'cleanup', 'before-length', '4rem'],
        'cleanup.stem.after' => ['default', 'cleanup', 'after-length', '2rem'],
        'selection.question.stem.before' => ['prop', 'selection', 'before-length', '3rem'],
        'selection.question.stem.after' => ['default', 'selection', 'after-length', '2rem'],
        'selection.true.stem' => ['calculated', 'selection', 'stem-length / if-end.returnLength', '8rem'],
    ] as $id => [$kind, $owner, $property, $result]) {
        expect($html)->toContain('data-tw-graph-calculated-marker="'.$id.'" data-tw-graph-length-kind="'.$kind.'"');
        preg_match('/data-tw-graph-calculated-marker="'.preg_quote($id, '/').'".*?data-copy-text="([^"]*)"/s', $html, $marker);
        expect(html_entity_decode($marker[1], ENT_QUOTES))->toContain('ID: '.$owner, 'Property: '.$property, 'Result: '.$result);
    }
});

it('reports start and plain sideways length props without replacing supplied calculations', function () {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph :dev="true" graph-id="part-length-provenance">
            @php
                $__env->getConsumableComponentData('twGraphCalculatedLengths')->record(
                    'calculated-start', 'parts.start', 'calculated-start', 'length', [], 'Authored distance.');
                $__env->getConsumableComponentData('twGraphCalculatedLengths')->record(
                    'calculated-side.bridge1', 'parts.sideways', 'calculated-side', 'bridge-length', [], 'Authored distance.');
            @endphp
            <x-translation-workbench::ui.tw-graph.parts.start id="calculated-start" length="6rem" />
            <x-translation-workbench::ui.tw-graph.parts.sideways id="calculated-side" bridge-length="6rem" />
            <x-translation-workbench::ui.tw-graph.parts.start id="explicit-start" length="3rem" />
            <x-translation-workbench::ui.tw-graph.parts.start id="default-start" />
            <x-translation-workbench::ui.tw-graph.parts.sideways id="explicit-side" bridge-length="3rem" />
            <x-translation-workbench::ui.tw-graph.parts.sideways id="default-side" />
        </x-translation-workbench::ui.tw-graph>
    BLADE);
    foreach (['calculated-start' => 'calculated', 'calculated-side.bridge1' => 'calculated',
        'explicit-start' => 'prop', 'default-start' => 'default',
        'explicit-side.bridge1' => 'prop', 'default-side.bridge1' => 'default'] as $id => $kind) {
        expect($html)->toContain('data-tw-graph-calculated-marker="'.$id.'" data-tw-graph-length-kind="'.$kind.'"');
    }
});

it('tracks merge and extension lengths through public forwarding without converting defaults into props', function (string $side): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph :dev="true" graph-id="merge-lengths">
            <x-dynamic-component :component="'translation-workbench::ui.tw-graph.strang.merge-'.$side"
                id="merge" bridge-length="6rem" :stem-lengths="[1 => '4rem']"
                :extension-count="2" :extension-stem-lengths="[1 => '3rem']"
                :extension-stem-continuations="[1 => [1 => '2rem']]" />
        </x-translation-workbench::ui.tw-graph>
    BLADE, ['side' => $side]);
    foreach ([
        'merge.paths.merge.bridge' => 'prop',
        'merge.paths.merge.stem1' => 'prop',
        'merge.paths.merge.start' => 'default',
        'merge.extension.1.paths.merge-extension.stem1' => 'prop',
        'merge.extension.1.paths.merge-extension.stem2' => 'prop',
        'merge.extension.1.paths.merge-extension.bridge' => 'default',
        'merge.extension.2.paths.merge-extension.stem1' => 'default',
    ] as $id => $kind) {
        expect($html)->toContain('data-tw-graph-calculated-marker="'.$id.'" data-tw-graph-length-kind="'.$kind.'"');
    }
    expect($html)->toContain('Property: extension-stem-lengths.1', 'Property: extension-stem-continuations.1.1');
})->with(['left', 'right']);
