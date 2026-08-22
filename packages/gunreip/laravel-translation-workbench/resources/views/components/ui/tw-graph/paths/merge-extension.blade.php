{{-- packages/gunreip/laravel-translation-workbench/resources/views/components/ui/tw-graph/paths/merge-extension.blade.php --}}
{{--
    Path: merge-extension

    Usage:
    <x-translation-workbench::ui.tw-graph.paths.merge-extension
        side="left"
        :anchor-start="['x' => '0rem', 'y' => '0rem']"
        start-length="2rem"
        stem-length="2rem"
        bridge-length="3rem"
        :node-labels="[4 => ['top' => 'Root #1']]"
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
    'lineLength' => '4rem',
    'arcSize' => '2.75rem',
    'stemLength' => null,
    'bridgeLength' => null,
    'nodeLabels' => [],
    'color' => 'sky',
    'zIndex' => null,
    'counterStart' => 1,
    'dev' => false,
    'showDevBox' => true,
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
    $resolvedLineLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::localOrFallback($lineLength ?? null, '4rem');
    $resolvedArcSize = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::localOrFallback($arcSize ?? null, '2.75rem');
    $resolvedStemLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($stemLength, $resolvedLineLength, '4rem');
    $resolvedBridgeLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($bridgeLength, $resolvedLineLength, '4rem');

    $arcStartAnchor = $isLeft ? 'w' : 'e';
    $arcEndAnchor = 'n';
    $bridgeDirection = $isLeft ? 'left-right' : 'right-left';
    $arcDelta = $isLeft ? $resolvedArcSize : $neg($resolvedArcSize);
    $bridgeDelta = $isLeft ? $resolvedBridgeLength : $neg($resolvedBridgeLength);
    $startLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($startLength, $resolvedArcSize, '2.75rem');
    $startEnd = [
        'x' => $currentAnchor['x'],
        'y' => $add($currentAnchor['y'], $startLength),
    ];
    $stemEnd = [
        'x' => $startEnd['x'],
        'y' => $add($startEnd['y'], $resolvedStemLength),
    ];
    $arcEnd = [
        'x' => $add($stemEnd['x'], $arcDelta),
        'y' => $add($stemEnd['y'], $resolvedArcSize),
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
    $pathNodeLabels = function (int $nodeNumber, string $defaultSide) use ($nodeLabels, $normalizeLabel): mixed {
        $label = $normalizeLabel(data_get($nodeLabels, $nodeNumber), $defaultSide);

        return $label ? [$label, null] : true;
    };
    $arcNodeLabel = fn (int $nodeNumber, string $defaultSide): ?array => $normalizeLabel(
        data_get($nodeLabels, $nodeNumber),
        $defaultSide,
    );
    $startLabel = $normalizeLabel(data_get($nodeLabels, 'start'), 'bottom') ?? [
        'text' => ['Merge extension', 'start'],
        'side' => 'bottom',
        'offset' => '0.75rem',
        'badgeColor' => $color,
    ];

    $segments = [
        [
            'component' => 'start',
            'segment' => [
                'id' => $id . '.start',
                'direction' => 'bottom-top',
                'length' => $startLength,
                'anchorStart' => $currentAnchor,
                'anchorEnd' => $startEnd,
                'nodeEnd' => $pathNodeLabels(1, $isLeft ? 'right' : 'left'),
                'devCounterEnd' => $counter++,
                'devCounterColor' => $color,
                'startLabel' => $startLabel,
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
                'length' => $resolvedStemLength,
                'anchorStart' => $startEnd,
                'anchorEnd' => $stemEnd,
                'nodeStart' => false,
                'nodeEnd' => $pathNodeLabels(2, $isLeft ? 'right' : 'left'),
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
                'anchorStart' => $stemEnd,
                'anchorEnd' => $arcEnd,
                'nodeStart' => false,
                'nodeEnd' => true,
                'devCounterEnd' => $counter++,
                'devCounterColor' => $color,
                'endLabel' => $arcNodeLabel(3, 'top'),
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
                'length' => $resolvedBridgeLength,
                'anchorStart' => $arcEnd,
                'anchorEnd' => $bridgeEnd,
                'nodeStart' => false,
                'nodeEnd' => $pathNodeLabels(4, 'top'),
                'devCounterEnd' => $counter++,
                'devCounterColor' => $color,
                'color' => $color,
                'zIndex' => $zIndex,
                'dev' => $dev,
            ],
        ],
    ];
@endphp

@if ($showDevBox)
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
@endif

@foreach ($segments as $segment)
    @if ($segment['component'] === 'start')
        <x-translation-workbench::ui.tw-graph.segments.start :segment="$segment['segment']" />
    @elseif ($segment['component'] === 'arc')
        <x-translation-workbench::ui.tw-graph.segments.arc :segment="$segment['segment']" />
    @else
        <x-translation-workbench::ui.tw-graph.segments.path :segment="$segment['segment']" />
    @endif
@endforeach
