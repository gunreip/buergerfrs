<?php

use Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry;
use Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry;
use Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\OverviewLayoutOverrides;
use Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\OverviewStructure;
use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

uses(TestCase::class);

it('keeps the entry direction while selecting the exit arc and extension direction', function (string $side, string $direction, ?string $exit) {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="exit-direction-test">
            <x-translation-workbench::ui.tw-graph.parts.sideways
                id="route" :side="$side" :direction="$direction" :exit-direction="$exit"
                arc-radius="2rem" bridge-length="4rem" extension="1rem"
                :joint-arrow-end="true"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE, compact('side', 'direction', 'exit'));
    $actualExit = $exit ?? $direction;
    $end = AnchorRegistry::get('exit-direction-test', 'route.anchorNode-end');
    $entrySign = $direction === 'top-bottom' ? -1 : 1;
    $exitSign = $actualExit === 'top-bottom' ? -1 : 1;
    expect(BoundsRegistry::evaluateRemExpression($end['x']))->toEqual($side === 'left' ? -8 : 8);
    expect(BoundsRegistry::evaluateRemExpression($end['y']))->toEqual(2 * $entrySign + 3 * $exitSign);
    expect($end['direction'])->toBe($actualExit);
    $arc = ($actualExit === 'top-bottom' ? 'north-' : 'south-').($side === 'left' ? 'west' : 'east');
    expect($html)->toContain('route.arc2-'.$arc, 'route.extension-stem1');
})->with(['left', 'right'])->with(['bottom-top', 'top-bottom'])->with([null, 'bottom-top', 'top-bottom']);

it('routes center vertically with twice the radius and no bridge or arcs', function (string $direction, string $radius, string $extension) {
    $template = <<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="center-route-test" arc-radius="3rem">
            <x-translation-workbench::ui.tw-graph.parts.sideways
                id="route" side="center" :direction="$direction"
                :arc-radius="$radius ?: null" bridge-length="99rem" :extension="$extension"
                :anchor-start="['x' => '7rem', 'y' => '10rem']"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE;
    if ($radius === '') {
        $template = str_replace(':arc-radius="$radius ?: null"', '', $template);
    }
    $html = Blade::render($template, compact('direction', 'radius', 'extension'));
    $end = AnchorRegistry::get('center-route-test', 'route.anchorNode-end');
    expect(BoundsRegistry::evaluateRemExpression($end['x']))->toEqual(7);
    $distance = 2 * (float) ($radius ?: '3rem') + (float) $extension;
    expect(BoundsRegistry::evaluateRemExpression($end['y']))->toEqual(10 + ($direction === 'top-bottom' ? -$distance : $distance));
    expect($end['direction'])->toBe($direction);
    expect($html)->toContain('route.stem');
    expect($html)->not->toContain('route.arc1-', 'route.arc2-', 'route.bridge1', 'route.arc-in.bridge.joint-arrow');
})->with(['top-bottom', 'bottom-top'])->with(['', '2rem', '3.375rem'])->with(['0rem', '1rem']);

it('uses the center connection through overview overrides without changing common defaults', function () {
    $compiled = OverviewLayoutOverrides::scope('canvas.php', ['canvas' => [
        'global' => [], 'children' => ['global' => [], 'default-trunk' => ['global' => ['side' => 'center', 'bridgeLength' => '99rem']]],
    ]]);
    expect($compiled['issues'])->toBe([]);
    $base = OverviewStructure::data();
    $resolved = OverviewLayoutOverrides::apply($base, ['canvas.php' => $compiled['values']]);
    expect($resolved['issues'])->toBe([]);
    $structure = $resolved['data'];
    Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph :graph-id="$structure['canvas']['graphId']">
            @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.overview.structure.root')
            @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.overview.structure.main-tabs')
            @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.overview.structure.canvas')
        </x-translation-workbench::ui.tw-graph>
    BLADE, compact('structure'));
    $graph = $structure['canvas']['graphId'];
    $before = AnchorRegistry::get($graph, 'literature.overview.canvas.tabs.default-trunk-stem.anchorNode-end');
    $after = AnchorRegistry::get($graph, 'literature.overview.canvas.tabs.default-trunk-branch.anchorNode-end');
    expect(BoundsRegistry::evaluateRemExpression($after['x']))->toEqual(BoundsRegistry::evaluateRemExpression($before['x']));
    expect(BoundsRegistry::evaluateRemExpression($after['y']))->toEqual(BoundsRegistry::evaluateRemExpression($before['y']) - 5.5);
    expect($base['canvasTabs']['levels']['subtabs']['side'])->toBe('left');
});

it('extends the sideways exit with one stem in its exit direction', function (string $side, string $direction) {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="single-extension-test">
            <x-translation-workbench::ui.tw-graph.parts.sideways
                id="route" :side="$side" :direction="$direction"
                arc-radius="2rem" bridge-length="4rem" extension-length="3rem"
                :node-end="false" :extension-end="['nodeEnd' => false]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE, compact('side', 'direction'));
    $end = AnchorRegistry::get('single-extension-test', 'route.anchorNode-end');
    expect(BoundsRegistry::evaluateRemExpression($end['x']))->toEqual(match ($side) {
        'left' => -8, 'right' => 8, default => 0
    });
    expect(BoundsRegistry::evaluateRemExpression($end['y']))->toEqual($direction === 'top-bottom' ? -7 : 7);
    expect($html)->toContain('route.extension-stem');
    expect($html)->not->toContain('route.extension-stem1', 'route.extension-stem2');
})->with(['left', 'right', 'center'])->with(['top-bottom', 'bottom-top']);

it('keeps an extension label visible independently of the arc marker', function () {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="extension-label-test">
            <x-translation-workbench::ui.tw-graph.parts.sideways
                id="route" extension-length="3rem" :node-end="false"
                :extension-end="['nodeEnd' => false]"
                :node-label-right="['text' => 'Extension label']"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);
    expect($html)->toContain('Extension label', 'route.anchorNode-end.label-1', 'route.extension-stem.node.end');
});
