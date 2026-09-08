<?php

use Gunreip\TranslationWorkbench\Support\TwGraph\DataDriven\TimelineChainGraphData;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function (): void {
    config()->set('tw-graph-data-driven-defaults.trunk_start_shift_enabled', false);
    config()->set('tw-graph-data-driven-defaults.merge_layout.vertical_stagger_enabled', false);

    Schema::shouldReceive('hasTable')->byDefault()->andReturnFalse();
});

it('returns an empty graph payload when no main row is available', function (): void {
    expect(TimelineChainGraphData::fromTimelineChain(null, [], []))->toBe([
        'state' => 'empty',
        'meta' => [
            'reason' => 'No timeline-chain main row is available.',
        ],
        'strangs' => [],
    ]);
});

it('builds a ready graph payload with normalized facts and render preview', function (): void {
    $payload = TimelineChainGraphData::fromTimelineChain([
        'id' => 1014,
        'chain_type' => 'bulk',
        'chain_status' => 'active',
        'translation_key' => 'ui.states.all',
        'root_key_id' => 5,
        'key_ids' => [5, '5', 'abc'],
        'finding_ids' => ['5486', null, 0],
        'review_ids' => [12, 12, 13],
        'timeline_event_ids' => ['20', 'nope'],
        'lang_value_ids' => [1507, '703'],
        'related_translation_keys' => ['ui.states.all', '', 'ui.other'],
        'timeline_event_summary' => [],
    ], new Collection([
        [
            'timestamp' => '2026-08-04 09:22:11',
            'branch' => 'Root',
            'event' => 'key_created',
            'state' => 'active',
        ],
    ]), new Collection());

    expect($payload)->toMatchArray([
        'state' => 'ready',
        'meta' => [
            'graph_id' => 'timeline-chain-1014',
            'source' => 'translation_workbench_timeline_chains',
            'chain_id' => 1014,
            'chain_type' => 'bulk',
            'chain_status' => 'active',
            'translation_key' => 'ui.states.all',
        ],
        'facts' => [
            'key_ids' => [5],
            'finding_ids' => [5486],
            'review_ids' => [12, 13],
            'timeline_event_ids' => [20],
            'lang_value_ids' => [1507, 703],
            'related_translation_keys' => ['ui.states.all', 'ui.other'],
        ],
    ])
        ->and($payload['strangs'])->toHaveKeys(['trunk', 'merge', 'branch', 'rekey'])
        ->and($payload['render_preview']['graph']['graph_id'])->toBe('timeline-chain-1014-data-preview')
        ->and($payload['merge_outcomes']['summary']['total'])->toBe(0);
});

it('keeps trunk-only root rows from creating public branch component intent', function (): void {
    $payload = TimelineChainGraphData::fromTimelineChain([
        'id' => 590,
        'chain_type' => 'single',
        'chain_status' => 'active',
        'translation_key' => 'ui.only.trunk',
        'root_key_id' => 3165,
        'key_ids' => [3165],
        'finding_ids' => [],
        'review_ids' => [],
        'timeline_event_ids' => [],
        'lang_value_ids' => [],
        'related_translation_keys' => ['ui.only.trunk'],
        'timeline_event_summary' => [],
    ], new Collection([
        [
            'timestamp' => '2026-07-22 20:26:00',
            'branch' => 'Root',
            'event' => 'key_created',
            'state' => 'active',
        ],
        [
            'timestamp' => '2026-07-22 20:27:00',
            'branch' => 'Root key',
            'event' => 'key_reviewed',
            'state' => 'accepted',
        ],
    ]), new Collection());

    expect(data_get($payload, 'strangs.branch.count'))->toBe(0)
        ->and(data_get($payload, 'component_intent.2'))->toMatchArray([
            'component' => 'tw-graph.strang.branch-left/right',
            'required' => false,
            'count' => 0,
        ]);
});
