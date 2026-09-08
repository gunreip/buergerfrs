<?php

use Gunreip\TranslationWorkbench\Support\TwGraph\DataDriven\TimelineChainGraphData\BranchPreviewBuilder;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function (): void {
    config()->set('tw-graph-defaults.colors.branch', 'rose');
    config()->set('tw-graph-defaults.colors.branch_badge', 'red');
    config()->set('tw-graph-data-driven-defaults.label_offset', '0.75rem');
});

it('returns no branch previews when no branch outcome rows are present', function (): void {
    expect(BranchPreviewBuilder::previews([
        'summary' => [
            'branch_candidates' => 0,
            'branch_candidate_findings' => 0,
        ],
        'rows' => [],
    ]))->toBe([]);
});

it('groups ended after merge rows into aggregate branch stems without phantom rows', function (): void {
    $previews = BranchPreviewBuilder::previews([
        'rows' => branchRows('ended after merge', 3),
    ]);

    expect($previews)->toHaveCount(2)
        ->and($previews[0])->toMatchArray([
            'component' => 'tw-graph.strang.branch-left',
            'side' => 'left',
            'color' => 'rose',
            'attach_to' => 'strang.merge-left.end',
            'step' => [
                'stepLabel' => [
                    'text' => ['Source inactive', 'shared obsolete', '2 rows'],
                    'badgeColor' => 'red',
                ],
            ],
            'end_label' => [
                'text' => ['Ended after merge', 'shared obsolete', '2 rows'],
                'side' => 'top',
                'offset' => '0.75rem',
                'badgeColor' => 'red',
            ],
        ])
        ->and($previews[0]['source']['finding_ids'])->toBe([5481, 5483])
        ->and($previews[0]['stem_continuation'][1]['left']['text'])->toBe([
            'finding ID #5481',
            'ui.state.1',
            '2026-07-21 10:09',
        ])
        ->and($previews[0]['stem_continuation'][1]['right']['text'])->toBe([
            'finding ID #5483',
            'ui.state.3',
            '2026-07-23 10:09',
        ])
        ->and($previews[1]['source']['finding_ids'])->toBe([5482])
        ->and(collect($previews)->pluck('stem_continuation')->flatten(4)->all())
        ->not->toContain('finding ID #?');
});

it('keeps same anchor branch rows separated by rendered side', function (): void {
    $previews = BranchPreviewBuilder::previews([
        'rows' => [
            branchRow(5481, 'left', 'ended after merge', 'ui.state.left.1', '2026-07-22 09:00:00'),
            branchRow(5482, 'left', 'ended after merge', 'ui.state.left.2', '2026-07-22 10:00:00'),
            branchRow(5483, 'right', 'ended after merge', 'ui.state.right.1', '2026-07-22 11:00:00'),
            branchRow(5484, 'right', 'ended after merge', 'ui.state.right.2', '2026-07-22 12:00:00'),
        ],
    ], [
        [
            'anchor' => 'strang.trunk.path.4.end',
            'timestamp' => '2026-07-20 00:00:00',
            'event' => 'key_created',
            'y_rem' => 20,
        ],
    ]);

    expect($previews)->toHaveCount(2)
        ->and($previews[0])->toMatchArray([
            'side' => 'left',
            'attach_to' => 'strang.trunk.path.4.end',
            'finding_count' => 2,
        ])
        ->and($previews[0]['source']['finding_ids'])->toBe([5481, 5482])
        ->and($previews[1])->toMatchArray([
            'side' => 'right',
            'attach_to' => 'strang.trunk.path.4.end',
            'finding_count' => 2,
        ])
        ->and($previews[1]['source']['finding_ids'])->toBe([5483, 5484]);
});

it('attaches branch previews to the latest trunk timeline anchor that already happened', function (): void {
    $previews = BranchPreviewBuilder::previews([
        'rows' => [
            branchRow(5481, 'left', 'ended after merge', 'ui.state.1', '2026-07-22 09:00:00'),
            branchRow(5482, 'right', 'ended before target', 'ui.state.2', '2026-07-25 09:00:00'),
        ],
    ], [
        [
            'anchor' => 'strang.trunk.path.4.end',
            'timestamp' => '2026-07-20 00:00:00',
            'event' => 'key_created',
            'y_rem' => 20,
        ],
        [
            'anchor' => 'strang.trunk.path.6.end',
            'timestamp' => '2026-07-23 00:00:00',
            'event' => 'translation_key_updated',
            'y_rem' => 30,
        ],
        [
            'anchor' => 'strang.trunk.path.8.end',
            'timestamp' => '2026-07-28 00:00:00',
            'event' => 'lang_value_linked',
            'y_rem' => 40,
        ],
    ]);

    expect($previews)->toHaveCount(2)
        ->and($previews[0]['attach_to'])->toBe('strang.trunk.path.4.end')
        ->and($previews[0]['anchor_y_rem'])->toBe(20.0)
        ->and($previews[1]['attach_to'])->toBe('strang.trunk.path.6.end')
        ->and($previews[1]['anchor_y_rem'])->toBe(30.0)
        ->and($previews[1]['end_label']['text'])->toBe([
            'Ended before target',
            'not shared obsolete',
            '1 rows',
        ]);
});

