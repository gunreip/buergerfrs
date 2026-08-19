{{-- packages/gunreip/laravel-translation-workbench/resources/views/components/ui/tw-graph-protocol/strangs/branch-right.blade.php --}}
{{--
    Strang: branch-right

    Usage:
    <x-translation-workbench::ui.tw-graph-protocol.strangs.branch-right
        :anchor-start="['x' => '0rem', 'y' => '0rem']"
        extension-count="2"
        connector-length="3rem"
        vertical-length="2rem"
        branch-end-path-length="2rem"
        :branch-return="true"
        branch-return-vertical-length="3rem"
        branch-return-connector-length="5rem"
        :extension-end-path-lengths="[1 => '2rem', 2 => '2rem']"
        :extension-branch-indexes="[2]"
        :extension-colors="[1 => 'sky', 2 => 'cyan']"
        :extension-return-indexes="[1]"
        :extension-return-colors="[1 => 'cyan']"
        :extension-branch-return-indexes="[2]"
        :extension-branch-return-colors="[2 => 'red']"
    />

    Strang role:
    Keeps the right branch layer explicit and passes its geometry intent to
    paths.branch right. Branch extensions attach to the horizontal connector
    nodeEnd, not to the final vertical branch end.

    DEV bounds are resolved through the shared TwGraphProtocol GeometryBounds
    helper so the visible protocol box follows the same point list that can
    later be written to/read from the graph protocol cache.
--}}

@props([
    // Identity / rendering state
    'id' => 'strang.branch-right',
    'dev' => false,

    // Base anchor / shared geometry
    'anchorStart' => ['x' => '0rem', 'y' => '0rem'],
    'arcSize' => '2.75rem',
    'color' => 'violet',
    'zIndex' => 40,

    // Main branch: paths.branch right
    'connectorLength' => '3rem',
    'verticalLength' => '2rem',
    'branchEndPathLength' => null,
    'branchContinuationNodeLabels' => true,

    // Main branch return: paths.branch-return right from branch.vertical.continuation
    'branchReturn' => false,
    'branchReturnVerticalLength' => '3rem',
    'branchReturnConnectorLength' => '5rem',
    'branchReturnColor' => null,

    // Branch extensions: paths.branch-extension right[]
    'extensionCount' => 0,
    'extensionConnectorLengths' => [],
    'extensionVerticalLengths' => [],
    'extensionEndPathLengths' => [],
    'extensionContinuationNodeLabels' => [],
    'extensionColors' => [],

    // Branches from extension vertical endpoints: paths.branch right[]
    'extensionBranchIndexes' => [],
    'extensionBranchConnectorLengths' => [],
    'extensionBranchVerticalLengths' => [],
    'extensionBranchColors' => [],

    // Returns from extension branch endpoints: paths.branch-return-extension right[]
    'extensionBranchReturnIndexes' => [],
    'extensionBranchReturnVerticalLengths' => [],
    'extensionBranchReturnConnectorLengths' => [],
    'extensionBranchReturnColors' => [],

    // Returns from extension continuations: paths.branch-return right[]
    'extensionReturnIndexes' => [],
    'extensionReturnVerticalLengths' => [],
    'extensionReturnConnectorLengths' => [],
    'extensionReturnColors' => [],
])

