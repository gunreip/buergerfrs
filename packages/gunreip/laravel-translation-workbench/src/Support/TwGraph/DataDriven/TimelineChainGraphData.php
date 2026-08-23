<?php

declare(strict_types=1);

namespace Gunreip\TranslationWorkbench\Support\TwGraph\DataDriven;

use Illuminate\Support\Collection;

final class TimelineChainGraphData
{
    /**
     * Build the first data-driven graph intent from an aggregated timeline-chain row.
     *
     * This is deliberately not a renderer. The result describes which graph strangs
     * should exist and which source rows motivated them; coordinates stay in the
     * component/rendering layer.
     *
     * @param  array<string, mixed>|null  $mainRow
     * @param  Collection<int, array<string, mixed>>|array<int, array<string, mixed>>  $rootRows
     * @param  Collection<int, array<string, mixed>>|array<int, array<string, mixed>>  $originRows
     * @return array<string, mixed>
     */
    public static function fromTimelineChain(
        ?array $mainRow,
        Collection|array $rootRows,
        Collection|array $originRows,
    ): array {
        if ($mainRow === null) {
            return [
                'state' => 'empty',
                'meta' => [
                    'reason' => 'No timeline-chain main row is available.',
                ],
                'strangs' => [],
            ];
        }

        $roots = collect($rootRows)->values();
        $origins = collect($originRows)->values();
        $translationKey = (string) ($mainRow['translation_key'] ?? '');
        $graphId = 'timeline-chain-' . (int) ($mainRow['id'] ?? 0);

        return [
            'state' => 'ready',
            'meta' => [
                'graph_id' => $graphId,
                'source' => 'translation_workbench_timeline_chains',
                'chain_id' => (int) ($mainRow['id'] ?? 0),
                'chain_type' => (string) ($mainRow['chain_type'] ?? 'single'),
                'chain_status' => (string) ($mainRow['chain_status'] ?? 'inactive'),
                'translation_key' => $translationKey,
            ],
            'facts' => [
                'key_ids' => self::integerList($mainRow['key_ids'] ?? []),
                'finding_ids' => self::integerList($mainRow['finding_ids'] ?? []),
                'review_ids' => self::integerList($mainRow['review_ids'] ?? []),
                'timeline_event_ids' => self::integerList($mainRow['timeline_event_ids'] ?? []),
                'lang_value_ids' => self::integerList($mainRow['lang_value_ids'] ?? []),
                'related_translation_keys' => collect($mainRow['related_translation_keys'] ?? [])
                    ->map(static fn(mixed $key): string => trim((string) $key))
                    ->filter()
                    ->values()
                    ->all(),
            ],
            'strangs' => [
                'trunk' => self::trunk($mainRow, $roots),
                'merge' => self::merge($origins),
                'branch' => self::branch($roots),
            ],
            'component_intent' => self::componentIntent($mainRow, $roots, $origins),
            'render_preview' => self::renderPreview($mainRow, $roots, $origins),
        ];
    }

