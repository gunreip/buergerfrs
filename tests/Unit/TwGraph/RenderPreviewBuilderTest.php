<?php

use App\Settings\AppGeneralSettings;
use Gunreip\TranslationWorkbench\Support\TwGraph\DataDriven\TimelineChainGraphData\RenderPreviewBuilder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function (): void {
    $settings = (new ReflectionClass(AppGeneralSettings::class))->newInstanceWithoutConstructor();
    $settings->locale = 'en';
    $settings->availableLocales = ['en', 'de'];
    $settings->addedPrimaryLocales = ['en', 'de'];

    app()->instance(AppGeneralSettings::class, $settings);

    config()->set('tw-graph-defaults.colors.graph', 'cyan');
    config()->set('tw-graph-defaults.colors.trunk', 'green');
    config()->set('tw-graph-defaults.line_length', '4rem');
    config()->set('tw-graph-defaults.stem_length', '4rem');
    config()->set('tw-graph-defaults.bridge_length', '4rem');
    config()->set('tw-graph-defaults.connector_length', '2rem');
    config()->set('tw-graph-data-driven-defaults.stem_length', '4rem');
    config()->set('tw-graph-data-driven-defaults.trunk_start_shift_enabled', false);
    config()->set('tw-graph-data-driven-defaults.trunk_start_unlabeled_next_stem_factor', 1.0);
    config()->set('tw-graph-data-driven-defaults.merge_layout.vertical_stagger_enabled', false);

    Schema::shouldReceive('hasTable')->byDefault()->andReturnFalse();
});

it('builds a trunk-only render preview without side strangs', function (): void {
    $preview = RenderPreviewBuilder::build([
        'id' => 913,
        'chain_type' => 'single',
        'chain_status' => 'inactive',
        'translation_key' => 'ui.labels.actions',
        'root_key_id' => 3676,
        'created_at' => '2026-07-17 16:39:00',
        'timeline_event_summary' => [
            'key_created' => 1,
            'key_obsoleted' => 1,
        ],
    ], new Collection([
        [
            'timestamp' => '2026-07-17 16:39:00',
            'event' => 'Key created',
            'state' => 'obsolete',
        ],
        [
            'timestamp' => '2026-07-18 09:15:00',
            'event' => 'key_obsoleted',
            'state' => 'inactive',
        ],
    ]), new Collection(), [
        'summary' => [
            'source_active' => 0,
            'source_inactive' => 1,
            'branch_candidates' => 0,
            'branch_candidate_findings' => 0,
        ],
        'rows' => [],
    ]);

    expect($preview['mode'])->toBe('trunk_only')
        ->and($preview['graph']['graph_id'])->toBe('timeline-chain-913-data-preview')
        ->and($preview['graph']['header']['text'])->toBe([
            'Timeline chain ID #913',
            'Single · Inactive · ui.labels.actions',
        ])
        ->and($preview['graph']['color'])->toBe('cyan')
        ->and($preview['graph']['line_length'])->toBe('4rem')
        ->and($preview['graph']['stem_length'])->toBe('4rem')
        ->and($preview['graph']['bridge_length'])->toBe('4rem')
        ->and($preview['trunk']['component'])->toBe('tw-graph.strang.trunk')
        ->and($preview['trunk']['color'])->toBe('green')
        ->and($preview['trunk']['start_label']['text'])->toBe(['key ID #3676', '2026-07-17 16:39'])
        ->and($preview['trunk']['end_label']['text'])->toBe(['key ID #3676', '0 active - 1 ended', '- TIMELINE CHAIN END -'])
        ->and($preview['trunk']['end_label']['long'])->toBeTrue()
        ->and($preview['merge'])->toBeNull()
        ->and($preview['merges'])->toBe([])
        ->and($preview['rekeys'])->toBe([])
        ->and($preview['branches'])->toBe([])
        ->and($preview['trunk']['start_node_labels']['left']['text'])->toBe([
        'Key created',
        '2026-07-17 16:39 · obsolete',
    ])
        ->and($preview['trunk']['node_labels'][1][0]['text'])->toBe([
            'key_obsoleted',
            '2026-07-18 09:15 · inactive',
        ])
        ->and($preview['limits'])->toMatchArray([
            'available_events' => 2,
            'normal_events' => 2,
            'dead_dev_events' => 0,
            'rendered_merge_candidates' => 0,
            'rendered_branch_candidates' => 0,
            'rendered_rekey_strangs' => 0,
        ]);
});