@php
    $add = fn (string $value, string $delta): string => $delta === '0rem' ? $value : 'calc(' . $value . ' + ' . $delta . ')';
    $neg = fn (string $value): string => 'calc(' . $value . ' * -1)';
    $subtract = fn (string $value, string $delta): string => $add($value, $neg($delta));
    $anchor = [
        'x' => data_get($anchorStart, 'x', '0rem'),
        'y' => data_get($anchorStart, 'y', '0rem'),
    ];
    $extensionCount = max(0, (int) $extensionCount);
    $extensionResolvedConnectorLengths = [];
    $extensionResolvedVerticalLengths = [];
    $extensionResolvedZIndexes = [];
    $extensionAnchors = [];
    $extensionEnds = [];
    $strangPoints = [$anchor];
    $currentExtensionAnchor = null;
    $branchContinuationCounter = 5;
    $branchReturnCounterStart = $branchContinuationCounter + 1;
    $extensionContinuationCounter = 4;
    $extensionBranchCounterStart = $extensionContinuationCounter + 1;
    $extensionBranchReturnCounterStart = $extensionBranchCounterStart + 4;
    $extensionReturnCounterStart = $extensionContinuationCounter + 1;

    $branchConnectorEnd = [
        'x' => $add($add($anchor['x'], $arcSize), $connectorLength),
        'y' => $add($anchor['y'], $arcSize),
    ];
    $branchEnd = [
        'x' => $add($branchConnectorEnd['x'], $arcSize),
        'y' => $add($add($branchConnectorEnd['y'], $arcSize), $verticalLength),
    ];
    $branchContinuationEnd = filled($branchEndPathLength)
        ? ['x' => $branchEnd['x'], 'y' => $add($branchEnd['y'], (string) $branchEndPathLength)]
        : null;
    $strangPoints[] = $branchConnectorEnd;
    $strangPoints[] = $branchEnd;
    if (filled($branchContinuationEnd)) {
        $strangPoints[] = $branchContinuationEnd;
    }

    $hasBranchReturn = (bool) $branchReturn && filled($branchContinuationEnd);
    $branchReturnColor = filled($branchReturnColor) ? (string) $branchReturnColor : $color;
    $branchReturnEnd = null;
    if ($hasBranchReturn) {
        $branchReturnVerticalEnd = [
            'x' => $branchContinuationEnd['x'],
            'y' => $add($branchContinuationEnd['y'], (string) $branchReturnVerticalLength),
        ];
        $branchReturnArcInEnd = [
            'x' => $subtract($branchReturnVerticalEnd['x'], $arcSize),
            'y' => $add($branchReturnVerticalEnd['y'], $arcSize),
        ];
        $branchReturnConnectorEnd = [
            'x' => $subtract($branchReturnArcInEnd['x'], (string) $branchReturnConnectorLength),
            'y' => $branchReturnArcInEnd['y'],
        ];
        $branchReturnEnd = [
            'x' => $subtract($branchReturnConnectorEnd['x'], $arcSize),
            'y' => $add($branchReturnConnectorEnd['y'], $arcSize),
        ];
        $strangPoints[] = $branchReturnVerticalEnd;
        $strangPoints[] = $branchReturnArcInEnd;
        $strangPoints[] = $branchReturnConnectorEnd;
        $strangPoints[] = $branchReturnEnd;
    }

    $currentExtensionAnchor = $branchConnectorEnd;
    for ($extensionIndex = 1; $extensionIndex <= $extensionCount; $extensionIndex++) {
        $extensionConnectorLength = (string) data_get($extensionConnectorLengths, $extensionIndex, $connectorLength);
        $extensionVerticalLength = (string) data_get($extensionVerticalLengths, $extensionIndex, $verticalLength);
        $extensionEndPathLength = data_get($extensionEndPathLengths, $extensionIndex);
        $extensionColor = (string) data_get($extensionColors, $extensionIndex, $color);
        $hasExtensionBranch = in_array($extensionIndex, collect($extensionBranchIndexes)->map(fn (mixed $index): int => (int) $index)->all(), true);
        $hasExtensionReturn = in_array($extensionIndex, collect($extensionReturnIndexes)->map(fn (mixed $index): int => (int) $index)->all(), true);
        $hasExtensionBranchReturn = in_array($extensionIndex, collect($extensionBranchReturnIndexes)->map(fn (mixed $index): int => (int) $index)->all(), true);
        $extensionBranchConnectorLength = (string) data_get($extensionBranchConnectorLengths, $extensionIndex, $connectorLength);
        $extensionBranchVerticalLength = (string) data_get($extensionBranchVerticalLengths, $extensionIndex, $verticalLength);
        $extensionResolvedConnectorLengths[$extensionIndex] = $extensionConnectorLength;
        $extensionResolvedVerticalLengths[$extensionIndex] = $extensionVerticalLength;
        $extensionResolvedZIndexes[$extensionIndex] = max(1, (int) $zIndex - ($extensionIndex * 5));
        $extensionAnchors[$extensionIndex] = $currentExtensionAnchor;
        $extensionConnectorEnd = [
            'x' => $add($currentExtensionAnchor['x'], $extensionConnectorLength),
            'y' => $currentExtensionAnchor['y'],
        ];
        $extensionEnd = [
            'x' => $add($extensionConnectorEnd['x'], $arcSize),
            'y' => $add($add($extensionConnectorEnd['y'], $arcSize), $extensionVerticalLength),
        ];
        $extensionEnds[$extensionIndex] = $extensionEnd;
        $extensionContinuationEnd = filled($extensionEndPathLength)
            ? ['x' => $extensionEnd['x'], 'y' => $add($extensionEnd['y'], (string) $extensionEndPathLength)]
            : null;
        $strangPoints[] = $currentExtensionAnchor;
        $strangPoints[] = $extensionConnectorEnd;
        $strangPoints[] = $extensionEnd;
        if (filled($extensionContinuationEnd)) {
            $strangPoints[] = $extensionContinuationEnd;
        }

        if ($hasExtensionBranch) {
            $extensionBranchConnectorEnd = [
                'x' => $add($add($extensionEnd['x'], $arcSize), $extensionBranchConnectorLength),
                'y' => $add($extensionEnd['y'], $arcSize),
            ];
            $extensionBranchEnd = [
                'x' => $add($extensionBranchConnectorEnd['x'], $arcSize),
                'y' => $add($add($extensionBranchConnectorEnd['y'], $arcSize), $extensionBranchVerticalLength),
            ];
            $strangPoints[] = $extensionBranchConnectorEnd;
            $strangPoints[] = $extensionBranchEnd;

            if ($hasExtensionBranchReturn) {
                $extensionBranchReturnVerticalLength = (string) data_get($extensionBranchReturnVerticalLengths, $extensionIndex, $branchReturnVerticalLength);
                $extensionBranchReturnConnectorLength = (string) data_get($extensionBranchReturnConnectorLengths, $extensionIndex, $branchReturnConnectorLength);
                $extensionBranchReturnVerticalEnd = [
                    'x' => $extensionBranchEnd['x'],
                    'y' => $add($extensionBranchEnd['y'], $extensionBranchReturnVerticalLength),
                ];
                $extensionBranchReturnArcEnd = [
                    'x' => $subtract($extensionBranchReturnVerticalEnd['x'], $arcSize),
                    'y' => $add($extensionBranchReturnVerticalEnd['y'], $arcSize),
                ];
                $extensionBranchReturnEnd = [
                    'x' => $subtract($extensionBranchReturnArcEnd['x'], $extensionBranchReturnConnectorLength),
                    'y' => $extensionBranchReturnArcEnd['y'],
                ];
                $strangPoints[] = $extensionBranchReturnVerticalEnd;
                $strangPoints[] = $extensionBranchReturnArcEnd;
                $strangPoints[] = $extensionBranchReturnEnd;
            }
        }

        if ($hasExtensionReturn && filled($extensionContinuationEnd)) {
            $extensionReturnVerticalLength = (string) data_get($extensionReturnVerticalLengths, $extensionIndex, $branchReturnVerticalLength);
            $extensionReturnConnectorLength = (string) data_get($extensionReturnConnectorLengths, $extensionIndex, $branchReturnConnectorLength);
            $extensionReturnVerticalEnd = [
                'x' => $extensionContinuationEnd['x'],
                'y' => $add($extensionContinuationEnd['y'], $extensionReturnVerticalLength),
            ];
            $extensionReturnArcInEnd = [
                'x' => $subtract($extensionReturnVerticalEnd['x'], $arcSize),
                'y' => $add($extensionReturnVerticalEnd['y'], $arcSize),
            ];
            $extensionReturnConnectorEnd = [
                'x' => $subtract($extensionReturnArcInEnd['x'], $extensionReturnConnectorLength),
                'y' => $extensionReturnArcInEnd['y'],
            ];
            $extensionReturnEnd = [
                'x' => $subtract($extensionReturnConnectorEnd['x'], $arcSize),
                'y' => $add($extensionReturnConnectorEnd['y'], $arcSize),
            ];
            $strangPoints[] = $extensionReturnVerticalEnd;
            $strangPoints[] = $extensionReturnArcInEnd;
            $strangPoints[] = $extensionReturnConnectorEnd;
            $strangPoints[] = $extensionReturnEnd;
        }

        $currentExtensionAnchor = [
            'x' => $extensionConnectorEnd['x'],
            'y' => $extensionConnectorEnd['y'],
        ];
    }

    $strangBounds = \Gunreip\TranslationWorkbench\Support\TwGraphProtocol\GeometryBounds::fromPoints($strangPoints, '1rem');
    $strangBorderLeft = $strangBounds['left'];
    $strangBorderBottom = $strangBounds['bottom'];
    $strangBorderWidth = $strangBounds['width'];
    $strangBorderHeight = $strangBounds['height'];
    $colorRgb = \Gunreip\TranslationWorkbench\Support\TranslationWorkbenchColorPalette::rgb($color, '139 92 246');
