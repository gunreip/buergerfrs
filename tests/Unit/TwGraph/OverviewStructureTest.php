<?php

use Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry;
use Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry;
use Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\OverviewLayoutOverrides;
use Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\OverviewStructure;
use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

uses(TestCase::class);

it('renders authored overview data changes through existing components with stable IDs', function () {
    $structure = OverviewStructure::data();
    $structure['deepReference']['children']['parts']['children']['end']['stemLength'] = '8rem';
    $structure['deepReference']['children']['parts']['color'] = 'fuchsia';
    $structure['deepReference']['children']['parts']['nodes']['side'] = 'left';
    $structure['deepReference']['children']['parts']['nodes']['align'] = 'right';
    $structure['deepReference']['children']['parts']['children']['extra'] = ['text' => 'Additional entry', 'stemLength' => '5rem'];

    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph :graph-id="$structure['canvas']['graphId']" :dev="true">
            @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.overview.structure.root')
            @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.overview.structure.main-tabs')
            @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.overview.structure.deep-reference')
        </x-translation-workbench::ui.tw-graph>
    BLADE, compact('structure'));

    $graphId = $structure['canvas']['graphId'];
    $prefix = 'literature.overview.deep-reference.parts.';
    $position = fn ($key) => AnchorRegistry::get($graphId, $prefix.$key.'.anchorNode-end');
    $start = $position('start');
    $end = $position('end');
    expect(BoundsRegistry::evaluateRemExpression($end['y']))
        ->toEqual(BoundsRegistry::evaluateRemExpression($start['y']) - 8);
    expect($end['color'])->toBe('fuchsia');
    $split = $position('split');
    $extra = $position('extra');
    expect(BoundsRegistry::evaluateRemExpression($extra['y']))
        ->toEqual(BoundsRegistry::evaluateRemExpression($split['y']) - 5);
    expect($extra['x'])->toBe($split['x']);
    expect($html)->toContain('Additional entry', $prefix.'extra.stem.after.label.left.2.connector', $prefix.'end-cap');

    preg_match('/<script type="application\/json" data-tw-graph-bounds-records>(.*?)<\/script>/s', $html, $matches);
    $records = collect(json_decode($matches[1], true));
    $cap = $records->firstWhere('id', $prefix.'end-cap');
    expect(BoundsRegistry::evaluateRemExpression($cap['rects'][0]['y']))
        ->toEqual(BoundsRegistry::evaluateRemExpression($extra['y']) - 2);
});

it('maps the actual Canvas tabs and props tabs into separate connected overview levels', function () {
    $structure = OverviewStructure::data();
    $base = base_path('packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/canvas/');
    foreach (['subtabs' => 'index', 'props' => 'canvas-props'] as $group => $file) {
        preg_match_all('/<flux:tab name="([^"]+)">\{\{ __\(\'([^\']+)\'\) \}\}<\/flux:tab>/', file_get_contents($base.$file.'.blade.php'), $matches);
        $entries = $group === 'subtabs' ? $structure['canvasTabs']['children'] : $structure['canvasTabs']['children']['props']['children'];
        expect(array_keys($entries))->toBe(array_map(fn ($key) => preg_replace('/^canvas-(?:props-)?/', '', $key), $matches[1]));
        expect(array_column($entries, 'text'))->toBe(array_map('__', $matches[2]));
    }
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph :graph-id="$structure['canvas']['graphId']">
            @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.overview.structure.root')
            @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.overview.structure.main-tabs')
            @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.overview.structure.canvas')
        </x-translation-workbench::ui.tw-graph>
    BLADE, compact('structure'));
    $graph = $structure['canvas']['graphId'];
    $parent = AnchorRegistry::get($graph, 'literature.overview.canvas.props.anchorNode-end');
    $child = AnchorRegistry::get($graph, 'literature.overview.canvas.props.line.anchorNode-end');
    expect($parent)->not->toBeNull();
    expect($child)->not->toBeNull();
    expect(BoundsRegistry::evaluateRemExpression($child['x']))->toEqual(BoundsRegistry::evaluateRemExpression($parent['x']));
    expect(BoundsRegistry::evaluateRemExpression($child['y']))->toEqual(BoundsRegistry::evaluateRemExpression($parent['y']) - 3);
    expect($html)->toContain('literature.overview.canvas.props.label');
    expect($html)->not->toContain('literature.overview.canvas.tabs.props.stem.after.label');
    $heading = AnchorRegistry::get($graph, 'literature.overview.canvas.props.anchorNode-end');
    expect($child['x'])->toEqual($heading['x']);
    expect(BoundsRegistry::evaluateRemExpression($child['y']))->toEqual(BoundsRegistry::evaluateRemExpression($heading['y']) - 3);
    expect($html)->toContain('literature.overview.canvas.props.end-cap');
    foreach ($structure['canvasTabs']['children'] as $entry) {
        expect($html)->toContain($entry['id'].'.label', $entry['id'].'.end-cap');
        expect($html)->not->toContain($entry['id'].'.stem.after.label.left');
    }
});

