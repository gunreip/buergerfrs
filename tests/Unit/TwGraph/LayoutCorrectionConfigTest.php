<?php

use Gunreip\TranslationWorkbench\Support\TwGraph\DataDriven\TimelineChainGraphData\LayoutCorrectionConfig;
use Tests\TestCase;

uses(TestCase::class);

it('normalizes flat and trunk layout corrections from config', function (): void {
    config()->set('tw-graph-data-driven-layout-corrections', [
        'corrections' => [
            [
                'target' => 'strang.branch-left.1.main.path.branch.bridge1',
                'prop' => 'bridge-length',
                'delta' => '4rem',
                'reason' => 'Move branch outward.',
            ],
            [
                'target' => '',
                'prop' => 'stem-length',
                'delta' => '2rem',
            ],
        ],
        'strang.trunk' => [
            'stem' => [
                3 => '2rem',
            ],
        ],
    ]);

    expect(LayoutCorrectionConfig::forDataDriven())->toBe([
        [
            'target' => 'strang.left.1.branch.bridge1',
            'prop' => 'bridge_length',
            'delta' => '4rem',
            'value' => null,
            'reason' => 'Move branch outward.',
        ],
        [
            'target' => 'strang.trunk.1.stem3',
            'prop' => 'length',
            'delta' => '2rem',
            'value' => null,
            'reason' => 'Configured data-driven trunk path delta.',
        ],
    ]);
});

it('keeps graph lookup graph-family based instead of timeline-chain specific', function (): void {
    config()->set('tw-graph-data-driven-layout-corrections', [
        'corrections' => [
            [
                'target' => 'strang.branch-left.1.main.path.branch.bridge1',
                'prop' => 'bridge-length',
                'delta' => '+4rem',
                'reason' => 'Graph family correction.',
            ],
        ],
    ]);

    expect(LayoutCorrectionConfig::forGraph('timeline-chain-1014-data-preview'))
        ->toBe(LayoutCorrectionConfig::forDataDriven());
});

it('applies trunk path corrections as deltas or explicit values', function (): void {
    $result = LayoutCorrectionConfig::applyToTrunkPathLengths([
        1 => '5rem',
        2 => ['length' => '6rem', 'label' => 'kept'],
        3 => ['7rem', 'top' => 'kept'],
    ], [
        [
            'target' => 'strang.trunk.1.main.path.trunk.path1',
            'prop' => 'stem_length',
            'delta' => '2.5rem',
            'value' => null,
            'reason' => 'nudge',
        ],
        [
            'target' => 'strang.trunk.1.main.path.trunk.path2',
            'prop' => 'length',
            'delta' => null,
            'value' => '12rem',
            'reason' => 'explicit',
        ],
        [
            'target' => 'strang.trunk.1.main.path.trunk.path3',
            'prop' => 'length',
            'delta' => '-1rem',
            'value' => null,
            'reason' => 'compact',
        ],
    ], '5rem');

    expect($result['path_lengths'])->toBe([
        1 => '7.5rem',
        2 => ['length' => '12rem', 'label' => 'kept'],
        3 => ['6rem', 'top' => 'kept'],
    ])->and($result['applied'])->toHaveCount(3);
});

it('applies trunk path corrections to compact canonical stem targets', function (): void {
    $result = LayoutCorrectionConfig::applyToTrunkPathLengths([
        3 => '5rem',
    ], [
        [
            'target' => 'strang.trunk.1.stem3',
            'prop' => 'length',
            'delta' => '4rem',
            'value' => null,
            'reason' => 'canonical target',
        ],
    ], '5rem');

    expect($result['path_lengths'])->toBe([
        3 => '9rem',
    ])->and(data_get($result, 'applied.0.target'))->toBe('strang.trunk.1.stem3')
        ->and(data_get($result, 'applied.0.baseValue'))->toBe('5rem')
        ->and(data_get($result, 'applied.0.effectiveValue'))->toBe('9rem');
});

it('ignores invalid correction targets and leaves trunk path entries unchanged', function (): void {
    $pathLengths = [
        1 => '5rem',
        2 => ['length' => '6rem', 'label' => 'kept'],
    ];

    $result = LayoutCorrectionConfig::applyToTrunkPathLengths($pathLengths, [
        [
            'target' => 'strang.branch-left.1.main.path.branch.bridge1',
            'prop' => 'bridge_length',
            'delta' => '2rem',
            'value' => null,
            'reason' => 'wrong target family',
        ],
        [
            'target' => 'strang.trunk.1.main.path.trunk.path2',
            'prop' => 'color',
            'delta' => '2rem',
            'value' => null,
            'reason' => 'wrong prop',
        ],
    ], '5rem');

    expect($result['path_lengths'])->toBe($pathLengths)
        ->and($result['applied'])->toBe([]);
});