    /**
     * @param  array<string, mixed>  $mainRow
     * @param  Collection<int, array<string, mixed>>  $rootRows
     * @return array<string, mixed>
     */
    private static function trunk(array $mainRow, Collection $rootRows): array
    {
        return [
            'component' => 'tw-graph.strang.trunk',
            'role' => 'canonical continuation',
            'key' => (string) ($mainRow['translation_key'] ?? ''),
            'root_key_id' => $mainRow['root_key_id'] ?? null,
            'root_finding_id' => $mainRow['root_finding_id'] ?? null,
            'event_count' => $rootRows->count(),
            'events' => $rootRows
                ->map(static fn(array $row): array => [
                    'timestamp' => $row['timestamp'] ?? null,
                    'branch' => (string) ($row['branch'] ?? ''),
                    'event' => (string) ($row['event'] ?? ''),
                    'state' => (string) ($row['state'] ?? ''),
                    'color' => (string) ($row['color'] ?? 'zinc'),
                ])
                ->values()
                ->all(),
        ];
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $originRows
     * @return array<string, mixed>
     */
    private static function merge(Collection $originRows): array
    {
        return [
            'component_family' => 'tw-graph.strang.merge-*',
            'role' => 'origin strands folded into the canonical key',
            'count' => $originRows->count(),
            'strangs' => $originRows
                ->map(static function (array $row, int $index): array {
                    $side = $index % 2 === 0 ? 'left' : 'right';

                    return [
                        'component' => 'tw-graph.strang.merge-' . $side,
                        'side' => $side,
                        'source_root' => (string) ($row['first_root'] ?? ''),
                        'target_trunk' => (string) ($row['trunk'] ?? ''),
                        'origin_key' => (string) ($row['first_origin_key'] ?? ''),
                        'context' => (string) ($row['context'] ?? ''),
                        'first' => [
                            'timestamp' => $row['first_timestamp'] ?? null,
                            'event' => (string) ($row['first_event'] ?? ''),
                            'state' => (string) ($row['first_state'] ?? ''),
                        ],
                        'last' => [
                            'timestamp' => $row['last_timestamp'] ?? null,
                            'event' => (string) ($row['last_event'] ?? ''),
                            'state' => (string) ($row['last_state'] ?? ''),
                        ],
                    ];
                })
                ->values()
                ->all(),
        ];
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $rootRows
     * @return array<string, mixed>
     */
    private static function branch(Collection $rootRows): array
    {
        $nonTrunkRows = $rootRows
            ->filter(static fn(array $row): bool => ! in_array((string) ($row['branch'] ?? ''), ['Root', 'Root key'], true))
            ->values();

        return [
            'component_family' => 'tw-graph.strang.branch-*',
            'role' => 'timeline side events attached to the canonical key',
            'count' => $nonTrunkRows->count(),
            'strangs' => $nonTrunkRows
                ->map(static function (array $row, int $index): array {
                    $side = $index % 2 === 0 ? 'left' : 'right';

                    return [
                        'component' => 'tw-graph.strang.branch-' . $side,
                        'side' => $side,
                        'branch' => (string) ($row['branch'] ?? ''),
                        'translation_key' => (string) ($row['translation_key'] ?? ''),
                        'timestamp' => $row['timestamp'] ?? null,
                        'event' => (string) ($row['event'] ?? ''),
                        'state' => (string) ($row['state'] ?? ''),
                        'color' => (string) ($row['branch_color'] ?? $row['color'] ?? 'zinc'),
                    ];
                })
                ->values()
                ->all(),
        ];
    }

    /**
     * @param  array<string, mixed>  $mainRow
     * @param  Collection<int, array<string, mixed>>  $rootRows
     * @param  Collection<int, array<string, mixed>>  $originRows
     * @return array<int, array<string, mixed>>
     */
    private static function componentIntent(array $mainRow, Collection $rootRows, Collection $originRows): array
    {
        return [
            [
                'component' => 'tw-graph.strang.trunk',
                'from' => 'timelineChainMainRow + timelineChainRootRows',
                'required' => true,
                'suggested_props' => [
                    'graph-id' => 'timeline-chain-' . (int) ($mainRow['id'] ?? 0),
                    'path-count' => max(3, $rootRows->count()),
                    'start-label' => 'key #' . (string) ($mainRow['root_key_id'] ?? '?'),
                    'end-label' => (string) ($mainRow['translation_key'] ?? ''),
                ],
            ],
            [
                'component' => 'tw-graph.strang.merge-left/right',
                'from' => 'timelineChainOriginRows',
                'required' => $originRows->isNotEmpty(),
                'count' => $originRows->count(),
            ],
            [
                'component' => 'tw-graph.strang.branch-left/right',
                'from' => 'timelineChainRootRows',
                'required' => $rootRows->isNotEmpty(),
                'count' => $rootRows->count(),
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $mainRow
     * @param  Collection<int, array<string, mixed>>  $rootRows
     * @param  Collection<int, array<string, mixed>>  $originRows
     * @return array<string, mixed>
     */
    private static function renderPreview(array $mainRow, Collection $rootRows, Collection $originRows): array
    {
        $maxEventLabels = 6;
        $eventRows = $rootRows
            ->filter(static fn(array $row): bool => filled($row['timestamp'] ?? null) || filled($row['event'] ?? null))
            ->sortBy(static fn(array $row): string => (string) ($row['timestamp'] ?? ''))
            ->values();
        $pathCount = min(12, max(4, $eventRows->count() + 1));
        $nodeLabels = $eventRows
            ->take($maxEventLabels)
            ->mapWithKeys(static function (array $row, int $index): array {
                $nodeIndex = $index + 2;
                $side = $index % 2 === 0 ? 'left' : 'right';
                $event = trim((string) ($row['event'] ?? ''));
                $state = trim((string) ($row['state'] ?? ''));

                return [
                    $nodeIndex => [
                        $side => array_values(array_filter([$event, $state])),
                    ],
                ];
            })
            ->all();
        $maxMergeStrangs = 5;
        $mergePreviews = self::mergePreviewStrangs($originRows, $maxMergeStrangs);
        $mergePreviewCount = count($mergePreviews);
        $previewMode = $mergePreviewCount > 0 ? 'trunk_with_limited_merge' : 'trunk_only';
        $renderedMergeCandidates = collect($mergePreviews)
            ->sum(static fn(array $preview): int => 1 + (int) ($preview['extension_count'] ?? 0));

        return [
            'mode' => $previewMode,
            'reason' => $mergePreviewCount > 0
                ? 'Second visual pass: render the canonical trunk and up to five origin merge candidates as side strangs with extensions.'
                : 'First visual pass: render only the canonical trunk before enabling data-driven merge and branch strangs.',
            'limits' => [
                'max_event_labels' => $maxEventLabels,
                'rendered_event_labels' => count($nodeLabels),
                'available_events' => $eventRows->count(),
                'max_merge_candidates' => $maxMergeStrangs,
                'rendered_merge_candidates' => $renderedMergeCandidates,
                'rendered_merge_strangs' => $mergePreviewCount,
                'available_merge_strangs' => $originRows->count(),
            ],
            'graph' => [
                'graph_id' => 'timeline-chain-' . (int) ($mainRow['id'] ?? 0) . '-data-preview',
                'color' => 'cyan',
                'line_length' => '3.5rem',
                'slot_min_height' => max($mergePreviewCount > 0 ? 42 : 34, ($pathCount + 3) * 4) . 'rem',
            ],
            'trunk' => [
                'component' => 'tw-graph.strang.trunk',
                'color' => 'green',
                'path_count' => $pathCount,
                'path_lengths' => collect(range(1, min(10, $pathCount)))
                    ->mapWithKeys(static fn(int $pathNumber): array => [$pathNumber => '5.5rem'])
                    ->all(),
                'start_label' => [
                    'text' => array_values(array_filter([
                        'key #' . (string) ($mainRow['root_key_id'] ?? '?'),
                        (string) ($mainRow['chain_type'] ?? ''),
                    ])),
                ],
                'end_label' => [
                    'text' => array_values(array_filter([
                        (string) ($mainRow['translation_key'] ?? ''),
                        (string) ($mainRow['chain_status'] ?? ''),
                    ])),
                ],
                'node_labels' => $nodeLabels,
            ],
            'merge' => $mergePreviews[0] ?? null,
            'merges' => $mergePreviews,
        ];
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $originRows
     * @return array<int, array<string, mixed>>
     */
    private static function mergePreviewStrangs(Collection $originRows, int $maxMergeCandidates): array
    {
        return $originRows
            ->take($maxMergeCandidates)
            ->values()
            ->map(static fn(array $row, int $index): array => [
                'row' => $row,
                'index' => $index,
                'side' => $index % 2 === 0 ? 'left' : 'right',
            ])
            ->groupBy('side')
            ->map(static function (Collection $sideRows, string $side): array {
                $main = $sideRows->first();
                $mainPreview = self::mergePreview($main['row'], (int) $main['index'], $side);
                $extensions = $sideRows
                    ->skip(1)
                    ->values();

                $mainPreview['extension_count'] = $extensions->count();
                $mainPreview['extension_node_labels'] = $extensions
                    ->mapWithKeys(static function (array $extension, int $extensionOffset) use ($side): array {
                        $extensionIndex = $extensionOffset + 1;
                        $row = $extension['row'];
                        $labelSide = $side === 'left' ? 'left' : 'right';
                        $firstRoot = trim((string) ($row['first_root'] ?? ''));
                        $firstRootLabel = preg_match('/#\d+/', $firstRoot, $matches) === 1
                            ? 'finding ID ' . $matches[0]
                            : ($firstRoot !== '' ? $firstRoot : 'finding ID ?');
                        $firstOriginKey = trim((string) ($row['first_origin_key'] ?? ''));

                        return [
                            $extensionIndex => [
                                1 => [
                                    $labelSide => array_values(array_filter([
                                        $firstRootLabel,
                                        '@start',
                                    ])),
                                ],
                                4 => [
                                    $labelSide => array_values(array_filter([
                                        'Origin key',
                                        $firstOriginKey,
                                    ])),
                                ],
                            ],
                        ];
                    })
                    ->all();

                return $mainPreview;
            })
            ->values()
            ->all();
    }

    /**
     * @param  array<string, mixed>  $row
     * @return array<string, mixed>
     */
    private static function mergePreview(array $row, int $index, ?string $side = null): array
    {
        $side ??= $index % 2 === 0 ? 'left' : 'right';
        $labelSide = $side === 'left' ? 'left' : 'right';
        $firstRoot = trim((string) ($row['first_root'] ?? ''));
        $firstRootLabel = preg_match('/#\d+/', $firstRoot, $matches) === 1
            ? 'finding ID ' . $matches[0]
            : ($firstRoot !== '' ? $firstRoot : 'finding ID ?');
        $firstOriginKey = trim((string) ($row['first_origin_key'] ?? ''));

        return [
            'component' => 'tw-graph.strang.merge-' . $side,
            'side' => $side,
            'color' => 'amber',
            'attach_to' => 'strang.trunk.path.1.end',
            'bridge_length' => '6rem',
            'stem_length' => '5rem',
            'start_label' => [
                'text' => [$firstRootLabel, '@start'],
                'side' => 'bottom',
                'offset' => '0.75rem',
                'badgeColor' => 'amber',
            ],
            'node_labels' => [
                1 => [
                    $labelSide => array_values(array_filter([
                        'Origin key',
                        $firstOriginKey,
                    ])),
                ],
                5 => [
                    $labelSide => array_values(array_filter([
                        (string) ($row['last_event'] ?? ''),
                        (string) ($row['last_state'] ?? ''),
                    ])),
                ],
            ],
            'source' => [
                'context' => (string) ($row['context'] ?? ''),
                'first_timestamp' => $row['first_timestamp'] ?? null,
                'last_timestamp' => $row['last_timestamp'] ?? null,
            ],
        ];
    }

    /**
     * @return array<int, int>
     */
    private static function integerList(mixed $value): array
    {
        return collect(is_array($value) ? $value : [])
            ->map(static fn(mixed $id): int => (int) $id)
            ->filter(static fn(int $id): bool => $id > 0)
            ->unique()
            ->values()
            ->all();
    }
}
