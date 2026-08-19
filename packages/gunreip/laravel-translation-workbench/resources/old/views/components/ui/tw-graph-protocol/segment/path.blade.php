{{-- resources/views/components/ui/tw-graph-protocol/segment/path.blade.php --}}
{{--
    Usage:
    <x-ui.tw-graph-protocol.segment.path :segment="$segment" />

    Required segment fields:
    id, direction, length, anchorStart{x,y}, anchorEnd{x,y}
--}}

@props([
    'segment' => [],
])

@php
    $id = data_get($segment, 'id', 'path');
    $direction = data_get($segment, 'direction', 'bottom-top');
    $color = data_get($segment, 'color', 'cyan');
@endphp

<x-ui.tw-graph-protocol.primitives.path
    :id="$id"
    :direction="$direction"
    :length="data_get($segment, 'length', '4rem')"
    :start-x="data_get($segment, 'anchorStart.x', '0rem')"
    :start-y="data_get($segment, 'anchorStart.y', '0rem')"
    :end-x="data_get($segment, 'anchorEnd.x', '0rem')"
    :end-y="data_get($segment, 'anchorEnd.y', '0rem')"
    :node-start="data_get($segment, 'nodeStart', true)"
    :node-end="data_get($segment, 'nodeEnd', true)"
    :color="$color"
/>