@endphp

@if ($dev)
    <span
        class="tw-graph-protocol-dev-only pointer-events-none absolute rounded-lg border border-dashed"
        style="
            left: calc(var(--tw-graph-protocol-trunk-x) + {{ $strangBorderLeft }});
            bottom: {{ $strangBorderBottom }};
            width: {{ $strangBorderWidth }};
            height: {{ $strangBorderHeight }};
            border-color: rgb({{ $colorRgb }} / 0.6);
        "
        title="{{ $id }} | strang.branch-right"
    >
        <span class="absolute right-2 top-0 -translate-y-1/2">
            <flux:badge
                size="sm"
                color="{{ $color }}"
            >
                {{ $id }}
            </flux:badge>
        </span>
    </span>
@endif

<x-translation-workbench::ui.tw-graph-protocol.paths.branch
    :id="$id . '.branch'"
    side="right"
    :anchor-start="$anchor"
    :connector-length="$connectorLength"
    :vertical-length="$verticalLength"
    :arc-size="$arcSize"
    :color="$color"
    :z-index="$zIndex"
    :dev="$dev"
/>

@if (filled($branchEndPathLength))
    <x-translation-workbench::ui.tw-graph-protocol.segments.path
        :segment="[
            'id' => $id . '.branch.vertical.continuation',
            'direction' => 'bottom-top',
            'length' => (string) $branchEndPathLength,
            'anchorStart' => $branchEnd,
            'anchorEnd' => $branchContinuationEnd,
            'nodeStart' => false,
            'nodeEnd' => $branchContinuationNodeLabels,
            'devCounterEnd' => $branchContinuationCounter,
            'devCounterColor' => $color,
            'color' => $color,
            'zIndex' => $zIndex,
            'dev' => $dev,
        ]"
    />
