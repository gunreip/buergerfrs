{{-- resources/views/components/ui/tw-graph-protocol/segments/path.blade.php --}}
{{--
    Segment: path

    Usage:
    <x-ui.tw-graph-protocol.segments.path :segment="$segment" />

    Segment role:
    A concrete graph path segment built from the neutral line primitive.
    Paths decide direction, length, anchors, optional path nodes, gradient,
    and cap behavior. The primitive only renders the requested line shape.

    Required segment fields:
    id, direction, length, anchorStart{x,y}, anchorEnd{x,y}
--}}

@props([
    'segment' => [],
])

@php
    $id = data_get($segment, 'id', 'segment.path');
    $direction = data_get($segment, 'direction', 'bottom-top');
    $color = data_get($segment, 'color', 'cyan');
@endphp

<x-ui.tw-graph-protocol.primitives.line
    :id="$id"
    :direction="$direction"
    :length="data_get($segment, 'length', '4rem')"
    :start-x="data_get($segment, 'anchorStart.x', '0rem')"
    :start-y="data_get($segment, 'anchorStart.y', '0rem')"
    :end-x="data_get($segment, 'anchorEnd.x', '0rem')"
    :end-y="data_get($segment, 'anchorEnd.y', '0rem')"
    :node-start="data_get($segment, 'nodeStart', false)"
    :node-end="data_get($segment, 'nodeEnd', false)"
    :gradient="data_get($segment, 'gradient', false)"
    :cap="data_get($segment, 'cap', false)"
    :cap-length="data_get($segment, 'capLength', '1.25rem')"
    :color="$color"
/>
