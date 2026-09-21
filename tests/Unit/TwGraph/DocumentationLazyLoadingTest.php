<?php

use Gunreip\TranslationWorkbench\Livewire\TwGraphDocumentation;
use Illuminate\Support\Facades\View;
use Livewire\Livewire;
use Tests\TestCase;

uses(TestCase::class);

it('renders only the default inventory and draft and defers the other result graphs', function () {
    $rendered = [];
    View::composer('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.*', function ($view) use (&$rendered) {
        $rendered[] = $view->name();
    });

    Livewire::test(TwGraphDocumentation::class)
        ->assertSee('Component chains from the current Blade source.')
        ->assertSee('tw-graph-sample-idea-to-paper-thought-draft', false)
        ->assertDontSee('tw-graph-sample-idea-to-paper-current-result', false)
        ->assertDontSee('tw-graph-sample-idea-to-paper-flow-diagram', false)
        ->assertDontSee('Select a graph');

    expect($rendered)->toHaveCount(3);
    expect(implode(' ', $rendered))->toContain('inventory.index')->not->toContain('canvas.canvas-default')->not->toContain('flow.index');
});

it('loads a nested leaf and removes the previous example when switching tabs', function () {
    $component = Livewire::test(TwGraphDocumentation::class)
        ->set('tabs.main', 'idea-to-paper-flow')
        ->set('tabs.flow_index', 'flow-if')
        ->set('tabs.flow_flow_if', 'flow-if-nested-9')
        ->assertSee('literature.flow', false)
        ->assertSee('literature.flow.1.if-nested-9.outer', false)
        ->assertDontSee('graph-id="idea-to-paper-canvas-default"', false);

    $component->set('tabs.main', 'idea-to-paper-canvas')
        ->assertDontSee('literature.flow.1.if-nested-9.outer', false)
        ->set('tabs.main', 'idea-to-paper-flow')
        ->assertSee('literature.flow.1.if-nested-9.outer', false);
});

it('loads the parts overview only when its tab is selected', function () {
    Livewire::test(TwGraphDocumentation::class)
        ->assertDontSee('Parts in the component chain')
        ->set('tabs.main', 'idea-to-paper-parts')
        ->assertSet('tabs.main', 'idea-to-paper-parts')
        ->assertSee('Parts in the component chain')
        ->assertSee('parts.sideways')
        ->assertSee('parts.chain')
        ->assertDontSee('graph-id="idea-to-paper-canvas-default"', false)
        ->set('tabs.main', 'idea-to-paper-canvas')
        ->assertDontSee('Parts in the component chain');
});

it('loads each authored parts example with code props and preview controls', function (string $part) {
    $component = Livewire::test(TwGraphDocumentation::class)
        ->set('tabs.main', 'idea-to-paper-parts')
        ->set('tabs.parts_index', 'idea-to-paper-parts-' . $part)
        ->assertSet('tabs.parts_index', 'idea-to-paper-parts-' . $part)
        ->assertSee('id="idea-to-paper-parts-' . $part . '-preview"', false)
        ->assertSee('data-tw-graph-preview-tools', false)
        ->assertSee('Default')
        ->assertSee('Purpose')
        ->assertSee('&lt;x-translation-workbench::ui.tw-graph', false)
        ->assertDontSee('&amp;gt;', false);

    foreach (array_diff(['start', 'end', 'sideways', 'chain'], [$part]) as $other) {
        $component->assertDontSee('id="idea-to-paper-parts-' . $other . '-preview"', false);
    }
})->with(['start', 'end', 'sideways', 'chain']);

it('restores the complete tab selection from the URL', function () {
    Livewire::withQueryParams(['graph-tabs' => [
        'main' => 'idea-to-paper-canvas',
        'canvas_index' => 'canvas-props',
        'canvas_canvas_props' => 'canvas-props-node-size',
    ]])->test(TwGraphDocumentation::class)
        ->assertSet('tabs.canvas_canvas_props', 'canvas-props-node-size')
        ->assertSee('Node size')
        ->assertDontSee('graph-id="idea-to-paper-canvas-default"', false);
});

