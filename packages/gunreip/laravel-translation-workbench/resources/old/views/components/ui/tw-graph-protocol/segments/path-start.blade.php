{{-- resources/views/components/ui/tw-graph-protocol/segments/path-start.blade.php --}}
{{--
    Segment: path-start

    Transitional rescue wrapper for the former concrete path-start primitive.
--}}

@props([
    'segment' => [],
])

<x-ui.tw-graph-protocol.primitives.path-start
    :id="data_get($segment, 'id', 'path-start')"
    :direction="data_get($segment, 'direction', 'bottom-top')"
    :length="data_get($segment, 'length', '4rem')"
    :start-x="data_get($segment, 'anchorStart.x', '0rem')"
    :start-y="data_get($segment, 'anchorStart.y', '0rem')"
    :end-x="data_get($segment, 'anchorEnd.x', '0rem')"
    :end-y="data_get($segment, 'anchorEnd.y', '0rem')"
    :node-start="data_get($segment, 'nodeStart', false)"
    :node-end="data_get($segment, 'nodeEnd', true)"
    :color="data_get($segment, 'color', 'cyan')"
/>
