@php
    // Diagnostic settings belong exclusively to the enclosing tw-graph canvas.
    $attributes = ($attributes ?? new \Illuminate\View\ComponentAttributeBag)->except(['dev', 'dev-mode', 'coordinates']);
@endphp
{{-- Closed side loop built from shared arc, path, label-bridge and label segments.
     anchorStart is the upper split; anchorReturn is the lower re-entry on the same axis.
     Names true/false are stable rendered suffixes, not routing conditions. --}}
@aware(['graphId' => null, 'color' => null, 'arcRadius' => null])
@php
    $inheritedColor = $color;
    $inheritedArcRadius = $arcRadius;
@endphp
@props([
    'id',
    'anchorStart',
    'anchorReturn',
    'side' => 'left',
    'counterStart' => 2,
    'return' => true,
    'arcRadius' => null,
    'entryBridgeLength' => '2rem',
    'bridgeLength' => '4rem',
    'bridgeOutLength' => '4rem',
    'exitLength' => '4rem',
    'bridgeLabel' => [],
    'entryLabel' => [],
    'entryLabelAnchor' => null,
    'exitLabel' => [],
    'color' => null,

    'zIndex' => 20,
])
@php
    $graph = $graphId ?: 'tw-graph';
    $arcRadius = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::graphStringFor($arcRadius, $inheritedArcRadius, 'arc_radius', '2.75rem');
    $counterStart = (int) $counterStart;
    $closeLoop = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::bool($return, true);
    $loopColor = $color ?? $inheritedColor ?? 'zinc';

    $bodyColor = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(data_get($bridgeLabel, 'color'), $loopColor, 'zinc');
    $exitColor = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(data_get($exitLabel, 'color'), $loopColor, 'zinc');
    $labelOnBridge = $entryLabelAnchor === null;
    if (! in_array($side, ['left', 'right'], true)) {
        throw new \InvalidArgumentException('paths.loop side must be left or right.');
    }
    foreach (compact('arcRadius', 'entryBridgeLength', 'bridgeLength', 'bridgeOutLength', 'exitLength') as $prop => $length) {
        $value = \Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry::evaluateRemExpression($length);
        if ($value === null || $value <= 0) {
            throw new \InvalidArgumentException("paths.loop {$prop} must be a positive rem length.");
        }
    }
    $sign = $side === 'left' ? -1 : 1;
    $point = fn ($anchor, $dx, $dy) => [
        'x' => 'calc(' . $anchor['x'] . ' + (' . $dx . '))',
        'y' => 'calc(' . $anchor['y'] . ' + (' . $dy . '))',
    ];
    $start = $anchorReturn;
    $questionEnd = $anchorStart;
    $height = 'calc(' . $questionEnd['y'] . ' - ' . $start['y'] . ')';
    $heightValue = \Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry::evaluateRemExpression($height);
    $axisOffset = \Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry::evaluateRemExpression('calc(' . $questionEnd['x'] . ' - ' . $start['x'] . ')');
    if ($heightValue === null || $heightValue <= 0 || $axisOffset !== 0.0) {
        throw new \InvalidArgumentException('paths.loop requires a return anchor below the start on the same X axis.');
    }
    $arcInEnd = $point($questionEnd, "{$arcRadius} * {$sign}", $arcRadius);
    $trueBridgeEnd = $point($arcInEnd, "{$entryBridgeLength} * {$sign}", '0rem');
    $labelAnchor = $entryLabelAnchor ?? $trueBridgeEnd;
    // Shared label geometry retains the explicitly supplied bridge lengths.
    $bodyGeometry = \Gunreip\TranslationWorkbench\Support\TwGraph\LabelBridge::geometry(
        $trueBridgeEnd, $side === 'left' ? 'right-left' : 'left-right',
        \Gunreip\TranslationWorkbench\Support\TwGraph\LabelBridge::labelWidth($bridgeLabel),
        $bridgeLength, $bridgeOutLength,
    );
    $bodyEnd = $bodyGeometry['anchorEnd'];
    $turnEnd = $point($bodyEnd, "{$arcRadius} * {$sign}", "{$arcRadius} * -1");
    $exit = $point($questionEnd, '0rem', $exitLength);

    $common = ['color' => $loopColor, 'devCounterColor' => $loopColor,  'zIndex' => $zIndex,
        'nodeStart' => false, 'nodeEnd' => true, 'nodeEndDot' => false, 'jointArrowEnd' => true];
    $bodyStyle = array_replace($common, ['color' => $bodyColor, 'devCounterColor' => $bodyColor]);
    $arcs = [
        ['id' => $id . '.true.arc-in', 'anchorStart' => $questionEnd, 'anchorEnd' => $arcInEnd,
            'startAnchor' => $sign < 0 ? 'e' : 'w', 'endAnchor' => 'n', 'jointArrowEndDirection' => $sign < 0 ? 'left' : 'right', 'devCounterEnd' => $counterStart],
        ['id' => $id . '.body.arc-out', 'anchorStart' => $bodyEnd, 'anchorEnd' => $turnEnd,
            'startAnchor' => 'n', 'endAnchor' => $sign < 0 ? 'w' : 'e', 'jointArrowEndDirection' => 'bottom', 'devCounterEnd' => $counterStart + 3],
    ];
    foreach (['anchorNode-start' => $questionEnd, 'true.bridge.anchorNode-end' => $trueBridgeEnd, 'body.anchorNode-end' => $turnEnd, 'anchorNode-return' => $start, 'anchorNode-end' => $exit] as $key => $anchor) {
        \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($graph, $id . '.' . $key, array_replace($anchor, [
            'source' => $id, 'sourceType' => 'paths.loop', 'sourceAnchor' => $key,
            'direction' => $key === 'true.bridge.anchorNode-end' ? ($side === 'left' ? 'right-left' : 'left-right') : ($key === 'body.anchorNode-end' ? 'top-bottom' : 'bottom-top'), 'color' => in_array($key, ['true.bridge.anchorNode-end', 'body.anchorNode-end'], true) ? $bodyColor : ($key === 'anchorNode-end' ? $exitColor : $loopColor),
        ]));
    }
