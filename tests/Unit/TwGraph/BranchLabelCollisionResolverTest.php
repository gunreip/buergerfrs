<?php

use Gunreip\TranslationWorkbench\Support\TwGraph\DataDriven\TimelineChainGraphData\BranchLabelCollisionResolver;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function (): void {
    config()->set('tw-graph-defaults.line_length', '4rem');
    config()->set('tw-graph-defaults.bridge_length', '4rem');
    config()->set('tw-graph-defaults.stem_length', '4rem');
    config()->set('tw-graph-defaults.arc_size', '2.75rem');
    config()->set('tw-graph-defaults.connector_length', '2rem');
    config()->set('tw-graph-defaults.connector_gap', '0.25rem');
    config()->set('tw-graph-defaults.node_size', '0.95rem');
    config()->set('tw-graph-defaults.debug_bound_box_gap', '0rem');
    config()->set('tw-graph-defaults.debug_bound_bridge_height', '1.5rem');
    config()->set('tw-graph-defaults.debug_bound_end_segment_width', '16rem');
    config()->set('tw-graph-defaults.debug_bound_label_reach', '16rem');
    config()->set('tw-graph-data-driven-defaults.debug_bound_box_gap', '0rem');
});

it('moves same-side overlapping branch previews outward and reports the applied compensation', function (): void {
    $branches = BranchLabelCollisionResolver::resolve([
        branchPreview(1, 'left', 24.0),
        branchPreview(2, 'left', 24.5),
    ]);

    expect($branches[0]['bridge_length'])->not->toBe('4rem')
        ->and(data_get($branches[0], 'layout.appliedCompensations.0'))->toMatchArray([
            'target' => 'strang.left.1.branch.bridge1',
            'prop' => 'bridge_length',
            'baseValue' => '4rem',
            'reason' => 'Automatic top-down same-side branch placement against already placed branch bounds.',
        ])
        ->and(data_get($branches[0], 'layout.appliedCompensations.0.delta'))->not->toBe('0rem')
        ->and(data_get($branches[0], 'layout.appliedCompensations.0.effectiveValue'))->toBe($branches[0]['bridge_length'])
        ->and(data_get($branches[0], 'layout.branchCollisionReport.collisionDelta'))->not->toBe([])
        ->and(data_get($branches[1], 'layout.appliedCompensations'))->toBeNull();
});

it('calculates left and right branch placement independently', function (): void {
    $branches = BranchLabelCollisionResolver::resolve([
        branchPreview(1, 'left', 24.0),
        branchPreview(2, 'left', 24.5),
        branchPreview(1, 'right', 90.0),
        branchPreview(2, 'right', 120.0),
    ]);

    expect(data_get($branches[0], 'layout.appliedCompensations.0.target'))->toBe('strang.left.1.branch.bridge1')
        ->and(data_get($branches[2], 'layout.appliedCompensations'))->toBeNull()
        ->and(data_get($branches[3], 'layout.appliedCompensations'))->toBeNull();
});

it('moves overlapping right branch previews without mirroring left side deltas', function (): void {
    $branches = BranchLabelCollisionResolver::resolve([
        branchPreview(1, 'left', 20.0),
        branchPreview(2, 'right', 24.0),
        branchPreview(3, 'right', 24.5),
    ]);

    expect(data_get($branches[1], 'layout.appliedCompensations.0'))->toMatchArray([
        'target' => 'strang.right.2.branch.bridge1',
        'prop' => 'bridge_length',
        'baseValue' => '4rem',
        'reason' => 'Automatic top-down same-side branch placement against already placed branch bounds.',
    ])
        ->and(data_get($branches[0], 'layout.appliedCompensations'))->toBeNull()
        ->and(data_get($branches[2], 'layout.appliedCompensations'))->toBeNull();
});

it('calculates trunk spacing adjustments for branch end over bridge collisions', function (): void {
    $adjustments = BranchLabelCollisionResolver::trunkPathSpacingAdjustments([
        branchPreview(1, 'left', 20.0, bridgeLength: '4rem', endLength: '8rem'),
        branchPreview(2, 'left', 36.0, bridgeLength: '4rem'),
    ], [
        1 => 0.0,
        2 => 4.0,
        3 => 8.0,
        4 => 12.0,
        5 => 16.0,
        6 => 20.0,
        7 => 24.0,
        8 => 28.0,
        9 => 32.0,
    ]);

    expect($adjustments)->not->toBeEmpty();

    $firstAdjustment = array_values($adjustments)[0];
    expect($firstAdjustment['delta'])->toBeGreaterThan(0.0)
        ->and($firstAdjustment['collisionKey'])->toContain('strang.left.1.branch.end.segment')
        ->and($firstAdjustment['collisionKey'])->toContain('strang.left.2.branch.bridge1');
});

