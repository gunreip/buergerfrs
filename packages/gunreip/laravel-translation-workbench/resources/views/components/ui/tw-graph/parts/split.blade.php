@php
    $attributes = ($attributes ?? new \Illuminate\View\ComponentAttributeBag)->except(['dev', 'dev-mode', 'coordinates']);
@endphp
{{-- Binary horizontal split: the existing fusion segment supplies each outward S-bend. --}}
@aware(['graphId' => null, 'color' => null])
@php $inheritedColor = $color; @endphp
@props([
    'id' => 'part.split', 'anchorStart', 'outputs' => [], 'direction' => 'right-left',
    'arcRadius' => '1.375rem', 'minStemLength' => '1rem', 'color' => null,
    'counterStart' => 1, 'zIndex' => 20,
])
@php
    $frame = \Gunreip\TranslationWorkbench\Support\TwGraph\ComponentRegion::begin(
        '...::ui.tw-graph.parts.split',
        \Gunreip\TranslationWorkbench\Support\TwGraph\CanvasDiagnostics::current($__env),
    );
    try {
        $graph = $graphId ?: 'tw-graph';
        $color = $color ?? $inheritedColor ?? 'zinc';
        $plan = \Gunreip\TranslationWorkbench\Support\TwGraph\SplitGeometry::plan($anchorStart, $outputs, $direction, $arcRadius, $minStemLength);
        \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($graph, $id . '.anchorNode-start', [...$anchorStart, 'direction' => $direction, 'color' => $color]);
@endphp
@foreach ($plan['lanes'] as $index => $lane)
    @php
        $laneId = $id . '.outputs.' . $lane['key'];
        $laneColor = $lane['color'] ?? $color;
        \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($graph, $laneId . '.anchorNode-end', [...$lane['anchor'], 'direction' => $direction, 'color' => $laneColor]);
    @endphp
    <x-translation-workbench::ui.tw-graph.segments.fusion
        :id="$laneId" :anchor-start="$anchorStart" :anchor-end="$lane['anchor']"
        :direction="$direction" :arc-radius="$plan['radius']" min-stem-length="0rem"
        :color="$laneColor" :z-index="$zIndex"
    />
    <x-translation-workbench::ui.tw-graph.segments.path :segment="[
        'id' => $laneId . '.node', 'anchorStart' => $lane['anchor'], 'anchorEnd' => $lane['anchor'],
        'length' => '0rem', 'direction' => $direction, 'nodeEnd' => isset($lane['label']) ? [$lane['label'], null] : true, 'nodeEndDot' => true, 'devCounterEnd' => (int) $counterStart + $index + 1,
        'color' => $laneColor, 'zIndex' => $zIndex + 1,
    ]" />
@endforeach
<x-translation-workbench::ui.tw-graph.segments.path :segment="[
    'id' => $id . '.input', 'anchorStart' => $anchorStart, 'anchorEnd' => $anchorStart,
    'length' => '0rem', 'direction' => $direction, 'nodeEnd' => true, 'nodeEndDot' => true,
    'devCounterEnd' => $counterStart, 'color' => $color, 'zIndex' => $zIndex + 1,
]" />
@php
    } finally {
        $region = \Gunreip\TranslationWorkbench\Support\TwGraph\ComponentRegion::finish($frame, $id, $color ?? 'sky');
    }
@endphp
@if ($region !== null)
    <script type="application/json" data-tw-graph-component-region>{!! json_encode($region, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) !!}</script>
@endif
