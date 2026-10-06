<?php

use Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry;
use Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource;
use Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\OverviewLayoutOverrides;
use Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\OverviewStructure;
use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

uses(TestCase::class);

it('replaces selected nested leaves without reindexing numeric keys or modifying the base', function () {
    $base = OverviewStructure::data();
    $result = OverviewLayoutOverrides::apply($base, [
        'canvas.php' => ['merges' => ['left' => ['extensionBridgeContinuations' => [4 => '16rem']]]],
        'deep-reference.php' => ['deepReference' => ['children' => ['strang' => ['label' => ['beforeLength' => '3rem']]]]],
    ]);
    expect($result['issues'])->toBe([]);
    expect($result['data']['merges']['left']['extensionBridgeContinuations'])->toBe([4 => '16rem']);
    expect($base['merges']['left']['extensionBridgeContinuations'])->toBe([]);
    expect($result['data']['deepReference']['children']['strang']['label']['beforeLength'])->toBe('3rem');
    expect($result['data']['canvasTabs'])->toBe($base['canvasTabs']);
});

it('reports unknown paths and wrong shapes while applying unrelated valid leaves', function () {
    $result = OverviewLayoutOverrides::apply(['length' => '4rem', 'nodes' => [4 => '8rem']], [
        'canvas.php' => ['lenght' => '7rem', 'length' => [], 'nodes' => [5 => '9rem']],
        'deep-reference.php' => ['nodes' => [4 => '10rem']],
        'invalid.php' => false,
    ]);
    expect($result['data'])->toBe(['length' => '4rem', 'nodes' => [4 => '10rem']]);
    expect($result['issues'])->toHaveCount(4);
    expect(array_column($result['issues'], 'path'))->toBe(['lenght', 'length', 'nodes.5', '']);
    expect(array_column($result['issues'], 'file'))->toBe(['canvas.php', 'canvas.php', 'canvas.php', 'invalid.php']);
});

it('rejects conflicting leaves regardless of file order and identifies both files', function () {
    $base = ['length' => '4rem', 'color' => 'sky'];
    $overrides = ['canvas.php' => ['length' => '8rem'], 'deep-reference.php' => ['length' => '9rem', 'color' => 'fuchsia']];
    foreach ([$overrides, array_reverse($overrides, true)] as $files) {
        $result = OverviewLayoutOverrides::apply($base, $files);
        expect($result['data'])->toBe(['length' => '4rem', 'color' => 'fuchsia']);
        expect($result['issues'])->toHaveCount(1);
        expect($result['issues'][0]['file'])->toBe(array_key_last($files));
        expect($result['issues'][0]['message'])->toContain(array_key_first($files));
        expect($result['issues'][0]['path'])->toBe('length');
    }
});

it('loads authored geometry while the overview source accordions remain paused', function () {
    $result = OverviewLayoutOverrides::load();
    expect($result['issues'])->toBe([]);
    expect(array_map('basename', $result['files']))->toContain('canvas.php', 'deep-reference.php');
    $source = ExampleSource::fromFile(OverviewLayoutOverrides::directory().'/canvas.php')->source();
    expect($source)->toBe(rtrim(file_get_contents(OverviewLayoutOverrides::directory().'/canvas.php')));
    $authored = require OverviewLayoutOverrides::directory().'/deep-reference.php';

    $html = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.overview.overview-structure')->render();
    preg_match('/<script type="application\/json" data-tw-graph-bounds-records>(.*?)<\/script>/s', $html, $matches);
    $records = collect(json_decode($matches[1], true));
    $bridge = $records->firstWhere('id', 'literature.overview.tabs.left.extension.4.paths.merge-extension.bridge');
    expect(BoundsRegistry::evaluateRemExpression($bridge['rects'][0]['width']))->toBe(BoundsRegistry::evaluateRemExpression($authored['deep-reference']['global']['connection']['bridgeLength']));
    expect($html)->toContain('data/canvas.php', 'data/deep-reference.php');
    expect($html)->not->toContain('Layout overrides', 'aria-label="Code example"');
    expect($html)->not->toContain('data-overview-layout-issues="true"');
});

