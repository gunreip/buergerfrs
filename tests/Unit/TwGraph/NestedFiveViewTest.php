<?php

declare(strict_types=1);

use Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry;
use Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry;
use Tests\TestCase;

uses(TestCase::class);

it('connects both independent nested blocks in each orientation with exchanged jump owners', function (): void {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.if.flow-if-nested-5';
    $html = view($view)->render();
    $source = file_get_contents(app('view')->getFinder()->find($view));
    expect($source)->not->toContain('@foreach')->not->toContain('@include');
    expect(substr_count($source, '<x-translation-workbench::ui.tw-graph.strang.flow-if-elseif-multi'))->toBe(6);
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    $evaluate = new ReflectionMethod(BoundsRegistry::class, 'evaluateRemExpression');
    foreach (['', '-right'] as $suffix) {
        $prefix = 'literature.flow.1.if-nested-5' . $suffix;
        $graph = 'idea-to-paper-step-08-flow-if-nested-5' . $suffix;
        $get = fn ($id) => AnchorRegistry::get($graph, $prefix . '.' . $id);
        foreach (['inner-automatic' => 'automatic', 'inner' => 'deferred'] as $inner => $branch) {
            foreach (['x', 'y'] as $axis) {
                expect($evaluate->invoke(null, $get($inner . '.anchorNode-start')[$axis]))
                    ->toBe($evaluate->invoke(null, $get('outer.elseif.' . $branch . '.true.anchorNode-end')[$axis]));
                expect($evaluate->invoke(null, $get($inner . '-return.stem.anchorNode-end')[$axis]))
                    ->toBe($evaluate->invoke(null, $get('outer.elseif.' . $branch . '.true.anchorNode-return')[$axis]));
            }
        }
        $pairs = [
            ['outer.elseif.automatic.true.bridge1.bridge-out', 'outer.if.true.stem'],
            ['outer.elseif.sources.true.stem', 'outer.elseif.deferred.true.bridge1.bridge-out'],
        ];
        foreach ($pairs as [$leftOwner, $rightOwner]) {
            [$owner, $target] = $suffix === '' ? [$leftOwner, $rightOwner] : [$rightOwner, $leftOwner];
            $jump = $xpath->query('//*[@data-tw-graph-path="' . $prefix . '.' . $owner . '"]')->item(0);
            expect($jump)->not->toBeNull();
            expect($jump->getAttribute('data-tw-graph-line-jumps'))->toContain($prefix . '.' . $target);
        }
        expect($get('inner-automatic.anchorNode-end')['returnColor'])->toBe('blue');
        expect($get('inner.anchorNode-end')['returnColor'])->toBe('green');
    }
});