it('adds chunk event labels for high volume timeline event types', function (): void {
    config()->set('tw-graph-defaults.colors.chunk_event', 'amber');
    config()->set('tw-graph-data-driven-defaults.colors.chunk_event', 'amber');

    $preview = RenderPreviewBuilder::build([
        'id' => 590,
        'chain_type' => 'single',
        'chain_status' => 'active',
        'translation_key' => 'ui.dynamic.source',
        'root_key_id' => 3165,
        'created_at' => '2026-07-17 16:39:00',
        'timeline_event_count' => 15,
        'timeline_event_ids' => [],
        'timeline_event_summary' => [
            'dynamic_source_classification_changed' => 13,
            'key_finding_relation_obsoleted' => 2,
        ],
    ], new Collection([
        [
            'timestamp' => '2026-07-17 16:39:00',
            'event' => 'Key created',
            'state' => 'active',
        ],
    ]), new Collection(), [
        'summary' => [
            'source_active' => 1,
            'source_inactive' => 0,
            'branch_candidates' => 0,
            'branch_candidate_findings' => 0,
        ],
        'rows' => [],
    ]);

    $chunkLabels = collect(array_values($preview['trunk']['node_labels']))
        ->flatMap(static fn(array $labels): array => $labels)
        ->filter(static fn(mixed $label): bool => is_array($label) && ($label['color'] ?? null) === 'amber')
        ->values();

    expect($preview['limits'])->toMatchArray([
        'available_events' => 15,
        'normal_events' => 15,
        'dead_dev_events' => 0,
        'available_event_types' => 2,
        'compacted_events' => 13,
    ])
        ->and($chunkLabels)->toHaveCount(4)
        ->and($chunkLabels[0])->toMatchArray([
            'side' => 'left',
            'text' => ['dynamic_source_classification_changed', '13 events'],
            'badgeColor' => 'amber',
            'width' => 'halfLong',
        ])
        ->and($chunkLabels[1])->toMatchArray([
            'side' => 'right',
            'text' => [
                [
                    'ordinal' => [
                        'number' => 1,
                        'suffix' => 'st',
                    ],
                    'text' => 'sample:',
                ],
            ],
            'badgeColor' => 'amber',
            'maxLines' => 4,
        ])
        ->and($chunkLabels[2])->toMatchArray([
            'side' => 'left',
            'text' => ['key_finding_relation_obsoleted', '2 events'],
            'width' => 'halfLong',
        ]);
});

it('keeps compacted chunk event labels chronologically merged with normal trunk events', function (): void {
    config()->set('tw-graph-defaults.colors.chunk_event', 'amber');
    config()->set('tw-graph-data-driven-defaults.colors.chunk_event', 'amber');

    Schema::shouldReceive('hasColumn')
        ->with('translation_workbench_timeline_events', 'event_classification')
        ->once()
        ->andReturnFalse();

    DB::shouldReceive('table')
        ->with('translation_workbench_timeline_events')
        ->once()
        ->andReturn(new class {
            public function whereIn(string $column, array $values): self
            {
                if ($column === 'id') {
                    expect($values)->toBe([38512]);
                }

                if ($column === 'event_type') {
                    expect($values)->toBe(['dynamic_source_classification_changed']);
                }

                return $this;
            }

            public function orderBy(string $column): self
            {
                expect($column)->toBeIn(['created_at', 'id']);

                return $this;
            }

            public function get(array $columns): Collection
            {
                expect($columns)->toBe(['id', 'finding_id', 'key_id', 'event_type', 'created_at']);

                return collect([
                    (object) [
                        'id' => 38512,
                        'finding_id' => 2684,
                        'key_id' => 3165,
                        'event_type' => 'dynamic_source_classification_changed',
                        'created_at' => '2026-02-01 12:00:00',
                    ],
                ]);
            }
        });

    $preview = RenderPreviewBuilder::build([
        'id' => 590,
        'chain_type' => 'single',
        'chain_status' => 'active',
        'translation_key' => 'ui.dynamic.source',
        'root_key_id' => 3165,
        'created_at' => '2026-01-01 09:00:00',
        'timeline_event_count' => 14,
        'timeline_event_ids' => [38512],
        'timeline_event_summary' => [
            'dynamic_source_classification_changed' => 12,
        ],
    ], new Collection([
        [
            'timestamp' => '2026-01-01 09:00:00',
            'event' => 'Key created',
            'state' => 'active',
        ],
        [
            'timestamp' => '2026-03-01 09:00:00',
            'event' => 'key_reviewed',
            'state' => 'accepted',
        ],
    ]), new Collection(), [
        'summary' => [
            'source_active' => 1,
            'source_inactive' => 0,
            'branch_candidates' => 0,
            'branch_candidate_findings' => 0,
        ],
        'rows' => [],
    ]);

    expect($preview['trunk']['start_node_labels']['left']['text'])->toBe([
        'Key created',
        '2026-01-01 09:00 · active',
    ])
        ->and($preview['trunk']['node_labels'][1][0]['text'])->toBe([
            'dynamic_source_classification_changed',
            '2026-02-01 12:00',
            '12 events',
        ])
        ->and($preview['trunk']['node_labels'][2][0]['text'])->toBe([
            'key_reviewed',
            '2026-03-01 09:00 · accepted',
        ])
        ->and($preview['trunk']['path_lengths'][1]['component'])->toBe('stem-compressed')
        ->and($preview['trunk']['path_lengths'][2]['component'])->toBe('stem-compressed')
        ->and($preview['trunk']['path_lengths'][1]['toColor'])->toBe(\Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::dataDrivenString('colors.chunk_event', 'amber'))
        ->and($preview['trunk']['path_lengths'][2]['toColor'])->toBe(\Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::dataDrivenString('colors.trunk', 'green'));
});

