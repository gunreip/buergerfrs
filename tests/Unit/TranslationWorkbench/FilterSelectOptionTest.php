<?php

use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

uses(TestCase::class);

it('renders each filter option icon once through the Flux icon slot', function () {
    $html = Blade::render('<flux:select variant="listbox"><x-ui.input.select-option value="all" icon="type">All types</x-ui.input.select-option></flux:select>');
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    expect($xpath->query('//*[@data-flux-option]//*[name()="svg" and contains(@class, "text-zinc-400")]')->length)->toBe(1);
});