it('rejects unknown tab values and groups without resolving client supplied view names', function () {
    Livewire::test(TwGraphDocumentation::class)
        ->set('tabs', ['main' => '../../.env', 'unknown' => 'secret', 'canvas_index' => ['invalid']])
        ->assertSet('tabs.main', 'idea-to-paper-inventory')
        ->assertSet('tabs.canvas_index', 'canvas-default')
        ->assertSee('Component chains from the current Blade source.')
        ->assertSet('tabs.unknown', null);
});

it('switches result graphs with preview controls and falls back from the former empty tab', function () {
    Livewire::test(TwGraphDocumentation::class)
        ->assertSet('tabs.results', 'idea-to-paper-draft')
        ->assertSee('tw-graph-sample-idea-to-paper-thought-draft', false)
        ->assertDontSee('tw-graph-sample-idea-to-paper-current-result', false)
        ->set('tabs.results', 'idea-to-paper-result')
        ->assertDontSee('tw-graph-sample-idea-to-paper-thought-draft', false)
        ->assertSee('tw-graph-sample-idea-to-paper-current-result', false)
        ->set('tabs.results', 'idea-to-paper-flow-result')
        ->assertDontSee('tw-graph-sample-idea-to-paper-current-result', false)
        ->assertSee('tw-graph-sample-idea-to-paper-flow-diagram', false)
        ->set('tabs.results', 'idea-to-paper-results-idle')
        ->assertSet('tabs.results', 'idea-to-paper-draft');
});

it('opens true false inside flow IF and drops the retired documentation tabs', function () {
    Livewire::test(TwGraphDocumentation::class)
        ->set('tabs.main', 'idea-to-paper-flow')
        ->set('tabs.flow_index', 'flow-if')
        ->set('tabs.flow_flow_if', 'flow-if-else')
        ->assertSet('tabs.flow_flow_if', 'flow-if-else')
        ->assertSee('id="idea-to-paper-step-08-flow-if-else"', false)
        ->assertSee('id="idea-to-paper-step-08-flow-if-else-right"', false)
        ->assertDontSee('name="flow-if-if"', false)
        ->assertDontSee('name="flow-if-endif"', false)
        ->assertDontSee('name="flow-if-else-endif"', false)
        ->assertDontSee('name="flow-if-elseif-endif"', false)
        ->set('tabs.flow_flow_if', 'flow-if-test')
        ->assertSet('tabs.flow_flow_if', 'flow-if-simple');

    Livewire::withQueryParams(['graph-tabs' => [
        'main' => 'idea-to-paper-flow',
        'flow_index' => 'flow-if-else',
    ]])->test(TwGraphDocumentation::class)
        ->assertSet('tabs.flow_index', 'flow-if')
        ->assertSet('tabs.flow_flow_if', 'flow-if-else');
});

it('loads the ternary examples only in their nested IF tab', function () {
    Livewire::test(TwGraphDocumentation::class)
        ->set('tabs.main', 'idea-to-paper-flow')
        ->set('tabs.flow_index', 'flow-if')
        ->assertDontSee('id="idea-to-paper-step-08-flow-if-ternary"', false)
        ->set('tabs.flow_flow_if', 'flow-if-ternary')
        ->assertSee('id="idea-to-paper-step-08-flow-if-ternary"', false)
        ->assertSee('id="idea-to-paper-step-08-flow-if-ternary-right"', false)
        ->assertSee('literature.flow.1.ternary-process.assignment', false)
        ->assertSee('literature.flow.1.ternary-process-right.assignment', false)
        ->assertSee('if-start')
        ->assertSee('if-end')
        ->assertDontSee('id="idea-to-paper-step-08-flow-if-else"', false);
});

