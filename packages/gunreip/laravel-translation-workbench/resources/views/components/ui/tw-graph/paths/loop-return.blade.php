@php
    // Diagnostic settings belong exclusively to the enclosing tw-graph canvas.
    $attributes = ($attributes ?? new \Illuminate\View\ComponentAttributeBag)->except(['dev', 'dev-mode', 'coordinates']);
@endphp
{{-- Downward continuation -> stem -> two rounded corners and bridge -> upward loop re-entry. --}}
@aware(['graphId' => null, 'color' => null, 'arcRadius' => null])
@php
    $inheritedColor = $color;
    $inheritedArcRadius = $arcRadius;
@endphp
@props([
    'id',
    'attachTo' => null,
    'returnTo' => null,
    'anchorStart' => null,
    'anchorReturn' => null,
    'side' => 'left',
    'arcRadius' => null,
    'counterStart' => 1,
    'color' => null,

    'zIndex' => 20,
])

@php
    $twGraphRegionFrame = \Gunreip\TranslationWorkbench\Support\TwGraph\ComponentRegion::begin(
        '...::ui.tw-graph.paths.loop-return',
        \Gunreip\TranslationWorkbench\Support\TwGraph\CanvasDiagnostics::current($__env),
    );
    try {
@endphp

@php
    $graph = $graphId ?: 'tw-graph';
    $arcRadius = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::graphStringFor($arcRadius, $inheritedArcRadius, 'arc_radius', '2.75rem');
    $routeColor = $color ?? $inheritedColor ?? 'zinc';

    $start = filled($attachTo) ? \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get($graph, $attachTo) : $anchorStart;
    $target = filled($returnTo) ? \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get($graph, $returnTo) : $anchorReturn;
    if (! is_array($start) || ! is_array($target)) {
        throw new \InvalidArgumentException('paths.loop-return requires resolved start and return anchors.');
    }
    if (! in_array($side, ['left', 'right'], true)) {
        throw new \InvalidArgumentException('paths.loop-return side must be left or right.');
    }
    $sign = $side === 'left' ? 1 : -1;
    $point = fn ($anchor, $dx, $dy) => [
        'x' => 'calc(' . $anchor['x'] . ' + (' . $dx . '))',
        'y' => 'calc(' . $anchor['y'] . ' + (' . $dy . '))',
    ];
    $stemLength = 'calc(' . $start['y'] . ' - ' . $target['y'] . ')';
    $bridgeLength = 'calc((' . $target['x'] . ' - ' . $start['x'] . ') * ' . $sign . ' - (' . $arcRadius . ' * 2))';
    $layoutIssues = $__env->getConsumableComponentData('twGraphLayoutIssues');
    $validLayout = true;
    foreach (['arcRadius' => $arcRadius, 'remainingStemLength' => $stemLength, 'bridgeLength' => $bridgeLength] as $prop => $length) {
        $value = \Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry::evaluateRemExpression($length);
        if ($value === null || $value < 0 || ($prop === 'arcRadius' && $value == 0)) {
            if ($layoutIssues === null) {
                throw new \InvalidArgumentException("paths.loop-return {$prop} cannot fit the supplied anchors; adjust the authored layout.");
            }
            $layoutIssues->add('paths.loop-return', $id, $prop, $value, $prop === 'arcRadius' ? '> 0rem' : '>= 0rem');
            $validLayout = false;
        }
    }
    if ($validLayout) {
        $provenance = $__env->getConsumableComponentData('twGraphCalculatedLengths');
        $provenance?->record($id . '.stem', 'paths.loop-return', $id, 'remainingStemLength',
            ['start' => $start, 'target' => $target], 'Vertical distance between the supplied anchors.');
        $provenance?->record($id . '.bridge', 'paths.loop-return', $id, 'bridgeLength',
            ['start' => $start, 'target' => $target, 'arcRadius' => $arcRadius, 'side' => $side],
            'Horizontal distance between anchors minus both arc radii.');
        $stemEnd = ['x' => $start['x'], 'y' => $target['y']];
        $bridgeStart = $point($stemEnd, "{$arcRadius} * {$sign}", "{$arcRadius} * -1");
        $bridgeEnd = $point($target, "{$arcRadius} * " . -$sign, "{$arcRadius} * -1");
        $common = ['color' => $routeColor,  'zIndex' => $zIndex,
            'devCounterColor' => $routeColor, 'nodeStart' => false, 'nodeEnd' => true,
            'nodeEndDot' => false, 'jointArrowEnd' => true];
        $counterStart = (int) $counterStart;
        foreach (['anchorNode-start' => $start, 'anchorNode-end' => $target] as $key => $anchor) {
            \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($graph, $id . '.' . $key, array_replace($anchor, [
                'source' => $id, 'sourceType' => 'paths.loop-return', 'sourceAnchor' => $key,
                'direction' => $key === 'anchorNode-start' ? 'top-bottom' : 'bottom-top', 'color' => $routeColor,
            ]));
        }

@endphp
@if (\Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry::evaluateRemExpression($stemLength) > 0)
    <x-translation-workbench::ui.tw-graph.segments.path :segment="array_replace($common, [
        'id' => $id . '.stem', 'anchorStart' => $start, 'anchorEnd' => $stemEnd,
        'direction' => 'top-bottom', 'length' => $stemLength, 'devCounterEnd' => $counterStart,
    ])" />
@endif
<x-translation-workbench::ui.tw-graph.segments.arc :segment="array_replace($common, [
    'id' => $id . '.arc-in', 'anchorStart' => $stemEnd, 'anchorEnd' => $bridgeStart,
    'startAnchor' => $side === 'left' ? 'w' : 'e', 'endAnchor' => 's', 'arcRadius' => $arcRadius,
    'jointArrowEndDirection' => $side === 'left' ? 'right' : 'left', 'devCounterEnd' => $counterStart + 1,
])" />
<x-translation-workbench::ui.tw-graph.segments.path :segment="array_replace($common, [
    'id' => $id . '.bridge', 'anchorStart' => $bridgeStart, 'anchorEnd' => $bridgeEnd,
    'direction' => $side === 'left' ? 'left-right' : 'right-left', 'length' => $bridgeLength,
    'devCounterEnd' => $counterStart + 2,
])" />
<x-translation-workbench::ui.tw-graph.segments.arc :segment="array_replace($common, [
    'id' => $id . '.arc-out', 'anchorStart' => $bridgeEnd, 'anchorEnd' => $target,
    'startAnchor' => 's', 'endAnchor' => $side === 'left' ? 'e' : 'w', 'arcRadius' => $arcRadius,
    'nodeEndDot' => true, 'jointArrowEnd' => false, 'devCounterEnd' => $counterStart + 3,
])" />

@php
    } // Only publish anchors and geometry for a valid return.
    } finally {
        $twGraphRegionDefinition = \Gunreip\TranslationWorkbench\Support\TwGraph\ComponentRegion::finish(
            $twGraphRegionFrame, $id ?? null, $resolvedColor ?? $color ?? 'sky',
        );
    }
@endphp
@if ($twGraphRegionDefinition !== null)
    <script type="application/json" data-tw-graph-component-region>{!! json_encode($twGraphRegionDefinition, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) !!}</script>
@endif
