{{-- packages/gunreip/laravel-translation-workbench/resources/views/components/ui/tw-graph/segments/label-bridge.blade.php --}}
{{--
    Segment: label-bridge

    Places a text label inside the line flow:
    bridge-in -> centered text label -> bridge-out.

    Use this when the label is part of the visual path. For ordinary labels
    attached to a visible anchor node, use segments.label instead.
--}}

@props([
    'id' => 'segment.label-bridge',
    'labelId' => null,
    'label' => [],
    'anchorStart' => ['x' => '0rem', 'y' => '0rem'],
    'direction' => 'left-right',
    'bridgeLength' => null,
    'labelWidth' => null,
    'geometry' => null,
    'color' => 'zinc',
    'pathTone' => 'line',
    'zIndex' => null,
    'dev' => null,
    'devCounterEnd' => 'E',
    'devCounterColor' => 'zinc',
])

@php
    $label = \Gunreip\TranslationWorkbench\Support\TwGraph\TextLabel::normalize($label, 'center', $color)
        ?? ['text' => [], 'color' => $color, 'side' => 'center'];
    $labelColor = data_get($label, 'badgeColor', data_get($label, 'color', $color));
    $resolvedLabelWidth = \Gunreip\TranslationWorkbench\Support\TwGraph\LabelBridge::labelWidth($label, $labelWidth);
    $resolvedBridgeLength = \Gunreip\TranslationWorkbench\Support\TwGraph\LabelBridge::bridgeLength($bridgeLength);
    $geometry = is_array($geometry)
        ? $geometry
        : \Gunreip\TranslationWorkbench\Support\TwGraph\LabelBridge::geometry(
            is_array($anchorStart) ? $anchorStart : ['x' => '0rem', 'y' => '0rem'],
            $direction,
            $resolvedLabelWidth,
            $resolvedBridgeLength,
        );
    $anchorStart = is_array($anchorStart) ? $anchorStart : ['x' => '0rem', 'y' => '0rem'];
    $anchorEnd = data_get($geometry, 'anchorEnd', ['x' => '0rem', 'y' => '0rem']);
    $bridgeInEnd = data_get($geometry, 'bridgeInEnd', ['x' => '0rem', 'y' => '0rem']);
    $bridgeOutStart = data_get($geometry, 'bridgeOutStart', ['x' => '0rem', 'y' => '0rem']);
    $labelAnchor = data_get($geometry, 'labelAnchor', ['x' => '0rem', 'y' => '0rem']);
    $labelId = filled($labelId) ? (string) $labelId : $id . '.label.center.1';
    $labelMaskZIndex = is_numeric($zIndex) ? ((int) $zIndex + 1) : $zIndex;
    $labelZIndex = is_numeric($zIndex) ? ((int) $zIndex + 2) : $zIndex;
@endphp

<x-translation-workbench::ui.tw-graph.segments.path :segment="[
    'id' => $id . '.bridge-in',
    'direction' => $direction,
    'length' => $resolvedBridgeLength,
    'anchorStart' => $anchorStart,
    'anchorEnd' => $bridgeInEnd,
    'nodeStart' => false,
    'nodeEnd' => false,
    'color' => $color,
    'toColor' => $labelColor,
    'colorGradient' => true,
    'tone' => $pathTone,
    'zIndex' => $zIndex,
    'dev' => $dev,
]" />

<x-translation-workbench::ui.tw-graph.primitives.text
    class="tw-graph-protocol-label-bridge-mask"
    :id="$labelId . '.mask'"
    :text="data_get($label, 'text')"
    side="center"
    :anchor-x="data_get($labelAnchor, 'x', '0rem')"
    :anchor-y="data_get($labelAnchor, 'y', '0rem')"
    offset="0rem"
    :badge="data_get($label, 'badge', true)"
    :badge-color="$labelColor"
    :long="data_get($label, 'width') === 'long'"
    :half-long="in_array(data_get($label, 'width'), ['halfLong', 'half-long', 'half_long'], true)"
    :half="in_array(data_get($label, 'width'), ['half', 'halfWidth', 'half-width', 'half_width'], true)"
    :align="data_get($label, 'align', 'center')"
    :justify="data_get($label, 'justify', false)"
    :max-lines="data_get($label, 'maxLines', 3)"
    :z-index="$labelMaskZIndex"
    aria-hidden="true"
/>

<x-translation-workbench::ui.tw-graph.primitives.text
    :id="$labelId"
    :text="data_get($label, 'text')"
    side="center"
    :anchor-x="data_get($labelAnchor, 'x', '0rem')"
    :anchor-y="data_get($labelAnchor, 'y', '0rem')"
    offset="0rem"
    :badge="data_get($label, 'badge', true)"
    :badge-color="$labelColor"
    :long="data_get($label, 'width') === 'long'"
    :half-long="in_array(data_get($label, 'width'), ['halfLong', 'half-long', 'half_long'], true)"
    :half="in_array(data_get($label, 'width'), ['half', 'halfWidth', 'half-width', 'half_width'], true)"
    :align="data_get($label, 'align', 'center')"
    :justify="data_get($label, 'justify', false)"
    :max-lines="data_get($label, 'maxLines', 3)"
    :z-index="$labelZIndex"
/>

<x-translation-workbench::ui.tw-graph.segments.path :segment="[
    'id' => $id . '.bridge-out',
    'direction' => $direction,
    'length' => $resolvedBridgeLength,
    'anchorStart' => $bridgeOutStart,
    'anchorEnd' => $anchorEnd,
    'nodeStart' => false,
    'nodeEnd' => true,
    'nodeEndDot' => false,
    'jointArrowEnd' => true,
    'jointArrowEndColor' => $color,
    'devCounterEnd' => $devCounterEnd,
    'devCounterColor' => $devCounterColor,
    'color' => $labelColor,
    'toColor' => $color,
    'colorGradient' => true,
    'tone' => $pathTone,
    'zIndex' => $zIndex,
    'dev' => $dev,
]" />
