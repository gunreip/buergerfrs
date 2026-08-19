{{-- packages/gunreip/laravel-translation-workbench/resources/views/components/ui/tw-graph-protocol/paths/merge.blade.php --}}
{{--
    Path: merge

    Usage:
    <x-translation-workbench::ui.tw-graph.paths.merge
        side="left"
        :anchor-start="['x' => '0rem', 'y' => '0.75rem']"
        vertical-length="2rem"
        connector-length="3rem"
    />

    Path role:
    Merge owns one inbound side path and calculates its anchor chain from
    branch origin to trunk attach point:
    start -> vertical path -> arc-in -> connector -> arc-out.
--}}

@props([
    'id' => 'path.merge',
    'side' => 'left',
    'anchorStart' => ['x' => '0rem', 'y' => '0rem'],
    'startLength' => null,
    'arcSize' => '2.75rem',
    'connectorLength' => '3rem',
    'verticalLength' => '2rem',
    'color' => 'amber',
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
    $arcOutStartAnchor = 's';
    $arcOutEndAnchor = $isLeft ? 'e' : 'w';
    $connectorDirection = $isLeft ? 'left-right' : 'right-left';
    $connectorDelta = $isLeft ? $connectorLength : $neg($connectorLength);
    $startLength = $startLength ?? $arcSize;
    $startEnd = [
        'x' => $currentAnchor['x'],
        'y' => $add($currentAnchor['y'], $startLength),
    ];
    $verticalEnd = [
        'x' => $startEnd['x'],
        'y' => $add($startEnd['y'], $verticalLength),
    ];
    $arcInEnd = [
        'x' => $add($verticalEnd['x'], $isLeft ? $arcSize : $neg($arcSize)),
        'y' => $add($verticalEnd['y'], $arcSize),
    ];
    $connectorEnd = [
        'x' => $add($arcInEnd['x'], $connectorDelta),
        'y' => $arcInEnd['y'],
    ];
    $arcOutEnd = [
        'x' => $add($connectorEnd['x'], $isLeft ? $arcSize : $neg($arcSize)),
        'y' => $add($connectorEnd['y'], $arcSize),
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
            'component' => 'start',
            'segment' => [
                'id' => $id . '.start',
                'direction' => 'bottom-top',
                'length' => $startLength,
                'anchorStart' => $currentAnchor,
                'anchorEnd' => $startEnd,
                'nodeEnd' => true,
                'devCounterEnd' => $counter++,
                'devCounterColor' => $color,
                'startLabel' => [
                    'text' => ['Merge', 'start'],
                    'side' => 'bottom',
                    'offset' => '0.75rem',
                    'badgeColor' => $color,
                ],
                'color' => $color,
                'dev' => $dev,
            ],
        ],
        [
            'component' => 'path',
            'segment' => [
                'id' => $id . '.vertical',
                'direction' => 'bottom-top',
                'length' => $verticalLength,
                'anchorStart' => $startEnd,
                'anchorEnd' => $verticalEnd,
                'nodeStart' => false,
                'nodeEnd' => true,
                'devCounterEnd' => $counter++,
                'devCounterColor' => $color,
                'color' => $color,
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
                'nodeStart' => true,
                'nodeEnd' => true,
                'devCounterStart' => $counter++,
                'devCounterEnd' => $counter++,
                'color' => $color,
                'devCounterColor' => $color,
                'dev' => $dev,
            ],
        ],
        [
            'component' => 'path',
            'segment' => [
                'id' => $id . '.connector',
                'direction' => $connectorDirection,
                'length' => $connectorLength,
                'anchorStart' => $arcInEnd,
                'anchorEnd' => $connectorEnd,
                'nodeStart' => false,
                'nodeEnd' => false,
                'color' => $color,
                'dev' => $dev,
            ],
        ],
        [
            'component' => 'arc',
            'segment' => [
                'id' => $id . '.arc.out',
                'startAnchor' => $arcOutStartAnchor,
                'endAnchor' => $arcOutEndAnchor,
                'anchorStart' => $connectorEnd,
                'anchorEnd' => $arcOutEnd,
                'nodeStart' => true,
                'nodeEnd' => true,
                'devCounterStart' => $counter++,
                'devCounterEnd' => $counter++,
                'color' => $color,
                'devCounterColor' => $color,
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
    @if ($segment['component'] === 'start')
        <x-translation-workbench::ui.tw-graph.segments.start :segment="$segment['segment']" />
    @elseif ($segment['component'] === 'arc')
        <x-translation-workbench::ui.tw-graph.segments.arc :segment="$segment['segment']" />
    @else
        <x-translation-workbench::ui.tw-graph.segments.path :segment="$segment['segment']" />
    @endif
@endforeach
