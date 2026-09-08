<?php

declare(strict_types=1);

use Gunreip\TranslationWorkbench\Support\TwGraphProtocol\GeometryResolver;

it('resolves sequential trunk segment anchors from directions and lengths', function (): void {
    $protocol = (new GeometryResolver())->resolve([
        'twGraph' => [
            'strang' => [
                'trunk' => [
                    'trunk' => [
                        'paths' => [
                            [
                                'id' => 'path-1',
                                'segments' => [
                                    'start' => [
                                        'id' => 'start',
                                        'direction' => 'bottom-top',
                                        'length' => '4rem',
                                    ],
                                    'paths' => [
                                        [
                                            'id' => 'stem-1',
                                            'direction' => 'bottom-top',
                                            'length' => '6rem',
                                        ],
                                        [
                                            'id' => 'bridge-1',
                                            'direction' => 'left-right',
                                            'length' => '8rem',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ]);

    expect(data_get($protocol, 'twGraph.strang.trunk.trunk.paths.0.segments.start.anchorStart'))
        ->toBe(['x' => '0rem', 'y' => '0rem'])
        ->and(data_get($protocol, 'twGraph.strang.trunk.trunk.paths.0.segments.start.anchorEnd'))
        ->toBe(['x' => '0rem', 'y' => '4rem'])
        ->and(data_get($protocol, 'twGraph.strang.trunk.trunk.paths.0.segments.paths.0.anchorEnd'))
        ->toBe(['x' => '0rem', 'y' => '10rem'])
        ->and(data_get($protocol, 'twGraph.strang.trunk.trunk.paths.0.segments.paths.1.anchorEnd'))
        ->toBe(['x' => '8rem', 'y' => '10rem']);
});

it('reuses cached trunk path coordinates when fingerprints still match', function (): void {
    $plan = [
        'twGraph' => [
            'strang' => [
                'trunk' => [
                    'trunk' => [
                        'paths' => [
                            [
                                'id' => 'path-1',
                                'segments' => [
                                    'paths' => [
                                        [
                                            'id' => 'stem-1',
                                            'direction' => 'bottom-top',
                                            'length' => '6rem',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ];

    $resolver = new GeometryResolver();
    $firstProtocol = $resolver->resolve($plan);
    $cachedProtocol = $firstProtocol;
    data_set($cachedProtocol, 'twGraph.strang.trunk.trunk.paths.0.segments.paths.0.anchorStart', ['x' => '2rem', 'y' => '3rem']);
    data_set($cachedProtocol, 'twGraph.strang.trunk.trunk.paths.0.segments.paths.0.anchorEnd', ['x' => '2rem', 'y' => '9rem']);

    $secondProtocol = $resolver->resolve($plan, $cachedProtocol);

    expect(data_get($secondProtocol, 'twGraph.strang.trunk.trunk.paths.0.segments.paths.0.anchorStart'))
        ->toBe(['x' => '2rem', 'y' => '3rem'])
        ->and(data_get($secondProtocol, 'twGraph.strang.trunk.trunk.paths.0.segments.paths.0.anchorEnd'))
        ->toBe(['x' => '2rem', 'y' => '9rem']);
});

it('recalculates cached trunk path coordinates when fingerprints changed', function (): void {
    $plan = [
        'twGraph' => [
            'strang' => [
                'trunk' => [
                    'trunk' => [
                        'paths' => [
                            [
                                'id' => 'path-1',
                                'segments' => [
                                    'paths' => [
                                        [
                                            'id' => 'stem-1',
                                            'direction' => 'bottom-top',
                                            'length' => '6rem',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ];

    $resolver = new GeometryResolver();
    $cachedProtocol = $resolver->resolve($plan);
    data_set($cachedProtocol, 'twGraph.strang.trunk.trunk.paths.0.segments.paths.0.anchorStart', ['x' => '2rem', 'y' => '3rem']);
    data_set($cachedProtocol, 'twGraph.strang.trunk.trunk.paths.0.segments.paths.0.anchorEnd', ['x' => '2rem', 'y' => '9rem']);

    data_set($plan, 'twGraph.strang.trunk.trunk.paths.0.segments.paths.0.length', '7rem');
    $resolvedProtocol = $resolver->resolve($plan, $cachedProtocol);

    expect(data_get($resolvedProtocol, 'twGraph.strang.trunk.trunk.paths.0.segments.paths.0.anchorStart'))
        ->toBe(['x' => '0rem', 'y' => '0rem'])
        ->and(data_get($resolvedProtocol, 'twGraph.strang.trunk.trunk.paths.0.segments.paths.0.anchorEnd'))
        ->toBe(['x' => '0rem', 'y' => '7rem']);
});

it('recalculates later segment coordinates when an earlier sibling changes the chain cursor', function (): void {
    $plan = [
        'twGraph' => [
            'strang' => [
                'trunk' => [
                    'trunk' => [
                        'paths' => [
                            [
                                'id' => 'path-1',
                                'segments' => [
                                    'paths' => [
                                        [
                                            'id' => 'stem-1',
                                            'direction' => 'bottom-top',
                                            'length' => '6rem',
                                        ],
                                        [
                                            'id' => 'stem-2',
                                            'direction' => 'bottom-top',
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
    ];

    $resolver = new GeometryResolver();
    $cachedProtocol = $resolver->resolve($plan);
    data_set($cachedProtocol, 'twGraph.strang.trunk.trunk.paths.0.segments.paths.1.anchorStart', ['x' => '3rem', 'y' => '11rem']);
    data_set($cachedProtocol, 'twGraph.strang.trunk.trunk.paths.0.segments.paths.1.anchorEnd', ['x' => '3rem', 'y' => '15rem']);

    data_set($plan, 'twGraph.strang.trunk.trunk.paths.0.segments.paths.0.length', '7rem');
    $resolvedProtocol = $resolver->resolve($plan, $cachedProtocol);

    expect(data_get($resolvedProtocol, 'twGraph.strang.trunk.trunk.paths.0.segments.paths.0.anchorEnd'))
        ->toBe(['x' => '0rem', 'y' => '7rem'])
        ->and(data_get($resolvedProtocol, 'twGraph.strang.trunk.trunk.paths.0.segments.paths.1.anchorStart'))
        ->toBe(['x' => '0rem', 'y' => '7rem'])
        ->and(data_get($resolvedProtocol, 'twGraph.strang.trunk.trunk.paths.0.segments.paths.1.anchorEnd'))
        ->toBe(['x' => '0rem', 'y' => '11rem']);
});

it('resolves semantic arc anchors from start and end anchor names', function (): void {
    $protocol = (new GeometryResolver())->resolve([
        'twGraph' => [
            'strang' => [
                'trunk' => [
                    'trunk' => [
                        'paths' => [
                            [
                                'segments' => [
                                    'paths' => [
                                        [
                                            'id' => 'arc-1',
                                            'type' => 'arc',
                                            'startAnchor' => 'w',
                                            'endAnchor' => 'n',
                                            'arcSpan' => '3rem',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ]);

    expect(data_get($protocol, 'twGraph.strang.trunk.trunk.paths.0.segments.paths.0.anchorEnd'))
        ->toBe(['x' => '3rem', 'y' => '3rem']);
});

it('resolves merge chains from a previously resolved trunk anchor', function (): void {
    $protocol = (new GeometryResolver())->resolve([
        'twGraph' => [
            'strang' => [
                'trunk' => [
                    'trunk' => [
                        'paths' => [
                            [
                                'segments' => [
                                    'paths' => [
                                        [
                                            'id' => 'trunk-stem-1',
                                            'direction' => 'bottom-top',
                                            'length' => '10rem',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'merge' => [
                    'left' => [
                        'merge' => [
                            'anchorFrom' => [
                                'segmentId' => 'trunk-stem-1',
                                'anchor' => 'anchorEnd',
                            ],
                            'paths' => [
                                'merge' => [
                                    'segments' => [
                                        [
                                            'id' => 'merge-bridge-1',
                                            'direction' => 'right-left',
                                            'length' => '6rem',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ]);

    expect(data_get($protocol, 'twGraph.strang.merge.left.merge.paths.merge.segments.0.anchorStart'))
        ->toBe(['x' => '0rem', 'y' => '10rem'])
        ->and(data_get($protocol, 'twGraph.strang.merge.left.merge.paths.merge.segments.0.anchorEnd'))
        ->toBe(['x' => '-6rem', 'y' => '10rem']);
});

it('resolves branch chains from an explicit chain anchor when no source segment exists', function (): void {
    $protocol = (new GeometryResolver())->resolve([
        'twGraph' => [
            'strang' => [
                'branch' => [
                    'right' => [
                        [
                            'anchorStart' => ['x' => '4rem', 'y' => '8rem'],
                            'segments' => [
                                [
                                    'id' => 'branch-stem-1',
                                    'direction' => 'bottom-top',
                                    'length' => '5rem',
                                ],
                                [
                                    'id' => 'branch-bridge-1',
                                    'direction' => 'left-right',
                                    'length' => '7rem',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ]);

    expect(data_get($protocol, 'twGraph.strang.branch.right.0.segments.0.anchorStart'))
        ->toBe(['x' => '4rem', 'y' => '8rem'])
        ->and(data_get($protocol, 'twGraph.strang.branch.right.0.segments.0.anchorEnd'))
        ->toBe(['x' => '4rem', 'y' => '13rem'])
        ->and(data_get($protocol, 'twGraph.strang.branch.right.0.segments.1.anchorEnd'))
        ->toBe(['x' => '11rem', 'y' => '13rem']);
});

it('falls back to zero anchors when anchor from references a missing segment', function (): void {
    $protocol = (new GeometryResolver())->resolve([
        'twGraph' => [
            'strang' => [
                'merge' => [
                    'left' => [
                        'merge' => [
                            'anchorFrom' => [
                                'segmentId' => 'missing-segment',
                                'anchor' => 'anchorEnd',
                            ],
                            'paths' => [
                                'merge' => [
                                    'segments' => [
                                        [
                                            'id' => 'merge-bridge-1',
                                            'direction' => 'right-left',
                                            'length' => '6rem',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ]);

    expect(data_get($protocol, 'twGraph.strang.merge.left.merge.paths.merge.segments.0.anchorStart'))
        ->toBe(['x' => '0rem', 'y' => '0rem'])
        ->and(data_get($protocol, 'twGraph.strang.merge.left.merge.paths.merge.segments.0.anchorEnd'))
        ->toBe(['x' => '-6rem', 'y' => '0rem']);
});

it('resolves top bottom and right left path chains from the current cursor', function (): void {
    $protocol = (new GeometryResolver())->resolve([
        'twGraph' => [
            'strang' => [
                'branch' => [
                    'left' => [
                        [
                            'anchorStart' => ['x' => '12rem', 'y' => '20rem'],
                            'segments' => [
                                [
                                    'id' => 'branch-down',
                                    'direction' => 'top-bottom',
                                    'length' => '5rem',
                                ],
                                [
                                    'id' => 'branch-left',
                                    'direction' => 'right-left',
                                    'length' => '7rem',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ]);

    expect(data_get($protocol, 'twGraph.strang.branch.left.0.segments.0.anchorStart'))
        ->toBe(['x' => '12rem', 'y' => '20rem'])
        ->and(data_get($protocol, 'twGraph.strang.branch.left.0.segments.0.anchorEnd'))
        ->toBe(['x' => '12rem', 'y' => '15rem'])
        ->and(data_get($protocol, 'twGraph.strang.branch.left.0.segments.1.anchorStart'))
        ->toBe(['x' => '12rem', 'y' => '15rem'])
        ->and(data_get($protocol, 'twGraph.strang.branch.left.0.segments.1.anchorEnd'))
        ->toBe(['x' => '5rem', 'y' => '15rem']);
});

it('continues merge end segments from the last resolved merge segment', function (): void {
    $protocol = (new GeometryResolver())->resolve([
        'twGraph' => [
            'strang' => [
                'trunk' => [
                    'trunk' => [
                        'paths' => [
                            [
                                'segments' => [
                                    'paths' => [
                                        [
                                            'id' => 'trunk-stem-1',
                                            'direction' => 'bottom-top',
                                            'length' => '10rem',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'merge' => [
                    'right' => [
                        'merge' => [
                            'anchorFrom' => [
                                'segmentId' => 'trunk-stem-1',
                                'anchor' => 'anchorEnd',
                            ],
                            'paths' => [
                                'merge' => [
                                    'segments' => [
                                        [
                                            'id' => 'merge-bridge-1',
                                            'direction' => 'left-right',
                                            'length' => '6rem',
                                        ],
                                    ],
                                ],
                                'mergeEnd' => [
                                    'segment' => [
                                        'id' => 'merge-end-stem',
                                        'direction' => 'bottom-top',
                                        'length' => '3rem',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ]);

    expect(data_get($protocol, 'twGraph.strang.merge.right.merge.paths.merge.segments.0.anchorEnd'))
        ->toBe(['x' => '6rem', 'y' => '10rem'])
        ->and(data_get($protocol, 'twGraph.strang.merge.right.merge.paths.mergeEnd.segment.anchorStart'))
        ->toBe(['x' => '6rem', 'y' => '10rem'])
        ->and(data_get($protocol, 'twGraph.strang.merge.right.merge.paths.mergeEnd.segment.anchorEnd'))
        ->toBe(['x' => '6rem', 'y' => '13rem']);
});
