<?php

use Gunreip\TranslationWorkbench\Support\TwGraph\DataDriven\TimelineChainGraphData\MergePreviewBuilder;
use Illuminate\Support\Collection;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function (): void {
    config()->set('tw-graph-defaults.colors.merge', 'amber');
    config()->set('tw-graph-defaults.colors.merge_aggregate', 'yellow');
    config()->set('tw-graph-data-driven-defaults.merge_layout.vertical_stagger_enabled', false);
    config()->set('tw-graph-data-driven-defaults.merge_layout.direct_per_side_before_aggregate', 5);
});

it('renders ten merge candidates as real merge and extension rows without aggregate placeholders', function (): void {
    $previews = MergePreviewBuilder::previews(mergeRows(10), 10);

    expect($previews)->toHaveCount(2);

    foreach ($previews as $preview) {
        expect($preview['extension_count'])->toBe(4)
            ->and($preview['extension_node_labels'])->toHaveCount(4)
            ->and($preview['extension_node_labels'])
            ->each(fn($labels) => $labels->toHaveKey('start'));
    }

    expect(collect($previews)->pluck('extension_node_labels')->flatten(1)->toArray())
        ->not->toContain('finding ID ?');
});

it('uses configured direct merge limit before inserting aggregate extensions', function (): void {
    config()->set('tw-graph-data-driven-defaults.merge_layout.direct_per_side_before_aggregate', 3);

    $previews = MergePreviewBuilder::previews(mergeRows(10), 6);
    $leftPreview = collect($previews)->firstWhere('side', 'left');
    $rightPreview = collect($previews)->firstWhere('side', 'right');

    expect($leftPreview['extension_node_labels'][3]['start']['text'])->toBe(['Aggregated origins (1)'])
        ->and($rightPreview['extension_node_labels'][3]['start']['text'])->toBe(['Aggregated origins (1)'])
        ->and($leftPreview['extension_node_labels'][4]['start']['text'][0])->toStartWith('finding ID #')
        ->and($rightPreview['extension_node_labels'][4]['start']['text'][0])->toStartWith('finding ID #')
        ->and(json_encode([$leftPreview['extension_node_labels'], $rightPreview['extension_node_labels']]))->not->toContain('finding ID ?');
});

it('keeps aggregation based on rendered merge rows instead of unavailable total rows', function (): void {
    $previews = MergePreviewBuilder::previews(mergeRows(6), 20);
    $leftPreview = collect($previews)->firstWhere('side', 'left');
    $rightPreview = collect($previews)->firstWhere('side', 'right');

    expect($leftPreview['extension_count'])->toBe(2)
        ->and($rightPreview['extension_count'])->toBe(2)
        ->and($leftPreview['extension_node_labels'])->toHaveKeys([1, 2])
        ->and($rightPreview['extension_node_labels'])->toHaveKeys([1, 2])
        ->and(json_encode([$leftPreview['extension_node_labels'], $rightPreview['extension_node_labels']]))->not->toContain('Aggregated origins')
        ->and(json_encode([$leftPreview['extension_node_labels'], $rightPreview['extension_node_labels']]))->not->toContain('finding ID ?');
});

it('returns no merge previews when no origin rows exist', function (): void {
    expect(MergePreviewBuilder::previews(new Collection(), 6))->toBe([]);
});

it('aggregates fourteen merge candidates as three hidden rows per side without phantom labels', function (): void {
    $previews = MergePreviewBuilder::previews(mergeRows(14), 6);

    expect($previews)->toHaveCount(2);

    foreach ($previews as $preview) {
        expect($preview['extension_count'])->toBe(4)
            ->and($preview['extension_node_labels'][3]['start']['text'])->toBe(['Aggregated origins (3)'])
            ->and($preview['extension_node_labels'][3])->not->toContain('findingID ?');
    }
});

it('aggregates eleven merge candidates only on the side that has more rows than the direct limit', function (): void {
    $previews = MergePreviewBuilder::previews(mergeRows(11), 6);

    $leftPreview = collect($previews)->firstWhere('side', 'left');
    $rightPreview = collect($previews)->firstWhere('side', 'right');

    expect($leftPreview['extension_count'])->toBe(4)
        ->and($leftPreview['extension_node_labels'][3]['start']['text'])->toBe(['Aggregated origins (2)'])
        ->and($leftPreview['extension_node_labels'][3][1]['left'])->toBe([
            'findingID #5407',
            '2026-08-07 09:22',
        ])
        ->and($leftPreview['extension_node_labels'][3][1]['right'])->toBe([
            'findingID #5409',
            '2026-08-09 09:22',
        ])
        ->and($leftPreview['extension_node_labels'][3])->not->toHaveKey(2)
        ->and($rightPreview['extension_count'])->toBe(4)
        ->and($rightPreview['extension_node_labels'][3]['start']['text'][0])->toStartWith('finding ID #');
});