it('keeps trunk only start promotion at the start node instead of leaving an empty first stem', function (): void {
    $preview = RenderPreviewBuilder::build([
        'id' => 251,
        'chain_type' => 'single',
        'chain_status' => 'active',
        'translation_key' => 'ui.empty.start',
        'root_key_id' => 251,
        'created_at' => '2026-07-17 16:39:00',
        'timeline_event_summary' => [
            'key_created' => 1,
            'key_reviewed' => 1,
        ],
    ], new Collection([
        [
            'timestamp' => '2026-07-17 16:39:00',
            'event' => 'Key created',
            'state' => 'active',
        ],
        [
            'timestamp' => '2026-07-18 09:15:00',
            'event' => 'key_reviewed',
            'state' => 'active',
        ],
    ]), new Collection(), [
        'summary' => [
            'source_active' => 1,
            'source_inactive' => 0,
        ],
        'rows' => [],
    ]);

    expect($preview['mode'])->toBe('trunk_only')
        ->and($preview['trunk']['start_node_labels']['left']['text'])->toBe([
            'Key created',
            '2026-07-17 16:39 · active',
        ])
        ->and($preview['trunk']['node_labels'][1][0]['text'])->toBe([
            'key_reviewed',
            '2026-07-18 09:15 · active',
        ]);
});

it('applies data driven trunk layout corrections to rendered preview path lengths', function (): void {
    config()->set('tw-graph-data-driven-layout-corrections', [
        'strang.trunk' => [
            'stem' => [
                2 => '3rem',
            ],
        ],
    ]);

    $preview = RenderPreviewBuilder::build([
        'id' => 913,
        'chain_type' => 'single',
        'chain_status' => 'inactive',
        'translation_key' => 'ui.labels.actions',
        'root_key_id' => 3676,
        'created_at' => '2026-07-17 16:39:00',
        'timeline_event_summary' => [
            'key_created' => 1,
            'key_obsoleted' => 1,
        ],
    ], new Collection([
        [
            'timestamp' => '2026-07-17 16:39:00',
            'event' => 'Key created',
            'state' => 'obsolete',
        ],
        [
            'timestamp' => '2026-07-18 09:15:00',
            'event' => 'key_obsoleted',
            'state' => 'inactive',
        ],
    ]), new Collection(), [
        'summary' => [
            'source_active' => 0,
            'source_inactive' => 1,
        ],
        'rows' => [],
    ]);

    expect($preview['trunk']['path_lengths'][2])->toBe('7rem');
});

it('uses data driven graph defaults ahead of central graph defaults in render previews', function (): void {
    config()->set('tw-graph-defaults.stem_length', '4rem');
    config()->set('tw-graph-defaults.bridge_length', '4rem');
    config()->set('tw-graph-defaults.arc_radius', '2.75rem');
    config()->set('tw-graph-data-driven-defaults.stem_length', '9rem');
    config()->set('tw-graph-data-driven-defaults.bridge_length', '11rem');
    config()->set('tw-graph-data-driven-defaults.arc_radius', '4.75rem');

    $preview = RenderPreviewBuilder::build([
        'id' => 914,
        'chain_type' => 'single',
        'chain_status' => 'active',
        'translation_key' => 'ui.defaults.override',
        'root_key_id' => 914,
        'created_at' => '2026-07-17 16:39:00',
        'timeline_event_summary' => [
            'key_created' => 1,
            'key_reviewed' => 1,
        ],
    ], new Collection([
        [
            'timestamp' => '2026-07-17 16:39:00',
            'event' => 'Key created',
            'state' => 'active',
        ],
        [
            'timestamp' => '2026-07-18 09:15:00',
            'event' => 'key_reviewed',
            'state' => 'active',
        ],
    ]), new Collection(), [
        'summary' => [
            'source_active' => 1,
            'source_inactive' => 0,
        ],
        'rows' => [],
    ]);

    expect($preview['graph']['stem_length'])->toBe('9rem')
        ->and($preview['graph']['bridge_length'])->toBe('11rem')
        ->and($preview['graph']['arc_radius'])->toBe('4.75rem')
        ->and($preview['trunk']['path_lengths'][1])->toMatchArray([
            'component' => 'path',
        ])
        ->and($preview['trunk']['layout']['stemClassification'][1]['length'])->toBe('9rem');
});

