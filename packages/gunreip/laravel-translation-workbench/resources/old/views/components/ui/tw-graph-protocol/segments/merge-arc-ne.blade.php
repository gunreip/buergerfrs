{{-- resources/views/components/ui/tw-graph-protocol/segments/merge-arc-ne.blade.php --}}
{{--
    Segment: merge-arc-ne

    Usage:
    <x-ui.tw-graph-protocol.segments.merge-arc-ne :segment="$segment" />

    Composition:
    primitive arc-ne
--}}

@props([
    'segment' => [],
])

<x-ui.tw-graph-protocol.segment.arc
    :segment="array_replace($segment, ['type' => 'arc', 'direction' => 'ne'])"
/>
