{{-- packages/gunreip/laravel-translation-workbench/resources/views/components/ui/tw-graph-protocol/paths/branch-return.blade.php --}}
{{--
    Path: branch-return

    Usage:
    <x-translation-workbench::ui.tw-graph.paths.branch-return
        side="left"
        :anchor-start="['x' => '0rem', 'y' => '0rem']"
        vertical-length="2rem"
        bridge-length="3rem"
    />

    Path role:
    Branch-return routes an outbound branch chain back toward the trunk:
    left:  segments.path bottom-top -> segments.arc west-north -> segments.path left-right -> segments.arc south-east
    right: segments.path bottom-top -> segments.arc east-north -> segments.path right-left -> segments.arc south-west
--}}

@props([
    'id' => 'path.branch-return',
    'side' => 'left',
    'anchorStart' => ['x' => '0rem', 'y' => '0rem'],
    'arcSize' => '2.75rem',
    'verticalLength' => '2rem',
    'bridgeLength' => '3rem',
    'color' => 'orange',
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

    $arcInStartAnchor = $isLeft ? 'w' : 'e';
    $arcInEndAnchor = 'n';
    $bridgeDirection = $isLeft ? 'left-right' : 'right-left';
    $arcOutStartAnchor = 's';
    $arcOutEndAnchor = $isLeft ? 'e' : 'w';
    $arcDelta = $isLeft ? $arcSize : $neg($arcSize);
    $bridgeDelta = $isLeft ? $bridgeLength : $neg($bridgeLength);

    $verticalEnd = [
        'x' => $currentAnchor['x'],
        'y' => $add($currentAnchor['y'], $verticalLength),
    ];
    $arcInEnd = [
        'x' => $add($verticalEnd['x'], $arcDelta),
        'y' => $add($verticalEnd['y'], $arcSize),
    ];
    $bridgeEnd = [
        'x' => $add($arcInEnd['x'], $bridgeDelta),
        'y' => $arcInEnd['y'],
    ];
    $arcOutEnd = [
        'x' => $add($bridgeEnd['x'], $arcDelta),
        'y' => $add($bridgeEnd['y'], $arcSize),
    ];
    $pathBoxPadding = '0.75rem';
    $pathBoxX = $isLeft ? $currentAnchor['x'] : $arcOutEnd['x'];
    $pathBoxY = $currentAnchor['y'];
    $pathBoxWidth = $isLeft
        ? 'calc(' . $arcOutEnd['x'] . ' - ' . $currentAnchor['x'] . ')'
        : 'calc(' . $currentAnchor['x'] . ' - ' . $arcOutEnd['x'] . ')';
    $pathBoxHeight = 'calc(' . $arcOutEnd['y'] . ' - ' . $currentAnchor['y'] . ')';

    $segments = [
        [
            'component' => 'path',
            'segment' => [
                'id' => $id . '.vertical',
                'direction' => 'bottom-top',
                'length' => $verticalLength,
                'anchorStart' => $currentAnchor,
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
        [
            'component' => 'arc',
            'segment' => [
                'id' => $id . '.arc.in',
                'startAnchor' => $arcInStartAnchor,
                'endAnchor' => $arcInEndAnchor,
                'anchorStart' => $verticalEnd,
                'anchorEnd' => $arcInEnd,
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
                'id' => $id . '.bridge',
                'direction' => $bridgeDirection,
                'length' => $bridgeLength,
                'anchorStart' => $arcInEnd,
                'anchorEnd' => $bridgeEnd,
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
                'id' => $id . '.arc.out',
                'startAnchor' => $arcOutStartAnchor,
                'endAnchor' => $arcOutEndAnchor,
                'anchorStart' => $bridgeEnd,
                'anchorEnd' => $arcOutEnd,
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

<x-translation-workbench::ui.tw-graph.dev-box
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
        <x-translation-workbench::ui.tw-graph.segments.arc :segment="$segment['segment']" />
    @else
        <x-translation-workbench::ui.tw-graph.segments.path :segment="$segment['segment']" />
    @endif
@endforeach
