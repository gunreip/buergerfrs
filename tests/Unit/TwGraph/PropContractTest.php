<?php

declare(strict_types=1);

use Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry;
use Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry;
use Illuminate\Support\Facades\Blade;
use Tests\Support\PropContract;
use Tests\TestCase;

uses(TestCase::class);

it('preserves explicit part lengths and forwards both label bridges', function (string $side, string $direction, string $bridge, string $out, string $radius): void {
    Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="prop-parts">
            <x-translation-workbench::ui.tw-graph.parts.start id="stem" :length="$out" :direction="$direction" />
            <x-translation-workbench::ui.tw-graph.parts.sideways
                id="part" :side="$side" :direction="$direction" :arc-radius="$radius"
                :bridge-length="$bridge" :bridge-out-length="$out"
                :bridge-label="['text' => ['Action'], 'width' => 'default']"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE, compact('side', 'direction', 'bridge', 'out', 'radius'));
    $number = fn ($v) => BoundsRegistry::evaluateRemExpression($v);
    $part = AnchorRegistry::get('prop-parts', 'part.anchorNode-end');
    $stem = AnchorRegistry::get('prop-parts', 'stem.anchorNode-end');
    $contract = new PropContract('parts.start / parts.sideways');
    $contract->check('length='.$out, $number($out), abs($number($stem['y'])));
    $contract->check('bridge-length='.$bridge.', bridge-out-length='.$out, 12.0 + $number($bridge) + $number($out) + 2 * $number($radius), abs($number($part['x'])));
    $contract->check('arc-radius='.$radius, 2 * $number($radius), abs($number($part['y'])));
    $contract->verify();
})->with(['left', 'right'])->with(['bottom-top', 'top-bottom'])->with([
    'short bridges' => ['2rem', '3rem', '2.75rem'],
    'different bridges and radius' => ['5rem', '7rem', '1.5rem'],
]);

it('preserves switch defaults and explicit lengths independently of fall-through', function (bool $fallThrough, ?string $out, string $side, string $bridge, string $radius): void {
    Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="prop-switch">
            <x-translation-workbench::ui.tw-graph.strang.flow-switch-case
                id="switch" :side="$side" :bridge-length="$bridge" :arc-radius="$radius" stem-length="18rem"
                :cases="[
                    ['key' => 'first', 'fallThrough' => $fallThrough,
                     'actionLabel' => ['text' => ['First'], 'width' => 'default', 'bridgeOutLength' => $out]],
                    ['key' => 'next', 'actionLabel' => ['text' => ['Next'], 'width' => 'default']],
                ]"
                :case-default="['text' => ['Default'], 'width' => 'default']"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE, compact('fallThrough', 'out', 'side', 'bridge', 'radius'));
    $number = fn ($v) => BoundsRegistry::evaluateRemExpression($v);
    $get = fn ($key) => AnchorRegistry::get('prop-switch', 'switch.'.$key);
    $entry = $get('case.first.entry.anchorNode-end');
    $end = $get('case.first.anchorNode-end');
    $contract = new PropContract('strang.flow-switch-case');
    $expectedOut = $fallThrough ? $number($out ?? $bridge) : $number($bridge);
    $contract->check('bridge-length='.$bridge.'; actionLabel.bridgeOutLength='.($out ?? 'default').'; fallThrough='.json_encode($fallThrough),
        12.0 + $number($bridge) + $expectedOut + 2 * $number($radius), abs($number($entry['x']) - $number($end['x'])));
    $contract->check('arc-radius='.$radius, 2 * $number($radius), abs($number($end['y']) - $number($entry['y'])));
    $contract->check('stem-length=18rem', 18.0, abs($number($get('case.next.entry.anchorNode-end')['y']) - $number($entry['y'])));
    $contract->verify();
})->with([false, true])->with([null, '2rem', '3rem'])->with(['left', 'right'])->with([
    'standard radius' => ['5rem', '2.75rem'],
    'custom radius' => ['6rem', '1.5rem'],
]);

it('lists every prop discrepancy in one diagnostic', function (): void {
    $contract = new PropContract('example.component');
    $contract->check('bridge-length', '5rem', '0rem');
    $contract->check('color', 'cyan', 'amber');
    try {
        $contract->verify();
    } catch (\PHPUnit\Framework\ExpectationFailedException $exception) {
        expect($exception->getMessage())->toContain('example.component', 'bridge-length', '5rem', '0rem', 'color', 'cyan', 'amber');
        return;
    }
    $this->fail('Prop mismatches must not silently pass.');
});

it('keeps the bridge-owned join offset explicit and rejects offsets outside the bridge', function (string $offset, bool $valid): void {
    $render = fn () => Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="owned-join">
            <x-translation-workbench::ui.tw-graph.segments.label-bridge
                id="bridge" direction="right-left" bridge-length="5rem" :bridge-in-join-length="$offset"
                color="green" :z-index="20" :label="['text' => ['Action']]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE, ['offset' => $offset]);
    if (!$valid) {
        expect($render)->toThrow(\Illuminate\View\ViewException::class, 'joinLength must lie within the path');
        return;
    }
    $html = $render();
    $anchor = AnchorRegistry::get('owned-join', 'bridge.bridge-in.anchorNode-join');
    expect(BoundsRegistry::evaluateRemExpression($anchor['x']))->toBe(-BoundsRegistry::evaluateRemExpression($offset));
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $nodes = (new DOMXPath($dom))->query('//*[@data-tw-graph-path="bridge.bridge-in.node.join"]');
    expect($nodes->length)->toBe(1);
    expect($nodes->item(0)->getAttribute('style'))->toContain('--tw-graph-protocol-z-index: 21', '--tw-graph-protocol-local-color-rgb: '.\Gunreip\TranslationWorkbench\Support\TranslationWorkbenchColorPalette::rgb('green'));
})->with([
    'first offset' => ['1rem', true],
    'second offset' => ['3rem', true],
    'outside bridge' => ['6rem', false],
    'negative offset' => ['-1rem', false],
]);
