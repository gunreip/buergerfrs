{{-- resources/views/components/ui/tw-graph-protocol/segments/trunk-start.blade.php --}}
{{--
    Segment: trunk-start

    Usage:
    <x-ui.tw-graph-protocol.segments.trunk-start :segment="$segment" />

    Composition:
    primitives.path-start
--}}

@props([
    'segment' => [],
])

<x-ui.tw-graph-protocol.primitives.path-start
    :id="data_get($segment, 'id', 'trunk.start')"
    :direction="data_get($segment, 'direction', 'bottom-top')"
    :length="data_get($segment, 'length', '4rem')"
    :start-x="data_get($segment, 'anchorStart.x', '0rem')"
    :start-y="data_get($segment, 'anchorStart.y', '0rem')"
    :end-x="data_get($segment, 'anchorEnd.x', '0rem')"
    :end-y="data_get($segment, 'anchorEnd.y', '0rem')"
    :node-start="data_get($segment, 'nodeStart', false)"
    :node-end="data_get($segment, 'nodeEnd', true)"
    :color="data_get($segment, 'color', 'green')"
    class="tw-graph-protocol-segment-trunk"
/>
