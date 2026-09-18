<?php

declare(strict_types=1);

use Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry;
use Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry;
use Tests\TestCase;

uses(TestCase::class);

it('connects both saved last ELSEIF nested examples and places their jumps on different lanes', function (): void {
    $html = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.if.flow-if-nested-4')->render();
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    $evaluate = new ReflectionMethod(BoundsRegistry::class, 'evaluateRemExpression');
    foreach (['', '-right'] as $suffix) {
        $prefix = 'literature.flow.1.if-nested-4' . $suffix;
        $graph = 'idea-to-paper-step-08-flow-if-nested-4' . $suffix;
        $get = fn ($id) => AnchorRegistry::get($graph, $prefix . '.' . $id);
        foreach (['x', 'y'] as $axis) {
            expect($evaluate->invoke(null, $get('inner.anchorNode-start')[$axis]))
                ->toBe($evaluate->invoke(null, $get('outer.elseif.deferred.true.anchorNode-end')[$axis]));
            expect($evaluate->invoke(null, $get('inner-return.stem.anchorNode-end')[$axis]))
                ->toBe($evaluate->invoke(null, $get('outer.anchorNode-end')[$axis]));
        }
        $owner = $suffix === '' ? 'outer.elseif.deferred.true.bridge1.bridge-out' : 'outer.elseif.sources.true.stem';
        $target = $suffix === '' ? 'outer.elseif.sources.true.stem' : 'outer.elseif.deferred.true.bridge1.bridge-out';
        $jump = $xpath->query('//*[@data-tw-graph-path="' . $prefix . '.' . $owner . '"]')->item(0);
        expect($jump)->not->toBeNull();
        expect($jump->getAttribute('data-tw-graph-line-jumps'))->toContain($prefix . '.' . $target);
    }
});
