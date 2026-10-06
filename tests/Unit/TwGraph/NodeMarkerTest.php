<?php

use Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry;
use Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\OverviewLayoutOverrides;
use Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\OverviewStructure;
use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

uses(TestCase::class);

it('renders the two-switch marker contract while keeping labels and anchors', function ($visible, $dot, $label, $expected) {
    $labels = $label === null ? [] : ['right' => $label];
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="marker-contract">
            <x-translation-workbench::ui.tw-graph.strang.flow-step
                id="marker-test" :anchor-start="['x' => '0rem', 'y' => '0rem']"
                :node-end="$visible" :node-end-dot="$dot" :node-labels="$labels"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE, compact('visible', 'dot', 'labels'));
    $dom = new DOMDocument;
    @$dom->loadHTML('<?xml encoding="utf-8" ?>'.$html);
    $ids = [];
    foreach ((new DOMXPath($dom))->query('//*[@data-tw-graph-bounds]') as $node) {
        $ids[] = json_decode($node->getAttribute('data-tw-graph-bounds'), true)['id'];
    }
    expect(in_array('marker-test.stem.after.node.end', $ids, true))->toBe($expected === 'dot');
    expect(in_array('marker-test.stem.after.end.joint-arrow', $ids, true))->toBe($expected === 'arrow');
    expect(AnchorRegistry::get('marker-contract', 'marker-test.anchorNode-end'))->not->toBeNull();
    if ($label !== null) {
        expect($html)->toContain('Marker information', 'marker-test.stem.after.label.right.1.connector');
    }
})->with([
    'dot' => [true, true, null, 'dot'],
    'arrow' => [true, false, null, 'arrow'],
    'disabled dot' => [false, true, null, 'none'],
    'disabled arrow' => [false, false, null, 'none'],
    'label overrides disabled owner' => [false, false, ['text' => 'Marker information'], 'dot'],
    'label overrides arrow' => [true, false, ['text' => 'Marker information'], 'dot'],
    'label explicitly hides dot' => [true, true, ['text' => 'Marker information', 'nodeEnd' => false], 'none'],
    'label explicitly restores dot' => [false, false, ['text' => 'Marker information', 'nodeEnd' => true], 'dot'],
]);

it('keeps the label-local override separate from the owner and rejects the removed public flag', function () {
    $compiled = OverviewLayoutOverrides::scope('deep-reference.php', ['deep-reference' => [
        'global' => [], 'children' => ['global' => [], 'strang' => ['global' => [], 'children' => [
            'global' => ['nodeEnd' => false],
            'flow-start' => ['global' => ['label' => ['nodeEnd' => false]]],
        ]]],
    ]]);
    expect($compiled['issues'])->toBe([]);
    $resolved = OverviewLayoutOverrides::apply(OverviewStructure::data(), ['deep-reference.php' => $compiled['values']]);
    expect($resolved['issues'])->toBe([]);
    $nodes = $resolved['data']['deepReference']['children']['strang']['children'];
    expect($nodes['flow-start']['labelNodeEnd'])->toBeFalse();
    expect($nodes['flow-while'])->not->toHaveKey('labelNodeEnd');
    $bad = OverviewLayoutOverrides::scope('canvas.php', ['canvas' => ['global' => ['jointArrowEnd' => true]]]);
    expect(array_column($bad['issues'], 'path'))->toBe(['canvas.global.jointArrowEnd']);
});
