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

it('registers drawing primitives independently of DEV and ignores diagnostic box extents', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="diagnostics-hidden-test" :dev="false" :coordinates="false" horizontal-padding="4rem">
            <x-translation-workbench::ui.tw-graph.parts.start id="diagnostics.center.1.start" />
            <x-translation-workbench::ui.tw-graph.dev-box
                id="diagnostics.left.1.bounds"
                x="-10rem"
                y="3rem"
                width="6rem"
                height="8rem"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->not->toContain('tw-graph-protocol-dev-only group pointer-events-none absolute rounded border border-dashed')
        ->toContain('--tw-graph-protocol-calculated-width');

    $summary = BoundsRegistry::summary('diagnostics-hidden-test');

    expect($summary['left']['count'])->toBe(0)
        ->and($summary['center']['count'])->toBeGreaterThan(0)
        ->and(BoundsRegistry::canvasMetrics('diagnostics-hidden-test')['minXRem'])->toBeGreaterThan(-10.0);
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
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($visibleHtml)
        ->toContain('tw-graph-protocol-coordinate-only')
        ->toContain('data-tw-graph-canvas-summary')
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
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    $summary = BoundsRegistry::summary('diagnostics-non-canvas-test');

    expect($summary['left']['count'])->toBe(0)
        ->and($summary['right']['count'])->toBe(0)
        ->and(collect($summary['center']['items'])->pluck('renderId')->all())
        ->not->toContain('diagnostics.left.3.visual-only');
});

it('keeps diagnostic dev boxes behind graph elements and keeps captions visible with bounding boxes', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="diagnostic-fixture" :dev="true">
            <x-translation-workbench::ui.tw-graph.dev-box
                id="strang.branch-left.1.main.path.branch.bridge1.bounds"
                x="-12rem"
                y="4rem"
                width="8rem"
                height="1.5rem"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('tw-graph-protocol-dev-only group pointer-events-none absolute rounded border border-dashed')
        ->toContain('title="strang.branch.left.1.bridge-1"')
        ->toContain('data-tw-graph-dev-caption')
        ->not->toContain('data-flux-tooltip', 'opacity-0')
        ->not->toContain('group-hover:opacity-100')
        ->toContain('border-color: rgb(14 165 233 / 1)')
        ->not->toContain('absolute z-50 rounded border border-dashed')
        ->not->toContain('absolute z-40 rounded border border-dashed');
});

it('shows configured minimum dimensions and padding without changing the content metrics', function (): void {
    $render = function (bool $coordinates): string {
        BoundsRegistry::forgetGraph('diagnostics-minimum-frame');

        return Blade::render(<<<'BLADE'
            <x-translation-workbench::ui.tw-graph graph-id="diagnostics-minimum-frame" :dev="true" :coordinates="$coordinates" min-width="20rem" min-height="15rem" horizontal-padding="3rem">
                <x-translation-workbench::ui.tw-graph.dev-box id="minimum-frame-content" :dev="true" x="-30rem" y="-4rem" width="70rem" height="50rem" />
            </x-translation-workbench::ui.tw-graph>
        BLADE, ['coordinates' => $coordinates]);
    };

    $hidden = $render(false);
    $before = BoundsRegistry::canvasMetrics('diagnostics-minimum-frame', '2rem', '3rem');
    $visible = $render(true);
    $after = BoundsRegistry::canvasMetrics('diagnostics-minimum-frame', '2rem', '3rem');

    expect($hidden)->not->toContain('data-tw-graph-canvas-minimum')
        ->and($visible)->toContain('data-tw-graph-canvas-minimum', 'data-tw-graph-canvas-padding', '20rem × 15rem', 'x=3rem, y=2rem')
        ->and($after)->toBe($before)
        ->and($after['widthRem'])->toBe(6.0)
        ->and($after['heightRem'])->toBe(4.0);
});

it('uses the resolved protocol minimum dimensions and hides configuration lines without dev mode', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph :protocol="['geometry' => ['minWidth' => '55rem', 'minHeight' => '26rem']]" :dev="true" :coordinates="true" />
    BLADE);
    expect($html)->toContain('data-tw-graph-canvas-minimum', '55rem × 26rem')
        ->toContain('data-tw-graph-canvas-padding');

    $hidden = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph :dev="false" :coordinates="true" min-width="55rem" min-height="26rem">
            <span>Example</span>
        </x-translation-workbench::ui.tw-graph>
    BLADE);
    expect($hidden)->not->toContain('data-tw-graph-canvas-minimum');
});

it('labels enclosing IF regions permanently while individual elements retain copyable tooltips', function (): void {
    $html = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.if.flow-if-nested-1')->render();
    $document = new DOMDocument;
    @$document->loadHTML($html);
    $xpath = new DOMXPath($document);
    $captions = $xpath->query('//*[@data-tw-graph-dev-caption]');
    expect($captions->length)->toBe(12);
    foreach ($captions as $caption) {
        $sourceId = substr($caption->parentNode->getAttribute('data-tw-graph-dev-box'), 0, -strlen('.dev-box'));
        expect($caption->textContent)
            ->toContain('...::ui.tw-graph.', 'id="'.$sourceId.'"')
            ->not->toContain('Inner IF / ELSEIF / ELSE', 'Outer IF including nested section');
    }
    foreach (['primitive-line', 'primitive-arc', 'primitive-text', 'primitive-node', 'primitive-joint-arrow', 'primitive-dev-node-counter'] as $kind) {
        $elements = $xpath->query('//*[contains(@class, "tw-graph-protocol-'.$kind.'") and @data-tw-graph-path and @title]');
        expect($elements->length)->toBeGreaterThan(0, $kind);
        foreach ($elements as $element) {
            expect($element->getAttribute('title'))->toContain($element->getAttribute('data-tw-graph-path'));
            expect($element->getAttribute('x-on:click.stop'))->toContain('navigator.clipboard?.writeText($el.dataset.twGraphPath)');
        }
    }
});
