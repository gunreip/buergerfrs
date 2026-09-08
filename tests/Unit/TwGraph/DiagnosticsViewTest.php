<?php

declare(strict_types=1);

use Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry;
use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function (): void {
    BoundsRegistry::forgetGraph('diagnostics-hidden-test');
    BoundsRegistry::forgetGraph('diagnostics-coordinates-test');
    BoundsRegistry::forgetGraph('diagnostics-style-test');
    BoundsRegistry::forgetGraph('diagnostics-non-canvas-test');
});

it('registers dev box canvas bounds even when the diagnostic overlay is hidden', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="diagnostics-hidden-test" :dev="false" :coordinates="false" horizontal-padding="4rem">
            <x-translation-workbench::ui.tw-graph.parts.start id="diagnostics.center.1.start" />
            <x-translation-workbench::ui.tw-graph.dev-box
                id="diagnostics.left.1.bounds"
                x="-10rem"
                y="3rem"
                width="6rem"
                height="8rem"
                metrics-scope="canvas"
                metrics-side="left"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->not->toContain('tw-graph-protocol-dev-only group pointer-events-none absolute rounded border border-dashed')
        ->toContain('--tw-graph-protocol-calculated-width');

    $summary = BoundsRegistry::summary('diagnostics-hidden-test');

    expect($summary['left']['count'])
        ->toBe(1)
        ->and($summary['left']['items'][0]['renderId'])
        ->toBe('diagnostics.left.1.bounds')
        ->and($summary['left']['items'][0]['x'])
        ->toBe('-10rem')
        ->and($summary['left']['items'][0]['width'])
        ->toBe('6rem');
});

it('renders canvas coordinate diagnostics only when dev mode and coordinates are enabled together', function (): void {
    $visibleHtml = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="diagnostics-coordinates-test" :dev="true" :coordinates="true">
            <x-translation-workbench::ui.tw-graph.parts.start id="diagnostics.center.1.start" />
            <x-translation-workbench::ui.tw-graph.dev-box
                id="diagnostics.center.1.bounds"
                x="0rem"
                y="0rem"
                width="4rem"
                height="7rem"
                metrics-scope="canvas"
                metrics-side="center"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    $hiddenHtml = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="diagnostics-style-test" :dev="true" :coordinates="false">
            <x-translation-workbench::ui.tw-graph.parts.start id="diagnostics.center.2.start" />
            <x-translation-workbench::ui.tw-graph.dev-box
                id="diagnostics.center.2.bounds"
                x="0rem"
                y="0rem"
                width="4rem"
                height="7rem"
                metrics-scope="canvas"
                metrics-side="center"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($visibleHtml)
        ->toContain('tw-graph-protocol-coordinate-only')
        ->toContain('canvas-center-top')
        ->and($hiddenHtml)
        ->not->toContain('tw-graph-protocol-coordinate-only')
        ->toContain('--tw-graph-protocol-calculated-width');
});

it('does not register non canvas dev boxes as graph layout bounds', function (): void {
    Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="diagnostics-non-canvas-test" :dev="true" :coordinates="false">
            <x-translation-workbench::ui.tw-graph.parts.start id="diagnostics.center.3.start" />
            <x-translation-workbench::ui.tw-graph.dev-box
                id="diagnostics.left.3.visual-only"
                x="-12rem"
                y="4rem"
                width="8rem"
                height="9rem"
                metrics-scope="debug"
                metrics-side="left"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    $summary = BoundsRegistry::summary('diagnostics-non-canvas-test');

    expect($summary['left']['count'])->toBe(0)
        ->and($summary['right']['count'])->toBe(0)
        ->and(collect($summary['center']['items'])->pluck('renderId')->all())
        ->not->toContain('diagnostics.left.3.visual-only');
});

it('keeps diagnostic dev boxes behind graph elements while preserving hover labels', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph.dev-box
            id="strang.branch-left.1.main.path.branch.bridge1.bounds"
            x="-12rem"
            y="4rem"
            width="8rem"
            height="1.5rem"
            metrics-scope="canvas"
            metrics-side="left"
            :dev="true"
        />
    BLADE);

    expect($html)
        ->toContain('tw-graph-protocol-dev-only group pointer-events-none absolute rounded border border-dashed')
        ->toContain('title="strang.branch.left.1.bridge-1"')
        ->toContain('border-color: rgb(14 165 233 / 0.35)')
        ->not->toContain('absolute z-50 rounded border border-dashed')
        ->not->toContain('absolute z-40 rounded border border-dashed');
});
