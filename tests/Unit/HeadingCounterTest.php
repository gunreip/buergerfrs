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

it('pairs every handwritten Line code heading with its preview and resets on refresh', function () {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.primitives.primitives-line';
    foreach ([1, 2] as $render) {
        $entries = headingCounterEntries(view($view)->render());
        expect($entries)->toHaveCount(32);
        expect(array_slice($entries, 16))->toBe(array_slice($entries, 0, 16));
        expect(array_column(array_slice($entries, 0, 16), 2))->toBe(array_map(fn ($number) => '(#'.$number.')', range(1, 16)));
    }
});
