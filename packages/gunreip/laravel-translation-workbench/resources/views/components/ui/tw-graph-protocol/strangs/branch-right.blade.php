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
        :extension-end-path-lengths="[1 => '2rem', 2 => '2rem']"
        :extension-branch-indexes="[2]"
        :extension-colors="[1 => 'sky', 2 => 'cyan']"
    />

    Strang role:
    Keeps the right branch layer explicit and passes its geometry intent to
    paths.branch right. Branch extensions attach to the horizontal connector
    nodeEnd, not to the final vertical branch end.
--}}

@props([
    'id' => 'strang.branch-right',
    'anchorStart' => ['x' => '0rem', 'y' => '0rem'],
    'arcSize' => '2.75rem',
    'connectorLength' => '3rem',
    'verticalLength' => '2rem',
    'extensionCount' => 0,
    'extensionConnectorLengths' => [],
    'extensionVerticalLengths' => [],
    'branchEndPathLength' => null,
    'extensionEndPathLengths' => [],
    'extensionBranchIndexes' => [],
    'extensionBranchConnectorLengths' => [],
    'extensionBranchVerticalLengths' => [],
    'extensionBranchColors' => [],
    'extensionColors' => [],
    'color' => 'violet',
    'zIndex' => 40,
    'dev' => false,
])

@php
    $add = fn (string $value, string $delta): string => $delta === '0rem' ? $value : 'calc(' . $value . ' + ' . $delta . ')';
    $subtract = fn (string $value, string $delta): string => $add($value, 'calc(' . $delta . ' * -1)');
    $diff = fn (string $max, string $min): string => 'calc(' . $max . ' - ' . $min . ')';
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
    $currentExtensionAnchor = null;

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

    $strangEnd = $branchContinuationEnd ?? $branchEnd;
    $currentExtensionAnchor = $branchConnectorEnd;
    for ($extensionIndex = 1; $extensionIndex <= $extensionCount; $extensionIndex++) {
        $extensionConnectorLength = (string) data_get($extensionConnectorLengths, $extensionIndex, $connectorLength);
        $extensionVerticalLength = (string) data_get($extensionVerticalLengths, $extensionIndex, $verticalLength);
        $extensionEndPathLength = data_get($extensionEndPathLengths, $extensionIndex);
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
        $strangEnd = $extensionContinuationEnd ?? $extensionEnd;
        $currentExtensionAnchor = [
            'x' => $extensionConnectorEnd['x'],
            'y' => $extensionConnectorEnd['y'],
        ];
    }

    $strangBorderPadding = '1rem';
    $strangBorderLeft = $subtract($anchor['x'], $strangBorderPadding);
    $strangBorderBottom = $subtract($anchor['y'], $strangBorderPadding);
    $strangBorderWidth = $add($diff($strangEnd['x'], $anchor['x']), $add($strangBorderPadding, $strangBorderPadding));
    $strangBorderHeight = $add($diff($strangEnd['y'], $anchor['y']), $add($strangBorderPadding, $strangBorderPadding));
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
            'nodeEnd' => true,
            'devCounterEnd' => 5,
            'devCounterColor' => $color,
            'color' => $color,
            'zIndex' => $zIndex,
            'dev' => $dev,
        ]"
    />
@endif

@foreach ($extensionAnchors as $extensionIndex => $extensionAnchor)
    @php
        $extensionColor = (string) data_get($extensionColors, $extensionIndex, $color);
        $extensionEndPathLength = data_get($extensionEndPathLengths, $extensionIndex);
        $hasExtensionBranch = in_array($extensionIndex, collect($extensionBranchIndexes)->map(fn (mixed $index): int => (int) $index)->all(), true);
        $extensionEnd = $extensionEnds[$extensionIndex] ?? null;
        $extensionContinuationEnd = (filled($extensionEndPathLength) && filled($extensionEnd))
            ? ['x' => $extensionEnd['x'], 'y' => $add($extensionEnd['y'], (string) $extensionEndPathLength)]
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
                'nodeEnd' => true,
                'devCounterEnd' => 4,
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
            :connector-length="(string) data_get($extensionBranchConnectorLengths, $extensionIndex, $connectorLength)"
            :vertical-length="(string) data_get($extensionBranchVerticalLengths, $extensionIndex, $verticalLength)"
            :arc-size="$arcSize"
            :color="(string) data_get($extensionBranchColors, $extensionIndex, 'red')"
            :z-index="max(1, $extensionResolvedZIndexes[$extensionIndex] - 2)"
            :dev="$dev"
        />
    @endif
@endforeach