it('scales distributed trunk spacing compensation with the configured factor', function (): void {
    config()->set('tw-graph-data-driven-defaults.collision_compensation_enabled', true);
    $method = new ReflectionMethod(RenderPreviewBuilder::class, 'applyTrunkPathSpacingCompensation');
    $method->setAccessible(true);

    $pathLengths = [
        1 => '4rem',
        2 => '4rem',
        3 => '4rem',
    ];
    $adjustments = [
        1 => [
            'delta' => 6.0,
            'collisionKey' => 'strang.left.1.branch.end.segment|strang.left.2.branch.bridge1',
        ],
    ];
    $branches = [
        [
            'attach_to' => 'strang.trunk.path.3.end',
        ],
    ];

    config()->set('tw-graph-data-driven-defaults.trunk_spacing_compensation_factor', 1.0);
    config()->set('tw-graph-data-driven-defaults.trunk_spacing_compensation_stem_step', '2.75rem');
    $fullCompensation = $method->invoke(null, $pathLengths, $adjustments, $branches, '4rem', 1);

    config()->set('tw-graph-data-driven-defaults.trunk_spacing_compensation_factor', 0.5);
    $halfCompensation = $method->invoke(null, $pathLengths, $adjustments, $branches, '4rem', 1);

    expect($fullCompensation['path_lengths'])->toBe([
        1 => '6rem',
        2 => '6rem',
        3 => '6rem',
    ])
        ->and($halfCompensation['path_lengths'])->toBe([
            1 => '5.5rem',
            2 => '5.5rem',
            3 => '4rem',
        ])
        ->and($fullCompensation['applied'][0])->toMatchArray([
            'target' => 'strang.trunk.1.stem1',
            'delta' => '2rem',
            'baseValue' => '4rem',
            'effectiveValue' => '6rem',
        ])
        ->and($fullCompensation['applied'][0]['sources'][0])->toMatchArray([
            'measuredIncrement' => '6rem',
            'requiredIncrement' => '6rem',
            'factor' => 1.0,
            'distributedAcross' => [
                'strang.trunk.1.stem1',
                'strang.trunk.1.stem2',
                'strang.trunk.1.stem3',
            ],
        ])
        ->and($halfCompensation['applied'][0]['sources'][0])->toMatchArray([
            'measuredIncrement' => '6rem',
            'requiredIncrement' => '3rem',
            'factor' => 0.5,
            'distributedAcross' => [
                'strang.trunk.1.stem1',
                'strang.trunk.1.stem2',
            ],
        ]);
});

it('keeps trunk path counts tied to rendered events instead of prebuilt empty stems', function (): void {
    $preview = RenderPreviewBuilder::build([
        'id' => 915,
        'chain_type' => 'single',
        'chain_status' => 'active',
        'translation_key' => 'ui.no.empty.stems',
        'root_key_id' => 915,
        'created_at' => '2026-07-17 16:39:00',
        'timeline_event_summary' => [
            'key_created' => 1,
        ],
    ], new Collection([
        [
            'timestamp' => '2026-07-17 16:39:00',
            'event' => 'Key created',
            'state' => 'active',
        ],
    ]), new Collection(), [
        'summary' => [
            'source_active' => 1,
            'source_inactive' => 0,
        ],
        'rows' => [],
    ]);

    expect($preview['trunk']['path_count'])->toBe(1)
        ->and($preview['trunk']['path_lengths'][1])->toBe([
            'length' => '0rem',
            'labels' => false,
        ])
        ->and($preview['trunk']['layout']['stemClassification'][1]['roles'])->toBe(['empty-eliminated']);
});

