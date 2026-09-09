<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

uses(TestCase::class);

it('passes branch path scalar stem labels through without null option fallbacks', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph.paths.branch
            id="paths.branch.scalar-label"
            side="left"
            :anchor-start="['x' => '0rem', 'y' => '0rem']"
            bridge-length="8rem"
            stem-length="4rem"
            color="green"
            :stem-continuation="[
                1 => [
                    'left' => 'finding ID #42|ui.checkout.save|2026-04-14 11:36',
                    'width' => 'halfLong',
                    'align' => 'right',
                    'justify' => true,
                    'color' => 'amber',
                ],
            ]"
            :dev="true"
        />
    BLADE);

    expect($html)
        ->toContain('scalar-label.stem-1.label.left.1')
        ->toContain('finding ID #42')
        ->toContain('ui.checkout.save')
        ->toContain('2026-04-14 11:36')
        ->toContain('w-72')
        ->toContain('items-end text-right')
        ->toContain('text-justify')
        ->toContain('text-amber-700')
        ->toContain('--tw-graph-protocol-text-label-offset: calc(var(--tw-graph-protocol-node-half) + 2rem + 0.25rem)')
        ->not->toContain('halfLong right')
        ->not->toContain('justify true');
});

it('passes branch extension scalar stem labels through without null option fallbacks', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph.paths.branch-extension
            id="paths.branch-extension.scalar-label"
            side="right"
            :anchor-start="['x' => '0rem', 'y' => '0rem']"
            bridge-length="8rem"
            stem-length="4rem"
            color="green"
            :node-labels="[
                3 => [
                    'left' => 'event ID #12|needs review',
                    'width' => 'halfLong',
                    'align' => 'right',
                    'justify' => true,
                    'color' => 'amber',
                ],
            ]"
            :dev="true"
        />
    BLADE);

    expect($html)
        ->toContain('scalar-label.stem.label.left.1')
        ->toContain('event ID #12')
        ->toContain('needs review')
        ->toContain('w-72')
        ->toContain('items-end text-right')
        ->toContain('text-justify')
        ->toContain('text-amber-700')
        ->toContain('--tw-graph-protocol-text-label-offset: calc(var(--tw-graph-protocol-node-half) + 2rem + 0.25rem)')
        ->not->toContain('halfLong right')
        ->not->toContain('justify true');
});

it('uses graph defaults for branch extension arc stem and bridge geometry', function (): void {
    config()->set('tw-graph-defaults.arc_size', '4rem');
    config()->set('tw-graph-defaults.stem_length', '5rem');
    config()->set('tw-graph-defaults.bridge_length', '6rem');

    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph.paths.branch-extension
            id="paths.branch-extension.defaulted"
            side="left"
            :anchor-start="['x' => '-12rem', 'y' => '8rem']"
            color="green"
            :dev="true"
        />
    BLADE);

    expect($html)
        ->toContain('defaulted.bridge')
        ->toContain('defaulted.arc')
        ->toContain('defaulted.stem')
        ->toContain('--tw-graph-protocol-local-arc-size: 4rem')
        ->toContain('--tw-graph-protocol-local-length: 5rem')
        ->toContain('--tw-graph-protocol-local-length: 6rem')
        ->toContain('--tw-graph-protocol-anchor-x: calc(calc(-12rem + calc(6rem * -1)) + calc(4rem * -1))')
        ->toContain('--tw-graph-protocol-anchor-y: calc(8rem + 4rem)');
});

it('renders branch returns solid when fallback styling is explicitly disabled', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph.paths.branch-return
            id="orders.left.1.payment-return"
            side="left"
            :anchor-start="['x' => '-12rem', 'y' => '8rem']"
            arc-size="2.75rem"
            bridge-length="8rem"
            color="green"
            :fallback-used="true"
            :fallback="false"
            :dev="true"
        />
    BLADE);

    expect($html)
        ->toContain('orders.left.1.payment-return.arc.in')
        ->toContain('orders.left.1.payment-return.bridge')
        ->toContain('orders.left.1.payment-return.arc.out')
        ->toContain('left-right')
        ->not->toContain('tw-graph-protocol-primitive-line-dashed')
        ->not->toContain('tw-graph-protocol-primitive-arc-dashed');
});

