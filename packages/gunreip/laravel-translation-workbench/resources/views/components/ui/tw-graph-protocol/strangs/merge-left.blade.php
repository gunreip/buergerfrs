{{-- packages/gunreip/laravel-translation-workbench/resources/views/components/ui/tw-graph-protocol/strangs/merge-left.blade.php --}}
{{--
    Strang: merge-left

    Usage:
    <x-translation-workbench::ui.tw-graph-protocol.strangs.merge-left
        :anchor-start="['x' => '-2rem', 'y' => '7rem']"
        extension-count="2"
        :extension-vertical-lengths="[1 => '6rem']"
        :extension-connector-lengths="[1 => '8rem']"
        :extension-labels="[1 => ['connectorEnd' => [['text' => 'Root #1', 'side' => 'top'], null]]]"
    />

    Strang role:
    Groups the left merge path with its outward extensions:
    paths.merge-extension left -> paths.merge-extension left -> paths.merge left.

    Anchor semantics:
    anchorStart is the start anchor of the trunk-nearest paths.merge left.
    The first extension ends at paths.merge left anchorPoint 4
    (merge.arc.in.node.end). Each next extension ends at anchorPoint 3 of
    the previous extension.
--}}

@props([
    // Identity / rendering state
    'id' => 'strang.merge-left',
    'dev' => false,

    // Base anchor / shared geometry
    'anchorStart' => ['x' => '0rem', 'y' => '0rem'],
    'arcSize' => '2.75rem',
    'color' => 'amber',

    // Trunk-nearest merge path: paths.merge left
    'startLength' => null,
    'verticalLength' => '2rem',
    'connectorLength' => '3rem',

    // Outward merge extensions: paths.merge-extension left[]
    'extensionCount' => 2,
    'extensionStartLength' => null,
    'extensionVerticalLength' => null,
    'extensionVerticalLengths' => [],
    'extensionConnectorLength' => null,
    'extensionConnectorLengths' => [],
    'extensionLabels' => [],
])

