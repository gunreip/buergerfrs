{{-- resources/views/components/ui/tw-graph-protocol/segments/text-end.blade.php --}}
{{--
    Segment: text-end

    Transitional rescue wrapper for the former concrete text-end primitive.
--}}

@props([
    'path' => [],
    'segment' => [],
])

<x-ui.tw-graph-protocol.primitives.text-end
    :id="data_get($segment, 'id', 'text-end') . '.text-end'"
    :text="data_get($path, 'textEnd')"
    :connector-placement="data_get($path, 'textEndConnectorPlacement', 'top')"
    :anchor-x="data_get($segment, 'anchorEnd.x', '0rem')"
    :anchor-y="data_get($segment, 'anchorEnd.y', '0rem')"
    :connector-length="data_get($path, 'textEndConnectorLength', '0.75rem')"
    :connector-gap="data_get($path, 'textEndConnectorGap')"
    :color="data_get($segment, 'color', 'green')"
    :badge-color="data_get($path, 'textEndBadgeColor', data_get($segment, 'color', 'green'))"
/>