it('keeps trunk event ordering stable when some event timestamps are missing', function (): void {
    $preview = RenderPreviewBuilder::build([
        'id' => 916,
        'chain_type' => 'single',
        'chain_status' => 'active',
        'translation_key' => 'ui.mixed.timestamps',
        'root_key_id' => 916,
        'created_at' => '2026-07-17 16:39:00',
        'timeline_event_summary' => [
            'key_created' => 1,
            'key_reviewed' => 1,
            'key_updated' => 1,
        ],
    ], new Collection([
        [
            'timestamp' => '2026-07-19 09:15:00',
            'event' => 'key_reviewed',
            'state' => 'accepted',
        ],
        [
            'timestamp' => '',
            'event' => 'key_updated_without_timestamp',
            'state' => 'active',
        ],
        [
            'timestamp' => '2026-07-17 16:39:00',
            'event' => 'Key created',
            'state' => 'active',
        ],
    ]), new Collection(), [
        'summary' => [
            'source_active' => 1,
            'source_inactive' => 0,
        ],
        'rows' => [],
    ]);

    expect($preview['mode'])->toBe('trunk_only')
        ->and($preview['trunk']['start_node_labels']['left']['text'])->toBe([
            'Key created',
            '2026-07-17 16:39 · active',
        ])
        ->and($preview['trunk']['node_labels'][1][0]['text'])->toBe([
            'key_updated_without_timestamp',
            'active',
        ])
        ->and($preview['trunk']['node_labels'][2][0]['text'])->toBe([
            'key_reviewed',
            '2026-07-19 09:15 · accepted',
        ])
        ->and($preview['trunk']['path_count'])->toBe(2)
        ->and($preview['limits']['available_events'])->toBe(3)
        ->and($preview['limits']['normal_events'])->toBe(3)
        ->and($preview['limits']['compacted_events'])->toBe(1);
});

it('treats non array timeline event summaries as empty instead of creating chunk labels', function (): void {
    $preview = RenderPreviewBuilder::build([
        'id' => 917,
        'chain_type' => 'single',
        'chain_status' => 'active',
        'translation_key' => 'ui.scalar.summary',
        'root_key_id' => 917,
        'created_at' => '2026-07-17 16:39:00',
        'timeline_event_summary' => 'dynamic_source_classification_changed: 99',
    ], new Collection([
        [
            'timestamp' => '2026-07-17 16:39:00',
            'event' => 'Key created',
            'state' => 'active',
        ],
    ]), new Collection(), [
        'summary' => [
            'source_active' => 1,
            'source_inactive' => 0,
        ],
        'rows' => [],
    ]);

    $allLabels = collect($preview['trunk']['node_labels'])
        ->flatMap(static fn(array $labels): array => $labels)
        ->pluck('text')
        ->flatten()
        ->all();

    expect($preview['limits'])->toMatchArray([
        'available_events' => 1,
        'normal_events' => 1,
        'dead_dev_events' => 0,
        'compacted_events' => 1,
    ])
        ->and($allLabels)->not->toContain('dynamic_source_classification_changed')
        ->and($preview['trunk']['path_count'])->toBe(1);
});

it('compensates rekey target bridges against trunk label collisions in the full render preview', function (): void {
    config()->set('tw-graph-data-driven-defaults.collision_compensation_enabled', true);
    config()->set('tw-graph-defaults.bridge_length', '20rem');
    config()->set('tw-graph-defaults.debug_bound_box_gap', '3rem');
    config()->set('tw-graph-data-driven-defaults.debug_bound_box_gap', '3rem');
    config()->set('tw-graph-data-driven-layout-corrections', []);

    $preview = RenderPreviewBuilder::build([
        'id' => 980,
        'chain_type' => 'moved',
        'chain_status' => 'active',
        'translation_key' => 'ui.save',
        'root_key_id' => 3576,
        'created_at' => '2026-07-17 16:39:00',
        'updated_at' => '2026-08-04 09:22:55',
        'timeline_event_summary' => [
            'key_created' => 1,
        ],
        'meta' => [
            'moved_relations' => [
                [
                    'translation_key' => 'ui.save',
                    'rekeyed_to_translation_key' => 'ui.button.save.save',
                    'lang_value_id' => 801,
                    'rekeyed_to_lang_value_id' => 802,
                    'locale' => 'en',
                    'updated_at' => '2026-08-04 09:22:55',
                ],
            ],
        ],
    ], new Collection([
        [
            'timestamp' => '2026-07-17 16:39:00',
            'event' => 'Key created',
            'state' => 'active',
        ],
    ]), new Collection(), [
        'summary' => [
            'source_active' => 1,
            'source_inactive' => 0,
        ],
        'rows' => [],
    ]);

    expect($preview['mode'])->toBe('trunk_with_limited_merge')
        ->and($preview['limits']['rendered_rekey_strangs'])->toBe(1)
        ->and($preview['limits']['available_rekey_relations'])->toBe(1)
        ->and($preview['rekeys'])->toHaveCount(1)
        ->and($preview['rekeys'][0])->toMatchArray([
            'component' => 'tw-graph.strang.rekey-target-right',
            'kind' => 'target',
            'side' => 'right',
            'attach_to' => 'strang.trunk.path.2.end',
            'bridge_length' => '25.73rem',
        ])
        ->and(data_get($preview, 'rekeys.0.layout.appliedCompensations.0'))->toMatchArray([
            'target' => 'strang.right.1.rekey.target.bridge1',
            'prop' => 'bridge_length',
            'baseValue' => '20rem',
            'effectiveValue' => '25.73rem',
            'gap' => '3rem',
            'reason' => 'Automatic trunk-label vs rekey-target-footprint collision compensation.',
        ])
        ->and(data_get($preview, 'rekeys.0.layout.rekeyBoundsDebug.0'))->toMatchArray([
            'type' => 'rekey-target-bridge',
            'id' => 'strang.right.1.rekey.target.bridge1.bounds',
            'side' => 'right',
        ]);
});

