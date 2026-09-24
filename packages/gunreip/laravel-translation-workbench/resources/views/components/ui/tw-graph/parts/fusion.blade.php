{{-- Multiple parallel horizontal inputs converge on one shared output. --}}
@aware(['graphId' => null, 'color' => null, 'dev' => false])
@php
    $inheritedColor = $color;
    $inheritedDev = $dev;
@endphp
@props(['id' => 'part.fusion', 'inputs' => [], 'direction' => 'right-left', 'arcRadius' => '1.375rem', 'minStemLength' => '1rem', 'color' => null, 'devMode' => null, 'devCounterEnd' => 1, 'zIndex' => 20])
@php
    $color = $color ?? $inheritedColor ?? 'zinc';
    $devMode = $devMode ?? $inheritedDev;
    $graphId = $graphId ?: 'tw-graph';
    if (!is_array($inputs) || count($inputs) < 2) {
        throw new \InvalidArgumentException('parts.fusion requires at least two inputs.');
    }
    $number = fn ($value) => \Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry::evaluateRemExpression((string) $value);
    $radius = $number($arcRadius);
    $minimumStem = $number($minStemLength);
    if ($radius === null || $minimumStem === null) {
        throw new \InvalidArgumentException('Fusion radius and minimum stem must resolve to rem lengths.');
    }
    $plan = \Gunreip\TranslationWorkbench\Support\TwGraph\FusionGeometry::group($inputs, $direction, $radius, $minimumStem);
    $output = [...$plan['output'], 'color' => $color];
    $sx = $direction === 'left-right' ? 1 : -1;
@endphp
@foreach ($plan['lanes'] as $lane)
    @php
        $laneId = $id . '.inputs.' . $lane['key'];
        $laneColor = $lane['color'] ?? $color;
        $common = ['color' => $laneColor, 'dev' => $devMode, 'zIndex' => $zIndex, 'nodeStart' => false, 'nodeEnd' => false];
        \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($graphId, $laneId, $lane['anchor']);
    @endphp
    @if ($lane['straight'] || $lane['next'] === null)
        <x-translation-workbench::ui.tw-graph.segments.fusion
            :id="$laneId" :anchor-start="$lane['anchor']" :anchor-end="$output"
            :direction="$direction" :arc-radius="$plan['radius'] > 0 ? $plan['radius'] . 'rem' : $arcRadius"
            min-stem-length="0rem" :color="$laneColor" :dev="$devMode" :z-index="$zIndex"
        />
    @else
        @php $lead = abs($number($lane['bendStart']['x']) - $lane['x']); @endphp
        @if ($lead > 0)
            <x-translation-workbench::ui.tw-graph.segments.path :segment="array_merge($common, [
                'id' => $laneId . '.bridge-in', 'anchorStart' => $lane['anchor'], 'anchorEnd' => $lane['bendStart'],
                'direction' => $direction, 'length' => $lead . 'rem',
            ])" />
        @endif
        <x-translation-workbench::ui.tw-graph.segments.arc :segment="array_merge($common, [
            'id' => $laneId . '.arc-in', 'anchorStart' => $lane['bendStart'], 'anchorEnd' => $lane['arcEnd'],
            'startAnchor' => $lane['sy'] > 0 ? 's' : 'n', 'endAnchor' => $sx > 0 ? 'e' : 'w',
            'arcRadius' => $plan['radius'] . 'rem',
            'nodeStart' => true, 'nodeStartDot' => false, 'jointArrowStart' => true,
            'jointArrowStartDirection' => $sx > 0 ? 'right' : 'left', 'devCounterStart' => false,
            'nodeEnd' => true, 'nodeEndDot' => false, 'jointArrowEnd' => true,
            'jointArrowEndDirection' => $lane['sy'] > 0 ? 'top' : 'bottom', 'devCounterEnd' => false,
        ])" />
        <x-translation-workbench::ui.tw-graph.segments.path :segment="array_merge($common, [
            'id' => $laneId . '.stem', 'anchorStart' => $lane['arcEnd'], 'anchorEnd' => $lane['stemEnd'],
            // Incoming outer stems stay below receiving arcs, independent of input order.
            'zIndex' => $zIndex - 1,
            'direction' => $lane['sy'] > 0 ? 'bottom-top' : 'top-bottom',
            'length' => abs($number($lane['stemEnd']['y']) - $number($lane['arcEnd']['y'])) . 'rem',
        ])" />
    @endif
@endforeach
<x-translation-workbench::ui.tw-graph.segments.path :segment="[
    'id' => $id . '.output', 'anchorStart' => $output, 'anchorEnd' => $output, 'length' => '0rem',
    'direction' => $direction, 'nodeEnd' => true, 'nodeEndDot' => true,
    'devCounterEnd' => $devCounterEnd, 'color' => $color, 'dev' => $devMode, 'zIndex' => $zIndex + 1,
]" />
@php
    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($graphId, $id . '.anchorNode-end', $output);
@endphp
