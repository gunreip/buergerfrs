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
        $trunkAnchorYRem = 3.5 + 24.5;

        $timelineLabelItems
            ->sortBy([
                ['timestamp_sort', 'asc'],
                ['sort_index', 'asc'],
            ])
            ->values()
            ->each(static function (array $item) use (&$nodeLabels, &$eventPathEntries, &$nodeIndex, &$previousTimelineItemType, &$trunkTimelineAnchors, &$trunkAnchorYRem, $timelineSegmentLength): void {
                $labels = (array) data_get($item, 'labels', []);
                $trunkAnchorYRem += 6.0;

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
                        'y_rem' => $trunkAnchorYRem,
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
        $trunkPathLengths = collect(range(1, $pathCount))
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
            ->all();
        $branchPreviews = BranchLabelCollisionResolver::refreshDebugBounds(
            self::withFinalBranchAnchorY($branchPreviews, $trunkPathLengths, '3.5rem'),
        );
        $mergePreviews = self::withMergeBridgeBoundsDebug($mergePreviews, $trunkPathLengths, '3.5rem');
        $rekeyPreviews = self::withRekeyBoundsDebug($rekeyPreviews, $trunkPathLengths, '3.5rem');
        $trunkPreview = [
            'component' => 'tw-graph.strang.trunk',
            'color' => 'green',
            'path_count' => $pathCount,
            'path_lengths' => $trunkPathLengths,
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
            'layout' => [
                'trunkBoundsDebug' => [
                    ...self::trunkBoundsDebug($trunkPathLengths, '3.5rem', '6rem'),
                    ...self::trunkStartBoundsDebug(
                        '3.5rem',
                        $langValueLabels,
                        [
                            'text' => array_values(array_filter([
                                'key ID #' . (string) ($mainRow['root_key_id'] ?? '?'),
                                trim($trunkStartTimestamp . ' · ' . $mergeOriginCountLabel, ' ·'),
                            ])),
                        ],
                    ),
                    ...self::trunkEndBoundsDebug(
                        $trunkPathLengths,
                        '3.5rem',
                        '6rem',
                        [
                            'text' => $trunkEndLabelLines,
                            'long' => true,
                        ],
                    ),
                    ...self::trunkMiddleBoundsDebug(
                        $trunkPathLengths,
                        '3.5rem',
                        '6rem',
                        $nodeLabels,
                        self::trunkAttachedPathNumbers($mergePreviews, $rekeyPreviews, $branchPreviews),
                    ),
                    ...self::trunkLabelBoundsDebug(
                        $trunkPathLengths,
                        '3.5rem',
                        '6rem',
                        $langValueLabels,
                        $nodeLabels,
                        [
                            'start' => [
                                'label' => [
                                    'text' => array_values(array_filter([
                                        'key ID #' . (string) ($mainRow['root_key_id'] ?? '?'),
                                        trim($trunkStartTimestamp . ' · ' . $mergeOriginCountLabel, ' ·'),
                                    ])),
                                ],
                            ],
                            'end' => [
                                'label' => [
                                    'text' => $trunkEndLabelLines,
                                    'long' => true,
                                ],
                            ],
                        ],
                    ),
                ],
            ],
        ];
        $trunkPreview['layout']['trunkCollisionDebug'] = self::trunkPotentialCollisions(
            (array) data_get($trunkPreview, 'layout.trunkBoundsDebug', []),
            $branchPreviews,
            $mergePreviews,
        );
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
                        self::headlineLabel($mainRow['chain_type'] ?? '')
                            . ' · '
                            . self::headlineLabel($mainRow['chain_status'] ?? '')
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

    private static function headlineLabel(mixed $value): string
    {
        $value = trim(str_replace(['_', '-'], ' ', (string) $value));

        return $value === '' ? '' : mb_convert_case($value, MB_CASE_TITLE, 'UTF-8');
    }

    /**
     * Build a data-preview debug box for the final rendered trunk geometry.
     * It intentionally uses the already adjusted path lengths so branch bounds
     * can later be compared against the same trunk version that is rendered.
     *
     * DEV contract: the pure `strang.trunk.1.bounds` overlay is visually
     * verified against the rendered trunk body. Do not change this calculation
     * as a side effect of branch/merge tuning; add a separate bounds layer when
     * another footprint needs to be inspected.
     *
     * @param  array<int, mixed>  $pathLengths
     * @return array<int, array<string, string>>
     */
    private static function trunkBoundsDebug(array $pathLengths, string $startLength, string $endLength): array
    {
        $height = self::toRem($startLength)
            + collect($pathLengths)
                ->map(static fn(mixed $entry): float => self::pathEntryLength($entry))
                ->sum()
            + self::toRem($endLength);

        return [[
            'type' => 'strang',
            'id' => 'strang.trunk.1.bounds',
            'x' => '-0.95rem',
            'y' => '0rem',
            'width' => '1.9rem',
            'height' => self::rem($height),
        ]];
    }

    /**
     * Sub-bounds for the first trunk segment only. Keep this separate from the
     * verified full trunk bounds: aggregate bounds can overlap another strang
     * while the concrete trunk-start footprint still leaves enough room.
     *
     * @param  array<string, mixed>  $startNodeLabels
     * @param  array<string, mixed>  $startLabel
     * @return array<int, array<string, string>>
     */
    private static function trunkStartBoundsDebug(
        string $startLength,
        array $startNodeLabels,
        array $startLabel,
    ): array {
        $startHeight = self::toRem($startLength);
        $bounds = [
            'xStart' => -0.95,
            'xEnd' => 0.95,
            'yStart' => 0.0,
            'yEnd' => $startHeight,
        ];

        foreach (self::labelsFromSideMap($startNodeLabels) as $label) {
            $bounds = self::expandBoundsForLabel($bounds, $label, $startHeight);
        }

        if (filled(data_get($startLabel, 'text'))) {
            $bounds = self::expandBoundsForLabel(
                $bounds,
                $startLabel,
                0.0,
                (string) data_get($startLabel, 'side', 'bottom'),
                self::toRem(data_get($startLabel, 'offset', '0.75rem')),
            );
        }

        return [[
            'type' => 'start-label-inclusive',
            'id' => 'strang.trunk.1.start.label-bounds',
            'x' => self::rem($bounds['xStart']),
            'y' => self::rem($bounds['yStart']),
            'width' => self::rem($bounds['xEnd'] - $bounds['xStart']),
            'height' => self::rem($bounds['yEnd'] - $bounds['yStart']),
        ]];
    }

    /**
     * Sub-bounds for the final trunk segment only. This mirrors trunk-start
     * bounds and keeps terminal label collisions inspectable without widening
     * the already verified pure trunk-body bounds.
     *
     * @param  array<int, mixed>  $pathLengths
     * @param  array<string, mixed>  $endLabel
     * @return array<int, array<string, string>>
     */
    private static function trunkEndBoundsDebug(
        array $pathLengths,
        string $startLength,
        string $endLength,
        array $endLabel,
    ): array {
        $bodyHeight = self::toRem($startLength)
            + collect($pathLengths)
                ->map(static fn(mixed $entry): float => self::pathEntryLength($entry))
                ->sum()
            + self::toRem($endLength);
        $endStartY = $bodyHeight - self::toRem($endLength);
        $bounds = [
            'xStart' => -0.95,
            'xEnd' => 0.95,
            'yStart' => $endStartY,
            'yEnd' => $bodyHeight,
        ];

        if (filled(data_get($endLabel, 'text'))) {
            $bounds = self::expandBoundsForLabel(
                $bounds,
                $endLabel,
                $bodyHeight,
                (string) data_get($endLabel, 'side', 'top'),
                self::toRem(data_get($endLabel, 'offset', '0.75rem')),
            );
        }

        return [[
            'type' => 'end-label-inclusive',
            'id' => 'strang.trunk.1.end.label-bounds',
            'x' => self::rem($bounds['xStart']),
            'y' => self::rem($bounds['yStart']),
            'width' => self::rem($bounds['xEnd'] - $bounds['xStart']),
            'height' => self::rem($bounds['yEnd'] - $bounds['yStart']),
        ]];
    }

    /**
     * Build a second trunk debug box that includes labels attached to trunk
     * start/end and trunk nodes. This stays separate from the pure strang box
     * so we can compare geometry body vs. label footprint in DEV mode.
     *
     * @param  array<int, mixed>  $pathLengths
     * @param  array<string, mixed>  $startNodeLabels
     * @param  array<int|string, mixed>  $nodeLabels
     * @param  array<string, mixed>  $terminalLabels
     * @return array<int, array<string, string>>
     */
    private static function trunkLabelBoundsDebug(
        array $pathLengths,
        string $startLength,
        string $endLength,
        array $startNodeLabels,
        array $nodeLabels,
        array $terminalLabels,
    ): array {
        $bodyHeight = self::toRem($startLength)
            + collect($pathLengths)
                ->map(static fn(mixed $entry): float => self::pathEntryLength($entry))
                ->sum()
            + self::toRem($endLength);
        $bounds = [
            'xStart' => -0.95,
            'xEnd' => 0.95,
            'yStart' => 0.0,
            'yEnd' => $bodyHeight,
        ];
        $nodeAnchors = self::trunkNodeAnchors($pathLengths, $startLength);

        foreach (self::labelsFromSideMap($startNodeLabels) as $label) {
            $bounds = self::expandBoundsForLabel($bounds, $label, $nodeAnchors[1] ?? self::toRem($startLength));
        }

        foreach ($nodeLabels as $pathNumber => $labels) {
            $anchorY = $nodeAnchors[(int) $pathNumber + 1] ?? null;

            if ($anchorY === null) {
                continue;
            }

            foreach (self::labelsFromMixed($labels) as $label) {
                $bounds = self::expandBoundsForLabel($bounds, $label, $anchorY);
            }
        }

        foreach (['start', 'end'] as $terminal) {
            $entry = (array) data_get($terminalLabels, $terminal, []);
            $label = (array) data_get($entry, 'label', []);

            if ($label === [] || blank(data_get($label, 'text'))) {
                continue;
            }

            $bounds = self::expandBoundsForLabel(
                $bounds,
                $label,
                $terminal === 'start' ? 0.0 : $bodyHeight,
                $terminal === 'start' ? 'bottom' : 'top',
                self::toRem(data_get($label, 'offset', '0.75rem')),
            );
        }

        return [[
            'type' => 'label-inclusive',
            'id' => 'strang.trunk.1.label-bounds',
            'x' => self::rem($bounds['xStart']),
            'y' => self::rem($bounds['yStart']),
            'width' => self::rem($bounds['xEnd'] - $bounds['xStart']),
            'height' => self::rem($bounds['yEnd'] - $bounds['yStart']),
        ]];
    }

    /**
     * Split the trunk body into label-inclusive middle zones between external
     * strang attachment points. These boxes intentionally exclude the already
     * dedicated trunk-start and trunk-end boxes.
     *
     * @param  array<int, mixed>  $pathLengths
     * @param  array<int|string, mixed>  $nodeLabels
     * @param  array<int, int>  $attachedPathNumbers
     * @return array<int, array<string, string>>
     */
    private static function trunkMiddleBoundsDebug(
        array $pathLengths,
        string $startLength,
        string $endLength,
        array $nodeLabels,
        array $attachedPathNumbers,
    ): array {
        $pathCount = count($pathLengths);
        $bodyHeight = self::toRem($startLength)
            + collect($pathLengths)
                ->map(static fn(mixed $entry): float => self::pathEntryLength($entry))
                ->sum()
            + self::toRem($endLength);
        $middleEndY = $bodyHeight - self::toRem($endLength);
        $nodeAnchors = self::trunkNodeAnchors($pathLengths, $startLength);
        $attachedPathNumbers = collect($attachedPathNumbers)
            ->filter(static fn(int $pathNumber): bool => $pathNumber >= 1 && $pathNumber <= $pathCount)
            ->unique()
            ->sort()
            ->values()
            ->all();
        $zoneEdges = collect([1, ...collect($attachedPathNumbers)->map(static fn(int $pathNumber): int => $pathNumber + 1)->all(), $pathCount + 1])
            ->unique()
            ->sort()
            ->values()
            ->all();
        $boxes = [];

        foreach ($zoneEdges as $index => $startPath) {
            $endPathExclusive = $zoneEdges[$index + 1] ?? null;

            if ($endPathExclusive === null || $endPathExclusive <= $startPath) {
                continue;
            }

            $yStart = $nodeAnchors[$startPath] ?? self::toRem($startLength);
            $yEnd = $nodeAnchors[$endPathExclusive] ?? $middleEndY;

            if ($yEnd <= $yStart) {
                continue;
            }

            $bounds = [
                'xStart' => -0.95,
                'xEnd' => 0.95,
                'yStart' => $yStart,
                'yEnd' => $yEnd,
            ];

            for ($pathNumber = $startPath; $pathNumber < $endPathExclusive; $pathNumber++) {
                $anchorY = $nodeAnchors[$pathNumber + 1] ?? null;

                if ($anchorY === null) {
                    continue;
                }

                foreach (self::labelsFromMixed($nodeLabels[$pathNumber] ?? []) as $label) {
                    $bounds = self::expandBoundsForLabel($bounds, $label, $anchorY);
                }
            }

            $boxes[] = [
                'type' => 'middle-label-inclusive',
                'id' => 'strang.trunk.1.middle.' . (count($boxes) + 1) . '.label-bounds',
                'x' => self::rem($bounds['xStart']),
                'y' => self::rem($bounds['yStart']),
                'width' => self::rem($bounds['xEnd'] - $bounds['xStart']),
                'height' => self::rem($bounds['yEnd'] - $bounds['yStart']),
            ];
        }

        return $boxes;
    }

    /**
     * @param  array<int, array<string, mixed>>  $mergePreviews
     * @param  array<int, array<string, mixed>>  $rekeyPreviews
     * @param  array<int, array<string, mixed>>  $branchPreviews
     * @return array<int, int>
     */
    private static function trunkAttachedPathNumbers(array $mergePreviews, array $rekeyPreviews, array $branchPreviews): array
    {
        return collect([$mergePreviews, $rekeyPreviews, $branchPreviews])
            ->flatten(1)
            ->map(static fn(mixed $preview): string => is_array($preview) ? (string) ($preview['attach_to'] ?? '') : '')
            ->filter()
            ->map(static function (string $attachTo): ?int {
                if (preg_match('/strang\.trunk\.path\.(\d+)\.end/', $attachTo, $matches) !== 1) {
                    return null;
                }

                return (int) $matches[1];
            })
            ->filter(static fn(?int $pathNumber): bool => $pathNumber !== null)
            ->unique()
            ->sort()
            ->values()
            ->all();
    }

    /**
     * Report-only collision layer: use the same debug boxes that are rendered
     * in DEV mode and do not mutate graph geometry here.
     *
     * @param  array<int, array<string, mixed>>  $trunkBounds
     * @param  array<int, array<string, mixed>>  $branchPreviews
     * @return array<int, array<string, string>>
     */
    private static function trunkPotentialCollisions(array $trunkBounds, array $branchPreviews, array $mergePreviews = []): array
    {
        $trunkBoxes = collect($trunkBounds)
            ->filter(static fn(array $box): bool => in_array((string) ($box['type'] ?? ''), [
                'start-label-inclusive',
                'middle-label-inclusive',
                'end-label-inclusive',
            ], true))
            ->map(static fn(array $box): array => self::debugBoxToFloat($box))
            ->values();
        $branchBoxes = collect($branchPreviews)
            ->flatMap(static fn(array $branch): array => (array) data_get($branch, 'layout.branchBoundsDebug', []))
            ->map(static fn(array $box): array => self::debugBoxToFloat($box))
            ->values();
        $mergeBoxes = collect($mergePreviews)
            ->flatMap(static fn(array $merge): array => (array) data_get($merge, 'layout.mergeBoundsDebug', []))
            ->map(static fn(array $box): array => self::debugBoxToFloat($box))
            ->values();
        $collisions = [];

        foreach ($trunkBoxes as $trunkBox) {
            foreach ($branchBoxes->merge($mergeBoxes) as $againstBox) {
                if (! self::debugBoxesOverlap($trunkBox, $againstBox)) {
                    continue;
                }

                $collisions[] = [
                    'type' => 'trunk-strang-bounds',
                    'trunk' => (string) ($trunkBox['id'] ?? ''),
                    'against' => (string) ($againstBox['id'] ?? ''),
                    'trunkType' => (string) ($trunkBox['type'] ?? ''),
                    'againstType' => (string) ($againstBox['type'] ?? ''),
                    'overlapWidth' => self::rem(min((float) $trunkBox['xEnd'], (float) $againstBox['xEnd']) - max((float) $trunkBox['xStart'], (float) $againstBox['xStart'])),
                    'overlapHeight' => self::rem(min((float) $trunkBox['yEnd'], (float) $againstBox['yEnd']) - max((float) $trunkBox['yStart'], (float) $againstBox['yStart'])),
                ];
            }
        }

        return $collisions;
    }

    /**
     * @param  array<string, mixed>  $box
     * @return array<string, mixed>
     */
    private static function debugBoxToFloat(array $box): array
    {
        $x = self::toRem($box['x'] ?? '0rem');
        $y = self::toRem($box['y'] ?? '0rem');
        $width = self::toRem($box['width'] ?? '0rem');
        $height = self::toRem($box['height'] ?? '0rem');

        return [
            ...$box,
            'xStart' => $x,
            'xEnd' => $x + $width,
            'yStart' => $y,
            'yEnd' => $y + $height,
        ];
    }

    /**
     * @param  array<string, mixed>  $first
     * @param  array<string, mixed>  $second
     */
    private static function debugBoxesOverlap(array $first, array $second): bool
    {
        return (float) $first['xStart'] < (float) $second['xEnd']
            && (float) $first['xEnd'] > (float) $second['xStart']
            && (float) $first['yStart'] < (float) $second['yEnd']
            && (float) $first['yEnd'] > (float) $second['yStart'];
    }

    /**
     * @param  array<int, array<string, mixed>>  $mergePreviews
     * @param  array<int, mixed>  $trunkPathLengths
     * @return array<int, array<string, mixed>>
     *
     * DEV contract: merge/merge-extension bounds are visually verified against
     * start/stem label areas, tail stems, and bridges. Do not tune these boxes
     * as a side effect of graph layout changes; add a new explicit debug layer
     * if another footprint needs to be inspected.
     */
    private static function withMergeBridgeBoundsDebug(array $mergePreviews, array $trunkPathLengths, string $trunkStartLength): array
    {
        $trunkAnchors = self::trunkNodeAnchors($trunkPathLengths, $trunkStartLength);

        foreach ($mergePreviews as $index => $mergePreview) {
            $mergePreviews[$index]['layout'] = [
                ...((array) ($mergePreviews[$index]['layout'] ?? [])),
                'mergeBoundsDebug' => self::mergeBridgeBoundsDebug($mergePreview, $index, $trunkAnchors),
            ];
        }

        return $mergePreviews;
    }

    /**
     * @param  array<int, array<string, mixed>>  $rekeyPreviews
     * @param  array<int, mixed>  $trunkPathLengths
     * @return array<int, array<string, mixed>>
     *
     * DEV contract: rekey-source uses the same start/stem bounds model as
     * merge, while rekey-target follows the branch-like bridge/body/end model.
     * Keep both derived from their rendered chain props instead of introducing
     * rekey-only offsets.
     */
    private static function withRekeyBoundsDebug(array $rekeyPreviews, array $trunkPathLengths, string $trunkStartLength): array
    {
        $trunkAnchors = self::trunkNodeAnchors($trunkPathLengths, $trunkStartLength);

        foreach ($rekeyPreviews as $index => $rekeyPreview) {
            $boundsDebug = (string) ($rekeyPreview['kind'] ?? '') === 'target'
                ? self::rekeyTargetBoundsDebug($rekeyPreview, $index, $trunkAnchors)
                : self::rekeySourceBoundsDebug($rekeyPreview, $index, $trunkAnchors);

            $rekeyPreviews[$index]['layout'] = [
                ...((array) ($rekeyPreviews[$index]['layout'] ?? [])),
                'rekeyBoundsDebug' => $boundsDebug,
            ];
        }

        return $rekeyPreviews;
    }

    /**
     * @param  array<string, mixed>  $mergePreview
     * @param  array<int, float>  $trunkAnchors
     * @return array<int, array<string, string>>
     */
    private static function mergeBridgeBoundsDebug(array $mergePreview, int $index, array $trunkAnchors): array
    {
        $side = (string) ($mergePreview['side'] ?? 'left');
        $direction = $side === 'left' ? -1.0 : 1.0;
        $componentCounter = $index + 1;
        $componentId = 'strang.merge-' . $side . '.' . $componentCounter;
        $attachPath = self::trunkAttachPathNumber((string) ($mergePreview['attach_to'] ?? 'strang.trunk.path.1.end')) ?? 1;
        $attachY = $trunkAnchors[$attachPath + 1] ?? 0.0;
        $bridgeHeight = 1.5;
        $bridgeLength = self::toRem($mergePreview['bridge_length'] ?? '4rem');
        $arcOutSize = self::toRem(data_get($mergePreview, 'arc_sizes.2', data_get($mergePreview, 'arc_sizes.out', '2.75rem'))) ?: 2.75;
        $startLength = self::toRem($mergePreview['start_length'] ?? $arcOutSize . 'rem');
        $stemLength = self::toRem($mergePreview['stem_length'] ?? '4rem');
        $stemContinuationLength = self::stemContinuationLength((array) ($mergePreview['stem_continuation'] ?? []), $stemLength);
        $arcInSize = self::toRem(data_get($mergePreview, 'arc_sizes.1', data_get($mergePreview, 'arc_sizes.in', '2.75rem'))) ?: 2.75;
        $mainInnerX = $direction * $arcOutSize;
        $mainOuterX = $direction * ($arcOutSize + $bridgeLength);
        $mainBridgeY = $attachY - $arcOutSize;
        $mainStemEndY = $mainBridgeY - $arcInSize;
        $mainStartY = $mainStemEndY - $stemContinuationLength - $stemLength - $startLength;
        $mainStemX = $mainOuterX + ($direction * $arcInSize);
        $mainStartStemId = $componentId . '.main.path.merge.start-stem';
        $boxes = [
            self::mergeStartStemBoxToRem(
                $mainStartStemId . '.bounds',
                'merge-start-stem',
                $mainStemX,
                $mainStartY,
                $mainStemEndY,
                $side,
                (array) ($mergePreview['node_labels'] ?? []),
                (array) ($mergePreview['start_label'] ?? []),
                $startLength,
                $stemLength,
                (array) ($mergePreview['stem_continuation'] ?? []),
            ),
            ...self::mergeStartStemSubBoxesToRem(
                $mainStartStemId,
                'merge-start-stem',
                $mainStemX,
                $mainStartY,
                $mainStemEndY,
                $side,
                (array) ($mergePreview['node_labels'] ?? []),
                (array) ($mergePreview['start_label'] ?? []),
                $startLength,
                $stemLength,
                (array) ($mergePreview['stem_continuation'] ?? []),
            ),
            self::lineBoxToRem(
                $componentId . '.main.path.merge.bridge1.bounds',
                'merge-bridge',
                min($mainInnerX, $mainOuterX),
                max($mainInnerX, $mainOuterX),
                $mainBridgeY,
                $bridgeHeight,
                $side,
            ),
        ];
        $extensionCount = max(0, (int) ($mergePreview['extension_count'] ?? 0));
        $extensionBridgeDefault = self::toRem($mergePreview['extension_bridge_length'] ?? $mergePreview['bridge_length'] ?? '4rem');
        $extensionTargetX = $mainOuterX;
        $extensionTargetY = $mainBridgeY;
        $extensionStartLength = self::toRem($mergePreview['extension_start_length'] ?? '2.75rem');
        $extensionStemDefault = self::toRem($mergePreview['extension_stem_length'] ?? $mergePreview['stem_length'] ?? '4rem');
        $extensionArcDefault = self::toRem($mergePreview['extension_arc_size'] ?? '2.75rem');

        for ($extensionIndex = 1; $extensionIndex <= $extensionCount; $extensionIndex++) {
            $extensionBridgeLength = self::toRem(data_get($mergePreview, 'extension_bridge_continuations.' . $extensionIndex, $extensionBridgeDefault));
            $extensionStemLength = self::toRem(data_get($mergePreview, 'extension_stem_lengths.' . $extensionIndex, $extensionStemDefault));
            $extensionStemContinuationLength = self::stemContinuationLength(
                (array) data_get($mergePreview, 'extension_stem_continuations.' . $extensionIndex, []),
                $extensionStemLength,
            );
            $extensionArcSize = self::toRem(data_get($mergePreview, 'extension_arc_sizes.' . $extensionIndex, $extensionArcDefault)) ?: 2.75;
            $extensionOuterX = $extensionTargetX + ($direction * $extensionBridgeLength);
            $extensionStemX = $extensionOuterX + ($direction * $extensionArcSize);
            $extensionStemEndY = $extensionTargetY - $extensionArcSize;
            $extensionStartY = $extensionStemEndY - $extensionStemContinuationLength - $extensionStemLength - $extensionStartLength;
            $extensionLabels = (array) data_get($mergePreview, 'extension_node_labels.' . $extensionIndex, []);
            $extensionStartStemId = $componentId . '.extension' . $extensionIndex . '.path.merge-extension.start-stem';
            $boxes[] = self::mergeStartStemBoxToRem(
                $extensionStartStemId . '.bounds',
                'merge-extension-start-stem',
                $extensionStemX,
                $extensionStartY,
                $extensionStemEndY,
                $side,
                $extensionLabels,
                (array) data_get($extensionLabels, 'start', []),
                $extensionStartLength,
                $extensionStemLength,
                (array) data_get($mergePreview, 'extension_stem_continuations.' . $extensionIndex, []),
            );
            array_push(
                $boxes,
                ...self::mergeStartStemSubBoxesToRem(
                    $extensionStartStemId,
                    'merge-extension-start-stem',
                    $extensionStemX,
                    $extensionStartY,
                    $extensionStemEndY,
                    $side,
                    $extensionLabels,
                    (array) data_get($extensionLabels, 'start', []),
                    $extensionStartLength,
                    $extensionStemLength,
                    (array) data_get($mergePreview, 'extension_stem_continuations.' . $extensionIndex, []),
                ),
            );
            $boxes[] = self::lineBoxToRem(
                $componentId . '.extension' . $extensionIndex . '.path.merge-extension.bridge1.bounds',
                'merge-extension-bridge',
                min($extensionOuterX, $extensionTargetX),
                max($extensionOuterX, $extensionTargetX),
                $extensionTargetY,
                $bridgeHeight,
                $side,
            );
            $extensionTargetX = $extensionOuterX;
        }

        return $boxes;
    }

    /**
     * @param  array<string, mixed>  $rekeyPreview
     * @param  array<int, float>  $trunkAnchors
     * @return array<int, array<string, string>>
     */
    private static function rekeySourceBoundsDebug(array $rekeyPreview, int $index, array $trunkAnchors): array
    {
        $side = (string) ($rekeyPreview['side'] ?? 'left');
        $direction = $side === 'left' ? -1.0 : 1.0;
        $componentCounter = (int) ($rekeyPreview['component_counter'] ?? ($index + 1));
        $componentId = 'strang.rekey-source-' . $side . '.' . $componentCounter;
        $attachPath = self::trunkAttachPathNumber((string) ($rekeyPreview['attach_to'] ?? 'strang.trunk.path.1.end')) ?? 1;
        $attachY = $trunkAnchors[$attachPath + 1] ?? 0.0;
        $bridgeHeight = 1.5;
        $bridgeLength = self::toRem($rekeyPreview['bridge_length'] ?? '4rem');
        $arcOutSize = self::toRem(data_get($rekeyPreview, 'arc_sizes.2', data_get($rekeyPreview, 'arc_sizes.out', '2.75rem'))) ?: 2.75;
        $arcInSize = self::toRem(data_get($rekeyPreview, 'arc_sizes.1', data_get($rekeyPreview, 'arc_sizes.in', '2.75rem'))) ?: 2.75;
        $startLength = self::toRem($rekeyPreview['start_length'] ?? $arcInSize . 'rem');
        $stemLength = self::toRem($rekeyPreview['stem_length'] ?? '4rem');
        $stemContinuation = (array) ($rekeyPreview['stem_continuation'] ?? []);
        $stemContinuationLength = self::stemContinuationLength($stemContinuation, $stemLength);
        $innerX = $direction * $arcOutSize;
        $outerX = $direction * ($arcOutSize + $bridgeLength);
        $bridgeY = $attachY - $arcOutSize;
        $stemEndY = $bridgeY - $arcInSize;
        $startY = $stemEndY - $stemContinuationLength - $stemLength - $startLength;
        $stemX = $outerX + ($direction * $arcInSize);
        $startStemId = $componentId . '.main.path.rekey-source.start-stem';

        return [
            self::mergeStartStemBoxToRem(
                $startStemId . '.bounds',
                'rekey-source-start-stem',
                $stemX,
                $startY,
                $stemEndY,
                $side,
                (array) ($rekeyPreview['node_labels'] ?? []),
                (array) ($rekeyPreview['start_label'] ?? []),
                $startLength,
                $stemLength,
                $stemContinuation,
            ),
            ...self::mergeStartStemSubBoxesToRem(
                $startStemId,
                'rekey-source-start-stem',
                $stemX,
                $startY,
                $stemEndY,
                $side,
                (array) ($rekeyPreview['node_labels'] ?? []),
                (array) ($rekeyPreview['start_label'] ?? []),
                $startLength,
                $stemLength,
                $stemContinuation,
            ),
            self::lineBoxToRem(
                $componentId . '.main.path.rekey-source.bridge1.bounds',
                'rekey-source-bridge',
                min($innerX, $outerX),
                max($innerX, $outerX),
                $bridgeY,
                $bridgeHeight,
                $side,
            ),
        ];
    }

    /**
     * @param  array<string, mixed>  $rekeyPreview
     * @param  array<int, float>  $trunkAnchors
     * @return array<int, array<string, string>>
     */
    private static function rekeyTargetBoundsDebug(array $rekeyPreview, int $index, array $trunkAnchors): array
    {
        $side = (string) ($rekeyPreview['side'] ?? 'right');
        $direction = $side === 'left' ? -1.0 : 1.0;
        $componentCounter = (int) ($rekeyPreview['component_counter'] ?? ($index + 1));
        $componentId = 'strang.rekey-target-' . $side . '.' . $componentCounter;
        $attachPath = self::trunkAttachPathNumber((string) ($rekeyPreview['attach_to'] ?? 'strang.trunk.path.7.end')) ?? 7;
        $attachY = $trunkAnchors[$attachPath + 1] ?? 0.0;
        $bridgeHeight = 1.5;
        $bridgeLength = self::toRem($rekeyPreview['bridge_length'] ?? '4rem');
        $arcSize = self::toRem(data_get($rekeyPreview, 'arc_size', data_get($rekeyPreview, 'arc_sizes.1', '2.75rem'))) ?: 2.75;
        $stemLength = self::toRem($rekeyPreview['stem_length'] ?? '4rem');
        $stemEntries = self::effectiveStemContinuationEntries((array) ($rekeyPreview['stem_continuation'] ?? []), $stemLength);
        $endLength = self::toRem($rekeyPreview['end_length'] ?? '4rem');
        $endLabel = (array) ($rekeyPreview['end_label'] ?? []);

        $bridgeStartX = $direction * $arcSize;
        $bridgeEndX = $direction * ($arcSize + $bridgeLength);
        $bridgeY = $attachY + $arcSize;
        $stemX = $direction * ($bridgeLength + (2 * $arcSize));
        $stemStartY = $attachY + (2 * $arcSize);
        $stemEndY = $stemStartY;
        $padding = 0.75;
        $bodyBounds = [
            'xStart' => $stemX - $padding,
            'xEnd' => $stemX + $padding,
            'yStart' => $stemStartY,
            'yEnd' => $stemStartY,
        ];

        foreach ($stemEntries as $stemEntry) {
            $stemEndY += self::toRem(data_get($stemEntry, 'length', is_array($stemEntry) ? data_get($stemEntry, 0, $stemLength) : $stemEntry));

            foreach (self::labelsFromMixed(is_array($stemEntry) ? $stemEntry : []) as $label) {
                $bodyBounds = self::expandBoundsForLabelAt($bodyBounds, $label, $stemX, $stemEndY);
            }
        }

        $bodyBounds['yEnd'] = max($bodyBounds['yEnd'], $stemEndY);
        $endAnchorY = $stemEndY + $endLength;
        $endBounds = [
            'xStart' => $stemX - $padding,
            'xEnd' => $stemX + $padding,
            'yStart' => min($stemEndY, $endAnchorY),
            'yEnd' => max($stemEndY, $endAnchorY),
        ];

        if (filled(data_get($endLabel, 'text'))) {
            $endBounds = self::expandBoundsForLabelAt(
                $endBounds,
                $endLabel,
                $stemX,
                $endAnchorY,
                (string) data_get($endLabel, 'side', 'top'),
                self::toRem(data_get($endLabel, 'offset', '0.75rem')),
            );
        }

        $labelBounds = self::unionBounds([$bodyBounds, $endBounds]);

        return [
            self::lineBoxToRem(
                $componentId . '.main.path.rekey-target.bridge1.bounds',
                'rekey-target-bridge',
                min($bridgeStartX, $bridgeEndX),
                max($bridgeStartX, $bridgeEndX),
                $bridgeY,
                $bridgeHeight,
                $side,
            ),
            [
                'type' => 'rekey-target-label',
                'id' => $componentId . '.label-bounds',
                'side' => $side,
                ...self::boundsToRem($labelBounds),
            ],
            [
                'type' => 'rekey-target-body',
                'id' => $componentId . '.main.path.rekey-target.body.bounds',
                'side' => $side,
                ...self::boundsToRem($bodyBounds),
            ],
            [
                'type' => 'rekey-target-end',
                'id' => $componentId . '.end.path.rekey-target-end.bounds',
                'side' => $side,
                ...self::boundsToRem($endBounds),
            ],
        ];
    }

    /**
     * @param  array<int|string, mixed>  $continuation
     */
    private static function stemContinuationLength(array $continuation, float $defaultLength): float
    {
        return collect($continuation)
            ->map(static fn(mixed $entry): float => self::toRem(is_array($entry)
                ? data_get($entry, 'length', data_get($entry, 0, $defaultLength))
                : (filled($entry) ? $entry : $defaultLength)))
            ->sum();
    }

    /**
     * @return array<string, string>
     */
    private static function mergeStartStemBoxToRem(
        string $id,
        string $type,
        float $stemX,
        float $yStart,
        float $yEnd,
        string $side,
        array $nodeLabels,
        array $startLabel,
        float $startLength,
        float $stemLength,
        array $stemContinuation,
    ): array {
        $padding = 0.75;
        $bounds = [
            'xStart' => $stemX - $padding,
            'xEnd' => $stemX + $padding,
            'yStart' => min($yStart, $yEnd),
            'yEnd' => max($yStart, $yEnd),
        ];

        if (filled(data_get($startLabel, 'text'))) {
            $bounds = self::expandBoundsForLabelAt(
                $bounds,
                $startLabel,
                $stemX,
                $yStart,
                (string) data_get($startLabel, 'side', 'bottom'),
                self::toRem(data_get($startLabel, 'offset', '0.75rem')),
            );
        }

        $nodeY = $yStart + $startLength;
        foreach (self::labelsFromMixed($nodeLabels[1] ?? []) as $label) {
            $bounds = self::expandBoundsForLabelAt($bounds, $label, $stemX, $nodeY);
        }

        $nodeY += $stemLength;
        foreach (self::labelsFromMixed($nodeLabels[2] ?? []) as $label) {
            $bounds = self::expandBoundsForLabelAt($bounds, $label, $stemX, $nodeY);
        }

        $nodeNumber = 3;
        foreach ($stemContinuation as $stemEntry) {
            $nodeY += self::toRem(is_array($stemEntry)
                ? data_get($stemEntry, 'length', data_get($stemEntry, 0, $stemLength))
                : (filled($stemEntry) ? $stemEntry : $stemLength));

            foreach (self::labelsFromMixed($nodeLabels[$nodeNumber] ?? []) as $label) {
                $bounds = self::expandBoundsForLabelAt($bounds, $label, $stemX, $nodeY);
            }

            $nodeNumber++;
        }

        return [
            'type' => $type,
            'id' => $id,
            'side' => $side,
            'x' => self::rem($bounds['xStart']),
            'y' => self::rem($bounds['yStart']),
            'width' => self::rem($bounds['xEnd'] - $bounds['xStart']),
            'height' => self::rem($bounds['yEnd'] - $bounds['yStart']),
        ];
    }

    /**
     * Split the verified merge start/stem box into inspectable sub-zones:
     * label footprint up to the last labeled stem anchor, then the remaining
     * bare stem up to the arc. Keep this derived from the same coordinates as
     * mergeStartStemBoxToRem so the sub boxes cannot drift independently.
     *
     * @param  array<int|string, mixed>  $nodeLabels
     * @param  array<string, mixed>  $startLabel
     * @param  array<int|string, mixed>  $stemContinuation
     * @return array<int, array<string, string>>
     */
    private static function mergeStartStemSubBoxesToRem(
        string $idPrefix,
        string $typePrefix,
        float $stemX,
        float $yStart,
        float $yEnd,
        string $side,
        array $nodeLabels,
        array $startLabel,
        float $startLength,
        float $stemLength,
        array $stemContinuation,
    ): array {
        $padding = 0.75;
        $startBounds = [
            'xStart' => $stemX - $padding,
            'xEnd' => $stemX + $padding,
            'yStart' => min($yStart, $yStart + $startLength),
            'yEnd' => max($yStart, $yStart + $startLength),
        ];
        $lastLabeledAnchorY = $yStart;
        $labelBounds = [
            'xStart' => $stemX - $padding,
            'xEnd' => $stemX + $padding,
            'yStart' => min($yStart, $lastLabeledAnchorY),
            'yEnd' => max($yStart, $lastLabeledAnchorY),
        ];

        if (filled(data_get($startLabel, 'text'))) {
            $startBounds = self::expandBoundsForLabelAt(
                $startBounds,
                $startLabel,
                $stemX,
                $yStart,
                (string) data_get($startLabel, 'side', 'bottom'),
                self::toRem(data_get($startLabel, 'offset', '0.75rem')),
            );
            $labelBounds = self::expandBoundsForLabelAt(
                $labelBounds,
                $startLabel,
                $stemX,
                $yStart,
                (string) data_get($startLabel, 'side', 'bottom'),
                self::toRem(data_get($startLabel, 'offset', '0.75rem')),
            );
        }

        $nodeY = $yStart + $startLength;
        foreach (self::labelsFromMixed($nodeLabels[1] ?? []) as $label) {
            $lastLabeledAnchorY = $nodeY;
            $labelBounds = self::expandBoundsForLabelAt($labelBounds, $label, $stemX, $nodeY);
        }

        $nodeY += $stemLength;
        foreach (self::labelsFromMixed($nodeLabels[2] ?? []) as $label) {
            $lastLabeledAnchorY = $nodeY;
            $labelBounds = self::expandBoundsForLabelAt($labelBounds, $label, $stemX, $nodeY);
        }

        $nodeNumber = 3;
        foreach ($stemContinuation as $stemEntry) {
            $nodeY += self::toRem(is_array($stemEntry)
                ? data_get($stemEntry, 'length', data_get($stemEntry, 0, $stemLength))
                : (filled($stemEntry) ? $stemEntry : $stemLength));

            foreach (self::labelsFromMixed($nodeLabels[$nodeNumber] ?? []) as $label) {
                $lastLabeledAnchorY = $nodeY;
                $labelBounds = self::expandBoundsForLabelAt($labelBounds, $label, $stemX, $nodeY);
            }

            $nodeNumber++;
        }

        $tailStartY = min(max($lastLabeledAnchorY, min($yStart, $yEnd)), max($yStart, $yEnd));
        $tailBounds = [
            'xStart' => $stemX - $padding,
            'xEnd' => $stemX + $padding,
            'yStart' => min($tailStartY, $yEnd),
            'yEnd' => max($tailStartY, $yEnd),
        ];

        return [
            [
                'type' => $typePrefix . '-start',
                'id' => $idPrefix . '.start.bounds',
                'side' => $side,
                'x' => self::rem($startBounds['xStart']),
                'y' => self::rem($startBounds['yStart']),
                'width' => self::rem($startBounds['xEnd'] - $startBounds['xStart']),
                'height' => self::rem($startBounds['yEnd'] - $startBounds['yStart']),
            ],
            [
                'type' => $typePrefix . '-labels',
                'id' => $idPrefix . '.labels.bounds',
                'side' => $side,
                'x' => self::rem($labelBounds['xStart']),
                'y' => self::rem($labelBounds['yStart']),
                'width' => self::rem($labelBounds['xEnd'] - $labelBounds['xStart']),
                'height' => self::rem($labelBounds['yEnd'] - $labelBounds['yStart']),
            ],
            [
                'type' => $typePrefix . '-tail',
                'id' => $idPrefix . '.tail.bounds',
                'side' => $side,
                'x' => self::rem($tailBounds['xStart']),
                'y' => self::rem($tailBounds['yStart']),
                'width' => self::rem($tailBounds['xEnd'] - $tailBounds['xStart']),
                'height' => self::rem($tailBounds['yEnd'] - $tailBounds['yStart']),
            ],
        ];
    }

    /**
     * @param  array{xStart: float, xEnd: float, yStart: float, yEnd: float}  $bounds
     * @param  array<string, mixed>  $label
     * @return array{xStart: float, xEnd: float, yStart: float, yEnd: float}
     */
    private static function expandBoundsForLabelAt(
        array $bounds,
        array $label,
        float $anchorX,
        float $anchorY,
        ?string $side = null,
        ?float $offset = null,
    ): array {
        $side ??= (string) data_get($label, 'side', 'right');
        $offset ??= 0.95 / 2 + self::toRem(data_get($label, 'connectorLength', '2rem')) + self::toRem(data_get($label, 'connectorGap', '0.25rem'));
        $width = self::textLabelWidth($label);
        $height = self::textLabelHeight($label);

        [$xStart, $xEnd, $yStart, $yEnd] = match ($side) {
            'left' => [$anchorX - $offset - $width, $anchorX - $offset, $anchorY - ($height / 2), $anchorY + ($height / 2)],
            'top' => [$anchorX - ($width / 2), $anchorX + ($width / 2), $anchorY + $offset, $anchorY + $offset + $height],
            'bottom' => [$anchorX - ($width / 2), $anchorX + ($width / 2), $anchorY - $offset - $height, $anchorY - $offset],
            default => [$anchorX + $offset, $anchorX + $offset + $width, $anchorY - ($height / 2), $anchorY + ($height / 2)],
        };

        return [
            'xStart' => min($bounds['xStart'], $xStart),
            'xEnd' => max($bounds['xEnd'], $xEnd),
            'yStart' => min($bounds['yStart'], $yStart),
            'yEnd' => max($bounds['yEnd'], $yEnd),
        ];
    }

    /**
     * @return array<string, string>
     */
    private static function rectBoxToRem(string $id, string $type, float $xStart, float $xEnd, float $yStart, float $yEnd, ?string $side = null): array
    {
        $padding = 0.75;

        return [
            'type' => $type,
            'id' => $id,
            'side' => $side,
            'x' => self::rem(min($xStart, $xEnd) - $padding),
            'y' => self::rem(min($yStart, $yEnd)),
            'width' => self::rem(abs($xEnd - $xStart) + ($padding * 2)),
            'height' => self::rem(abs($yEnd - $yStart)),
        ];
    }

    /**
     * @return array<string, string>
     */
    private static function lineBoxToRem(string $id, string $type, float $xStart, float $xEnd, float $centerY, float $height, ?string $side = null): array
    {
        return [
            'type' => $type,
            'id' => $id,
            'side' => $side,
            'x' => self::rem($xStart),
            'y' => self::rem($centerY - ($height / 2)),
            'width' => self::rem($xEnd - $xStart),
            'height' => self::rem($height),
        ];
    }

    private static function trunkAttachPathNumber(string $attachTo): ?int
    {
        if (preg_match('/strang\.trunk\.path\.(\d+)\.end/', $attachTo, $matches) !== 1) {
            return null;
        }

        return (int) $matches[1];
    }

    /**
     * @param  array<int, array<string, mixed>>  $branchPreviews
     * @param  array<int, mixed>  $trunkPathLengths
     * @return array<int, array<string, mixed>>
     */
    private static function withFinalBranchAnchorY(array $branchPreviews, array $trunkPathLengths, string $trunkStartLength): array
    {
        $trunkAnchors = self::trunkNodeAnchors($trunkPathLengths, $trunkStartLength);

        return collect($branchPreviews)
            ->map(static function (array $branch) use ($trunkAnchors): array {
                $attachPath = self::trunkAttachPathNumber((string) ($branch['attach_to'] ?? ''));

                if ($attachPath !== null && isset($trunkAnchors[$attachPath + 1])) {
                    $branch['anchor_y_rem'] = $trunkAnchors[$attachPath + 1];
                }

                return $branch;
            })
            ->values()
            ->all();
    }

    /**
     * @param  array<int, mixed>  $pathLengths
     * @return array<int, float>
     */
    private static function trunkNodeAnchors(array $pathLengths, string $startLength): array
    {
        $currentY = self::toRem($startLength);
        $anchors = [1 => $currentY];

        foreach ($pathLengths as $pathNumber => $pathLength) {
            $currentY += self::pathEntryLength($pathLength);
            $anchors[(int) $pathNumber + 1] = $currentY;
        }

        return $anchors;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private static function labelsFromSideMap(array $labels): array
    {
        return collect(['left', 'right', 'top', 'bottom'])
            ->map(static fn(string $side): ?array => filled(data_get($labels, $side))
                ? ['text' => data_get($labels, $side), 'side' => $side]
                : null)
            ->filter()
            ->values()
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private static function labelsFromMixed(mixed $labels): array
    {
        if (! is_array($labels)) {
            return [];
        }

        if (array_key_exists('text', $labels)) {
            return [$labels];
        }

        $commonLabelOptions = collect($labels)
            ->reject(static fn(mixed $value, int|string $key): bool => is_int($key) || in_array($key, ['left', 'right', 'top', 'bottom'], true))
            ->all();

        return collect($labels)
            ->flatMap(static function (mixed $label, int|string $key) use ($commonLabelOptions): array {
                if (blank($label)) {
                    return [];
                }

                if (is_string($key) && in_array($key, ['left', 'right', 'top', 'bottom'], true)) {
                    $labelOptions = is_array($label) && ! array_is_list($label) ? $label : [];
                    $labelText = is_array($label) && array_key_exists('text', $label)
                        ? data_get($label, 'text')
                        : $label;

                    return [[
                        ...$commonLabelOptions,
                        ...$labelOptions,
                        'text' => $labelText,
                        'side' => $key,
                    ]];
                }

                return is_array($label) ? [$label] : [];
            })
            ->filter(static fn(array $label): bool => filled(data_get($label, 'text')))
            ->values()
            ->all();
    }

    /**
     * @param  array{xStart: float, xEnd: float, yStart: float, yEnd: float}  $bounds
     * @param  array<string, mixed>  $label
     * @return array{xStart: float, xEnd: float, yStart: float, yEnd: float}
     */
    private static function expandBoundsForLabel(
        array $bounds,
        array $label,
        float $anchorY,
        ?string $side = null,
        ?float $offset = null,
    ): array {
        $side ??= (string) data_get($label, 'side', 'right');
        $offset ??= 0.95 / 2 + self::toRem(data_get($label, 'connectorLength', '2rem')) + self::toRem(data_get($label, 'connectorGap', '0.25rem'));
        $width = self::textLabelWidth($label);
        $height = self::textLabelHeight($label);

        [$xStart, $xEnd, $yStart, $yEnd] = match ($side) {
            'left' => [-$offset - $width, -$offset, $anchorY - ($height / 2), $anchorY + ($height / 2)],
            'top' => [0.0 - ($width / 2), 0.0 + ($width / 2), $anchorY + $offset, $anchorY + $offset + $height],
            'bottom' => [0.0 - ($width / 2), 0.0 + ($width / 2), $anchorY - $offset - $height, $anchorY - $offset],
            default => [$offset, $offset + $width, $anchorY - ($height / 2), $anchorY + ($height / 2)],
        };

        return [
            'xStart' => min($bounds['xStart'], $xStart),
            'xEnd' => max($bounds['xEnd'], $xEnd),
            'yStart' => min($bounds['yStart'], $yStart),
            'yEnd' => max($bounds['yEnd'], $yEnd),
        ];
    }

    /**
     * @param  array<string, mixed>  $label
     */
    private static function textLabelWidth(array $label): float
    {
        $labelPadding = 1.0;

        if ((bool) data_get($label, 'long', false)) {
            return 24.0 + $labelPadding;
        }

        if ((bool) data_get($label, 'halfLong', false)) {
            return 18.0 + $labelPadding;
        }

        return 12.0 + $labelPadding;
    }

    /**
     * @param  array<string, mixed>  $label
     */
    private static function textLabelHeight(array $label): float
    {
        $text = data_get($label, 'text');
        $lineCount = collect(is_iterable($text) && ! is_string($text) ? $text : [$text])
            ->filter(static fn(mixed $line): bool => filled($line))
            ->take((int) data_get($label, 'maxLines', 3))
            ->count();

        return match ($lineCount) {
            0 => 0.0,
            1 => 2.0,
            2 => 3.0,
            3 => 4.0,
            default => 4.5,
        };
    }

    private static function pathEntryLength(mixed $entry): float
    {
        if (is_array($entry)) {
            return self::toRem(data_get($entry, 'length', data_get($entry, 0, '0rem')));
        }

        return self::toRem($entry);
    }

    /**
     * @param  array<int|string, mixed>  $entries
     * @return array<int|string, mixed>
     */
    private static function effectiveStemContinuationEntries(array $entries, float $defaultLength): array
    {
        return $entries === [] ? [1 => ['length' => self::rem($defaultLength)]] : $entries;
    }

    /**
     * @param  array<int, array{xStart: float, xEnd: float, yStart: float, yEnd: float}>  $bounds
     * @return array{xStart: float, xEnd: float, yStart: float, yEnd: float}
     */
    private static function unionBounds(array $bounds): array
    {
        if ($bounds === []) {
            return [
                'xStart' => 0.0,
                'xEnd' => 0.0,
                'yStart' => 0.0,
                'yEnd' => 0.0,
            ];
        }

        return [
            'xStart' => min(array_column($bounds, 'xStart')),
            'xEnd' => max(array_column($bounds, 'xEnd')),
            'yStart' => min(array_column($bounds, 'yStart')),
            'yEnd' => max(array_column($bounds, 'yEnd')),
        ];
    }

    /**
     * @param  array{xStart: float, xEnd: float, yStart: float, yEnd: float}  $bounds
     * @return array{x: string, y: string, width: string, height: string}
     */
    private static function boundsToRem(array $bounds): array
    {
        return [
            'x' => self::rem($bounds['xStart']),
            'y' => self::rem($bounds['yStart']),
            'width' => self::rem($bounds['xEnd'] - $bounds['xStart']),
            'height' => self::rem($bounds['yEnd'] - $bounds['yStart']),
        ];
    }

    private static function toRem(mixed $value): float
    {
        if (is_numeric($value)) {
            return (float) $value;
        }

        if (preg_match('/-?\d+(?:\.\d+)?/', (string) $value, $matches) !== 1) {
            return 0.0;
        }

        return (float) $matches[0];
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
