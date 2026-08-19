{{-- packages/gunreip/laravel-translation-workbench/resources/views/components/ui/tw-graph-protocol/paths/merge-extension.blade.php --}}
{{--
    Path: merge-extension

    Usage:
    <x-translation-workbench::ui.tw-graph.paths.merge-extension
        side="left"
        :anchor-start="['x' => '0rem', 'y' => '0rem']"
        start-length="2rem"
        connector-length="3rem"
        :labels="['connectorEnd' => [['text' => 'Root #1', 'side' => 'top'], null]]"
    />

    Path role:
    Merge-extension continues a merge side chain outward:
    left:  segments.start -> segments.path bottom-top -> segments.arc west-north -> segments.path left-right
    right: segments.start -> segments.path bottom-top -> segments.arc east-north -> segments.path right-left
--}}

@props([
    'id' => 'path.merge-extension',
    'side' => 'left',
    'anchorStart' => ['x' => '0rem', 'y' => '0rem'],
    'startLength' => null,
    'arcSize' => '2.75rem',
    'verticalLength' => '2rem',
    'connectorLength' => '3rem',
    'labels' => [],
    'color' => 'sky',
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
    $startLength = $startLength ?? $arcSize;
    $startEnd = [
        'x' => $currentAnchor['x'],
        'y' => $add($currentAnchor['y'], $startLength),
    ];
    $verticalEnd = [
        'x' => $startEnd['x'],
        'y' => $add($startEnd['y'], $verticalLength),
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
    $labelSlots = function (string $key, mixed $default = true) use ($labels): mixed {
        $label = data_get($labels, $key);

        if (blank($label)) {
            return $default;
        }

        if (is_array($label) && array_key_exists('text', $label)) {
            return [$label, null];
        }

        return $label;
    };
    $singleLabel = function (string $key) use ($labels): ?array {
        $label = data_get($labels, $key);

        if (blank($label)) {
            return null;
        }

        if (is_array($label) && array_key_exists('text', $label)) {
            return $label;
        }

        if (is_array($label)) {
            return collect($label)
                ->filter()
                ->first();
        }

        return ['text' => $label];
    };
    $startLabel = $singleLabel('start') ?? [
        'text' => ['Merge extension', 'start'],
        'side' => 'bottom',
        'offset' => '0.75rem',
        'badgeColor' => $color,
    ];
    $arcEndLabel = $singleLabel('arcEnd');

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
                'startLabel' => $startLabel,
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
                'nodeEnd' => $labelSlots('verticalEnd'),
                'devCounterEnd' => $counter++,
                'devCounterColor' => $color,
                'color' => $color,
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
                'endLabel' => $arcEndLabel,
                'color' => $color,
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
                'nodeEnd' => $labelSlots('connectorEnd'),
                'devCounterEnd' => $counter++,
                'devCounterColor' => $color,
                'color' => $color,
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
