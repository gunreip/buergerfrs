{{-- resources/views/components/ui/tw-graph-protocol/segments/trunk-end.blade.php --}}
{{--
    Segment: trunk-end

    Usage:
    <x-ui.tw-graph-protocol.segments.trunk-end :segment="$segment" />

    Composition:
    primitives.path-end
--}}

@props([
    'segment' => [],
])

<x-ui.tw-graph-protocol.primitives.path-end
    :id="data_get($segment, 'id', 'trunk.end')"
    :direction="data_get($segment, 'direction', 'bottom-top')"
    :length="data_get($segment, 'length', '4rem')"
    :start-x="data_get($segment, 'anchorStart.x', '0rem')"
    :start-y="data_get($segment, 'anchorStart.y', '0rem')"
    :end-x="data_get($segment, 'anchorEnd.x', '0rem')"
    :end-y="data_get($segment, 'anchorEnd.y', '0rem')"
    :node-start="data_get($segment, 'nodeStart', true)"
    :node-end="data_get($segment, 'nodeEnd', false)"
    :cap-length="data_get($segment, 'capLength', '1.75rem')"
    :color="data_get($segment, 'color', 'green')"
    class="tw-graph-protocol-segment-trunk"
/>