it('calculates right side trunk spacing adjustments for branch end over bridge collisions', function (): void {
    $adjustments = BranchLabelCollisionResolver::trunkPathSpacingAdjustments([
        branchPreview(1, 'right', 20.0, bridgeLength: '4rem', endLength: '8rem'),
        branchPreview(2, 'right', 36.0, bridgeLength: '4rem'),
    ], [
        1 => 0.0,
        2 => 4.0,
        3 => 8.0,
        4 => 12.0,
        5 => 16.0,
        6 => 20.0,
        7 => 24.0,
        8 => 28.0,
        9 => 32.0,
    ]);

    expect($adjustments)->not->toBeEmpty();

    $firstAdjustment = array_values($adjustments)[0];
    expect($firstAdjustment['delta'])->toBeGreaterThan(0.0)
        ->and($firstAdjustment['collisionKey'])->toContain('strang.right.1.branch.end.segment')
        ->and($firstAdjustment['collisionKey'])->toContain('strang.right.2.branch.bridge1');
});

it('uses the branch end anchor rather than the colliding bridge anchor for trunk spacing adjustments', function (): void {
    $adjustments = BranchLabelCollisionResolver::trunkPathSpacingAdjustments([
        branchPreview(4, 'left', 48.0, bridgeLength: '4rem', endLength: '8rem'),
        branchPreview(6, 'left', 64.0, bridgeLength: '4rem'),
    ], [
        1 => 0.0,
        2 => 4.0,
        3 => 8.0,
        4 => 12.0,
        5 => 16.0,
        6 => 20.0,
        7 => 24.0,
        8 => 28.0,
        9 => 32.0,
        10 => 36.0,
        11 => 40.0,
        12 => 44.0,
        13 => 48.0,
        14 => 52.0,
        15 => 56.0,
        16 => 60.0,
        17 => 64.0,
    ]);

    expect($adjustments)->toHaveKey(13)
        ->and($adjustments)->not->toHaveKey(17)
        ->and($adjustments[13]['collisionKey'])->toContain('strang.left.4.branch.end.segment')
        ->and($adjustments[13]['collisionKey'])->toContain('strang.left.6.branch.bridge1');
});

it('does not return ignored trunk spacing collision adjustments', function (): void {
    $branches = [
        branchPreview(1, 'left', 20.0, bridgeLength: '4rem', endLength: '8rem'),
        branchPreview(2, 'left', 36.0, bridgeLength: '4rem'),
    ];
    $trunkNodeAnchors = [
        1 => 0.0,
        2 => 4.0,
        3 => 8.0,
        4 => 12.0,
        5 => 16.0,
        6 => 20.0,
        7 => 24.0,
        8 => 28.0,
        9 => 32.0,
    ];
    $adjustments = BranchLabelCollisionResolver::trunkPathSpacingAdjustments($branches, $trunkNodeAnchors);
    $ignoredCollisionKey = array_values($adjustments)[0]['collisionKey'];

    $ignoredAdjustments = BranchLabelCollisionResolver::trunkPathSpacingAdjustments(
        $branches,
        $trunkNodeAnchors,
        [$ignoredCollisionKey],
    );

    expect($ignoredAdjustments)->toBe([]);
});

it('can suppress branch end over bridge warnings after trunk spacing compensation handled them', function (): void {
    $branches = [
        branchPreview(1, 'left', 20.0, bridgeLength: '4rem', endLength: '8rem'),
        branchPreview(2, 'left', 36.0, bridgeLength: '4rem'),
    ];
    $adjustments = BranchLabelCollisionResolver::trunkPathSpacingAdjustments($branches, [
        1 => 0.0,
        2 => 4.0,
        3 => 8.0,
        4 => 12.0,
        5 => 16.0,
        6 => 20.0,
        7 => 24.0,
        8 => 28.0,
        9 => 32.0,
    ]);
    $ignoredKeys = collect($adjustments)->pluck('collisionKey')->all();

    $refreshed = BranchLabelCollisionResolver::refreshDebugBounds($branches, $ignoredKeys);

    expect(data_get($refreshed[1], 'layout.warnings'))->toBeNull()
        ->and(data_get($refreshed[0], 'layout.branchBoundsDebug'))->not->toBeEmpty()
        ->and(data_get($refreshed[1], 'layout.branchBoundsDebug'))->not->toBeEmpty();
});