it('renders branch returns dashed when fallback styling is active', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph.paths.branch-return
            id="orders.right.1.payment-return"
            side="right"
            :anchor-start="['x' => '12rem', 'y' => '8rem']"
            arc-size="2.75rem"
            bridge-length="8rem"
            color="amber"
            :fallback-used="true"
            :fallback="true"
            :dev="true"
        />
    BLADE);

    expect($html)
        ->toContain('orders.right.1.payment-return.arc.in')
        ->toContain('orders.right.1.payment-return.bridge')
        ->toContain('orders.right.1.payment-return.arc.out')
        ->toContain('right-left')
        ->toContain('tw-graph-protocol-primitive-line-dashed')
        ->toContain('tw-graph-protocol-primitive-arc-dashed');
});

it('renders branch returns solid by default for hand authored graphs', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph.paths.branch-return
            id="orders.left.1.default-return"
            side="left"
            :anchor-start="['x' => '-12rem', 'y' => '8rem']"
            arc-size="2.75rem"
            bridge-length="8rem"
            color="green"
            :dev="true"
        />
    BLADE);

    expect($html)
        ->toContain('orders.left.1.default-return.arc.in')
        ->toContain('orders.left.1.default-return.bridge')
        ->toContain('orders.left.1.default-return.arc.out')
        ->not->toContain('tw-graph-protocol-primitive-line-dashed')
        ->not->toContain('tw-graph-protocol-primitive-arc-dashed');
});

it('renders branch return joint arrows in return flow direction', function (): void {
    $leftHtml = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph.paths.branch-return
            id="orders.left.1.default-return"
            side="left"
            :anchor-start="['x' => '-12rem', 'y' => '8rem']"
            color="green"
            :dev="true"
        />
    BLADE);
    $rightHtml = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph.paths.branch-return
            id="orders.right.1.default-return"
            side="right"
            :anchor-start="['x' => '12rem', 'y' => '8rem']"
            color="green"
            :dev="true"
        />
    BLADE);

    expect($leftHtml)
        ->toMatch('/class="[^"]*tw-graph-protocol-primitive-joint-arrow-right[^"]*"[^>]*title="orders\.left\.1\.default-return\.arc\.in\.end\.joint-arrow"/')
        ->toContain('orders.left.1.default-return.bridge.end.joint-arrow')
        ->not->toContain('orders.left.1.default-return.arc.out.end.joint-arrow')
        ->and($rightHtml)
        ->toMatch('/class="[^"]*tw-graph-protocol-primitive-joint-arrow-left[^"]*"[^>]*title="orders\.right\.1\.default-return\.arc\.in\.end\.joint-arrow"/')
        ->toContain('orders.right.1.default-return.bridge.end.joint-arrow')
        ->not->toContain('orders.right.1.default-return.arc.out.end.joint-arrow');
});

it('uses graph defaults for branch return arc and bridge geometry', function (): void {
    config()->set('tw-graph-defaults.arc_size', '4rem');
    config()->set('tw-graph-defaults.bridge_length', '6rem');

    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph.paths.branch-return
            id="orders.left.1.defaulted-return"
            side="left"
            :anchor-start="['x' => '-12rem', 'y' => '8rem']"
            color="green"
            :dev="true"
        />
    BLADE);

    expect($html)
        ->toContain('orders.left.1.defaulted-return.arc.in')
        ->toContain('orders.left.1.defaulted-return.bridge')
        ->toContain('orders.left.1.defaulted-return.arc.out')
        ->toContain('--tw-graph-protocol-local-arc-size: 4rem')
        ->toContain('--tw-graph-protocol-local-length: 6rem')
        ->toContain('--tw-graph-protocol-anchor-x: calc(-12rem + 4rem)')
        ->toContain('--tw-graph-protocol-anchor-y: calc(calc(8rem + 4rem) + 4rem)');
});

