<?php

use Gunreip\TranslationWorkbench\Support\HeadingCounter;
use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

uses(TestCase::class);

function headingCounterEntries(string $html): array
{
    $dom = new DOMDocument;
    @$dom->loadHTML('<?xml encoding="UTF-8">'.$html);
    $entries = [];
    foreach ((new DOMXPath($dom))->query('//*[@data-heading-counter-example]') as $node) {
        $entries[] = [$node->getAttribute('data-heading-counter-group'), $node->getAttribute('data-heading-counter-example'), $node->textContent];
    }

    return $entries;
}

it('numbers first occurrences and reuses numbers regardless of preview order', function () {
    $counter = new HeadingCounter('lines');
    expect($counter->number('first'))->toBe(1);
    expect($counter->number('inserted'))->toBe(2);
    expect($counter->number('last'))->toBe(3);
    expect($counter->number('last'))->toBe(3);
    expect($counter->number('first'))->toBe(1);
    expect((new HeadingCounter('arcs'))->number('last'))->toBe(1);
});

it('keeps counters local to their group component including repeated renders', function () {
    $template = <<<'BLADE'
        <x-translation-workbench::ui.common.heading-counter-group group="outer">
            <x-translation-workbench::ui.common.heading-counter example="one">One</x-translation-workbench::ui.common.heading-counter>
            <x-translation-workbench::ui.common.heading-counter-group group="inner">
                <x-translation-workbench::ui.common.heading-counter example="two">Two</x-translation-workbench::ui.common.heading-counter>
            </x-translation-workbench::ui.common.heading-counter-group>
            <x-translation-workbench::ui.common.heading-counter example="two">Two</x-translation-workbench::ui.common.heading-counter>
            <x-translation-workbench::ui.common.heading-counter example="one">One preview</x-translation-workbench::ui.common.heading-counter>
        </x-translation-workbench::ui.common.heading-counter-group>
        <x-translation-workbench::ui.common.heading-counter-group group="outer">
            <x-translation-workbench::ui.common.heading-counter example="two">Separate instance</x-translation-workbench::ui.common.heading-counter>
        </x-translation-workbench::ui.common.heading-counter-group>
        BLADE;
    $expected = [
        ['outer', 'one', '(#1)'], ['inner', 'two', '(#1)'], ['outer', 'two', '(#2)'],
        ['outer', 'one', '(#1)'], ['outer', 'two', '(#1)'],
    ];
    expect(headingCounterEntries(Blade::render($template)))->toBe($expected);
    expect(headingCounterEntries(Blade::render($template)))->toBe($expected);
});

it('renders an optional escaped prefix and preserves Flux heading and accordion controls', function () {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.common.heading-counter-group group="lines">
            <flux:accordion>
                <flux:accordion.item>
                    <x-translation-workbench::ui.common.heading-counter example="first" variant="accordion" :prefix-text="$prefix">bottom-top</x-translation-workbench::ui.common.heading-counter>
                    <flux:accordion.content>Code</flux:accordion.content>
                </flux:accordion.item>
            </flux:accordion>
            <x-translation-workbench::ui.common.heading-counter example="first" class="px-3 pt-3" size="sm">bottom-top</x-translation-workbench::ui.common.heading-counter>
        </x-translation-workbench::ui.common.heading-counter-group>
        BLADE, ['prefix' => '<b>Complete example</b>']);
    expect($html)->toContain('&lt;b&gt;Complete example&lt;/b&gt;:', 'data-flux-accordion-heading', 'data-flux-heading', 'px-3 pt-3')
        ->not->toContain('<b>Complete example</b>');
    expect(headingCounterEntries($html))->toBe([['lines', 'first', '(#1)'], ['lines', 'first', '(#1)']]);
});