it('applies preview corrections using canonical preview ids', function (): void {
    config()->set('tw-graph-defaults.bridge_length', '18rem');
    config()->set('tw-graph-data-driven-defaults.bridge_length', '20rem');

    $previews = LayoutCorrectionConfig::applyToPreviews([
        [
            'component' => 'merge-left',
            'side' => 'left',
            'component_counter' => 1,
        ],
        [
            'id' => 'strang.branch-right.2.main.path.branch.bridge1',
            'bridge_length' => '24rem',
        ],
    ], [
        [
            'target' => 'strang.merge-left.1',
            'prop' => 'bridge_length',
            'delta' => '3rem',
            'value' => null,
            'reason' => 'calculated after defaults',
        ],
        [
            'target' => 'strang.branch-right.2.main.path.branch.bridge1',
            'prop' => 'bridge_length',
            'delta' => null,
            'value' => '30rem',
            'reason' => 'manual final nudge',
        ],
    ]);

    expect($previews[0]['bridge_length'])->toBe('23rem')
        ->and(data_get($previews[0], 'layout.appliedCorrections.0.baseValue'))->toBe('20rem')
        ->and($previews[1]['bridge_length'])->toBe('30rem')
        ->and(data_get($previews[1], 'layout.appliedCorrections.0.baseValue'))->toBe('24rem');
});

it('uses explicit preview length values as correction base ahead of graph defaults', function (): void {
    config()->set('tw-graph-defaults.bridge_length', '18rem');
    config()->set('tw-graph-data-driven-defaults.bridge_length', '20rem');
    config()->set('tw-graph-data-driven-defaults.stem_length', '8rem');

    $previews = LayoutCorrectionConfig::applyToPreviews([
        [
            'component' => 'merge-right',
            'side' => 'right',
            'component_counter' => 1,
            'bridge_length' => '14rem',
            'stem_length' => '6rem',
        ],
    ], [
        [
            'target' => 'strang.merge.right.1.bridge1',
            'prop' => 'bridge_length',
            'delta' => '3rem',
            'value' => null,
            'reason' => 'explicit bridge wins',
        ],
        [
            'target' => 'strang.merge.right.1.stem1',
            'prop' => 'stem_length',
            'delta' => '2rem',
            'value' => null,
            'reason' => 'explicit stem wins',
        ],
    ]);

    expect($previews[0]['bridge_length'])->toBe('17rem')
        ->and($previews[0]['stem_length'])->toBe('8rem')
        ->and(data_get($previews[0], 'layout.appliedCorrections.0'))->toMatchArray([
            'target' => 'strang.merge.right.1.bridge1',
            'prop' => 'bridge_length',
            'baseValue' => '14rem',
            'effectiveValue' => '17rem',
        ])
        ->and(data_get($previews[0], 'layout.appliedCorrections.1'))->toMatchArray([
            'target' => 'strang.merge.right.1.stem1',
            'prop' => 'stem_length',
            'baseValue' => '6rem',
            'effectiveValue' => '8rem',
        ]);
});

it('applies preview corrections to generated rekey preview ids', function (): void {
    config()->set('tw-graph-defaults.stem_length', '6rem');
    config()->set('tw-graph-data-driven-defaults.stem_length', '8rem');

    $previews = LayoutCorrectionConfig::applyToPreviews([
        [
            'component' => 'rekey-target-right',
            'side' => 'right',
            'component_counter' => 2,
        ],
        [
            'component' => 'rekey-source-left',
            'side' => 'left',
            'component_counter' => 1,
            'end_length' => '10rem',
        ],
    ], [
        [
            'target' => 'strang.rekey-target-right.2',
            'prop' => 'stem_length',
            'delta' => '2rem',
            'value' => null,
            'reason' => 'target nudge',
        ],
        [
            'target' => 'strang.rekey-source-left.1',
            'prop' => 'end_length',
            'delta' => '-3rem',
            'value' => null,
            'reason' => 'source nudge',
        ],
    ]);

    expect($previews[0]['stem_length'])->toBe('10rem')
        ->and(data_get($previews[0], 'layout.appliedCorrections.0.baseValue'))->toBe('8rem')
        ->and($previews[1]['end_length'])->toBe('7rem')
        ->and(data_get($previews[1], 'layout.appliedCorrections.0.baseValue'))->toBe('10rem');
});