it('opens simple IF as the first conditional example with both bypass previews', function () {
    Livewire::test(TwGraphDocumentation::class)
        ->set('tabs.main', 'idea-to-paper-flow')
        ->set('tabs.flow_index', 'flow-if')
        ->assertSet('tabs.flow_flow_if', 'flow-if-simple')
        ->assertSee('id="idea-to-paper-step-08-flow-if-simple"', false)
        ->assertSee('id="idea-to-paper-step-08-flow-if-simple-right"', false)
        ->assertSee('literature.flow.1.simple-if-process.decision-1.false.bridge1', false)
        ->assertSee('literature.flow.1.simple-if-process-right.decision-1.false.bridge1', false)
        ->assertDontSee('literature.flow.1.simple-if-process.decision-1.false.bridge1.label.center.1', false)
        ->assertDontSee('id="idea-to-paper-step-08-flow-if-else"', false);
});

it('loads IF ELSEIF with two decisions actions and a plain final bypass', function () {
    Livewire::test(TwGraphDocumentation::class)
        ->set('tabs.main', 'idea-to-paper-flow')
        ->set('tabs.flow_index', 'flow-if')
        ->assertDontSee('id="idea-to-paper-flow-if-elseif"', false)
        ->set('tabs.flow_flow_if', 'flow-if-elseif')
        ->assertSee('id="idea-to-paper-flow-if-elseif"', false)
        ->assertSee('id="idea-to-paper-flow-if-elseif-right"', false)
        ->assertSee('literature.flow.1.if-elseif.elseif.question.label', false)
        ->assertSee('literature.flow.1.if-elseif-right.elseif.question.label', false)
        ->assertSee('literature.flow.1.if-elseif.elseif.false.bridge1', false)
        ->assertDontSee('literature.flow.1.if-elseif.elseif.false.bridge1.label.center.1', false)
        ->assertSee('Publish paper')->assertSee('Revise draft')->assertSee('Continue process');
});

it('loads the authored multi ELSEIF examples only for their selected tab', function () {
    Livewire::test(TwGraphDocumentation::class)
        ->set('tabs.main', 'idea-to-paper-flow')
        ->set('tabs.flow_index', 'flow-if')
        ->assertDontSee('id="idea-to-paper-flow-if-elseif-multi"', false)
        ->set('tabs.flow_flow_if', 'flow-if-elseif-multi')
        ->assertSee('id="idea-to-paper-flow-if-elseif-multi"', false)
        ->assertSee('id="idea-to-paper-flow-if-elseif-multi-right"', false)
        ->assertSee('literature.flow.1.if-elseif-multi.elseif.sources.question.label', false)
        ->assertSee('literature.flow.1.if-elseif-multi-right.elseif.review.question.label', false)
        ->assertSee('Add sources')->assertSee('Request review')
        ->assertDontSee('id="idea-to-paper-flow-if-elseif"', false);
});


it('loads the saved nested example independently from the saved action sequence tab', function () {
    Livewire::test(TwGraphDocumentation::class)
        ->set('tabs.main', 'idea-to-paper-flow')
        ->set('tabs.flow_index', 'flow-if')
        ->set('tabs.flow_flow_if', 'flow-if-nested-1')
        ->assertSet('tabs.flow_flow_if', 'flow-if-nested-1')
        ->assertSee('literature.flow.1.if-nested-1.outer', false)
        ->assertDontSee('literature.flow.1.if-nested-9.outer', false)
        ->set('tabs.flow_flow_if', 'flow-if-nested-9')
        ->assertSee('literature.flow.1.if-nested-9.outer', false)
        ->assertDontSee('literature.flow.1.if-nested-1.outer', false);
});

