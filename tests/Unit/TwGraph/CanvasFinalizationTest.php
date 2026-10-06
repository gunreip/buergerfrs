<?php

use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

uses(TestCase::class);

it('keeps each bounds model and its diagnostics inside its own canvas when flushing markup', function () {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="outer-flush" :dev="true" :coordinates="true">
            <x-translation-workbench::ui.tw-graph.primitives.line id="outer-line" length="4rem" />
            <x-translation-workbench::ui.tw-graph graph-id="inner-flush" :dev="true" :coordinates="true">
                <x-translation-workbench::ui.tw-graph.primitives.line id="inner-line" length="8rem" />
            </x-translation-workbench::ui.tw-graph>
        </x-translation-workbench::ui.tw-graph>
        <div id="after-canvases">After</div>
    BLADE);
    $dom = new DOMDocument;
    $previous = libxml_use_internal_errors(true);
    try {
        $dom->loadHTML('<?xml encoding="UTF-8">'.$html);
    } finally {
        libxml_clear_errors();
        libxml_use_internal_errors($previous);
    }
    $xpath = new DOMXPath($dom);
    $models = $xpath->query('//script[@data-tw-graph-bounds-records]');
    expect($models->length)->toBe(2);
    foreach ($models as $model) {
        $graph = $xpath->query('ancestor::*[@data-tw-graph-bounds-model][1]', $model)->item(0);
        $id = $graph->getAttribute('id');
        $records = json_decode($model->textContent, true, flags: JSON_THROW_ON_ERROR);
        expect(array_column($records, 'id'))->toBe([$id === 'outer-flush' ? 'outer-line' : 'inner-line']);
        expect($model->parentNode->getAttribute('class'))->toContain('tw-graph-protocol-canvas-slot');
    }
    expect($xpath->query('//*[@id="after-canvases"]/ancestor::*[@data-tw-graph-bounds-model]')->length)->toBe(0);
});
