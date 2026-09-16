<?php

use Gunreip\TranslationWorkbench\Livewire\TwGraphDocumentation;
use Illuminate\Support\Facades\View;
use Livewire\Livewire;
use Tests\TestCase;

uses(TestCase::class);

it('renders only the default canvas and draft and defers the other result graphs', function () {
    $rendered = [];
    View::composer('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.*', function ($view) use (&$rendered) {
        $rendered[] = $view->name();
    });

    Livewire::test(TwGraphDocumentation::class)
        ->assertSee('idea-to-paper-canvas-default', false)
        ->assertSee('tw-graph-sample-idea-to-paper-thought-draft', false)
        ->assertDontSee('tw-graph-sample-idea-to-paper-current-result', false)
        ->assertDontSee('tw-graph-sample-idea-to-paper-flow-diagram', false)
        ->assertDontSee('Select a graph');

    expect($rendered)->toHaveCount(4);
    expect(implode(' ', $rendered))->toContain('canvas.canvas-default')->not->toContain('flow.index');
});

it('loads a nested leaf and removes the previous example when switching tabs', function () {
    $component = Livewire::test(TwGraphDocumentation::class)
        ->set('tabs.main', 'idea-to-paper-flow')
        ->set('tabs.flow_index', 'flow-if')
        ->set('tabs.flow_flow_if', 'flow-if-nested-test')
        ->assertSee('literature.flow', false)
        ->assertSee('literature.flow.1.if-nested-test.outer', false)
        ->assertDontSee('graph-id="idea-to-paper-canvas-default"', false);

    $component->set('tabs.main', 'idea-to-paper-canvas')
        ->assertDontSee('literature.flow.1.if-nested-test.outer', false)
        ->set('tabs.main', 'idea-to-paper-flow')
        ->assertSee('literature.flow.1.if-nested-test.outer', false);
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
        ->assertSet('tabs.main', 'idea-to-paper-canvas')
        ->assertSet('tabs.canvas_index', 'canvas-default')
        ->assertSee('idea-to-paper-canvas-default', false)
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


it('loads the saved nested example independently from the nested test tab', function () {
    Livewire::test(TwGraphDocumentation::class)
        ->set('tabs.main', 'idea-to-paper-flow')
        ->set('tabs.flow_index', 'flow-if')
        ->set('tabs.flow_flow_if', 'flow-if-nested-1')
        ->assertSet('tabs.flow_flow_if', 'flow-if-nested-1')
        ->assertSee('literature.flow.1.if-nested-1.outer', false)
        ->assertDontSee('literature.flow.1.if-nested-test.outer', false)
        ->set('tabs.flow_flow_if', 'flow-if-nested-test')
        ->assertSee('literature.flow.1.if-nested-test.outer', false)
        ->assertDontSee('literature.flow.1.if-nested-1.outer', false);
});

it('loads the second saved nested example independently from the nested test tab', function () {
    Livewire::test(TwGraphDocumentation::class)
        ->set('tabs.main', 'idea-to-paper-flow')
        ->set('tabs.flow_index', 'flow-if')
        ->set('tabs.flow_flow_if', 'flow-if-nested-2')
        ->assertSet('tabs.flow_flow_if', 'flow-if-nested-2')
        ->assertSee('literature.flow.1.if-nested-2.outer', false)
        ->assertDontSee('literature.flow.1.if-nested-test.outer', false)
        ->set('tabs.flow_flow_if', 'flow-if-nested-test')
        ->assertSee('literature.flow.1.if-nested-test.outer', false)
        ->assertDontSee('literature.flow.1.if-nested-2.outer', false);
});
