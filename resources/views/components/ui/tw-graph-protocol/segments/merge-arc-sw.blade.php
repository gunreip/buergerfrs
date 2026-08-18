{{-- resources/views/components/ui/tw-graph-protocol/segments/merge-arc-sw.blade.php --}}
{{--
    Segment: merge-arc-sw

    Usage:
    <x-ui.tw-graph-protocol.segments.merge-arc-sw :segment="$segment" />

    Composition:
    primitive arc-sw
--}}

@props([
    'segment' => [],
])

<x-ui.tw-graph-protocol.segment.arc
    :segment="array_replace($segment, ['type' => 'arc', 'direction' => 'sw'])"
/>
