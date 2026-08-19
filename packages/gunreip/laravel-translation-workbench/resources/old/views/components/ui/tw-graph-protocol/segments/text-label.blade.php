{{-- resources/views/components/ui/tw-graph-protocol/segments/text-label.blade.php --}}
{{--
    Segment: text-label

    Usage:
    <x-ui.tw-graph-protocol.segments.text-label :label="$label" :segment="$segment" />

    Composition:
    primitive connector
    primitive text-label

    Layer rule:
    This segment composes a helper connector and a visible text label. The
    primitives stay fachlich getrennt.
--}}

@props([
    'label' => [],
    'segment' => [],
])

@php
    $side = data_get($label, 'side', 'right');
    $connectorLength = data_get($label, 'connectorLength', '2rem');
    $connectorGap = data_get($label, 'connectorGap', '0.25rem');
    $anchorX = data_get($label, 'anchor.x', data_get($segment, 'anchorEnd.x', '0rem'));
    $anchorY = data_get($label, 'anchor.y', data_get($segment, 'anchorEnd.y', '0rem'));
    $labelOffset = 'calc(' . $connectorLength . ' + ' . $connectorGap . ')';
    $id = data_get($label, 'id', data_get($segment, 'id', 'segment') . '.text-label.' . $side);
@endphp

<x-ui.tw-graph-protocol.primitives.connector
    :id="$id . '.connector'"
    :placement="$side"
    :anchor-x="$anchorX"
    :anchor-y="$anchorY"
    :length="$connectorLength"
    :gap="$connectorGap"
    :color="data_get($label, 'color', data_get($segment, 'color', 'sky'))"
/>

<x-ui.tw-graph-protocol.primitives.text-label
    :id="$id"
    :text="data_get($label, 'text')"
    :side="$side"
    :anchor-x="$anchorX"
    :anchor-y="$anchorY"
    :offset="$labelOffset"
    :badge-color="data_get($label, 'badgeColor', data_get($label, 'color', 'sky'))"
/>