it('accepts sparse dimensions for every existing extension on both sides without materializing defaults', function (string $side, string $property) {
    $base = OverviewStructure::data();
    $result = OverviewLayoutOverrides::apply($base, ['canvas.php' => ['merges' => [$side => [$property => [3 => '26rem']]]]]);
    expect($result['issues'])->toBe([]);
    expect($result['data']['merges'][$side][$property][3])->toBe('26rem');
    expect($result['data']['merges'][$side][$property])->not->toHaveKey(2);
})->with(['left', 'right'])->with(['extensionBridgeContinuations', 'extensionStemLengths', 'extensionArcRadiuss']);

it('rejects invalid extension indices types and conflicting newly authored leaves', function () {
    $result = OverviewLayoutOverrides::apply(OverviewStructure::data(), [
        'a.php' => ['merges' => ['left' => ['extensionBridgeContinuations' => [0 => '2rem', 7 => '3rem', 2 => 5, 3 => '26rem']]]],
        'b.php' => ['merges' => ['left' => ['extensionBridgeContinuations' => [3 => '20rem', 4 => '11rem']]]],
    ]);
    expect($result['issues'])->toHaveCount(4);
    expect($result['data']['merges']['left']['extensionBridgeContinuations'])->toBe([4 => '11rem']);
});

it('forwards newly authored extension bridges into rendered geometry on both sides', function () {
    $structure = OverviewLayoutOverrides::apply(OverviewStructure::data(), ['layout.php' => ['merges' => [
        'left' => ['extensionBridgeContinuations' => [3 => '26rem']],
        'right' => ['extensionBridgeContinuations' => [3 => '19rem']],
    ]]])['data'];
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph :graph-id="$structure['canvas']['graphId']">
            @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.overview.structure.root')
            @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.overview.structure.main-tabs')
        </x-translation-workbench::ui.tw-graph>
    BLADE, compact('structure'));
    preg_match('/data-tw-graph-bounds-records>(.*?)<\/script>/s', $html, $matches);
    $records = collect(json_decode($matches[1], true));
    foreach (['left' => 26.0, 'right' => 19.0] as $side => $length) {
        $record = $records->firstWhere('id', "literature.overview.tabs.$side.extension.3.paths.merge-extension.bridge");
        expect(BoundsRegistry::evaluateRemExpression($record['rects'][0]['width']))->toBe($length);
    }
});

it('shows override mismatches outside and before the scrollable canvas', function () {
    $layoutOverrides = OverviewLayoutOverrides::apply(OverviewStructure::data(), ['invalid.php' => ['merges' => ['left' => ['extensionBridgeContinuations' => [99 => '2rem']]]]]);
    $layoutOverrides['files'] = [];
    $path = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.overview.overview-structure')->getPath();
    $source = str_replace('\\Gunreip\\TranslationWorkbench\\Support\\TwGraph\\Documentation\\OverviewLayoutOverrides::load()', '$suppliedOverrides', file_get_contents($path));
    $html = Blade::render($source, ['suppliedOverrides' => $layoutOverrides]);
    $document = new DOMDocument;
    @$document->loadHTML($html);
    $xpath = new DOMXPath($document);
    expect($xpath->query('//*[@data-overview-layout-issues]'))->toHaveCount(1);
    expect($xpath->query('//*[@data-overview-canvas-scroll]//*[@data-overview-layout-issues]'))->toHaveCount(0);
    expect(strpos($html, 'data-overview-layout-issues'))->toBeLessThan(strpos($html, 'data-overview-canvas-scroll'));
    expect($html)->toContain('merges.left.extensionBridgeContinuations.99');
});