it('passes return bridge node labels through with width alignment and color', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph.paths.branch-return-bridge
            id="orders.left.1.refund-return-bridge"
            side="left"
            :anchor-start="['x' => '-16rem', 'y' => '12rem']"
            arc-size="2.75rem"
            bridge-length="10rem"
            color="rose"
            :node-labels="[
                1 => [
                    'text' => ['Return starts'],
                    'width' => 'halfLong',
                    'align' => 'right',
                ],
                2 => [
                    'top' => [
                        'text' => ['Refund queued'],
                        'width' => 'long',
                        'align' => 'left',
                    ],
                ],
            ]"
            :dev="true"
        />
    BLADE);

    expect($html)
        ->toContain('orders.left.1.refund-return-bridge.arc.label.top.1')
        ->toContain('orders.left.1.refund-return-bridge.bridge.label.top.1')
        ->toContain('Return starts')
        ->toContain('Refund queued')
        ->toContain('w-72')
        ->toContain('w-96')
        ->toContain('items-end text-right')
        ->toContain('items-start text-left')
        ->toContain('text-rose-700');
});

it('keeps labeled branch return bridge anchors as dots and unlabeled bridge anchors as joint arrows', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph.paths.branch-return-bridge
            id="orders.left.1.return-bridge"
            side="left"
            :anchor-start="['x' => '-16rem', 'y' => '12rem']"
            color="rose"
            :node-labels="[
                1 => ['text' => ['Return starts']],
            ]"
            :dev="true"
        />
    BLADE);

    expect($html)
        ->toContain('Return starts')
        ->not->toContain('.arc.end.joint-arrow')
        ->toContain('.end.joint-arrow');
});

it('keeps geometric branch returns separate from label carrying return bridges', function (): void {
    $returnHtml = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph.paths.branch-return
            id="orders.left.1.geometric-return"
            side="left"
            :anchor-start="['x' => '-12rem', 'y' => '8rem']"
            color="green"
            :dev="true"
        />
    BLADE);
    $returnBridgeHtml = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph.paths.branch-return-bridge
            id="orders.left.1.labeled-return"
            side="left"
            :anchor-start="['x' => '-12rem', 'y' => '8rem']"
            color="green"
            :node-labels="[
                1 => [
                    'text' => ['Labeled return'],
                    'width' => 'half',
                    'align' => 'center',
                ],
            ]"
            :dev="true"
        />
    BLADE);

    expect($returnHtml)
        ->toContain('orders.left.1.geometric-return.arc.in')
        ->toContain('orders.left.1.geometric-return.bridge')
        ->toContain('orders.left.1.geometric-return.arc.out')
        ->not->toContain('geometric-return.bridge.label')
        ->not->toContain('Labeled return')
        ->and($returnBridgeHtml)
        ->toContain('orders.left.1.labeled-return.arc.label.top.1')
        ->toContain('Labeled return')
        ->toContain('w-24');
});

it('uses graph defaults for branch return bridge arc and bridge geometry', function (): void {
    config()->set('tw-graph-defaults.arc_size', '4rem');
    config()->set('tw-graph-defaults.bridge_length', '6rem');

    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph.paths.branch-return-bridge
            id="orders.left.1.defaulted-return-bridge"
            side="left"
            :anchor-start="['x' => '-16rem', 'y' => '12rem']"
            color="green"
            :dev="true"
        />
    BLADE);

    expect($html)
        ->toContain('orders.left.1.defaulted-return-bridge.arc')
        ->toContain('orders.left.1.defaulted-return-bridge.bridge')
        ->toContain('--tw-graph-protocol-local-arc-size: 4rem')
        ->toContain('--tw-graph-protocol-local-length: 6rem')
        ->toContain('--tw-graph-protocol-anchor-x: calc(-16rem + 4rem)')
        ->toContain('--tw-graph-protocol-end-y: calc(12rem + 4rem)');
});

it('renders branch return extensions with outward stem arc and bridge geometry', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph.paths.branch-return-extension
            id="orders.left.1.refund-extension-return"
            side="left"
            :anchor-start="['x' => '-20rem', 'y' => '10rem']"
            arc-size="2.75rem"
            stem-length="3rem"
            bridge-length="9rem"
            color="green"
            :counter-start="4"
            :dev="true"
        />
    BLADE);

    expect($html)
        ->toContain('orders.left.1.refund-extension-return.stem')
        ->toContain('orders.left.1.refund-extension-return.arc')
        ->toContain('orders.left.1.refund-extension-return.bridge')
        ->toContain('--tw-graph-protocol-local-length: 3rem')
        ->toContain('--tw-graph-protocol-local-length: 9rem')
        ->toContain('tw-graph-protocol-primitive-line-left-right')
        ->toContain('4')
        ->toContain('5')
        ->toContain('6')
        ->toContain('text-green-800');
});

