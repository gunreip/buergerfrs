<?php

declare(strict_types=1);

namespace Gunreip\TranslationWorkbench\Support\TwGraph\DataDriven\TimelineChainGraphData;

final class BranchLabelCollisionResolver
{
    private const TRUNK_STEP_REM = 6.0;
    private const LABEL_REACH_REM = 16.0;
    private const LABEL_GAP_REM = 2.0;
    private const BRIDGE_HEIGHT_REM = 1.5;
    private const END_SEGMENT_WIDTH_REM = 1.5;

    /**
     * @param  array<int, array<string, mixed>>  $branches
     * @return array<int, array<string, mixed>>
     */
    public static function resolve(array $branches): array
    {
        return self::withBranchEndBridgeWarnings(self::withBranchBoundsDebug(self::resolveBranchLabelCollisions($branches)));
    }

    /**
     * Rebuild report-only branch bounds after the final trunk path lengths are
     * known. The rendered strangs attach to the AnchorRegistry, so debug bounds
     * must use the same final anchor coordinates instead of the first-pass
     * timeline estimate.
     *
     * @param  array<int, array<string, mixed>>  $branches
     * @return array<int, array<string, mixed>>
     */
    public static function refreshDebugBounds(array $branches): array
    {
        foreach ($branches as $index => $branch) {
            unset(
                $branches[$index]['layout']['branchBoundsDebug'],
                $branches[$index]['layout']['warnings'],
            );
        }

        return self::withBranchEndBridgeWarnings(self::withBranchBoundsDebug($branches));
    }

    /**
     * Branches on the same side can overlap when their stem labels or bridge
     * boxes occupy the same band. This pass is report-only: it records the
     * collision delta but must not mutate bridge/stem props.
     *
     * @param  array<int, array<string, mixed>>  $branches
     * @return array<int, array<string, mixed>>
     */
    private static function resolveBranchLabelCollisions(array $branches): array
    {
        $debugEntries = [];

        foreach (['left', 'right'] as $side) {
            $sideBranches = self::indexedBranchBounds($branches, $side, 'label');

            foreach ($sideBranches as $position => $entry) {
                foreach ($sideBranches->slice($position + 1) as $next) {
                    if (! self::overlaps($entry['bounds'], $next['bounds'])) {
                        continue;
                    }

                    $index = (int) $entry['index'];
                    $debugEntries[$index][] = self::collisionDebugEntry(
                        (string) $side,
                        'label',
                        (array) $entry['branch'],
                        (array) $next['branch'],
                        1,
                        self::requiredOutwardBridgeIncrement((string) $side, (array) $entry['bounds'], (array) $next['bounds']),
                    );
                }
            }

            $sideBridges = self::indexedBranchBounds($branches, $side, 'bridge');

            foreach ($sideBridges as $position => $entry) {
                foreach ($sideBridges->slice($position + 1) as $next) {
                    if (! self::overlaps($entry['bounds'], $next['bounds'])) {
                        continue;
                    }

                    $index = (int) $entry['index'];
                    $debugEntries[$index][] = self::collisionDebugEntry(
                        (string) $side,
                        'bridge',
                        (array) $entry['branch'],
                        (array) $next['branch'],
                        1,
                        self::requiredOutwardBridgeIncrement((string) $side, (array) $entry['bounds'], (array) $next['bounds']),
                    );
                }
            }
        }

        foreach ($debugEntries as $index => $entries) {
            $branches[$index]['layout'] = [
                ...((array) ($branches[$index]['layout'] ?? [])),
                'branchCollisionDebug' => [
                    ...((array) data_get($branches[$index], 'layout.branchCollisionDebug', [])),
                    ...$entries,
                ],
                'branchCollisionReport' => [
                    'collisionDelta' => collect($entries)
                        ->pluck('requiredIncrement')
                        ->filter()
                        ->unique()
                        ->values()
                        ->all(),
                    'appliedCorrection' => '',
                    'reason' => 'raw collision report only; bridge-length correction is intentionally not applied',
                ],
            ];
        }

        return $branches;
    }