it('keeps same-side branch rows on different trunk anchors as separate branch previews', function (): void {
    $previews = BranchPreviewBuilder::previews([
        'rows' => [
            branchRow(5481, 'left', 'ended after merge', 'ui.state.early', '2026-07-22 09:00:00'),
            branchRow(5483, 'left', 'ended after merge', 'ui.state.late', '2026-07-27 09:00:00'),
        ],
    ], [
        [
            'anchor' => 'strang.trunk.path.4.end',
            'timestamp' => '2026-07-20 00:00:00',
            'event' => 'key_created',
            'y_rem' => 20,
        ],
        [
            'anchor' => 'strang.trunk.path.8.end',
            'timestamp' => '2026-07-25 00:00:00',
            'event' => 'lang_value_linked',
            'y_rem' => 40,
        ],
    ]);

    expect($previews)->toHaveCount(2)
        ->and($previews[0])->toMatchArray([
            'side' => 'left',
            'component_counter' => 1,
            'attach_to' => 'strang.trunk.path.4.end',
            'anchor_y_rem' => 20.0,
            'finding_count' => 1,
        ])
        ->and($previews[1])->toMatchArray([
            'side' => 'left',
            'component_counter' => 2,
            'attach_to' => 'strang.trunk.path.8.end',
            'anchor_y_rem' => 40.0,
            'finding_count' => 1,
        ]);
});

it('keeps different branch outcome groups separate even when they share side and trunk anchor', function (): void {
    $previews = BranchPreviewBuilder::previews([
        'rows' => [
            branchRow(5481, 'left', 'ended after merge', 'ui.state.shared', '2026-07-22 09:00:00'),
            branchRow(5482, 'left', 'ended before target', 'ui.state.unshared', '2026-07-22 10:00:00'),
        ],
    ], [
        [
            'anchor' => 'strang.trunk.path.4.end',
            'timestamp' => '2026-07-20 00:00:00',
            'event' => 'key_created',
            'y_rem' => 20,
        ],
        [
            'anchor' => 'strang.trunk.path.5.end',
            'timestamp' => '2026-07-22 00:00:00',
            'event' => 'translation_key_updated',
            'y_rem' => 25,
        ],
    ]);

    expect($previews)->toHaveCount(2)
        ->and($previews[0]['source']['outcome_group'])->toBe('ended after merge')
        ->and($previews[0]['end_label']['text'])->toBe([
            'Ended after merge',
            'shared obsolete',
            '1 rows',
        ])
        ->and($previews[1]['source']['outcome_group'])->toBe('ended before target')
        ->and($previews[1]['end_label']['text'])->toBe([
            'Ended before target',
            'not shared obsolete',
            '1 rows',
        ]);
});

it('uses first seen timestamps as branch row label fallback', function (): void {
    $row = branchRow(5481, 'right', 'ended after merge', 'ui.state.fallback', '');
    unset($row['last_seen_at'], $row['last_seen_at_raw']);
    $row['first_seen_at'] = '2026-07-19 08:11:00';

    $previews = BranchPreviewBuilder::previews([
        'rows' => [$row],
    ]);

    expect($previews[0]['stem_continuation'][1]['right']['text'])->toBe([
        'finding ID #5481',
        'ui.state.fallback',
        '2026-07-19 08:11',
    ]);
});

it('falls back to merge end anchors when no chronological trunk anchor can be resolved', function (): void {
    $previews = BranchPreviewBuilder::previews([
        'rows' => [
            branchRow(5481, 'left', 'ended after merge', 'ui.state.left', ''),
            branchRow(5482, 'right', 'ended after merge', 'ui.state.right', ''),
        ],
    ]);

    expect($previews)->toHaveCount(2)
        ->and($previews[0]['attach_to'])->toBe('strang.merge-left.end')
        ->and($previews[0]['anchor_y_rem'])->toBeNull()
        ->and($previews[1]['attach_to'])->toBe('strang.merge-right.end')
        ->and($previews[1]['anchor_y_rem'])->toBeNull();
});

it('uses data driven branch colors ahead of central defaults', function (): void {
    config()->set('tw-graph-defaults.colors.branch', 'rose');
    config()->set('tw-graph-defaults.colors.branch_badge', 'red');
    config()->set('tw-graph-data-driven-defaults.colors.branch', 'amber');
    config()->set('tw-graph-data-driven-defaults.colors.branch_badge', 'lime');

    $previews = BranchPreviewBuilder::previews([
        'rows' => [
            branchRow(5481, 'left', 'ended after merge', 'ui.state.1', '2026-07-22 09:00:00'),
        ],
    ]);

    expect($previews[0]['color'])->toBe('amber')
        ->and($previews[0]['step']['stepLabel']['badgeColor'])->toBe('lime')
        ->and($previews[0]['end_label']['badgeColor'])->toBe('lime')
        ->and($previews[0]['stem_continuation'][1]['left']['badgeColor'])->toBe('lime');
});

function branchRows(string $outcomeGroup, int $count): array
{
    return array_map(
        static fn(int $number): array => branchRow(
            5480 + $number,
            $number % 2 === 1 ? 'left' : 'right',
            $outcomeGroup,
            'ui.state.' . $number,
            sprintf('2026-07-%02d 10:09:00', 20 + $number),
        ),
        range(1, $count),
    );
}

function branchRow(int $findingId, string $side, string $outcomeGroup, string $originKey, string $lastSeenAt): array
{
    return [
        'finding_id' => $findingId,
        'side' => $side,
        'outcome_group' => $outcomeGroup,
        'origin_key' => $originKey,
        'last_seen_at' => $lastSeenAt,
        'last_seen_at_raw' => $lastSeenAt,
    ];
}
