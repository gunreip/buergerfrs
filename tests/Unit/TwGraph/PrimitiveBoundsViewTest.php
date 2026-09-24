<?php

use Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry;
use Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry;
use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

uses(TestCase::class);

it('includes steps and side labels in server bounds before browser code runs', function (): void {
    view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.while.flow-while-test')->render();
    $metrics = BoundsRegistry::canvasMetrics('idea-to-paper-while-basic');
    $end = AnchorRegistry::get('idea-to-paper-while-basic', 'literature.while.1.continue.anchorNode-end');
    expect($metrics['maxYRem'])->toBeGreaterThanOrEqual(BoundsRegistry::evaluateRemExpression($end['y']))
        ->and($metrics['maxXRem'])->toBeGreaterThan(6.0)
        ->and($metrics['minXRem'])->not->toBeNull();
});

it('uses graph node size and local endpoint sizes for line bounds without DEV', function (): void {
    Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="primitive-bounds" node-size="3rem" :dev="false">
            <x-translation-workbench::ui.tw-graph.primitives.line id="line" direction="left-right" start-x="-20rem" start-y="4rem" length="10rem" :node-start="true" :node-end="true" node-end-size="5rem" />
        </x-translation-workbench::ui.tw-graph>
    BLADE);
    expect(BoundsRegistry::canvasMetrics('primitive-bounds'))->toMatchArray([
        'minXRem' => -21.5, 'maxYRem' => 6.5, 'minYRem' => 0.0,
    ]);
});

it('rebuilds bounds per render and does not inherit stale component boxes', function (): void {
    $render = fn ($x) => Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="bounds-refresh">
            <x-translation-workbench::ui.tw-graph.primitives.node id="node" :anchor-x="$x" size="2rem" />
        </x-translation-workbench::ui.tw-graph>
    BLADE, ['x' => $x]);
    $render('-100rem');
    expect(BoundsRegistry::canvasMetrics('bounds-refresh')['minXRem'])->toBe(-101.0);
    $render('0rem');
    expect(BoundsRegistry::canvasMetrics('bounds-refresh')['minXRem'])->toBe(-1.0);
});

it('declares bounds for every visible drawing primitive in existing examples', function (string $view): void {
    $html = view($view, ['dev' => true, 'coordinates' => true])->render();
    $dom = new DOMDocument;
    $previous = libxml_use_internal_errors(true);
    $dom->loadHTML('<?xml encoding="UTF-8">'.$html);
    libxml_clear_errors();
    libxml_use_internal_errors($previous);
    $xpath = new DOMXPath($dom);
    $missing = [];
    foreach ($xpath->query('//*[contains(concat(" ", normalize-space(@class), " "), " tw-graph-protocol-primitive ")]') as $element) {
        $class = $element->getAttribute('class');
        if (str_contains($class, 'dev-node-counter') || str_contains($class, 'dev-only')) {
            continue;
        }
        if (! $element->hasAttribute('data-tw-graph-bounds')) {
            $missing[] = $element->getAttribute('data-tw-graph-path');
        }
    }
    expect($missing)->toBe([], $view);
    foreach ($xpath->query('//script[@data-tw-graph-bounds-records]') as $model) {
        foreach (json_decode($model->textContent, true, flags: JSON_THROW_ON_ERROR) as $record) {
            expect($record['kind'])->toBeIn(['geometry', 'text']);
            foreach ($record['rects'] as $rect) {
                expect(array_keys($rect))->toBe(['x', 'y', 'width', 'height']);
                foreach ($rect as $value) {
                    expect(BoundsRegistry::evaluateRemExpression($value))->not->toBeNull($record['id']);
                }
            }
        }
    }
})->with(function (): array {
    $base = dirname(__DIR__, 3).'/packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper';
    $views = [];
    foreach (['canvas', 'primitives', 'segments', 'parts', 'paths', 'strang', 'flow'] as $area) {
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($base.'/'.$area, FilesystemIterator::SKIP_DOTS)) as $file) {
            if (! str_ends_with($file->getPathname(), '.blade.php') || ! preg_match('/<x-translation-workbench::ui\.tw-graph(?:\s|>)/', file_get_contents($file->getPathname()))) {
                continue;
            }
            $key = str_replace('/', '.', substr($file->getPathname(), strlen($base) + 1, -10));
            $views[$key] = ['translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.'.$key];
        }
    }

    return $views;
});

it('renders graph bounding boxes independently of coordinate guides', function (): void {
    $html = \Illuminate\Support\Facades\Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="independent-boxes" :dev="true" :coordinates="false">
            <x-translation-workbench::ui.tw-graph.primitives.line length="4rem" />
        </x-translation-workbench::ui.tw-graph>
    BLADE);
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    $box = $xpath->query('//*[@data-tw-graph-content-bounds]')->item(0);
    expect($box)->not->toBeNull();
    expect($box->getAttribute('data-tw-graph-dev-box'))->toBe('independent-boxes.content-bounds');
    expect($box->getAttribute('class'))->not->toContain('tw-graph-protocol-coordinate-only');
});
