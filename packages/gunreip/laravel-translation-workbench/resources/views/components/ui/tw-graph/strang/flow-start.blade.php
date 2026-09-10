{{-- packages/gunreip/laravel-translation-workbench/resources/views/components/ui/tw-graph/strang/flow-start.blade.php --}}
{{--
    Strang: flow-start

    Flow is the process-oriented strang family. The first implementation is a
    thin public wrapper around parts.start, so the flow API can be documented
    without duplicating geometry or bypassing the existing part/segment chain.
--}}

@aware([
    'color' => null,
    'dev' => false,
])

@php
    $inheritedColor = $color ?? null;
@endphp

@props([
    'id' => null,
    'componentCounter' => 1,
    'direction' => 'bottom-top',
    'anchorStart' => ['x' => '0rem', 'y' => '0rem'],
    'startLength' => null,
    'length' => null,
    'color' => null,
    'nodeEnd' => true,
    'nodeEndDot' => null,
    'nodeImage' => null,
    'startLabel' => null,
    'startNodeLabels' => [],
    'nodeLabelLeft' => null,
    'nodeLabelRight' => null,
    'counterStart' => 1,
    'devCounterColor' => null,
    'zIndex' => 20,
    'devMode' => null,
])

@php
    $resolvedColor = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(
        $color,
        $inheritedColor ?? null,
        'zinc',
    );
    $startNodeLabels = is_array($startNodeLabels) ? $startNodeLabels : [];
    $resolvedNodeLabelLeft = $nodeLabelLeft ?? data_get($startNodeLabels, 'left');
    $resolvedNodeLabelRight = $nodeLabelRight ?? data_get($startNodeLabels, 'right');
    $resolvedLength = $startLength ?? $length;
@endphp

<x-translation-workbench::ui.tw-graph.parts.start
    :id="$id"
    :component-counter="$componentCounter"
    :direction="$direction"
    :anchor-start="$anchorStart"
    :length="$resolvedLength"
    :color="$resolvedColor"
    :node-end="$nodeEnd"
    :node-end-dot="$nodeEndDot"
    :node-image="$nodeImage"
    :node-label-left="$resolvedNodeLabelLeft"
    :node-label-right="$resolvedNodeLabelRight"
    :dev-counter-end="$counterStart"
    :dev-counter-color="$devCounterColor"
    :start-label="$startLabel"
    :z-index="$zIndex"
    :dev-mode="$devMode"
/>
