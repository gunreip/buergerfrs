<?php

declare(strict_types=1);

namespace Gunreip\TranslationWorkbench\Support\TwGraph\DataDriven\TimelineChainGraphData;

use Gunreip\TranslationWorkbench\Support\TwGraph\Defaults;
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
                $mainPreview['stem_continuation'] = self::withConfiguredVerticalStagger(
                    (array) ($mainPreview['stem_continuation'] ?? []),
                    1,
                );

                $mainPreview['extension_count'] = $extensions->count();
                $mainPreview['extension_stem_lengths'] = [];
                $mainPreview['extension_bridge_continuations'] = [];
                $mainPreview['extension_stem_continuations'] = $extensions->isNotEmpty()
                    ? $extensions
                    ->mapWithKeys(static fn(array $extension, int $extensionOffset): array => [
                        $extensionOffset + 1 => self::withConfiguredVerticalStagger(
                            (array) ($extension['stem_continuation'] ?? []),
                            $extensionOffset + 2,
                        ),
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
        $directPerSideBeforeAggregate = max(1, self::mergeLayoutInt('direct_per_side_before_aggregate', 5));
        $extensionRows = $sideRows
            ->skip(1)
            ->values();

        if ($sideRows->count() <= $directPerSideBeforeAggregate) {
            return $extensionRows
                ->map(static fn(array $row, int $index): array => [
                    'type' => 'real',
                    'row' => $row['row'],
                    'stem_continuation' => self::realExtensionStemContinuation($index),
                ]);
        }

        $head = $extensionRows
            ->take($headExtensionCount)
            ->values()
            ->map(static function (array $row, int $index): array {
                return [
                    'type' => 'real',
                    'row' => $row['row'],
                    'stem_continuation' => self::realExtensionStemContinuation($index),
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
                'stem_continuation' => self::aggregateStemContinuation($hiddenStemContinuationCount),
            ],
        ];
        $tail = [
            [
                'type' => 'real',
                'row' => $tailRow['row'],
                'stem_continuation' => self::tailExtensionStemContinuation(),
            ],
        ];

        return collect([...$head->all(), ...$aggregate, ...$tail]);
    }

    /**
     * @return array<int, string>
     */
    private static function aggregateStemContinuation(int $hiddenStemContinuationCount): array
    {
        return collect(range(1, $hiddenStemContinuationCount + 1))
            ->mapWithKeys(static fn(int $index): array => [
                $index => [],
            ])
            ->all();
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
                'offset' => self::defaultLabelOffset(),
                'badgeColor' => self::color('merge', 'amber'),
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
        $aggregateBadgeColor = self::color('merge_aggregate', self::color('merge', 'amber'));
        $labels = [
            'start' => [
                'text' => ['Aggregated origins (' . $rows->count() . ')'],
                'side' => 'bottom',
                'offset' => self::defaultLabelOffset(),
                'badgeColor' => $aggregateBadgeColor,
            ],
        ];

        $rows
            ->values()
            ->chunk(2)
            ->each(static function (Collection $chunk, int $chunkIndex) use (&$labels, $side, $aggregateBadgeColor): void {
                $chunk = $chunk->values();
                $nodeIndex = $chunkIndex + 1;
                $leftRow = $chunk->get(0);
                $rightRow = $chunk->get(1);
                $leftLabel = is_array($leftRow) ? LabelFormatter::findingIdLabelWithTimestamp($leftRow) : null;
                $rightLabel = is_array($rightRow) ? LabelFormatter::findingIdLabelWithTimestamp($rightRow) : null;

                $labels[$nodeIndex] = $side === 'left'
                    ? array_filter([
                        'badgeColor' => $aggregateBadgeColor,
                        'left' => $leftLabel,
                        'right' => $rightLabel,
                    ])
                    : array_filter([
                        'badgeColor' => $aggregateBadgeColor,
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
        $stemContinuation = self::mainStemContinuation();
        $attachNodeNumber = 5 + count($stemContinuation);

        return [
            'component' => 'tw-graph.strang.merge-' . $side,
            'side' => $side,
            'color' => self::color('merge', 'amber'),
            'attach_to' => 'strang.trunk.path.1.end',
            'stem_continuation' => $stemContinuation,
            'arc_sizes' => [],
            'start_label' => [
                'text' => array_values(array_filter([$firstRootLabel, LabelFormatter::graphTimestampLabel($firstTimestamp)])),
                'side' => 'bottom',
                'offset' => self::defaultLabelOffset(),
                'badgeColor' => self::color('merge', 'amber'),
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
                    'connectorLength' => self::defaultConnectorLength('merge_end_label_connector_length'),
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

    private static function defaultLabelOffset(): string
    {
        return Defaults::dataDrivenString(
            'label_offset',
            Defaults::graphString('label_offset', Defaults::dataDrivenString('connector_gap', '0.25rem')),
        );
    }

    /**
     * @return array<int|string, mixed>
     */
    private static function mainStemContinuation(): array
    {
        return self::mergeLayoutArray('main_stem_continuation', [1 => []]);
    }

    /**
     * @return array<int|string, mixed>
     */
    private static function realExtensionStemContinuation(int $extensionIndex): array
    {
        return self::mergeLayoutArray('real_extension_stem_continuation', [1 => []]);
    }

    /**
     * @return array<int|string, mixed>
     */
    private static function tailExtensionStemContinuation(): array
    {
        return self::mergeLayoutArray('tail_extension_stem_continuation', [1 => []]);
    }

    /**
     * Apply the configured visual merge rhythm as real stem-continuation props.
     * Collision compensation may later add measured deltas to the same target.
     *
     * @param  array<int|string, mixed>  $continuation
     * @return array<int|string, mixed>
     */
    private static function withConfiguredVerticalStagger(array $continuation, int $sequenceNumber): array
    {
        if (! self::mergeLayoutBool('vertical_stagger_enabled', true)) {
            return $continuation;
        }

        if (! self::matchesConfiguredVerticalStaggerSequence($sequenceNumber)) {
            return $continuation;
        }

        $length = self::mergeLayoutString('vertical_stagger_length', '0rem');

        if (Defaults::dataDrivenRem('merge_layout.vertical_stagger_length', Defaults::graphString('merge_layout.vertical_stagger_length', '0rem')) <= 0.0) {
            return $continuation;
        }

        $continuationKey = self::mergeVerticalStaggerContinuationKey($continuation);
        $entry = $continuation[$continuationKey] ?? [];

        if (! is_array($entry)) {
            $entry = ['length' => $entry];
        }

        $entry['length'] = $length;
        $entry['staggered'] = true;
        $continuation[$continuationKey] = $entry;

        return $continuation;
    }

    /**
     * Use the configured stem as minimum, but stagger aggregate extensions on
     * their last existing continuation stem so the visual rhythm affects the
     * actual rendered body instead of only the first short start stem.
     *
     * @param  array<int|string, mixed>  $continuation
     */
    private static function mergeVerticalStaggerContinuationKey(array $continuation): int
    {
        $configuredKey = max(1, self::mergeLayoutInt('vertical_stagger_stem', 2) - 1);
        $lastExistingKey = collect(array_keys($continuation))
            ->map(static fn(mixed $key): int => is_numeric($key) ? (int) $key : 0)
            ->max();

        return max($configuredKey, is_numeric($lastExistingKey) ? (int) $lastExistingKey : 0);
    }

    private static function matchesConfiguredVerticalStaggerSequence(int $sequenceNumber): bool
    {
        $sequence = self::mergeLayoutString('vertical_stagger_sequence', 'even');

        return match ($sequence) {
            'odd' => $sequenceNumber % 2 === 1,
            default => $sequenceNumber % 2 === 0,
        };
    }

    /**
     * @param  array<int|string, mixed>  $fallback
     * @return array<int|string, mixed>
     */
    private static function mergeLayoutArray(string $key, array $fallback): array
    {
        $config = app('config');
        $dataDrivenKey = 'tw-graph-data-driven-defaults.merge_layout.' . $key;
        $centralKey = 'tw-graph-defaults.merge_layout.' . $key;
        $value = $config->has($dataDrivenKey)
            ? $config->get($dataDrivenKey)
            : ($config->has($centralKey) ? $config->get($centralKey) : $fallback);

        return is_array($value) ? $value : $fallback;
    }

    private static function mergeLayoutBool(string $key, bool $fallback): bool
    {
        return Defaults::dataDrivenBool('merge_layout.' . $key, Defaults::graphBool('merge_layout.' . $key, $fallback));
    }

    private static function mergeLayoutInt(string $key, int $fallback): int
    {
        $value = Defaults::dataDriven(
            'merge_layout.' . $key,
            Defaults::graph('merge_layout.' . $key, $fallback),
        );

        return is_numeric($value) ? (int) $value : $fallback;
    }

    private static function mergeLayoutString(string $key, string $fallback): string
    {
        return Defaults::dataDrivenString(
            'merge_layout.' . $key,
            Defaults::graphString('merge_layout.' . $key, $fallback),
        );
    }
}