it('aggregates twelve merge candidates symmetrically once both sides exceed the direct limit', function (): void {
    $previews = MergePreviewBuilder::previews(mergeRows(12), 6);
    $leftPreview = collect($previews)->firstWhere('side', 'left');
    $rightPreview = collect($previews)->firstWhere('side', 'right');

    expect($leftPreview['extension_count'])->toBe(4)
        ->and($rightPreview['extension_count'])->toBe(4)
        ->and($leftPreview['extension_node_labels'][3]['start']['text'])->toBe(['Aggregated origins (2)'])
        ->and($rightPreview['extension_node_labels'][3]['start']['text'])->toBe(['Aggregated origins (2)'])
        ->and($leftPreview['extension_node_labels'][3][1]['left'])->toBe([
            'findingID #5407',
            '2026-08-07 09:22',
        ])
        ->and($leftPreview['extension_node_labels'][3][1]['right'])->toBe([
            'findingID #5409',
            '2026-08-09 09:22',
        ])
        ->and($rightPreview['extension_node_labels'][3][1]['right'])->toBe([
            'findingID #5408',
            '2026-08-08 09:22',
        ])
        ->and($rightPreview['extension_node_labels'][3][1]['left'])->toBe([
            'findingID #5410',
            '2026-08-10 09:22',
        ])
        ->and($leftPreview['extension_node_labels'][3])->not->toHaveKey(2)
        ->and($rightPreview['extension_node_labels'][3])->not->toHaveKey(2);
});

it('aggregates thirteen merge candidates without inventing a missing paired finding', function (): void {
    $previews = MergePreviewBuilder::previews(mergeRows(13), 6);
    $leftPreview = collect($previews)->firstWhere('side', 'left');
    $rightPreview = collect($previews)->firstWhere('side', 'right');

    expect($leftPreview['extension_count'])->toBe(4)
        ->and($rightPreview['extension_count'])->toBe(4)
        ->and($leftPreview['extension_node_labels'][3]['start']['text'])->toBe(['Aggregated origins (3)'])
        ->and($rightPreview['extension_node_labels'][3]['start']['text'])->toBe(['Aggregated origins (2)'])
        ->and($leftPreview['extension_node_labels'][3][1])->toHaveKeys(['left', 'right'])
        ->and($rightPreview['extension_node_labels'][3][1])->toHaveKeys(['left', 'right'])
        ->and($leftPreview['extension_node_labels'][3][2])->toHaveKey('left')
        ->and($leftPreview['extension_node_labels'][3][2])->not->toHaveKey('right')
        ->and($rightPreview['extension_node_labels'][3])->not->toHaveKey(2)
        ->and($leftPreview['extension_node_labels'][4]['start']['text'][0])->toStartWith('finding ID #')
        ->and($rightPreview['extension_node_labels'][4]['start']['text'][0])->toStartWith('finding ID #')
        ->and(json_encode([$leftPreview['extension_node_labels'], $rightPreview['extension_node_labels']]))->not->toContain('finding ID ?');
});

it('aggregates fifteen merge candidates across uneven sides without phantom labels', function (): void {
    $previews = MergePreviewBuilder::previews(mergeRows(15), 6);
    $leftPreview = collect($previews)->firstWhere('side', 'left');
    $rightPreview = collect($previews)->firstWhere('side', 'right');

    expect($leftPreview['extension_count'])->toBe(4)
        ->and($rightPreview['extension_count'])->toBe(4)
        ->and($leftPreview['extension_node_labels'][3]['start']['text'])->toBe(['Aggregated origins (4)'])
        ->and($rightPreview['extension_node_labels'][3]['start']['text'])->toBe(['Aggregated origins (3)'])
        ->and($leftPreview['extension_node_labels'][3][1])->toHaveKeys(['left', 'right'])
        ->and($leftPreview['extension_node_labels'][3][2])->toHaveKeys(['left', 'right'])
        ->and($rightPreview['extension_node_labels'][3][1])->toHaveKeys(['left', 'right'])
        ->and($rightPreview['extension_node_labels'][3][2])->toHaveKey('right')
        ->and($rightPreview['extension_node_labels'][3][2])->not->toHaveKey('left')
        ->and(json_encode([$leftPreview['extension_node_labels'], $rightPreview['extension_node_labels']]))->not->toContain('finding ID ?');
});