@endphp
@foreach ($arcs as $arc)
    <x-translation-workbench::ui.tw-graph.segments.arc :segment="array_replace($bodyStyle, ['arcRadius' => $arcRadius], $arc)" />
@endforeach
<x-translation-workbench::ui.tw-graph.segments.path :segment="array_replace($bodyStyle, [
    'id' => $id . '.true.bridge', 'anchorStart' => $arcInEnd, 'anchorEnd' => $trueBridgeEnd,
    'direction' => $side === 'left' ? 'right-left' : 'left-right', 'length' => $entryBridgeLength,
    'nodeEndDot' => $labelOnBridge, 'jointArrowEnd' => ! $labelOnBridge, 'devCounterEnd' => $counterStart + 1,
])" />
<x-translation-workbench::ui.tw-graph.segments.label-bridge
    :id="$id . '.body.bridge'" :anchor-start="$trueBridgeEnd" :geometry="$bodyGeometry"
    :direction="$side === 'left' ? 'right-left' : 'left-right'"
    :label="$bridgeLabel" :bridge-length="$bridgeLength" :color="$bodyColor" :z-index="$zIndex" :dev-counter-end="$counterStart + 2" :dev-counter-color="$bodyColor"
/>
@if ($closeLoop)
    <x-translation-workbench::ui.tw-graph.paths.loop-return
        :id="$id . '.return'" :anchor-start="$turnEnd" :anchor-return="$start"
        :side="$side" :arc-radius="$arcRadius" :counter-start="$counterStart + 4"
        :color="$loopColor" :z-index="$zIndex"
    />
@endif
<x-translation-workbench::ui.tw-graph.segments.path :segment="array_replace($common, [
    'color' => $exitColor, 'devCounterColor' => $exitColor,
    'id' => $id . '.false.stem', 'anchorStart' => $questionEnd, 'anchorEnd' => $exit,
    'direction' => 'bottom-top', 'length' => $exitLength, 'devCounterEnd' => $counterStart + ($closeLoop ? 8 : 4), 'nodeEndDot' => true, 'jointArrowEnd' => false,
])" />
<x-translation-workbench::ui.tw-graph.segments.label
    :id="$id . '.true.label'" :anchor-x="$labelAnchor['x']" :anchor-y="$labelAnchor['y']"
    :label="$entryLabel" :side="data_get($entryLabel, 'side', 'top')" :color="$loopColor"
/>
<x-translation-workbench::ui.tw-graph.segments.label
    :id="$id . '.false.label'" :anchor-x="$exit['x']" :anchor-y="$exit['y']"
    :label="$exitLabel" :side="data_get($exitLabel, 'side', $side === 'left' ? 'right' : 'left')" :color="$exitColor"
/>