it('loads saved later nested examples independently from the saved action sequence tab', function (string $number) {
    Livewire::test(TwGraphDocumentation::class)
        ->set('tabs.main', 'idea-to-paper-flow')
        ->set('tabs.flow_index', 'flow-if')
        ->set('tabs.flow_flow_if', 'flow-if-nested-' . $number)
        ->assertSet('tabs.flow_flow_if', 'flow-if-nested-' . $number)
        ->assertSee('literature.flow.1.if-nested-' . $number . '.outer', false)
        ->assertDontSee('literature.flow.1.if-nested-9.outer', false)
        ->set('tabs.flow_flow_if', 'flow-if-nested-9')
        ->assertSee('literature.flow.1.if-nested-9.outer', false)
        ->assertDontSee('literature.flow.1.if-nested-' . $number . '.outer', false);
})->with(['2', '3', '4', '5', '6', '7', '8']);


it('loads the four primitive line-jump drawings only in their own tab', function () {
    Livewire::test(TwGraphDocumentation::class)
        ->set('tabs.main', 'idea-to-paper-primitives')
        ->assertDontSee('data-line-jump-example="top"', false)
        ->set('tabs.primitives_index', 'idea-to-paper-primitives-line-jump')
        ->assertSee('data-line-jump-example="top"', false)
        ->assertSee('data-line-jump-example="right"', false)
        ->set('tabs.primitives_index', 'idea-to-paper-primitives-line')
        ->assertDontSee('data-line-jump-example="top"', false);
});

it('keeps the experimental nested tab inactive even when requested through URL state', function () {
    Livewire::test(TwGraphDocumentation::class)
        ->set('tabs.main', 'idea-to-paper-flow')
        ->set('tabs.flow_index', 'flow-if')
        ->set('tabs.flow_flow_if', 'flow-if-nested-test')
        ->assertSet('tabs.flow_flow_if', 'flow-if-simple')
        ->assertDontSee('Flow IF nested Test')
        ->assertDontSee('literature.flow.1.if-nested-test.outer', false);
});

it('loads saved switch examples only when the switch case subtab is active', function () {
    Livewire::test(TwGraphDocumentation::class)
        ->set('tabs.main', 'idea-to-paper-flow')
        ->assertDontSee('One SWITCH expression selects a CASE.')
        ->set('tabs.flow_index', 'flow-switch-case')
        ->assertSet('tabs.flow_switch_case', 'flow-switch-case-default')
        ->assertSee('id="idea-to-paper-flow-switch-case-default-right"', false)
        ->assertSee('Language examples')
        ->assertSee('CASEs single')
        ->set('tabs.flow_switch_case', 'flow-switch-case-grouped')
        ->assertSee('id="idea-to-paper-flow-switch-case-grouped"', false)
        ->assertSee('literature.switch.1.grouped.status', false)
        ->assertSee('Language examples')
        ->assertDontSee('id="idea-to-paper-flow-switch-case-test"', false)
        ->set('tabs.flow_switch_case', 'flow-switch-case-grouped-3')
        ->assertSee('id="idea-to-paper-flow-switch-case-grouped-3-right"', false)
        ->assertDontSee('id="idea-to-paper-flow-switch-case-grouped"', false)
        ->set('tabs.flow_switch_case', 'flow-switch-case-grouped-multi')
        ->assertSee('id="idea-to-paper-flow-switch-case-grouped-multi-right"', false)
        ->assertSee('CASE reopened')
        ->assertDontSee('id="idea-to-paper-flow-switch-case-grouped-3-right"', false)
        ->set('tabs.flow_switch_case', 'flow-switch-case-nested')
        ->assertSee('id="idea-to-paper-flow-switch-case-nested-right"', false)
        ->assertSee('SWITCH ($format)')
        ->assertDontSee('id="idea-to-paper-flow-switch-case-test"', false)
        ->set('tabs.flow_switch_case', 'flow-switch-case-without-default')
        ->assertSee('id="idea-to-paper-flow-switch-case-without-default-right"', false)
        ->assertDontSee('id="idea-to-paper-flow-switch-case-test"', false)
        ->assertDontSee('id="idea-to-paper-flow-switch-case-nested-right"', false)
        ->set('tabs.flow_switch_case', 'flow-switch-case-fallthrough')
        ->assertSee('id="idea-to-paper-flow-switch-case-fallthrough-right"', false)
        ->assertDontSee('id="idea-to-paper-flow-switch-case-test"', false)
        ->assertDontSee('id="idea-to-paper-flow-switch-case-nested-right"', false)
        ->set('tabs.flow_switch_case', 'flow-switch-case-action-sequence')
        ->assertDontSee('id="idea-to-paper-flow-switch-case-fallthrough-right"', false)
        ->assertDontSee('id="idea-to-paper-flow-switch-case-without-default-right"', false)
        ->assertDontSee('id="idea-to-paper-flow-switch-case-nested-right"', false)
        ->assertDontSee('id="idea-to-paper-flow-switch-case-grouped-multi-right"', false)
        ->assertDontSee('id="idea-to-paper-flow-switch-case-grouped-3-right"', false)
        ->assertDontSee('id="idea-to-paper-flow-switch-case-grouped"', false)
        ->assertDontSee('id="idea-to-paper-flow-switch-case-default-right"', false)
        ->assertSee('Language examples')
        ->assertSee('id="idea-to-paper-flow-switch-case-action-sequence"', false)
        ->assertDontSee('SWITCH/CASE Test')
        ->set('tabs.flow_index', 'flow-if')
        ->assertDontSee('id="idea-to-paper-flow-switch-case-action-sequence"', false);
});

