{{-- resources/views/components/ui/tw-graph-protocol/segments/text-start.blade.php --}}
{{--
    Segment: text-start

    Transitional rescue wrapper for the former concrete text-start primitive.
--}}

@props([
    'path' => [],
    'segment' => [],
])

<x-ui.tw-graph-protocol.primitives.text-start
    :id="data_get($segment, 'id', 'text-start') . '.text-start'"
    :text="data_get($path, 'textStart')"
    :connector-placement="data_get($path, 'textStartConnectorPlacement', 'bottom')"
    :anchor-x="data_get($segment, 'anchorStart.x', '0rem')"
    :anchor-y="data_get($segment, 'anchorStart.y', '0rem')"
    :connector-length="data_get($path, 'textStartConnectorLength', '0.75rem')"
    :connector-gap="data_get($path, 'textStartConnectorGap')"
    :color="data_get($segment, 'color', 'green')"
    :badge-color="data_get($path, 'textStartBadgeColor', data_get($segment, 'color', 'green'))"
/>
