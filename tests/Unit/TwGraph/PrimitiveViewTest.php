<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

uses(TestCase::class);

it('renders primitive lines with direction caps nodes dashed style and custom sizes', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph.primitives.line
            id="primitive.line.test"
            direction="right-left"
            length="9rem"
            start-x="12rem"
            start-y="4rem"
            end-x="3rem"
            end-y="4rem"
            :node-start="true"
            :node-end="true"
            node-start-size="1.25rem"
            node-end-size="1.5rem"
            :gradient="true"
            :cap-start="true"
            :cap-end="true"
            cap-length="2rem"
            :dashed="true"
            color="green"
            z-index="31"
        />
    BLADE);

    expect($html)
        ->toContain('primitive.line.test')
        ->toContain('tw-graph-protocol-primitive-line-right-left')
        ->toContain('tw-graph-protocol-primitive-line-start')
        ->toContain('tw-graph-protocol-primitive-line-cap-start')
        ->toContain('tw-graph-protocol-primitive-line-end')
        ->toContain('tw-graph-protocol-primitive-line-dashed')
        ->toContain('tw-graph-protocol-primitive-line-node-start')
        ->toContain('tw-graph-protocol-primitive-line-node-end')
        ->toContain('--tw-graph-protocol-start-x: 12rem')
        ->toContain('--tw-graph-protocol-end-x: 3rem')
        ->toContain('--tw-graph-protocol-local-length: 9rem')
        ->toContain('--tw-graph-protocol-line-node-start-size: 1.25rem')
        ->toContain('--tw-graph-protocol-line-node-end-size: 1.5rem')
        ->toContain('--tw-graph-protocol-line-end-cap-length: 2rem')
        ->toContain('--tw-graph-protocol-local-color-rgb: 44 144 103')
        ->toContain('--tw-graph-protocol-z-index: 31');
});

it('renders primitive line surface tone without changing the semantic color', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph.primitives.line
            id="primitive.line.surface"
            color="cyan"
            tone="surface"
        />
    BLADE);

    expect($html)
        ->toContain('primitive.line.surface')
        ->toContain('tw-graph-protocol-tone-surface')
        ->toContain('--tw-graph-protocol-local-color-rgb: 6 182 212')
        ->toContain('--tw-graph-protocol-local-surface-color-rgb: 222 248 252')
        ->toContain('--tw-graph-protocol-local-dark-surface-color-rgb: 14 98 113');
});

it('renders primitive arc transition color variables for surface tone', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph.primitives.arc
            id="primitive.arc.transition"
            color="cyan"
            to-color="amber"
            tone="surface"
        />
    BLADE);

    expect($html)
        ->toContain('primitive.arc.transition')
        ->toContain('tw-graph-protocol-tone-surface')
        ->toContain('--tw-graph-protocol-local-surface-color-rgb: 222 248 252')
        ->toContain('--tw-graph-protocol-local-to-surface-color-rgb: 254 245 222')
        ->toContain('--tw-graph-protocol-local-dark-surface-color-rgb: 14 98 113')
        ->toContain('--tw-graph-protocol-local-to-dark-surface-color-rgb: 115 91 31');
});

it('renders primitive arcs with semantic corner classes and explicit node anchors', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph.primitives.arc
            id="primitive.arc.test"
            start-anchor="e"
            end-anchor="n"
            start-x="6rem"
            start-y="2rem"
            end-x="6rem"
            end-y="5rem"
            arc-size="3rem"
            :node-start="true"
            :node-end="true"
            node-start-size="1rem"
            node-end-size="1.2rem"
            :dashed="true"
            color="amber"
            z-index="22"
        />
    BLADE);

    expect($html)
        ->toContain('primitive.arc.test')
        ->toContain('tw-graph-protocol-primitive-arc-ne')
        ->toContain('tw-graph-protocol-primitive-arc-dashed')
        ->toContain('--tw-graph-protocol-local-arc-size: 3rem')
        ->toContain('--tw-graph-protocol-local-color-rgb: 245 158 11')
        ->toContain('--tw-graph-protocol-z-index: 22')
        ->toContain('primitive.arc.test.node.start')
        ->toContain('primitive.arc.test.node.end')
        ->toContain('--tw-graph-protocol-local-node-size: 1rem')
        ->toContain('--tw-graph-protocol-local-node-size: 1.2rem');
});