it('maps the real Primitives hierarchy and applies individual layout and color overrides', function () {
    $structure = OverviewStructure::data();
    $directory = base_path('packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/primitives/');
    foreach (['index', 'primitives-markers-connectors'] as $file) {
        preg_match_all('/<flux:tab name="([^"]+)">\{\{ __\(\'([^\']+)\'\) \}\}<\/flux:tab>/', file_get_contents($directory.$file.'.blade.php'), $matches);
        $entries = $file === 'index' ? $structure['primitivesTabs']['children'] : $structure['primitivesTabs']['children']['markers-connectors']['children'];
        expect(array_keys($entries))->toBe(array_map(fn ($key) => preg_replace('/^(?:idea-to-paper-)?primitives-(?:markers-(?!connectors$))?/', '', $key), $matches[1]));
        expect(array_column($entries, 'text'))->toBe(array_map('__', $matches[2]));
    }
    $result = OverviewLayoutOverrides::apply($structure, ['primitives.php' => [
        'primitivesTabs' => ['children' => [
            'line' => ['bridgeLength' => '7rem', 'color' => 'violet', 'label' => ['beforeLength' => '3rem']],
            'markers-connectors' => ['children' => ['joint-arrow' => ['stemLength' => '5rem', 'color' => 'cyan']]],
        ]],
    ]]);
    expect($result['issues'])->toBe([]);
    expect($result['data']['canvasTabs'])->toBe($structure['canvasTabs']);
    $structure = $result['data'];
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph :graph-id="$structure['canvas']['graphId']">
            @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.overview.structure.root')
            @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.overview.structure.main-tabs')
            @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.overview.structure.primitives')
        </x-translation-workbench::ui.tw-graph>
    BLADE, compact('structure'));
    $graph = $structure['canvas']['graphId'];
    $node = fn ($id) => AnchorRegistry::get($graph, 'literature.overview.primitives.'.$id.'.anchorNode-end');
    expect($node('tabs.arc')['color'])->toBe('violet');
    $markers = $node('markers-connectors');
    $first = $node('markers-connectors.node');
    expect($first['x'])->toBe($markers['x']);
    expect(BoundsRegistry::evaluateRemExpression($first['y']))->toEqual(BoundsRegistry::evaluateRemExpression($markers['y']) - 3);
    $joint = $node('markers-connectors.joint-arrow');
    expect(BoundsRegistry::evaluateRemExpression($joint['y']))->toEqual(BoundsRegistry::evaluateRemExpression($first['y']) - 5);
    expect($node('markers-connectors.connector')['color'])->toBe('cyan');
    expect($html)->toContain('structure/primitives.blade.php', 'data/primitives.php');
    foreach ($structure['primitivesTabs']['children'] as $entry) {
        expect($html)->toContain($entry['id'].'.label', $entry['id'].'.end-cap');
    }
    preg_match('/data-tw-graph-bounds-records>(.*?)<\/script>/s', $html, $matches);
    $record = collect(json_decode($matches[1], true))->firstWhere('id', 'literature.overview.primitives.tabs.line-branch.bridge1');
    expect(BoundsRegistry::evaluateRemExpression($record['rects'][0]['width']))->toEqual(7);
});

it('maps every Segments subtab using shared defaults and short override keys', function () {
    $base = OverviewStructure::data();
    $tree = $base['segmentsTabs'];
    $view = base_path('packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/segments/index.blade.php');
    preg_match_all('/<flux:tab name="idea-to-paper-segments-([^"]+)">/', file_get_contents($view), $matches);
    expect(array_keys($tree['children']))->toBe($matches[1]);
    expect($tree['levels'])->toBe($base['primitivesTabs']['levels']);
    foreach ($tree['children'] as $key => $entry) {
        expect(array_keys($entry))->toBe(['id', 'text', 'children']);
        expect($entry['children'])->toBe([]);
        expect($entry['id'])->toBe('literature.overview.segments.tabs.'.$key);
    }
    $compiled = OverviewLayoutOverrides::scope('segments.php', ['segments' => [
        'global' => ['side' => 'right', 'width' => 'half'], 'children' => ['global' => [], 'path' => ['global' => ['side' => 'left', 'stemLength' => '7rem']],
        ],
    ]]);
    expect($compiled['issues'])->toBe([]);
    $resolved = OverviewLayoutOverrides::apply($base, ['segments.php' => $compiled['values']]);
    expect($resolved['issues'])->toBe([]);
    expect($resolved['data']['segmentsTabs']['children']['path']['side'])->toBe('left');
    expect($resolved['data']['segmentsTabs']['children']['arc']['side'])->toBe('right');
    expect($resolved['data']['segmentsTabs']['children']['fusion']['label']['width'])->toBe('half');
});
