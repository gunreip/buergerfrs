{{-- resources/views/components/ui/tw-graph-protocol/segments/path-end.blade.php --}}
{{--
    Segment: path-end

    Transitional rescue wrapper for the former concrete path-end primitive.
--}}

@props([
    'segment' => [],
])

<x-ui.tw-graph-protocol.primitives.path-end
    :id="data_get($segment, 'id', 'path-end')"
    :direction="data_get($segment, 'direction', 'bottom-top')"
    :length="data_get($segment, 'length', '4rem')"
    :start-x="data_get($segment, 'anchorStart.x', '0rem')"
    :start-y="data_get($segment, 'anchorStart.y', '0rem')"
    :end-x="data_get($segment, 'anchorEnd.x', '0rem')"
    :end-y="data_get($segment, 'anchorEnd.y', '0rem')"
    :node-start="data_get($segment, 'nodeStart', true)"
    :node-end="data_get($segment, 'nodeEnd', false)"
    :cap-length="data_get($segment, 'capLength', '1.25rem')"
    :color="data_get($segment, 'color', 'cyan')"
/>
