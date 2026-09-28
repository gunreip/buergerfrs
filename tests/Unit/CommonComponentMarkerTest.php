<?php

use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

uses(TestCase::class);

it('marks shared separators while leaving direct Flux separators unmarked', function () {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.common.separator-deep-reference-links />
        <x-translation-workbench::ui.common.separator-code-example-tw-graph />
        <x-translation-workbench::ui.common.separator-props-used-tw-graph />
        <flux:separator text="Direct separator" />
        BLADE);
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $markers = (new DOMXPath($dom))->query('//*[@data-ui-component-marker]');
    expect($markers->length)->toBe(3);
    foreach ($markers as $marker) {
        expect($marker->getAttribute('style'))->toBe('display: none;');
        expect($marker->getAttribute('data-ui-component-marker'))->toStartWith('translation-workbench::ui.common.separator-');
        expect($marker->parentNode->getAttribute('class'))->toContain('relative');
    }
});

it('keeps the marker outside the copyable code example', function () {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph.code-box>Example code</x-translation-workbench::ui.tw-graph.code-box>
        BLADE);
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    expect($xpath->query('//*[@data-ui-component-marker]')->length)->toBe(1);
    expect($xpath->query('//pre/code')->item(0)->textContent)->toBe('Example code');
    expect($xpath->query('//pre//*[@data-ui-component-marker]')->length)->toBe(0);
});
