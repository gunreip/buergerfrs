<?php

declare(strict_types=1);

namespace Gunreip\TranslationWorkbench\Support\TwGraph\DataDriven\TimelineChainGraphData;

final class BranchLabelCollisionResolver
{
    private const TRUNK_STEP_REM = 6.0;
    private const ARC_STACK_REM = 5.5;
    private const LABEL_REACH_REM = 14.0;
    private const LABEL_GAP_REM = 2.0;
    private const BRIDGE_HEIGHT_REM = 1.5;
    private const END_SEGMENT_WIDTH_REM = 1.5;
    private const BRIDGE_INCREMENT_REM = 33.0;

    /**
     * @param  array<int, array<string, mixed>>  $branches
     * @return array<int, array<string, mixed>>
     */
    public static function resolve(array $branches): array
    {
        $indexedBranches = collect($branches)
            ->values()
            ->map(static fn(array $branch, int $index): array => [
                'index' => $index,
                'branch' => $branch,
                'bounds' => self::labelBounds($branch),
            ]);
        $updates = [];

        foreach (['left', 'right'] as $side) {
            $sideBranches = $indexedBranches
                ->filter(static fn(array $entry): bool => (string) data_get($entry, 'branch.side') === $side)
                ->sortBy(static fn(array $entry): array => [
                    (float) data_get($entry, 'bounds.yStart', 0.0),
                    (int) data_get($entry, 'branch.component_counter', 0),
                ])
                ->values();

            foreach ($sideBranches as $position => $entry) {
                $next = $sideBranches->get($position + 1);

                if ($next === null || ! self::overlaps($entry['bounds'], $next['bounds'])) {
                    continue;
                }

                $index = (int) $entry['index'];
                $currentLength = self::toRem(data_get($branches, "{$index}.bridge_length", '28rem'));
                $updates[$index] = max($updates[$index] ?? $currentLength, $currentLength + self::BRIDGE_INCREMENT_REM);
            }
        }

        foreach ($updates as $index => $length) {
            $branches[$index]['bridge_length'] = self::rem($length);
            $branches[$index]['layout'] = [
                ...((array) ($branches[$index]['layout'] ?? [])),
                'branchLabelCollision' => [
                    'bridgeIncrement' => self::rem(self::BRIDGE_INCREMENT_REM),
                    'reason' => 'stem label bounds overlap the next branch on the same side',
                ],
            ];
        }

        return self::withBranchEndBridgeWarnings($branches);
    }

    /**
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
        $side = (string) ($branch['side'] ?? 'left');
        $attachIndex = self::attachIndex((string) ($branch['attach_to'] ?? ''));
        $bridgeLength = self::toRem($branch['bridge_length'] ?? '28rem');
        $stemLength = self::stemLabelHeight(self::effectiveStemEntries($branch));
        $x = $side === 'left' ? -$bridgeLength : $bridgeLength;

        return [
            'xStart' => $x - self::LABEL_REACH_REM,
            'xEnd' => $x + self::LABEL_REACH_REM,
            'yStart' => ($attachIndex * self::TRUNK_STEP_REM) + self::ARC_STACK_REM,
            'yEnd' => ($attachIndex * self::TRUNK_STEP_REM) + self::ARC_STACK_REM + $stemLength,
        ];
    }

    /**
     * @param  array<string, mixed>  $branch
     * @return array<int, array{id: string, branchIndex: int, xStart: float, xEnd: float, yStart: float, yEnd: float}>
     */
    private static function bridgeBoxes(array $branch, int $branchIndex): array
    {
        $side = (string) ($branch['side'] ?? 'left');
        $attachIndex = self::attachIndex((string) ($branch['attach_to'] ?? ''));
        $bridgeLength = self::toRem($branch['bridge_length'] ?? '28rem');
        $entryStemLength = self::toRem($branch['entry_stem_length'] ?? '0rem');
        $arcSize = self::toRem($branch['arc_size'] ?? '2.75rem') ?: 2.75;
        $direction = $side === 'left' ? -1.0 : 1.0;
        $xStart = $direction * $arcSize;
        $xEnd = $direction * ($arcSize + $bridgeLength);
        $y = ($attachIndex * self::TRUNK_STEP_REM) + $entryStemLength + $arcSize;
        $halfHeight = self::BRIDGE_HEIGHT_REM / 2;

        return [[
            'id' => self::branchId($branch) . '.main.path.branch.bridge1',
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
        $attachIndex = self::attachIndex((string) ($branch['attach_to'] ?? ''));
        $bridgeLength = self::toRem($branch['bridge_length'] ?? '28rem');
        $entryStemLength = self::toRem($branch['entry_stem_length'] ?? '0rem');
        $arcSize = self::toRem($branch['arc_size'] ?? '2.75rem') ?: 2.75;
        $endLength = self::toRem($branch['end_length'] ?? '3rem');
        $direction = $side === 'left' ? -1.0 : 1.0;
        $x = $direction * ($bridgeLength + (2 * $arcSize));
        $yStart = ($attachIndex * self::TRUNK_STEP_REM)
            + $entryStemLength
            + (2 * $arcSize)
            + self::stepHeight((array) ($branch['step'] ?? []))
            + self::stemLabelHeight(self::effectiveStemEntries($branch));
        $halfWidth = self::END_SEGMENT_WIDTH_REM / 2;

        return [[
            'id' => self::branchId($branch) . '.end.path.branch-end.segment',
            'branchIndex' => $branchIndex,
            'xStart' => $x - $halfWidth,
            'xEnd' => $x + $halfWidth,
            'yStart' => $yStart,
            'yEnd' => $yStart + $endLength,
        ]];
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

        if ($stemEntries === []
            || blank(data_get($branch, 'step.stepLabel.text'))) {
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
            ->map(static fn(mixed $entry): float => self::toRem(data_get($entry, 'length', is_array($entry) ? data_get($entry, 0, '5.25rem') : $entry)))
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
