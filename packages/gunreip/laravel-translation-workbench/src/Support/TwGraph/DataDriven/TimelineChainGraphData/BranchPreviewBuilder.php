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
     * @return array<int, array<string, mixed>>
     */
    public static function previews(array $mergeOutcomes): array
    {
        $rows = collect($mergeOutcomes['rows'] ?? []);
        $branches = collect();

        foreach (self::outcomeSpecs() as $spec) {
            if (($spec['placement'] ?? null) === 'branch') {
                $branches = $branches->merge(self::group($rows, $spec));
            }
        }

        foreach (self::outcomeSpecs() as $spec) {
            if (($spec['placement'] ?? null) === 'branch-extension') {
                $branches = self::attachExtensions($branches, $rows, $spec);
            }
        }

        return $branches->values()->all();
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
                'color' => 'red',
                'end_label' => ['Ended after merge', 'shared obsolete'],
                'step_label' => ['Source inactive', 'shared obsolete'],
                'component_counter_offset' => 0,
            ],
            [
                'outcome_group' => 'ended before target',
                'placement' => 'branch-extension',
                'parent_outcome_group' => 'ended after merge',
                'attach_to' => 'bridge.end',
                'color' => 'rose',
                'end_label' => ['Ended before target', 'not shared obsolete'],
                'step_label' => ['Source inactive', 'not shared obsolete'],
                'end_length' => '4rem',
                'cap_length' => '2rem',
                'bridge_length' => '28rem',
                'stem_length' => '5.25rem',
            ],
        ];
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $rows
     * @param  array<string, mixed>  $spec
     * @return array<int, array<string, mixed>>
     */
    private static function group(Collection $rows, array $spec): array
    {
        $outcomeGroup = (string) ($spec['outcome_group'] ?? '');
        $color = (string) ($spec['color'] ?? 'red');
        $endLabel = array_values((array) ($spec['end_label'] ?? [$outcomeGroup]));
        $stepLabel = array_values((array) ($spec['step_label'] ?? []));
        $componentCounterOffset = (int) ($spec['component_counter_offset'] ?? 0);
        $endedRows = $rows
            ->filter(static fn(array $row): bool => (string) ($row['outcome_group'] ?? '') === $outcomeGroup)
            ->values();

        return $endedRows
            ->map(static fn(array $row, int $index): array => [
                'row' => $row,
                'side' => $index % 2 === 0 ? 'left' : 'right',
            ])
            ->groupBy('side')
            ->map(static function (Collection $sideRows, string $side) use ($color, $componentCounterOffset, $endLabel, $outcomeGroup, $stepLabel): array {
                $labelSide = $side === 'left' ? 'left' : 'right';
                $insideLabelSide = $side === 'left' ? 'right' : 'left';
                $rows = $sideRows->pluck('row')->values();
                $stemContinuation = $rows
                    ->chunk(2)
                    ->mapWithKeys(static function (Collection $stemRows, int $index) use ($labelSide, $insideLabelSide): array {
                        $stemRows = $stemRows->values();
                        $entry = [
                            'length' => '5.25rem',
                        ];

                        if ($stemRows->has(0)) {
                            $entry[$labelSide] = self::rowLabel((array) $stemRows->get(0));
                        }

                        if ($stemRows->has(1)) {
                            $entry[$insideLabelSide] = self::rowLabel((array) $stemRows->get(1));
                        }

                        return [$index + 1 => $entry];
                    })
                    ->all();
                $stemCount = count($stemContinuation);
                $step = [
                    'beforeLength' => '1.5rem',
                    'afterLength' => '1.5rem',
                    'stepLabel' => [
                        'text' => [
                            ...$stepLabel,
                            $rows->count() . ' rows',
                        ],
                        'badgeColor' => $color,
                    ],
                ];

                return [
                    'component' => 'tw-graph.strang.branch-' . $side,
                    'side' => $side,
                    'component_counter' => $componentCounterOffset + ($side === 'left' ? 1 : 2),
                    'color' => $color,
                    'attach_to' => $side === 'left'
                        ? 'strang.merge-left.end'
                        : 'strang.merge-right.end',
                    'bridge_length' => '30rem',
                    'stem_length' => '5.25rem',
                    'step' => $step,
                    'stem_continuation' => $stemContinuation,
                    'branch_extension' => [],
                    'end_length' => '4rem',
                    'end_cap_length' => '2rem',
                    'end_counter_start' => 5 + $stemCount + ($step !== null ? 1 : 0),
                    'end_label' => [
                        'text' => [
                            ...$endLabel,
                            $rows->count() . ' rows',
                        ],
                        'side' => 'top',
                        'offset' => '0.75rem',
                        'badgeColor' => $color,
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
        $endLength = (string) ($spec['end_length'] ?? '0rem');
        $capLength = (string) ($spec['cap_length'] ?? '1.75rem');
        $bridgeLength = (string) ($spec['bridge_length'] ?? '12rem');
        $stemLength = (string) ($spec['stem_length'] ?? '5.25rem');
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
                            'bridgeLength' => $bridgeLength,
                            'stemLength' => $stemLength,
                            'color' => $color,
                            'step' => [
                                'beforeLength' => '1.5rem',
                                'afterLength' => '1.5rem',
                                'stepLabel' => [
                                    'text' => [
                                        ...$stepLabel,
                                        '1 row',
                                    ],
                                    'badgeColor' => $color,
                                ],
                            ],
                            'endLength' => $endLength,
                            'capLength' => $capLength,
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
}
