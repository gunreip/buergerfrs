<?php

declare(strict_types=1);

namespace Gunreip\TranslationWorkbench\Support\TwGraph\DataDriven\TimelineChainGraphData;

use Illuminate\Support\Collection;

final class MergePreviewBuilder
{
    /**
     * @param  Collection<int, array<string, mixed>>  $originRows
     * @return array<int, array<string, mixed>>
     */
    public static function previews(Collection $originRows, int $maxMergeCandidates): array
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
                $mainPreview = self::preview($main['row'], (int) $main['index'], $side);
                $extensions = self::extensionRows($sideRows, max(0, intdiv($maxMergeCandidates, 2) - 1));

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
                            $extensionOffset + 1 => self::extensionNodeLabels($extension, $side),
                        ];
                    })
                    ->all();

                return $mainPreview;
            })
            ->values()
            ->all();
    }

    /**
     * @param  Collection<int, array{row: array<string, mixed>, index: int, side: string}>  $sideRows
     * @return Collection<int, array<string, mixed>>
     */
    private static function extensionRows(Collection $sideRows, int $headExtensionCount): Collection
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
    private static function extensionNodeLabels(array $extension, string $side): array
    {
        if (($extension['type'] ?? 'real') === 'aggregate') {
            return self::aggregateExtensionNodeLabels(collect($extension['rows'] ?? []), $side);
        }

        return self::realExtensionNodeLabels((array) ($extension['row'] ?? []), $side);
    }

    /**
     * @param  array<string, mixed>  $row
     * @return array<string|int, mixed>
     */
    private static function realExtensionNodeLabels(array $row, string $side): array
    {
        $firstRoot = trim((string) ($row['first_root'] ?? ''));
        $firstRootLabel = LabelFormatter::findingLabel($firstRoot);
        $firstOriginKey = trim((string) ($row['first_origin_key'] ?? ''));
        $sourcePath = trim((string) ($row['source_path'] ?? ''));
        $firstTimestamp = trim((string) ($row['first_timestamp'] ?? ''));
        $context = trim((string) ($row['context'] ?? ''));
        $firstSeenLabel = array_values(array_filter(['First seen', $firstTimestamp]));
        $literalLabel = array_values(array_filter(['Literal', LabelFormatter::graphLabelText($context)]));
        $originKeyLabel = array_values(array_filter(['Origin key', LabelFormatter::graphKeyLabelText($firstOriginKey)]));
        $sourceLabel = array_values(array_filter(['Source', LabelFormatter::graphSourceLabelText($sourcePath)]));

        return [
            'start' => [
                'text' => array_values(array_filter([$firstRootLabel, LabelFormatter::graphTimestampLabel($firstTimestamp)])),
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
    private static function aggregateExtensionNodeLabels(Collection $rows, string $side): array
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
                $leftLabel = LabelFormatter::findingIdLabelWithTimestamp((array) $chunk->get(0));
                $rightLabel = LabelFormatter::findingIdLabelWithTimestamp((array) $chunk->get(1));

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
    private static function preview(array $row, int $index, ?string $side = null): array
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
            LabelFormatter::graphLabelText($context),
        ]));
        $originKeyLabel = array_values(array_filter([
            'Origin key',
            LabelFormatter::graphKeyLabelText($firstOriginKey),
        ]));
        $sourceLabel = array_values(array_filter([
            'Source',
            LabelFormatter::graphSourceLabelText($sourcePath),
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
                'text' => array_values(array_filter([$firstRootLabel, LabelFormatter::graphTimestampLabel($firstTimestamp)])),
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
                        trim(LabelFormatter::graphTimestampLabel($row['last_timestamp'] ?? null) . ' · ' . (string) ($row['last_state'] ?? ''), ' ·'),
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
}
