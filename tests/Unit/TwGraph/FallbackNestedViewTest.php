<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Blade;
use Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry;
use Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry;
use Tests\TestCase;

uses(TestCase::class);

it('separates an open fallback from the shared return in every IF orientation', function (string $variant, string $side, string $direction, bool $withText): void {
    $ifEnd = ['return' => false, 'returnOffset' => '12rem', 'returnLength' => '40rem', 'stemLength' => '4rem'];
    if ($withText) $ifEnd['text'] = ['Fallback action'];
    $template = <<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="fallback-test">
            <x-translation-workbench::ui.tw-graph.strang.VARIANT
                id="outer" :side="$side" :direction="$direction" :if-end="$ifEnd"
                :elseifs="[['key' => 'next', 'conditionLabel' => ['text' => ['Next?']], 'actionLabel' => ['text' => ['Next action']]]]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE;
    Blade::render(str_replace('VARIANT', $variant, $template), compact('side', 'direction', 'ifEnd'));
    $get = fn ($suffix) => AnchorRegistry::get('fallback-test', 'outer.' . $suffix);
    $evaluate = new ReflectionMethod(BoundsRegistry::class, 'evaluateRemExpression');
    $number = fn ($value) => $evaluate->invoke(null, $value);
    $terminal = match ($variant) {
        'flow-if-elseif-multi' => 'elseif.next.',
        'flow-if-elseif' => 'elseif.',
        default => '',
    };
    foreach (['x', 'y'] as $axis) {
        expect($number($get('false.anchorNode-return')[$axis]))->toBe($number($get('anchorNode-end')[$axis]));
        expect($number($get($terminal . 'true.stem.anchorNode-end')[$axis]))->toBe($number($get('anchorNode-end')[$axis]));
    }
    expect($number($get('false.anchorNode-end')['x']) - $number($get('anchorNode-end')['x']))->toBe($side === 'left' ? -12.0 : 12.0);
    expect($number($get('anchorNode-end')['y']) - $number($get('false.anchorNode-end')['y']))->toBe($direction === 'bottom-top' ? 40.0 : -40.0);
})->with(['flow-if', 'flow-if-else', 'flow-if-elseif', 'flow-if-elseif-multi'])
    ->with(['left', 'right'])->with(['bottom-top', 'top-bottom'])->with([true, false]);

it('joins both saved fallback examples and demonstrates bridge versus stem jumps', function (): void {
    $html = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.if.flow-if-nested-3')->render();
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    $evaluate = new ReflectionMethod(BoundsRegistry::class, 'evaluateRemExpression');
    $number = fn ($value) => $evaluate->invoke(null, $value);
    foreach (['', '-right'] as $suffix) {
        $prefix = 'literature.flow.1.if-nested-3' . $suffix;
        $get = fn ($id) => AnchorRegistry::get('idea-to-paper-step-08-flow-if-nested-3' . $suffix, $prefix . '.' . $id);
        foreach (['x', 'y'] as $axis) {
            expect($number($get('inner.anchorNode-start')[$axis]))->toBe($number($get('outer.false.anchorNode-end')[$axis]));
            expect($number($get('inner-return.stem.anchorNode-end')[$axis]))->toBe($number($get('outer.anchorNode-end')[$axis]));
        }
        expect($number($get('outer.anchorNode-end')['y']) - $number($get('inner-return.anchorNode-end')['y']))->toBeGreaterThanOrEqual(0.0);
        expect($xpath->query('//*[@data-tw-graph-path="' . $prefix . '.inner-return.stem.end.joint-arrow"]')->length)->toBe(0);
        expect($xpath->query('//*[@data-tw-graph-path="' . $prefix . '.outer.elseif.deferred.true.stem.end.joint-arrow"]')->length)->toBe(0);
        $dot = $xpath->query('//*[@data-tw-graph-path="' . $prefix . '.outer.elseif.deferred.true.stem.node.end" and contains(concat(" ", normalize-space(@class), " "), " tw-graph-protocol-primitive-node ")]');
        expect($dot->length)->toBe(1);
        $before = $xpath->query('//*[@data-tw-graph-path="' . $prefix . '.continue.stem.before"]')->item(0);
        foreach (['x', 'y'] as $axis) {
            preg_match('/--tw-graph-protocol-anchor-' . $axis . ': ([^;]+);/', $dot->item(0)->getAttribute('style'), $dotPosition);
            preg_match('/--tw-graph-protocol-start-' . $axis . ': ([^;]+);/', $before->getAttribute('style'), $startPosition);
            expect($number($dotPosition[1]))->toBe($number($startPosition[1]));
        }
        $arcName = $suffix === '' ? 'south-east' : 'south-west';
        expect($xpath->query('//*[@data-tw-graph-path="' . $prefix . '.inner-return.arc2-' . $arcName . '.end.joint-arrow"]')->length)->toBe(0);
        $bridge = $prefix . '.outer.elseif.deferred.false.bridge1.bridge-out';
        $stem = $prefix . '.outer.elseif.deferred.true.stem';
        $owner = $suffix === '' ? $bridge : $stem;
        $target = $suffix === '' ? $stem : $bridge;
        $line = $xpath->query('//*[@data-tw-graph-path="' . $owner . '"]')->item(0);
        $jumps = json_decode($line->getAttribute('data-tw-graph-line-jumps'), true, flags: JSON_THROW_ON_ERROR);
        expect($jumps[0]['over'])->toBe($target);
        expect($jumps[0]['side'])->toBe($suffix === '' ? 'top' : 'right');
        expect($xpath->query('//*[@data-tw-graph-path="' . $target . '"]')->item(0)->hasAttribute('data-tw-graph-line-jumps'))->toBeFalse();
    }
});
