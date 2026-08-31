<?php

declare(strict_types=1);

namespace Gunreip\TranslationWorkbench\Support\TwGraph\DataDriven\TimelineChainGraphData;

use Illuminate\Support\Collection;

final class BranchPreviewBuilder
{
    /**
     * Render ended-after-merge origins as left/right aggregate branch strangs.
     * Each affected finding stays visible as a stem label, but the graph does
     * not explode into one full branch component per row.
     *
     * @param  array<string, mixed>  $mergeOutcomes
     * @param  array<int, array<string, mixed>>  $trunkTimelineAnchors
     * @return array<int, array<string, mixed>>
     */
    public static function previews(array $mergeOutcomes, array $trunkTimelineAnchors = []): array
    {
        $rows = collect($mergeOutcomes['rows'] ?? []);
        $branches = collect();

        foreach (self::outcomeSpecs() as $spec) {
            if (($spec['placement'] ?? null) === 'branch') {
                $branches = $branches->merge(self::group($rows, $spec, $trunkTimelineAnchors));
            }
        }

        foreach (self::outcomeSpecs() as $spec) {
            if (($spec['placement'] ?? null) === 'branch-extension') {
                $branches = self::attachExtensions($branches, $rows, $spec);
            }
        }

        $branches = $branches
            ->values()
            ->map(static function (array $branch, int $index): array {
                $branch['component_counter'] = $index + 1;
                $branch['id'] = 'strang.branch-' . (string) ($branch['side'] ?? 'left') . '.' . $branch['component_counter'];

                return $branch;
            })
            ->all();

        return BranchLabelCollisionResolver::resolve($branches);
    }