it('applies preview corrections to compact canonical branch and rekey targets', function (): void {
    config()->set('tw-graph-data-driven-defaults.bridge_length', '10rem');

    $previews = LayoutCorrectionConfig::applyToPreviews([
        [
            'component' => 'branch-left',
            'side' => 'left',
            'component_counter' => 3,
        ],
        [
            'component' => 'rekey-source-right',
            'side' => 'right',
            'component_counter' => 4,
        ],
    ], [
        [
            'target' => 'strang.left.3.branch.bridge1',
            'prop' => 'bridge_length',
            'delta' => '5rem',
            'value' => null,
            'reason' => 'compact branch target',
        ],
        [
            'target' => 'strang.right.4.rekey.source.bridge1',
            'prop' => 'bridge_length',
            'delta' => '7rem',
            'value' => null,
            'reason' => 'compact rekey target',
        ],
    ]);

    expect($previews[0]['bridge_length'])->toBe('15rem')
        ->and(data_get($previews[0], 'layout.appliedCorrections.0.target'))->toBe('strang.left.3.branch.bridge1')
        ->and($previews[1]['bridge_length'])->toBe('17rem')
        ->and(data_get($previews[1], 'layout.appliedCorrections.0.target'))->toBe('strang.right.4.rekey.source.bridge1');
});

it('applies preview corrections to nested continuation props by canonical target', function (): void {
    config()->set('tw-graph-data-driven-layout-corrections', [
        'corrections' => [
            [
                'target' => 'strang.merge.left.1.extension.3.stem1',
                'prop' => 'extension-stem-continuations.3.1.length',
                'delta' => '4rem',
                'reason' => 'nested continuation nudge',
            ],
        ],
    ]);

    $previews = LayoutCorrectionConfig::applyToPreviews([
        [
            'component' => 'merge-left',
            'side' => 'left',
            'component_counter' => 1,
            'extension_stem_continuations' => [
                3 => [
                    1 => [
                        'length' => '8rem',
                        'labels' => true,
                    ],
                ],
            ],
        ],
    ], LayoutCorrectionConfig::forDataDriven());

    expect(data_get($previews, '0.extension_stem_continuations.3.1.length'))->toBe('12rem')
        ->and(data_get($previews, '0.extension_stem_continuations.3.1.labels'))->toBeTrue()
        ->and(data_get($previews, '0.layout.appliedCorrections.0'))->toMatchArray([
            'target' => 'strang.left.1.merge.extension.3.stem1',
            'prop' => 'extension_stem_continuations.3.1.length',
            'baseValue' => '8rem',
            'effectiveValue' => '12rem',
        ]);
});

it('collects applied corrections from multiple preview groups in render order', function (): void {
    $applied = LayoutCorrectionConfig::appliedCorrections([
        [
            'layout' => [
                'appliedCorrections' => [
                    ['target' => 'strang.left.1.branch.bridge1'],
                ],
            ],
        ],
    ], [
        [
            'layout' => [
                'appliedCorrections' => [
                    ['target' => 'strang.right.1.rekey.target.bridge1'],
                ],
            ],
        ],
    ]);

    expect($applied)->toBe([
        ['target' => 'strang.left.1.branch.bridge1'],
        ['target' => 'strang.right.1.rekey.target.bridge1'],
    ]);
});

it('reports the highest corrected trunk path number from canonical and legacy targets', function (): void {
    $corrections = [
        [
            'target' => 'strang.trunk.1.main.path.trunk.path3',
            'prop' => 'length',
            'delta' => '2rem',
            'value' => null,
            'reason' => '',
        ],
        [
            'target' => 'strang.trunk.1.stem7',
            'prop' => 'stem_length',
            'delta' => '1rem',
            'value' => null,
            'reason' => '',
        ],
        [
            'target' => 'strang.branch-left.1.main.path.branch.bridge1',
            'prop' => 'bridge_length',
            'delta' => '4rem',
            'value' => null,
            'reason' => '',
        ],
    ];

    expect(LayoutCorrectionConfig::maxTrunkPathNumber($corrections))->toBe(7);
});

it('leaves preview entries without a stable id untouched', function (): void {
    $previews = [
        ['component' => 'custom-widget', 'bridge_length' => '10rem'],
        ['side' => 'left', 'bridge_length' => '12rem'],
    ];

    expect(LayoutCorrectionConfig::applyToPreviews($previews, [
        [
            'target' => 'strang.merge-left.1',
            'prop' => 'bridge_length',
            'delta' => '4rem',
            'value' => null,
            'reason' => 'requires stable preview id',
        ],
    ]))->toBe($previews);
});

