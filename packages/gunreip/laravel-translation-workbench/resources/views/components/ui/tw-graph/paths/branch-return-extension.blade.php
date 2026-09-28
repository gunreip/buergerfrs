@php
    // Diagnostic settings belong exclusively to the enclosing tw-graph canvas.
    $attributes = ($attributes ?? new \Illuminate\View\ComponentAttributeBag)->except(['dev', 'dev-mode', 'coordinates']);
@endphp
{{-- packages/gunreip/laravel-translation-workbench/resources/views/components/ui/tw-graph/paths/branch-return-extension.blade.php --}}
{{--
    Path: branch-return-extension

    Usage:
    <x-translation-workbench::ui.tw-graph.paths.branch-return-extension
        side="left"
        :anchor-start="['x' => '0rem', 'y' => '0rem']"
        stem-length="2rem"
        bridge-length="3rem"
    />

    Path role:
    Branch-return-extension continues a branch-return chain outward:
    left:  segments.path bottom-top -> segments.arc west-north -> segments.path left-right
    right: segments.path bottom-top -> segments.arc east-north -> segments.path right-left
--}}

@aware([
    'color' => null,
])

@php
    $inheritedColor = $color ?? null;



@endphp

@props([
    'id' => 'path.branch-return-extension',
    'side' => 'left',
    'anchorStart' => ['x' => '0rem', 'y' => '0rem'],
    'arcRadius' => null,
    'stemLength' => null,
    'bridgeLength' => null,
    'color' => null,
    'zIndex' => null,
    'counterStart' => 1,
])

@php
    $twGraphRegionFrame = \Gunreip\TranslationWorkbench\Support\TwGraph\ComponentRegion::begin(
        '...::ui.tw-graph.paths.branch-return-extension',
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
    $stemLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::localOrGraphString($stemLength ?? null, 'stem_length', '2rem');
    $bridgeLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($bridgeLength, null, \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::graphString('bridge_length', '4rem'));

    $arcStartAnchor = $isLeft ? 'w' : 'e';
    $arcEndAnchor = 'n';
    $bridgeDirection = $isLeft ? 'left-right' : 'right-left';
    $bridgeJointArrowDirection = $isLeft ? 'right' : 'left';
    $arcDelta = $isLeft ? $arcRadius : $neg($arcRadius);
    $bridgeDelta = $isLeft ? $bridgeLength : $neg($bridgeLength);

    $verticalEnd = [
        'x' => $currentAnchor['x'],
        'y' => $add($currentAnchor['y'], $stemLength),
    ];
    $arcEnd = [
        'x' => $add($verticalEnd['x'], $arcDelta),
        'y' => $add($verticalEnd['y'], $arcRadius),
    ];
    $bridgeEnd = [
        'x' => $add($arcEnd['x'], $bridgeDelta),
        'y' => $arcEnd['y'],
    ];

    $segments = [
        [
            'component' => 'path',
            'segment' => [
                'id' => $id . '.stem',
                'direction' => 'bottom-top',
                'length' => $stemLength,
                'anchorStart' => $currentAnchor,
                'anchorEnd' => $verticalEnd,
                'nodeStart' => false,
                'nodeEnd' => true,
                'nodeEndDot' => false,
                'jointArrowEnd' => true,
                'devCounterEnd' => $counter++,
                'devCounterColor' => $resolvedColor,
                'color' => $resolvedColor,
                'zIndex' => $zIndex,

            ],
        ],
        [
            'component' => 'arc',
            'segment' => [
                'id' => $id . '.arc',
                'startAnchor' => $arcStartAnchor,
                'endAnchor' => $arcEndAnchor,
                'arcRadius' => $arcRadius,
                'anchorStart' => $verticalEnd,
                'anchorEnd' => $arcEnd,
                'nodeStart' => false,
                'nodeEnd' => true,
                'nodeEndDot' => false,
                'jointArrowEnd' => true,
                'jointArrowEndDirection' => $bridgeJointArrowDirection,
                'devCounterEnd' => $counter++,
                'devCounterColor' => $resolvedColor,
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
                'anchorStart' => $arcEnd,
                'anchorEnd' => $bridgeEnd,
                'nodeStart' => false,
                'nodeEnd' => true,
                'nodeEndDot' => false,
                'jointArrowEnd' => true,
                'devCounterEnd' => $counter++,
                'devCounterColor' => $resolvedColor,
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