@php
    $add = fn (string $value, string $delta): string => $delta === '0rem' ? $value : 'calc(' . $value . ' + ' . $delta . ')';
    $neg = fn (string $value): string => 'calc(' . $value . ' * -1)';
    $subtract = fn (string $value, string $delta): string => $add($value, $neg($delta));
    $mergeAnchor = [
        'x' => data_get($anchorStart, 'x', '0rem'),
        'y' => data_get($anchorStart, 'y', '0rem'),
    ];
    $extensionCount = max(0, (int) $extensionCount);
    $extensionStartLength = $extensionStartLength ?? $arcSize;
    $extensionVerticalLength = $extensionVerticalLength ?? $verticalLength;
    $extensionConnectorLength = $extensionConnectorLength ?? $connectorLength;
    $lengthAsFloat = function (string $value): float {
        if (preg_match('/^-?\d+(?:\.\d+)?rem$/', trim($value), $matches) === 1) {
            return (float) $matches[0];
        }

        return 0.0;
    };
    $extensionVerticalLengthFor = function (int $extensionIndex) use ($extensionVerticalLengths, $extensionVerticalLength): string {
        return data_get($extensionVerticalLengths, $extensionIndex)
            ?? data_get($extensionVerticalLengths, $extensionIndex - 1)
            ?? $extensionVerticalLength;
    };
    $extensionConnectorLengthFor = function (int $extensionIndex) use ($extensionConnectorLengths, $extensionConnectorLength): string {
        return data_get($extensionConnectorLengths, $extensionIndex)
            ?? data_get($extensionConnectorLengths, $extensionIndex - 1)
            ?? $extensionConnectorLength;
    };
    $extensionAnchors = [];
    $extensionResolvedVerticalLengths = [];
    $extensionResolvedConnectorLengths = [];
    $mergeStartLength = $startLength ?? $arcSize;
    $mergeAnchorPoint4 = [
        'x' => $add($mergeAnchor['x'], $arcSize),
        'y' => $add($add($add($mergeAnchor['y'], $mergeStartLength), $verticalLength), $arcSize),
    ];
    $nextTargetAnchor = $mergeAnchorPoint4;

    for ($extensionIndex = 1; $extensionIndex <= $extensionCount; $extensionIndex++) {
        $currentExtensionVerticalLength = $extensionVerticalLengthFor($extensionIndex);
        $currentExtensionConnectorLength = $extensionConnectorLengthFor($extensionIndex);
        $extensionDeltaX = $add($arcSize, $currentExtensionConnectorLength);
        $extensionDeltaY = $add($add($extensionStartLength, $currentExtensionVerticalLength), $arcSize);
        $extensionAnchor = [
            'x' => $subtract($nextTargetAnchor['x'], $extensionDeltaX),
            'y' => $subtract($nextTargetAnchor['y'], $extensionDeltaY),
        ];
        $extensionAnchors[$extensionIndex] = $extensionAnchor;
        $extensionResolvedVerticalLengths[$extensionIndex] = $currentExtensionVerticalLength;
        $extensionResolvedConnectorLengths[$extensionIndex] = $currentExtensionConnectorLength;
        $nextTargetAnchor = [
            'x' => $subtract($nextTargetAnchor['x'], $currentExtensionConnectorLength),
            'y' => $nextTargetAnchor['y'],
        ];
    }

    $outermostExtensionAnchor = $extensionAnchors[$extensionCount] ?? $mergeAnchor;
    $longestExtensionIndex = collect($extensionResolvedVerticalLengths)
        ->sortByDesc(fn (string $length): float => $lengthAsFloat($length))
        ->keys()
        ->first();
    $lowestExtensionAnchor = filled($longestExtensionIndex)
        ? ($extensionAnchors[$longestExtensionIndex] ?? $outermostExtensionAnchor)
        : $outermostExtensionAnchor;
    $mergeTopAnchor = [
        'x' => $add($mergeAnchorPoint4['x'], $add($connectorLength, $arcSize)),
        'y' => $add($mergeAnchorPoint4['y'], $arcSize),
    ];
    $strangBounds = \Gunreip\TranslationWorkbench\Support\TwGraphProtocol\GeometryBounds::fromPoints([
        $outermostExtensionAnchor,
        $lowestExtensionAnchor,
        $mergeTopAnchor,
    ], '1rem');
    $strangBorderLeft = $strangBounds['left'];
    $strangBorderBottom = $strangBounds['bottom'];
    $strangBorderWidth = $strangBounds['width'];
    $strangBorderHeight = $strangBounds['height'];
@endphp

@if ($dev)
    <span
        class="tw-graph-protocol-dev-only pointer-events-none absolute rounded-lg border border-dashed border-amber-400/60"
        style="
            left: calc(var(--tw-graph-protocol-trunk-x) + {{ $strangBorderLeft }});
            bottom: {{ $strangBorderBottom }};
            width: {{ $strangBorderWidth }};
            height: {{ $strangBorderHeight }};
        "
        title="{{ $id }} | strang.merge-left"
    >
        <span class="absolute left-2 top-0 -translate-y-1/2">
            <flux:badge
                size="sm"
                color="amber"
            >
                {{ $id }}
            </flux:badge>
        </span>
    </span>
@endif

@foreach (array_reverse($extensionAnchors, true) as $extensionIndex => $extensionAnchor)
    <x-translation-workbench::ui.tw-graph-protocol.paths.merge-extension
        :id="$id . '.extension.' . $extensionIndex"
        side="left"
        :anchor-start="$extensionAnchor"
        :start-length="$extensionStartLength"
        :vertical-length="$extensionResolvedVerticalLengths[$extensionIndex]"
        :connector-length="$extensionResolvedConnectorLengths[$extensionIndex]"
        :arc-size="$arcSize"
        :labels="data_get($extensionLabels, $extensionIndex, data_get($extensionLabels, $extensionIndex - 1, []))"
        :color="$color"
        :dev="$dev"
    />
@endforeach

<x-translation-workbench::ui.tw-graph-protocol.paths.merge
    :id="$id . '.merge'"
    side="left"
    :anchor-start="$mergeAnchor"
    :start-length="$startLength"
    :vertical-length="$verticalLength"
    :connector-length="$connectorLength"
    :arc-size="$arcSize"
    :color="$color"
    :dev="$dev"
/>
