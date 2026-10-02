<?php

use Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry;
use Gunreip\TranslationWorkbench\Support\TwGraph\SplitGeometry;
use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

uses(TestCase::class);

it('splits one input into named mirrored outputs and renders their information labels', function (string $direction) {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="split-test" :dev="true">
            <x-translation-workbench::ui.tw-graph.parts.split id="split" :anchor-start="['x' => '0rem', 'y' => '10rem']"
                :direction="$direction" :counter-start="4" :outputs="[
                    ['key' => 'true', 'offset' => '6rem', 'color' => 'green', 'label' => ['text' => ['TRUE'], 'side' => 'top']],
                    ['key' => 'false', 'offset' => '-6rem', 'color' => 'red', 'label' => ['text' => ['FALSE'], 'side' => 'bottom']],
                ]" />
        </x-translation-workbench::ui.tw-graph>
    BLADE, compact('direction'));
    $x = $direction === 'left-right' ? '2.75rem' : '-2.75rem';
    expect(AnchorRegistry::get('split-test', 'split.outputs.true.anchorNode-end'))->toMatchArray(['x' => $x, 'y' => '16rem', 'direction' => $direction, 'color' => 'green']);
    expect(AnchorRegistry::get('split-test', 'split.outputs.false.anchorNode-end'))->toMatchArray(['x' => $x, 'y' => '4rem', 'direction' => $direction, 'color' => 'red']);
    expect(AnchorRegistry::get('split-test', 'split.anchorNode-end'))->toBeNull();
    expect($html)->toContain('TRUE', 'FALSE', 'split.outputs.true.node.label.top.1', 'split.outputs.false.node.label.bottom.1');
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xp = new DOMXPath($dom);
    $numbers = [];
    foreach ($xp->query('//*[contains(concat(" ", normalize-space(@class), " "), " tw-graph-protocol-primitive-dev-node-counter ")]') as $node) {
        $numbers[] = (int) trim($node->textContent);
    }
    sort($numbers);
    expect($numbers)->toBe([4, 5, 6]);
})->with(['left-right', 'right-left']);

it('rejects ambiguous output topology instead of silently moving anchors', function (array $outputs) {
    SplitGeometry::plan(['x' => '0rem', 'y' => '0rem'], $outputs, 'right-left', '1.375rem', '1rem');
})->with([
    [[['key' => 'a', 'offset' => '6rem']]],
    [[['key' => 'a', 'offset' => '6rem'], ['key' => 'a', 'offset' => '-6rem']]],
    [[['key' => 'a', 'offset' => '6rem'], ['key' => 'b', 'offset' => '4rem']]],
    [[['key' => 'a', 'offset' => '0rem'], ['key' => 'b', 'offset' => '-4rem']]],
    [[['key' => 'a', 'offset' => 'bogus'], ['key' => 'b', 'offset' => '-4rem']]],
])->throws(InvalidArgumentException::class);

it('uses a common radius with no too-short compensator', function () {
    $plan = SplitGeometry::plan(['x' => '0rem', 'y' => '0rem'], [
        ['key' => 'a', 'offset' => '3rem'], ['key' => 'b', 'offset' => '-3rem'],
    ], 'left-right', '1.375rem', '1rem');
    expect($plan['radius'])->toBe('1.5rem');
    expect($plan['lanes'][0]['anchor'])->toBe(['x' => '3rem', 'y' => '3rem']);
});
