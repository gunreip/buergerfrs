<?php

use Gunreip\TranslationWorkbench\Support\TranslationWorkbenchColorPalette;
use Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry;
use Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\OverviewLayoutOverrides;
use Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\OverviewStructure;
use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

uses(TestCase::class);

it('continues extension colors by index and exposes them on anchors on both merge sides', function (string $side) {
    $component = 'x-translation-workbench::ui.tw-graph.strang.merge-'.$side;
    $html = Blade::render('<x-translation-workbench::ui.tw-graph graph-id="colors"><'.$component.' id="merge" color="sky" :extension-count="6" :extension-colors="[3 => \'fuchsia\', 5 => \'cyan\']" /></x-translation-workbench::ui.tw-graph>');
    $document = new DOMDocument;
    @$document->loadHTML($html);
    $xpath = new DOMXPath($document);
    foreach ([1 => 'sky', 2 => 'sky', 3 => 'fuchsia', 4 => 'fuchsia', 5 => 'cyan', 6 => 'cyan'] as $index => $color) {
        expect(AnchorRegistry::get('colors', "strang.merge-$side.extension.$index.node.1")['color'])->toBe($color);
        $bridge = $xpath->query('//*[@data-tw-graph-path="merge.extension.'.$index.'.paths.merge-extension.bridge"]')->item(0);
        expect($bridge)->not->toBeNull();
        expect($bridge->getAttribute('style'))->toContain('--tw-graph-protocol-local-color-rgb: '.TranslationWorkbenchColorPalette::rgb($color));
    }
    expect(AnchorRegistry::get('colors', "strang.merge-$side.node.1")['color'])->toBe('sky');
})->with(['left', 'right']);

it('inherits connected colors through tabs branches and leaves without leaking across unrelated branches', function () {
    $result = OverviewLayoutOverrides::apply(OverviewStructure::data(), ['colors.php' => [
        'merges' => ['left' => ['extensionColors' => [3 => 'fuchsia', 5 => 'cyan']]],
        'canvasTabs' => ['children' => [
            'borders' => ['color' => 'red'],
            'props' => ['children' => ['node-size' => ['color' => 'orange']]],
        ]],
        'deepReference' => ['children' => ['strang' => ['children' => ['flow-if' => ['color' => 'violet']]]]],
    ]]);
    expect($result['issues'])->toBe([]);
    $structure = $result['data'];
    Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph :graph-id="$structure['canvas']['graphId']">
            @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.overview.structure.root')
            @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.overview.structure.main-tabs')
            @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.overview.structure.deep-reference')
            @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.overview.structure.canvas')
        </x-translation-workbench::ui.tw-graph>
    BLADE, compact('structure'));
    $color = fn ($id) => AnchorRegistry::get($structure['canvas']['graphId'], $id.'.anchorNode-end')['color'];
    expect($color('literature.overview.canvas'))->toBe('fuchsia');
    expect($color('literature.overview.paths'))->toBe('zinc');
    expect($color('literature.overview.canvas.tabs.default'))->toBe('fuchsia');
    expect($color('literature.overview.canvas.tabs.borders'))->toBe('red');
    expect($color('literature.overview.canvas.tabs.height'))->toBe('red');
    expect($color('literature.overview.canvas.props.cap-length'))->toBe('orange');
    expect($color('literature.overview.deep-reference.strang.flow-while'))->toBe('violet');
    expect($color('literature.overview.deep-reference.parts'))->toBe('fuchsia');
});
