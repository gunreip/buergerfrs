<?php

use Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry;
use Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry;
use Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\OverviewEntryLayout;
use Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\OverviewLayoutOverrides;
use Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\OverviewStructure;
use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

uses(TestCase::class);

it('inherits level values while individual nested leaves take precedence without affecting siblings', function () {
    $result = OverviewLayoutOverrides::apply(OverviewStructure::data(), ['canvas.php' => [
        'canvasTabs' => [
            'levels' => ['subtabs' => ['bridgeLength' => '6rem', 'label' => ['afterLength' => '4rem']]],
            'children' => ['default' => [
                'bridgeLength' => '8rem', 'color' => 'fuchsia',
                'label' => ['beforeLength' => '3rem'],
                'endCap' => ['capLength' => '2rem'],
            ]],
        ],
    ]]);
    expect($result['issues'])->toBe([]);
    $tree = $result['data']['canvasTabs'];
    $layout = OverviewEntryLayout::group($tree, $tree['children']['default']);
    $sibling = OverviewEntryLayout::group($tree, $tree['children']['borders']);
    expect($layout['bridgeLength'])->toBe('8rem');
    expect($layout['label']['beforeLength'])->toBe('3rem');
    expect($layout['label']['afterLength'])->toBe('4rem');
    expect($layout['endCap'])->toBe(['length' => '2rem', 'capLength' => '2rem']);
    expect($sibling['bridgeLength'])->toBe('6rem');
    expect($sibling['color'])->toBe(''); // Inherit from the actual connection at render time.
    expect($tree['children']['borders'])->not->toHaveKey('bridgeLength');

    $structure = $result['data'];
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph :graph-id="$structure['canvas']['graphId']" :dev="true">
            @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.overview.structure.root')
            @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.overview.structure.main-tabs')
            @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.overview.structure.canvas')
        </x-translation-workbench::ui.tw-graph>
    BLADE, compact('structure'));
    preg_match('/<script type="application\/json" data-tw-graph-bounds-records>(.*?)<\/script>/s', $html, $matches);
    $records = collect(json_decode($matches[1], true));
    expect($records->firstWhere('id', 'literature.overview.canvas.tabs.default-branch.bridge1')['rects'][0]['width'])->toBe('8rem');
    expect($records->firstWhere('id', 'literature.overview.canvas.tabs.borders-branch.bridge1')['rects'][0]['width'])->toBe('6rem');
});

it('supports branch node defaults and individual leaf overrides in Canvas and Deep Reference', function () {
    $result = OverviewLayoutOverrides::apply(OverviewStructure::data(), ['canvas.php' => [
        'canvasTabs' => ['children' => ['props' => [
            'color' => 'green', 'nodes' => ['width' => 'default'],
            'children' => ['node-size' => ['color' => 'red', 'side' => 'left', 'align' => 'right']],
        ]]],
        'deepReference' => ['children' => ['strang' => ['children' => ['flow-while' => ['color' => 'fuchsia', 'width' => 'half']]]]],
    ]]);
    expect($result['issues'])->toBe([]);
    $tree = $result['data']['canvasTabs'];
    $entry = $tree['children']['props'];
    $parent = OverviewEntryLayout::group($tree, $entry);
    $node = OverviewEntryLayout::node($parent['nodes'], $parent, $entry['children']['node-size']);
    expect($node['color'])->toBe('red');
    expect($node['width'])->toBe('default');
    expect($node['side'])->toBe('left');
    expect(OverviewEntryLayout::node($parent['nodes'], $parent, $entry['children']['line'])['color'])->toBe('green');
    $reference = OverviewEntryLayout::group($result['data']['deepReference'], $result['data']['deepReference']['children']['strang']);
    $reference['children'] = $result['data']['deepReference']['children']['strang']['children'];
    expect(OverviewEntryLayout::node($reference['nodes'], $reference, $reference['children']['flow-while'])['color'])->toBe('fuchsia');
});

it('still rejects misspelled inherited keys and conflicts on optional individual values', function () {
    $base = OverviewStructure::data();
    $result = OverviewLayoutOverrides::apply($base, [
        'canvas.php' => ['canvasTabs' => ['children' => ['default' => ['bridgeLength' => '8rem', 'label' => ['beforeLenght' => '3rem']]]]],
        'other.php' => ['canvasTabs' => ['children' => ['default' => ['bridgeLength' => '9rem']]]],
    ]);
    expect($result['issues'])->toHaveCount(2);
    $tree = $result['data']['canvasTabs'];
    expect($tree['children']['default'])->not->toHaveKey('bridgeLength');
    expect(OverviewEntryLayout::group($tree, $tree['children']['default'])['bridgeLength'])->toBe('4rem');
});

it('renders the Canvas height branch on its individually selected destination side', function (string $side) {
    $result = OverviewLayoutOverrides::apply(OverviewStructure::data(), ['canvas.php' => [
        'canvasTabs' => ['children' => ['height' => ['side' => $side, 'bridgeLength' => '0rem']]],
    ]]);
    expect($result['issues'])->toBe([]);
    $structure = $result['data'];
    Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph :graph-id="$structure['canvas']['graphId']">
            @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.overview.structure.root')
            @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.overview.structure.main-tabs')
            @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.overview.structure.canvas')
        </x-translation-workbench::ui.tw-graph>
    BLADE, compact('structure'));
    $graph = $structure['canvas']['graphId'];
    $id = 'literature.overview.canvas.tabs.height';
    $start = AnchorRegistry::get($graph, $id.'-stem.anchorNode-end');
    $end = AnchorRegistry::get($graph, $id.'-branch.anchorNode-end');
    $x = fn ($anchor) => BoundsRegistry::evaluateRemExpression($anchor['x']);
    expect($x($end) - $x($start))->toEqual($side === 'left' ? -5.5 : 5.5);
})->with(['left', 'right']);
