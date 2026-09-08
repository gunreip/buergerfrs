<?php

use Gunreip\TranslationWorkbench\Support\TwGraph\DataDriven\TimelineChainGraphData\GraphFacts;
use Illuminate\Support\Collection;
use Tests\TestCase;

uses(TestCase::class);

it('summarizes trunk facts from the main row and root events', function (): void {
    config()->set('tw-graph-data-driven-defaults.colors.fallback', 'zinc');

    $facts = GraphFacts::trunk([
        'translation_key' => 'ui.states.all',
        'root_key_id' => 5,
        'root_finding_id' => 5486,
    ], new Collection([
        [
            'timestamp' => '2026-08-04 09:22:11',
            'branch' => 'Root',
            'event' => 'key_created',
            'state' => 'active',
        ],
        [
            'timestamp' => '2026-08-04 09:25:00',
            'branch' => 'Root key',
            'event' => 'lang_value_linked',
            'state' => 'active',
            'color' => 'emerald',
        ],
    ]));

    expect($facts)->toMatchArray([
        'component' => 'tw-graph.strang.trunk',
        'role' => 'canonical continuation',
        'key' => 'ui.states.all',
        'root_key_id' => 5,
        'root_finding_id' => 5486,
        'event_count' => 2,
        'events' => [
            [
                'timestamp' => '2026-08-04 09:22:11',
                'branch' => 'Root',
                'event' => 'key_created',
                'state' => 'active',
                'color' => 'zinc',
            ],
            [
                'timestamp' => '2026-08-04 09:25:00',
                'branch' => 'Root key',
                'event' => 'lang_value_linked',
                'state' => 'active',
                'color' => 'emerald',
            ],
        ],
    ]);
});

it('alternates merge and branch fact sides without including trunk rows as branches', function (): void {
    $mergeFacts = GraphFacts::merge(new Collection([
        ['first_root' => 'finding #1', 'first_origin_key' => 'ui.a'],
        ['first_root' => 'finding #2', 'first_origin_key' => 'ui.b'],
        ['first_root' => 'finding #3', 'first_origin_key' => 'ui.c'],
    ]));
    $branchFacts = GraphFacts::branch(new Collection([
        ['branch' => 'Root', 'event' => 'key_created'],
        ['branch' => 'classification', 'event' => 'source_inactive'],
        ['branch' => 'review', 'event' => 'commented_out'],
    ]));

    expect($mergeFacts['strangs'])->sequence(
        fn($row) => $row->toMatchArray(['component' => 'tw-graph.strang.merge-left', 'side' => 'left']),
        fn($row) => $row->toMatchArray(['component' => 'tw-graph.strang.merge-right', 'side' => 'right']),
        fn($row) => $row->toMatchArray(['component' => 'tw-graph.strang.merge-left', 'side' => 'left']),
    )->and($branchFacts['count'])->toBe(2)
        ->and($branchFacts['strangs'])->sequence(
            fn($row) => $row->toMatchArray([
                'component' => 'tw-graph.strang.branch-left',
                'side' => 'left',
                'branch' => 'classification',
            ]),
            fn($row) => $row->toMatchArray([
                'component' => 'tw-graph.strang.branch-right',
                'side' => 'right',
                'branch' => 'review',
            ]),
        );
});

it('describes component intent for moved chains and optional side strangs', function (): void {
    $intent = GraphFacts::componentIntent([
        'id' => 853,
        'chain_type' => 'moved',
        'root_key_id' => 4507,
        'translation_key' => 'ui.button.save.save',
        'meta' => [
            'moved_relations' => [
                ['translation_key' => 'ui.save'],
            ],
        ],
    ], new Collection([
        ['event' => 'key_created'],
    ]), new Collection([
        ['first_root' => 'finding #5486'],
    ]));

    expect($intent)->sequence(
        fn($row) => $row->toMatchArray([
            'component' => 'tw-graph.strang.trunk',
            'required' => true,
            'suggested_props' => [
                'graph-id' => 'timeline-chain-853',
                'path-count' => 3,
                'start-label' => 'key #4507',
                'end-label' => 'ui.button.save.save',
            ],
        ]),
        fn($row) => $row->toMatchArray([
            'component' => 'tw-graph.strang.merge-left/right',
            'required' => true,
            'count' => 1,
        ]),
        fn($row) => $row->toMatchArray([
            'component' => 'tw-graph.strang.branch-left/right',
            'required' => true,
            'count' => 1,
        ]),
        fn($row) => $row->toMatchArray([
            'component' => 'tw-graph.strang.rekey-source-left/right + tw-graph.strang.rekey-target-left/right',
            'required' => true,
            'count' => 1,
        ]),
    );
});