it('applies preview corrections to nested continuation length targets', function (): void {
    $previews = LayoutCorrectionConfig::applyToPreviews([
        [
            'component' => 'merge-left',
            'side' => 'left',
            'component_counter' => 1,
            'extension_stem_continuations' => [
                3 => [
                    1 => [
                        'length' => '8rem',
                    ],
                ],
            ],
        ],
    ], [
        [
            'target' => 'strang.merge.left.1',
            'prop' => 'extension_stem_continuations.3.1.length',
            'delta' => '2.5rem',
            'value' => null,
            'reason' => 'Nested aggregate stem nudge.',
        ],
    ]);

    expect(data_get($previews, '0.extension_stem_continuations.3.1.length'))->toBe('10.5rem')
        ->and(data_get($previews, '0.layout.appliedCorrections.0'))->toMatchArray([
            'target' => 'strang.merge.left.1',
            'prop' => 'extension_stem_continuations.3.1.length',
            'baseValue' => '8rem',
            'effectiveValue' => '10.5rem',
        ]);
});

it('applies preview corrections to canonical rekey target continuation stems', function (): void {
    $previews = LayoutCorrectionConfig::applyToPreviews([
        [
            'component' => 'rekey-target-right',
            'side' => 'right',
            'component_counter' => 1,
            'stem_continuation' => [
                1 => ['length' => '5rem'],
                2 => ['length' => '6rem'],
            ],
        ],
    ], [
        [
            'target' => 'strang.rekey.right.target.1.stem-2',
            'prop' => 'stem_continuation.2.length',
            'delta' => '2rem',
            'value' => null,
            'reason' => 'Target continuation nudge.',
        ],
    ]);

    expect(data_get($previews, '0.stem_continuation.2.length'))->toBe('8rem')
        ->and(data_get($previews, '0.stem_continuation.1.length'))->toBe('5rem')
        ->and(data_get($previews, '0.layout.appliedCorrections.0'))->toMatchArray([
            'target' => 'strang.rekey.right.target.1.stem-2',
            'prop' => 'stem_continuation.2.length',
            'baseValue' => '6rem',
            'effectiveValue' => '8rem',
        ]);
});

it('applies preview corrections to canonical branch continuation stems', function (): void {
    $previews = LayoutCorrectionConfig::applyToPreviews([
        [
            'component' => 'branch-left',
            'side' => 'left',
            'component_counter' => 2,
            'stem_continuation' => [
                1 => ['length' => '4rem'],
                2 => ['length' => '5rem'],
                3 => ['length' => '6rem'],
            ],
        ],
    ], [
        [
            'target' => 'strang.branch.left.2',
            'prop' => 'stem_continuation.3.length',
            'delta' => '3rem',
            'value' => null,
            'reason' => 'Branch continuation nudge.',
        ],
    ]);

    expect(data_get($previews, '0.stem_continuation.3.length'))->toBe('9rem')
        ->and(data_get($previews, '0.stem_continuation.1.length'))->toBe('4rem')
        ->and(data_get($previews, '0.stem_continuation.2.length'))->toBe('5rem')
        ->and(data_get($previews, '0.layout.appliedCorrections.0'))->toMatchArray([
            'target' => 'strang.branch.left.2',
            'prop' => 'stem_continuation.3.length',
            'baseValue' => '6rem',
            'effectiveValue' => '9rem',
        ]);
});

it('does not apply compact correction targets to sibling preview ids', function (): void {
    config()->set('tw-graph-data-driven-defaults.bridge_length', '10rem');

    $previews = LayoutCorrectionConfig::applyToPreviews([
        [
            'component' => 'branch-left',
            'side' => 'left',
            'component_counter' => 1,
        ],
        [
            'component' => 'branch-left',
            'side' => 'left',
            'component_counter' => 10,
        ],
    ], [
        [
            'target' => 'strang.left.1.branch',
            'prop' => 'bridge_length',
            'delta' => '4rem',
            'value' => null,
            'reason' => 'Only the first branch should move.',
        ],
    ]);

    expect($previews[0]['bridge_length'])->toBe('14rem')
        ->and(data_get($previews[0], 'layout.appliedCorrections.0.target'))->toBe('strang.left.1.branch')
        ->and($previews[1])->not->toHaveKey('bridge_length')
        ->and(data_get($previews[1], 'layout.appliedCorrections'))->toBeNull();
});

