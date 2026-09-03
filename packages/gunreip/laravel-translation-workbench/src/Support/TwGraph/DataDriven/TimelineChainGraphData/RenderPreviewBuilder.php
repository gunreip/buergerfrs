<?php

declare(strict_types=1);

namespace Gunreip\TranslationWorkbench\Support\TwGraph\DataDriven\TimelineChainGraphData;

use Gunreip\TranslationWorkbench\Support\TwGraph\Defaults;
use Gunreip\TranslationWorkbench\Support\TwGraph\ElementIdentifier;
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
        $rootLineLength = self::defaultTrunkPathLength();
        $trunkStartLength = $rootLineLength;
        $buildTrunkTimeline = static function (
            Collection $items,
            int $firstPathNumber,
            string $pathLength,
            string $startLength,
            string $firstPathLength,
        ): array {
            $nodeLabels = [];
            $eventPathEntries = [];
            $pathNumber = $firstPathNumber;
            $previousTimelineItemType = null;
            $trunkTimelineAnchors = [];
            $trunkAnchorYRem = self::toRem($startLength)
                + ($firstPathNumber > 1 ? self::toRem($firstPathLength) : 0.0);

            $items
                ->sortBy([
                    ['timestamp_sort', 'asc'],
                    ['sort_index', 'asc'],
                ])
                ->values()
                ->each(static function (array $item) use (&$nodeLabels, &$eventPathEntries, &$pathNumber, &$previousTimelineItemType, &$trunkTimelineAnchors, &$trunkAnchorYRem, $pathLength): void {
                    $labels = (array) data_get($item, 'labels', []);
                    $trunkAnchorYRem += self::toRem($pathLength);

                    if ($labels !== []) {
                        $nodeLabels[$pathNumber] = $labels;

                        $itemType = (string) data_get($item, 'type', 'normal');
                        $isTimelineTypeTransition = $previousTimelineItemType !== null
                            && $previousTimelineItemType !== $itemType;
                        $pathEntry = [
                            'component' => $isTimelineTypeTransition ? 'stem-compressed' : 'path',
                            'labels' => $labels,
                        ];

                        $eventPathEntries[$pathNumber] = $pathEntry;
                        $previousTimelineItemType = $itemType;
                        $trunkTimelineAnchors[] = [
                            'path' => $pathNumber,
                            'anchor' => 'strang.trunk.path.' . $pathNumber . '.end',
                            'timestamp' => (string) data_get($item, 'timestamp_sort', ''),
                            'type' => $itemType,
                            'event' => (string) data_get($labels, '0.text.0', ''),
                            'y_rem' => $trunkAnchorYRem,
                        ];
                    }

                    $pathNumber++;
                });

            return [
                'node_labels' => $nodeLabels,
                'event_path_entries' => $eventPathEntries,
                'trunk_timeline_anchors' => $trunkTimelineAnchors,
            ];
        };
        $trunkTimelineState = $buildTrunkTimeline(
            $timelineLabelItems,
            2,
            $rootLineLength,
            $trunkStartLength,
            $rootLineLength,
        );
        $nodeLabels = $trunkTimelineState['node_labels'];
        $eventPathEntries = $trunkTimelineState['event_path_entries'];
        $trunkTimelineAnchors = $trunkTimelineState['trunk_timeline_anchors'];
        $langValueLabels = LangValueLabels::active($mainRow);
        $graphId = 'timeline-chain-' . (int) ($mainRow['id'] ?? 0) . '-data-preview';
        $layoutCorrections = LayoutCorrectionConfig::forDataDriven();
        $mergePreviewHeadCandidates = max(0, (int) Defaults::dataDriven(
            'merge_layout.preview_head_candidates',
            Defaults::graph('merge_layout.preview_head_candidates', 6),
        ));
        $mergePreviews = MergePreviewBuilder::previews($originRows, $mergePreviewHeadCandidates);
        $branchPreviews = BranchPreviewBuilder::previews($mergeOutcomes, $trunkTimelineAnchors);
        $rekeyPreviews = RekeyPreviewBuilder::previews($mainRow);
        $mergePreviews = LayoutCorrectionConfig::applyToPreviews($mergePreviews, $layoutCorrections);
        $branchPreviews = LayoutCorrectionConfig::applyToPreviews($branchPreviews, $layoutCorrections);
        $rekeyPreviews = LayoutCorrectionConfig::applyToPreviews($rekeyPreviews, $layoutCorrections);
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
        $mergePreviewCount = count($mergePreviews);
        $branchPreviewCount = collect($branchPreviews)->sum(static fn(array $preview): int => (int) ($preview['finding_count'] ?? 0));
        $rekeyPreviewCount = count($rekeyPreviews);
        $hasSideStrangPreviews = $mergePreviewCount > 0 || $branchPreviewCount > 0 || $rekeyPreviewCount > 0;
        if (! $hasSideStrangPreviews) {
            $trunkOnlyStartPromotion = self::promoteTrunkOnlyStartEvent($timelineLabelItems, $langValueLabels);
            $timelineLabelItems = $trunkOnlyStartPromotion['items'];
            $langValueLabels = $trunkOnlyStartPromotion['start_node_labels'];
            $trunkTimelineState = $buildTrunkTimeline(
                $timelineLabelItems,
                1,
                $rootLineLength,
                $trunkStartLength,
                $rootLineLength,
            );
            $nodeLabels = $trunkTimelineState['node_labels'];
            $eventPathEntries = $trunkTimelineState['event_path_entries'];
            $trunkTimelineAnchors = $trunkTimelineState['trunk_timeline_anchors'];
        }

        $lastLabelNodeIndex = $nodeLabels !== [] ? max(array_keys($nodeLabels)) : 1;
        $rekeyTargetPathNumber = $hasRekeyTargetPreview ? max(1, $lastLabelNodeIndex + 1) : null;
        $rekeyPreviews = self::withRekeyTargetAttachPath($rekeyPreviews, $rekeyTargetPathNumber);
        $rekeyTargetTrunkLabel = $rekeyTargetPathNumber !== null
            ? [
                'text' => array_values(array_filter([
                    'rekeyed to this key ID #' . (string) ($rekeyTargetKeyId ?: '?'),
                    $rekeyTargetRelationLine,
                ])),
                'side' => 'right',
                'connectorLength' => self::defaultConnectorLength('rekey_target_trunk_label_connector_length'),
                'badgeColor' => self::color('rekey', 'sky'),
                'long' => true,
            ]
            : null;
        $pathCount = self::plannedTrunkPathCount(
            $eventPathEntries,
            self::trunkAttachedPathNumbers($mergePreviews, $rekeyPreviews, $branchPreviews),
            $rekeyTargetPathNumber,
            $layoutCorrections,
        );
        $previewMode = $hasSideStrangPreviews ? 'trunk_with_limited_merge' : 'trunk_only';
        $trunkStartShiftEnabledForPreview = $hasSideStrangPreviews && self::trunkStartShiftEnabled();
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
            ->mapWithKeys(static function (int $pathNumber) use ($eventPathEntries, $rekeyTargetTrunkLabel, $rekeyTargetPathNumber): array {
                $eventTypePathEntry = $eventPathEntries[$pathNumber] ?? null;
                if (is_array($eventTypePathEntry)) {
                    return [$pathNumber => $eventTypePathEntry];
                }

                if ($pathNumber === $rekeyTargetPathNumber && $rekeyTargetTrunkLabel !== null) {
                    return [
                        $pathNumber => [
                            'labels' => [$rekeyTargetTrunkLabel, null],
                        ],
                    ];
                }

                return [$pathNumber => null];
            })
            ->all();
        $trunkLayoutCorrections = LayoutCorrectionConfig::applyToTrunkPathLengths(
            $trunkPathLengths,
            $layoutCorrections,
            $rootLineLength,
        );
        $trunkPathLengths = $trunkLayoutCorrections['path_lengths'];
        $trunkAppliedCorrections = $trunkLayoutCorrections['applied'];
        $trunkAppliedCompensations = [];
        $trunkStartLabel = [
            'text' => array_values(array_filter([
                'key ID #' . (string) ($mainRow['root_key_id'] ?? '?'),
                trim($trunkStartTimestamp . ' · ' . $mergeOriginCountLabel, ' ·'),
            ])),
        ];
        $trunkEndLabel = [
            'text' => $trunkEndLabelLines,
            'long' => true,
        ];
        $trunkStemClassification = self::classifyAndEliminateEmptyTrunkStems(
            $trunkPathLengths,
            $nodeLabels,
            self::trunkAttachedPathNumbers($mergePreviews, $rekeyPreviews, $branchPreviews),
            $trunkAppliedCorrections,
            self::hasTrunkStartSideLabels($langValueLabels),
        );
        $trunkPathLengths = $trunkStemClassification['path_lengths'];
        $branchPreviews = BranchLabelCollisionResolver::refreshDebugBounds(
            self::withFinalBranchAnchorY($branchPreviews, $trunkPathLengths, $trunkStartLength),
        );
        $mergePreviews = self::withMergeCollisionDebug(self::withMergeBridgeBoundsDebug($mergePreviews, $trunkPathLengths, $trunkStartLength));
        $mergeCompensation = self::applyMergeCollisionCompensation($mergePreviews);
        if ($mergeCompensation['applied'] !== []) {
            $mergePreviews = self::withMergeCollisionDebug(self::withMergeBridgeBoundsDebug($mergeCompensation['merges'], $trunkPathLengths, $trunkStartLength));
        }
        $rekeyPreviews = self::withRekeyBoundsDebug($rekeyPreviews, $trunkPathLengths, $trunkStartLength);
        $trunkBoundsDebug = self::trunkBoundsDebugPayload(
            $trunkPathLengths,
            $trunkStartLength,
            $rootLineLength,
            $langValueLabels,
            $nodeLabels,
            $trunkStartLabel,
            $trunkEndLabel,
            self::trunkAttachedPathNumbers($mergePreviews, $rekeyPreviews, $branchPreviews),
        );
        $trunkCollisionDebug = self::trunkPotentialCollisions($trunkBoundsDebug, $branchPreviews, $mergePreviews, $rekeyPreviews);
        $trunkStartCompensation = self::applyTrunkStartStemCollisionCompensation($trunkPathLengths, $trunkCollisionDebug, $rootLineLength);
        if ($trunkStartCompensation['applied'] !== []) {
            $trunkPathLengths = $trunkStartCompensation['path_lengths'];
            $trunkAppliedCompensations = [
                ...$trunkAppliedCompensations,
                ...$trunkStartCompensation['applied'],
            ];
            $branchPreviews = BranchLabelCollisionResolver::refreshDebugBounds(
                self::withFinalBranchAnchorY($branchPreviews, $trunkPathLengths, $trunkStartLength),
            );
            $mergePreviews = self::withMergeCollisionDebug(self::withMergeBridgeBoundsDebug($mergePreviews, $trunkPathLengths, $trunkStartLength));
            $mergeCompensation = self::applyMergeCollisionCompensation($mergePreviews);
            if ($mergeCompensation['applied'] !== []) {
                $mergePreviews = self::withMergeCollisionDebug(self::withMergeBridgeBoundsDebug($mergeCompensation['merges'], $trunkPathLengths, $trunkStartLength));
            }
            $rekeyPreviews = self::withRekeyBoundsDebug($rekeyPreviews, $trunkPathLengths, $trunkStartLength);
            $trunkBoundsDebug = self::trunkBoundsDebugPayload(
                $trunkPathLengths,
                $trunkStartLength,
                $rootLineLength,
                $langValueLabels,
                $nodeLabels,
                $trunkStartLabel,
                $trunkEndLabel,
                self::trunkAttachedPathNumbers($mergePreviews, $rekeyPreviews, $branchPreviews),
            );
            $trunkCollisionDebug = self::trunkPotentialCollisions($trunkBoundsDebug, $branchPreviews, $mergePreviews, $rekeyPreviews);
        }
        $branchCompensation = self::applyBranchCompensationFromTrunkCollisions($branchPreviews, $trunkCollisionDebug);
        if ($branchCompensation['applied'] !== []) {
            $branchPreviews = BranchLabelCollisionResolver::refreshDebugBounds($branchCompensation['branches']);
            $mergePreviews = self::withMergeCollisionDebug($mergePreviews);
            $trunkCollisionDebug = self::trunkPotentialCollisions($trunkBoundsDebug, $branchPreviews, $mergePreviews, $rekeyPreviews);
        }
        $rekeySourceCompensation = self::applyRekeySourceEndLabelSideSwitchFromTrunkCollisions($rekeyPreviews, $trunkBoundsDebug, $trunkCollisionDebug);
        if ($rekeySourceCompensation['applied'] !== []) {
            $rekeyPreviews = self::withRekeyBoundsDebug($rekeySourceCompensation['rekeys'], $trunkPathLengths, $trunkStartLength);
            $trunkCollisionDebug = self::trunkPotentialCollisions($trunkBoundsDebug, $branchPreviews, $mergePreviews, $rekeyPreviews);
        }
        $rekeyTargetCompensation = self::applyRekeyTargetCompensationFromTrunkCollisions($rekeyPreviews, $trunkCollisionDebug);
        if ($rekeyTargetCompensation['applied'] !== []) {
            $rekeyPreviews = self::withRekeyBoundsDebug($rekeyTargetCompensation['rekeys'], $trunkPathLengths, $trunkStartLength);
            $trunkCollisionDebug = self::trunkPotentialCollisions($trunkBoundsDebug, $branchPreviews, $mergePreviews, $rekeyPreviews);
        }
        $handledTrunkSpacingCollisionKeys = [];
        for ($finalPass = 1; $finalPass <= 4; $finalPass++) {
            $trunkSpacingCandidates = self::trunkSpacingCollisionCandidates($branchPreviews, $rekeyPreviews, $trunkPathLengths, $trunkStartLength);
            $trunkSpacingAdjustments = BranchLabelCollisionResolver::trunkPathSpacingAdjustments(
                $trunkSpacingCandidates,
                self::trunkNodeAnchors($trunkPathLengths, $trunkStartLength),
                $handledTrunkSpacingCollisionKeys,
            );

            if ($trunkSpacingAdjustments === []) {
                break;
            }

            $trunkSpacingAdjustments = self::nextTrunkPathSpacingAdjustment($trunkSpacingAdjustments);
            $trunkSpacingCompensation = self::applyTrunkPathSpacingCompensation(
                $trunkPathLengths,
                $trunkSpacingAdjustments,
                $trunkSpacingCandidates,
                $trunkStartLength,
                $finalPass,
            );

            if ($trunkSpacingCompensation['applied'] === []) {
                break;
            }

            $trunkPathLengths = $trunkSpacingCompensation['path_lengths'];
            $trunkAppliedCompensations = [
                ...$trunkAppliedCompensations,
                ...$trunkSpacingCompensation['applied'],
            ];
            $handledTrunkSpacingCollisionKeys = [
                ...$handledTrunkSpacingCollisionKeys,
                ...$trunkSpacingCompensation['handled_collision_keys'],
            ];
            $branchPreviews = BranchLabelCollisionResolver::refreshDebugBounds(
                self::withFinalBranchAnchorY($branchPreviews, $trunkPathLengths, $trunkStartLength),
                $handledTrunkSpacingCollisionKeys,
            );
            $rekeyPreviews = self::withRekeyBoundsDebug($rekeyPreviews, $trunkPathLengths, $trunkStartLength);
        }
        $mergePreviews = self::withMergeCollisionDebug(self::withMergeBridgeBoundsDebug($mergePreviews, $trunkPathLengths, $trunkStartLength));
        $rekeyPreviews = self::withRekeyBoundsDebug($rekeyPreviews, $trunkPathLengths, $trunkStartLength);
        $trunkBoundsDebug = self::trunkBoundsDebugPayload(
            $trunkPathLengths,
            $trunkStartLength,
            $rootLineLength,
            $langValueLabels,
            $nodeLabels,
            $trunkStartLabel,
            $trunkEndLabel,
            self::trunkAttachedPathNumbers($mergePreviews, $rekeyPreviews, $branchPreviews),
        );
        $trunkCollisionDebug = self::trunkPotentialCollisions($trunkBoundsDebug, $branchPreviews, $mergePreviews, $rekeyPreviews);
        $trunkPreview = [
            'component' => 'tw-graph.strang.trunk',
            'color' => self::color('trunk', 'green'),
            'path_count' => $pathCount,
            'start_length' => $trunkStartLength,
            'start_shift_enabled' => $trunkStartShiftEnabledForPreview,
            'start_shift_length' => self::trunkStartShiftLength(),
            'path_lengths' => $trunkPathLengths,
            'start_label' => $trunkStartLabel,
            'end_label' => $trunkEndLabel,
            'start_node_labels' => $langValueLabels,
            'node_labels' => $nodeLabels,
            'layout' => [
                'trunkBoundsDebug' => $trunkBoundsDebug,
                'trunkCollisionDebug' => $trunkCollisionDebug,
                'appliedCompensations' => $trunkAppliedCompensations,
                'stemClassification' => $trunkStemClassification['classification'],
            ],
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
                'graph_id' => $graphId,
                'header' => [
                    'text' => [
                        'Timeline chain ID #' . (string) ($mainRow['id'] ?? '?'),
                        self::headlineLabel($mainRow['chain_type'] ?? '')
                            . ' · '
                            . self::headlineLabel($mainRow['chain_status'] ?? '')
                            . ' · '
                            . (string) ($mainRow['translation_key'] ?? ''),
                    ],
                    'badgeColor' => self::color('graph', 'cyan'),
                ],
                'color' => self::color('graph', 'cyan'),
                'line_length' => Defaults::dataDrivenString('line_length', '4rem'),
                'line_width' => Defaults::dataDrivenString('line_width', '0.25rem'),
                'node_size' => Defaults::dataDrivenString('node_size', '0.95rem'),
                'arc_size' => Defaults::dataDrivenString('arc_size', '2.75rem'),
                'cap_length' => Defaults::dataDrivenString('cap_length', '1.75rem'),
                'bridge_length' => Defaults::dataDrivenString('bridge_length', Defaults::dataDrivenString('line_length', '4rem')),
                'stem_length' => Defaults::dataDrivenString('stem_length', Defaults::dataDrivenString('line_length', '4rem')),
                'connector_length' => Defaults::dataDrivenString('connector_length', '2rem'),
                'connector_gap' => Defaults::dataDrivenString('connector_gap', '0.25rem'),
                'horizontal_padding' => $horizontalPadding,
                'horizontal_padding_debug' => [
                    'trunk_label_level' => $trunkLabelPaddingLevel,
                    'has_left_strangs' => self::hasLeftStrangs($mergePreviews, $rekeyPreviews, $branchPreviews),
                    'horizontal_padding' => $horizontalPadding,
                ],
                'layout_corrections' => [
                    'configured' => count($layoutCorrections),
                    'applied' => [
                        ...$trunkAppliedCorrections,
                        ...LayoutCorrectionConfig::appliedCorrections($mergePreviews, $rekeyPreviews, $branchPreviews),
                    ],
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
     * In trunk-only graphs the first key-created state belongs to the trunk
     * start node. Side-strang graphs keep the event in the normal timeline so
     * their existing start spacing and attached-strang anchors stay stable.
     *
     * @param  Collection<int, array<string, mixed>>  $items
     * @param  array<string, mixed>  $startNodeLabels
     * @return array{items: Collection<int, array<string, mixed>>, start_node_labels: array<string, mixed>}
     */
    private static function promoteTrunkOnlyStartEvent(Collection $items, array $startNodeLabels): array
    {
        $targetSide = blank($startNodeLabels['left'] ?? null)
            ? 'left'
            : (blank($startNodeLabels['right'] ?? null) ? 'right' : null);

        if ($targetSide === null) {
            return [
                'items' => $items,
                'start_node_labels' => $startNodeLabels,
            ];
        }

        $promoted = false;
        $items = $items
            ->map(static function (array $item) use (&$promoted, &$startNodeLabels, $targetSide): ?array {
                if ($promoted || (string) data_get($item, 'type', 'normal') !== 'normal') {
                    return $item;
                }

                $labels = array_values((array) data_get($item, 'labels', []));

                foreach ($labels as $labelIndex => $label) {
                    if (! is_array($label) || trim((string) data_get($label, 'text.0', '')) !== 'Key created') {
                        continue;
                    }

                    $label['side'] = $targetSide;
                    $startNodeLabels[$targetSide] = $label;
                    unset($labels[$labelIndex]);
                    $labels = array_values($labels);
                    $promoted = true;

                    if ($labels === []) {
                        return null;
                    }

                    $item['labels'] = $labels;

                    return $item;
                }

                return $item;
            })
            ->filter()
            ->values();

        return [
            'items' => $items,
            'start_node_labels' => $startNodeLabels,
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
     * @param  array<int, mixed>  $pathLengths
     * @param  array<string, mixed>  $startNodeLabels
     * @param  array<int|string, mixed>  $nodeLabels
     * @param  array<string, mixed>  $startLabel
     * @param  array<string, mixed>  $endLabel
     * @param  array<int, int>  $attachedPathNumbers
     * @return array<int, array<string, string>>
     */
    private static function trunkBoundsDebugPayload(
        array $pathLengths,
        string $startLength,
        string $endLength,
        array $startNodeLabels,
        array $nodeLabels,
        array $startLabel,
        array $endLabel,
        array $attachedPathNumbers,
    ): array {
        return [
            ...self::trunkBoundsDebug($pathLengths, $startLength, $endLength),
            ...self::trunkStartBoundsDebug($startLength, $startNodeLabels, $startLabel),
            ...self::trunkEndBoundsDebug($pathLengths, $startLength, $endLength, $endLabel),
            ...self::trunkMiddleBoundsDebug($pathLengths, $startLength, $endLength, $nodeLabels, $attachedPathNumbers),
            ...self::trunkSideLabelBoundsDebug($pathLengths, $startLength, $endLength, $startNodeLabels, $nodeLabels, $attachedPathNumbers),
            ...self::trunkConcreteLabelBoundsDebug($pathLengths, $startLength, $startNodeLabels, $nodeLabels),
            ...self::trunkConcreteTerminalLabelBoundsDebug($pathLengths, $startLength, $endLength, $startLabel, $endLabel),
            ...self::trunkLabelBoundsDebug(
                $pathLengths,
                $startLength,
                $endLength,
                $startNodeLabels,
                $nodeLabels,
                [
                    'start' => ['label' => $startLabel],
                    'end' => ['label' => $endLabel],
                ],
            ),
        ];
    }

    /**
     * Apply only one final trunk-spacing correction per pass. A higher trunk
     * stem can move a later branch enough to resolve lower snapshot collisions,
     * so every applied change must be followed by a fresh bounds pass.
     *
     * @param  array<int, array{delta: float, collisionKey: string}>  $adjustments
     * @return array<int, array{delta: float, collisionKey: string}>
     */
    private static function nextTrunkPathSpacingAdjustment(array $adjustments): array
    {
        if ($adjustments === []) {
            return [];
        }

        krsort($adjustments, SORT_NUMERIC);
        $pathNumber = (int) array_key_first($adjustments);

        return [
            $pathNumber => (array) $adjustments[$pathNumber],
        ];
    }

    /**
     * Keep trunk path numbering stable while removing optically empty stems.
     * A stem may disappear only when it has no label, no special component, no
     * attached side strang and no explicit correction target.
     *
     * @param  array<int|string, mixed>  $pathLengths
     * @param  array<int|string, mixed>  $nodeLabels
     * @param  array<int, int>  $attachedPathNumbers
     * @param  array<int, array<string, mixed>>  $appliedCorrections
     * @return array{path_lengths: array<int|string, mixed>, classification: array<int, array<string, mixed>>}
     */
    private static function classifyAndEliminateEmptyTrunkStems(
        array $pathLengths,
        array $nodeLabels,
        array $attachedPathNumbers,
        array $appliedCorrections,
        bool $hasTrunkStartSideLabels,
    ): array {
        $attachedPathNumbers = collect($attachedPathNumbers)
            ->map(static fn(mixed $pathNumber): int => (int) $pathNumber)
            ->filter(static fn(int $pathNumber): bool => $pathNumber > 0)
            ->unique()
            ->all();
        $correctedPathNumbers = collect($appliedCorrections)
            ->map(static function (array $correction): ?int {
                $target = ElementIdentifier::normalize((string) data_get($correction, 'target', ''));

                if (preg_match('/^strang\.trunk\.1\.stem(\d+)$/', $target, $matches) !== 1) {
                    return null;
                }

                return (int) $matches[1];
            })
            ->filter()
            ->unique()
            ->all();
        $classification = [];

        foreach ($pathLengths as $pathNumber => $entry) {
            $pathNumber = (int) $pathNumber;
            $reasons = [];
            $component = is_array($entry) ? (string) data_get($entry, 'component', 'path') : 'path';
            $labels = is_array($entry) ? data_get($entry, 'labels') : null;
            $hasEntryLabels = is_array($labels)
                ? collect($labels)->filter(static fn(mixed $label): bool => filled($label))->isNotEmpty()
                : ($labels !== null && $labels !== false && filled($labels));
            $hasNodeLabels = collect(self::labelsFromMixed($nodeLabels[$pathNumber] ?? []))->isNotEmpty();

            if ($hasEntryLabels || $hasNodeLabels) {
                $reasons[] = 'label';
            }

            if ($component !== 'path') {
                $reasons[] = $component;
            }

            if (in_array($pathNumber, $attachedPathNumbers, true)) {
                $reasons[] = 'anchor';
            }

            if (in_array($pathNumber, $correctedPathNumbers, true)) {
                $reasons[] = 'correction';
            }

            if ($pathNumber === 2
                && ! $hasTrunkStartSideLabels
                && $component === 'path'
                && ! in_array('anchor', $reasons, true)
                && ! in_array('correction', $reasons, true)
            ) {
                $pathLengths[$pathNumber] = self::pathEntryWithLength(
                    $entry,
                    self::trunkStartUnlabeledNextStemLength(),
                );
                $reasons = collect(['after-unlabeled-start-shortened', ...$reasons])
                    ->unique()
                    ->values()
                    ->all();
            }

            if ($reasons === []) {
                $pathLengths[$pathNumber] = self::pathEntryWithoutVisibleStem($entry);
                $reasons[] = 'empty-eliminated';
            }

            $classification[$pathNumber] = [
                'path' => $pathNumber,
                'role' => $reasons[0],
                'roles' => $reasons,
                'length' => self::rem(self::pathEntryLength($pathLengths[$pathNumber] ?? null)),
            ];
        }

        return [
            'path_lengths' => $pathLengths,
            'classification' => $classification,
        ];
    }

    /**
     * The trunk-start dot is only hidden when no side labels are attached to
     * that exact anchor. Top/bottom start labels are segment labels and do not
     * count for this layout rhythm rule.
     *
     * @param  array<string, mixed>  $startNodeLabels
     */
    private static function hasTrunkStartSideLabels(array $startNodeLabels): bool
    {
        return filled($startNodeLabels['left'] ?? null) || filled($startNodeLabels['right'] ?? null);
    }

    /**
     * Resolve real start-label collisions by extending the first trunk stem.
     * This only reacts to concrete trunk start node labels, not to broad trunk
     * main bounds, so visual near-misses stay diagnostic-only.
     *
     * @param  array<int|string, mixed>  $pathLengths
     * @param  array<int, array<string, mixed>>  $collisions
     * @return array{path_lengths: array<int|string, mixed>, applied: array<int, array<string, mixed>>}
     */
    private static function applyTrunkStartStemCollisionCompensation(
        array $pathLengths,
        array $collisions,
        string $defaultLength,
    ): array {
        if (! self::trunkStartShiftEnabled()) {
            return [
                'path_lengths' => $pathLengths,
                'applied' => [],
            ];
        }

        $startLabelCollisions = collect($collisions)
            ->filter(static function (array $collision): bool {
                $trunkId = ElementIdentifier::normalize((string) data_get($collision, 'trunk', ''));

                return str_contains($trunkId, 'strang.trunk.1.start.nodeEndLabel')
                    && in_array((string) data_get($collision, 'trunkType'), ['trunk-left-label', 'trunk-right-label'], true);
            })
            ->values();

        if ($startLabelCollisions->isEmpty()) {
            return [
                'path_lengths' => $pathLengths,
                'applied' => [],
            ];
        }

        $gap = self::debugBoundBoxGap();
        $measuredDelta = $startLabelCollisions
            ->map(static function (array $collision) use ($gap): float {
                $trunkYEnd = self::toRem(data_get($collision, 'trunkYEnd', '0rem'));
                $againstYStart = self::toRem(data_get($collision, 'againstYStart', '0rem'));

                return max(0.0, $trunkYEnd - $againstYStart + $gap);
            })
            ->max();

        if (! is_numeric($measuredDelta) || (float) $measuredDelta <= 0.0) {
            return [
                'path_lengths' => $pathLengths,
                'applied' => [],
            ];
        }

        $delta = max((float) $measuredDelta, self::toRem(self::trunkStartShiftLength()));
        $currentEntry = $pathLengths[1] ?? null;
        $currentLength = self::pathEntryLength($currentEntry);
        $baseLength = $currentLength > 0.0 ? $currentLength : self::toRem($defaultLength);
        $effectiveLength = self::rem($baseLength + $delta);
        $pathLengths[1] = self::pathEntryWithLength($currentEntry, $effectiveLength);

        return [
            'path_lengths' => $pathLengths,
            'applied' => [
                [
                    'target' => ElementIdentifier::normalize('strang.trunk.1.main.path.trunk.path1'),
                    'prop' => 'length',
                    'delta' => self::rem($delta),
                    'baseValue' => self::rem($baseLength),
                    'effectiveValue' => $effectiveLength,
                    'reason' => 'Automatic real trunk-start-label collision compensation.',
                    'configuredMinimum' => self::trunkStartShiftLength(),
                    'measuredDelta' => self::rem((float) $measuredDelta),
                    'sources' => $startLabelCollisions
                        ->map(static fn(array $collision): array => [
                            'source' => 'trunk-start-label-collision',
                            'trunk' => ElementIdentifier::normalize((string) data_get($collision, 'trunk', '')),
                            'against' => ElementIdentifier::normalize((string) data_get($collision, 'against', '')),
                            'overlapHeight' => (string) data_get($collision, 'overlapHeight', '0rem'),
                            'gap' => self::rem($gap),
                            'requiredIncrement' => self::rem(max(
                                0.0,
                                self::toRem(data_get($collision, 'trunkYEnd', '0rem'))
                                    - self::toRem(data_get($collision, 'againstYStart', '0rem'))
                                    + $gap,
                            )),
                        ])
                        ->all(),
                ],
            ],
        ];
    }

    /**
     * Resolve trunk-label vs branch-footprint collisions by moving the affected
     * branch outward. This is intentionally graph-family compensation, not a
     * per-dataset correction; final hand-tuned deltas still belong in layout
     * correction config.
     *
     * @param  array<int, array<string, mixed>>  $branches
     * @param  array<int, array<string, mixed>>  $collisions
     * @return array{branches: array<int, array<string, mixed>>, applied: array<int, array<string, mixed>>}
     */
    private static function applyBranchCompensationFromTrunkCollisions(array $branches, array $collisions): array
    {
        $increments = [];

        foreach ($collisions as $collision) {
            $against = ElementIdentifier::normalize(data_get($collision, 'against', ''));

            if (preg_match('/^strang\.(left|right)\.(\d+)\.branch\./', $against, $matches) !== 1) {
                continue;
            }

            $branchIndex = self::branchPreviewIndexByCanonicalCounter($branches, $matches[1], (int) $matches[2]);

            if ($branchIndex === null) {
                continue;
            }

            $overlapWidth = self::toRem(data_get($collision, 'overlapWidth', '0rem'));
            $gap = Defaults::dataDrivenRem('debug_bound_box_gap', Defaults::graphString('debug_bound_box_gap', '2rem'));
            $delta = $overlapWidth + $gap;

            if ($delta <= 0.0) {
                continue;
            }

            $increments[$branchIndex] = max((float) ($increments[$branchIndex] ?? 0.0), $delta);
        }

        $applied = [];

        foreach ($increments as $branchIndex => $delta) {
            if (! isset($branches[$branchIndex])) {
                continue;
            }

            $target = ElementIdentifier::normalize(self::branchPreviewId($branches[$branchIndex]) . '.main.path.branch.bridge1');
            $baseValue = (string) ($branches[$branchIndex]['bridge_length'] ?? Defaults::dataDrivenString('bridge_length', Defaults::dataDrivenString('line_length', '4rem')));
            $effectiveValue = self::rem(self::toRem($baseValue) + (float) $delta);

            $branches[$branchIndex]['bridge_length'] = $effectiveValue;
            $appliedEntry = [
                'target' => $target,
                'prop' => 'bridge_length',
                'delta' => self::rem((float) $delta),
                'baseValue' => $baseValue,
                'effectiveValue' => $effectiveValue,
                'overlapWidth' => self::rem($overlapWidth),
                'gap' => self::rem($gap),
                'reason' => 'Automatic trunk-label vs branch-footprint collision compensation.',
            ];
            $branches[$branchIndex]['layout'] = [
                ...((array) ($branches[$branchIndex]['layout'] ?? [])),
                'appliedCompensations' => [
                    ...((array) data_get($branches[$branchIndex], 'layout.appliedCompensations', [])),
                    $appliedEntry,
                ],
            ];
            $applied[] = $appliedEntry;
        }

        return [
            'branches' => $branches,
            'applied' => $applied,
        ];
    }

    /**
     * Rekey-source end labels sit at the trunk-facing arc. Extending the
     * bridge moves the wrong footprint, so the first safe compensation is a
     * side switch when the opposite side is free against concrete trunk labels.
     *
     * @param  array<int, array<string, mixed>>  $rekeys
     * @param  array<int, array<string, mixed>>  $trunkBounds
     * @param  array<int, array<string, mixed>>  $collisions
     * @return array{rekeys: array<int, array<string, mixed>>, applied: array<int, array<string, mixed>>}
     */
    private static function applyRekeySourceEndLabelSideSwitchFromTrunkCollisions(array $rekeys, array $trunkBounds, array $collisions): array
    {
        $candidates = [];

        foreach ($collisions as $collision) {
            $against = ElementIdentifier::normalize(data_get($collision, 'against', ''));

            if ((string) data_get($collision, 'againstType') !== 'rekey-source-end-label') {
                continue;
            }

            if (preg_match('/^strang\.(left|right)\.(\d+)\.rekey\.source\./', $against, $matches) !== 1) {
                continue;
            }

            $rekeyIndex = self::rekeySourcePreviewIndexByCanonicalCounter($rekeys, $matches[1], (int) $matches[2]);

            if ($rekeyIndex === null) {
                continue;
            }

            $candidates[$rekeyIndex][] = $collision;
        }

        $applied = [];

        foreach ($candidates as $rekeyIndex => $sourceCollisions) {
            if (! isset($rekeys[$rekeyIndex])) {
                continue;
            }

            $side = (string) ($rekeys[$rekeyIndex]['side'] ?? 'left');
            $oppositeSide = $side === 'left' ? 'right' : 'left';
            $endNodeNumber = 5 + count((array) ($rekeys[$rekeyIndex]['stem_continuation'] ?? []));
            $nodeLabel = (array) data_get($rekeys[$rekeyIndex], 'node_labels.' . $endNodeNumber, []);
            $labelText = data_get($nodeLabel, $side);

            if (blank($labelText) || filled(data_get($nodeLabel, $oppositeSide))) {
                continue;
            }

            $sourceBox = collect((array) data_get($rekeys[$rekeyIndex], 'layout.rekeyBoundsDebug', []))
                ->first(static fn(array $box): bool => (string) ($box['type'] ?? '') === 'rekey-source-end-label');

            if (! is_array($sourceBox)) {
                continue;
            }

            $sourceBox = self::debugBoxToFloat($sourceBox);
            $anchorY = ((float) $sourceBox['yStart'] + (float) $sourceBox['yEnd']) / 2;
            $labelOptions = collect($nodeLabel)->except(['left', 'right', 'top', 'bottom'])->all();
            $candidateLabel = [
                ...$labelOptions,
                'text' => $labelText,
                'side' => $oppositeSide,
            ];
            $candidateBox = [
                'type' => 'rekey-source-end-label',
                'id' => (string) ($sourceBox['id'] ?? ''),
                'side' => $oppositeSide,
                ...self::labelBoxAt($candidateLabel, 0.0, $anchorY, $oppositeSide),
            ];

            $oppositeCollisionExists = collect($trunkBounds)
                ->filter(static fn(array $box): bool => in_array((string) ($box['type'] ?? ''), [
                    'trunk-left-label',
                    'trunk-right-label',
                    'trunk-terminal-label',
                ], true))
                ->map(static fn(array $box): array => self::debugBoxToFloat($box))
                ->contains(static function (array $trunkBox) use ($candidateBox, $oppositeSide): bool {
                    $trunkSide = (string) ($trunkBox['side'] ?? '');

                    if ($trunkSide !== '' && $trunkSide !== $oppositeSide) {
                        return false;
                    }

                    return self::debugBoxesOverlap($trunkBox, $candidateBox);
                });

            if ($oppositeCollisionExists) {
                continue;
            }

            $componentCounter = (int) ($rekeys[$rekeyIndex]['component_counter'] ?? ($rekeyIndex + 1));
            $target = ElementIdentifier::normalize('strang.rekey-source-' . $side . '.' . $componentCounter . '.main.path.rekey-source.arc-south-' . ($side === 'left' ? 'east' : 'west') . '-2.end-label');
            $nodeLabel = [
                ...$labelOptions,
                $oppositeSide => $labelText,
            ];
            $nodeLabels = (array) ($rekeys[$rekeyIndex]['node_labels'] ?? []);
            $nodeLabels[$endNodeNumber] = $nodeLabel;
            $rekeys[$rekeyIndex]['node_labels'] = $nodeLabels;

            $appliedEntry = [
                'target' => $target,
                'prop' => 'label_side',
                'baseValue' => $side,
                'effectiveValue' => $oppositeSide,
                'reason' => 'Automatic trunk-label vs rekey-source-end-label side-switch compensation.',
                'sources' => collect($sourceCollisions)
                    ->map(static fn(array $collision): array => [
                        'source' => 'trunk-rekey-source-label-collision',
                        'trunk' => ElementIdentifier::normalize(data_get($collision, 'trunk', '')),
                        'against' => ElementIdentifier::normalize(data_get($collision, 'against', '')),
                        'overlapWidth' => (string) data_get($collision, 'overlapWidth', '0rem'),
                        'overlapHeight' => (string) data_get($collision, 'overlapHeight', '0rem'),
                    ])
                    ->values()
                    ->all(),
            ];
            $rekeys[$rekeyIndex]['layout'] = [
                ...((array) ($rekeys[$rekeyIndex]['layout'] ?? [])),
                'appliedCompensations' => [
                    ...((array) data_get($rekeys[$rekeyIndex], 'layout.appliedCompensations', [])),
                    $appliedEntry,
                ],
            ];
            $applied[] = $appliedEntry;
        }

        return [
            'rekeys' => $rekeys,
            'applied' => $applied,
        ];
    }

    /**
     * Rekey-target has the same outward bridge escape from trunk labels as a
     * branch, but it remains a rekey preview and keeps its own component IDs.
     *
     * @param  array<int, array<string, mixed>>  $rekeys
     * @param  array<int, array<string, mixed>>  $collisions
     * @return array{rekeys: array<int, array<string, mixed>>, applied: array<int, array<string, mixed>>}
     */
    private static function applyRekeyTargetCompensationFromTrunkCollisions(array $rekeys, array $collisions): array
    {
        $increments = [];

        foreach ($collisions as $collision) {
            $against = ElementIdentifier::normalize(data_get($collision, 'against', ''));

            if (preg_match('/^strang\.(left|right)\.(\d+)\.rekey\.target\./', $against, $matches) !== 1) {
                continue;
            }

            $rekeyIndex = self::rekeyTargetPreviewIndexByCanonicalCounter($rekeys, $matches[1], (int) $matches[2]);

            if ($rekeyIndex === null) {
                continue;
            }

            $overlapWidth = self::toRem(data_get($collision, 'overlapWidth', '0rem'));
            $gap = Defaults::dataDrivenRem('debug_bound_box_gap', Defaults::graphString('debug_bound_box_gap', '2rem'));
            $delta = $overlapWidth + $gap;

            if ($delta <= 0.0) {
                continue;
            }

            $increments[$rekeyIndex] = max((float) ($increments[$rekeyIndex] ?? 0.0), $delta);
        }

        $applied = [];

        foreach ($increments as $rekeyIndex => $delta) {
            if (! isset($rekeys[$rekeyIndex])) {
                continue;
            }

            $side = (string) ($rekeys[$rekeyIndex]['side'] ?? 'right');
            $componentCounter = (int) ($rekeys[$rekeyIndex]['component_counter'] ?? ($rekeyIndex + 1));
            $target = ElementIdentifier::normalize('strang.rekey-target-' . $side . '.' . $componentCounter . '.main.path.rekey-target.bridge1');
            $baseValue = (string) ($rekeys[$rekeyIndex]['bridge_length'] ?? Defaults::dataDrivenString('bridge_length', Defaults::dataDrivenString('line_length', '4rem')));
            $effectiveValue = self::rem(self::toRem($baseValue) + (float) $delta);

            $rekeys[$rekeyIndex]['bridge_length'] = $effectiveValue;
            $appliedEntry = [
                'target' => $target,
                'prop' => 'bridge_length',
                'delta' => self::rem((float) $delta),
                'baseValue' => $baseValue,
                'effectiveValue' => $effectiveValue,
                'overlapWidth' => self::rem($overlapWidth),
                'gap' => self::rem($gap),
                'reason' => 'Automatic trunk-label vs rekey-target-footprint collision compensation.',
            ];
            $rekeys[$rekeyIndex]['layout'] = [
                ...((array) ($rekeys[$rekeyIndex]['layout'] ?? [])),
                'appliedCompensations' => [
                    ...((array) data_get($rekeys[$rekeyIndex], 'layout.appliedCompensations', [])),
                    $appliedEntry,
                ],
            ];
            $applied[] = $appliedEntry;
        }

        return [
            'rekeys' => $rekeys,
            'applied' => $applied,
        ];
    }

    /**
     * Add measured vertical merge collision deltas on top of the configured
     * merge stagger. The baseline rhythm is configured in merge_layout; this
     * method only reacts to remaining real start/stem overlaps.
     *
     * @param  array<int, array<string, mixed>>  $merges
     * @return array{merges: array<int, array<string, mixed>>, applied: array<int, array<string, mixed>>}
     */
    private static function applyMergeCollisionCompensation(array $merges): array
    {
        if (self::mergePreferredCompensationDirection() !== 'vertical') {
            return [
                'merges' => $merges,
                'applied' => [],
            ];
        }

        $increments = [];

        foreach (self::mergeConcreteCollisionDebugEntries($merges) as $collision) {
                $delta = self::toRem(data_get($collision, 'requiredStemIncrement', '0rem'));

                if ($delta <= 0.0) {
                    continue;
                }

                $target = self::mergeCompensationTargetForCollision($collision);

                if ($target === null) {
                    continue;
                }

                $key = implode('|', [
                    $target['previewIndex'],
                    $target['scope'],
                    $target['extensionIndex'] ?? 0,
                    $target['stemNumber'],
                ]);
                $increments[$key] = [
                    ...$target,
                    'delta' => max((float) data_get($increments, $key . '.delta', 0.0), $delta),
                    'sources' => [
                        ...((array) data_get($increments, $key . '.sources', [])),
                        [
                            'source' => (string) data_get($collision, 'type', 'merge-concrete-overlap'),
                            'first' => (string) data_get($collision, 'first', ''),
                            'second' => (string) data_get($collision, 'second', ''),
                            'requiredIncrement' => self::rem($delta),
                            'gap' => (string) data_get($collision, 'gap', self::rem(self::debugBoundBoxGap())),
                        ],
                    ],
                ];
        }

        $applied = [];

        foreach ($increments as $increment) {
            $previewIndex = (int) $increment['previewIndex'];

            if (! isset($merges[$previewIndex])) {
                continue;
            }

            $delta = (float) $increment['delta'];

            if ((string) $increment['scope'] === 'extension') {
                $extensionIndex = max(1, (int) ($increment['extensionIndex'] ?? 1));
                $continuations = (array) data_get($merges[$previewIndex], 'extension_stem_continuations.' . $extensionIndex, []);
                $stemNumber = self::mergeCompensationStemNumber($continuations);
                $continuationKey = $stemNumber - 1;
                $baseValue = self::mergeContinuationEntryLength(
                    $continuations[$continuationKey] ?? null,
                    (string) ($merges[$previewIndex]['extension_stem_length'] ?? $merges[$previewIndex]['stem_length'] ?? Defaults::dataDrivenString('stem_length', Defaults::dataDrivenString('line_length', '4rem'))),
                );
                $effectiveValue = self::rem(self::toRem($baseValue) + $delta);
                $continuations[$continuationKey] = self::mergeContinuationEntryWithLength(
                    $continuations[$continuationKey] ?? [],
                    $effectiveValue,
                );
                $merges[$previewIndex]['extension_stem_continuations'][$extensionIndex] = $continuations;
                $target = ElementIdentifier::normalize(
                    'strang.merge-' . (string) ($merges[$previewIndex]['side'] ?? 'left')
                    . '.' . ($previewIndex + 1)
                    . '.extension' . $extensionIndex
                    . '.path.merge-extension.stem' . $stemNumber,
                );
            } else {
                $continuations = (array) ($merges[$previewIndex]['stem_continuation'] ?? []);
                $stemNumber = self::mergeCompensationStemNumber($continuations);
                $continuationKey = $stemNumber - 1;
                $baseValue = self::mergeContinuationEntryLength(
                    $continuations[$continuationKey] ?? null,
                    (string) ($merges[$previewIndex]['stem_length'] ?? Defaults::dataDrivenString('stem_length', Defaults::dataDrivenString('line_length', '4rem'))),
                );
                $effectiveValue = self::rem(self::toRem($baseValue) + $delta);
                $continuations[$continuationKey] = self::mergeContinuationEntryWithLength(
                    $continuations[$continuationKey] ?? [],
                    $effectiveValue,
                );
                $merges[$previewIndex]['stem_continuation'] = $continuations;
                $target = ElementIdentifier::normalize(
                    'strang.merge-' . (string) ($merges[$previewIndex]['side'] ?? 'left')
                    . '.' . ($previewIndex + 1)
                    . '.main.path.merge.stem' . $stemNumber,
                );
            }

            $appliedEntry = [
                'target' => $target,
                'prop' => 'length',
                'delta' => self::rem($delta),
                'baseValue' => $baseValue,
                'effectiveValue' => $effectiveValue,
                'reason' => 'Automatic merge start/stem collision compensation on top of configured merge stagger.',
                'sources' => $increment['sources'],
            ];
            $merges[$previewIndex]['layout'] = [
                ...((array) ($merges[$previewIndex]['layout'] ?? [])),
                'appliedCompensations' => [
                    ...((array) data_get($merges[$previewIndex], 'layout.appliedCompensations', [])),
                    $appliedEntry,
                ],
            ];
            $applied[] = $appliedEntry;
        }

        return [
            'merges' => $merges,
            'applied' => $applied,
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $merges
     * @return array<int, array<string, string>>
     */
    private static function mergeConcreteCollisionDebugEntries(array $merges): array
    {
        $entries = collect($merges)
            ->flatMap(static function (array $merge, int $previewIndex): array {
                return collect((array) data_get($merge, 'layout.mergeBoundsDebug', []))
                    ->map(static fn(array $box): array => [
                        'previewIndex' => $previewIndex,
                        'box' => self::debugBoxToFloat($box),
                    ])
                    ->all();
            })
            ->values();
        $labels = $entries
            ->filter(static fn(array $entry): bool => in_array((string) data_get($entry, 'box.type'), [
                'merge-start-stem-labels',
                'merge-extension-start-stem-labels',
            ], true))
            ->values();
        $bridges = $entries
            ->filter(static fn(array $entry): bool => in_array((string) data_get($entry, 'box.type'), [
                'merge-bridge',
                'merge-extension-bridge',
            ], true))
            ->values();
        $collisions = [];

        foreach (['left', 'right'] as $side) {
            $sideLabels = $labels
                ->filter(static fn(array $entry): bool => (string) data_get($entry, 'box.side') === $side)
                ->values();

            foreach ($sideLabels as $position => $entry) {
                foreach ($sideLabels->slice($position + 1) as $next) {
                    $collision = self::mergeBoundsCollisionDebugEntry(
                        $side,
                        'merge-label-label-overlap',
                        (array) $entry['box'],
                        (array) $next['box'],
                    );

                    if ($collision !== null) {
                        $collisions[] = $collision;
                    }
                }
            }

            $sideBridges = $bridges
                ->filter(static fn(array $entry): bool => (string) data_get($entry, 'box.side') === $side)
                ->values();

            foreach ($sideBridges as $bridgeEntry) {
                foreach ($sideLabels as $labelEntry) {
                    if (self::mergeCollisionOwner((string) data_get($bridgeEntry, 'box.id', '')) === self::mergeCollisionOwner((string) data_get($labelEntry, 'box.id', ''))) {
                        continue;
                    }

                    $collision = self::mergeBoundsCollisionDebugEntry(
                        $side,
                        'merge-bridge-label-overlap',
                        (array) $bridgeEntry['box'],
                        (array) $labelEntry['box'],
                    );

                    if ($collision !== null) {
                        $collisions[] = $collision;
                    }
                }
            }
        }

        return collect($collisions)
            ->unique(static fn(array $entry): string => (string) $entry['first'] . '|' . (string) $entry['second'] . '|' . (string) $entry['type'])
            ->values()
            ->all();
    }

    /**
     * Resolve final side-strang end/bridge collisions by distributing the
     * required spacing across the affected trunk stems up to the next side
     * anchor. This keeps branch/rekey-target geometry intact without making one
     * stem carry the whole gap.
     *
     * @param  array<int|string, mixed>  $pathLengths
     * @param  array<int, array{delta: float, collisionKey: string}>  $adjustments
     * @param  array<int, array<string, mixed>>  $branches
     * @return array{path_lengths: array<int|string, mixed>, applied: array<int, array<string, mixed>>, handled_collision_keys: array<int, string>}
     */
    private static function applyTrunkPathSpacingCompensation(
        array $pathLengths,
        array $adjustments,
        array $branches,
        string $defaultLength,
        int $pass,
    ): array {
        $applied = [];
        $handledCollisionKeys = [];

        foreach ($adjustments as $pathNumber => $adjustment) {
            $pathNumber = (int) $pathNumber;
            $measuredDelta = (float) ($adjustment['delta'] ?? 0.0);
            $collisionKey = (string) ($adjustment['collisionKey'] ?? '');

            if ($pathNumber < 1 || $measuredDelta <= 0.0 || $collisionKey === '') {
                continue;
            }

            $factor = self::trunkSpacingCompensationFactor();
            $delta = $measuredDelta * $factor;

            if ($delta <= 0.0) {
                continue;
            }

            $handledCollisionKeys[] = $collisionKey;
            $distributedPathNumbers = self::distributedTrunkSpacingPathNumbers($pathNumber, $branches, $pathLengths, $delta);
            $distributedDelta = $delta / max(1, count($distributedPathNumbers));

            foreach ($distributedPathNumbers as $distributedPathNumber) {
                $currentEntry = $pathLengths[$distributedPathNumber] ?? null;
                $currentLength = self::pathEntryLength($currentEntry);
                $baseLength = $currentLength > 0.0 ? $currentLength : self::toRem($defaultLength);
                $effectiveLength = self::rem($baseLength + $distributedDelta);

                $pathLengths[$distributedPathNumber] = self::pathEntryWithLength($currentEntry, $effectiveLength);
                $applied[] = [
                    'target' => ElementIdentifier::normalize('strang.trunk.1.main.path.trunk.path' . $distributedPathNumber),
                    'prop' => 'length',
                    'delta' => self::rem($distributedDelta),
                    'baseValue' => self::rem($baseLength),
                    'effectiveValue' => $effectiveLength,
                    'reason' => 'Automatic distributed final side-strang-end vs side-strang-bridge collision compensation.',
                    'sources' => [
                        [
                            'source' => 'final-side-strang-end-bridge-pass-' . $pass,
                            'collisionKey' => $collisionKey,
                            'measuredIncrement' => self::rem($measuredDelta),
                            'requiredIncrement' => self::rem($delta),
                            'factor' => $factor,
                            'distributedAcross' => collect($distributedPathNumbers)
                                ->map(static fn(int $distributedPathNumber): string => ElementIdentifier::normalize('strang.trunk.1.main.path.trunk.path' . $distributedPathNumber))
                                ->values()
                                ->all(),
                        ],
                    ],
                ];
            }
        }

        return [
            'path_lengths' => $pathLengths,
            'applied' => $applied,
            'handled_collision_keys' => array_values(array_unique($handledCollisionKeys)),
        ];
    }

    private static function trunkSpacingCompensationFactor(): float
    {
        $value = Defaults::dataDriven(
            'trunk_spacing_compensation_factor',
            Defaults::graph('trunk_spacing_compensation_factor', 1.0),
        );

        return max(0.0, is_numeric($value) ? (float) $value : 1.0);
    }

    private static function color(string $key, string $fallback): string
    {
        return Defaults::dataDrivenString('colors.' . $key, Defaults::graphString('colors.' . $key, $fallback));
    }

    private static function defaultConnectorLength(string $key): string
    {
        return Defaults::dataDrivenString(
            $key,
            Defaults::graphString($key, Defaults::dataDrivenString('connector_length', '2rem')),
        );
    }

    /**
     * @param  array<int, array<string, mixed>>  $branches
     * @param  array<int|string, mixed>  $pathLengths
     * @return array<int, int>
     */
    private static function distributedTrunkSpacingPathNumbers(int $pathNumber, array $branches, array $pathLengths, float $delta): array
    {
        $pathCount = max(1, count($pathLengths));
        $endPathNumber = collect($branches)
            ->map(static fn(array $branch): ?int => self::trunkAttachPathNumber((string) ($branch['attach_to'] ?? '')))
            ->filter(static fn(?int $attachPath): bool => $attachPath !== null && $attachPath >= $pathNumber)
            ->sort()
            ->first();

        $endPathNumber = min($pathCount, max($pathNumber, (int) ($endPathNumber ?? $pathNumber)));

        $availablePathNumbers = range($pathNumber, $endPathNumber);
        $stemStep = Defaults::dataDrivenRem(
            'trunk_spacing_compensation_stem_step',
            Defaults::graphString('trunk_spacing_compensation_stem_step', '2.75rem'),
        ) ?: 2.75;
        $desiredCount = max(1, (int) ceil($delta / $stemStep));

        return array_slice($availablePathNumbers, 0, min(count($availablePathNumbers), $desiredCount));
    }

    /**
     * @param  array<int, array<string, mixed>>  $branches
     */
    private static function branchPreviewIndexByCanonicalCounter(array $branches, string $side, int $counter): ?int
    {
        foreach ($branches as $index => $branch) {
            if ((string) ($branch['side'] ?? '') === $side && (int) ($branch['component_counter'] ?? 0) === $counter) {
                return (int) $index;
            }
        }

        return null;
    }

    /**
     * @param  array<int, array<string, mixed>>  $rekeys
     */
    private static function rekeySourcePreviewIndexByCanonicalCounter(array $rekeys, string $side, int $counter): ?int
    {
        foreach ($rekeys as $index => $rekey) {
            if (
                (string) ($rekey['kind'] ?? '') === 'source'
                && (string) ($rekey['side'] ?? '') === $side
                && (int) ($rekey['component_counter'] ?? 0) === $counter
            ) {
                return (int) $index;
            }
        }

        return null;
    }

    /**
     * @param  array<int, array<string, mixed>>  $rekeys
     */
    private static function rekeyTargetPreviewIndexByCanonicalCounter(array $rekeys, string $side, int $counter): ?int
    {
        foreach ($rekeys as $index => $rekey) {
            if (
                (string) ($rekey['kind'] ?? '') === 'target'
                && (string) ($rekey['side'] ?? '') === $side
                && (int) ($rekey['component_counter'] ?? 0) === $counter
            ) {
                return (int) $index;
            }
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $branch
     */
    private static function branchPreviewId(array $branch): string
    {
        $id = trim((string) ($branch['id'] ?? ''));

        if ($id !== '') {
            return ElementIdentifier::normalize($id);
        }

        return ElementIdentifier::normalize(
            'strang.branch-' . (string) ($branch['side'] ?? 'left') . '.' . (string) ($branch['component_counter'] ?? 1),
        );
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
            'id' => ElementIdentifier::normalize('strang.trunk.1.bounds'),
            'renderId' => 'strang.trunk.1.bounds',
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
            'id' => ElementIdentifier::normalize('strang.trunk.1.start.label-bounds'),
            'renderId' => 'strang.trunk.1.start.label-bounds',
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
            'id' => ElementIdentifier::normalize('strang.trunk.1.end.label-bounds'),
            'renderId' => 'strang.trunk.1.end.label-bounds',
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
            'id' => ElementIdentifier::normalize('strang.trunk.1.label-bounds'),
            'renderId' => 'strang.trunk.1.label-bounds',
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

            $renderId = 'strang.trunk.1.middle.' . (count($boxes) + 1) . '.label-bounds';
            $boxes[] = [
                'type' => 'middle-label-inclusive',
                'id' => ElementIdentifier::normalize($renderId),
                'renderId' => $renderId,
                'x' => self::rem($bounds['xStart']),
                'y' => self::rem($bounds['yStart']),
                'width' => self::rem($bounds['xEnd'] - $bounds['xStart']),
                'height' => self::rem($bounds['yEnd'] - $bounds['yStart']),
            ];
        }

        return $boxes;
    }

    /**
     * Side-aware trunk label bounds used by trunk-vs-strang collision checks.
     * The aggregate trunk label box stays available for visual inspection, but
     * compensation must not let a wide left label influence right-side strangs.
     *
     * @param  array<int, mixed>  $pathLengths
     * @param  array<string, mixed>  $startNodeLabels
     * @param  array<int|string, mixed>  $nodeLabels
     * @param  array<int, int>  $attachedPathNumbers
     * @return array<int, array<string, string>>
     */
    private static function trunkSideLabelBoundsDebug(
        array $pathLengths,
        string $startLength,
        string $endLength,
        array $startNodeLabels,
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

        foreach (['left', 'right'] as $side) {
            $startBounds = [
                'xStart' => -0.95,
                'xEnd' => 0.95,
                'yStart' => 0.0,
                'yEnd' => self::toRem($startLength),
            ];
            $startHasLabel = false;

            foreach (self::labelsFromSideMap($startNodeLabels) as $label) {
                if ((string) data_get($label, 'side') !== $side) {
                    continue;
                }

                $startBounds = self::expandBoundsForLabel($startBounds, $label, $nodeAnchors[1] ?? self::toRem($startLength));
                $startHasLabel = true;
            }

            if ($startHasLabel) {
                $boxes[] = [
                    'type' => 'start-' . $side . '-label-inclusive',
                    'id' => ElementIdentifier::normalize('strang.trunk.1.start.' . $side . '.label-bounds'),
                    'renderId' => 'strang.trunk.1.start.' . $side . '.label-bounds',
                    'side' => $side,
                    'x' => self::rem($startBounds['xStart']),
                    'y' => self::rem($startBounds['yStart']),
                    'width' => self::rem($startBounds['xEnd'] - $startBounds['xStart']),
                    'height' => self::rem($startBounds['yEnd'] - $startBounds['yStart']),
                ];
            }
        }

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

            foreach (['left', 'right'] as $side) {
                $bounds = [
                    'xStart' => -0.95,
                    'xEnd' => 0.95,
                    'yStart' => $yStart,
                    'yEnd' => $yEnd,
                ];
                $hasLabel = false;

                for ($pathNumber = $startPath; $pathNumber < $endPathExclusive; $pathNumber++) {
                    $anchorY = $nodeAnchors[$pathNumber + 1] ?? null;

                    if ($anchorY === null) {
                        continue;
                    }

                    foreach (self::labelsFromMixed($nodeLabels[$pathNumber] ?? []) as $label) {
                        if ((string) data_get($label, 'side') !== $side) {
                            continue;
                        }

                        $bounds = self::expandBoundsForLabel($bounds, $label, $anchorY);
                        $hasLabel = true;
                    }
                }

                if (! $hasLabel) {
                    continue;
                }

                $renderId = 'strang.trunk.1.middle.' . ($index + 1) . '.' . $side . '.label-bounds';
                $boxes[] = [
                    'type' => 'middle-' . $side . '-label-inclusive',
                    'id' => ElementIdentifier::normalize($renderId),
                    'renderId' => $renderId,
                    'side' => $side,
                    'x' => self::rem($bounds['xStart']),
                    'y' => self::rem($bounds['yStart']),
                    'width' => self::rem($bounds['xEnd'] - $bounds['xStart']),
                    'height' => self::rem($bounds['yEnd'] - $bounds['yStart']),
                ];
            }
        }

        return $boxes;
    }

    /**
     * Concrete trunk label boxes used as the most precise collision footprint.
     * These do not include the trunk body between labels; each box maps one
     * rendered left/right node label to its own measured text-label area.
     *
     * @param  array<int, mixed>  $pathLengths
     * @param  array<string, mixed>  $startNodeLabels
     * @param  array<int|string, mixed>  $nodeLabels
     * @return array<int, array<string, string>>
     */
    private static function trunkConcreteLabelBoundsDebug(
        array $pathLengths,
        string $startLength,
        array $startNodeLabels,
        array $nodeLabels,
    ): array {
        $nodeAnchors = self::trunkNodeAnchors($pathLengths, $startLength);
        $boxes = [];

        foreach (self::labelsFromSideMap($startNodeLabels) as $labelIndex => $label) {
            $side = (string) data_get($label, 'side', '');

            if (! in_array($side, ['left', 'right'], true)) {
                continue;
            }

            $boxes[] = self::trunkConcreteLabelBox(
                'strang.trunk.1.start.nodeEndLabel' . ($labelIndex + 1) . '.bounds',
                $label,
                $nodeAnchors[1] ?? self::toRem($startLength),
                $side,
            );
        }

        foreach ($nodeLabels as $pathNumber => $labels) {
            $anchorY = $nodeAnchors[(int) $pathNumber + 1] ?? null;

            if ($anchorY === null) {
                continue;
            }

            foreach (self::labelsFromMixed($labels) as $labelIndex => $label) {
                $side = (string) data_get($label, 'side', '');

                if (! in_array($side, ['left', 'right'], true)) {
                    continue;
                }

                $boxes[] = self::trunkConcreteLabelBox(
                    'strang.trunk.1.path.trunk.path' . (int) $pathNumber . '.nodeEndLabel' . ($labelIndex + 1) . '.bounds',
                    $label,
                    $anchorY,
                    $side,
                );
            }
        }

        return $boxes;
    }

    /**
     * Concrete terminal label boxes are side-neutral because centered
     * start/end labels can overlap left or right side strangs.
     *
     * @param  array<int, mixed>  $pathLengths
     * @param  array<string, mixed>  $startLabel
     * @param  array<string, mixed>  $endLabel
     * @return array<int, array<string, string>>
     */
    private static function trunkConcreteTerminalLabelBoundsDebug(
        array $pathLengths,
        string $startLength,
        string $endLength,
        array $startLabel,
        array $endLabel,
    ): array {
        $bodyHeight = self::toRem($startLength)
            + collect($pathLengths)
                ->map(static fn(mixed $entry): float => self::pathEntryLength($entry))
                ->sum()
            + self::toRem($endLength);
        $boxes = [];

        if (filled(data_get($startLabel, 'text'))) {
            $boxes[] = self::trunkConcreteTerminalLabelBox(
                'strang.trunk.1.start.start-label.bounds',
                $startLabel,
                0.0,
                (string) data_get($startLabel, 'side', 'bottom'),
                self::toRem(data_get($startLabel, 'offset', '0.75rem')),
            );
        }

        if (filled(data_get($endLabel, 'text'))) {
            $boxes[] = self::trunkConcreteTerminalLabelBox(
                'strang.trunk.1.end.end-label.bounds',
                $endLabel,
                $bodyHeight,
                (string) data_get($endLabel, 'side', 'top'),
                self::toRem(data_get($endLabel, 'offset', '0.75rem')),
            );
        }

        return $boxes;
    }

    /**
     * @param  array<string, mixed>  $label
     * @return array<string, string>
     */
    private static function trunkConcreteLabelBox(string $id, array $label, float $anchorY, string $side): array
    {
        $box = self::labelBoxAt($label, 0.0, $anchorY, $side);

        return [
            'type' => 'trunk-' . $side . '-label',
            'id' => ElementIdentifier::normalize($id),
            'renderId' => $id,
            'side' => $side,
            'x' => self::rem($box['xStart']),
            'y' => self::rem($box['yStart']),
            'width' => self::rem($box['xEnd'] - $box['xStart']),
            'height' => self::rem($box['yEnd'] - $box['yStart']),
        ];
    }

    /**
     * @param  array<string, mixed>  $label
     * @return array<string, string>
     */
    private static function trunkConcreteTerminalLabelBox(string $id, array $label, float $anchorY, string $side, float $offset): array
    {
        $box = self::labelBoxAt($label, 0.0, $anchorY, $side, $offset);

        return [
            'type' => 'trunk-terminal-label',
            'id' => ElementIdentifier::normalize($id),
            'renderId' => $id,
            'x' => self::rem($box['xStart']),
            'y' => self::rem($box['yStart']),
            'width' => self::rem($box['xEnd'] - $box['xStart']),
            'height' => self::rem($box['yEnd'] - $box['yStart']),
        ];
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
     * Keep rekey-target attached to the planned trunk timeline instead of a
     * fixed DEV-era path number.
     *
     * @param  array<int, array<string, mixed>>  $rekeyPreviews
     * @return array<int, array<string, mixed>>
     */
    private static function withRekeyTargetAttachPath(array $rekeyPreviews, ?int $pathNumber): array
    {
        if ($pathNumber === null) {
            return $rekeyPreviews;
        }

        return collect($rekeyPreviews)
            ->map(static function (array $preview) use ($pathNumber): array {
                if ((string) ($preview['kind'] ?? '') !== 'target') {
                    return $preview;
                }

                $preview['attach_to'] = 'strang.trunk.path.' . $pathNumber . '.end';

                return $preview;
            })
            ->values()
            ->all();
    }

    /**
     * Build only the trunk paths that are required by labels, attachments,
     * corrections or a dedicated rekey-target label.
     *
     * @param  array<int|string, mixed>  $eventPathEntries
     * @param  array<int, int>  $attachedPathNumbers
     * @param  array<int, array<string, mixed>>  $layoutCorrections
     */
    private static function plannedTrunkPathCount(
        array $eventPathEntries,
        array $attachedPathNumbers,
        ?int $rekeyTargetPathNumber,
        array $layoutCorrections,
    ): int {
        return max([
            1,
            collect(array_keys($eventPathEntries))
                ->map(static fn(int|string $pathNumber): int => (int) $pathNumber)
                ->max() ?? 0,
            collect($attachedPathNumbers)->max() ?? 0,
            $rekeyTargetPathNumber ?? 0,
            LayoutCorrectionConfig::maxTrunkPathNumber($layoutCorrections),
        ]);
    }

    /**
     * Report-only collision layer: use the same debug boxes that are rendered
     * in DEV mode and do not mutate graph geometry here.
     *
     * @param  array<int, array<string, mixed>>  $trunkBounds
     * @param  array<int, array<string, mixed>>  $branchPreviews
     * @param  array<int, array<string, mixed>>  $mergePreviews
     * @param  array<int, array<string, mixed>>  $rekeyPreviews
     * @return array<int, array<string, string>>
     */
    private static function trunkPotentialCollisions(array $trunkBounds, array $branchPreviews, array $mergePreviews = [], array $rekeyPreviews = []): array
    {
        $trunkBoxes = collect($trunkBounds)
            ->filter(static fn(array $box): bool => in_array((string) ($box['type'] ?? ''), [
                'trunk-left-label',
                'trunk-right-label',
                'trunk-terminal-label',
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
        $rekeyBoxes = collect($rekeyPreviews)
            ->flatMap(static fn(array $rekey): array => (array) data_get($rekey, 'layout.rekeyBoundsDebug', []))
            ->map(static fn(array $box): array => self::debugBoxToFloat($box))
            ->values();
        $collisions = [];

        foreach ($trunkBoxes as $trunkBox) {
            foreach ($branchBoxes->merge($mergeBoxes)->merge($rekeyBoxes) as $againstBox) {
                $againstSide = (string) ($againstBox['side'] ?? '');

                $trunkSide = (string) ($trunkBox['side'] ?? '');
                if ($trunkSide !== '' && $againstSide !== '' && $trunkSide !== $againstSide) {
                    continue;
                }

                if (! self::debugBoxesOverlap($trunkBox, $againstBox)) {
                    continue;
                }

                $collisions[] = [
                    'type' => 'trunk-strang-bounds',
                    'trunk' => ElementIdentifier::normalize($trunkBox['id'] ?? ''),
                    'against' => ElementIdentifier::normalize($againstBox['id'] ?? ''),
                    'trunkType' => (string) ($trunkBox['type'] ?? ''),
                    'againstType' => (string) ($againstBox['type'] ?? ''),
                    'overlapWidth' => self::rem(min((float) $trunkBox['xEnd'], (float) $againstBox['xEnd']) - max((float) $trunkBox['xStart'], (float) $againstBox['xStart'])),
                    'overlapHeight' => self::rem(min((float) $trunkBox['yEnd'], (float) $againstBox['yEnd']) - max((float) $trunkBox['yStart'], (float) $againstBox['yStart'])),
                    'trunkYEnd' => self::rem((float) $trunkBox['yEnd']),
                    'againstYStart' => self::rem((float) $againstBox['yStart']),
                ];
            }
        }

        return $collisions;
    }

    /**
     * Report-only merge collision layer. It compares the verified merge
     * start/stem footprints and bridges on each side and records the measured
     * delta without mutating bridge or stem props.
     *
     * @param  array<int, array<string, mixed>>  $mergePreviews
     * @return array<int, array<string, mixed>>
     */
    private static function withMergeCollisionDebug(array $mergePreviews): array
    {
        foreach ($mergePreviews as $index => $mergePreview) {
            unset($mergePreviews[$index]['layout']['mergeCollisionDebug']);
        }

        $entries = collect($mergePreviews)
            ->flatMap(static function (array $mergePreview, int $previewIndex): array {
                return collect((array) data_get($mergePreview, 'layout.mergeBoundsDebug', []))
                    ->map(static fn(array $box): array => [
                        'previewIndex' => $previewIndex,
                        'box' => self::debugBoxToFloat($box),
                    ])
                    ->all();
            })
            ->values();

        $footprints = $entries
            ->filter(static fn(array $entry): bool => in_array((string) data_get($entry, 'box.type'), [
                'merge-start-stem',
                'merge-extension-start-stem',
            ], true))
            ->values();
        $bridges = $entries
            ->filter(static fn(array $entry): bool => in_array((string) data_get($entry, 'box.type'), [
                'merge-bridge',
                'merge-extension-bridge',
            ], true))
            ->values();
        $debugEntries = [];

        foreach (['left', 'right'] as $side) {
            $sideFootprints = $footprints
                ->filter(static fn(array $entry): bool => (string) data_get($entry, 'box.side') === $side)
                ->values();

            foreach ($sideFootprints as $position => $entry) {
                foreach ($sideFootprints->slice($position + 1) as $next) {
                    $collision = self::mergeBoundsCollisionDebugEntry(
                        $side,
                        'merge-start-stem-overlap',
                        (array) $entry['box'],
                        (array) $next['box'],
                    );

                    if ($collision === null) {
                        continue;
                    }

                    $debugEntries[(int) $entry['previewIndex']][] = $collision;
                    $debugEntries[(int) $next['previewIndex']][] = $collision;
                }
            }

            $sideBridges = $bridges
                ->filter(static fn(array $entry): bool => (string) data_get($entry, 'box.side') === $side)
                ->values();

            foreach ($sideBridges as $bridgeEntry) {
                foreach ($sideFootprints as $footprintEntry) {
                    if ((string) data_get($bridgeEntry, 'box.id') === (string) data_get($footprintEntry, 'box.id')) {
                        continue;
                    }

                    if (self::mergeCollisionOwner((string) data_get($bridgeEntry, 'box.id', '')) === self::mergeCollisionOwner((string) data_get($footprintEntry, 'box.id', ''))) {
                        continue;
                    }

                    $collision = self::mergeBoundsCollisionDebugEntry(
                        $side,
                        'merge-bridge-start-stem-overlap',
                        (array) $bridgeEntry['box'],
                        (array) $footprintEntry['box'],
                    );

                    if ($collision === null) {
                        continue;
                    }

                    $debugEntries[(int) $bridgeEntry['previewIndex']][] = $collision;
                    $debugEntries[(int) $footprintEntry['previewIndex']][] = $collision;
                }
            }
        }

        foreach ($debugEntries as $index => $entriesForPreview) {
            $mergePreviews[$index]['layout'] = [
                ...((array) ($mergePreviews[$index]['layout'] ?? [])),
                'mergeCollisionDebug' => collect($entriesForPreview)
                    ->unique(static fn(array $entry): string => (string) $entry['first'] . '|' . (string) $entry['second'] . '|' . (string) $entry['type'])
                    ->values()
                    ->all(),
            ];
        }

        return $mergePreviews;
    }

    /**
     * @param  array<string, mixed>  $first
     * @param  array<string, mixed>  $second
     * @return array<string, string>|null
     */
    private static function mergeBoundsCollisionDebugEntry(string $side, string $type, array $first, array $second): ?array
    {
        if (! self::debugBoxesOverlapWithGap($first, $second)) {
            return null;
        }

        $preferredDirection = self::mergePreferredCompensationDirection();
        $requiredBridgeIncrement = self::requiredOutwardSideIncrement($side, $first, $second);
        $requiredStemIncrement = in_array($type, [
            'merge-start-stem-overlap',
            'merge-label-label-overlap',
            'merge-bridge-label-overlap',
        ], true)
            ? self::requiredVerticalDebugBoxIncrement($first, $second)
            : 0.0;

        return [
            'type' => $type,
            'side' => $side,
            'first' => ElementIdentifier::normalize((string) data_get($first, 'id', '')),
            'second' => ElementIdentifier::normalize((string) data_get($second, 'id', '')),
            'overlapWidth' => self::rem(self::debugBoxOverlapWidth($first, $second)),
            'overlapHeight' => self::rem(self::debugBoxOverlapHeight($first, $second)),
            'preferredCompensationDirection' => $preferredDirection,
            'preferredIncrement' => self::rem($preferredDirection === 'horizontal'
                ? $requiredBridgeIncrement
                : $requiredStemIncrement),
            'requiredBridgeIncrement' => self::rem($requiredBridgeIncrement),
            'requiredStemIncrement' => self::rem($requiredStemIncrement),
            'gap' => self::rem(self::debugBoundBoxGap()),
        ];
    }

    /**
     * @param  array<string, mixed>  $first
     * @param  array<string, mixed>  $second
     */
    private static function debugBoxesOverlapWithGap(array $first, array $second): bool
    {
        $gap = self::debugBoundBoxGap();

        return (float) $first['xStart'] < (float) $second['xEnd'] + $gap
            && (float) $second['xStart'] < (float) $first['xEnd'] + $gap
            && (float) $first['yStart'] < (float) $second['yEnd'] + $gap
            && (float) $second['yStart'] < (float) $first['yEnd'] + $gap;
    }

    /**
     * @param  array<string, mixed>  $first
     * @param  array<string, mixed>  $second
     */
    private static function debugBoxOverlapWidth(array $first, array $second): float
    {
        return max(0.0, min((float) $first['xEnd'], (float) $second['xEnd']) - max((float) $first['xStart'], (float) $second['xStart']));
    }

    /**
     * @param  array<string, mixed>  $first
     * @param  array<string, mixed>  $second
     */
    private static function debugBoxOverlapHeight(array $first, array $second): float
    {
        return max(0.0, min((float) $first['yEnd'], (float) $second['yEnd']) - max((float) $first['yStart'], (float) $second['yStart']));
    }

    /**
     * @param  array<string, mixed>  $first
     * @param  array<string, mixed>  $second
     */
    private static function requiredVerticalDebugBoxIncrement(array $first, array $second): float
    {
        return self::debugBoxOverlapHeight($first, $second) + self::debugBoundBoxGap();
    }

    /**
     * @param  array<string, mixed>  $inner
     * @param  array<string, mixed>  $outer
     */
    private static function requiredOutwardSideIncrement(string $side, array $first, array $second): float
    {
        $gap = self::debugBoundBoxGap();

        if ($side === 'left') {
            $inner = (float) $first['xEnd'] >= (float) $second['xEnd'] ? $first : $second;
            $outer = $inner === $first ? $second : $first;

            return max(0.0, (float) $outer['xEnd'] - (float) $inner['xStart'] + $gap);
        }

        $inner = (float) $first['xStart'] <= (float) $second['xStart'] ? $first : $second;
        $outer = $inner === $first ? $second : $first;

        return max(0.0, (float) $inner['xEnd'] - (float) $outer['xStart'] + $gap);
    }

    private static function mergeCollisionOwner(string $id): string
    {
        $id = ElementIdentifier::normalize($id);

        if (preg_match('/^(strang\.(?:left|right)\.\d+\.merge(?:\.extension\.\d+)?)/', $id, $matches) === 1) {
            return $matches[1];
        }

        return $id;
    }

    /**
     * @param  array<string, mixed>  $collision
     * @return array{previewIndex: int, scope: string, extensionIndex?: int, sequenceNumber: int, stemNumber: int}|null
     */
    private static function mergeCompensationTargetForCollision(array $collision): ?array
    {
        $owners = collect([
            self::mergeCompensationOwner((string) data_get($collision, 'first', '')),
            self::mergeCompensationOwner((string) data_get($collision, 'second', '')),
        ])->filter()->values();

        if ($owners->isEmpty()) {
            return null;
        }

        $target = $owners
            ->first(static fn(array $owner): bool => self::matchesMergeVerticalStaggerSequence((int) $owner['sequenceNumber']))
            ?? $owners->sortByDesc(static fn(array $owner): int => (int) $owner['sequenceNumber'])->first();

        if (! is_array($target)) {
            return null;
        }

        return [
            ...$target,
            'stemNumber' => self::mergeVerticalStaggerStemNumber(),
        ];
    }

    /**
     * @return array{previewIndex: int, scope: string, extensionIndex?: int, sequenceNumber: int}|null
     */
    private static function mergeCompensationOwner(string $id): ?array
    {
        $id = ElementIdentifier::normalize($id);

        if (preg_match('/^strang\.(left|right)\.(\d+)\.merge\.extension\.(\d+)/', $id, $matches) === 1) {
            $extensionIndex = (int) $matches[3];

            return [
                'previewIndex' => max(0, (int) $matches[2] - 1),
                'scope' => 'extension',
                'extensionIndex' => $extensionIndex,
                'sequenceNumber' => $extensionIndex + 1,
            ];
        }

        if (preg_match('/^strang\.(left|right)\.(\d+)\.merge\./', $id, $matches) === 1) {
            return [
                'previewIndex' => max(0, (int) $matches[2] - 1),
                'scope' => 'main',
                'sequenceNumber' => 1,
            ];
        }

        return null;
    }

    private static function mergePreferredCompensationDirection(): string
    {
        $value = Defaults::dataDrivenString(
            'merge_layout.preferred_compensation_direction',
            Defaults::graphString('merge_layout.preferred_compensation_direction', 'vertical'),
        );

        return in_array($value, ['vertical', 'horizontal'], true) ? $value : 'vertical';
    }

    private static function matchesMergeVerticalStaggerSequence(int $sequenceNumber): bool
    {
        $sequence = Defaults::dataDrivenString(
            'merge_layout.vertical_stagger_sequence',
            Defaults::graphString('merge_layout.vertical_stagger_sequence', 'even'),
        );

        return match ($sequence) {
            'odd' => $sequenceNumber % 2 === 1,
            default => $sequenceNumber % 2 === 0,
        };
    }

    private static function mergeVerticalStaggerStemNumber(): int
    {
        $value = Defaults::dataDriven(
            'merge_layout.vertical_stagger_stem',
            Defaults::graph('merge_layout.vertical_stagger_stem', 2),
        );

        return max(2, is_numeric($value) ? (int) $value : 2);
    }

    /**
     * @param  array<int|string, mixed>  $continuation
     */
    private static function mergeCompensationStemNumber(array $continuation): int
    {
        $lastExistingKey = collect(array_keys($continuation))
            ->map(static fn(mixed $key): int => is_numeric($key) ? (int) $key : 0)
            ->max();

        return max(
            self::mergeVerticalStaggerStemNumber(),
            (is_numeric($lastExistingKey) ? (int) $lastExistingKey : 0) + 1,
        );
    }

    private static function mergeContinuationEntryLength(mixed $entry, string $defaultLength): string
    {
        if (is_array($entry)) {
            return Defaults::string(data_get($entry, 'length', data_get($entry, 0)), $defaultLength, '4rem');
        }

        return Defaults::string($entry, $defaultLength, '4rem');
    }

    private static function mergeContinuationEntryWithLength(mixed $entry, string $length): array
    {
        if (! is_array($entry)) {
            return ['length' => $length];
        }

        $entry['length'] = $length;

        return $entry;
    }

    private static function debugBoundBoxGap(): float
    {
        return Defaults::dataDrivenRem('debug_bound_box_gap', Defaults::graphString('debug_bound_box_gap', '2rem'));
    }

    /**
     * Final trunk spacing works on rendered side-strang footprints. Branches
     * already use that shape directly; rekey-target has the same lower
     * bridge/body/end geometry and is normalized here so the resolver can reuse
     * the verified formula without adding a rekey-only layout path.
     *
     * @param  array<int, array<string, mixed>>  $branchPreviews
     * @param  array<int, array<string, mixed>>  $rekeyPreviews
     * @param  array<int|string, mixed>  $trunkPathLengths
     * @return array<int, array<string, mixed>>
     */
    private static function trunkSpacingCollisionCandidates(
        array $branchPreviews,
        array $rekeyPreviews,
        array $trunkPathLengths,
        string $trunkStartLength,
    ): array {
        $trunkAnchors = self::trunkNodeAnchors($trunkPathLengths, $trunkStartLength);
        $rekeyTargetCandidates = collect($rekeyPreviews)
            ->filter(static fn(array $preview): bool => (string) ($preview['kind'] ?? '') === 'target')
            ->values()
            ->map(static function (array $preview, int $index) use ($trunkAnchors): array {
                $side = (string) ($preview['side'] ?? 'right');
                $componentCounter = (int) ($preview['component_counter'] ?? ($index + 1));
                $componentId = 'strang.rekey-target-' . $side . '.' . $componentCounter;
                $attachTo = (string) ($preview['attach_to'] ?? 'strang.trunk.path.7.end');
                $attachPath = self::trunkAttachPathNumber($attachTo);
                $stemLength = self::toRem($preview['stem_length'] ?? Defaults::dataDrivenString('stem_length', Defaults::dataDrivenString('line_length', '4rem')));

                return [
                    'id' => $componentId,
                    'side' => $side,
                    'component_counter' => $componentCounter,
                    'attach_to' => $attachTo,
                    'anchor_y_rem' => $attachPath !== null ? ($trunkAnchors[$attachPath + 1] ?? null) : null,
                    'bridge_length' => $preview['bridge_length'] ?? Defaults::dataDrivenString('bridge_length', Defaults::dataDrivenString('line_length', '4rem')),
                    'arc_size' => data_get($preview, 'arc_size', data_get($preview, 'arc_sizes.1', self::defaultArcSize())),
                    'entry_stem_length' => '0rem',
                    'step' => [],
                    'stem_length' => self::rem($stemLength),
                    'stem_continuation' => self::effectiveStemContinuationEntries((array) ($preview['stem_continuation'] ?? []), $stemLength),
                    'end_length' => $preview['end_length'] ?? Defaults::dataDrivenString('line_length', '4rem'),
                    'end_label' => (array) ($preview['end_label'] ?? []),
                    'collision_bridge_id' => $componentId . '.main.path.rekey-target.bridge1',
                    'collision_end_segment_id' => $componentId . '.end.path.rekey-target-end.segment',
                ];
            })
            ->all();

        return [
            ...$branchPreviews,
            ...$rekeyTargetCandidates,
        ];
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
        $bridgeLength = self::toRem($mergePreview['bridge_length'] ?? Defaults::dataDrivenString('bridge_length', Defaults::dataDrivenString('line_length', '4rem')));
        $arcOutSize = self::toRem(data_get($mergePreview, 'arc_sizes.2', data_get($mergePreview, 'arc_sizes.out', self::defaultArcSize()))) ?: self::defaultArcSizeRem();
        $startLength = self::toRem($mergePreview['start_length'] ?? $arcOutSize . 'rem');
        $stemLength = self::toRem($mergePreview['stem_length'] ?? Defaults::dataDrivenString('stem_length', Defaults::dataDrivenString('line_length', '4rem')));
        $stemContinuationLength = self::stemContinuationLength((array) ($mergePreview['stem_continuation'] ?? []), $stemLength);
        $arcInSize = self::toRem(data_get($mergePreview, 'arc_sizes.1', data_get($mergePreview, 'arc_sizes.in', self::defaultArcSize()))) ?: self::defaultArcSizeRem();
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
        $extensionBridgeDefault = self::toRem($mergePreview['extension_bridge_length'] ?? $mergePreview['bridge_length'] ?? Defaults::dataDrivenString('bridge_length', Defaults::dataDrivenString('line_length', '4rem')));
        $extensionTargetX = $mainOuterX;
        $extensionTargetY = $mainBridgeY;
        $extensionStartLength = self::toRem($mergePreview['extension_start_length'] ?? self::defaultArcSize());
        $extensionStemDefault = self::toRem($mergePreview['extension_stem_length'] ?? $mergePreview['stem_length'] ?? Defaults::dataDrivenString('stem_length', Defaults::dataDrivenString('line_length', '4rem')));
        $extensionArcDefault = self::toRem($mergePreview['extension_arc_size'] ?? self::defaultArcSize());

        for ($extensionIndex = 1; $extensionIndex <= $extensionCount; $extensionIndex++) {
            $extensionBridgeLength = self::toRem(data_get($mergePreview, 'extension_bridge_continuations.' . $extensionIndex, $extensionBridgeDefault));
            $extensionStemLength = self::toRem(data_get($mergePreview, 'extension_stem_lengths.' . $extensionIndex, $extensionStemDefault));
            $extensionStemContinuationLength = self::stemContinuationLength(
                (array) data_get($mergePreview, 'extension_stem_continuations.' . $extensionIndex, []),
                $extensionStemLength,
            );
            $extensionArcSize = self::toRem(data_get($mergePreview, 'extension_arc_sizes.' . $extensionIndex, $extensionArcDefault)) ?: self::defaultArcSizeRem();
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
        $bridgeLength = self::toRem($rekeyPreview['bridge_length'] ?? Defaults::dataDrivenString('bridge_length', Defaults::dataDrivenString('line_length', '4rem')));
        $arcOutSize = self::toRem(data_get($rekeyPreview, 'arc_sizes.2', data_get($rekeyPreview, 'arc_sizes.out', self::defaultArcSize()))) ?: self::defaultArcSizeRem();
        $arcInSize = self::toRem(data_get($rekeyPreview, 'arc_sizes.1', data_get($rekeyPreview, 'arc_sizes.in', self::defaultArcSize()))) ?: self::defaultArcSizeRem();
        $startLength = self::toRem($rekeyPreview['start_length'] ?? $arcInSize . 'rem');
        $stemLength = self::toRem($rekeyPreview['stem_length'] ?? Defaults::dataDrivenString('stem_length', Defaults::dataDrivenString('line_length', '4rem')));
        $stemContinuation = (array) ($rekeyPreview['stem_continuation'] ?? []);
        $stemContinuationLength = self::stemContinuationLength($stemContinuation, $stemLength);
        $innerX = $direction * $arcOutSize;
        $outerX = $direction * ($arcOutSize + $bridgeLength);
        $bridgeY = $attachY - $arcOutSize;
        $stemEndY = $bridgeY - $arcInSize;
        $startY = $stemEndY - $stemContinuationLength - $stemLength - $startLength;
        $stemX = $outerX + ($direction * $arcInSize);
        $startStemId = $componentId . '.main.path.rekey-source.start-stem';
        $endNodeLabels = self::labelsFromMixed(data_get($rekeyPreview, 'node_labels.' . (5 + count($stemContinuation)), []));
        $endLabelBounds = [
            'xStart' => -0.75,
            'xEnd' => 0.75,
            'yStart' => $attachY - 0.75,
            'yEnd' => $attachY + 0.75,
        ];
        foreach ($endNodeLabels as $label) {
            $endLabelBounds = self::expandBoundsForLabelAt($endLabelBounds, $label, 0.0, $attachY);
        }
        $endLabelDebug = $endNodeLabels === []
            ? []
            : [[
                'type' => 'rekey-source-end-label',
                'id' => ElementIdentifier::normalize($componentId . '.main.path.rekey-source.arc-south-' . ($side === 'left' ? 'east' : 'west') . '-2.end-label.bounds'),
                'renderId' => $componentId . '.main.path.rekey-source.arc-south-' . ($side === 'left' ? 'east' : 'west') . '-2.end-label.bounds',
                'side' => $side,
                ...self::boundsToRem($endLabelBounds),
            ]];

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
            ...$endLabelDebug,
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
        $bridgeLength = self::toRem($rekeyPreview['bridge_length'] ?? Defaults::dataDrivenString('bridge_length', Defaults::dataDrivenString('line_length', '4rem')));
        $arcSize = self::toRem(data_get($rekeyPreview, 'arc_size', data_get($rekeyPreview, 'arc_sizes.1', self::defaultArcSize()))) ?: self::defaultArcSizeRem();
        $stemLength = self::toRem($rekeyPreview['stem_length'] ?? Defaults::dataDrivenString('stem_length', Defaults::dataDrivenString('line_length', '4rem')));
        $stemEntries = self::effectiveStemContinuationEntries((array) ($rekeyPreview['stem_continuation'] ?? []), $stemLength);
        $endLength = self::toRem($rekeyPreview['end_length'] ?? Defaults::dataDrivenString('line_length', '4rem'));
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
                'id' => ElementIdentifier::normalize($componentId . '.label-bounds'),
                'renderId' => $componentId . '.label-bounds',
                'side' => $side,
                ...self::boundsToRem($labelBounds),
            ],
            [
                'type' => 'rekey-target-body',
                'id' => ElementIdentifier::normalize($componentId . '.main.path.rekey-target.body.bounds'),
                'renderId' => $componentId . '.main.path.rekey-target.body.bounds',
                'side' => $side,
                ...self::boundsToRem($bodyBounds),
            ],
            [
                'type' => 'rekey-target-end',
                'id' => ElementIdentifier::normalize($componentId . '.end.path.rekey-target-end.bounds'),
                'renderId' => $componentId . '.end.path.rekey-target-end.bounds',
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
            'id' => ElementIdentifier::normalize($id),
            'renderId' => $id,
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
                'id' => ElementIdentifier::normalize($idPrefix . '.start.bounds'),
                'renderId' => $idPrefix . '.start.bounds',
                'side' => $side,
                'x' => self::rem($startBounds['xStart']),
                'y' => self::rem($startBounds['yStart']),
                'width' => self::rem($startBounds['xEnd'] - $startBounds['xStart']),
                'height' => self::rem($startBounds['yEnd'] - $startBounds['yStart']),
            ],
            [
                'type' => $typePrefix . '-labels',
                'id' => ElementIdentifier::normalize($idPrefix . '.labels.bounds'),
                'renderId' => $idPrefix . '.labels.bounds',
                'side' => $side,
                'x' => self::rem($labelBounds['xStart']),
                'y' => self::rem($labelBounds['yStart']),
                'width' => self::rem($labelBounds['xEnd'] - $labelBounds['xStart']),
                'height' => self::rem($labelBounds['yEnd'] - $labelBounds['yStart']),
            ],
            [
                'type' => $typePrefix . '-tail',
                'id' => ElementIdentifier::normalize($idPrefix . '.tail.bounds'),
                'renderId' => $idPrefix . '.tail.bounds',
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
        $labelBox = self::labelBoxAt($label, $anchorX, $anchorY, $side, $offset);

        return [
            'xStart' => min($bounds['xStart'], $labelBox['xStart']),
            'xEnd' => max($bounds['xEnd'], $labelBox['xEnd']),
            'yStart' => min($bounds['yStart'], $labelBox['yStart']),
            'yEnd' => max($bounds['yEnd'], $labelBox['yEnd']),
        ];
    }

    /**
     * @param  array<string, mixed>  $label
     * @return array{xStart: float, xEnd: float, yStart: float, yEnd: float}
     */
    private static function labelBoxAt(
        array $label,
        float $anchorX,
        float $anchorY,
        ?string $side = null,
        ?float $offset = null,
    ): array {
        $side ??= (string) data_get($label, 'side', 'right');
        $offset ??= Defaults::dataDrivenRem('node_size', '0.95rem') / 2
            + self::toRem(data_get($label, 'connectorLength', Defaults::dataDrivenString('connector_length', '2rem')))
            + self::toRem(data_get($label, 'connectorGap', Defaults::dataDrivenString('connector_gap', '0.25rem')));
        $width = self::textLabelWidth($label);
        $height = self::textLabelHeight($label);

        [$xStart, $xEnd, $yStart, $yEnd] = match ($side) {
            'left' => [$anchorX - $offset - $width, $anchorX - $offset, $anchorY - ($height / 2), $anchorY + ($height / 2)],
            'top' => [$anchorX - ($width / 2), $anchorX + ($width / 2), $anchorY + $offset, $anchorY + $offset + $height],
            'bottom' => [$anchorX - ($width / 2), $anchorX + ($width / 2), $anchorY - $offset - $height, $anchorY - $offset],
            default => [$anchorX + $offset, $anchorX + $offset + $width, $anchorY - ($height / 2), $anchorY + ($height / 2)],
        };

        return [
            'xStart' => $xStart,
            'xEnd' => $xEnd,
            'yStart' => $yStart,
            'yEnd' => $yEnd,
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
            'id' => ElementIdentifier::normalize($id),
            'renderId' => $id,
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
            'id' => ElementIdentifier::normalize($id),
            'renderId' => $id,
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
        $offset ??= Defaults::dataDrivenRem('node_size', '0.95rem') / 2
            + self::toRem(data_get($label, 'connectorLength', Defaults::dataDrivenString('connector_length', '2rem')))
            + self::toRem(data_get($label, 'connectorGap', Defaults::dataDrivenString('connector_gap', '0.25rem')));
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

        if ((bool) data_get($label, 'long', false) || data_get($label, 'width') === 'long') {
            return 24.0 + $labelPadding;
        }

        if ((bool) data_get($label, 'halfLong', false) || in_array(data_get($label, 'width'), ['halfLong', 'half-long', 'half_long'], true)) {
            return 18.0 + $labelPadding;
        }

        if ((bool) data_get($label, 'half', false) || in_array(data_get($label, 'width'), ['half', 'halfWidth', 'half-width', 'half_width'], true)) {
            return 6.0 + $labelPadding;
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
        if (blank($entry)) {
            return self::toRem(self::defaultTrunkPathLength());
        }

        if (is_array($entry)) {
            $rootLineLength = self::defaultTrunkPathLength();

            return self::toRem(data_get($entry, 'length', data_get($entry, 0, $rootLineLength)) ?: $rootLineLength);
        }

        return self::toRem($entry);
    }

    private static function pathEntryWithLength(mixed $entry, string $length): mixed
    {
        if (! is_array($entry)) {
            return $length;
        }

        if (array_key_exists('length', $entry) || ! array_key_exists(0, $entry)) {
            $entry['length'] = $length;

            return $entry;
        }

        $entry[0] = $length;

        return $entry;
    }

    private static function pathEntryWithoutVisibleStem(mixed $entry): array
    {
        $entry = is_array($entry) ? $entry : [];
        $entry['length'] = '0rem';
        $entry['labels'] = false;

        return $entry;
    }

    private static function trunkStartUnlabeledNextStemLength(): string
    {
        return self::rem(self::toRem(self::defaultTrunkPathLength()) * self::trunkStartUnlabeledNextStemFactor());
    }

    private static function trunkStartUnlabeledNextStemFactor(): float
    {
        $value = Defaults::dataDriven(
            'trunk_start_unlabeled_next_stem_factor',
            Defaults::graph('trunk_start_unlabeled_next_stem_factor', 1.0),
        );

        return max(0.0, is_numeric($value) ? (float) $value : 1.0);
    }

    private static function defaultTrunkPathLength(): string
    {
        return Defaults::dataDrivenString(
            'stem_length',
            Defaults::dataDrivenString('line_length', '4rem'),
        );
    }

    private static function trunkStartShiftEnabled(): bool
    {
        return Defaults::dataDrivenBool('trunk_start_shift_enabled', false);
    }

    private static function trunkStartShiftLength(): string
    {
        return Defaults::dataDrivenString(
            'trunk_start_shift_length',
            Defaults::graphString('trunk_start_shift_length', '4rem'),
        );
    }

    private static function defaultArcSize(): string
    {
        return Defaults::dataDrivenString('arc_size', '2.75rem');
    }

    private static function defaultArcSizeRem(): float
    {
        return self::toRem(self::defaultArcSize()) ?: 2.75;
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

    private static function rem(float $value): string
    {
        return rtrim(rtrim(number_format($value, 2, '.', ''), '0'), '.') . 'rem';
    }

    private static function horizontalPaddingForLevel(string $required): string
    {
        return match ($required) {
            'long' => Defaults::dataDrivenString('label_width.long', Defaults::graphString('label_width.long', '20rem')),
            'halfLong' => Defaults::dataDrivenString('label_width.half_long', Defaults::graphString('label_width.half_long', '16rem')),
            'half' => Defaults::dataDrivenString('label_width.half', Defaults::graphString('label_width.half', '6rem')),
            default => Defaults::dataDrivenString('label_width.default', Defaults::graphString('label_width.default', '12rem')),
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

        if ((bool) ($value['long'] ?? false) || ($value['width'] ?? null) === 'long') {
            $level = 'long';
        } elseif ((bool) ($value['halfLong'] ?? false) || in_array($value['width'] ?? null, ['halfLong', 'half-long', 'half_long'], true)) {
            $level = 'halfLong';
        } elseif ((bool) ($value['half'] ?? false) || in_array($value['width'] ?? null, ['half', 'halfWidth', 'half-width', 'half_width'], true)) {
            $level = 'half';
        }

        foreach ($value as $child) {
            $level = self::maxLabelPaddingLevel($level, self::labelPaddingLevel($child));
        }

        return $level;
    }

    private static function maxLabelPaddingLevel(string $current, string $candidate): string
    {
        $rank = [
            'half' => -1,
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
                    'color' => self::color('chunk_event', 'amber'),
                    'badgeColor' => self::color('chunk_event', 'amber'),
                    'halfLong' => self::usesHalfLongChunkEventLabel((string) $eventType),
                ],
                [
                    'side' => 'right',
                    'text' => array_values(array_filter([
                        LabelFormatter::ordinalSampleLine(1),
                        $sampleIdLine,
                    ])),
                    'color' => self::color('chunk_event', 'amber'),
                    'badgeColor' => self::color('chunk_event', 'amber'),
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
