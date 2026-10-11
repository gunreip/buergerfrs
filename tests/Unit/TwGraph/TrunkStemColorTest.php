<?php

use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

uses(TestCase::class);

it('forwards per-stem colors through the trunk chain including compressed pieces and nodes', function () {
    $html = Blade::render(<<<'BLADE'
    <x-translation-workbench::ui.tw-graph color="green">
        <x-translation-workbench::ui.tw-graph.strang.trunk
            id="color.trunk"
            :stem-count="3"
            :stem-lengths="[
                1 => ['length' => '4rem', 'color' => 'fuchsia', 'labels' => [['text' => 'Group']]],
                2 => ['length' => '8rem', 'component' => 'stem-compressed', 'color' => 'violet', 'labels' => [['text' => 'Compressed']]],
                3 => ['length' => '4rem', 'labels' => [['text' => 'Default']]],
            ]"
        />
    </x-translation-workbench::ui.tw-graph>
    BLADE);
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $found = [];
    foreach ((new DOMXPath($dom))->query('//*[@data-tw-graph-bounds]') as $element) {
        $bounds = json_decode($element->getAttribute('data-tw-graph-bounds'), true);
        $id = $bounds['id'];
        if ($bounds['kind'] !== 'geometry' || ! preg_match('/color\.trunk\.paths\.trunk\.path\.([123])(?:\.|$)/', $id, $match)
            || str_contains($id, 'label')) continue;
        $rgb = ['1' => '217 70 239', '2' => '139 92 246', '3' => '44 144 103'][$match[1]];
        expect($element->getAttribute('style'))->toContain('--tw-graph-protocol-local-color-rgb: '.$rgb);
        $found[] = $id;
    }
    expect($found)->toContain('color.trunk.paths.trunk.path.1', 'color.trunk.paths.trunk.path.1.node.end',
        'color.trunk.paths.trunk.path.2.stem.before', 'color.trunk.paths.trunk.path.2.stem.dotted',
        'color.trunk.paths.trunk.path.2.stem.after', 'color.trunk.paths.trunk.path.3');
});

it('limits the color gradient to the dotted middle and gives the end node the destination color', function (string $direction) {
    $html = Blade::render(<<<'BLADE'
    <x-translation-workbench::ui.tw-graph>
        <x-translation-workbench::ui.tw-graph.segments.stem-compressed
            :segment="['id' => 'transition', 'direction' => $direction,
                'color' => 'green', 'toColor' => 'violet', 'colorGradient' => true,
                'nodeEnd' => [['text' => 'Next event']]]"
        />
    </x-translation-workbench::ui.tw-graph>
    BLADE, compact('direction'));
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $pieces = [];
    foreach ((new DOMXPath($dom))->query('//*[@data-tw-graph-bounds]') as $element) {
        $bounds = json_decode($element->getAttribute('data-tw-graph-bounds'), true);
        $pieces[$bounds['id']] = $element;
    }
    foreach (['before' => '44 144 103', 'dotted' => '44 144 103', 'after' => '139 92 246'] as $part => $rgb) {
        $element = $pieces["transition.stem.$part"];
        expect($element->getAttribute('style'))->toContain('--tw-graph-protocol-local-color-rgb: '.$rgb);
        expect(str_contains($element->getAttribute('class'), 'tw-graph-protocol-primitive-line-color-gradient'))->toBe($part === 'dotted');
    }
    expect($pieces['transition.stem.dotted']->getAttribute('class'))->toContain('tw-graph-protocol-primitive-line-dashed')
        ->and($pieces['transition.stem.dotted']->getAttribute('style'))->toContain('--tw-graph-protocol-local-to-color-rgb: 139 92 246')
        ->and($pieces['transition.stem.after.node.end']->getAttribute('style'))->toContain('--tw-graph-protocol-local-color-rgb: 139 92 246');
})->with(['bottom-top', 'top-bottom', 'left-right', 'right-left']);
