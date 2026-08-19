{{-- packages/gunreip/laravel-translation-workbench/resources/views/components/ui/tw-graph-protocol/paths/branch-extension.blade.php --}}
{{--
    Path: branch-extension

    Usage:
    <x-translation-workbench::ui.tw-graph-protocol.paths.branch-extension
        side="left"
        :anchor-start="['x' => '0rem', 'y' => '0rem']"
        connector-length="3rem"
        vertical-length="2rem"
    />

    Path role:
    Branch-extension continues an outbound branch side chain outward:
    left:  segments.path right-left -> segments.arc south-west -> segments.path bottom-top
    right: segments.path left-right -> segments.arc south-east -> segments.path bottom-top
--}}

@props([
    'id' => 'path.branch-extension',
    'side' => 'left',
    'anchorStart' => ['x' => '0rem', 'y' => '0rem'],
    'arcSize' => '2.75rem',
    'connectorLength' => '3rem',
    'verticalLength' => '2rem',
    'color' => 'rose',
    'zIndex' => null,
    'counterStart' => 1,
    'dev' => false,
])

@php
    $add = fn (string $value, string $delta): string => $delta === '0rem' ? $value : 'calc(' . $value . ' + ' . $delta . ')';
    $neg = fn (string $value): string => 'calc(' . $value . ' * -1)';
    $currentAnchor = [
        'x' => data_get($anchorStart, 'x', '0rem'),
        'y' => data_get($anchorStart, 'y', '0rem'),
    ];
    $counter = (int) $counterStart;
    $isLeft = $side === 'left';

    $connectorDirection = $isLeft ? 'right-left' : 'left-right';
    $arcStartAnchor = 's';
    $arcEndAnchor = $isLeft ? 'w' : 'e';
    $connectorDelta = $isLeft ? $neg($connectorLength) : $connectorLength;
    $arcDelta = $isLeft ? $neg($arcSize) : $arcSize;

    $connectorEnd = [
        'x' => $add($currentAnchor['x'], $connectorDelta),
        'y' => $currentAnchor['y'],
    ];
    $arcEnd = [
        'x' => $add($connectorEnd['x'], $arcDelta),
        'y' => $add($connectorEnd['y'], $arcSize),
    ];
    $verticalEnd = [
        'x' => $arcEnd['x'],
        'y' => $add($arcEnd['y'], $verticalLength),
    ];
    $pathBoxPadding = '0.75rem';
    $pathBoxX = $isLeft ? $verticalEnd['x'] : $currentAnchor['x'];
    $pathBoxY = $currentAnchor['y'];
    $pathBoxWidth = $isLeft
        ? 'calc(' . $currentAnchor['x'] . ' - ' . $verticalEnd['x'] . ')'
        : 'calc(' . $verticalEnd['x'] . ' - ' . $currentAnchor['x'] . ')';
    $pathBoxHeight = 'calc(' . $verticalEnd['y'] . ' - ' . $currentAnchor['y'] . ')';

    $segments = [
        [
            'component' => 'path',
            'segment' => [
                'id' => $id . '.connector',
                'direction' => $connectorDirection,
                'length' => $connectorLength,
                'anchorStart' => $currentAnchor,
                'anchorEnd' => $connectorEnd,
                'nodeStart' => false,
                'nodeEnd' => true,
                'devCounterEnd' => $counter++,
                'devCounterColor' => $color,
                'color' => $color,
                'zIndex' => $zIndex,
                'dev' => $dev,
            ],
        ],
        [
            'component' => 'arc',
            'segment' => [
                'id' => $id . '.arc',
                'startAnchor' => $arcStartAnchor,
                'endAnchor' => $arcEndAnchor,
                'anchorStart' => $connectorEnd,
                'anchorEnd' => $arcEnd,
                'nodeStart' => false,
                'nodeEnd' => true,
                'devCounterEnd' => $counter++,
                'devCounterColor' => $color,
                'color' => $color,
                'zIndex' => $zIndex,
                'dev' => $dev,
            ],
        ],
        [
            'component' => 'path',
            'segment' => [
                'id' => $id . '.vertical',
                'direction' => 'bottom-top',
                'length' => $verticalLength,
                'anchorStart' => $arcEnd,
                'anchorEnd' => $verticalEnd,
                'nodeStart' => false,
                'nodeEnd' => true,
                'devCounterEnd' => $counter++,
                'devCounterColor' => $color,
                'color' => $color,
                'zIndex' => $zIndex,
                'dev' => $dev,
            ],
        ],
    ];
@endphp

<x-translation-workbench::ui.tw-graph-protocol.dev-box
    :id="$id . '.dev-box'"
    :x="'calc(' . $pathBoxX . ' - ' . $pathBoxPadding . ')'"
    :y="'calc(' . $pathBoxY . ' - ' . $pathBoxPadding . ')'"
    :width="'calc(' . $pathBoxWidth . ' + (' . $pathBoxPadding . ' * 2))'"
    :height="'calc(' . $pathBoxHeight . ' + (' . $pathBoxPadding . ' * 2))'"
    color="amber"
    :label="$id"
    :dev="$dev"
/>

@foreach ($segments as $segment)
    @if ($segment['component'] === 'arc')
        <x-translation-workbench::ui.tw-graph-protocol.segments.arc :segment="$segment['segment']" />
    @else
        <x-translation-workbench::ui.tw-graph-protocol.segments.path :segment="$segment['segment']" />
    @endif
@endforeach
