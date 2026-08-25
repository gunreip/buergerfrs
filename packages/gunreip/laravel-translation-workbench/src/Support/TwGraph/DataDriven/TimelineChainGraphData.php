<?php

declare(strict_types=1);

namespace Gunreip\TranslationWorkbench\Support\TwGraph\DataDriven;

use App\Settings\AppGeneralSettings;
use App\Support\Locale\LocaleCode;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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
        $mergeOutcomes = self::mergeOutcomes($mainRow, $origins);

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
            'render_preview' => self::renderPreview($mainRow, $roots, $origins, $mergeOutcomes),
            'merge_outcomes' => $mergeOutcomes,
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
    private static function renderPreview(
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
        $pathCount = min(7, max(4, $eventRows->count() + 1));
        $nodeLabels = $eventRows
            ->take($maxEventLabels)
            ->mapWithKeys(static function (array $row, int $index): array {
                $nodeIndex = $index + 2;
                $side = $index % 2 === 0 ? 'left' : 'right';
                $event = trim((string) ($row['event'] ?? ''));
                $state = trim((string) ($row['state'] ?? ''));
                $timestamp = self::graphTimestampLabel($row['timestamp'] ?? null);
                $stateLine = trim($timestamp . ' · ' . $state, ' ·');

                if ($nodeIndex === 2 && $stateLine !== '') {
                    $stateLine .= ' Dings';
                }

                return [
                    $nodeIndex => [
                        $side => array_values(array_filter([
                            $event,
                            $stateLine,
                        ])),
                    ],
                ];
            })
            ->all();
        $langValueLabels = self::activeLangValueLabels($mainRow);
        $mergePreviewHeadCandidates = 6;
        $mergePreviews = self::mergePreviewStrangs($originRows, $mergePreviewHeadCandidates);
        $branchPreviews = self::branchPreviewStrangs($mergeOutcomes);
        $mergePreviewCount = count($mergePreviews);
        $branchPreviewCount = collect($branchPreviews)->sum(static fn(array $preview): int => (int) ($preview['finding_count'] ?? 0));
        $previewMode = $mergePreviewCount > 0 ? 'trunk_with_limited_merge' : 'trunk_only';
        $renderedMergeCandidates = collect($mergePreviews)
            ->sum(static fn(array $preview): int => 1 + (int) ($preview['extension_count'] ?? 0));
        $trunkStartTimestamp = self::graphTimestampLabel(
            data_get($eventRows->first(), 'timestamp')
                ?? ($mainRow['first_seen_at'] ?? null)
                ?? ($mainRow['created_at'] ?? null)
                ?? ($mainRow['updated_at'] ?? null),
        );
        $mergeOutcomeSummary = collect($mergeOutcomes['summary'] ?? []);
        $mergeOriginCountLabel = self::mergeOriginCountLabel($mergeOutcomeSummary);
        $mergeOutcomeLine = self::mergeOutcomeResultLine($mergeOutcomeSummary);

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
            ],
            'graph' => [
                'graph_id' => 'timeline-chain-' . (int) ($mainRow['id'] ?? 0) . '-data-preview',
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
                    ->mapWithKeys(static fn(int $pathNumber): array => [
                        $pathNumber => $pathNumber === 1
                            ? '24.5rem'
                            : '5.5rem',
                    ])
                    ->all(),
                'end_length' => '6rem',
                'start_label' => [
                    'text' => array_values(array_filter([
                        'key ID #' . (string) ($mainRow['root_key_id'] ?? '?'),
                        trim($trunkStartTimestamp . ' · ' . $mergeOriginCountLabel, ' ·'),
                    ])),
                ],
                'end_label' => [
                    'text' => array_values(array_filter([
                        'timeline chain ID #' . (string) ($mainRow['id'] ?? '?'),
                        $mergeOutcomeLine,
                    ])),
                ],
                'start_node_labels' => $langValueLabels,
                'node_labels' => $nodeLabels,
            ],
            'merge' => $mergePreviews[0] ?? null,
            'merges' => $mergePreviews,
            'branches' => $branchPreviews,
        ];
    }

    /**
     * Return only the graph-relevant lang values: source locale plus current UI
     * target main locale. Other locales stay out of the graph until tooltip work.
     *
     * @param  array<string, mixed>  $mainRow
     * @return array{left?: array<int, string>, right?: array<int, string>}
     */
    private static function activeLangValueLabels(array $mainRow): array
    {
        $langValueIds = self::integerList($mainRow['lang_value_ids'] ?? []);

        if ($langValueIds === [] || ! Schema::hasTable('translation_workbench_lang_values')) {
            return [];
        }

        $sourceLocale = LocaleCode::normalize((string) config('translation-workbench.source_locale', 'en')) ?: 'en';
        $targetLocale = self::activeTargetMainLocale();
        $rows = DB::table('translation_workbench_lang_values')
            ->whereIn('id', $langValueIds)
            ->where('status', 'active')
            ->whereIn('locale', array_values(array_unique(array_filter([$sourceLocale, $targetLocale]))))
            ->get(['id', 'locale', 'locale_role', 'value', 'last_seen_at', 'updated_at', 'created_at']);

        $source = $rows->first(static function (object $row) use ($sourceLocale): bool {
            return (string) $row->locale === $sourceLocale
                || (string) $row->locale_role === 'source_main';
        });
        $target = $rows->first(static function (object $row) use ($targetLocale, $sourceLocale): bool {
            return (string) $row->locale === $targetLocale
                && (string) $row->locale !== $sourceLocale;
        }) ?? $rows->first(static fn(object $row): bool => (string) $row->locale_role === 'target_main');
        $labels = [];

        if ($source !== null) {
            $labels['left'] = [
                'source lang value ID #' . (string) $source->id,
                self::langValueTimestampLine($source),
            ];
        }

        if ($target !== null) {
            $labels['right'] = [
                'target lang value ID #' . (string) $target->id,
                self::langValueTimestampLine($target),
            ];
        }

        return $labels;
    }

    private static function activeTargetMainLocale(): string
    {
        $configuredLocale = '';

        if (class_exists(AppGeneralSettings::class)) {
            $configuredLocale = LocaleCode::normalize((string) (app(AppGeneralSettings::class)->locale ?? ''));
        }

        $configuredLocale = $configuredLocale !== ''
            ? $configuredLocale
            : LocaleCode::normalize((string) app()->getLocale());
        $activeLanguage = (string) (LocaleCode::parts($configuredLocale)['language'] ?? $configuredLocale);

        return $activeLanguage !== '' ? $activeLanguage : $configuredLocale;
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $originRows
     * @return array<int, array<string, mixed>>
     */
    private static function mergePreviewStrangs(Collection $originRows, int $maxMergeCandidates): array
    {
        return $originRows
            ->values()
            ->map(static fn(array $row, int $index): array => [
                'row' => $row,
                'index' => $index,
                'side' => $index % 2 === 0 ? 'left' : 'right',
            ])
            ->groupBy('side')
            ->map(static function (Collection $sideRows, string $side) use ($maxMergeCandidates): array {
                $main = $sideRows->first();
                $mainPreview = self::mergePreview($main['row'], (int) $main['index'], $side);
                $extensions = self::mergePreviewExtensionRows($sideRows, max(0, intdiv($maxMergeCandidates, 2) - 1));

                $mainPreview['extension_count'] = $extensions->count();
                $mainPreview['extension_stem_lengths'] = $extensions->isNotEmpty()
                    ? $extensions
                    ->mapWithKeys(static function (array $extension, int $extensionOffset) use ($side): array {
                        $extensionIndex = $extensionOffset + 1;

                        if ($side !== 'left' || $extensionIndex !== 3 || ($extension['type'] ?? null) !== 'aggregate') {
                            return [];
                        }

                        return [$extensionIndex => '3.5rem'];
                    })
                    ->all()
                    : [];
                $mainPreview['extension_bridge_continuations'] = $extensions->isNotEmpty()
                    ? $extensions
                    ->mapWithKeys(static fn(array $extension, int $extensionOffset): array => [
                        $extensionOffset + 1 => (string) ($extension['bridge_length'] ?? '19rem'),
                    ])
                    ->all()
                    : [];
                $mainPreview['extension_stem_continuations'] = $extensions->isNotEmpty()
                    ? $extensions
                    ->mapWithKeys(static fn(array $extension, int $extensionOffset): array => [
                        $extensionOffset + 1 => $extension['stem_continuation'] ?? [],
                    ])
                    ->filter(static fn(array $continuation): bool => $continuation !== [])
                    ->all()
                    : [];
                $mainPreview['extension_arc_sizes'] = [];
                $mainPreview['extension_node_labels'] = $extensions
                    ->mapWithKeys(static function (array $extension, int $extensionOffset) use ($side): array {
                        return [
                            $extensionOffset + 1 => self::mergeExtensionNodeLabels($extension, $side),
                        ];
                    })
                    ->all();

                return $mainPreview;
            })
            ->values()
            ->all();
    }

    /**
     * Render ended-after-merge origins as left/right aggregate branch strangs.
     * Each affected finding stays visible as a stem label, but the graph does
     * not explode into one full branch component per row.
     *
     * @param  array<string, mixed>  $mergeOutcomes
     * @return array<int, array<string, mixed>>
     */
    private static function branchPreviewStrangs(array $mergeOutcomes): array
    {
        $rows = collect($mergeOutcomes['rows'] ?? []);
        $branches = collect();

        foreach (self::branchPreviewOutcomeSpecs() as $spec) {
            if (($spec['placement'] ?? null) === 'branch') {
                $branches = $branches->merge(self::branchPreviewGroup($rows, $spec));
            }
        }

        foreach (self::branchPreviewOutcomeSpecs() as $spec) {
            if (($spec['placement'] ?? null) === 'branch-extension') {
                $branches = self::attachBranchPreviewExtensions($branches, $rows, $spec);
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
    private static function branchPreviewOutcomeSpecs(): array
    {
        return [
            [
                'outcome_group' => 'ended after merge',
                'placement' => 'branch',
                'color' => 'red',
                'end_label' => ['Ended after merge', 'shared obsolete'],
                'component_counter_offset' => 0,
            ],
            [
                'outcome_group' => 'ended before target',
                'placement' => 'branch-extension',
                'parent_outcome_group' => 'ended after merge',
                'attach_to' => 'bridge.end',
                'color' => 'rose',
                'end_label' => ['Ended before target', 'not shared obsolete'],
                'end_length' => '4rem',
                'cap_length' => '2rem',
                'bridge_length' => '28rem',
                'stem_length' => '4.25rem',
            ],
        ];
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $rows
     * @param  array<string, mixed>  $spec
     * @return array<int, array<string, mixed>>
     */
    private static function branchPreviewGroup(Collection $rows, array $spec): array
    {
        $outcomeGroup = (string) ($spec['outcome_group'] ?? '');
        $color = (string) ($spec['color'] ?? 'red');
        $endLabel = array_values((array) ($spec['end_label'] ?? [$outcomeGroup]));
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
            ->map(static function (Collection $sideRows, string $side) use ($color, $componentCounterOffset, $endLabel, $outcomeGroup): array {
                $labelSide = $side === 'left' ? 'left' : 'right';
                $insideLabelSide = $side === 'left' ? 'right' : 'left';
                $rows = $sideRows->pluck('row')->values();
                $stemContinuation = $rows
                    ->chunk(2)
                    ->mapWithKeys(static function (Collection $stemRows, int $index) use ($labelSide, $insideLabelSide): array {
                        $stemRows = $stemRows->values();
                        $entry = [
                            'length' => '4.25rem',
                        ];

                        if ($stemRows->has(0)) {
                            $entry[$labelSide] = self::branchPreviewRowLabel((array) $stemRows->get(0));
                        }

                        if ($stemRows->has(1)) {
                            $entry[$insideLabelSide] = self::branchPreviewRowLabel((array) $stemRows->get(1));
                        }

                        return [$index + 1 => $entry];
                    })
                    ->all();
                $stemCount = count($stemContinuation);
                $step = $side === 'left'
                    ? [
                        'beforeLength' => '1.5rem',
                        'afterLength' => '1.5rem',
                        'stepLabel' => [
                            'text' => [
                                'Source inactive',
                                'shared obsolete',
                                $rows->count() . ' rows',
                            ],
                            'badgeColor' => $color,
                        ],
                    ]
                    : null;

                return [
                    'component' => 'tw-graph.strang.branch-' . $side,
                    'side' => $side,
                    'component_counter' => $componentCounterOffset + ($side === 'left' ? 1 : 2),
                    'color' => $color,
                    'attach_to' => $side === 'left'
                        ? 'strang.merge-left.end'
                        : 'strang.merge-right.end',
                    'bridge_length' => '30rem',
                    'stem_length' => '4.25rem',
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
    private static function attachBranchPreviewExtensions(Collection $branches, Collection $rows, array $spec): Collection
    {
        $outcomeGroup = (string) ($spec['outcome_group'] ?? '');
        $parentOutcomeGroup = (string) ($spec['parent_outcome_group'] ?? '');
        $attachTo = (string) ($spec['attach_to'] ?? 'bridge.end');
        $color = (string) ($spec['color'] ?? 'rose');
        $endLabel = array_values((array) ($spec['end_label'] ?? [$outcomeGroup]));
        $endLength = (string) ($spec['end_length'] ?? '0rem');
        $capLength = (string) ($spec['cap_length'] ?? '1.75rem');
        $bridgeLength = (string) ($spec['bridge_length'] ?? '12rem');
        $stemLength = (string) ($spec['stem_length'] ?? '4.25rem');
        $extensionRows = $rows
            ->filter(static fn(array $row): bool => (string) ($row['outcome_group'] ?? '') === $outcomeGroup)
            ->values();

        if ($extensionRows->isEmpty()) {
            return $branches;
        }

        return $branches->map(static function (array $branch) use ($attachTo, $bridgeLength, $capLength, $color, $endLabel, $endLength, $extensionRows, $parentOutcomeGroup, $stemLength): array {
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
                                        'text' => self::branchPreviewRowLabel($row),
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
    private static function branchPreviewRowLabel(array $row): array
    {
        $findingId = $row['finding_id'] ?? null;
        $originKeyLabel = self::graphKeyLabelText((string) ($row['origin_key'] ?? ''), 52);
        $timestamp = self::graphTimestampLabel($row['last_seen_at'] ?? $row['first_seen_at'] ?? null);

        return array_values(array_filter([
            'finding ID #' . (string) ($findingId ?: '?'),
            $originKeyLabel,
            $timestamp,
        ]));
    }

    /**
     * @param  Collection<int, array{row: array<string, mixed>, index: int, side: string}>  $sideRows
     * @return Collection<int, array<string, mixed>>
     */
    private static function mergePreviewExtensionRows(Collection $sideRows, int $headExtensionCount): Collection
    {
        $extensionRows = $sideRows
            ->skip(1)
            ->values();

        if ($extensionRows->count() <= $headExtensionCount + 1) {
            return $extensionRows
                ->map(static fn(array $row): array => [
                    'type' => 'real',
                    'row' => $row['row'],
                    'bridge_length' => '19rem',
                    'stem_continuation' => [],
                ]);
        }

        $head = $extensionRows
            ->take($headExtensionCount)
            ->values()
            ->map(static function (array $row, int $index): array {
                return [
                    'type' => 'real',
                    'row' => $row['row'],
                    'bridge_length' => in_array($index, [0, 1], true) ? '20.75rem' : '17rem',
                    'stem_continuation' => $index === 0
                        ? [1 => '18rem']
                        : [1 => '2rem'],
                ];
            });
        $tailRow = $extensionRows->last();
        $hiddenRows = $extensionRows
            ->slice($headExtensionCount, -1)
            ->values();
        $hiddenNodeCount = (int) ceil($hiddenRows->count() / 2);
        $hiddenStemContinuationCount = max(0, $hiddenNodeCount - 2);
        $aggregate = [
            [
                'type' => 'aggregate',
                'rows' => $hiddenRows->map(static fn(array $row): array => $row['row'])->all(),
                'bridge_length' => '21rem',
                'stem_continuation' => collect(range(1, $hiddenStemContinuationCount + 1))
                    ->mapWithKeys(static fn(int $index): array => [
                        $index => $index === $hiddenStemContinuationCount + 1
                            ? '17rem'
                            : '3.5rem',
                    ])
                    ->all(),
            ],
        ];
        $tail = [
            [
                'type' => 'real',
                'row' => $tailRow['row'],
                'bridge_length' => '21rem',
                'stem_continuation' => [1 => '2rem'],
            ],
        ];

        return collect([...$head->all(), ...$aggregate, ...$tail]);
    }

    /**
     * @param  array<string, mixed>  $extension
     * @return array<string|int, mixed>
     */
    private static function mergeExtensionNodeLabels(array $extension, string $side): array
    {
        if (($extension['type'] ?? 'real') === 'aggregate') {
            return self::mergeAggregateExtensionNodeLabels(collect($extension['rows'] ?? []), $side);
        }

        return self::mergeRealExtensionNodeLabels((array) ($extension['row'] ?? []), $side);
    }

    /**
     * @param  array<string, mixed>  $row
     * @return array<string|int, mixed>
     */
    private static function mergeRealExtensionNodeLabels(array $row, string $side): array
    {
        $firstRoot = trim((string) ($row['first_root'] ?? ''));
        $firstRootLabel = self::findingLabel($firstRoot);
        $firstOriginKey = trim((string) ($row['first_origin_key'] ?? ''));
        $sourcePath = trim((string) ($row['source_path'] ?? ''));
        $firstTimestamp = trim((string) ($row['first_timestamp'] ?? ''));
        $context = trim((string) ($row['context'] ?? ''));
        $firstSeenLabel = array_values(array_filter(['First seen', $firstTimestamp]));
        $literalLabel = array_values(array_filter(['Literal', self::graphLabelText($context)]));
        $originKeyLabel = array_values(array_filter(['Origin key', self::graphKeyLabelText($firstOriginKey)]));
        $sourceLabel = array_values(array_filter(['Source', self::graphSourceLabelText($sourcePath)]));

        return [
            'start' => [
                'text' => array_values(array_filter([$firstRootLabel, self::graphTimestampLabel($firstTimestamp)])),
                'side' => 'bottom',
                'offset' => '0.75rem',
                'badgeColor' => 'amber',
            ],
            1 => $side === 'left'
                ? ['left' => $firstSeenLabel, 'right' => $literalLabel]
                : ['left' => $literalLabel, 'right' => $firstSeenLabel],
            2 => [
                ...($side === 'left'
                    ? ['left' => $originKeyLabel, 'right' => $sourceLabel]
                    : ['left' => $sourceLabel, 'right' => $originKeyLabel]),
            ],
        ];
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $rows
     * @return array<string|int, mixed>
     */
    private static function mergeAggregateExtensionNodeLabels(Collection $rows, string $side): array
    {
        $labels = [
            'start' => [
                'text' => ['Aggregated origins (' . $rows->count() . ')'],
                'side' => 'bottom',
                'offset' => '0.75rem',
                'badgeColor' => 'amber',
            ],
        ];

        $rows
            ->values()
            ->chunk(2)
            ->each(static function (Collection $chunk, int $chunkIndex) use (&$labels, $side): void {
                $chunk = $chunk->values();
                $nodeIndex = $chunkIndex + 1;
                $leftLabel = self::findingIdLabelWithTimestamp((array) $chunk->get(0));
                $rightLabel = self::findingIdLabelWithTimestamp((array) $chunk->get(1));

                $labels[$nodeIndex] = $side === 'left'
                    ? array_filter([
                        'left' => $leftLabel,
                        'right' => $rightLabel,
                    ])
                    : array_filter([
                        'left' => $rightLabel,
                        'right' => $leftLabel,
                    ]);
            });

        return $labels;
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
        $sourcePath = trim((string) ($row['source_path'] ?? ''));
        $firstTimestamp = trim((string) ($row['first_timestamp'] ?? ''));
        $context = trim((string) ($row['context'] ?? ''));
        $firstSeenLabel = array_values(array_filter([
            'First seen',
            $firstTimestamp,
        ]));
        $literalLabel = array_values(array_filter([
            'Literal',
            self::graphLabelText($context),
        ]));
        $originKeyLabel = array_values(array_filter([
            'Origin key',
            self::graphKeyLabelText($firstOriginKey),
        ]));
        $sourceLabel = array_values(array_filter([
            'Source',
            self::graphSourceLabelText($sourcePath),
        ]));
        $stemContinuation = [1 => '2rem'];
        $attachNodeNumber = 5 + count($stemContinuation);

        return [
            'component' => 'tw-graph.strang.merge-' . $side,
            'side' => $side,
            'color' => 'amber',
            'attach_to' => 'strang.trunk.path.1.end',
            'bridge_length' => '15rem',
            'stem_length' => '5rem',
            'stem_continuation' => $stemContinuation,
            'arc_sizes' => [],
            'start_label' => [
                'text' => array_values(array_filter([$firstRootLabel, self::graphTimestampLabel($firstTimestamp)])),
                'side' => 'bottom',
                'offset' => '0.75rem',
                'badgeColor' => 'amber',
            ],
            'node_labels' => [
                1 => $side === 'left'
                    ? [
                        'left' => $firstSeenLabel,
                        'right' => $literalLabel,
                    ]
                    : [
                        'left' => $literalLabel,
                        'right' => $firstSeenLabel,
                    ],
                2 => [
                    ...($side === 'left'
                        ? [
                            'left' => $originKeyLabel,
                            'right' => $sourceLabel,
                        ]
                        : [
                            'left' => $sourceLabel,
                            'right' => $originKeyLabel,
                        ]),
                ],
                $attachNodeNumber => [
                    $labelSide => array_values(array_filter([
                        (string) ($row['last_event'] ?? ''),
                        trim(self::graphTimestampLabel($row['last_timestamp'] ?? null) . ' · ' . (string) ($row['last_state'] ?? ''), ' ·'),
                    ])),
                    'connectorLength' => '5rem',
                    'long' => true,
                ],
            ],
            'source' => [
                'context' => $context,
                'first_timestamp' => $row['first_timestamp'] ?? null,
                'last_timestamp' => $row['last_timestamp'] ?? null,
            ],
        ];
    }

    private static function graphLabelText(string $value, int $limit = 44): string
    {
        $value = trim(preg_replace('/\s+/', ' ', $value) ?? '');

        if ($value === '') {
            return '';
        }

        return str($value)->limit($limit, '...')->toString();
    }

    private static function graphTimestampLabel(mixed $value): string
    {
        $value = trim(str_replace('T', ' ', (string) $value));

        if ($value === '') {
            return '';
        }

        return mb_substr($value, 0, 16);
    }

    private static function langValueTimestampLine(object $row): string
    {
        $timestamp = self::graphTimestampLabel($row->last_seen_at ?? $row->updated_at ?? $row->created_at ?? null);
        $localeValue = trim((string) $row->locale . ' · ' . self::graphLabelText((string) $row->value, 30));

        return trim(implode(' · ', array_filter([$timestamp, $localeValue])));
    }

    private static function findingLabel(string $value): string
    {
        $value = trim($value);

        if (preg_match('/#\d+/', $value, $matches) === 1) {
            return 'finding ID ' . $matches[0];
        }

        return $value !== '' ? $value : 'finding ID ?';
    }

    /**
     * @return array<int, string>
     */
    private static function findingIdLabel(string $value): array
    {
        $value = trim($value);

        if (preg_match('/#\d+/', $value, $matches) === 1) {
            return ['findingID', $matches[0]];
        }

        return ['findingID', $value !== '' ? $value : '?'];
    }

    /**
     * @param  array<string, mixed>  $row
     * @return array<int, string>
     */
    private static function findingIdLabelWithTimestamp(array $row): array
    {
        $findingId = self::findingIdLabel(trim((string) ($row['first_root'] ?? '')));

        return array_values(array_filter([
            trim(implode(' ', $findingId)),
            self::graphTimestampLabel($row['first_timestamp'] ?? null),
        ]));
    }

    private static function graphKeyLabelText(string $value, int $limit = 52): string
    {
        $value = trim(preg_replace('/\s+/', ' ', $value) ?? '');

        if ($value === '') {
            return '';
        }

        if (mb_strlen($value) <= $limit) {
            return $value;
        }

        return '...' . mb_substr($value, - ($limit - 3));
    }

    private static function graphSourceLabelText(string $value, int $limit = 52): string
    {
        return self::graphKeyLabelText($value, $limit);
    }

    private static function rem(float|int $value): string
    {
        $formatted = number_format((float) $value, 2, '.', '');
        $formatted = rtrim(rtrim($formatted, '0'), '.');

        return $formatted . 'rem';
    }

    /**
     * @param  Collection<string, mixed>  $summary
     */
    private static function mergeOriginCountLabel(Collection $summary): string
    {
        $total = (int) $summary->get('total', 0);

        return $total > 0 ? $total . ' origins' : '';
    }

    /**
     * @param  Collection<string, mixed>  $summary
     */
    private static function mergeOutcomeResultLine(Collection $summary): string
    {
        $total = (int) $summary->get('total', 0);

        if ($total <= 0) {
            return '';
        }

        $active = (int) $summary->get('source_active', 0);
        $ended = (int) $summary->get('source_inactive', 0);
        $unknown = (int) $summary->get('unknown', 0);
        $parts = [
            $active . ' active',
            $ended . ' ended',
        ];

        if ($unknown > 0) {
            $parts[] = $unknown . ' unknown';
        }

        return implode(' · ', $parts);
    }

    /**
     * Collect the current state of origin findings before turning inactive
     * origins into branch strangs.
     *
     * @param  array<string, mixed>  $mainRow
     * @param  Collection<int, array<string, mixed>>  $originRows
     * @return array<string, mixed>
     */
    private static function mergeOutcomes(array $mainRow, Collection $originRows): array
    {
        $rows = $originRows->values();
        $findingIds = $rows
            ->map(static fn(array $row): ?int => self::findingIdFromRoot((string) ($row['first_root'] ?? '')))
            ->filter()
            ->unique()
            ->values();

        if ($rows->isEmpty() || $findingIds->isEmpty() || ! Schema::hasTable('translation_workbench_findings')) {
            return [
                'summary' => [
                    'total' => $rows->count(),
                    'source_active' => 0,
                    'source_inactive' => 0,
                    'unknown' => $rows->count(),
                    'branch_candidates' => 0,
                ],
                'rows' => [],
            ];
        }

        $findings = DB::table('translation_workbench_findings as findings')
            ->leftJoin('translation_workbench_source_files as source_files', 'source_files.id', '=', 'findings.source_file_id')
            ->whereIn('findings.id', $findingIds->all())
            ->get([
                'findings.id',
                'findings.status',
                'findings.suggested_key',
                'findings.found_translation_key',
                'findings.existing_key',
                'findings.first_seen_at',
                'findings.last_seen_at',
                'findings.updated_at',
                'source_files.path as source_path',
            ])
            ->keyBy('id');
        $sharedCandidates = Schema::hasTable('translation_workbench_shared_key_candidates')
            ? DB::table('translation_workbench_shared_key_candidates')
            ->whereIn('finding_id', $findingIds->all())
            ->orderByDesc('updated_at')
            ->get([
                'id',
                'finding_id',
                'key_id',
                'matched_key_id',
                'current_translation_key',
                'suggested_shared_translation_key',
                'status',
                'last_seen_at',
                'updated_at',
            ])
            ->groupBy('finding_id')
            ->map(static fn(Collection $candidates): object => $candidates->first())
            : collect();
        $keyLookups = collect([
            $mainRow['translation_key'] ?? null,
            ...$rows->pluck('first_origin_key')->all(),
        ])
            ->map(static fn(mixed $key): string => trim((string) $key))
            ->filter()
            ->unique()
            ->values();
        $keys = Schema::hasTable('translation_workbench_keys') && $keyLookups->isNotEmpty()
            ? DB::table('translation_workbench_keys')
            ->whereIn('translation_key', $keyLookups->all())
            ->orWhereIn('suggested_key', $keyLookups->all())
            ->get(['id', 'translation_key', 'suggested_key', 'status', 'review_status', 'updated_at'])
            ->flatMap(static fn(object $key): array => array_filter([
                trim((string) $key->translation_key) => $key,
                trim((string) $key->suggested_key) => $key,
            ]))
            : collect();

        $outcomeRows = $rows
            ->map(static function (array $row, int $index) use ($findings, $sharedCandidates, $keys, $mainRow): array {
                $findingId = self::findingIdFromRoot((string) ($row['first_root'] ?? ''));
                $finding = $findingId ? $findings->get($findingId) : null;
                $candidate = $findingId ? $sharedCandidates->get($findingId) : null;
                $originKey = trim((string) ($row['first_origin_key'] ?? ''));
                $originKeyRow = $originKey !== '' ? $keys->get($originKey) : null;
                $targetKey = trim((string) ($mainRow['translation_key'] ?? ''));
                $findingStatus = (string) ($finding->status ?? 'unknown');
                $sharedStatus = (string) ($candidate->status ?? 'none');
                $originKeyStatus = (string) ($originKeyRow->status ?? 'unknown');
                $sourceActive = $finding !== null && $findingStatus === 'active';
                $branchCandidate = $finding === null || ! $sourceActive;
                $outcomeGroup = self::mergeOutcomeGroup($findingStatus, $sharedStatus, $originKeyStatus);

                return [
                    'index' => $index + 1,
                    'side' => $index % 2 === 0 ? 'left' : 'right',
                    'finding_id' => $findingId,
                    'origin_key' => $originKey,
                    'target_key' => $targetKey,
                    'finding_status' => $findingStatus,
                    'origin_key_status' => $originKeyStatus,
                    'origin_key_review_status' => (string) ($originKeyRow->review_status ?? 'unknown'),
                    'shared_candidate_id' => $candidate->id ?? null,
                    'shared_candidate_status' => $sharedStatus,
                    'matched_key_id' => $candidate->matched_key_id ?? null,
                    'first_seen_at' => self::graphTimestampLabel($row['first_timestamp'] ?? $finding?->first_seen_at ?? null),
                    'last_seen_at' => self::graphTimestampLabel($row['last_timestamp'] ?? $finding?->last_seen_at ?? $candidate?->last_seen_at ?? null),
                    'source_path' => (string) ($finding->source_path ?? $row['source_path'] ?? ''),
                    'outcome' => $finding === null
                        ? 'unknown finding'
                        : ($sourceActive ? 'source active' : 'source inactive'),
                    'outcome_group' => $outcomeGroup,
                    'branch_hint' => $branchCandidate,
                ];
            })
            ->values();
        $groups = $outcomeRows
            ->countBy('outcome_group')
            ->sortKeys()
            ->all();

        return [
            'summary' => [
                'total' => $outcomeRows->count(),
                'source_active' => $outcomeRows->where('outcome', 'source active')->count(),
                'source_inactive' => $outcomeRows->where('outcome', 'source inactive')->count(),
                'unknown' => $outcomeRows->where('outcome', 'unknown finding')->count(),
                'branch_candidates' => $outcomeRows->where('branch_hint', true)->count(),
                'branch_candidate_findings' => $outcomeRows
                    ->where('branch_hint', true)
                    ->pluck('finding_id')
                    ->filter()
                    ->unique()
                    ->count(),
                'ended_after_merge_findings' => $outcomeRows
                    ->where('outcome_group', 'ended after merge')
                    ->pluck('finding_id')
                    ->filter()
                    ->unique()
                    ->count(),
                'ended_after_merge_rows' => $outcomeRows
                    ->where('outcome_group', 'ended after merge')
                    ->count(),
                'groups' => $groups,
            ],
            'rows' => $outcomeRows->all(),
        ];
    }

    private static function mergeOutcomeGroup(string $findingStatus, string $sharedStatus, string $originKeyStatus): string
    {
        if ($findingStatus === 'active' && in_array($sharedStatus, ['obsolete', 'accepted', 'applied'], true)) {
            return 'arrived at shared key';
        }

        if ($findingStatus === 'active' && $originKeyStatus === 'obsolete') {
            return 'active source, obsolete origin key';
        }

        if ($findingStatus !== 'active' && $sharedStatus === 'obsolete') {
            return 'ended after merge';
        }

        if ($findingStatus !== 'active') {
            return 'ended before target';
        }

        if (in_array($sharedStatus, ['pending', 'open'], true)) {
            return 'shared review pending';
        }

        return 'needs review';
    }

    private static function findingIdFromRoot(string $root): ?int
    {
        return preg_match('/finding\s+#(\d+)/i', $root, $matches) === 1
            ? (int) $matches[1]
            : null;
    }

    /**
     * Build a focused inspection payload for deciding which finding details belong
     * directly in the graph and which should move into tooltips later.
     *
     * @param  array<string, mixed>|null  $mainRow
     * @param  Collection<int, array<string, mixed>>|array<int, array<string, mixed>>  $originRows
     * @return array<string, mixed>
     */
    public static function inspectFinding(int $findingId, ?array $mainRow, Collection|array $originRows): array
    {
        $originRow = collect($originRows)
            ->first(static fn(array $row): bool => (string) ($row['first_root'] ?? '') === 'finding #' . $findingId);
        $preview = self::fromTimelineChain($mainRow, collect(), collect($originRows));
        $previewMerges = collect(data_get($preview, 'render_preview.merges', []));
        $renderedAs = null;

        foreach ($previewMerges as $mergeIndex => $merge) {
            if (str_contains(json_encode($merge, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?: '', 'finding ID #' . $findingId)) {
                $renderedAs = [
                    'side' => data_get($merge, 'side'),
                    'strang' => data_get($merge, 'component'),
                    'component_counter' => $mergeIndex + 1,
                    'extension_count' => data_get($merge, 'extension_count', 0),
                ];
                break;
            }
        }

        $finding = null;
        if (Schema::hasTable('translation_workbench_findings')) {
            $query = DB::table('translation_workbench_findings as findings')
                ->leftJoin('translation_workbench_source_files as source_files', 'source_files.id', '=', 'findings.source_file_id')
                ->where('findings.id', $findingId);

            $finding = $query->first([
                'findings.id',
                'findings.status',
                'findings.suggested_key',
                'findings.found_translation_key',
                'findings.existing_key',
                'findings.literal_text',
                'findings.literal_text_suggested',
                'findings.namespace',
                'findings.group',
                'findings.path_key',
                'findings.kind',
                'findings.function_name',
                'findings.raw_expression',
                'findings.source_line',
                'findings.first_seen_at',
                'findings.last_seen_at',
                'findings.scan_count',
                'findings.created_at',
                'findings.updated_at',
                'source_files.path as source_path',
                'source_files.source_root',
                'source_files.source_area',
            ]);
        }

        $sharedCandidate = Schema::hasTable('translation_workbench_shared_key_candidates')
            ? DB::table('translation_workbench_shared_key_candidates')
            ->where('finding_id', $findingId)
            ->orderByDesc('updated_at')
            ->first([
                'id',
                'key_id',
                'matched_key_id',
                'current_translation_key',
                'suggested_shared_translation_key',
                'matched_review_count',
                'matched_finding_count',
                'confidence',
                'status',
                'first_seen_at',
                'last_seen_at',
                'created_at',
                'updated_at',
            ])
            : null;

        $reviews = Schema::hasTable('translation_workbench_reviews')
            ? DB::table('translation_workbench_reviews')
            ->where('finding_id', $findingId)
            ->orWhereJsonContains('meta->selected_finding_ids', $findingId)
            ->orderByDesc('reviewed_at')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get(['id', 'key_id', 'finding_id', 'review_type', 'decision', 'reviewed_at', 'created_at'])
            ->map(static fn(object $row): array => (array) $row)
            ->all()
            : [];

        $timelineEvents = Schema::hasTable('translation_workbench_timeline_events')
            ? DB::table('translation_workbench_timeline_events')
            ->where('finding_id', $findingId)
            ->orderByDesc('created_at')
            ->limit(8)
            ->get(['id', 'key_id', 'finding_id', 'review_id', 'event_type', 'created_at'])
            ->map(static fn(object $row): array => (array) $row)
            ->all()
            : [];

        $translationKeys = collect([
            data_get($finding, 'suggested_key'),
            data_get($finding, 'found_translation_key'),
            data_get($sharedCandidate, 'current_translation_key'),
            data_get($sharedCandidate, 'suggested_shared_translation_key'),
            data_get($mainRow, 'translation_key'),
        ])
            ->map(static fn(mixed $key): string => trim((string) $key))
            ->filter()
            ->unique()
            ->values();

        $langValues = Schema::hasTable('translation_workbench_lang_values') && $translationKeys->isNotEmpty()
            ? DB::table('translation_workbench_lang_values')
            ->whereIn('translation_key', $translationKeys->all())
            ->orderBy('translation_key')
            ->orderBy('locale')
            ->limit(12)
            ->get(['id', 'locale', 'translation_key', 'value', 'status', 'locale_role', 'updated_at'])
            ->map(static fn(object $row): array => (array) $row)
            ->all()
            : [];

        return [
            'finding_id' => $findingId,
            'finding' => $finding ? (array) $finding : null,
            'origin_row' => $originRow,
            'rendered_as' => $renderedAs,
            'shared_candidate' => $sharedCandidate ? (array) $sharedCandidate : null,
            'reviews' => $reviews,
            'timeline_events' => $timelineEvents,
            'lang_values' => $langValues,
            'related_translation_keys' => $translationKeys->all(),
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
