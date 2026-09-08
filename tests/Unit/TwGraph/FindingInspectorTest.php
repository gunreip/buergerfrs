<?php

use Gunreip\TranslationWorkbench\Support\TwGraph\DataDriven\TimelineChainGraphData\FindingInspector;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function (): void {
    config()->set('tw-graph-data-driven-defaults.trunk_start_shift_enabled', false);
    config()->set('tw-graph-data-driven-defaults.merge_layout.vertical_stagger_enabled', false);

    Schema::shouldReceive('hasTable')->byDefault()->andReturnFalse();
});

it('builds a db-less finding inspection payload from available graph rows', function (): void {
    $inspection = FindingInspector::inspect(5486, [
        'id' => 1014,
        'chain_type' => 'bulk',
        'chain_status' => 'active',
        'translation_key' => 'ui.states.all',
        'root_key_id' => 5,
        'related_translation_keys' => ['ui.states.all'],
        'timeline_event_summary' => [],
    ], [
        [
            'first_root' => 'finding #5486',
            'first_origin_key' => 'ui.all',
            'origin_key' => 'ui.all',
            'source_path' => 'resources/views/example.blade.php',
            'first_timestamp' => '2026-08-04 09:22:11',
            'context' => 'All',
        ],
    ]);

    expect($inspection)->toMatchArray([
        'finding_id' => 5486,
        'finding' => null,
        'shared_candidate' => null,
        'reviews' => [],
        'timeline_events' => [],
        'lang_values' => [],
        'related_translation_keys' => ['ui.states.all'],
    ])
        ->and($inspection['origin_row']['first_root'])->toBe('finding #5486')
        ->and($inspection['rendered_as'])->toMatchArray([
            'side' => 'left',
            'strang' => 'tw-graph.strang.merge-left',
            'component_counter' => 1,
        ]);
});

it('keeps db-less finding inspection stable when the finding is not rendered', function (): void {
    $inspection = FindingInspector::inspect(9999, [
        'id' => 913,
        'chain_type' => 'single',
        'chain_status' => 'inactive',
        'translation_key' => 'ui.labels.actions',
        'root_key_id' => 3676,
        'timeline_event_summary' => [],
    ], [
        [
            'first_root' => 'finding #5486',
            'first_origin_key' => 'ui.all',
        ],
    ]);

    expect($inspection)->toMatchArray([
        'finding_id' => 9999,
        'finding' => null,
        'origin_row' => null,
        'rendered_as' => null,
        'shared_candidate' => null,
        'reviews' => [],
        'timeline_events' => [],
        'lang_values' => [],
        'related_translation_keys' => ['ui.labels.actions'],
    ]);
});

it('keeps db-less finding inspection stable when no main row is available', function (): void {
    $inspection = FindingInspector::inspect(5486, null, [
        [
            'first_root' => 'finding #5486',
            'first_origin_key' => 'ui.all',
        ],
    ]);

    expect($inspection)->toMatchArray([
        'finding_id' => 5486,
        'finding' => null,
        'origin_row' => [
            'first_root' => 'finding #5486',
            'first_origin_key' => 'ui.all',
        ],
        'rendered_as' => null,
        'shared_candidate' => null,
        'reviews' => [],
        'timeline_events' => [],
        'lang_values' => [],
        'related_translation_keys' => [],
    ]);
});

it('matches origin rows case insensitively when inspecting rendered findings', function (): void {
    $inspection = FindingInspector::inspect(5486, null, [
        [
            'first_root' => 'Finding #5486',
            'first_origin_key' => 'ui.all',
        ],
    ]);

    expect($inspection['origin_row'])->toMatchArray([
        'first_root' => 'Finding #5486',
        'first_origin_key' => 'ui.all',
    ]);
});

it('detects findings rendered inside aggregate merge previews', function (): void {
    config()->set('tw-graph-data-driven-defaults.merge_layout.direct_per_side_before_aggregate', 5);

    $originRows = array_map(
        static fn(int $number): array => [
            'first_root' => 'finding #' . (5400 + $number),
            'first_origin_key' => 'admin.example.origin.' . $number,
            'source_path' => 'resources/views/example-' . $number . '.blade.php',
            'first_timestamp' => sprintf('2026-08-%02d 09:22:11', $number),
            'context' => 'Example literal ' . $number,
        ],
        range(1, 14),
    );

    $inspection = FindingInspector::inspect(5407, [
        'id' => 1010,
        'chain_type' => 'bulk',
        'chain_status' => 'active',
        'translation_key' => 'ui.state.missing',
        'root_key_id' => 1010,
        'timeline_event_summary' => [],
    ], $originRows);

    expect($inspection['origin_row'])->toMatchArray([
        'first_root' => 'finding #5407',
        'first_origin_key' => 'admin.example.origin.7',
    ])
        ->and($inspection['rendered_as'])->toMatchArray([
            'side' => 'left',
            'strang' => 'tw-graph.strang.merge-left',
            'component_counter' => 1,
            'extension_count' => 4,
        ]);
});
