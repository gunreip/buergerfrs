<?php

declare(strict_types=1);

namespace Gunreip\TranslationWorkbench\Support\TwGraph\DataDriven\TimelineChainGraphData;

use Gunreip\TranslationWorkbench\Support\TwGraph\Defaults;
use Illuminate\Support\Collection;

final class GraphFacts
{
    /**
     * @param  array<string, mixed>  $mainRow
     * @param  Collection<int, array<string, mixed>>  $rootRows
     * @return array<string, mixed>
     */
    public static function trunk(array $mainRow, Collection $rootRows): array
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
                    'color' => (string) ($row['color'] ?? self::color('fallback', 'zinc')),
                ])
                ->values()
                ->all(),
        ];
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $originRows
     * @return array<string, mixed>
     */
    public static function merge(Collection $originRows): array
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
    public static function branch(Collection $rootRows): array
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
                        'color' => (string) ($row['branch_color'] ?? $row['color'] ?? self::color('fallback', 'zinc')),
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
    public static function componentIntent(array $mainRow, Collection $rootRows, Collection $originRows): array
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
            [
                'component' => 'tw-graph.strang.rekey-source-left/right + tw-graph.strang.rekey-target-left/right',
                'from' => 'timelineChainMainRow.meta.moved_relations',
                'required' => (string) ($mainRow['chain_type'] ?? '') === 'moved',
                'count' => collect(data_get($mainRow, 'meta.moved_relations', []))->count(),
            ],
        ];
    }

    private static function color(string $key, string $fallback): string
    {
        return Defaults::dataDrivenString('colors.' . $key, Defaults::graphString('colors.' . $key, $fallback));
    }
}