it('renders primitive nodes connectors counters and wavy lines with direct props', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph.primitives.node
            id="primitive.node.test"
            anchor-x="-2rem"
            anchor-y="8rem"
            size="1.75rem"
            color="rose"
        />
        <x-translation-workbench::ui.tw-graph.primitives.connector
            id="primitive.connector.test"
            placement="left"
            anchor-x="-2rem"
            anchor-y="8rem"
            length="3rem"
            gap="0.5rem"
            color="green"
        />
        <x-translation-workbench::ui.tw-graph.primitives.dev-node-counter
            id="primitive.counter.test"
            :dev="true"
            counter="12"
            anchor-x="-2rem"
            anchor-y="8rem"
            offset-x="1rem"
            offset-y="-1rem"
            color="amber"
        />
        <x-translation-workbench::ui.tw-graph.primitives.dev-node-counter
            id="primitive.counter.hidden"
            :dev="false"
            counter="13"
        />
        <x-translation-workbench::ui.tw-graph.primitives.wavy-line
            id="primitive.wavy.test"
            direction="bottom-top"
            start-x="1rem"
            start-y="2rem"
            length="2.5rem"
            color="sky"
            z-index="19"
        />
    BLADE);

    expect($html)
        ->toContain('primitive.node.test')
        ->toContain('--tw-graph-protocol-anchor-x: -2rem')
        ->toContain('--tw-graph-protocol-anchor-y: 8rem')
        ->toContain('--tw-graph-protocol-local-node-size: 1.75rem')
        ->toContain('--tw-graph-protocol-local-color-rgb: 244 63 94')
        ->toContain('primitive.connector.test')
        ->toContain('tw-graph-protocol-primitive-connector-left')
        ->toContain('--tw-graph-protocol-connector-length: 3rem')
        ->toContain('--tw-graph-protocol-connector-anchor-gap: 0.5rem')
        ->toContain('primitive.counter.test')
        ->toContain('tw-graph-protocol-primitive-dev-node-counter-offset')
        ->toContain('--tw-graph-protocol-dev-node-counter-offset-x: 1rem')
        ->toContain('--tw-graph-protocol-dev-node-counter-offset-y: -1rem')
        ->toContain('12')
        ->not->toContain('primitive.counter.hidden')
        ->toContain('primitive.wavy.test')
        ->toContain('tw-graph-protocol-primitive-wavy-line-bottom-top')
        ->toContain('--tw-graph-protocol-local-length: 2.5rem')
        ->toContain('--tw-graph-protocol-z-index: 19')
        ->toContain('~~~~~~~~~~~~~~~~');
});

it('renders primitive text labels with fixed width variants and alignment styles', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph.primitives.text
            id="primitive.text.half"
            text="Half"
            :half="true"
            align="left"
            badge-color="amber"
        />
        <x-translation-workbench::ui.tw-graph.primitives.text
            id="primitive.text.default"
            text="Default"
            align="center"
            badge-color="green"
        />
        <x-translation-workbench::ui.tw-graph.primitives.text
            id="primitive.text.half-long"
            text="Half long"
            :half-long="true"
            align="right"
            badge-color="sky"
        />
        <x-translation-workbench::ui.tw-graph.primitives.text
            id="primitive.text.long"
            text="Long"
            :long="true"
            align="right"
            badge-color="rose"
            z-index="32"
        />
    BLADE);

    expect($html)
        ->toContain('primitive.text.half')
        ->toContain('primitive.text.default')
        ->toContain('primitive.text.half-long')
        ->toContain('primitive.text.long')
        ->toContain('w-24')
        ->toContain('w-48')
        ->toContain('w-72')
        ->toContain('w-96')
        ->toContain('items-start text-left')
        ->toContain('items-center text-center')
        ->toContain('items-end text-right')
        ->toContain('--tw-graph-protocol-z-index: 32')
        ->toContain('style="text-align: left;"')
        ->toContain('style="text-align: center;"')
        ->toContain('style="text-align: right;"');
});

it('renders primitive node images with anchor size color alt and z index', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph.primitives.node-image
            id="primitive.node-image.test"
            source="/vendor/translation-workbench/img/einstein.png"
            size="4rem"
            anchor-x="-3rem"
            anchor-y="11rem"
            alt="Portrait"
            color="green"
            z-index="44"
        />
    BLADE);

    expect($html)
        ->toContain('primitive.node-image.test')
        ->toContain('tw-graph-protocol-primitive-node-image')
        ->toContain('--tw-graph-protocol-anchor-x: -3rem')
        ->toContain('--tw-graph-protocol-anchor-y: 11rem')
        ->toContain('--tw-graph-protocol-node-image-size: 4rem')
        ->toContain('--tw-graph-protocol-local-color-rgb: 44 144 103')
        ->toContain('--tw-graph-protocol-z-index: 44')
        ->toContain('src="/vendor/translation-workbench/img/einstein.png"')
        ->toContain('alt="Portrait"')
        ->toContain('data-tw-graph-path="primitive.node-image.test"');
});

it('does not render primitive node images without a source', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph.primitives.node-image
            id="primitive.node-image.empty"
            source=""
        />
    BLADE);

    expect($html)
        ->not->toContain('primitive.node-image.empty')
        ->not->toContain('tw-graph-protocol-primitive-node-image');
});

it('renders justified primitive text with hard wrapping and capped visible lines', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph.primitives.text
            id="primitive.text.justified"
            :text="['First', 'Second', 'Third', 'Fourth']"
            align="right"
            :justify="true"
            :max-lines="3"
        />
    BLADE);

    expect($html)
        ->toContain('primitive.text.justified')
        ->toContain('hyphens-auto')
        ->toContain('text-justify')
        ->toContain('block w-full')
        ->toContain('style="text-align: justify;"')
        ->toContain('First')
        ->toContain('Second')
        ->toContain('Third')
        ->not->toContain('Fourth');
});

it('renders mixed primitive text lines without leaking array values', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph.primitives.text
            id="primitive.text.mixed-lines"
            :text="[
                ['ordinal' => ['number' => 1, 'suffix' => 'st'], 'text' => ' sample event'],
                'event ID #38512',
                '2026-07-22 20:26',
            ]"
            badge-color="amber"
            :half-long="true"
        />
    BLADE);

    expect($html)
        ->toContain('primitive.text.mixed-lines')
        ->toContain('<span>1</span><sup')
        ->toContain('st')
        ->toContain('sample event')
        ->toContain('event ID #38512')
        ->toContain('2026-07-22 20:26')
        ->toContain('w-72')
        ->not->toContain('Array')
        ->not->toContain('htmlspecialchars');
});
