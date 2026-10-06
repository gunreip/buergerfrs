<?php

use Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\OverviewLayoutOverrides;
use Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\OverviewStructure;
use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

uses(TestCase::class);

it('inherits marker settings and renders arrows instead of dots with individual child overrides', function () {
    $compiled = OverviewLayoutOverrides::scope('deep-reference.php', ['deep-reference' => [

        'global' => [], 'children' => [
            'global' => [], 'strang' => [
                'global' => [], 'children' => [
                    'global' => ['nodeEnd' => true, 'nodeEndDot' => false], 'flow-while' => ['global' => ['nodeEndDot' => true]],
                ],
            ],
        ],
    ]]);
    expect($compiled['issues'])->toBe([]);
    $resolved = OverviewLayoutOverrides::apply(OverviewStructure::data(), ['deep-reference.php' => $compiled['values']]);
    expect($resolved['issues'])->toBe([]);
    $structure = $resolved['data'];
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph :graph-id="$structure['canvas']['graphId']">
            @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.overview.structure.root')
            @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.overview.structure.main-tabs')
            @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.overview.structure.deep-reference')
        </x-translation-workbench::ui.tw-graph>
    BLADE, compact('structure'));
    $dom = new DOMDocument;
    @$dom->loadHTML('<?xml encoding="utf-8" ?>'.$html);
    $xpath = new DOMXPath($dom);
    $ids = [];
    foreach ($xpath->query('//*[@data-tw-graph-bounds]') as $node) {
        $ids[] = json_decode($node->getAttribute('data-tw-graph-bounds'), true)['id'];
    }
    $prefix = 'literature.overview.deep-reference.strang.';
    expect($ids)->not->toContain($prefix.'flow-start.stem.after.end.joint-arrow');
    expect($ids)->toContain($prefix.'flow-start.stem.after.node.end');
    expect($ids)->toContain($prefix.'flow-while.stem.after.node.end');
    expect($ids)->not->toContain($prefix.'flow-while.stem.after.end.joint-arrow');
    expect($html)->toContain($prefix.'flow-start.stem.after.label.right.1.connector');
});

it('supports section and regular tab marker overrides and rejects non-boolean values', function () {
    $r = OverviewLayoutOverrides::scope('deep-reference.php', ['deep-reference' => ['global' => ['nodeEndDot' => false]]]);
    expect($r['issues'])->toBe([]);
    expect($r['values']['tabs']['deep-reference']['nodeEndDot'])->toBeFalse();
    expect($r['values']['deepReference']['children']['parts']['nodeEndDot'])->toBeFalse();
    expect($r['values']['deepReference']['children']['strang']['children']['flow-while']['nodeEndDot'])->toBeFalse();
    $r = OverviewLayoutOverrides::scope('flow.php', ['flow' => ['global' => ['label' => ['nodeEnd' => false, 'nodeEndDot' => false]]]]);
    expect($r['issues'])->toBe([]);
    expect($r['values']['tabs']['flow']['nodeEnd'])->toBeFalse();
    $bad = OverviewLayoutOverrides::scope('deep-reference.php', ['deep-reference' => [
        'global' => [], 'children' => ['global' => [], 'strang' => ['global' => [], 'children' => ['global' => ['nodeEndDot' => 'true']]],
        ],
    ]]);
    expect($bad['issues'])->toHaveCount(1);
    expect($bad['issues'][0]['path'])->toBe('deep-reference.children.strang.children.global.nodeEndDot');
});
