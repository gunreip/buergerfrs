{{-- packages/gunreip/laravel-translation-workbench/resources/views/components/ui/tw-graph-protocol/paths/trunk.blade.php --}}
{{--
    Path: trunk

    Usage:
    <x-translation-workbench::ui.tw-graph-protocol.paths.trunk
        direction="bottom-top"
        :anchor-start="['x' => '0rem', 'y' => '0.75rem']"
        start-length="2.5rem"
        :path-lengths="['2rem', '2rem']"
        end-length="1rem"
    />

    Path role:
    Trunk owns the segment chain. It calculates each segment anchorStart and
    anchorEnd from the previous segment end anchor, so callers provide lengths
    instead of hand-wiring every segment coordinate.
--}}

@props([
    'id' => 'path.trunk',
    'direction' => 'bottom-top',
    'anchorStart' => ['x' => '0rem', 'y' => '0rem'],
    'startLength' => null,
    'pathLengths' => [],
    'endLength' => null,
    'color' => 'emerald',
    'counterStart' => 1,
    'dev' => false,
])

@php
    $startLabelSide = match ($direction) {
        'left-right' => 'left',
        'right-left' => 'right',
        'top-bottom' => 'top',
        default => 'bottom',
    };
    $endLabelSide = match ($direction) {
        'left-right' => 'right',
        'right-left' => 'left',
        'top-bottom' => 'bottom',
        default => 'top',
    };
    $axisDelta = function (string $length) use ($direction): array {
        return match ($direction) {
            'top-bottom' => ['x' => '0rem', 'y' => 'calc(' . $length . ' * -1)'],
            'left-right' => ['x' => $length, 'y' => '0rem'],
            'right-left' => ['x' => 'calc(' . $length . ' * -1)', 'y' => '0rem'],
            default => ['x' => '0rem', 'y' => $length],
        };
    };
    $addAnchor = function (array $anchor, array $delta): array {
        return [
            'x' => $delta['x'] === '0rem' ? data_get($anchor, 'x', '0rem') : 'calc(' . data_get($anchor, 'x', '0rem') . ' + ' . $delta['x'] . ')',
            'y' => $delta['y'] === '0rem' ? data_get($anchor, 'y', '0rem') : 'calc(' . data_get($anchor, 'y', '0rem') . ' + ' . $delta['y'] . ')',
        ];
    };
    $normalizeLabel = function (mixed $label): ?array {
        if (blank($label) || $label === 'null') {
            return null;
        }

        if (is_array($label)) {
            return $label;
        }

        [$text, $side] = array_pad(explode('|', (string) $label, 2), 2, null);

        return [
            'text' => trim($text),
            'side' => filled($side) ? trim($side) : null,
        ];
    };
    $normalizePathLength = function (mixed $pathLength) use ($normalizeLabel): array {
        if (! is_array($pathLength)) {
            return ['length' => $pathLength, 'labels' => true];
        }

        $length = data_get($pathLength, 'length', data_get($pathLength, 0));
        $labels = data_get($pathLength, 'labels', data_get($pathLength, 1, true));

        if (is_array($labels)) {
            $labels = collect($labels)
                ->take(2)
                ->map(fn (mixed $label): ?array => $normalizeLabel($label))
                ->all();
        }

        return ['length' => $length, 'labels' => $labels];
    };
    $segments = [];
    $counter = (int) $counterStart;
    $currentAnchor = [
        'x' => data_get($anchorStart, 'x', '0rem'),
        'y' => data_get($anchorStart, 'y', '0rem'),
    ];
    $pathStartAnchor = $currentAnchor;

    if (filled($startLength)) {
        $nextAnchor = $addAnchor($currentAnchor, $axisDelta($startLength));
        $segments[] = [
            'component' => 'start',
            'segment' => [
                'id' => $id . '.start',
                'direction' => $direction,
                'length' => $startLength,
                'anchorStart' => $currentAnchor,
                'anchorEnd' => $nextAnchor,
                'nodeEnd' => true,
                'devCounterEnd' => $counter++,
                'devCounterColor' => $color,
                'startLabel' => [
                    'text' => ['Path', 'start'],
                    'side' => $startLabelSide,
                    'offset' => '0.75rem',
                    'badgeColor' => $color,
                ],
                'color' => $color,
                'dev' => $dev,
            ],
        ];
        $currentAnchor = $nextAnchor;
    }

    foreach (collect($pathLengths)->values() as $pathIndex => $pathLengthEntry) {
        $normalizedPathLength = $normalizePathLength($pathLengthEntry);
        $pathLength = data_get($normalizedPathLength, 'length', '0rem');
        $nodeEnd = data_get($normalizedPathLength, 'labels', true);
        $nextAnchor = $addAnchor($currentAnchor, $axisDelta($pathLength));
        $segments[] = [
            'component' => 'path',
            'segment' => [
                'id' => $id . '.path.' . ($pathIndex + 1),
                'direction' => $direction,
                'length' => $pathLength,
                'anchorStart' => $currentAnchor,
                'anchorEnd' => $nextAnchor,
                'nodeStart' => false,
                'nodeEnd' => $nodeEnd,
                'devCounterEnd' => $counter++,
                'devCounterColor' => $color,
                'color' => $color,
                'dev' => $dev,
            ],
        ];
        $currentAnchor = $nextAnchor;
    }

    if (filled($endLength)) {
        $nextAnchor = $addAnchor($currentAnchor, $axisDelta($endLength));
        $segments[] = [
            'component' => 'end',
            'segment' => [
                'id' => $id . '.end',
                'direction' => $direction,
                'length' => $endLength,
                'anchorStart' => $currentAnchor,
                'anchorEnd' => $nextAnchor,
                'nodeStart' => false,
                'devCounterEnd' => $counter++,
                'devCounterColor' => $color,
                'endLabel' => [
                    'text' => ['Path', 'end'],
                    'side' => $endLabelSide,
                    'offset' => '0.75rem',
                    'badgeColor' => $color,
                ],
                'color' => $color,
                'dev' => $dev,
            ],
        ];
        $currentAnchor = $nextAnchor;
    }

    $pathEndAnchor = $currentAnchor;
    $pathBoxPadding = '0.75rem';
    $isHorizontalPath = in_array($direction, ['left-right', 'right-left'], true);
    $pathBoxX = match ($direction) {
        'right-left' => data_get($pathEndAnchor, 'x', '0rem'),
        'left-right' => data_get($pathStartAnchor, 'x', '0rem'),
        default => 'calc(' . data_get($pathStartAnchor, 'x', '0rem') . ' - var(--tw-graph-protocol-node-half))',
    };
    $pathBoxY = match ($direction) {
        'top-bottom' => data_get($pathEndAnchor, 'y', '0rem'),
        'left-right', 'right-left' => 'calc(' . data_get($pathStartAnchor, 'y', '0rem') . ' - var(--tw-graph-protocol-node-half))',
        default => data_get($pathStartAnchor, 'y', '0rem'),
    };
    $pathBoxWidth = $isHorizontalPath
        ? 'calc(' . data_get($pathEndAnchor, 'x', '0rem') . ' - ' . data_get($pathStartAnchor, 'x', '0rem') . ')'
        : 'var(--tw-graph-protocol-node-size)';
    if ($direction === 'right-left') {
        $pathBoxWidth = 'calc(' . data_get($pathStartAnchor, 'x', '0rem') . ' - ' . data_get($pathEndAnchor, 'x', '0rem') . ')';
    }
    $pathBoxHeight = $isHorizontalPath
        ? 'var(--tw-graph-protocol-node-size)'
        : 'calc(' . data_get($pathEndAnchor, 'y', '0rem') . ' - ' . data_get($pathStartAnchor, 'y', '0rem') . ')';
    if ($direction === 'top-bottom') {
        $pathBoxHeight = 'calc(' . data_get($pathStartAnchor, 'y', '0rem') . ' - ' . data_get($pathEndAnchor, 'y', '0rem') . ')';
    }
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
    @if ($segment['component'] === 'start')
        <x-translation-workbench::ui.tw-graph-protocol.segments.start :segment="$segment['segment']" />
    @elseif ($segment['component'] === 'end')
        <x-translation-workbench::ui.tw-graph-protocol.segments.end :segment="$segment['segment']" />
    @else
        <x-translation-workbench::ui.tw-graph-protocol.segments.path :segment="$segment['segment']" />
    @endif
@endforeach