it('uses data driven aggregate color for aggregate merge extension labels', function (): void {
    config()->set('tw-graph-defaults.colors.merge_aggregate', 'yellow');
    config()->set('tw-graph-data-driven-defaults.colors.merge_aggregate', 'lime');

    $previews = MergePreviewBuilder::previews(mergeRows(14), 6);
    $aggregateLabels = $previews[0]['extension_node_labels'][3];

    expect($aggregateLabels['start']['badgeColor'])->toBe('lime')
        ->and($aggregateLabels[1]['badgeColor'])->toBe('lime');
});

it('mirrors real extension label sides for left and right merge previews', function (): void {
    $previews = MergePreviewBuilder::previews(mergeRows(4), 4);
    $leftExtensionLabels = $previews[0]['extension_node_labels'][1];
    $rightExtensionLabels = $previews[1]['extension_node_labels'][1];

    expect($leftExtensionLabels[1])->toHaveKeys(['left', 'right'])
        ->and($leftExtensionLabels[1]['left'][0])->toBe('First seen')
        ->and($leftExtensionLabels[1]['right'][0])->toBe('Literal')
        ->and($rightExtensionLabels[1]['left'][0])->toBe('Literal')
        ->and($rightExtensionLabels[1]['right'][0])->toBe('First seen');
});

it('applies configured vertical stagger only to the configured sequence', function (): void {
    config()->set('tw-graph-data-driven-defaults.merge_layout.vertical_stagger_enabled', true);
    config()->set('tw-graph-data-driven-defaults.merge_layout.vertical_stagger_sequence', 'even');
    config()->set('tw-graph-data-driven-defaults.merge_layout.vertical_stagger_stem', 2);
    config()->set('tw-graph-data-driven-defaults.merge_layout.vertical_stagger_length', '11rem');

    $previews = MergePreviewBuilder::previews(mergeRows(6), 6);

    expect(data_get($previews[0], 'stem_continuation.1.staggered'))->toBeNull()
        ->and(data_get($previews[1], 'stem_continuation.1.staggered'))->toBeNull()
        ->and(data_get($previews[0], 'extension_stem_continuations.1.1.length'))->toBe('11rem')
        ->and(data_get($previews[1], 'extension_stem_continuations.1.1.length'))->toBe('11rem');
});

it('applies configured vertical stagger to odd merge sequences when requested', function (): void {
    config()->set('tw-graph-data-driven-defaults.merge_layout.vertical_stagger_enabled', true);
    config()->set('tw-graph-data-driven-defaults.merge_layout.vertical_stagger_sequence', 'odd');
    config()->set('tw-graph-data-driven-defaults.merge_layout.vertical_stagger_stem', 2);
    config()->set('tw-graph-data-driven-defaults.merge_layout.vertical_stagger_length', '9rem');

    $previews = MergePreviewBuilder::previews(mergeRows(5), 5);

    expect(data_get($previews[0], 'stem_continuation.1.length'))->toBe('9rem')
        ->and(data_get($previews[1], 'stem_continuation.1.length'))->toBe('9rem')
        ->and(data_get($previews[0], 'extension_stem_continuations.1.1.length'))->toBeNull()
        ->and(data_get($previews[1], 'extension_stem_continuations.1.1.length'))->toBeNull();
});

it('staggers aggregate merge extensions on the last existing continuation stem', function (): void {
    config()->set('tw-graph-data-driven-defaults.merge_layout.vertical_stagger_enabled', true);
    config()->set('tw-graph-data-driven-defaults.merge_layout.vertical_stagger_sequence', 'even');
    config()->set('tw-graph-data-driven-defaults.merge_layout.vertical_stagger_stem', 2);
    config()->set('tw-graph-data-driven-defaults.merge_layout.vertical_stagger_length', '12rem');

    $previews = MergePreviewBuilder::previews(mergeRows(14), 6);

    expect(data_get($previews[0], 'extension_stem_continuations.3.1.length'))->toBe('12rem')
        ->and(data_get($previews[0], 'extension_stem_continuations.3.1.staggered'))->toBeTrue();
});

function mergeRows(int $count): Collection
{
    return new Collection(array_map(
        static fn(int $number): array => [
            'first_root' => 'finding #' . (5400 + $number),
            'first_origin_key' => 'admin.example.origin.' . $number,
            'source_path' => 'resources/views/example-' . $number . '.blade.php',
            'first_timestamp' => sprintf('2026-08-%02d 09:22:11', (($number - 1) % 28) + 1),
            'context' => 'Example literal ' . $number,
            'last_event' => 'merged',
            'last_timestamp' => sprintf('2026-09-%02d 10:15:00', (($number - 1) % 28) + 1),
            'last_state' => 'obsolete',
        ],
        range(1, $count),
    ));
}
