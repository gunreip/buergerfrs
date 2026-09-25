<?php

declare(strict_types=1);

use Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry;
use Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry;
use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

uses(TestCase::class);

it('merges multiple parallel inputs with one output dot and no negative compensators', function (string $direction, array $levels, float $expectedWidth): void {
    $inputs = array_map(fn ($y, $i) => ['key' => 'lane'.$i, 'anchor' => ['x' => 'calc(2rem + 3rem)', 'y' => $y.'rem'], 'color' => 'amber'], $levels, array_keys($levels));
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="fusion-test" :dev="true">
            <x-translation-workbench::ui.tw-graph.parts.fusion id="fusion" :inputs="$inputs" :direction="$direction" :dev-mode="true" />
        </x-translation-workbench::ui.tw-graph>
    BLADE, compact('inputs', 'direction'));
    $output = AnchorRegistry::get('fusion-test', 'fusion.anchorNode-end');
    expect(BoundsRegistry::evaluateRemExpression($output['x']))->toBe(5.0 + ($direction === 'left-right' ? 1 : -1) * $expectedWidth);
    expect(BoundsRegistry::evaluateRemExpression($output['y']))->toBe((float) ((min($levels) + max($levels)) / 2));
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    foreach ($inputs as $input) {
        expect(AnchorRegistry::get('fusion-test', 'fusion.inputs.'.$input['key']))->toBe($input['anchor']);
    }
    expect($xpath->query('//*[@data-tw-graph-path="fusion.output.node.end" and contains(@class, "tw-graph-protocol-primitive-node")]')->length)->toBe(1);
    foreach ($inputs as $input) {
        expect($xpath->query('//*[@data-tw-graph-path="fusion.inputs.' . $input['key'] . '.bridge"]')->length)->toBe(0);
    }
    expect($xpath->query('//*[contains(@data-tw-graph-path, ".arc-in.start.joint-arrow")]')->length)->toBeGreaterThan(0);
    if (in_array((min($levels) + max($levels)) / 2, $levels)) {
        expect($xpath->query('//*[contains(@data-tw-graph-path, ".bridge-in.start.joint-arrow")]')->length)->toBeGreaterThan(0);
    }
    expect($html)->not->toMatch('/--tw-graph-protocol-(?:line-length|arc-radius):\s*-\d/');
})->with(['left-right', 'right-left'])->with([
    'two close inputs' => [[0, 1], 0.5],
    'three with straight middle' => [[-4, 0, 4], 2.75],
    'four unequal inputs' => [[0, 1, 3, 8], 1.0],
    'almost centered middle' => [[0, 4, 9], 0.5],
    'five inputs' => [[-8, -4, 0, 4, 8], 2.75],
]);

it('replaces short compensators with larger direct arcs', function (float $offset, float $minimum, float $expectedRadius, float $expectedStem): void {
    $radius = \Gunreip\TranslationWorkbench\Support\TwGraph\FusionGeometry::radius($offset, 1.375, $minimum);
    expect($radius)->toBe($expectedRadius);
    expect(abs($offset) - 2 * $radius)->toBe($expectedStem);
})->with([
    'short remainder' => [3.0, 1.0, 1.5, 0.0],
    'mirrored short remainder' => [-3.0, 1.0, 1.5, 0.0],
    'direct arc pair' => [2.0, 1.0, 1.0, 0.0],
    'tiny offset' => [0.5, 1.0, 0.25, 0.0],
    'exact minimum' => [3.75, 1.0, 1.375, 1.0],
    'larger stem' => [5.0, 1.0, 1.375, 2.25],
    'disabled minimum' => [3.0, 0.0, 1.375, 0.25],
    'custom minimum' => [3.0, 0.5, 1.5, 0.0],
]);

it('uses the adjusted radius for the shared output and rendered stems', function (string $direction): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="minimum-fusion">
            <x-translation-workbench::ui.tw-graph.parts.fusion id="minimum" :direction="$direction"
                :inputs="[
                    ['key' => 'lower', 'anchor' => ['x' => '0rem', 'y' => '0rem']],
                    ['key' => 'middle', 'anchor' => ['x' => '0rem', 'y' => '3rem']],
                    ['key' => 'upper', 'anchor' => ['x' => '0rem', 'y' => '6rem']],
                ]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE, compact('direction'));
    $output = AnchorRegistry::get('minimum-fusion', 'minimum.anchorNode-end');
    expect(BoundsRegistry::evaluateRemExpression($output['x']))->toBe($direction === 'left-right' ? 3.0 : -3.0);
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    foreach (['lower', 'upper'] as $key) {
        expect($xpath->query('//*[@data-tw-graph-path="minimum.inputs.'.$key.'.arc-out.start.joint-arrow"]')->length)->toBe(0);
        $stem = $xpath->query('//*[@data-tw-graph-path="minimum.inputs.'.$key.'.stem"]')->item(0);
        expect($stem->getAttribute('style'))->toContain('--tw-graph-protocol-local-length: 0rem');
        expect($xpath->query('//*[@data-tw-graph-path="minimum.inputs.'.$key.'.arc-in" and contains(@style, "--tw-graph-protocol-local-arc-radius: 1.5rem")]')->length)->toBe(1);
    }
})->with(['left-right', 'right-left']);

