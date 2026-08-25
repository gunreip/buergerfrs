{{-- packages/gunreip/laravel-translation-workbench/resources/views/components/ui/tw-graph/paths/branch-extension.blade.php --}}
{{--
    Path: branch-extension

    Usage:
    <x-translation-workbench::ui.tw-graph.paths.branch-extension
        side="left"
        :anchor-start="['x' => '0rem', 'y' => '0rem']"
        bridge-length="3rem"
        stem-length="2rem"
    />

    Path role:
    Branch-extension continues an outbound branch side chain outward:
    from bridge end:
    left:  segments.path right-left -> segments.arc south-west -> segments.path bottom-top
    right: segments.path left-right -> segments.arc south-east -> segments.path bottom-top

    from stem end:
    left:  segments.arc east-north -> segments.path right-left -> segments.arc south-west -> segments.path bottom-top
    right: segments.arc west-north -> segments.path left-right -> segments.arc south-east -> segments.path bottom-top
--}}

@props([
    'id' => 'path.branch-extension',
    'side' => 'left',
    'anchorStart' => ['x' => '0rem', 'y' => '0rem'],
    'arcSize' => '2.75rem',
    'bridgeLength' => '3rem',
    'stemLength' => '2rem',
    'color' => 'rose',
    'zIndex' => null,
    'counterStart' => 1,
    'nodeLabels' => [],
    'endLabel' => null,
    'endLength' => '0rem',
    'capLength' => '1.75rem',
    'dev' => false,
])

@php
    $normalLabels = function (mixed $labels): array {
        if (! is_array($labels)) {
            return [];
        }

        return array_is_list($labels)
            ? array_values($labels)
            : [$labels];
    };
    $add = fn (string $value, string $delta): string => $delta === '0rem' ? $value : 'calc(' . $value . ' + ' . $delta . ')';
    $neg = fn (string $value): string => 'calc(' . $value . ' * -1)';
    $currentAnchor = [
        'x' => data_get($anchorStart, 'x', '0rem'),
        'y' => data_get($anchorStart, 'y', '0rem'),
        'source' => data_get($anchorStart, 'source'),
        'sourceType' => data_get($anchorStart, 'sourceType'),
        'sourceAnchor' => data_get($anchorStart, 'sourceAnchor'),
        'direction' => data_get($anchorStart, 'direction'),
    ];
    $counter = (int) $counterStart;
    $isLeft = $side === 'left';
    $startsFromStem = data_get($currentAnchor, 'sourceType') === 'stem';

    $bridgeDirection = $isLeft ? 'right-left' : 'left-right';
    $introArcStartAnchor = $isLeft ? 'e' : 'w';
    $introArcEndAnchor = 'n';
    $arcStartAnchor = 's';
    $arcEndAnchor = $isLeft ? 'w' : 'e';
    $bridgeDelta = $isLeft ? $neg($bridgeLength) : $bridgeLength;
    $arcDelta = $isLeft ? $neg($arcSize) : $arcSize;

    $bridgeStart = $currentAnchor;
    $introArcEnd = null;

    if ($startsFromStem) {
        $introArcEnd = [
            'x' => $add($currentAnchor['x'], $arcDelta),
            'y' => $add($currentAnchor['y'], $arcSize),
        ];
        $bridgeStart = $introArcEnd;
    }

    $bridgeEnd = [
        'x' => $add($bridgeStart['x'], $bridgeDelta),
        'y' => $bridgeStart['y'],
    ];
    $arcEnd = [
        'x' => $add($bridgeEnd['x'], $arcDelta),
        'y' => $add($bridgeEnd['y'], $arcSize),
    ];
    $verticalEnd = [
        'x' => $arcEnd['x'],
        'y' => $add($arcEnd['y'], $stemLength),
    ];
    $pathBoxPadding = '0.75rem';
    $pathBoxX = $isLeft ? $verticalEnd['x'] : $currentAnchor['x'];
    $pathBoxY = $currentAnchor['y'];
    $pathBoxWidth = $isLeft
        ? 'calc(' . $currentAnchor['x'] . ' - ' . $verticalEnd['x'] . ')'
        : 'calc(' . $verticalEnd['x'] . ' - ' . $currentAnchor['x'] . ')';
    $endNodeLabels = $normalLabels(data_get($nodeLabels, 3, []));
    $endLabelConfig = is_array($endLabel)
        ? $endLabel
        : (filled($endLabel) ? ['text' => $endLabel] : null);
    $hasEndSegment = $endLabelConfig !== null || filled($endLength);
    $resolvedEndLength = filled($endLength) ? (string) $endLength : '0rem';
    $endAnchor = [
        'x' => $verticalEnd['x'],
        'y' => $add($verticalEnd['y'], $resolvedEndLength),
    ];
    $pathBoxHeight = 'calc(' . ($hasEndSegment ? $endAnchor['y'] : $verticalEnd['y']) . ' - ' . $currentAnchor['y'] . ')';

    $segments = [];

    if ($startsFromStem) {
        $segments[] = [
            'component' => 'arc',
            'segment' => [
                'id' => $id . '.arc.in',
                'startAnchor' => $introArcStartAnchor,
                'endAnchor' => $introArcEndAnchor,
                'anchorStart' => $currentAnchor,
                'anchorEnd' => $introArcEnd,
                'nodeStart' => false,
                'nodeEnd' => true,
                'devCounterEnd' => $counter++,
                'devCounterColor' => $color,
                'color' => $color,
                'zIndex' => $zIndex,
                'dev' => $dev,
            ],
        ];
    }

    $segments = [
        ...$segments,
        [
            'component' => 'path',
            'segment' => [
                'id' => $id . '.bridge',
                'direction' => $bridgeDirection,
                'length' => $bridgeLength,
                'anchorStart' => $bridgeStart,
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
                'id' => $id . '.arc',
                'startAnchor' => $arcStartAnchor,
                'endAnchor' => $arcEndAnchor,
                'anchorStart' => $bridgeEnd,
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
                'id' => $id . '.stem',
                'direction' => 'bottom-top',
                'length' => $stemLength,
                'anchorStart' => $arcEnd,
                'anchorEnd' => $verticalEnd,
                'nodeStart' => false,
                'nodeEnd' => $endNodeLabels !== [] ? $endNodeLabels : true,
                'devCounterEnd' => $counter++,
                'devCounterColor' => $color,
                'color' => $color,
                'zIndex' => $zIndex,
                'dev' => $dev,
            ],
        ],
    ];

    if ($hasEndSegment) {
        $segments[] = [
            'component' => 'end',
            'segment' => [
                'id' => $id . '.end',
                'direction' => 'bottom-top',
                'length' => $resolvedEndLength,
                'anchorStart' => $verticalEnd,
                'anchorEnd' => $endAnchor,
                'nodeStart' => false,
                'nodeEnd' => false,
                'cap' => true,
                'capLength' => $capLength,
                'devCounterEnd' => $counter++,
                'devCounterColor' => $color,
                'color' => $color,
                'zIndex' => $zIndex,
                'dev' => $dev,
                'endLabel' => $endLabelConfig,
            ],
        ];
    }
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
    @elseif ($segment['component'] === 'end')
        <x-translation-workbench::ui.tw-graph.segments.end
            :segment="$segment['segment']"
            :dev="$dev"
        />
    @else
        <x-translation-workbench::ui.tw-graph.segments.path :segment="$segment['segment']" />
    @endif
@endforeach