@endif

@if ($hasBranchReturn)
    <x-translation-workbench::ui.tw-graph-protocol.paths.branch-return
        :id="$id . '.branch.return'"
        side="right"
        :anchor-start="$branchContinuationEnd"
        :vertical-length="$branchReturnVerticalLength"
        :connector-length="$branchReturnConnectorLength"
        :arc-size="$arcSize"
        :color="$branchReturnColor"
        :z-index="$zIndex"
        :counter-start="$branchReturnCounterStart"
        :dev="$dev"
    />
@endif

@foreach ($extensionAnchors as $extensionIndex => $extensionAnchor)
    @php
        $extensionColor = (string) data_get($extensionColors, $extensionIndex, $color);
        $extensionEndPathLength = data_get($extensionEndPathLengths, $extensionIndex);
        $hasExtensionBranch = in_array($extensionIndex, collect($extensionBranchIndexes)->map(fn (mixed $index): int => (int) $index)->all(), true);
        $hasExtensionReturn = in_array($extensionIndex, collect($extensionReturnIndexes)->map(fn (mixed $index): int => (int) $index)->all(), true);
        $hasExtensionBranchReturn = in_array($extensionIndex, collect($extensionBranchReturnIndexes)->map(fn (mixed $index): int => (int) $index)->all(), true);
        $extensionEnd = $extensionEnds[$extensionIndex] ?? null;
        $extensionContinuationEnd = (filled($extensionEndPathLength) && filled($extensionEnd))
            ? ['x' => $extensionEnd['x'], 'y' => $add($extensionEnd['y'], (string) $extensionEndPathLength)]
            : null;
        $extensionBranchConnectorLength = (string) data_get($extensionBranchConnectorLengths, $extensionIndex, $connectorLength);
        $extensionBranchVerticalLength = (string) data_get($extensionBranchVerticalLengths, $extensionIndex, $verticalLength);
        $extensionBranchEnd = filled($extensionEnd)
            ? [
                'x' => $add($add($add($extensionEnd['x'], $arcSize), $extensionBranchConnectorLength), $arcSize),
                'y' => $add($add($extensionEnd['y'], $arcSize), $add($arcSize, $extensionBranchVerticalLength)),
            ]
            : null;
    @endphp

    <x-translation-workbench::ui.tw-graph-protocol.paths.branch-extension
        :id="$id . '.extension.' . $extensionIndex"
        side="right"
        :anchor-start="$extensionAnchor"
        :connector-length="$extensionResolvedConnectorLengths[$extensionIndex]"
        :vertical-length="$extensionResolvedVerticalLengths[$extensionIndex]"
        :arc-size="$arcSize"
        :color="$extensionColor"
        :z-index="$extensionResolvedZIndexes[$extensionIndex]"
        :dev="$dev"
    />

    @if (filled($extensionEndPathLength) && filled($extensionEnd))
        <x-translation-workbench::ui.tw-graph-protocol.segments.path
            :segment="[
                'id' => $id . '.extension.' . $extensionIndex . '.vertical.continuation',
                'direction' => 'bottom-top',
                'length' => (string) $extensionEndPathLength,
                'anchorStart' => $extensionEnd,
                'anchorEnd' => $extensionContinuationEnd,
                'nodeStart' => false,
                'nodeEnd' => data_get($extensionContinuationNodeLabels, $extensionIndex, true),
                'devCounterEnd' => $extensionContinuationCounter,
                'devCounterColor' => $extensionColor,
                'color' => $extensionColor,
                'zIndex' => $extensionResolvedZIndexes[$extensionIndex],
                'dev' => $dev,
            ]"
        />
    @endif

    @if ($hasExtensionBranch && filled($extensionEnd))
        <x-translation-workbench::ui.tw-graph-protocol.paths.branch
            :id="$id . '.extension.' . $extensionIndex . '.branch'"
            side="right"
            :anchor-start="$extensionEnd"
            :connector-length="$extensionBranchConnectorLength"
            :vertical-length="$extensionBranchVerticalLength"
            :arc-size="$arcSize"
            :color="(string) data_get($extensionBranchColors, $extensionIndex, 'red')"
            :z-index="max(1, $extensionResolvedZIndexes[$extensionIndex] - 2)"
            :counter-start="$extensionBranchCounterStart"
            :dev="$dev"
        />
    @endif

    @if ($hasExtensionBranchReturn && filled($extensionBranchEnd))
        <x-translation-workbench::ui.tw-graph-protocol.paths.branch-return-extension
            :id="$id . '.extension.' . $extensionIndex . '.branch.return.extension'"
            side="right"
            :anchor-start="$extensionBranchEnd"
            :vertical-length="(string) data_get($extensionBranchReturnVerticalLengths, $extensionIndex, $branchReturnVerticalLength)"
            :connector-length="(string) data_get($extensionBranchReturnConnectorLengths, $extensionIndex, $branchReturnConnectorLength)"
            :arc-size="$arcSize"
            :color="(string) data_get($extensionBranchReturnColors, $extensionIndex, data_get($extensionBranchColors, $extensionIndex, 'red'))"
            :z-index="max(1, $extensionResolvedZIndexes[$extensionIndex] - 4)"
            :counter-start="$extensionBranchReturnCounterStart"
            :dev="$dev"
        />
    @endif

    @if ($hasExtensionReturn && filled($extensionContinuationEnd))
        <x-translation-workbench::ui.tw-graph-protocol.paths.branch-return
            :id="$id . '.extension.' . $extensionIndex . '.branch.return'"
            side="right"
            :anchor-start="$extensionContinuationEnd"
            :vertical-length="(string) data_get($extensionReturnVerticalLengths, $extensionIndex, $branchReturnVerticalLength)"
            :connector-length="(string) data_get($extensionReturnConnectorLengths, $extensionIndex, $branchReturnConnectorLength)"
            :arc-size="$arcSize"
            :color="(string) data_get($extensionReturnColors, $extensionIndex, $extensionColor)"
            :z-index="max(1, $extensionResolvedZIndexes[$extensionIndex] - 1)"
            :counter-start="$extensionReturnCounterStart"
            :dev="$dev"
        />
    @endif
@endforeach