it('keeps branch end over bridge warnings visible before they are compensated', function (): void {
    $branches = BranchLabelCollisionResolver::refreshDebugBounds([
        branchPreview(1, 'right', 20.0, bridgeLength: '4rem', endLength: '8rem'),
        branchPreview(2, 'right', 36.0, bridgeLength: '4rem'),
    ]);

    expect(data_get($branches[1], 'layout.warnings.0'))->toMatchArray([
        'type' => 'branch-end-over-bridge',
        'message' => 'Branch end segment overlaps branch bridge',
        'suggestion' => 'side-switch-or-extension-candidate',
    ]);
});

it('reports branch end over bridge warning boxes with normalized ids and concrete dimensions', function (): void {
    $branches = BranchLabelCollisionResolver::refreshDebugBounds([
        branchPreview(1, 'left', 20.0, bridgeLength: '4rem', endLength: '8rem'),
        branchPreview(2, 'left', 36.0, bridgeLength: '4rem'),
    ]);

    $warning = data_get($branches[1], 'layout.warnings.0');

    expect($warning['label'])->toBe('strang.left.1.branch.end.segment')
        ->and($warning['bridge'])->toBe('strang.left.2.branch.bridge1')
        ->and($warning['anchor'])->toBe([
            'x' => '-4.75rem',
            'y' => '38.75rem',
        ])
        ->and($warning['boxes'][0])->toMatchArray([
            'type' => 'branch-end',
            'id' => 'strang.left.1.branch.end.segment',
            'width' => '16rem',
        ])
        ->and($warning['boxes'][1])->toMatchArray([
            'type' => 'bridge',
            'id' => 'strang.left.2.branch.bridge1',
            'height' => '1.5rem',
        ]);
});

it('keeps promoted first stem labels in the branch start bounds instead of the body bounds', function (): void {
    $branches = BranchLabelCollisionResolver::refreshDebugBounds([
        branchPreview(1, 'left', 20.0, bridgeLength: '8rem'),
    ]);

    $debugBounds = collect(data_get($branches[0], 'layout.branchBoundsDebug'));
    $startBounds = $debugBounds->firstWhere('type', 'branch-start');
    $bodyBounds = $debugBounds->firstWhere('type', 'branch-body');

    expect($startBounds)->toMatchArray([
        'id' => 'strang.left.1.branch.start.bounds',
        'x' => '-20rem',
        'width' => '13rem',
    ])
        ->and($bodyBounds)->toMatchArray([
            'id' => 'strang.left.1.branch.body.bounds',
            'x' => '-20rem',
            'width' => '13rem',
        ]);
});

it('keeps forced first stem labels in branch body bounds', function (): void {
    $branch = branchPreview(1, 'left', 20.0, bridgeLength: '8rem');
    $branch['stem_continuation'][1]['force'] = true;

    $branches = BranchLabelCollisionResolver::refreshDebugBounds([$branch]);
    $debugBounds = collect(data_get($branches[0], 'layout.branchBoundsDebug'));
    $startBounds = $debugBounds->firstWhere('type', 'branch-start');
    $bodyBounds = $debugBounds->firstWhere('type', 'branch-body');

    expect($startBounds)->toMatchArray([
        'id' => 'strang.left.1.branch.start.bounds',
        'width' => '13rem',
    ])
        ->and($bodyBounds)->toMatchArray([
            'id' => 'strang.left.1.branch.body.bounds',
            'x' => '-29.23rem',
            'width' => '22.23rem',
        ]);
});

