{{-- packages/gunreip/laravel-translation-workbench/resources/views/components/ui/tw-graph/paths/branch-return-bridge.blade.php --}}
{{--
    Path: branch-return-bridge

    Usage:
    <x-translation-workbench::ui.tw-graph.paths.branch-return-bridge
        side="left"
        :anchor-start="['x' => '0rem', 'y' => '0rem']"
        bridge-length="3rem"
        :node-labels="[1 => ['top' => 'Arc'], 2 => ['bottom' => 'Bridge']]"
    />

    Path role:
    Branch-return-bridge starts an open return path from a branch extension:
    left:  segments.arc west-north -> segments.path left-right
    right: segments.arc east-north -> segments.path right-left
--}}

@props([
    'id' => 'path.branch-return-bridge',
    'side' => 'left',
    'anchorStart' => ['x' => '0rem', 'y' => '0rem'],
    'arcSize' => '2.75rem',
    'bridgeLength' => '3rem',
    'color' => 'orange',
    'zIndex' => null,
    'counterStart' => 1,
    'nodeLabels' => [],
    'fallbackUsed' => false,
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
    $bridgeDirection = $isLeft ? 'left-right' : 'right-left';
    $arcDelta = $isLeft ? $arcSize : $neg($arcSize);
    $bridgeDelta = $isLeft ? $bridgeLength : $neg($bridgeLength);
    $normalizeLabel = function (mixed $label, ?string $side = null) use ($color): ?array {
        if (blank($label)) {
            return null;
        }

        if (is_array($label)) {
            $text = data_get($label, 'text');
            $left = data_get($label, 'left');
            $right = data_get($label, 'right');
            $top = data_get($label, 'top');
            $bottom = data_get($label, 'bottom');

            if (filled($left)) {
                $text = $left;
                $side = 'left';
            } elseif (filled($right)) {
                $text = $right;
                $side = 'right';
            } elseif (filled($top)) {
                $text = $top;
                $side = 'top';
            } elseif (filled($bottom)) {
                $text = $bottom;
                $side = 'bottom';
            }

            if (blank($text)) {
                return null;
            }

            return array_replace([
                'text' => $text,
                'side' => $side,
                'badgeColor' => $color,
            ], collect($label)->except(['left', 'right', 'top', 'bottom'])->all());
        }

        return [
            'text' => $label,
            'side' => $side,
            'badgeColor' => $color,
        ];
    };
    $pathNodeLabels = function (mixed $label): array {
        if (! is_array($label)) {
            return filled($label) ? [$label, null] : [null, null];
        }

        $top = data_get($label, 'top');
        $bottom = data_get($label, 'bottom');
        if (filled($top) || filled($bottom)) {
            return [$top ?: null, $bottom ?: null];
        }
        if (filled(data_get($label, 'text'))) {
            return [$label, null];
        }

        return [
            data_get($label, 'labelA', data_get($label, 'label', data_get($label, 0))),
            data_get($label, 'labelB', data_get($label, 1)),
        ];
    };
    $arcEndLabel = $normalizeLabel(data_get($nodeLabels, 1), 'top');
    $bridgeEndLabels = collect($pathNodeLabels(data_get($nodeLabels, 2)))
        ->map(fn (mixed $label): ?array => $normalizeLabel($label))
        ->all();
    $bridgeEndNode = collect($bridgeEndLabels)->filter(fn (mixed $label): bool => filled($label))->isNotEmpty()
        ? $bridgeEndLabels
        : true;

    $arcEnd = [
        'x' => $add($currentAnchor['x'], $arcDelta),
        'y' => $add($currentAnchor['y'], $arcSize),
    ];
    $bridgeEnd = [
        'x' => $add($arcEnd['x'], $bridgeDelta),
        'y' => $arcEnd['y'],
    ];
    $pathBoxPadding = '0.75rem';
    $pathBoxX = $isLeft ? $currentAnchor['x'] : $bridgeEnd['x'];
    $pathBoxY = $currentAnchor['y'];
    $pathBoxWidth = $isLeft
        ? 'calc(' . $bridgeEnd['x'] . ' - ' . $currentAnchor['x'] . ')'
        : 'calc(' . $currentAnchor['x'] . ' - ' . $bridgeEnd['x'] . ')';
    $pathBoxHeight = 'calc(' . $bridgeEnd['y'] . ' - ' . $currentAnchor['y'] . ')';

    $segments = [
        [
            'component' => 'arc',
            'segment' => [
                'id' => $id . '.arc',
                'startAnchor' => $arcStartAnchor,
                'endAnchor' => $arcEndAnchor,
                'anchorStart' => $currentAnchor,
                'anchorEnd' => $arcEnd,
                'nodeStart' => false,
                'nodeEnd' => true,
                'devCounterEnd' => $counter++,
                'devCounterColor' => $color,
                'endLabel' => $arcEndLabel,
                'dashed' => $fallbackUsed,
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
                'anchorStart' => $arcEnd,
                'anchorEnd' => $bridgeEnd,
                'nodeStart' => false,
                'nodeEnd' => $bridgeEndNode,
                'devCounterEnd' => $counter++,
                'devCounterColor' => $color,
                'dashed' => $fallbackUsed,
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
