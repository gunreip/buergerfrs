<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

uses(TestCase::class);

it('passes named trunk node label options through to rendered path labels', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="strang-trunk-label-test" color="green" :dev="true" :coordinates="false">
            <x-translation-workbench::ui.tw-graph.strang.trunk
                id="sample.center.1.trunk"
                :stem-count="2"
                start-length="1rem"
                end-length="1rem"
                :node-labels="[
                    1 => [
                        'left' => [
                            'text' => ['Left fact', 'aligned right'],
                            'width' => 'halfLong',
                            'align' => 'right',
                        ],
                        'right' => [
                            'text' => ['Right fact', 'justified'],
                            'width' => 'long',
                            'align' => 'left',
                            'justify' => true,
                        ],
                    ],
                ]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('strang.trunk.center.1.stem-1.label.right.1')
        ->toContain('strang.trunk.center.1.stem-1.label.left.2')
        ->toContain('Right fact')
        ->toContain('justified')
        ->toContain('Left fact')
        ->toContain('aligned right')
        ->toContain('w-96')
        ->toContain('w-72')
        ->toContain('items-start text-left')
        ->toContain('items-end text-right')
        ->toContain('text-justify')
        ->toContain('text-green-800');
});

it('keeps the trunk start dev counter when the unlabeled start dot is hidden', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="strang-trunk-start-counter-test" :dev="true" :coordinates="false">
            <x-translation-workbench::ui.tw-graph.strang.trunk
                id="sample.center.1.trunk"
                :stem-count="0"
                start-length="2rem"
                end-length=""
                start-label="Trunk start"
                :start-node-labels="[]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('strang.trunk.center.1.start.anchorNode-end')
        ->toContain('Trunk start')
        ->not->toContain('tw-graph-protocol-primitive-line-node-end');
});

it('renders the trunk start dot counter and side labels when start node labels exist', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="strang-trunk-start-label-test" color="green" :dev="true" :coordinates="false">
            <x-translation-workbench::ui.tw-graph.strang.trunk
                id="sample.center.1.trunk"
                :stem-count="0"
                start-length="2rem"
                end-length=""
                start-label="Trunk start"
                :start-node-labels="[
                    'left' => [
                        'text' => ['Left start'],
                        'align' => 'right',
                    ],
                    'right' => [
                        'text' => ['Right start'],
                        'align' => 'left',
                    ],
                ]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('strang.trunk.center.1.start.anchorNode-end')
        ->toContain('tw-graph-protocol-primitive-line-node-end')
        ->toContain('strang.trunk.center.1.start.label.right.1')
        ->toContain('strang.trunk.center.1.start.label.left.2')
        ->toContain('Left start')
        ->toContain('Right start')
        ->toContain('items-end text-right')
        ->toContain('items-start text-left')
        ->toContain('strang.trunk.center.1.start.anchorNode-end')
        ->toContain(' 1');
});

it('uses public trunk stem length props for rendered path lengths', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="strang-trunk-stem-length-contract" :dev="true" :coordinates="false">
            <x-translation-workbench::ui.tw-graph.strang.trunk
                id="sample.center.1.trunk"
                stem-length="3rem"
                :stem-count="3"
                :stem-lengths="[
                    2 => '9rem',
                ]"
                start-length="1rem"
                end-length="1rem"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('strang.trunk.center.1.stem-1')
        ->toContain('strang.trunk.center.1.stem-2')
        ->toContain('strang.trunk.center.1.stem-3')
        ->toContain('--tw-graph-protocol-local-length: 3rem')
        ->toContain('--tw-graph-protocol-local-length: 9rem')
        ->not->toContain('path-length=')
        ->not->toContain('pathLengths');
});

it('keeps explicit first trunk stem lengths ahead of automatic start shift defaults', function (): void {
    config()->set('tw-graph-defaults.trunk_start_shift_enabled', true);
    config()->set('tw-graph-defaults.trunk_start_shift_length', '10rem');

    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="strang-trunk-explicit-first-stem-test" :dev="true" :coordinates="false">
            <x-translation-workbench::ui.tw-graph.strang.trunk
                id="sample.center.1.trunk"
                :stem-count="2"
                :stem-lengths="[
                    1 => '3rem',
                ]"
                stem-length="6rem"
                start-length="1rem"
                end-length="1rem"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('strang.trunk.center.1.stem-1')
        ->toContain('strang.trunk.center.1.stem-2')
        ->toContain('--tw-graph-protocol-local-length: 3rem')
        ->toContain('--tw-graph-protocol-local-length: 6rem')
        ->not->toContain('calc(3rem + 10rem)');
});

it('places trunk start and end labels according to top bottom direction', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="strang-trunk-top-bottom-label-side-test" color="green" :dev="true" :coordinates="false">
            <x-translation-workbench::ui.tw-graph.strang.trunk
                id="sample.center.1.trunk"
                direction="top-bottom"
                :stem-count="1"
                start-length="2rem"
                end-length="2rem"
                start-label="Top start"
                end-label="Bottom end"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('strang.trunk.center.1.start.label.top.1')
        ->toContain('strang.trunk.center.1.end.label.bottom.1')
        ->toContain('Top start')
        ->toContain('Bottom end')
        ->toContain('tw-graph-protocol-primitive-line-top-bottom');
});

it('uses graph stem defaults for trunk start stems and end geometry', function (): void {
    config()->set('tw-graph-defaults.stem_length', '6rem');

    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="strang-trunk-defaults-test" color="green" :dev="true" :coordinates="false">
            <x-translation-workbench::ui.tw-graph.strang.trunk
                id="sample.center.1.trunk"
                :stem-count="2"
                start-label="Start"
                end-label="End"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('strang.trunk.center.1.start')
        ->toContain('strang.trunk.center.1.stem-1')
        ->toContain('strang.trunk.center.1.stem-2')
        ->toContain('strang.trunk.center.1.end')
        ->toContain('--tw-graph-protocol-local-length: 6rem')
        ->toContain('Start')
        ->toContain('End');
});