it('keeps explicit zero rem correction values as deliberate values', function (): void {
    $result = LayoutCorrectionConfig::applyToTrunkPathLengths([
        1 => '5rem',
    ], [
        [
            'target' => 'strang.trunk.1.stem1',
            'prop' => 'length',
            'delta' => null,
            'value' => '0rem',
            'reason' => 'Deliberately collapse this stem.',
        ],
    ], '5rem');

    expect($result['path_lengths'][1])->toBe('0rem')
        ->and($result['applied'][0])->toMatchArray([
            'target' => 'strang.trunk.1.stem1',
            'prop' => 'length',
            'baseValue' => '5rem',
            'effectiveValue' => '0rem',
        ]);
});

it('normalizes public camel and kebab correction prop names consistently', function (): void {
    config()->set('tw-graph-data-driven-layout-corrections', [
        'corrections' => [
            [
                'target' => 'strang.branch.right.2',
                'prop' => 'entryStemLength',
                'delta' => '1rem',
            ],
            [
                'target' => 'strang.rekey-target-right.1',
                'prop' => 'end-length',
                'value' => '6rem',
            ],
            [
                'target' => 'strang.trunk.1.stem4',
                'prop' => 'path-length',
                'delta' => '-1rem',
            ],
        ],
    ]);

    $corrections = LayoutCorrectionConfig::forDataDriven();

    expect($corrections[0])->toMatchArray([
        'target' => 'strang.right.2.branch',
        'prop' => 'entry_stem_length',
        'delta' => '1rem',
    ])
        ->and($corrections[1])->toMatchArray([
            'target' => 'strang.right.1.rekey.target',
            'prop' => 'end_length',
            'value' => '6rem',
        ])
        ->and($corrections[2])->toMatchArray([
            'target' => 'strang.trunk.1.stem4',
            'prop' => 'path_length',
            'delta' => '-1rem',
        ]);
});

it('normalizes public kebab correction prop names inside nested targets', function (): void {
    config()->set('tw-graph-data-driven-layout-corrections', [
        'corrections' => [
            [
                'target' => 'strang.rekey.right.target.1.stem-2',
                'prop' => 'stem-continuation.2.length',
                'delta' => '2rem',
            ],
            [
                'target' => 'strang.merge.left.1.extension.3',
                'prop' => 'extension-stem-continuations.3.1.length',
                'delta' => '1rem',
            ],
        ],
    ]);

    $corrections = LayoutCorrectionConfig::forDataDriven();

    expect($corrections[0])->toMatchArray([
        'target' => 'strang.right.1.rekey.target.stem-2',
        'prop' => 'stem_continuation.2.length',
        'delta' => '2rem',
    ])
        ->and($corrections[1])->toMatchArray([
            'target' => 'strang.left.1.merge.extension.3',
            'prop' => 'extension_stem_continuations.3.1.length',
            'delta' => '1rem',
        ]);
});

it('applies public kebab correction props to internal preview arrays after normalization', function (): void {
    config()->set('tw-graph-data-driven-layout-corrections', [
        'corrections' => [
            [
                'target' => 'strang.rekey-target-right.1',
                'prop' => 'stem-continuation.2.length',
                'delta' => '1.5rem',
                'reason' => 'public config spelling',
            ],
            [
                'target' => 'strang.merge-left.1.extension3',
                'prop' => 'extension-stem-continuations.3.1.length',
                'delta' => '-2rem',
                'reason' => 'public nested config spelling',
            ],
        ],
    ]);

    $previews = LayoutCorrectionConfig::applyToPreviews([
        [
            'component' => 'rekey-target-right',
            'side' => 'right',
            'component_counter' => 1,
            'stem_continuation' => [
                1 => ['length' => '4rem'],
                2 => ['length' => '8rem'],
            ],
        ],
        [
            'component' => 'merge-left',
            'side' => 'left',
            'component_counter' => 1,
            'extension_stem_continuations' => [
                3 => [
                    1 => ['length' => '9rem'],
                ],
            ],
        ],
    ], LayoutCorrectionConfig::forDataDriven());

    expect(data_get($previews, '0.stem_continuation.2.length'))->toBe('9.5rem')
        ->and(data_get($previews, '0.layout.appliedCorrections.0'))->toMatchArray([
            'target' => 'strang.right.1.rekey.target',
            'prop' => 'stem_continuation.2.length',
            'baseValue' => '8rem',
            'effectiveValue' => '9.5rem',
        ])
        ->and(data_get($previews, '1.extension_stem_continuations.3.1.length'))->toBe('7rem')
        ->and(data_get($previews, '1.layout.appliedCorrections.0'))->toMatchArray([
            'target' => 'strang.left.1.merge.extension.3',
            'prop' => 'extension_stem_continuations.3.1.length',
            'baseValue' => '9rem',
            'effectiveValue' => '7rem',
        ]);
});
