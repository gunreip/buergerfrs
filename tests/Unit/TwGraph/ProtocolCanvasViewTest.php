<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

uses(TestCase::class);

it('renders tw graph wrapper props as shared canvas css variables and metrics padding', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph
            graph-id="wrapper-props-test"
            color="emerald"
            line-width="0.5rem"
            node-size="1.5rem"
            arc-size="4rem"
            slot-min-height="30rem"
            horizontal-padding="18rem"
            min-width="72rem"
            min-height="88rem"
            :dev="true"
            :coordinates="true"
        >
            <x-translation-workbench::ui.tw-graph.parts.start id="wrapper.center.1.start" />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('id="wrapper-props-test"')
        ->toContain('--tw-graph-protocol-color-rgb: 16 185 129')
        ->toContain('--tw-graph-protocol-color-alpha: 0.5')
        ->toContain('--tw-graph-protocol-min-width: 72rem')
        ->toContain('--tw-graph-protocol-min-height: 88rem')
        ->toContain('--tw-graph-protocol-path-width: 0.5rem')
        ->toContain('--tw-graph-protocol-node-size: 1.5rem')
        ->toContain('--tw-graph-protocol-arc-size: 4rem')
        ->toContain('wrapper.center.1.start')
        ->toContain('tw-graph-protocol-coordinate-only')
        ->not->toContain('tw-graph-protocol-coordinates-disabled');
});

it('renders protocol trunk segments and terminal labels from resolved data', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph.canvas
            :dev="true"
            :protocol="[
                'twGraph' => [
                    'strang' => [
                        'trunk' => [
                            'trunk' => [
                                'paths' => [
                                    [
                                        'id' => 'protocol.trunk.path',
                                        'textStart' => ['Protocol start'],
                                        'textEnd' => ['Protocol end'],
                                        'segments' => [
                                            'start' => [
                                                'id' => 'protocol.trunk.start',
                                                'segment' => 'trunk-start',
                                                'anchorStart' => ['x' => '0rem', 'y' => '0rem'],
                                                'anchorEnd' => ['x' => '0rem', 'y' => '3rem'],
                                                'length' => '3rem',
                                            ],
                                            'paths' => [
                                                [
                                                    'id' => 'protocol.trunk.stem-1',
                                                    'anchorStart' => ['x' => '0rem', 'y' => '3rem'],
                                                    'anchorEnd' => ['x' => '0rem', 'y' => '7rem'],
                                                    'length' => '4rem',
                                                ],
                                            ],
                                            'end' => [
                                                'id' => 'protocol.trunk.end',
                                                'segment' => 'trunk-end',
                                                'anchorStart' => ['x' => '0rem', 'y' => '7rem'],
                                                'anchorEnd' => ['x' => '0rem', 'y' => '9rem'],
                                                'length' => '2rem',
                                                'cap' => true,
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ]"
        />
    BLADE);

    expect($html)
        ->toContain('tw-graph-protocol-canvas content-center')
        ->toContain('protocol.trunk.start')
        ->toContain('protocol.trunk.stem-1')
        ->toContain('protocol.trunk.end')
        ->toContain('protocol.trunk.path.text-start')
        ->toContain('Protocol start')
        ->toContain('protocol.trunk.path.text-end')
        ->toContain('Protocol end');
});

it('flattens merge branch and extension protocol segments without recalculating them', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph.canvas
            :dev="true"
            :protocol="[
                'twGraph' => [
                    'strang' => [
                        'merge' => [
                            'left' => [
                                'merge' => [
                                    'paths' => [
                                        'merge' => [
                                            'segments' => [
                                                [
                                                    'id' => 'protocol.merge.left.stem',
                                                    'anchorStart' => ['x' => '0rem', 'y' => '0rem'],
                                                    'anchorEnd' => ['x' => '0rem', 'y' => '4rem'],
                                                    'length' => '4rem',
                                                ],
                                            ],
                                        ],
                                        'mergeEnd' => [
                                            'id' => 'protocol.merge.left.end-path',
                                            'textEnd' => ['Merge end'],
                                            'segment' => [
                                                'id' => 'protocol.merge.left.end',
                                                'anchorStart' => ['x' => '-8rem', 'y' => '4rem'],
                                                'anchorEnd' => ['x' => '-8rem', 'y' => '6rem'],
                                                'length' => '2rem',
                                            ],
                                        ],
                                    ],
                                ],
                                'extensions' => [
                                    [
                                        'segments' => [
                                            [
                                                'id' => 'protocol.merge.left.extension.stem',
                                                'anchorStart' => ['x' => '-10rem', 'y' => '0rem'],
                                                'anchorEnd' => ['x' => '-10rem', 'y' => '4rem'],
                                                'length' => '4rem',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        'branch' => [
                            'right' => [
                                [
                                    'segments' => [
                                        [
                                            'id' => 'protocol.branch.right.stem',
                                            'anchorStart' => ['x' => '0rem', 'y' => '0rem'],
                                            'anchorEnd' => ['x' => '0rem', 'y' => '4rem'],
                                            'length' => '4rem',
                                        ],
                                    ],
                                    'extensions' => [
                                        [
                                            'segments' => [
                                                [
                                                    'id' => 'protocol.branch.right.extension.stem',
                                                    'anchorStart' => ['x' => '8rem', 'y' => '0rem'],
                                                    'anchorEnd' => ['x' => '8rem', 'y' => '4rem'],
                                                    'length' => '4rem',
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ]"
        />
    BLADE);

    expect($html)
        ->toContain('protocol.merge.left.stem')
        ->toContain('protocol.merge.left.end')
        ->toContain('protocol.merge.left.end-path.text-end')
        ->toContain('Merge end')
        ->toContain('protocol.merge.left.extension.stem')
        ->toContain('protocol.branch.right.stem')
        ->toContain('protocol.branch.right.extension.stem');
});