it('keeps configured rekey target bridge corrections visible when they prevent automatic compensation', function (): void {
    config()->set('tw-graph-defaults.bridge_length', '20rem');
    config()->set('tw-graph-defaults.debug_bound_box_gap', '3rem');
    config()->set('tw-graph-data-driven-defaults.debug_bound_box_gap', '3rem');
    config()->set('tw-graph-data-driven-layout-corrections', [
        'corrections' => [
            [
                'target' => 'strang.rekey.right.target.1',
                'prop' => 'bridge-length',
                'delta' => '4rem',
                'reason' => 'Deliberate authoring nudge before measured compensation.',
            ],
        ],
    ]);

    $preview = RenderPreviewBuilder::build([
        'id' => 980,
        'chain_type' => 'moved',
        'chain_status' => 'active',
        'translation_key' => 'ui.save',
        'root_key_id' => 3576,
        'created_at' => '2026-07-17 16:39:00',
        'updated_at' => '2026-08-04 09:22:55',
        'timeline_event_summary' => [
            'key_created' => 1,
        ],
        'meta' => [
            'moved_relations' => [
                [
                    'translation_key' => 'ui.save',
                    'rekeyed_to_translation_key' => 'ui.button.save.save',
                    'lang_value_id' => 801,
                    'rekeyed_to_lang_value_id' => 802,
                    'locale' => 'en',
                    'updated_at' => '2026-08-04 09:22:55',
                ],
            ],
        ],
    ], new Collection([
        [
            'timestamp' => '2026-07-17 16:39:00',
            'event' => 'Key created',
            'state' => 'active',
        ],
    ]), new Collection(), [
        'summary' => [
            'source_active' => 1,
            'source_inactive' => 0,
        ],
        'rows' => [],
    ]);

    expect($preview['rekeys'][0]['bridge_length'])->toBe('24rem')
        ->and(data_get($preview, 'rekeys.0.layout.appliedCorrections.0'))->toMatchArray([
            'target' => 'strang.right.1.rekey.target',
            'prop' => 'bridge_length',
            'baseValue' => '20rem',
            'effectiveValue' => '24rem',
        ])
        ->and(data_get($preview, 'rekeys.0.layout.appliedCompensations', []))->toBe([]);
});