it('uses data driven fallback colors before central graph fallback colors', function (): void {
    config()->set('tw-graph-defaults.colors.fallback', 'rose');
    config()->set('tw-graph-data-driven-defaults.colors.fallback', 'amber');

    $facts = GraphFacts::trunk([
        'translation_key' => 'ui.badge.updated',
    ], new Collection([
        ['event' => 'key_created'],
    ]));

    expect($facts['events'][0]['color'])->toBe('amber');
});

it('excludes root key rows from branch facts and uses branch color ahead of event color', function (): void {
    $facts = GraphFacts::branch(new Collection([
        ['branch' => 'Root key', 'event' => 'key_created'],
        [
            'branch' => 'ended after merge',
            'translation_key' => 'ui.old',
            'event' => 'source_inactive',
            'color' => 'rose',
            'branch_color' => 'amber',
        ],
    ]));

    expect($facts['count'])->toBe(1)
        ->and($facts['strangs'][0])->toMatchArray([
            'component' => 'tw-graph.strang.branch-left',
            'side' => 'left',
            'branch' => 'ended after merge',
            'translation_key' => 'ui.old',
            'event' => 'source_inactive',
            'color' => 'amber',
        ]);
});

it('returns empty optional component facts without inventing side strangs', function (): void {
    expect(GraphFacts::merge(new Collection()))->toMatchArray([
        'component_family' => 'tw-graph.strang.merge-*',
        'count' => 0,
        'strangs' => [],
    ]);

    expect(GraphFacts::branch(new Collection([
        ['branch' => 'Root', 'event' => 'key_created'],
        ['branch' => 'Root key', 'event' => 'key_reviewed'],
    ])))->toMatchArray([
        'component_family' => 'tw-graph.strang.branch-*',
        'count' => 0,
        'strangs' => [],
    ]);
});

it('marks optional component intent as not required when source rows are unavailable', function (): void {
    $intent = GraphFacts::componentIntent([
        'id' => 12,
        'chain_type' => 'single',
        'root_key_id' => 5,
        'translation_key' => 'ui.only.trunk',
    ], new Collection(), new Collection());

    expect($intent[0]['required'])->toBeTrue()
        ->and($intent[0]['suggested_props']['path-count'])->toBe(3)
        ->and($intent[1])->toMatchArray([
            'component' => 'tw-graph.strang.merge-left/right',
            'required' => false,
            'count' => 0,
        ])
        ->and($intent[2])->toMatchArray([
            'component' => 'tw-graph.strang.branch-left/right',
            'required' => false,
            'count' => 0,
        ])
        ->and($intent[3])->toMatchArray([
            'component' => 'tw-graph.strang.rekey-source-left/right + tw-graph.strang.rekey-target-left/right',
            'required' => false,
            'count' => 0,
        ]);
});

it('does not mark branch components as required for trunk-only root events', function (): void {
    $intent = GraphFacts::componentIntent([
        'id' => 590,
        'chain_type' => 'single',
        'root_key_id' => 3165,
        'translation_key' => 'ui.only.trunk',
    ], new Collection([
        ['branch' => 'Root', 'event' => 'key_created'],
        ['branch' => 'Root key', 'event' => 'key_reviewed'],
    ]), new Collection());

    expect($intent[2])->toMatchArray([
        'component' => 'tw-graph.strang.branch-left/right',
        'required' => false,
        'count' => 0,
    ]);
});