it('refreshes branch debug bounds after anchor positions changed', function (): void {
    $branches = BranchLabelCollisionResolver::resolve([
        branchPreview(1, 'right', 20.0),
    ]);
    $oldBridgeY = data_get($branches[0], 'layout.branchBoundsDebug.0.y');

    $branches[0]['anchor_y_rem'] = 28.0;
    $refreshed = BranchLabelCollisionResolver::refreshDebugBounds($branches);

    expect(data_get($refreshed[0], 'layout.branchBoundsDebug.0.y'))->not->toBe($oldBridgeY)
        ->and(data_get($refreshed[0], 'layout.branchBoundsDebug.0.id'))->toBe('strang.right.1.branch.bridge1');
});

it('uses debug bound gap while resolving same-side branch placement', function (): void {
    $withoutGap = BranchLabelCollisionResolver::resolve([
        branchPreview(1, 'left', 24.0),
        branchPreview(2, 'left', 24.5),
    ]);

    config()->set('tw-graph-defaults.debug_bound_box_gap', '3rem');
    config()->set('tw-graph-data-driven-defaults.debug_bound_box_gap', '3rem');

    $withGap = BranchLabelCollisionResolver::resolve([
        branchPreview(1, 'left', 24.0),
        branchPreview(2, 'left', 24.5),
    ]);

    expect(data_get($withGap[0], 'layout.appliedCompensations.0.delta'))
        ->not->toBe(data_get($withoutGap[0], 'layout.appliedCompensations.0.delta'));
});

it('uses entry stem length while positioning branch bridge debug bounds', function (): void {
    $branches = BranchLabelCollisionResolver::refreshDebugBounds([
        branchPreview(1, 'right', 20.0, bridgeLength: '8rem') + [
            'entry_stem_length' => '2rem',
        ],
    ]);

    $bridgeBounds = collect(data_get($branches[0], 'layout.branchBoundsDebug'))
        ->firstWhere('type', 'bridge');

    expect($bridgeBounds)->toMatchArray([
        'id' => 'strang.right.1.branch.bridge1',
        'x' => '2.75rem',
        'y' => '24rem',
        'width' => '8rem',
        'height' => '1.5rem',
    ]);
});

it('keeps branch main and sub debug bounds visible with normalized ids', function (): void {
    $branches = BranchLabelCollisionResolver::refreshDebugBounds([
        branchPreview(1, 'left', 20.0, bridgeLength: '8rem'),
    ]);

    $debugBounds = collect(data_get($branches[0], 'layout.branchBoundsDebug'));

    expect($debugBounds->pluck('type')->all())->toContain('bridge', 'label', 'branch-start', 'branch-body', 'branch-end')
        ->and($debugBounds->firstWhere('type', 'label'))->toMatchArray([
            'id' => 'strang.left.1.branch.bounds.label',
            'side' => 'left',
        ])
        ->and($debugBounds->firstWhere('type', 'branch-start'))->toMatchArray([
            'id' => 'strang.left.1.branch.start.bounds',
            'side' => 'left',
        ])
        ->and($debugBounds->firstWhere('type', 'branch-body'))->toMatchArray([
            'id' => 'strang.left.1.branch.body.bounds',
            'side' => 'left',
        ])
        ->and($debugBounds->firstWhere('type', 'branch-end'))->toMatchArray([
            'id' => 'strang.left.1.branch.end.bounds',
            'side' => 'left',
        ]);
});

/**
 * @return array<string, mixed>
 */
function branchPreview(
    int $counter,
    string $side,
    float $anchorYRem,
    string $bridgeLength = '4rem',
    string $endLength = '4rem',
): array {
    return [
        'id' => 'strang.branch-' . $side . '.' . $counter,
        'component' => 'tw-graph.strang.branch-' . $side,
        'side' => $side,
        'component_counter' => $counter,
        'attach_to' => 'strang.trunk.path.' . $counter . '.end',
        'anchor_y_rem' => $anchorYRem,
        'bridge_length' => $bridgeLength,
        'end_length' => $endLength,
        'step' => [
            'stepLabel' => [
                'text' => ['Source inactive', 'shared obsolete'],
            ],
        ],
        'stem_continuation' => [
            1 => [
                'left' => [
                    'text' => ['finding ID #' . (5400 + $counter), 'ui.state.' . $counter, '2026-07-21 10:09'],
                ],
            ],
        ],
        'end_label' => [
            'text' => ['Ended after merge', 'shared obsolete', '1 row'],
            'side' => 'top',
        ],
    ];
}
