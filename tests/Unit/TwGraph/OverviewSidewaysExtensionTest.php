<?php

use Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry;
use Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry;
use Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\OverviewLayoutOverrides;
use Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\OverviewStructure;
use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

uses(TestCase::class);

it('adds an independent label extension with selectable marker and unchanged beforeLength', function ($length, $visible, $dot) {
    $compiled = OverviewLayoutOverrides::scope('strang-rekey.php', ['strang-rekey' => ['global' => [], 'children' => [
        'global' => ['sideways' => ['extensionLength' => '1rem']],
        'default' => ['global' => ['sideways' => ['extensionLength' => $length, 'nodeEndDot' => false, 'extensionEnd' => ['nodeEnd' => $visible, 'nodeEndDot' => $dot]], 'label' => ['beforeLength' => '4rem']]],
    ]]]);
    expect($compiled['issues'])->toBe([]);
    $resolved = OverviewLayoutOverrides::apply(OverviewStructure::data(), ['strang-rekey.php' => $compiled['values']]);
    expect($resolved['issues'])->toBe([]);
    $structure = $resolved['data'];
    expect($structure['strangRekeyTabs']['children']['source']['sideways']['extensionLength'])->toBe('1rem');
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph :graph-id="$structure['canvas']['graphId']">
            @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.overview.structure.root')
            @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.overview.structure.main-tabs')
            @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.overview.structure.strang-rekey')
        </x-translation-workbench::ui.tw-graph>
    BLADE, compact('structure'));
    $id = 'literature.overview.strang-rekey.tabs.default';
    $graph = $structure['canvas']['graphId'];
    $extension = AnchorRegistry::get($graph, $id.'-branch.anchorNode-end');
    $branch = AnchorRegistry::get($graph, $id.'-stem.anchorNode-end');
    if ($length === '0rem') {
        expect($html)->not->toContain($id.'-branch.extension-stem');
    } else {
        expect(BoundsRegistry::evaluateRemExpression($extension['x']))->toEqual(BoundsRegistry::evaluateRemExpression($branch['x']) - 9.5);
        expect(BoundsRegistry::evaluateRemExpression($extension['y']))->toEqual(BoundsRegistry::evaluateRemExpression($branch['y']) - 5.5 - (float) $length);
        $dom = new DOMDocument;
        @$dom->loadHTML('<?xml encoding="utf-8" ?>'.$html);
        $ids = [];
        foreach ((new DOMXPath($dom))->query('//*[@data-tw-graph-bounds]') as $node) {
            $ids[] = json_decode($node->getAttribute('data-tw-graph-bounds'), true)['id'];
        }
        expect($ids)->toContain($id.'-branch.arc2-north-west.end.joint-arrow');
        expect($ids)->not->toContain($id.'-branch.arc2-north-west.node.end');
        expect(in_array($id.'-branch.extension-stem.node.end', $ids, true))->toBe($visible && $dot);
        expect(in_array($id.'-branch.extension-stem.end.joint-arrow', $ids, true))->toBe($visible && ! $dot);
    }
    expect($structure['strangRekeyTabs']['children']['default']['label']['beforeLength'])->toBe('4rem');
})->with([
    ['0rem', true, true], ['3rem', true, true], ['3rem', true, false], ['3rem', false, false],
]);

it('rejects the removed label extension setting without an alias', function () {
    $r = OverviewLayoutOverrides::scope('strang-rekey.php', ['strang-rekey' => ['global' => [], 'children' => [
        'global' => [], 'default' => ['global' => ['label' => ['extensionLength' => '3rem']]],
    ]]]);
    expect(array_column($r['issues'], 'path'))->toBe(['strang-rekey.children.default.global.label.extensionLength']);
});
