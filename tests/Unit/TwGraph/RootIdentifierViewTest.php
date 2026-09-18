<?php

declare(strict_types=1);

use Gunreip\TranslationWorkbench\Support\TwGraph\RootIdentifier;
use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

uses(TestCase::class);

it('preserves the verbatim authoring ID across composed strangs without changing copied identifiers', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="root-test" :dev="true">
            <x-translation-workbench::ui.tw-graph.strang.flow-if-elseif-multi
                id="example.flow\.1.outer"
                :elseifs="[['key' => 'next', 'conditionLabel' => ['text' => ['Next?']], 'actionLabel' => ['text' => ['Next action']]]]"
            />
            <x-translation-workbench::ui.tw-graph.strang.flow-if id="example.flow\.1.inner" />
            <x-translation-workbench::ui.tw-graph.primitives.line id="standalone" />
        </x-translation-workbench::ui.tw-graph>
    BLADE);
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    foreach (['outer', 'inner'] as $name) {
        $nodes = $xpath->query('//*[@data-tw-graph-path and @title and starts-with(@data-tw-graph-path, "example.flow\\.1.' . $name . '.")]');
        expect($nodes->length)->toBeGreaterThan(10);
        foreach ($nodes as $node) {
            expect($node->getAttribute('title'))->toBe($node->getAttribute('data-tw-graph-path') . "\nRoot-ID: example.flow\\.1." . $name);
            if ($node->hasAttribute('x-on:click.stop')) {
                expect($node->getAttribute('x-on:click.stop'))->toContain('$el.dataset.twGraphPath');
            }
        }
    }
    expect($xpath->query('//*[@data-tw-graph-path="standalone"]')->item(0)->getAttribute('title'))->toBe('standalone');
    expect(RootIdentifier::tooltipSuffix())->toBe('');
});

it('restores provenance after a strang throws while rendering', function (): void {
    try {
        Blade::render(<<<'BLADE'
            <x-translation-workbench::ui.tw-graph.strang.flow-if-elseif-multi id="broken" :elseifs="[]" />
        BLADE);
        $this->fail('Expected invalid ELSEIF configuration to throw.');
    } catch (\Illuminate\View\ViewException $exception) {
        expect($exception->getMessage())->toContain('elseif');
    }
    expect(RootIdentifier::tooltipSuffix())->toBe('');
});

it('uses separate roots for independently authored nested IF examples', function (): void {
    $html = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.if.flow-if-nested-test')->render();
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    foreach (['outer', 'inner'] as $name) {
        $prefix = 'literature.flow.1.if-nested-test.' . $name;
        $nodes = $xpath->query('//*[@title and @data-tw-graph-path and starts-with(@data-tw-graph-path, "' . $prefix . '.")]');
        expect($nodes->length)->toBeGreaterThan(10);
        foreach ($nodes as $node) {
            expect($node->getAttribute('title'))->toEndWith("\nRoot-ID: literature.flow.1.if-nested-test." . $name);
        }
    }
    expect(RootIdentifier::tooltipSuffix())->toBe('');
});
