<?php

declare(strict_types=1);

namespace Gunreip\TranslationWorkbench\Support\TwGraph\DataDriven\TimelineChainGraphData;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

final class RenderPreviewBuilder
{
    /**
     * @param  array<string, mixed>  $mainRow
     * @param  Collection<int, array<string, mixed>>  $rootRows
     * @param  Collection<int, array<string, mixed>>  $originRows
     * @param  array<string, mixed>  $mergeOutcomes
     * @return array<string, mixed>
     */
    public static function build(
        array $mainRow,
        Collection $rootRows,
        Collection $originRows,
        array $mergeOutcomes = [],
    ): array {
        $maxEventLabels = 6;
        $eventRows = $rootRows
            ->filter(static fn(array $row): bool => filled($row['timestamp'] ?? null) || filled($row['event'] ?? null))
            ->sortBy(static fn(array $row): string => (string) ($row['timestamp'] ?? ''))
            ->values();
        $eventLabelRows = $eventRows
            ->take($maxEventLabels)
            ->values()
            ->map(static function (array $row, int $index): array {
                $timestamp = LabelFormatter::graphTimestampLabel($row['timestamp'] ?? null);

                return [
                    ...$row,
                    '_timestamp_label' => $timestamp,
                    '_timestamp_group' => filled($timestamp) ? $timestamp : 'event-row-' . $index,
                ];
            });
        $timelineEventSummary = is_array($mainRow['timeline_event_summary'] ?? null)
            ? $mainRow['timeline_event_summary']
            : [];
        $timelineClassificationSummary = self::timelineClassificationSummary($mainRow, $timelineEventSummary, $eventRows->count());
        $timelineEventCount = (int) $timelineClassificationSummary['total_count'];
        $timelineNormalEventCount = (int) $timelineClassificationSummary['normal_count'];
        $timelineDeadDevEventCount = (int) $timelineClassificationSummary['dead_dev_count'];
        $timelineEventTypes = $timelineClassificationSummary['normal_event_types'];
        $allTimelineEventTypes = $timelineClassificationSummary['all_event_types'];
        $classificationChangedEventCount = (int) $timelineEventTypes->get('dynamic_source_classification_changed', 0);
        $allClassificationChangedEventCount = (int) $allTimelineEventTypes->get('dynamic_source_classification_changed', 0);
        $classificationReferenceEventCount = max(
            (int) $timelineEventTypes->get('dynamic_source_classified', 0),
            (int) $timelineEventTypes->get('dynamic_runtime_source_link_suggested', 0),
        );
        $retainedClassificationEventCount = $allClassificationChangedEventCount >= 50
            ? max(1, $classificationReferenceEventCount)
            : $allClassificationChangedEventCount;
        $potentialDeadDevEventCount = max($timelineDeadDevEventCount, $allClassificationChangedEventCount - $retainedClassificationEventCount);
        $classificationNoiseReport = ClassificationNoiseAnalyzer::report(
            $mainRow,
            $allClassificationChangedEventCount,
            $retainedClassificationEventCount,
            $potentialDeadDevEventCount,
        );
        $timelineLabelItems = self::regularEventLabelItems($eventLabelRows);
        $eventPathEntries = [];
        $eventTypeBurst = self::eventTypeBurst($mainRow, $timelineEventTypes, $timelineNormalEventCount);
        if ($eventTypeBurst !== null) {
            $timelineLabelItems = $timelineLabelItems
                ->merge(data_get($eventTypeBurst, 'items', []))
                ->values();
        }
        $nodeLabels = [];
        $nodeIndex = 2;
        $timelineSegmentLength = '6rem';
        $previousTimelineItemType = null;
        $trunkTimelineAnchors = [];

        $timelineLabelItems
            ->sortBy([
                ['timestamp_sort', 'asc'],
                ['sort_index', 'asc'],
            ])
            ->values()
            ->each(static function (array $item) use (&$nodeLabels, &$eventPathEntries, &$nodeIndex, &$previousTimelineItemType, &$trunkTimelineAnchors, $timelineSegmentLength): void {
                $labels = (array) data_get($item, 'labels', []);

                if ($labels !== []) {
                    $nodeLabels[$nodeIndex] = $labels;

                    $itemType = (string) data_get($item, 'type', 'normal');
                    $isTimelineTypeTransition = $previousTimelineItemType !== null
                        && $previousTimelineItemType !== $itemType;
                    $pathEntry = [
                        'component' => $isTimelineTypeTransition ? 'stem-compressed' : 'path',
                        'length' => $timelineSegmentLength,
                        'labels' => $labels,
                    ];

                    if ($isTimelineTypeTransition) {
                        $pathEntry['capLength'] = '1.5rem';
                    }

                    $eventPathEntries[$nodeIndex] = $pathEntry;
                    $previousTimelineItemType = $itemType;
                    $trunkTimelineAnchors[] = [
                        'path' => $nodeIndex,
                        'anchor' => 'strang.trunk.path.' . $nodeIndex . '.end',
                        'timestamp' => (string) data_get($item, 'timestamp_sort', ''),
                        'type' => $itemType,
                        'event' => (string) data_get($labels, '0.text.0', ''),
                    ];
                }

                $nodeIndex++;
            });
        $lastLabelNodeIndex = $nodeLabels !== [] ? max(array_keys($nodeLabels)) : 1;
        $langValueLabels = LangValueLabels::active($mainRow);
        $mergePreviewHeadCandidates = 6;
        $mergePreviews = MergePreviewBuilder::previews($originRows, $mergePreviewHeadCandidates);
        $branchPreviews = BranchPreviewBuilder::previews($mergeOutcomes, $trunkTimelineAnchors);
        $trunkPathSpacingAdjustments = BranchLabelCollisionResolver::trunkPathSpacingAdjustments($branchPreviews);
        $rekeyPreviews = RekeyPreviewBuilder::previews($mainRow);
        $hasRekeyTargetPreview = collect($rekeyPreviews)->contains(static fn(array $preview): bool => (string) ($preview['kind'] ?? '') === 'target');
        $rekeyTargetPreview = collect($rekeyPreviews)->first(static fn(array $preview): bool => (string) ($preview['kind'] ?? '') === 'target');
        $rekeyTargetKeyId = data_get($rekeyTargetPreview, 'source.target_key_id');
        $rekeyTargetSourceKey = (string) data_get($rekeyTargetPreview, 'source.source_key', '');
        $rekeyTargetTargetKey = (string) data_get($rekeyTargetPreview, 'source.target_key', '');
        $rekeyTargetRelationLine = trim(
            LabelFormatter::graphKeyLabelText($rekeyTargetSourceKey, 44)
                . ' -> '
                . LabelFormatter::graphKeyLabelText($rekeyTargetTargetKey, 44),
            ' ->',
        );
        $rekeyTargetTrunkLabel = $hasRekeyTargetPreview
            ? [
                'text' => array_values(array_filter([
                    'rekeyed to this key ID #' . (string) ($rekeyTargetKeyId ?: '?'),
                    $rekeyTargetRelationLine,
                ])),
                'side' => 'right',
                'connectorLength' => '5rem',
                'badgeColor' => 'sky',
                'long' => true,
            ]
            : null;
        $basePathCount = max($hasRekeyTargetPreview ? 7 : 4, $lastLabelNodeIndex);
        $pathCount = max(
            $eventPathEntries !== [] ? $basePathCount : min(7, $basePathCount),
            $trunkPathSpacingAdjustments !== [] ? max(array_keys($trunkPathSpacingAdjustments)) : 0,
        );
        $mergePreviewCount = count($mergePreviews);
        $branchPreviewCount = collect($branchPreviews)->sum(static fn(array $preview): int => (int) ($preview['finding_count'] ?? 0));
        $rekeyPreviewCount = count($rekeyPreviews);
        $previewMode = $mergePreviewCount > 0 || $rekeyPreviewCount > 0 ? 'trunk_with_limited_merge' : 'trunk_only';
        $renderedMergeCandidates = collect($mergePreviews)
            ->sum(static fn(array $preview): int => 1 + (int) ($preview['extension_count'] ?? 0));
        $trunkStartTimestamp = LabelFormatter::graphTimestampLabel(
            data_get($eventRows->first(), 'timestamp')
                ?? ($mainRow['first_seen_at'] ?? null)
                ?? ($mainRow['created_at'] ?? null)
                ?? ($mainRow['updated_at'] ?? null),
        );
        $mergeOutcomeSummary = collect($mergeOutcomes['summary'] ?? []);
        $mergeOriginCountLabel = LabelFormatter::mergeOriginCountLabel($mergeOutcomeSummary);
        $trunkEndStateLine = trim(
            (int) $mergeOutcomeSummary->get('source_active', 0)
                . ' active - '
                . (int) $mergeOutcomeSummary->get('source_inactive', 0)
                . ' ended',
        );
        $trunkEndLabelLines = LabelFormatter::trunkEndLabelLines($mainRow, $trunkEndStateLine);
        $trunkPreview = [
            'component' => 'tw-graph.strang.trunk',
            'color' => 'green',
            'path_count' => $pathCount,
            'path_lengths' => collect(range(1, $pathCount))
                ->mapWithKeys(static function (int $pathNumber) use ($eventPathEntries, $rekeyTargetTrunkLabel, $trunkPathSpacingAdjustments): array {
                    $length = $pathNumber === 1
                        ? '24.5rem'
                        : ($pathNumber === 2 ? '7.5rem' : '5.5rem');
                    $length = self::addRem($length, (float) ($trunkPathSpacingAdjustments[$pathNumber] ?? 0.0));

                    $eventTypePathEntry = $eventPathEntries[$pathNumber] ?? null;
                    if (is_array($eventTypePathEntry)) {
                        return [$pathNumber => array_replace($eventTypePathEntry, ['length' => self::addRem(
                            data_get($eventTypePathEntry, 'length', $length),
                            (float) ($trunkPathSpacingAdjustments[$pathNumber] ?? 0.0),
                        )])];
                    }

                    if ($pathNumber === 7 && $rekeyTargetTrunkLabel !== null) {
                        return [
                            $pathNumber => [
                                'length' => $length,
                                'labels' => [$rekeyTargetTrunkLabel, null],
                            ],
                        ];
                    }

                    return [$pathNumber => $length];
                })
                ->all(),
            'end_length' => '6rem',
            'start_label' => [
                'text' => array_values(array_filter([
                    'key ID #' . (string) ($mainRow['root_key_id'] ?? '?'),
                    trim($trunkStartTimestamp . ' · ' . $mergeOriginCountLabel, ' ·'),
                ])),
            ],
            'end_label' => [
                'text' => $trunkEndLabelLines,
                'long' => true,
            ],
            'start_node_labels' => $langValueLabels,
            'node_labels' => $nodeLabels,
        ];
        $trunkLabelPaddingLevel = self::trunkNodeLabelPaddingLevel($trunkPreview);
        $horizontalPadding = self::hasLeftStrangs($mergePreviews, $rekeyPreviews, $branchPreviews)
            ? '12rem'
            : self::horizontalPaddingForLevel($trunkLabelPaddingLevel);

        return [
            'mode' => $previewMode,
            'reason' => $mergePreviewCount > 0
                ? 'Second visual pass: render the canonical trunk, limited origin merge candidates and per-finding ended branches.'
                : 'First visual pass: render only the canonical trunk before enabling data-driven merge and branch strangs.',
            'limits' => [
                'max_event_labels' => $maxEventLabels,
                'rendered_event_labels' => count($nodeLabels),
                'rendered_event_rows' => $eventLabelRows->count(),
                'available_events' => $timelineEventCount,
                'normal_events' => $timelineNormalEventCount,
                'dead_dev_events' => $timelineDeadDevEventCount,
                'compacted_events' => max(0, $timelineNormalEventCount - count($nodeLabels)),
                'available_event_types' => $timelineEventTypes->count(),
                'potential_dead_dev_events' => $potentialDeadDevEventCount,
                'retained_classification_events' => $retainedClassificationEventCount,
                'classification_noise_report' => $classificationNoiseReport,
                'top_event_summary' => $timelineEventTypes
                    ->take(5)
                    ->map(static fn(int $count, string $event): array => [
                        'event' => $event,
                        'count' => $count,
                    ])
                    ->values()
                    ->all(),
                'max_merge_candidates' => $originRows->count(),
                'head_merge_candidates' => $mergePreviewHeadCandidates,
                'rendered_merge_candidates' => $renderedMergeCandidates,
                'rendered_merge_strangs' => $mergePreviewCount,
                'available_merge_strangs' => $originRows->count(),
                'rendered_branch_candidates' => $branchPreviewCount,
                'rendered_branch_strangs' => count($branchPreviews),
                'available_branch_candidates' => (int) ($mergeOutcomes['summary']['branch_candidates'] ?? 0),
                'available_branch_findings' => (int) ($mergeOutcomes['summary']['branch_candidate_findings'] ?? 0),
                'available_ended_after_merge_rows' => (int) ($mergeOutcomes['summary']['ended_after_merge_rows'] ?? 0),
                'available_ended_after_merge_findings' => (int) ($mergeOutcomes['summary']['ended_after_merge_findings'] ?? 0),
                'rendered_rekey_strangs' => $rekeyPreviewCount,
                'available_rekey_relations' => collect(data_get($mainRow, 'meta.moved_relations', []))->count(),
            ],
            'graph' => [
                'graph_id' => 'timeline-chain-' . (int) ($mainRow['id'] ?? 0) . '-data-preview',
                'header' => [
                    'text' => [
                        'Timeline chain ID #' . (string) ($mainRow['id'] ?? '?'),
                        str((string) ($mainRow['chain_type'] ?? ''))->headline()
                            . ' · '
                            . str((string) ($mainRow['chain_status'] ?? ''))->headline()
                            . ' · '
                            . (string) ($mainRow['translation_key'] ?? ''),
                    ],
                    'badgeColor' => 'cyan',
                ],
                'color' => 'cyan',
                'line_length' => '3.5rem',
                'horizontal_padding' => $horizontalPadding,
                'horizontal_padding_debug' => [
                    'trunk_label_level' => $trunkLabelPaddingLevel,
                    'has_left_strangs' => self::hasLeftStrangs($mergePreviews, $rekeyPreviews, $branchPreviews),
                    'horizontal_padding' => $horizontalPadding,
                ],
                'slot_min_height' => max(
                    $mergePreviewCount > 0 ? 42 : 34,
                    ($pathCount + 3) * 4,
                    42 + (int) ceil($branchPreviewCount / 2) * 2,
                ) . 'rem',
            ],
            'trunk' => $trunkPreview,
            'merge' => $mergePreviews[0] ?? null,
            'merges' => $mergePreviews,
            'rekeys' => $rekeyPreviews,
            'branches' => $branchPreviews,
        ];
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $eventLabelRows
     * @return Collection<int, array{type: string, timestamp_sort: string, sort_index: int, labels: array<string, array<int, string>>}>
     */
    private static function regularEventLabelItems(Collection $eventLabelRows): Collection
    {
        $items = collect();
        $sortIndex = 0;

        $eventLabelRows
            ->groupBy('_timestamp_group')
            ->each(static function (Collection $timestampRows) use (&$items, &$sortIndex): void {
                $timestampRows
                    ->values()
                    ->chunk(2)
                    ->each(static function (Collection $chunk) use (&$items, &$sortIndex): void {
                        $labels = [];

                        foreach ($chunk->values() as $labelIndex => $row) {
                            $side = $labelIndex === 0 ? 'left' : 'right';
                            $event = trim((string) ($row['event'] ?? ''));
                            $state = trim((string) ($row['state'] ?? ''));
                            $timestamp = (string) ($row['_timestamp_label'] ?? '');
                            $stateLine = trim($timestamp . ' · ' . $state, ' ·');
                            $labelText = array_values(array_filter([
                                $event,
                                $stateLine,
                            ]));
                            $labels[] = [
                                'text' => $labelText,
                                'side' => $side,
                                'long' => self::usesLongEventLabel($event),
                            ];
                        }

                        if ($labels !== []) {
                            $items->push([
                                'type' => 'normal',
                                'timestamp_sort' => (string) data_get($chunk->first(), 'timestamp', ''),
                                'sort_index' => $sortIndex,
                                'labels' => $labels,
                            ]);
                            $sortIndex++;
                        }
                    });
            });

        return $items->values();
    }

    private static function usesLongEventLabel(string $event): bool
    {
        return in_array($event, [
            'key_finding_relation_commented_out',
            'key_finding_relation_obsoleted',
        ], true);
    }

    private static function addRem(mixed $value, float $delta): string
    {
        if ($delta === 0.0) {
            return (string) $value;
        }

        if (preg_match('/-?\d+(?:\.\d+)?/', (string) $value, $matches) !== 1) {
            return self::rem($delta);
        }

        return self::rem((float) $matches[0] + $delta);
    }

    private static function rem(float $value): string
    {
        return rtrim(rtrim(number_format($value, 2, '.', ''), '0'), '.') . 'rem';
    }

    private static function horizontalPaddingForLevel(string $required): string
    {
        return match ($required) {
            'long' => '20rem',
            'halfLong' => '16rem',
            default => '12rem',
        };
    }

    /**
     * Left-side strangs already expand the calculated canvas bounds through
     * their dev boxes. The extra label padding is only needed for trunk-only
     * left labels that would otherwise render outside the left canvas edge.
     *
     * @param  array<int, array<string, mixed>>  $mergePreviews
     * @param  array<int, array<string, mixed>>  $rekeyPreviews
     * @param  array<int, array<string, mixed>>  $branchPreviews
     */
    private static function hasLeftStrangs(array $mergePreviews, array $rekeyPreviews, array $branchPreviews): bool
    {
        return collect([$mergePreviews, $rekeyPreviews, $branchPreviews])
            ->flatten(1)
            ->contains(static fn(mixed $preview): bool => is_array($preview) && (string) ($preview['side'] ?? '') === 'left');
    }

    /**
     * Terminal trunk labels are centered at the path start/end and do not
     * affect the left canvas edge. Only labels attached to trunk nodes can
     * require extra horizontal padding in trunk-only graphs.
     *
     * @param  array<string, mixed>  $trunkPreview
     */
    private static function trunkNodeLabelPaddingLevel(array $trunkPreview): string
    {
        return self::labelPaddingLevel([
            'start_node_labels' => $trunkPreview['start_node_labels'] ?? [],
            'node_labels' => $trunkPreview['node_labels'] ?? [],
        ]);
    }

    private static function labelPaddingLevel(mixed $value): string
    {
        if (! is_array($value)) {
            return 'default';
        }

        $level = 'default';

        if ((bool) ($value['long'] ?? false)) {
            $level = 'long';
        } elseif ((bool) ($value['halfLong'] ?? false)) {
            $level = 'halfLong';
        }

        foreach ($value as $child) {
            $level = self::maxLabelPaddingLevel($level, self::labelPaddingLevel($child));
        }

        return $level;
    }

    private static function maxLabelPaddingLevel(string $current, string $candidate): string
    {
        $rank = [
            'default' => 0,
            'halfLong' => 1,
            'long' => 2,
        ];

        return ($rank[$candidate] ?? 0) > ($rank[$current] ?? 0)
            ? $candidate
            : $current;
    }

    private static function usesHalfLongChunkEventLabel(string $event): bool
    {
        return in_array($event, [
            'key_finding_relation_commented_out',
            'key_finding_relation_obsoleted',
            'scalar_key_transformed_to_array',
            'translation_key_bulk_equalized',
        ], true);
    }

    /**
     * @param  array<string, mixed>  $mainRow
     * @param  array<string, mixed>  $timelineEventSummary
     * @return array{total_count: int, normal_count: int, dead_dev_count: int, all_event_types: Collection<string, int>, normal_event_types: Collection<string, int>}
     */
    private static function timelineClassificationSummary(array $mainRow, array $timelineEventSummary, int $fallbackEventCount): array
    {
        $allEventTypes = collect($timelineEventSummary)
            ->mapWithKeys(static fn(mixed $count, string|int $event): array => [(string) $event => (int) $count])
            ->filter(static fn(int $count, string $event): bool => $event !== '' && $count > 0)
            ->sortDesc();
        $fallbackTotalCount = max((int) ($mainRow['timeline_event_count'] ?? 0), $fallbackEventCount);
        $eventIds = ValueNormalizer::integerList($mainRow['timeline_event_ids'] ?? []);

        if (
            $eventIds === []
            || ! Schema::hasTable('translation_workbench_timeline_events')
            || ! Schema::hasColumn('translation_workbench_timeline_events', 'event_classification')
        ) {
            return [
                'total_count' => $fallbackTotalCount,
                'normal_count' => $fallbackTotalCount,
                'dead_dev_count' => 0,
                'all_event_types' => $allEventTypes,
                'normal_event_types' => $allEventTypes,
            ];
        }

        $rows = DB::table('translation_workbench_timeline_events')
            ->whereIn('id', $eventIds)
            ->select('event_type', 'event_classification', DB::raw('count(*) as total'))
            ->groupBy('event_type', 'event_classification')
            ->get();
        $allEventTypes = $rows
            ->groupBy('event_type')
            ->map(static fn(Collection $typeRows): int => (int) $typeRows->sum('total'))
            ->filter(static fn(int $count, string $event): bool => $event !== '' && $count > 0)
            ->sortDesc();
        $normalEventTypes = $rows
            ->filter(static fn(object $row): bool => (string) ($row->event_classification ?? 'normal') !== 'dead_dev_event')
            ->groupBy('event_type')
            ->map(static fn(Collection $typeRows): int => (int) $typeRows->sum('total'))
            ->filter(static fn(int $count, string $event): bool => $event !== '' && $count > 0)
            ->sortDesc();
        $deadDevCount = (int) $rows
            ->filter(static fn(object $row): bool => (string) ($row->event_classification ?? 'normal') === 'dead_dev_event')
            ->sum('total');
        $totalCount = (int) $rows->sum('total');

        return [
            'total_count' => $totalCount,
            'normal_count' => max(0, $totalCount - $deadDevCount),
            'dead_dev_count' => $deadDevCount,
            'all_event_types' => $allEventTypes,
            'normal_event_types' => $normalEventTypes,
        ];
    }

    /**
     * @param  array<string, mixed>  $mainRow
     * @param  Collection<string, int>  $timelineEventTypes
     * @return array<string, mixed>|null
     */
    private static function eventTypeBurst(
        array $mainRow,
        Collection $timelineEventTypes,
        int $normalEventCount,
    ): ?array {
        if ($normalEventCount < 12 || $timelineEventTypes->isEmpty()) {
            return null;
        }

        $eventTypes = $timelineEventTypes->keys()->map(static fn(mixed $eventType): string => (string) $eventType)->values();
        $sampleRowsByType = self::timelineEventSampleByType(
            ValueNormalizer::integerList($mainRow['timeline_event_ids'] ?? []),
            $eventTypes->all(),
        );
        $items = [];
        $sortIndex = 10_000;

        foreach ($timelineEventTypes as $eventType => $count) {
            $sampleRow = $sampleRowsByType[(string) $eventType] ?? null;
            $sampleLabel = $sampleRow === null
                ? null
                : 'event ID #' . (string) ($sampleRow['id'] ?? '?');
            $sampleKeyLabel = $sampleRow === null
                ? null
                : 'key ID #' . (string) ($sampleRow['key_id'] ?? '?');
            $sampleFindingLabel = $sampleRow === null
                ? null
                : 'finding ID #' . (string) ($sampleRow['finding_id'] ?? '?');
            $sampleIdLine = trim(implode(' · ', array_filter([
                $sampleLabel,
                $sampleKeyLabel,
                $sampleFindingLabel,
            ])));
            $sampleTimestamp = $sampleRow === null
                ? null
                : LabelFormatter::graphTimestampLabel($sampleRow['created_at'] ?? null);

            $labels = [
                [
                    'side' => 'left',
                    'text' => array_values(array_filter([
                        (string) $eventType,
                        $sampleTimestamp,
                        number_format((int) $count) . ' events',
                    ])),
                    'color' => 'amber',
                    'badgeColor' => 'amber',
                    'halfLong' => self::usesHalfLongChunkEventLabel((string) $eventType),
                ],
                [
                    'side' => 'right',
                    'text' => array_values(array_filter([
                        LabelFormatter::ordinalSampleLine(1),
                        $sampleIdLine,
                    ])),
                    'color' => 'amber',
                    'badgeColor' => 'amber',
                    'maxLines' => 4,
                ],
            ];
            $items[] = [
                'type' => 'chunk',
                'timestamp_sort' => (string) ($sampleRow['created_at'] ?? ''),
                'sort_index' => $sortIndex,
                'labels' => $labels,
            ];
            $sortIndex++;
        }

        return [
            'items' => $items,
        ];
    }

    /**
     * @param  array<int, int>  $eventIds
     * @return array<int, array<string, mixed>>
     */
    private static function timelineEventSampleByType(array $eventIds, array $eventTypes): array
    {
        $eventTypes = collect($eventTypes)
            ->map(static fn(mixed $eventType): string => (string) $eventType)
            ->filter()
            ->values();

        if ($eventIds === [] || $eventTypes->isEmpty()) {
            return [];
        }

        $query = DB::table('translation_workbench_timeline_events')
            ->whereIn('id', $eventIds)
            ->whereIn('event_type', $eventTypes->all());

        if (Schema::hasColumn('translation_workbench_timeline_events', 'event_classification')) {
            $query->where('event_classification', '!=', 'dead_dev_event');
        }

        return $query
            ->orderBy('created_at')
            ->orderBy('id')
            ->get(['id', 'finding_id', 'key_id', 'event_type', 'created_at'])
            ->groupBy('event_type')
            ->map(static function (Collection $rows): array {
                $row = $rows->values()->first();

                return $row === null ? [] : (array) $row;
            })
            ->all();
    }
}
