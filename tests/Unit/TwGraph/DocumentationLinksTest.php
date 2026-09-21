<?php

use Gunreip\TranslationWorkbench\Livewire\TwGraphDocumentation;
use Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\DocumentationLinks;
use Illuminate\Support\Facades\View;
use Livewire\Livewire;
use Tests\TestCase;

uses(TestCase::class);

it('maps every destination to existing views and allowed tab selections', function () {
    $allowed = (new ReflectionClass(TwGraphDocumentation::class))->getConstant('TABS');
    foreach (DocumentationLinks::EXAMPLES as $key => $example) {
        expect(View::exists($example['view']))->toBeTrue($key);
        foreach ($example['tabs'] as $group => $tab) {
            expect($allowed[$group])->toContain($tab);
        }
        foreach ($example['components'] as $component) {
            expect(DocumentationLinks::reference($component))->not->toBeNull();
        }
    }
    foreach (DocumentationLinks::COMPONENTS as $component) {
        foreach (DocumentationLinks::reference($component) as $group => $tab) {
            expect($allowed[$group])->toContain($tab);
        }
    }
});

it('returns from a reference to the exact example and preserves other selections', function () {
    Livewire::test(TwGraphDocumentation::class)
        ->set('tabs.canvas_index', 'canvas-height')
        ->call('openExample', 'flow.switch-case.flow-switch-case-grouped-3')
        ->assertSee('openReference(', false)
        ->call('openReference', 'strang.flow-switch-case', 'flow.switch-case.flow-switch-case-grouped-3')
        ->assertSet('tabs.main', 'idea-to-paper-props-and-connections')
        ->assertSet('tabs.reference_strang', 'reference-flow-switch-case')
        ->assertSee('Back to example')
        ->assertSee('openExample(', false)
        ->assertDontSee('href="http://localhost/livewire/update', false)
        ->call('returnToExample')
        ->assertSet('tabs.main', 'idea-to-paper-flow')
        ->assertSet('tabs.flow_index', 'flow-switch-case')
        ->assertSet('tabs.flow_switch_case', 'flow-switch-case-grouped-3')
        ->assertSet('tabs.canvas_index', 'canvas-height');
});

it('opens parts previews and ignores unknown navigation destinations', function () {
    $component = Livewire::test(TwGraphDocumentation::class)
        ->call('openReference', 'parts.fusion')
        ->assertSet('tabs.reference_parts', 'reference-parts-fusion')
        ->call('openExample', 'parts.parts-fusion')
        ->assertSet('tabs.main', 'idea-to-paper-parts')
        ->assertSet('tabs.parts_index', 'idea-to-paper-parts-fusion')
        ->assertSee('id="parts-fusion-left-2"', false);
    $tabs = $component->get('tabs');
    $component->call('openExample', '../../unknown')
        ->call('openReference', 'unknown')
        ->assertSet('tabs', $tabs);
});

it('does not invent previews or return links for unrelated components', function () {
    expect(DocumentationLinks::examples('strang.rekey-target-left'))->toBe([]);
    Livewire::test(TwGraphDocumentation::class)
        ->call('openReference', 'strang.rekey-target-left', 'parts.parts-fusion')
        ->assertSet('referenceOrigin', null)
        ->assertSee('No preview for this component in these documentation tabs yet.');
});


it('opens the saved mixed switch examples and returns to them from deep reference', function () {
    Livewire::test(TwGraphDocumentation::class)
        ->call('openExample', 'flow.switch-case.flow-switch-case-two-nested-3')
        ->assertSet('tabs.flow_switch_case', 'flow-switch-case-two-nested-3')
        ->assertSee('id="idea-to-paper-flow-switch-case-two-nested-3-right"', false)
        ->assertDontSee('id="idea-to-paper-flow-switch-case-test"', false)
        ->call('openReference', 'strang.flow-switch-case', 'flow.switch-case.flow-switch-case-two-nested-3')
        ->assertSee('Back to example')
        ->call('returnToExample')
        ->assertSet('tabs.flow_switch_case', 'flow-switch-case-two-nested-3');
});


it('opens the saved action sequence and restores it from its reference', function () {
    Livewire::test(TwGraphDocumentation::class)
        ->call('openExample', 'flow.switch-case.flow-switch-case-action-sequence')
        ->assertSet('tabs.flow_switch_case', 'flow-switch-case-action-sequence')
        ->assertSee('id="idea-to-paper-flow-switch-case-action-sequence-right"', false)
        ->assertDontSee('id="idea-to-paper-flow-switch-case-test"', false)
        ->call('openReference', 'strang.flow-switch-case', 'flow.switch-case.flow-switch-case-action-sequence')
        ->assertSee('Back to example')
        ->call('returnToExample')
        ->assertSet('tabs.flow_switch_case', 'flow-switch-case-action-sequence');
});