it('side switches rekey source end labels when concrete trunk labels collide', function (): void {
    config()->set('tw-graph-data-driven-defaults.collision_compensation_enabled', true);
    config()->set('tw-graph-defaults.bridge_length', '20rem');
    config()->set('tw-graph-defaults.debug_bound_box_gap', '3rem');
    config()->set('tw-graph-data-driven-defaults.debug_bound_box_gap', '3rem');
    config()->set('tw-graph-data-driven-defaults.stem_length', '5.75rem');
    config()->set('tw-graph-data-driven-defaults.trunk_start_unlabeled_next_stem_factor', 0.25);

    $preview = RenderPreviewBuilder::build([
        'id' => 1052,
        'chain_type' => 'moved',
        'chain_status' => 'inactive',
        'translation_key' => 'ui.button.save.save',
        'root_key_id' => 4507,
        'created_at' => '2026-07-17 16:39:00',
        'updated_at' => '2026-08-04 09:22:55',
        'timeline_event_summary' => [
            'key_created' => 1,
            'key_reviewed' => 1,
        ],
        'meta' => [
            'moved_relations' => [
                [
                    'translation_key' => 'ui.save',
                    'rekeyed_to_translation_key' => 'ui.button.save.save',
                    'lang_value_id' => 801,
                    'rekeyed_to_lang_value_id' => 802,
                    'locale' => 'en',
                    'updated_at' => '2026-08-04 09:22:55',
                ],
            ],
        ],
    ], new Collection([
        [
            'timestamp' => '2026-07-17 16:39:00',
            'event' => 'Key created',
            'state' => 'active',
        ],
        [
            'timestamp' => '2026-08-04 09:22:55',
            'event' => 'key_reviewed',
            'state' => 'active',
        ],
    ]), new Collection(), [
        'summary' => [
            'source_active' => 0,
            'source_inactive' => 1,
        ],
        'rows' => [],
    ]);

    expect($preview['mode'])->toBe('trunk_with_limited_merge')
        ->and($preview['limits']['rendered_rekey_strangs'])->toBe(1)
        ->and($preview['rekeys'])->toHaveCount(1)
        ->and($preview['rekeys'][0])->toMatchArray([
            'component' => 'tw-graph.strang.rekey-source-left',
            'kind' => 'source',
            'side' => 'left',
            'attach_to' => 'strang.trunk.path.1.end',
        ])
        ->and($preview['rekeys'][0]['node_labels'][6])->toHaveKey('right')
        ->and($preview['rekeys'][0]['node_labels'][6])->not->toHaveKey('left')
        ->and($preview['rekeys'][0]['node_labels'][6]['right']['text'])->toBe([
            'rekeyed into this key ID #4507',
            'ui.save -> ui.button.save.save',
        ])
        ->and(data_get($preview, 'rekeys.0.layout.appliedCompensations.0'))->toMatchArray([
            'target' => 'strang.left.1.rekey.source.arc-south-east-2.end-label',
            'prop' => 'label_side',
            'baseValue' => 'left',
            'effectiveValue' => 'right',
            'reason' => 'Automatic trunk-label vs rekey-source-end-label side-switch compensation.',
        ])
        ->and(data_get($preview, 'rekeys.0.layout.appliedCompensations.0.sources.0'))->toMatchArray([
            'source' => 'trunk-rekey-source-label-collision',
            'against' => 'strang.left.1.rekey.source.arc-south-east-2.end-label.bounds',
        ]);
});

it('shares merge attachments with the first event labels without reserving an empty trunk stem', function (int $origins) {
    $preview = RenderPreviewBuilder::build([
        'id' => 42,
        'root_key_id' => 7,
        'chain_type' => 'single',
        'created_at' => '2026-01-01 10:00:00',
    ], collect([
        ['timestamp' => '2026-01-01 10:00:00', 'event' => 'Key created', 'state' => 'active'],
        ['timestamp' => '2026-01-02 10:00:00', 'event' => 'Key reviewed', 'state' => 'active'],
    ]), collect(range(1, $origins))->map(fn ($id) => [
        'finding_id' => $id,
        'translation_key' => 'example.key',
        'first_seen_at' => '2026-01-01 10:00:00',
    ]));

    expect($preview['merges'])->not->toBeEmpty()
        ->and($preview['trunk']['node_labels'])->toHaveKeys([1, 2])
        ->and($preview['trunk']['node_labels'][1][0]['text'][0])->toBe('Key created')
        ->and($preview['trunk']['node_labels'][2][0]['text'][0])->toBe('Key reviewed')
        ->and($preview['trunk']['path_lengths'][1]['nodeEndDot'])->toBeTrue();
    foreach ($preview['merges'] as $merge) {
        expect($merge['attach_to'])->toBe('strang.trunk.path.1.end');
    }
})->with([1, 2, 6]);

it('keeps branches on their chronological event and final height with multiple merges', function (string $side) {
    $preview = RenderPreviewBuilder::build([
        'id' => 73, 'root_key_id' => 7, 'chain_type' => 'single',
        'created_at' => '2026-01-01 10:00:00',
    ], collect([
        ['timestamp' => '2026-01-01 10:00:00', 'event' => 'key_created', 'state' => 'active'],
        ['timestamp' => '2026-01-02 10:00:00', 'event' => 'translation_key_updated', 'state' => 'active'],
        ['timestamp' => '2026-01-04 10:00:00', 'event' => 'key_reviewed', 'state' => 'active'],
    ]), collect(range(1, 6))->map(fn ($id) => [
        'finding_id' => $id, 'translation_key' => 'example.key',
        'first_seen_at' => '2026-01-01 10:00:00',
    ]), ['rows' => [[
        'finding_id' => 1, 'side' => $side, 'outcome_group' => 'ended after merge',
        'origin_key' => 'example.key', 'last_seen_at_raw' => '2026-01-03 10:00:00',
        'last_seen_at' => '2026-01-03 10:00:00',
    ]]]);

    expect($preview['merges'])->toHaveCount(2)
        ->and(array_column($preview['merges'], 'extension_count'))->toBe([2, 2])
        ->and($preview['branches'])->toHaveCount(1);
    $branch = $preview['branches'][0];
    expect($branch['attach_to'])->toBe('strang.trunk.path.2.end')
        ->and($preview['trunk']['node_labels'][2][0]['text'][0])->toBe('translation_key_updated');

    $height = (float) $preview['trunk']['start_length'];
    foreach ([1, 2] as $path) {
        $entry = $preview['trunk']['path_lengths'][$path];
        $length = is_array($entry) ? ($entry['length'] ?? $preview['graph']['stem_length']) : ($entry ?? $preview['graph']['stem_length']);
        $height += \Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry::evaluateRemExpression($length);
    }
    expect($branch['anchor_y_rem'])->toEqualWithDelta($height, 0.0001);
})->with(['left', 'right']);