it('pairs every handwritten code heading with its preview and resets on refresh', function (string $primitive, int $count, string $section = 'primitives') {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.'.$section.'.'.(str_starts_with($section, 'flow') ? 'flow' : basename(str_replace('.', '/', $section))).'-'.$primitive;
    foreach ([1, 2] as $render) {
        $entries = headingCounterEntries(view($view)->render());
        expect($entries)->toHaveCount($count * 2);
        expect(array_slice($entries, $count))->toBe(array_slice($entries, 0, $count));
        expect(array_column(array_slice($entries, 0, $count), 2))->toBe(array_map(fn ($number) => '(#'.$number.')', range(1, $count)));
    }
})->with(['Line' => ['line', 16], 'Arc' => ['arc', 8], 'Line jump' => ['line-jump', 4], 'Text Label' => ['text-label', 7], 'Node' => ['node', 4], 'Joint Arrow' => ['joint-arrow', 8], 'Connector' => ['connector', 8], 'Segment Path' => ['path', 4, 'segments'], 'Segment Start + End' => ['start-end', 4, 'segments'], 'Segment Arc' => ['arc', 8, 'segments'], 'Segment Step' => ['step', 2, 'segments'], 'Segment Stem compressed' => ['stem-compressed', 2, 'segments'], 'Segment Fusion' => ['fusion', 6, 'segments'], 'Part Start' => ['start', 2, 'parts'], 'Part End' => ['end', 2, 'parts'], 'Part Sideways' => ['sideways', 2, 'parts'], 'Part Chain' => ['chain', 1, 'parts'], 'Part Fusion' => ['fusion', 6, 'parts'], 'Flow branch-steps' => ['branch-steps', 1, 'flow'], 'Flow start' => ['start', 1, 'flow'], 'Flow step' => ['step', 1, 'flow'], 'Flow decision' => ['decision', 1, 'flow.if'], 'Flow if-else' => ['if-else', 2, 'flow.if'], 'Flow if-elseif-multi' => ['if-elseif-multi', 2, 'flow.if'], 'Flow if-elseif' => ['if-elseif', 2, 'flow.if'], 'Flow if-nested-1' => ['if-nested-1', 2, 'flow.if'], 'Flow if-nested-2' => ['if-nested-2', 2, 'flow.if'], 'Flow if-nested-3' => ['if-nested-3', 2, 'flow.if'], 'Flow if-nested-4' => ['if-nested-4', 2, 'flow.if'], 'Flow if-nested-5' => ['if-nested-5', 2, 'flow.if'], 'Flow if-nested-6' => ['if-nested-6', 2, 'flow.if'], 'Flow if-nested-7' => ['if-nested-7', 2, 'flow.if'], 'Flow if-nested-8' => ['if-nested-8', 2, 'flow.if'], 'Flow if-nested-9' => ['if-nested-9', 2, 'flow.if'], 'Flow if-nested-test' => ['if-nested-test', 2, 'flow.if'], 'Flow if-simple' => ['if-simple', 2, 'flow.if'], 'Flow if-ternary' => ['if-ternary', 2, 'flow.if'], 'Flow switch-case-action-sequence' => ['switch-case-action-sequence', 2, 'flow.switch-case'], 'Flow switch-case-default' => ['switch-case-default', 2, 'flow.switch-case'], 'Flow switch-case-fallthrough' => ['switch-case-fallthrough', 2, 'flow.switch-case'], 'Flow switch-case-grouped-3' => ['switch-case-grouped-3', 2, 'flow.switch-case'], 'Flow switch-case-grouped-multi' => ['switch-case-grouped-multi', 2, 'flow.switch-case'], 'Flow switch-case-grouped' => ['switch-case-grouped', 2, 'flow.switch-case'], 'Flow switch-case-nested-1' => ['switch-case-nested-1', 2, 'flow.switch-case'], 'Flow switch-case-nested-2' => ['switch-case-nested-2', 2, 'flow.switch-case'], 'Flow switch-case-nested' => ['switch-case-nested', 2, 'flow.switch-case'], 'Flow switch-case-test' => ['switch-case-test', 2, 'flow.switch-case'], 'Flow switch-case-two-nested-1' => ['switch-case-two-nested-1', 2, 'flow.switch-case'], 'Flow switch-case-two-nested-2' => ['switch-case-two-nested-2', 2, 'flow.switch-case'], 'Flow switch-case-two-nested-3' => ['switch-case-two-nested-3', 2, 'flow.switch-case'], 'Flow switch-case-without-default' => ['switch-case-without-default', 2, 'flow.switch-case'], 'Flow while-action-sequence' => ['while-action-sequence', 2, 'flow.while'], 'Flow while-basic' => ['while-basic', 2, 'flow.while'], 'Flow while-if' => ['while-if', 2, 'flow.while'], 'Flow while-independent' => ['while-independent', 2, 'flow.while'], 'Flow while-mixed' => ['while-mixed', 2, 'flow.while'], 'Flow while-multiple-actions' => ['while-multiple-actions', 2, 'flow.while'], 'Flow while-nested' => ['while-nested', 2, 'flow.while'], 'Flow while-test' => ['while-test', 1, 'flow.while'], 'Strang Rekey default' => ['default', 1, 'strang.rekey'], 'Strang Rekey source' => ['source', 1, 'strang.rekey'], 'Strang Rekey target' => ['target', 1, 'strang.rekey'], 'Strang Rekey compressed' => ['compressed', 1, 'strang.rekey'], 'Strang Branch default' => ['default', 1, 'strang.branch'], 'Strang Branch offset' => ['offset', 1, 'strang.branch'], 'Strang Branch step' => ['step', 1, 'strang.branch'], 'Strang Branch return' => ['return', 1, 'strang.branch'], 'Strang Branch continuation' => ['continuation', 1, 'strang.branch'], 'Strang Branch mismatch' => ['mismatch', 1, 'strang.branch'], 'Strang Merge default' => ['default', 1, 'strang.merge'], 'Strang Merge start' => ['start', 1, 'strang.merge'], 'Strang Merge extension' => ['extension', 1, 'strang.merge'], 'Strang Merge aggregated' => ['aggregated', 1, 'strang.merge'], 'Strang Merge mismatch' => ['mismatch', 1, 'strang.merge'], 'Strang Trunk default' => ['default', 1, 'strang.trunk'], 'Strang Trunk direction' => ['direction', 1, 'strang.trunk'], 'Strang Trunk end-cap' => ['end-cap', 1, 'strang.trunk'], 'Strang Trunk end-color' => ['end-color', 1, 'strang.trunk'], 'Strang Trunk end-default' => ['end-default', 1, 'strang.trunk'], 'Strang Trunk end-long-end' => ['end-long-end', 1, 'strang.trunk'], 'Strang Trunk end-overview' => ['end-overview', 2, 'strang.trunk'], 'Strang Trunk end-wide-label' => ['end-wide-label', 1, 'strang.trunk'], 'Strang Trunk start-colors' => ['start-colors', 1, 'strang.trunk'], 'Strang Trunk start-compare' => ['start-compare', 2, 'strang.trunk'], 'Strang Trunk start-default' => ['start-default', 1, 'strang.trunk'], 'Strang Trunk start-long-start' => ['start-long-start', 1, 'strang.trunk'], 'Strang Trunk start-overview' => ['start-overview', 2, 'strang.trunk'], 'Strang Trunk start-shift' => ['start-shift', 2, 'strang.trunk'], 'Strang Trunk start-spacing' => ['start-spacing', 1, 'strang.trunk'], 'Strang Trunk start-wide-labels' => ['start-wide-labels', 1, 'strang.trunk'], 'Strang Trunk stem-count' => ['stem-count', 1, 'strang.trunk'], 'Strang Trunk stem-lengths' => ['stem-lengths', 1, 'strang.trunk'], 'Path stem-detour' => ['stem-detour', 4, 'paths'], 'Path branch-return-bridge' => ['branch-return-bridge', 2, 'paths'], 'Path branch-return-extension' => ['branch-return-extension', 2, 'paths'], 'Path branch-return' => ['branch-return', 2, 'paths'], 'Path branch-extension' => ['branch-extension', 2, 'paths'], 'Path branch' => ['branch', 2, 'paths'], 'Path merge-extension' => ['merge-extension', 2, 'paths'], 'Path merge' => ['merge', 2, 'paths'], 'Path trunk' => ['trunk', 2, 'paths']]);
