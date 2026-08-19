{{-- resources/views/components/ui/tw-graph-protocol/segments/merge-arc-se.blade.php --}}
{{--
    Segment: merge-arc-se

    Usage:
    <x-ui.tw-graph-protocol.segments.merge-arc-se :segment="$segment" />

    Composition:
    primitive arc-se
--}}

@props([
    'segment' => [],
])

<x-ui.tw-graph-protocol.segment.arc
    :segment="array_replace($segment, ['type' => 'arc', 'direction' => 'se'])"
/>
