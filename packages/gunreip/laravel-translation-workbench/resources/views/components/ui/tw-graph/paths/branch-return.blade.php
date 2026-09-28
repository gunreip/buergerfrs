@php
    // Diagnostic settings belong exclusively to the enclosing tw-graph canvas.
    $attributes = ($attributes ?? new \Illuminate\View\ComponentAttributeBag)->except(['dev', 'dev-mode', 'coordinates']);
@endphp
{{-- packages/gunreip/laravel-translation-workbench/resources/views/components/ui/tw-graph/paths/branch-return.blade.php --}}
{{--
    Path: branch-return

    Usage:
    <x-translation-workbench::ui.tw-graph.paths.branch-return
        side="left"
        :anchor-start="['x' => '0rem', 'y' => '0rem']"
        bridge-length="3rem"
    />

    Path role:
    Branch-return routes an outbound branch chain back toward the trunk:
    left:  segments.arc west-north -> segments.path left-right -> segments.arc south-east
    right: segments.arc east-north -> segments.path right-left -> segments.arc south-west
--}}

@aware([
    'color' => null,
])

@php
    $inheritedColor = $color ?? null;



@endphp

@props([
    'id' => 'path.branch-return',
    'side' => 'left',
    'anchorStart' => ['x' => '0rem', 'y' => '0rem'],
    'arcRadius' => null,
    'bridgeLength' => null,
    'color' => null,
    'zIndex' => null,
    'counterStart' => 1,
    'fallbackUsed' => false,
    'fallback' => true,
])

@php
    $twGraphRegionFrame = \Gunreip\TranslationWorkbench\Support\TwGraph\ComponentRegion::begin(
        '...::ui.tw-graph.paths.branch-return',
        \Gunreip\TranslationWorkbench\Support\TwGraph\CanvasDiagnostics::current($__env),
    );
    try {
@endphp


@php
    $add = fn (string $value, string $delta): string => $delta === '0rem' ? $value : 'calc(' . $value . ' + ' . $delta . ')';
    $neg = fn (string $value): string => 'calc(' . $value . ' * -1)';
    $currentAnchor = [
        'x' => data_get($anchorStart, 'x', '0rem'),
        'y' => data_get($anchorStart, 'y', '0rem'),
    ];
    $counter = (int) $counterStart;
    $isLeft = $side === 'left';
    $resolvedColor = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($color, $inheritedColor ?? null, 'zinc');
    $arcRadius = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::localOrGraphString($arcRadius ?? null, 'arc_radius', '2.75rem');
    $bridgeLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($bridgeLength, null, \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::graphString('bridge_length', '4rem'));
    $renderFallbackStyle = (bool) $fallbackUsed && (bool) $fallback;

    $arcInStartAnchor = $isLeft ? 'w' : 'e';
    $arcInEndAnchor = 'n';
    $bridgeDirection = $isLeft ? 'left-right' : 'right-left';
    $bridgeJointArrowDirection = $isLeft ? 'right' : 'left';
    $arcOutStartAnchor = 's';
    $arcOutEndAnchor = $isLeft ? 'e' : 'w';
    $arcDelta = $isLeft ? $arcRadius : $neg($arcRadius);
    $bridgeDelta = $isLeft ? $bridgeLength : $neg($bridgeLength);

    $arcInEnd = [
        'x' => $add($currentAnchor['x'], $arcDelta),
        'y' => $add($currentAnchor['y'], $arcRadius),
    ];
    $bridgeEnd = [
        'x' => $add($arcInEnd['x'], $bridgeDelta),
        'y' => $arcInEnd['y'],
    ];
    $arcOutEnd = [
        'x' => $add($bridgeEnd['x'], $arcDelta),
        'y' => $add($bridgeEnd['y'], $arcRadius),
    ];

    $segments = [
        [
            'component' => 'arc',
            'segment' => [
                'id' => $id . '.arc.in',
                'startAnchor' => $arcInStartAnchor,
                'endAnchor' => $arcInEndAnchor,
                'arcRadius' => $arcRadius,
                'anchorStart' => $currentAnchor,
                'anchorEnd' => $arcInEnd,
                'nodeStart' => false,
                'nodeEnd' => true,
                'nodeEndDot' => false,
                'jointArrowEnd' => true,
                'jointArrowEndDirection' => $bridgeJointArrowDirection,
                'devCounterEnd' => $counter++,
                'devCounterColor' => $resolvedColor,
                'dashed' => $renderFallbackStyle,
                'color' => $resolvedColor,
                'zIndex' => $zIndex,

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
                'nodeEndDot' => false,
                'jointArrowEnd' => true,
                'devCounterEnd' => $counter++,
                'devCounterColor' => $resolvedColor,
                'dashed' => $renderFallbackStyle,
                'color' => $resolvedColor,
                'zIndex' => $zIndex,

            ],
        ],
        [
            'component' => 'arc',
            'segment' => [
                'id' => $id . '.arc.out',
                'startAnchor' => $arcOutStartAnchor,
                'endAnchor' => $arcOutEndAnchor,
                'arcRadius' => $arcRadius,
                'anchorStart' => $bridgeEnd,
                'anchorEnd' => $arcOutEnd,
                'nodeStart' => false,
                'nodeEnd' => true,
                'nodeEndDot' => false,
                'jointArrowEnd' => false,
                'devCounterEnd' => $counter++,
                'devCounterColor' => $resolvedColor,
                'dashed' => $renderFallbackStyle,
                'color' => $resolvedColor,
                'zIndex' => $zIndex,

            ],
        ],
    ];
@endphp



@foreach ($segments as $segment)
    @if ($segment['component'] === 'arc')
        <x-translation-workbench::ui.tw-graph.segments.arc :segment="$segment['segment']" />
    @else
        <x-translation-workbench::ui.tw-graph.segments.path :segment="$segment['segment']" />
    @endif
@endforeach

@php
    } finally {
        $twGraphRegionDefinition = \Gunreip\TranslationWorkbench\Support\TwGraph\ComponentRegion::finish(
            $twGraphRegionFrame, $id ?? null, $resolvedColor ?? $color ?? 'sky',
        );
    }
@endphp
@if ($twGraphRegionDefinition !== null)
    <script type="application/json" data-tw-graph-component-region>{!! json_encode($twGraphRegionDefinition, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) !!}</script>
@endif
