<?php

declare(strict_types=1);

namespace Gunreip\TranslationWorkbench\Support\TwGraph\DataDriven\TimelineChainGraphData;

use Illuminate\Support\Collection;

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
        $nodeLabels = [];
        $nodeIndex = 2;

        $eventLabelRows
            ->groupBy('_timestamp_group')
            ->each(static function (Collection $timestampRows) use (&$nodeLabels, &$nodeIndex): void {
                $timestampRows
                    ->values()
                    ->chunk(2)
                    ->each(static function (Collection $chunk) use (&$nodeLabels, &$nodeIndex): void {
                        $labels = [];

                        foreach ($chunk->values() as $labelIndex => $row) {
                            $side = $labelIndex === 0 ? 'left' : 'right';
                            $event = trim((string) ($row['event'] ?? ''));
                            $state = trim((string) ($row['state'] ?? ''));
                            $timestamp = (string) ($row['_timestamp_label'] ?? '');
                            $stateLine = trim($timestamp . ' · ' . $state, ' ·');

                            $labels[$side] = array_values(array_filter([
                                $event,
                                $stateLine,
                            ]));
                        }

                        if ($labels !== []) {
                            $nodeLabels[$nodeIndex] = $labels;
                            $nodeIndex++;
                        }
                    });
            });
        $lastLabelNodeIndex = $nodeLabels !== [] ? max(array_keys($nodeLabels)) : 1;
        $langValueLabels = LangValueLabels::active($mainRow);
        $mergePreviewHeadCandidates = 6;
        $mergePreviews = MergePreviewBuilder::previews($originRows, $mergePreviewHeadCandidates);
        $branchPreviews = BranchPreviewBuilder::previews($mergeOutcomes);
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
        $pathCount = min(7, max($hasRekeyTargetPreview ? 7 : 4, $lastLabelNodeIndex));
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

        return [
            'mode' => $previewMode,
            'reason' => $mergePreviewCount > 0
                ? 'Second visual pass: render the canonical trunk, limited origin merge candidates and per-finding ended branches.'
                : 'First visual pass: render only the canonical trunk before enabling data-driven merge and branch strangs.',
            'limits' => [
                'max_event_labels' => $maxEventLabels,
                'rendered_event_labels' => count($nodeLabels),
                'available_events' => $eventRows->count(),
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
                'slot_min_height' => max(
                    $mergePreviewCount > 0 ? 42 : 34,
                    ($pathCount + 3) * 4,
                    42 + (int) ceil($branchPreviewCount / 2) * 2,
                ) . 'rem',
            ],
            'trunk' => [
                'component' => 'tw-graph.strang.trunk',
                'color' => 'green',
                'path_count' => $pathCount,
                'path_lengths' => collect(range(1, $pathCount))
                    ->mapWithKeys(static function (int $pathNumber) use ($rekeyTargetTrunkLabel): array {
                        $length = $pathNumber === 1
                            ? '24.5rem'
                            : ($pathNumber === 2 ? '7.5rem' : '5.5rem');

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
            ],
            'merge' => $mergePreviews[0] ?? null,
            'merges' => $mergePreviews,
            'rekeys' => $rekeyPreviews,
            'branches' => $branchPreviews,
        ];
    }
}
