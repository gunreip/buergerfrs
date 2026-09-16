<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

uses(TestCase::class);

it('passes explicit jumps through stems and outgoing label bridges without changing anchors', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="line-jump-props" :dev="true">
            <x-translation-workbench::ui.tw-graph.parts.start
                id="stem" :gradient="false" length="8rem"
                :line-jumps="[['over' => 'bridge', 'side' => 'right'], ['over' => 'other', 'side' => 'left']]"
            />
            <x-translation-workbench::ui.tw-graph.parts.sideways
                id="action"
                :bridge-label="['text' => ['Action'], 'lineJumps' => [['over' => 'stem', 'side' => 'top']]]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    $lines = $xpath->query('//*[@data-tw-graph-line-jumps]');
    expect($lines->length)->toBe(2);
    $byId = [];
    foreach ($lines as $line) {
        $byId[$line->getAttribute('data-tw-graph-path')] = json_decode($line->getAttribute('data-tw-graph-line-jumps'), true, flags: JSON_THROW_ON_ERROR);
    }
    expect($byId['stem'])->toHaveCount(2);
    expect($byId['action.bridge1.bridge-out'][0]['over'])->toBe('stem');
    expect($html)->toContain('data-tw-graph-dev="true"');
    $anchor = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get('line-jump-props', 'stem.anchorNode-end');
    expect($anchor)->not->toBeNull();
});

it('configures the experimental nested crossing on the actual outgoing bridge', function (): void {
    $html = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.flow-if-nested-test', ['dev' => true, 'coordinates' => false])->render();
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    $line = $xpath->query('//*[@data-tw-graph-path="literature.flow.1.if-nested-test.outer.elseif.sources.true.bridge1.bridge-out"]')->item(0);
    expect($line)->not->toBeNull();
    $config = json_decode($line->getAttribute('data-tw-graph-line-jumps'), true, flags: JSON_THROW_ON_ERROR);
    expect($config)->toHaveCount(1);
    expect($config[0]['over'])->toBe('literature.flow.1.if-nested-test.outer.elseif.automatic.true.stem');
    expect($config[0]['side'])->toBe('top');
});
