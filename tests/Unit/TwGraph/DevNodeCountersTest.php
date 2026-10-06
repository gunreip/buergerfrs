<?php

use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

uses(TestCase::class);

function devCounterLabels(string $html): array
{
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    return array_map(fn ($node) => trim($node->textContent), iterator_to_array($xpath->query('//*[contains(concat(" ", normalize-space(@class), " "), " tw-graph-protocol-primitive-dev-node-counter ")]')));
}

it('uses canvas-local automatic numbering without allocating suppressed counters', function () {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="first" :dev="true" :dev-counter-auto="true">
            <x-translation-workbench::ui.tw-graph.primitives.dev-node-counter counter="99" />
            <x-translation-workbench::ui.tw-graph.primitives.dev-node-counter :counter="false" />
            <x-translation-workbench::ui.tw-graph.primitives.dev-node-counter counter="17" :visible="false" />
            <x-translation-workbench::ui.tw-graph graph-id="nested" :dev="true" :dev-counter-auto="true">
                <x-translation-workbench::ui.tw-graph.primitives.dev-node-counter counter="E" />
            </x-translation-workbench::ui.tw-graph>
            <x-translation-workbench::ui.tw-graph.primitives.dev-node-counter counter="E" />
        </x-translation-workbench::ui.tw-graph>
        <x-translation-workbench::ui.tw-graph graph-id="manual" :dev="true">
            <x-translation-workbench::ui.tw-graph.primitives.dev-node-counter counter="27" />
            <x-translation-workbench::ui.tw-graph.primitives.dev-node-counter counter="E" />
        </x-translation-workbench::ui.tw-graph>
    BLADE);
    expect(devCounterLabels($html))->toBe(['1', '1', '2', '27', 'E']);
});

it('numbers all overview partials consecutively and restarts on a fresh render', function () {
    foreach ([1, 2] as $render) {
        $html = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.overview.overview-structure')->render();
        $labels = devCounterLabels($html);
        expect(count($labels))->toBeGreaterThan(70);
        expect($labels)->toBe(array_map('strval', range(1, count($labels))));
    }
});
