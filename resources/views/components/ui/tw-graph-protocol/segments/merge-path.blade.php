{{-- resources/views/components/ui/tw-graph-protocol/segments/merge-path.blade.php --}}
{{--
    Segment: merge-path

    Usage:
    <x-ui.tw-graph-protocol.segments.merge-path :segment="$segment" />

    Composition:
    primitive path direction=left-right|right-left|top-bottom|bottom-top
--}}

@props([
    'segment' => [],
])

<x-ui.tw-graph-protocol.segment.path :segment="$segment" />
