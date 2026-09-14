<?php

use Gunreip\TranslationWorkbench\Livewire\TwGraphDocumentation;
use Illuminate\Support\Facades\View;
use Livewire\Livewire;
use Tests\TestCase;

uses(TestCase::class);

it('renders only the default canvas and defers the result graphs', function () {
    $rendered = [];
    View::composer('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.*', function ($view) use (&$rendered) {
        $rendered[] = $view->name();
    });

    Livewire::test(TwGraphDocumentation::class)
        ->assertSee('idea-to-paper-canvas-default', false)
        ->assertDontSee('tw-graph-sample-idea-to-paper-thought-draft', false);

    expect($rendered)->toHaveCount(3);
    expect(implode(' ', $rendered))->toContain('canvas.canvas-default')->not->toContain('flow.index');
});

it('loads a nested leaf and removes the previous example when switching tabs', function () {
    $component = Livewire::test(TwGraphDocumentation::class)
        ->set('tabs.main', 'idea-to-paper-flow')
        ->set('tabs.flow_index', 'flow-if')
        ->set('tabs.flow_flow_if', 'flow-if-nested-test')
        ->assertSee('literature.flow', false)
        ->assertSee('data-nested-flow-tools', false)
        ->assertDontSee('graph-id="idea-to-paper-canvas-default"', false);

    $component->set('tabs.main', 'idea-to-paper-canvas')
        ->assertDontSee('data-nested-flow-tools', false)
        ->set('tabs.main', 'idea-to-paper-flow')
        ->assertSee('data-nested-flow-tools', false);
});

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

it('loads result graphs only on selection and unloads them again', function () {
    Livewire::test(TwGraphDocumentation::class)
        ->assertDontSee('tw-graph-sample-idea-to-paper-thought-draft', false)
        ->set('tabs.results', 'idea-to-paper-draft')
        ->assertSee('tw-graph-sample-idea-to-paper-thought-draft', false)
        ->assertDontSee('tw-graph-sample-idea-to-paper-current-result', false)
        ->set('tabs.results', 'idea-to-paper-result')
        ->assertDontSee('tw-graph-sample-idea-to-paper-thought-draft', false)
        ->assertSee('tw-graph-sample-idea-to-paper-current-result', false)
        ->set('tabs.results', 'idea-to-paper-results-idle')
        ->assertDontSee('tw-graph-sample-idea-to-paper-current-result', false);
});