it('positions all merge extension bounds identically for calculated and literal lengths', function (string $side) {
    $bounds = new ReflectionMethod(RenderPreviewBuilder::class, 'mergeBridgeBoundsDebug');
    $preview = [
        'side' => $side, 'bridge_length' => '27rem', 'stem_length' => '5.75rem',
        'attach_to' => 'strang.trunk.path.1.end', 'extension_count' => 3,
        'extension_bridge_length' => 'calc(20rem + 40rem)',
        'extension_stem_length' => 'calc(2rem + 3.75rem)',
        'stem_continuation' => [1 => ['length' => 'calc(10rem + 5rem)']],
    ];
    $calculated = $bounds->invoke(null, $preview, 0, [2 => 0.0]);
    $literal = $bounds->invoke(null, array_replace($preview, [
        'extension_bridge_length' => '60rem',
        'extension_stem_length' => '5.75rem',
        'stem_continuation' => [1 => ['length' => '15rem']],
    ]), 0, [2 => 0.0]);

    expect($calculated)->not->toBeEmpty()->toBe($literal);
})->with(['left', 'right']);


it('keeps authored values intact while automatic compensation is disabled', function () {
    expect(config('tw-graph-data-driven-defaults.collision_compensation_enabled'))->toBeFalse();
    $lengths = [1 => '4rem', 2 => '7rem'];
    $method = new ReflectionMethod(RenderPreviewBuilder::class, 'applyTrunkPathSpacingCompensation');
    $result = $method->invoke(null, $lengths, [1 => ['delta' => 12, 'collisionKey' => 'overlap']], [], '4rem', 1);
    expect($result['path_lengths'])->toBe($lengths)->and($result['applied'])->toBe([]);
    foreach (['applyMergeCollisionCompensation' => 'merges', 'applyRekeyTargetCompensationFromTrunkCollisions' => 'rekeys'] as $name => $key) {
        $authored = [['bridge_length' => '17rem', 'stem_length' => '9rem']];
        $method = new ReflectionMethod(RenderPreviewBuilder::class, $name);
        $result = $method->invokeArgs(null, $key === 'merges' ? [$authored] : [$authored, [['collision' => true]]]);
        expect($result[$key])->toBe($authored)->and($result['applied'])->toBe([]);
    }
});


it('never shortens compressed stems during compensation but still permits extensions', function () {
    $method = new ReflectionMethod(RenderPreviewBuilder::class, 'pathEntryWithLength');
    config()->set('tw-graph-defaults.compressed_stem_length', '4rem');
    $entry = ['component' => 'stem-compressed', 'length' => '6rem', 'labels' => ['left' => 'Bulk']];
    expect($method->invoke(null, $entry, '2rem'))->toBe($entry)
        ->and($method->invoke(null, $entry, '8rem'))->toBe(array_replace($entry, ['length' => '8rem']))
        ->and($method->invoke(null, ['component' => 'stem-compressed', 'length' => '1rem'], '2rem')['length'])->toBe('4rem')
        ->and($method->invoke(null, ['component' => 'path', 'length' => '6rem'], '2rem')['length'])->toBe('2rem');
});

it('uses the dedicated compressed default instead of the ordinary stem default', function () {
    config()->set('tw-graph-defaults.compressed_stem_length', '8rem');
    $method = new ReflectionMethod(RenderPreviewBuilder::class, 'pathEntryWithLength');
    $entry = ['component' => 'stem-compressed'];
    expect($method->invoke(null, $entry, '4rem')['length'])->toBe('8rem')
        ->and($method->invoke(null, $entry, '10rem')['length'])->toBe('10rem');
});