it('lazy loads the handmade fusion documentation for each component level', function (string $level): void {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.'.$level.'.'.$level.'-fusion';
    $source = file_get_contents(app('view')->getFinder()->find($view));
    expect($source)->not->toContain('@foreach', '@include');
    expect(substr_count($source, '<x-translation-workbench::ui.tw-graph.'.$level.'.fusion'))->toBe(6);
    Livewire::test(TwGraphDocumentation::class)
        ->set('tabs.main', 'idea-to-paper-'.$level)
        ->assertDontSee('The configured arc-radius is a starting value.')
        ->set('tabs.'.$level.'_index', 'idea-to-paper-'.$level.'-fusion')
        ->assertSee('The configured arc-radius is a starting value.')
        ->assertSee('literature.'.$level.'.fusion', false)
        ->assertSee('wire:click="$refresh"', false)
        ->set('tabs.main', 'idea-to-paper-canvas')
        ->assertDontSee('The configured arc-radius is a starting value.');
    $html = view($view)->render();
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    expect($xpath->query('//pre/code')->length)->toBe(1);
    expect($xpath->query('//*[contains(@class, "tw-graph-protocol-canvas-slot")]')->length)->toBe(6);
})->with(['segments', 'parts']);


it('filters inventory chains and opens only known component sources', function () {
    Livewire::test(TwGraphDocumentation::class)
        ->set('inventoryRoot', 'strang.flow-if')
        ->assertSee('flow-if')
        ->set('inventorySource', 'strang.flow-if')
        ->assertSee('Close source')
        ->set('inventorySource', '../../.env')
        ->assertDontSee('Close source')
        ->set('inventoryPage', 2)
        ->set('inventoryArchive', true)
        ->assertSet('inventoryPage', 1)
        ->assertSet('inventoryRoot', '')
        ->assertSee('strang._old.if-else-endif');
});

