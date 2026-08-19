{{-- resources/views/components/ui/tw-graph-protocol/segments/merge-arc-nw.blade.php --}}
{{--
    Segment: merge-arc-nw

    Usage:
    <x-ui.tw-graph-protocol.segments.merge-arc-nw :segment="$segment" />

    Composition:
    primitive arc-nw
--}}

@props([
    'segment' => [],
])

<x-ui.tw-graph-protocol.segment.arc
    :segment="array_replace($segment, ['type' => 'arc', 'direction' => 'nw'])"
/>
