{{-- resources/views/components/ui/tw-graph-protocol/segments/merge-end-text.blade.php --}}
{{--
    Segment: merge-end-text

    Usage:
    <x-ui.tw-graph-protocol.segments.merge-end-text :path="$path" :segment="$segment" side="left" />

    Composition:
    primitive text-end

    Layer rule:
    Text is a segment of path.merge-end. The anchor is taken from the resolved
    merge-end path segment, normally anchorEnd.
--}}

@props([
    'path' => [],
    'segment' => [],
    'side' => 'left',
])

@php
    $textAnchor = data_get($path, 'textEndAnchor', 'anchorEnd');
    $anchor = $textAnchor === 'anchorStart'
        ? data_get($segment, 'anchorStart', [])
        : data_get($segment, 'anchorEnd', []);
@endphp

<x-ui.tw-graph-protocol.primitives.text-end
    :id="data_get($segment, 'id', 'merge-end') . '.text-end'"
    :text="data_get($path, 'textEnd', ['Merge end', $side])"
    :connector-placement="data_get($path, 'textEndConnectorPlacement', 'bottom')"
    :anchor-x="data_get($anchor, 'x', '0rem')"
    :anchor-y="data_get($anchor, 'y', '0rem')"
    :connector-length="data_get($path, 'textEndConnectorLength', '1.25rem')"
    :connector-gap="data_get($path, 'textEndConnectorGap', '0.25rem')"
    :color="data_get($segment, 'color', 'amber')"
    badge-color="amber"
/>