it('loads the component reference separately from example tables and restores its navigation', function () {
    $component = Livewire::test(TwGraphDocumentation::class)
        ->assertDontSee('Reading this reference')
        ->set('tabs.main', 'idea-to-paper-props-and-connections')
        ->assertSee('Reading this reference')
        ->assertSee('cases[].entries[].label')
        ->assertSee('cases[].actionLabel.bridgeOutLength')
        ->assertSee('case-default.exitLabel')
        ->assertSee('{id}.case.{key}.anchorNode-return')
        ->assertDontSee('id="idea-to-paper-flow-switch-case-test"', false)
        ->set('tabs.reference_index', 'reference-parts')
        ->assertSee('parts.start — Deep Reference')
        ->assertDontSee('strang.flow-switch-case — Deep Reference')
        ->set('tabs.reference_parts', 'reference-parts-fusion')
        ->assertSee('parts.fusion — Deep Reference')
        ->assertDontSee('parts.start — Deep Reference')
        ->set('tabs.reference_index', '../../.env')
        ->assertSet('tabs.reference_index', 'reference-strang')
        ->assertSee('Reading this reference')
        ->set('tabs.main', 'idea-to-paper-canvas')
        ->assertDontSee('Reading this reference');

    Livewire::withQueryParams(['graph-tabs' => [
        'main' => 'idea-to-paper-props-and-connections',
        'reference_index' => 'reference-strang',
        'reference_strang' => 'reference-flow-switch-case',
    ]])->test(TwGraphDocumentation::class)
        ->assertSee('Reading this reference');
});

it('nests array reference fields under their owning arrays with complete paths', function () {
    $html = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.props-and-connections.strang.flow-switch-case')->render();
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);

    foreach ([
        'anchor-start.x' => 1,
        'cases[].label.align' => 2,
        'cases[].entries[].label.connectorLength' => 3,
        'cases[].actionLabel.lineJumps[].over' => 3,
        'cases[].exitLabel.badgeColor' => 2,
        'case-default.width' => 1,
        'case-default.label.maxLines' => 2,
        'case-default.exitLabel.align' => 2,
        'case-default.lineJumps[].radius' => 2,
    ] as $path => $depth) {
        $cells = $xpath->query('//td[normalize-space(.)="'.$path.'"]');
        expect($cells->length)->toBe(1, $path);
        expect($xpath->query('ancestor::*[@data-flux-accordion-item]', $cells->item(0))->length)->toBe($depth, $path);
    }
    expect($dom->textContent)->not->toContain('Replace label below', 'case-default.text / width');
});


it('hides the retained switch test page and falls back from its old tab selection', function () {
    Livewire::test(TwGraphDocumentation::class)
        ->set('tabs.main', 'idea-to-paper-flow')
        ->set('tabs.flow_index', 'flow-switch-case')
        ->set('tabs.flow_switch_case', 'flow-switch-case-test')
        ->assertSet('tabs.flow_switch_case', 'flow-switch-case-default')
        ->assertSee('id="idea-to-paper-flow-switch-case-default-right"', false)
        ->assertDontSee('SWITCH/CASE Test')
        ->assertDontSee('id="idea-to-paper-flow-switch-case-test"', false);
    expect(\Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\DocumentationLinks::EXAMPLES)
        ->not->toHaveKey('flow.switch-case.flow-switch-case-test');
    expect(View::exists('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.switch-case.flow-switch-case-test'))->toBeTrue();
});

it('loads the while proposal page lazily and normalizes unknown while tabs', function () {
    $rendered = [];
    View::composer('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.while.*', function ($view) use (&$rendered) {
        $rendered[] = $view->name();
    });
    $component = Livewire::test(TwGraphDocumentation::class)
        ->assertDontSee('id="flow-while-test-proposals"', false);
    expect($rendered)->toBe([]);
    $component->set('tabs.main', 'idea-to-paper-flow')
        ->set('tabs.flow_index', 'flow-while')
        ->assertSet('tabs.flow_while', 'flow-while-test')
        ->assertSee('id="flow-while-test-proposals"', false)
        ->assertSee('WHILE basic')
        ->assertSee('Two independent inner loops')
        ->assertDontSee('id="idea-to-paper-flow-switch-case-default"', false)
        ->set('tabs.flow_while', 'unknown-while-tab')
        ->assertSet('tabs.flow_while', 'flow-while-test')
        ->set('tabs.flow_index', 'flow-switch-case')
        ->assertDontSee('id="flow-while-test-proposals"', false);
    expect($rendered)->not->toBe([]);
});
