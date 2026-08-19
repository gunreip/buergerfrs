{{-- packages/gunreip/laravel-translation-workbench/resources/views/components/ui/tw-graph-protocol/paths/branch-return-extension.blade.php --}}
{{--
    Path: branch-return-extension

    Usage:
    <x-translation-workbench::ui.tw-graph.paths.branch-return-extension
        side="left"
        :anchor-start="['x' => '0rem', 'y' => '0rem']"
        vertical-length="2rem"
        connector-length="3rem"
    />

    Path role:
    Branch-return-extension continues a branch-return chain outward:
    left:  segments.path bottom-top -> segments.arc west-north -> segments.path left-right
    right: segments.path bottom-top -> segments.arc east-north -> segments.path right-left
--}}

@props([
    'id' => 'path.branch-return-extension',
    'side' => 'left',
    'anchorStart' => ['x' => '0rem', 'y' => '0rem'],
    'arcSize' => '2.75rem',
    'verticalLength' => '2rem',
    'connectorLength' => '3rem',
    'color' => 'yellow',
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

    $arcStartAnchor = $isLeft ? 'w' : 'e';
    $arcEndAnchor = 'n';
    $connectorDirection = $isLeft ? 'left-right' : 'right-left';
    $arcDelta = $isLeft ? $arcSize : $neg($arcSize);
    $connectorDelta = $isLeft ? $connectorLength : $neg($connectorLength);

    $verticalEnd = [
        'x' => $currentAnchor['x'],
        'y' => $add($currentAnchor['y'], $verticalLength),
    ];
    $arcEnd = [
        'x' => $add($verticalEnd['x'], $arcDelta),
        'y' => $add($verticalEnd['y'], $arcSize),
    ];
    $connectorEnd = [
        'x' => $add($arcEnd['x'], $connectorDelta),
        'y' => $arcEnd['y'],
    ];
    $pathBoxPadding = '0.75rem';
    $pathBoxX = $isLeft ? $currentAnchor['x'] : $connectorEnd['x'];
    $pathBoxY = $currentAnchor['y'];
    $pathBoxWidth = $isLeft
        ? 'calc(' . $connectorEnd['x'] . ' - ' . $currentAnchor['x'] . ')'
        : 'calc(' . $currentAnchor['x'] . ' - ' . $connectorEnd['x'] . ')';
    $pathBoxHeight = 'calc(' . $connectorEnd['y'] . ' - ' . $currentAnchor['y'] . ')';

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
                'id' => $id . '.arc',
                'startAnchor' => $arcStartAnchor,
                'endAnchor' => $arcEndAnchor,
                'anchorStart' => $verticalEnd,
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
                'id' => $id . '.connector',
                'direction' => $connectorDirection,
                'length' => $connectorLength,
                'anchorStart' => $arcEnd,
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