    /**
     * Declarative outcome-to-graph mapping. Data-driven graph code consumes
     * these specs and converts them into generic strang/path props.
     *
     * @return array<int, array<string, mixed>>
     */
    private static function outcomeSpecs(): array
    {
        return [
            [
                'outcome_group' => 'ended after merge',
                'placement' => 'branch',
                'path_color' => 'rose',
                'badge_color' => 'red',
                'end_label' => ['Ended after merge', 'shared obsolete'],
                'step_label' => ['Source inactive', 'shared obsolete'],
                'component_counter_offset' => 0,
            ],
            [
                'outcome_group' => 'ended before target',
                'placement' => 'branch',
                'color' => 'rose',
                'end_label' => ['Ended before target', 'not shared obsolete'],
                'step_label' => ['Source inactive', 'not shared obsolete'],
            ],
        ];
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $rows
     * @param  array<string, mixed>  $spec
     * @param  array<int, array<string, mixed>>  $trunkTimelineAnchors
     * @return array<int, array<string, mixed>>
     */
    private static function group(Collection $rows, array $spec, array $trunkTimelineAnchors): array
    {
        $outcomeGroup = (string) ($spec['outcome_group'] ?? '');
        $pathColor = (string) ($spec['path_color'] ?? ($spec['color'] ?? 'red'));
        $badgeColor = (string) ($spec['badge_color'] ?? ($spec['color'] ?? $pathColor));
        $endLabel = array_values((array) ($spec['end_label'] ?? [$outcomeGroup]));
        $stepLabel = array_values((array) ($spec['step_label'] ?? []));
        $componentCounterOffset = (int) ($spec['component_counter_offset'] ?? 0);
        $endedRows = $rows
            ->filter(static fn(array $row): bool => (string) ($row['outcome_group'] ?? '') === $outcomeGroup)
            ->map(static function (array $row) use ($trunkTimelineAnchors): array {
                $row['attach_to'] = self::attachToForRow($row, $trunkTimelineAnchors);
                $row['anchor_y_rem'] = self::anchorYForAttachTo((string) $row['attach_to'], $trunkTimelineAnchors);

                return $row;
            })
            ->values();

        return $endedRows
            ->map(static fn(array $row, int $index): array => [
                'row' => $row,
                'side' => (string) ($row['side'] ?? ($index % 2 === 0 ? 'left' : 'right')),
            ])
            ->groupBy(static fn(array $entry): string => $entry['side'] . '|' . (string) data_get($entry, 'row.attach_to', ''))
            ->values()
            ->map(static function (Collection $sideRows, int $groupIndex) use ($badgeColor, $componentCounterOffset, $endLabel, $outcomeGroup, $pathColor, $stepLabel): array {
                $side = (string) data_get($sideRows->first(), 'side', 'left');
                $labelSide = $side === 'left' ? 'left' : 'right';
                $insideLabelSide = $side === 'left' ? 'right' : 'left';
                $rows = $sideRows->pluck('row')->values();
                $attachTo = (string) ($rows->first()['attach_to'] ?? (
                    $side === 'left'
                    ? 'strang.merge-left.end'
                    : 'strang.merge-right.end'
                ));
                $stemContinuation = $rows
                    ->chunk(2)
                    ->mapWithKeys(static function (Collection $stemRows, int $index) use ($badgeColor, $labelSide, $insideLabelSide): array {
                        $stemRows = $stemRows->values();
                        $entry = [];

                        if ($stemRows->has(0)) {
                            $entry[$labelSide] = [
                                'text' => self::rowLabel((array) $stemRows->get(0)),
                                'badgeColor' => $badgeColor,
                            ];
                        }

                        if ($stemRows->has(1)) {
                            $entry[$insideLabelSide] = [
                                'text' => self::rowLabel((array) $stemRows->get(1)),
                                'badgeColor' => $badgeColor,
                            ];
                        }

                        return [$index + 1 => $entry];
                    })
                    ->all();
                $stemCount = count($stemContinuation);
                $step = [
                    'stepLabel' => [
                        'text' => [
                            ...$stepLabel,
                            $rows->count() . ' rows',
                        ],
                        'badgeColor' => $badgeColor,
                    ],
                ];

                return [
                    'component' => 'tw-graph.strang.branch-' . $side,
                    'side' => $side,
                    'component_counter' => $componentCounterOffset + $groupIndex + 1,
                    'color' => $pathColor,
                    'attach_to' => $attachTo,
                    'anchor_y_rem' => data_get($rows->first(), 'anchor_y_rem'),
                    'step' => $step,
                    'stem_continuation' => $stemContinuation,
                    'branch_extension' => [],
                    'end_counter_start' => 5 + $stemCount + ($step !== null ? 1 : 0),
                    'end_label' => [
                        'text' => [
                            ...$endLabel,
                            $rows->count() . ' rows',
                        ],
                        'side' => 'top',
                        'offset' => '0.75rem',
                        'badgeColor' => $badgeColor,
                    ],
                    'finding_count' => $rows->count(),
                    'node_labels' => [],
                    'source' => [
                        'finding_ids' => $rows
                            ->pluck('finding_id')
                            ->filter()
                            ->values()
                            ->all(),
                        'outcome_group' => $outcomeGroup,
                        'attach_to' => $attachTo,
                    ],
                ];
            })
            ->values()
            ->all();
    }

    /**
     * Attach secondary outcome rows as generic branch-extension entries to an
     * already existing branch strang, without changing lower-level components.
     *
     * @param  Collection<int, array<string, mixed>>  $branches
     * @param  Collection<int, array<string, mixed>>  $rows
     * @param  array<string, mixed>  $spec
     * @return Collection<int, array<string, mixed>>
     */
    private static function attachExtensions(Collection $branches, Collection $rows, array $spec): Collection
    {
        $outcomeGroup = (string) ($spec['outcome_group'] ?? '');
        $parentOutcomeGroup = (string) ($spec['parent_outcome_group'] ?? '');
        $attachTo = (string) ($spec['attach_to'] ?? 'bridge.end');
        $color = (string) ($spec['color'] ?? 'rose');
        $endLabel = array_values((array) ($spec['end_label'] ?? [$outcomeGroup]));
        $stepLabel = array_values((array) ($spec['step_label'] ?? []));
        $endLength = $spec['end_length'] ?? null;
        $capLength = $spec['cap_length'] ?? null;
        $bridgeLength = $spec['bridge_length'] ?? null;
        $stemLength = $spec['stem_length'] ?? null;
        $extensionRows = $rows
            ->filter(static fn(array $row): bool => (string) ($row['outcome_group'] ?? '') === $outcomeGroup)
            ->values();

        if ($extensionRows->isEmpty()) {
            return $branches;
        }

        return $branches->map(static function (array $branch) use ($attachTo, $bridgeLength, $capLength, $color, $endLabel, $endLength, $extensionRows, $parentOutcomeGroup, $stemLength, $stepLabel): array {
            $side = (string) ($branch['side'] ?? 'left');

            if ((string) data_get($branch, 'source.outcome_group') !== $parentOutcomeGroup) {
                return $branch;
            }

            $labelSide = $side === 'left' ? 'left' : 'right';
            $sideRows = $extensionRows
                ->filter(static fn(array $row, int $index): bool => ($index % 2 === 0 ? 'left' : 'right') === $side)
                ->values();

            if ($sideRows->isEmpty()) {
                return $branch;
            }

            $existingExtensions = (array) ($branch['branch_extension'] ?? []);
            $existingExtensions[$attachTo] = [
                ...((array) ($existingExtensions[$attachTo] ?? [])),
                ...$sideRows
                    ->mapWithKeys(static fn(array $row, int $index): array => [
                        $index + 1 => [
                            'color' => $color,
                            'step' => [
                                'stepLabel' => [
                                    'text' => [
                                        ...$stepLabel,
                                        '1 row',
                                    ],
                                    'badgeColor' => $color,
                                ],
                            ],
                            ...($endLength !== null ? ['endLength' => (string) $endLength] : []),
                            ...($capLength !== null ? ['capLength' => (string) $capLength] : []),
                            'endLabel' => [
                                'text' => [
                                    ...$endLabel,
                                    '1 row',
                                ],
                                'badgeColor' => $color,
                            ],
                            'nodeLabels' => [
                                3 => [
                                    [
                                        'text' => self::rowLabel($row),
                                        'side' => $labelSide,
                                        'badgeColor' => $color,
                                    ],
                                ],
                            ],
                            ...($bridgeLength !== null ? ['bridgeLength' => (string) $bridgeLength] : []),
                            ...($stemLength !== null ? ['stemLength' => (string) $stemLength] : []),
                        ],
                    ])
                    ->all(),
            ];
            $branch['branch_extension'] = $existingExtensions;

            return $branch;
        });
    }

    /**
     * @param  array<string, mixed>  $row
     * @return array<int, string>
     */
    private static function rowLabel(array $row): array
    {
        $findingId = $row['finding_id'] ?? null;
        $originKeyLabel = LabelFormatter::graphKeyLabelText((string) ($row['origin_key'] ?? ''), 52);
        $timestamp = LabelFormatter::graphTimestampLabel($row['last_seen_at'] ?? $row['first_seen_at'] ?? null);

        return array_values(array_filter([
            'finding ID #' . (string) ($findingId ?: '?'),
            $originKeyLabel,
            $timestamp,
        ]));
    }

    /**
     * Branches attach to the latest visible trunk timeline anchor that has
     * already happened. This keeps the graph chronological and avoids connecting
     * a branch to a future trunk state just because it is visually closer.
     *
     * @param  array<string, mixed>  $row
     * @param  array<int, array<string, mixed>>  $trunkTimelineAnchors
     */
    private static function attachToForRow(array $row, array $trunkTimelineAnchors): string
    {
        $fallback = (string) ($row['side'] ?? 'left') === 'right'
            ? 'strang.merge-right.end'
            : 'strang.merge-left.end';
        $rowTimestamp = self::timestampValue($row['last_seen_at_raw'] ?? $row['last_seen_at'] ?? $row['first_seen_at_raw'] ?? $row['first_seen_at'] ?? null);

        if ($rowTimestamp === null) {
            return $fallback;
        }

        $anchors = collect($trunkTimelineAnchors)
            ->filter(static fn(array $anchor): bool => filled($anchor['anchor'] ?? null))
            ->map(static fn(array $anchor): array => [
                ...$anchor,
                '_timestamp_value' => self::timestampValue($anchor['timestamp'] ?? null),
            ])
            ->filter(static fn(array $anchor): bool => ($anchor['_timestamp_value'] ?? null) !== null);
        $anchor = null;

        if ((string) ($row['outcome_group'] ?? '') === 'ended before target') {
            $anchor = $anchors
                ->filter(static fn(array $anchor): bool => (string) ($anchor['event'] ?? '') === 'translation_key_updated')
                ->filter(static fn(array $anchor): bool => (int) $anchor['_timestamp_value'] <= $rowTimestamp)
                ->sortByDesc('_timestamp_value')
                ->first();
        }

        $anchor ??= $anchors
            ->filter(static fn(array $anchor): bool => (int) $anchor['_timestamp_value'] <= $rowTimestamp)
            ->sortByDesc('_timestamp_value')
            ->first();

        return is_array($anchor)
            ? (string) ($anchor['anchor'] ?? $fallback)
            : $fallback;
    }

    /**
     * @param  array<int, array<string, mixed>>  $trunkTimelineAnchors
     */
    private static function anchorYForAttachTo(string $attachTo, array $trunkTimelineAnchors): ?float
    {
        $anchor = collect($trunkTimelineAnchors)
            ->first(static fn(array $anchor): bool => (string) ($anchor['anchor'] ?? '') === $attachTo);

        return is_array($anchor) && is_numeric($anchor['y_rem'] ?? null)
            ? (float) $anchor['y_rem']
            : null;
    }

    private static function timestampValue(mixed $timestamp): ?int
    {
        if (blank($timestamp)) {
            return null;
        }

        $value = strtotime((string) $timestamp);

        return $value === false ? null : $value;
    }
}