it('joins outer lanes to inner arc ends with a shared radius and only two exit arcs', function (string $direction, int $spacing): void {
    $inputs = array_map(fn ($i) => ['key' => 'lane'.$i, 'anchor' => ['x' => '0rem', 'y' => ($i * $spacing).'rem']], range(0, 3));
    $plan = \Gunreip\TranslationWorkbench\Support\TwGraph\FusionGeometry::group($inputs, $direction, 1.375, 1.0);
    $lanes = array_column($plan['lanes'], null, 'key');
    foreach (['lane0' => 'lane1', 'lane3' => 'lane2'] as $outer => $inner) {
        expect($lanes[$outer]['next'])->toBe($inner);
        expect($lanes[$outer]['stemEnd'])->toBe($lanes[$inner]['arcEnd']);
    }
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="shared-fusion">
            <x-translation-workbench::ui.tw-graph.parts.fusion id="shared" :inputs="$inputs" :direction="$direction" />
        </x-translation-workbench::ui.tw-graph>
    BLADE, compact('inputs', 'direction'));
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    foreach (range(0, 3) as $i) {
        $arc = $xpath->query('//*[@data-tw-graph-path="shared.inputs.lane'.$i.'.arc-in" and contains(@class, "tw-graph-protocol-primitive-arc")]')->item(0);
        expect($arc)->not->toBeNull();
        expect($arc->getAttribute('style'))->toContain('--tw-graph-protocol-local-arc-radius: '.$plan['radius'].'rem');
        expect($xpath->query('//*[@data-tw-graph-path="shared.inputs.lane'.$i.'.arc-out" and contains(@class, "tw-graph-protocol-primitive-arc")]')->length)->toBe(in_array($i, [1, 2]) ? 1 : 0);
    }
    foreach ([0, 3] as $i) {
        $stem = $xpath->query('//*[@data-tw-graph-path="shared.inputs.lane'.$i.'.stem"]')->item(0);
        $inner = $i === 0 ? 1 : 2;
        $arc = $xpath->query('//*[@data-tw-graph-path="shared.inputs.lane'.$inner.'.arc-in" and contains(@class, "tw-graph-protocol-primitive-arc")]')->item(0);
        preg_match('/--tw-graph-protocol-z-index:\s*(-?\d+)/', $stem->getAttribute('style'), $stemLayer);
        preg_match('/--tw-graph-protocol-z-index:\s*(-?\d+)/', $arc->getAttribute('style'), $arcLayer);
        expect((int) $stemLayer[1])->toBeLessThan((int) $arcLayer[1]);
        expect($html)->not->toContain('shared.inputs.lane'.$i.'.stem.end.joint-arrow', 'shared.inputs.lane'.$i.'.stem.node.end');
    }
})->with(['left-right', 'right-left'])->with([3, 5, 6, 7]);

it('uses canvas diagnostics even when obsolete local attributes disagree', function (bool $canvasDev, ?bool $override, bool $expected): void {
    $attribute = $override === null ? '' : ($override ? ':dev="true"' : ':dev="false"');
    $html = Blade::render(<<<BLADE
        <x-translation-workbench::ui.tw-graph graph-id="fusion-dev-inheritance" :dev="\$canvasDev">
            <x-translation-workbench::ui.tw-graph.segments.fusion
                id="fusion.dev" direction="left-right"
                :anchor-start="['x' => '0rem', 'y' => '0rem']"
                :anchor-end="['x' => '8rem', 'y' => '6rem']"
                {$attribute}
            />
        </x-translation-workbench::ui.tw-graph>
        BLADE, compact('canvasDev'));
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    $boxes = $xpath->query('//*[starts-with(@data-tw-graph-dev-box, "fusion.dev.")]');
    expect($boxes->length > 0)->toBe($expected);
})->with([
    'canvas enabled' => [true, null, true],
    'canvas disabled' => [false, null, false],
    'local false cannot disable canvas' => [true, false, true],
    'local true cannot enable canvas' => [false, true, false],
]);
