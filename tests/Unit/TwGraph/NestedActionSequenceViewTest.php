<?php

declare(strict_types=1);

use Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry;
use Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry;
use Gunreip\TranslationWorkbench\Support\TranslationWorkbenchColorPalette;
use Tests\TestCase;

uses(TestCase::class);

it('runs actions before and after the nested IF before returning to the parent in both orientations', function (): void {
    $html = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.if.flow-if-nested-9')->render();
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    $evaluate = new ReflectionMethod(BoundsRegistry::class, 'evaluateRemExpression');
    $number = fn ($value) => $evaluate->invoke(null, $value);
    foreach (['', '-right'] as $suffix) {
        $prefix = 'literature.flow.1.if-nested-9' . $suffix;
        $get = fn ($id) => AnchorRegistry::get('idea-to-paper-step-08-flow-if-nested-9' . $suffix, $prefix . '.' . $id);
        foreach ([
            ['inner.anchorNode-start', 'prepare.anchorNode-end'],
            ['inner-return.stem.anchorNode-end', 'outer.elseif.deferred.true.anchorNode-return'],
        ] as [$from, $to]) {
            expect($get($from))->not->toBeNull();
            foreach (['x', 'y'] as $axis) {
                expect($number($get($from)[$axis]))->toBe($number($get($to)[$axis]));
            }
        }
        foreach (['prepare' => 'outer.elseif.deferred.true', 'finish' => 'inner'] as $step => $input) {
            expect($number($get($step . '.anchorNode-end')['x']))->toBe($number($get($input . '.anchorNode-end')['x']));
            expect($number($get($step . '.anchorNode-end')['y']) - $number($get($input . '.anchorNode-end')['y']))->toBe(7.25);
        }
        expect($number($get('inner-return.anchorNode-end')['y']) - $number($get('finish.anchorNode-end')['y']))->toBe(5.5);
        expect($number($get('prepare.anchorNode-end')['y']))->toBeGreaterThan($number($get('outer.elseif.deferred.true.anchorNode-end')['y']));
        expect($number($get('finish.anchorNode-end')['y']))->toBeGreaterThan($number($get('inner.anchorNode-end')['y']));
        expect($number($get('inner-return.anchorNode-end')['y']))->toBeLessThan($number($get('outer.anchorNode-end')['y']));
        expect($get('outer.anchorNode-end')['returnColor'])->toBe('rose');
        foreach (['prepare.stem.before' => 'violet', 'finish.stem.before' => 'rose', 'inner-return.stem' => 'rose', 'continue.stem.before' => 'fuchsia'] as $id => $color) {
            $node = $xpath->query('//*[@data-tw-graph-path="' . $prefix . '.' . $id . '"]')->item(0);
            expect($node->getAttribute('style'))->toContain('--tw-graph-protocol-local-color-rgb: ' . TranslationWorkbenchColorPalette::rgb($color) . ';');
        }
    }
});