it('uses graph defaults for branch return extension geometry', function (): void {
    config()->set('tw-graph-defaults.arc_size', '4rem');
    config()->set('tw-graph-defaults.stem_length', '5rem');
    config()->set('tw-graph-defaults.bridge_length', '6rem');

    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph.paths.branch-return-extension
            id="orders.left.1.defaulted-return-extension"
            side="left"
            :anchor-start="['x' => '-20rem', 'y' => '10rem']"
            color="green"
            :counter-start="4"
            :dev="true"
        />
    BLADE);

    expect($html)
        ->toContain('orders.left.1.defaulted-return-extension.stem')
        ->toContain('orders.left.1.defaulted-return-extension.arc')
        ->toContain('orders.left.1.defaulted-return-extension.bridge')
        ->toContain('--tw-graph-protocol-local-length: 5rem')
        ->toContain('--tw-graph-protocol-local-arc-size: 4rem')
        ->toContain('--tw-graph-protocol-local-length: 6rem')
        ->toContain('--tw-graph-protocol-anchor-x: calc(-20rem + 4rem)')
        ->toContain('--tw-graph-protocol-anchor-y: calc(calc(10rem + 5rem) + 4rem)');
});

it('mirrors branch return extension geometry on the right side', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph.paths.branch-return-extension
            id="orders.right.1.defaulted-return-extension"
            side="right"
            :anchor-start="['x' => '20rem', 'y' => '10rem']"
            arc-size="4rem"
            stem-length="5rem"
            bridge-length="6rem"
            color="amber"
            :counter-start="4"
            :dev="true"
        />
    BLADE);

    expect($html)
        ->toContain('orders.right.1.defaulted-return-extension.stem')
        ->toContain('orders.right.1.defaulted-return-extension.arc')
        ->toContain('orders.right.1.defaulted-return-extension.bridge')
        ->toContain('tw-graph-protocol-primitive-line-right-left')
        ->toContain('--tw-graph-protocol-local-length: 5rem')
        ->toContain('--tw-graph-protocol-local-arc-size: 4rem')
        ->toContain('--tw-graph-protocol-local-length: 6rem')
        ->toContain('--tw-graph-protocol-anchor-x: calc(20rem + calc(4rem * -1))')
        ->toContain('--tw-graph-protocol-anchor-y: calc(calc(10rem + 5rem) + 4rem)')
        ->toContain('text-amber-700');
});

it('renders branch return extension joint arrows for unlabeled continuation anchors', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph.paths.branch-return-extension
            id="orders.right.1.return-extension"
            side="right"
            :anchor-start="['x' => '20rem', 'y' => '10rem']"
            color="amber"
            :counter-start="4"
            :dev="true"
        />
    BLADE);

    expect($html)
        ->toContain('orders.right.1.return-extension.stem.end.joint-arrow')
        ->toMatch('/class="[^"]*tw-graph-protocol-primitive-joint-arrow-left[^"]*"[^>]*title="orders\.right\.1\.return-extension\.arc\.end\.joint-arrow"/')
        ->toContain('orders.right.1.return-extension.bridge.end.joint-arrow');
});

it('mirrors return bridge node labels on right side paths', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph.paths.branch-return-bridge
            id="orders.right.1.refund-return-bridge"
            side="right"
            :anchor-start="['x' => '16rem', 'y' => '12rem']"
            arc-size="2.75rem"
            bridge-length="10rem"
            color="amber"
            :node-labels="[
                1 => [
                    'text' => ['Right return starts'],
                    'width' => 'halfLong',
                    'align' => 'left',
                ],
                2 => [
                    'bottom' => [
                        'text' => ['Back to trunk'],
                        'width' => 'half',
                        'align' => 'right',
                    ],
                ],
            ]"
            :dev="true"
        />
    BLADE);

    expect($html)
        ->toContain('orders.right.1.refund-return-bridge.arc.label.top.1')
        ->toContain('orders.right.1.refund-return-bridge.bridge.label.bottom.2')
        ->toContain('Right return starts')
        ->toContain('Back to trunk')
        ->toContain('tw-graph-protocol-primitive-line-right-left')
        ->toContain('w-72')
        ->toContain('w-24')
        ->toContain('items-start text-left')
        ->toContain('items-end text-right')
        ->toContain('text-amber-700');
});