    /**
     * @param  array<string, mixed>  $branch
     * @param  array<string, mixed>  $against
     * @return array{side: string, type: string, branch: string, against: string, pass: int, requiredIncrement: string}
     */
    private static function collisionDebugEntry(
        string $side,
        string $type,
        array $branch,
        array $against,
        int $pass,
        float $requiredIncrement,
    ): array {
        return [
            'side' => $side,
            'type' => $type,
            'branch' => self::branchId($branch),
            'against' => self::branchId($against),
            'pass' => $pass,
            'requiredIncrement' => self::rem($requiredIncrement),
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $branches
     * @return array<int, array<string, mixed>>
     */
    private static function withBranchBoundsDebug(array $branches): array
    {
        foreach ($branches as $index => $branch) {
            $branches[$index]['layout'] = [
                ...((array) ($branches[$index]['layout'] ?? [])),
                'branchBoundsDebug' => collect([
                    ...self::bridgeBoxes($branch, $index),
                    [
                        'id' => self::branchId($branch) . '.label-bounds',
                        'type' => 'label',
                        'branchIndex' => $index,
                        ...self::labelBounds($branch),
                    ],
                    ...self::branchSubDebugBoxes($branch, $index),
                ])
                    ->map(static fn(array $box): array => [
                        'type' => (string) data_get($box, 'type', 'bounds'),
                        'side' => (string) ($branch['side'] ?? ''),
                        'id' => (string) $box['id'],
                        ...self::boxToRem($box),
                    ])
                    ->values()
                    ->all(),
            ];
        }

        return $branches;
    }

    /**
     * @param  array<int, array<string, mixed>>  $branches
     * @return \Illuminate\Support\Collection<int, array{index: int, branch: array<string, mixed>, bounds: array<string, float>}>
     */
    private static function indexedBranchBounds(array $branches, string $side, string $boundsType)
    {
        return collect($branches)
            ->values()
            ->map(static fn(array $branch, int $index): array => [
                'index' => $index,
                'branch' => $branch,
                'bounds' => $boundsType === 'bridge'
                    ? self::bridgeBoxes($branch, $index)[0]
                    : self::labelBounds($branch),
            ])
            ->filter(static fn(array $entry): bool => (string) data_get($entry, 'branch.side') === $side)
            ->sortBy(static fn(array $entry): array => [
                (int) data_get($entry, 'branch.component_counter', 0),
                (float) data_get($entry, 'bounds.yStart', 0.0),
            ])
            ->values();
    }

    /**
     * @param  array<string, float>  $inner
     * @param  array<string, float>  $outer
     */
    private static function requiredOutwardBridgeIncrement(string $side, array $inner, array $outer): float
    {
        if ($side === 'left') {
            return max(0.0, (float) $inner['xEnd'] - (float) $outer['xStart'] + self::LABEL_GAP_REM);
        }

        return max(0.0, (float) $outer['xEnd'] - (float) $inner['xStart'] + self::LABEL_GAP_REM);
    }

    /**
     * Calculate potential trunk spacing adjustments for branch-end/bridge
     * overlaps. The returned values are diagnostic deltas only unless a caller
     * explicitly applies them and reports that in Applied Correction.
     *
     * @param  array<int, array<string, mixed>>  $branches
     * @return array<int, float>
     */
    public static function trunkPathSpacingAdjustments(array $branches): array
    {
        return collect(self::branchEndBridgeCollisions($branches))
            ->reduce(static function (array $adjustments, array $collision): array {
                $pathNumber = self::attachIndex((string) data_get($collision, 'endBranch.attach_to')) + 1;
                $overlap = self::overlapSize((array) $collision['endSegmentBox'], (array) $collision['bridgeBox']);
                $adjustments[$pathNumber] = max(
                    (float) ($adjustments[$pathNumber] ?? 0.0),
                    $overlap + self::LABEL_GAP_REM,
                );

                return $adjustments;
            }, []);
    }

    /**
     * @param  array<int, array<string, mixed>>  $branches
     * @return array<int, array{endBranch: array<string, mixed>, bridgeBranch: array<string, mixed>, endSegmentBox: array<string, mixed>, bridgeBox: array<string, mixed>}>
     */
    private static function branchEndBridgeCollisions(array $branches): array
    {
        $collisions = [];
        $bridgeBoxes = collect($branches)
            ->flatMap(static fn(array $branch, int $index): array => self::bridgeBoxes($branch, $index))
            ->values();
        $endSegmentBoxes = collect($branches)
            ->flatMap(static fn(array $branch, int $index): array => self::branchEndSegmentBoxes($branch, $index))
            ->values();

        foreach ($endSegmentBoxes as $endSegmentBox) {
            foreach ($bridgeBoxes as $bridgeBox) {
                if ((int) $bridgeBox['branchIndex'] === (int) $endSegmentBox['branchIndex']) {
                    continue;
                }

                if (self::overlaps($endSegmentBox, $bridgeBox)) {
                    $collisions[] = [
                        'endBranch' => $branches[(int) $endSegmentBox['branchIndex']] ?? [],
                        'bridgeBranch' => $branches[(int) $bridgeBox['branchIndex']] ?? [],
                        'endSegmentBox' => $endSegmentBox,
                        'bridgeBox' => $bridgeBox,
                    ];
                }
            }
        }

        return $collisions;
    }

    /**
     * Detect branch-end segment boxes that overlap any rendered branch bridge.
     * This is intentionally report-only: side switching/extension fallback will
     * belong to a later layout rule after the warnings are visually verified.
     *
     * @param  array<int, array<string, mixed>>  $branches
     * @return array<int, array<string, mixed>>
     */
    private static function withBranchEndBridgeWarnings(array $branches): array
    {
        $bridgeBoxes = collect($branches)
            ->flatMap(static fn(array $branch, int $index): array => self::bridgeBoxes($branch, $index))
            ->values();
        $endSegmentBoxes = collect($branches)
            ->flatMap(static fn(array $branch, int $index): array => self::branchEndSegmentBoxes($branch, $index))
            ->values();

        foreach ($endSegmentBoxes as $endSegmentBox) {
            foreach ($bridgeBoxes as $bridgeBox) {
                if ((int) $bridgeBox['branchIndex'] === (int) $endSegmentBox['branchIndex']) {
                    continue;
                }

                if (! self::overlaps($endSegmentBox, $bridgeBox)) {
                    continue;
                }

                $branchIndex = (int) $bridgeBox['branchIndex'];

                $branches[$branchIndex]['layout'] = [
                    ...((array) ($branches[$branchIndex]['layout'] ?? [])),
                    'warnings' => [
                        ...((array) data_get($branches[$branchIndex], 'layout.warnings', [])),
                        [
                            'type' => 'branch-end-over-bridge',
                            'message' => 'Branch end segment overlaps branch bridge',
                            'label' => $endSegmentBox['id'],
                            'bridge' => $bridgeBox['id'],
                            'anchor' => [
                                'x' => self::rem(((float) $bridgeBox['xStart'] + (float) $bridgeBox['xEnd']) / 2),
                                'y' => self::rem(((float) $bridgeBox['yStart'] + (float) $bridgeBox['yEnd']) / 2),
                            ],
                            'boxes' => [
                                [
                                    'type' => 'branch-end',
                                    'id' => $endSegmentBox['id'],
                                    ...self::boxToRem($endSegmentBox),
                                ],
                                [
                                    'type' => 'bridge',
                                    'id' => $bridgeBox['id'],
                                    ...self::boxToRem($bridgeBox),
                                ],
                            ],
                            'suggestion' => 'side-switch-or-extension-candidate',
                        ],
                    ],
                ];
            }
        }

        return $branches;
    }

    /**
     * @param  array<string, mixed>  $branch
     * @return array{xStart: float, xEnd: float, yStart: float, yEnd: float}
     */
    private static function labelBounds(array $branch): array
    {
        return self::unionBoxes(collect(self::branchDebugBoxes($branch, 0))
            ->filter(static fn(array $box): bool => in_array((string) data_get($box, 'type'), ['step-label', 'stem-labels', 'end-label'], true))
            ->values()
            ->all());
    }

    /**
     * @param  array<string, mixed>  $branch
     * @return array<int, array{id: string, type: string, branchIndex: int, xStart: float, xEnd: float, yStart: float, yEnd: float}>
     */
    private static function branchDebugBoxes(array $branch, int $branchIndex, bool $includePromotedStepEndLabels = true): array
    {
        $boxes = self::bridgeBoxes($branch, $branchIndex);
        $side = (string) ($branch['side'] ?? 'left');
        $anchorY = self::anchorY($branch);
        $bridgeLength = self::toRem($branch['bridge_length'] ?? '3.5rem');
        $entryStemLength = self::toRem($branch['entry_stem_length'] ?? '0rem');
        $arcSize = self::toRem($branch['arc_size'] ?? '2.75rem') ?: 2.75;
        $direction = $side === 'left' ? -1.0 : 1.0;
        $stemX = $direction * ($bridgeLength + (2 * $arcSize));
        $arcOutY = $anchorY + $entryStemLength + (2 * $arcSize);
        $step = (array) ($branch['step'] ?? []);
        $stepHeight = self::stepHeight($step);
        $stepEndY = $arcOutY + $stepHeight;
        $stemEntries = self::effectiveStemEntries($branch);

        if ($stepHeight > 0.0) {
            $stepBounds = [
                'id' => self::branchId($branch) . '.main.path.branch.step.bounds',
                'type' => 'step-label',
                'branchIndex' => $branchIndex,
                'xStart' => $stemX - 0.75,
                'xEnd' => $stemX + 0.75,
                'yStart' => $arcOutY,
                'yEnd' => $stepEndY,
            ];

            $stepLabel = (array) data_get($step, 'stepLabel', []);
            if (filled(data_get($stepLabel, 'text'))) {
                $stepBounds = self::expandBoxForLabelAt($stepBounds, $stepLabel, $stemX, $arcOutY + self::toRem(data_get($step, 'beforeLength', '1.5rem')) + (self::toRem(data_get($step, 'labelGap', '3.75rem')) / 2), 'center', 0.0);
            }

            if ($includePromotedStepEndLabels) {
                foreach (self::promotedStepEndLabels($branch) as $label) {
                    $stepBounds = self::expandBoxForLabelAt($stepBounds, $label, $stemX, $stepEndY);
                }
            }

            $boxes[] = $stepBounds;
        }

        $stemBounds = [
            'id' => self::branchId($branch) . '.main.path.branch.stem-labels.bounds',
            'type' => 'stem-labels',
            'branchIndex' => $branchIndex,
            'xStart' => $stemX - 0.75,
            'xEnd' => $stemX + 0.75,
            'yStart' => $stepEndY,
            'yEnd' => $stepEndY,
        ];
        $stemY = $stepEndY;

        foreach ($stemEntries as $stemEntry) {
            $stemY += self::toRem(data_get($stemEntry, 'length', is_array($stemEntry) ? data_get($stemEntry, 0, $branch['stem_length'] ?? '3.5rem') : $stemEntry));

            foreach (self::labelsFromStemEntry($stemEntry) as $label) {
                $stemBounds = self::expandBoxForLabelAt($stemBounds, $label, $stemX, $stemY);
            }
        }

        $endLength = self::toRem($branch['end_length'] ?? '3rem');
        $endLabel = (array) ($branch['end_label'] ?? []);
        $hasEndBounds = $endLength > 0.0 || filled(data_get($endLabel, 'text'));
        if ($hasEndBounds) {
            $endBounds = [
                'id' => self::branchId($branch) . '.end.path.branch-end.segment.bounds',
                'type' => 'end-label',
                'branchIndex' => $branchIndex,
                'xStart' => $stemX - 0.75,
                'xEnd' => $stemX + 0.75,
                'yStart' => $stemY,
                'yEnd' => $stemY + $endLength,
            ];

            if (filled(data_get($endLabel, 'text'))) {
                $endBounds = self::expandBoxForLabelAt(
                    $endBounds,
                    $endLabel,
                    $stemX,
                    $stemY + $endLength,
                    (string) data_get($endLabel, 'side', 'top'),
                    self::toRem(data_get($endLabel, 'offset', '0.75rem')),
                );
            }

            $stemBounds = [
                ...$stemBounds,
                ...self::unionBoxes([$stemBounds, $endBounds]),
            ];
            $boxes[] = $endBounds;
        }

        if ($stemY > $stepEndY || $hasEndBounds || $stemBounds['xStart'] !== $stemX - 0.75 || $stemBounds['xEnd'] !== $stemX + 0.75) {
            $stemBounds['yEnd'] = max($stemBounds['yEnd'], $stemY);
            $boxes[] = $stemBounds;
        }

        return $boxes;
    }

    /**
     * Keep branch sub-bounds derived from the same coordinates as the verified
     * branch main bounds. They are report-only inspection zones and must not
     * drive or mutate visible branch geometry.
     *
     * @param  array<string, mixed>  $branch
     * @return array<int, array{id: string, type: string, branchIndex: int, xStart: float, xEnd: float, yStart: float, yEnd: float}>
     */
    private static function branchSubDebugBoxes(array $branch, int $branchIndex): array
    {
        return collect(self::branchDebugBoxes($branch, $branchIndex, false))
            ->filter(static fn(array $box): bool => in_array((string) data_get($box, 'type'), ['step-label', 'stem-labels', 'end-label'], true))
            ->map(static function (array $box) use ($branch): array {
                $type = (string) $box['type'];
                $idSuffix = match ($type) {
                    'step-label' => '.main.path.branch.start.bounds',
                    'stem-labels' => '.main.path.branch.body.bounds',
                    'end-label' => '.end.path.branch-end.bounds',
                    default => '.sub.bounds',
                };

                return [
                    ...$box,
                    'id' => self::branchId($branch) . $idSuffix,
                    'type' => match ($type) {
                        'step-label' => 'branch-start',
                        'stem-labels' => 'branch-body',
                        'end-label' => 'branch-end',
                        default => 'branch-sub',
                    },
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @param  array<string, mixed>  $branch
     * @return array<int, array{id: string, branchIndex: int, xStart: float, xEnd: float, yStart: float, yEnd: float}>
     */
    private static function bridgeBoxes(array $branch, int $branchIndex): array
    {
        $side = (string) ($branch['side'] ?? 'left');
        $anchorY = self::anchorY($branch);
        $bridgeLength = self::toRem($branch['bridge_length'] ?? '3.5rem');
        $entryStemLength = self::toRem($branch['entry_stem_length'] ?? '0rem');
        $arcSize = self::toRem($branch['arc_size'] ?? '2.75rem') ?: 2.75;
        $direction = $side === 'left' ? -1.0 : 1.0;
        $xStart = $direction * $arcSize;
        $xEnd = $direction * ($arcSize + $bridgeLength);
        $y = $anchorY + $entryStemLength + $arcSize;
        $halfHeight = self::BRIDGE_HEIGHT_REM / 2;

        return [[
            'id' => self::branchId($branch) . '.main.path.branch.bridge1',
            'type' => 'bridge',
            'branchIndex' => $branchIndex,
            'xStart' => min($xStart, $xEnd),
            'xEnd' => max($xStart, $xEnd),
            'yStart' => $y - $halfHeight,
            'yEnd' => $y + $halfHeight,
        ]];
    }

    /**
     * @param  array<string, mixed>  $branch
     * @return array<int, array{id: string, branchIndex: int, xStart: float, xEnd: float, yStart: float, yEnd: float}>
     */
    private static function branchEndSegmentBoxes(array $branch, int $branchIndex): array
    {
        $side = (string) ($branch['side'] ?? 'left');
        $anchorY = self::anchorY($branch);
        $bridgeLength = self::toRem($branch['bridge_length'] ?? '3.5rem');
        $entryStemLength = self::toRem($branch['entry_stem_length'] ?? '0rem');
        $arcSize = self::toRem($branch['arc_size'] ?? '2.75rem') ?: 2.75;
        $endLength = self::toRem($branch['end_length'] ?? '3rem');
        $direction = $side === 'left' ? -1.0 : 1.0;
        $x = $direction * ($bridgeLength + (2 * $arcSize));
        $yStart = $anchorY
            + $entryStemLength
            + (2 * $arcSize)
            + self::stepHeight((array) ($branch['step'] ?? []))
            + self::stemLabelHeight(self::effectiveStemEntries($branch));
        $halfWidth = max(self::END_SEGMENT_WIDTH_REM, self::LABEL_REACH_REM) / 2;

        return [[
            'id' => self::branchId($branch) . '.end.path.branch-end.segment',
            'branchIndex' => $branchIndex,
            'xStart' => $x - $halfWidth,
            'xEnd' => $x + $halfWidth,
            'yStart' => $yStart,
            'yEnd' => $yStart + $endLength + self::textLabelHeight((array) ($branch['end_label'] ?? [])),
        ]];
    }

    /**
     * @param  array<string, mixed>  $branch
     */
    private static function anchorY(array $branch): float
    {
        if (is_numeric($branch['anchor_y_rem'] ?? null)) {
            return (float) $branch['anchor_y_rem'];
        }

        return self::attachIndex((string) ($branch['attach_to'] ?? '')) * self::TRUNK_STEP_REM;
    }

    /**
     * @param  array<string, mixed>  $step
     */
    private static function stepHeight(array $step): float
    {
        if ($step === [] || blank(data_get($step, 'stepLabel.text'))) {
            return 0.0;
        }

        $text = data_get($step, 'stepLabel.text');
        $lineCount = collect(is_iterable($text) && ! is_string($text) ? $text : [$text])
            ->filter(static fn(mixed $line): bool => filled($line))
            ->take(3)
            ->count();
        $autoGap = match ($lineCount) {
            1 => 2.75,
            2 => 3.75,
            3 => 4.75,
            default => 3.75,
        };

        return self::toRem(data_get($step, 'beforeLength', '1.5rem'))
            + self::toRem(data_get($step, 'labelGap', self::rem($autoGap)))
            + self::toRem(data_get($step, 'afterLength', '2.5rem'));
    }

    /**
     * @param  array<string, mixed>  $label
     */
    private static function textLabelHeight(array $label): float
    {
        if ($label === [] || blank(data_get($label, 'text'))) {
            return 0.0;
        }

        $text = data_get($label, 'text');
        $lineCount = collect(is_iterable($text) && ! is_string($text) ? $text : [$text])
            ->filter(static fn(mixed $line): bool => filled($line))
            ->take((int) data_get($label, 'maxLines', 3))
            ->count();

        return match ($lineCount) {
            1 => 2.0,
            2 => 3.0,
            3 => 4.0,
            default => 4.5,
        };
    }

    /**
     * @param  array<string, mixed>  $label
     */
    private static function textLabelWidth(array $label): float
    {
        if ((bool) data_get($label, 'long', false)) {
            return 25.0;
        }

        if ((bool) data_get($label, 'halfLong', false)) {
            return 19.0;
        }

        return 13.0;
    }

    /**
     * @param  array<string, float|int|string>  $box
     * @param  array<string, mixed>  $label
     * @return array<string, mixed>
     */
    private static function expandBoxForLabelAt(
        array $box,
        array $label,
        float $anchorX,
        float $anchorY,
        ?string $side = null,
        ?float $offset = null,
    ): array {
        if ($label === [] || blank(data_get($label, 'text'))) {
            return $box;
        }

        $side ??= (string) data_get($label, 'side', 'right');
        $offset ??= 0.95 / 2
            + self::toRem(data_get($label, 'connectorLength', '2rem'))
            + self::toRem(data_get($label, 'connectorGap', '0.25rem'));
        $width = self::textLabelWidth($label);
        $height = self::textLabelHeight($label);

        [$xStart, $xEnd, $yStart, $yEnd] = match ($side) {
            'left' => [$anchorX - $offset - $width, $anchorX - $offset, $anchorY - ($height / 2), $anchorY + ($height / 2)],
            'right' => [$anchorX + $offset, $anchorX + $offset + $width, $anchorY - ($height / 2), $anchorY + ($height / 2)],
            'top' => [$anchorX - ($width / 2), $anchorX + ($width / 2), $anchorY + $offset, $anchorY + $offset + $height],
            'bottom' => [$anchorX - ($width / 2), $anchorX + ($width / 2), $anchorY - $offset - $height, $anchorY - $offset],
            'center' => [$anchorX - ($width / 2), $anchorX + ($width / 2), $anchorY - ($height / 2), $anchorY + ($height / 2)],
            default => [$anchorX + $offset, $anchorX + $offset + $width, $anchorY - ($height / 2), $anchorY + ($height / 2)],
        };

        return [
            ...$box,
            'xStart' => min((float) $box['xStart'], $xStart),
            'xEnd' => max((float) $box['xEnd'], $xEnd),
            'yStart' => min((float) $box['yStart'], $yStart),
            'yEnd' => max((float) $box['yEnd'], $yEnd),
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $boxes
     * @return array{xStart: float, xEnd: float, yStart: float, yEnd: float}
     */
    private static function unionBoxes(array $boxes): array
    {
        if ($boxes === []) {
            return [
                'xStart' => 0.0,
                'xEnd' => 0.0,
                'yStart' => 0.0,
                'yEnd' => 0.0,
            ];
        }

        return [
            'xStart' => min(array_map(static fn(array $box): float => (float) $box['xStart'], $boxes)),
            'xEnd' => max(array_map(static fn(array $box): float => (float) $box['xEnd'], $boxes)),
            'yStart' => min(array_map(static fn(array $box): float => (float) $box['yStart'], $boxes)),
            'yEnd' => max(array_map(static fn(array $box): float => (float) $box['yEnd'], $boxes)),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private static function labelsFromStemEntry(mixed $stemEntry): array
    {
        if (! is_array($stemEntry)) {
            return [];
        }

        $labels = [];
        $commonOptions = collect($stemEntry)
            ->reject(static fn(mixed $value, int|string $key): bool => is_int($key)
                || in_array($key, [
                    'length',
                    'compressed',
                    'force',
                    'render',
                    'spacer',
                    'left',
                    'right',
                    'top',
                    'bottom',
                    'labels',
                    'label',
                    'labelA',
                    'labelB',
                ], true))
            ->all();

        foreach (['left', 'right', 'top', 'bottom'] as $side) {
            if (! array_key_exists($side, $stemEntry) || blank($stemEntry[$side])) {
                continue;
            }

            $label = $stemEntry[$side];
            $labelOptions = is_array($label) && ! array_is_list($label) ? $label : [];
            $labelText = is_array($label) && array_key_exists('text', $label)
                ? data_get($label, 'text')
                : $label;

            $labels[] = [
                ...$commonOptions,
                ...$labelOptions,
                'side' => $side,
                'text' => $labelText,
            ];
        }

        $directLabels = (array) data_get($stemEntry, 'labels', []);
        foreach ($directLabels as $side => $label) {
            if (! in_array((string) $side, ['left', 'right', 'top', 'bottom'], true) || blank($label)) {
                continue;
            }

            $labelOptions = is_array($label) && ! array_is_list($label) ? $label : [];
            $labelText = is_array($label) && array_key_exists('text', $label)
                ? data_get($label, 'text')
                : $label;

            $labels[] = [
                ...$commonOptions,
                ...$labelOptions,
                'side' => (string) $side,
                'text' => $labelText,
            ];
        }

        return collect($labels)
            ->filter(static fn(array $label): bool => filled(data_get($label, 'text')))
            ->values()
            ->all();
    }

    /**
     * @param  array<string, mixed>  $branch
     * @return array<int, array<string, mixed>>
     */
    private static function promotedStepEndLabels(array $branch): array
    {
        if (blank(data_get($branch, 'step.stepLabel.text'))) {
            return [];
        }

        $stemEntries = (array) ($branch['stem_continuation'] ?? []);
        if ($stemEntries === []) {
            return [];
        }

        $firstStemEntry = $stemEntries[array_key_first($stemEntries)];
        if (
            ! is_array($firstStemEntry)
            || ! self::hasStemLabels($firstStemEntry)
            || (bool) data_get($firstStemEntry, 'compressed', false)
            || (bool) data_get($firstStemEntry, 'force', false)
            || (bool) data_get($firstStemEntry, 'render', false)
            || (bool) data_get($firstStemEntry, 'spacer', false)
        ) {
            return [];
        }

        return self::labelsFromStemEntry($firstStemEntry);
    }

    private static function hasStemLabels(mixed $stemEntry): bool
    {
        if (! is_array($stemEntry)) {
            return false;
        }

        foreach (['left', 'right', 'top', 'bottom', 'label', 'labelA', 'labelB'] as $key) {
            if (filled(data_get($stemEntry, $key))) {
                return true;
            }
        }

        $labels = data_get($stemEntry, 'labels');

        return is_array($labels)
            && collect($labels)->filter(static fn(mixed $label): bool => filled($label))->isNotEmpty();
    }

    /**
     * @param  array<string, mixed>  $branch
     * @return array<int|string, mixed>
     */
    private static function effectiveStemEntries(array $branch): array
    {
        $stemEntries = (array) ($branch['stem_continuation'] ?? []);

        if (
            $stemEntries === []
            || blank(data_get($branch, 'step.stepLabel.text'))
        ) {
            return $stemEntries;
        }

        $firstStemKey = array_key_first($stemEntries);
        $firstStemEntry = $stemEntries[$firstStemKey];
        $firstStemIsPromotable = is_array($firstStemEntry)
            && self::hasStemLabels($firstStemEntry)
            && ! (bool) data_get($firstStemEntry, 'compressed', false)
            && ! (bool) data_get($firstStemEntry, 'force', false)
            && ! (bool) data_get($firstStemEntry, 'render', false)
            && ! (bool) data_get($firstStemEntry, 'spacer', false);

        if ($firstStemIsPromotable) {
            unset($stemEntries[$firstStemKey]);
        }

        return $stemEntries;
    }

    private static function branchId(array $branch): string
    {
        return (string) ($branch['id'] ?? ('strang.branch-' . (string) ($branch['side'] ?? 'left') . '.' . (string) ($branch['component_counter'] ?? '1')));
    }

    /**
     * @param  array<int|string, mixed>  $stemContinuation
     */
    private static function stemLabelHeight(array $stemContinuation): float
    {
        return collect($stemContinuation)
            ->map(static fn(mixed $entry): float => self::toRem(data_get($entry, 'length', is_array($entry) ? data_get($entry, 0, '3.5rem') : $entry)))
            ->sum() ?: 5.25;
    }

    private static function overlaps(array $first, array $second): bool
    {
        $xOverlaps = $first['xStart'] < ($second['xEnd'] + self::LABEL_GAP_REM)
            && $second['xStart'] < ($first['xEnd'] + self::LABEL_GAP_REM);
        $yOverlaps = $first['yStart'] < ($second['yEnd'] + self::LABEL_GAP_REM)
            && $second['yStart'] < ($first['yEnd'] + self::LABEL_GAP_REM);

        return $xOverlaps && $yOverlaps;
    }

    private static function overlapSize(array $first, array $second): float
    {
        return max(
            0.0,
            min((float) $first['yEnd'], (float) $second['yEnd'])
                - max((float) $first['yStart'], (float) $second['yStart']),
        );
    }

    /**
     * @param  array{xStart: float, xEnd: float, yStart: float, yEnd: float}  $box
     * @return array{x: string, y: string, width: string, height: string}
     */
    private static function boxToRem(array $box): array
    {
        return [
            'x' => self::rem((float) $box['xStart']),
            'y' => self::rem((float) $box['yStart']),
            'width' => self::rem(max(0.0, (float) $box['xEnd'] - (float) $box['xStart'])),
            'height' => self::rem(max(0.0, (float) $box['yEnd'] - (float) $box['yStart'])),
        ];
    }

    private static function attachIndex(string $attachTo): int
    {
        if (preg_match('/strang\.trunk\.path\.(\d+)\.end/', $attachTo, $matches) !== 1) {
            return 0;
        }

        return max(0, (int) $matches[1]);
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
}
