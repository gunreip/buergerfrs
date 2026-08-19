{{-- resources/views/components/ui/tw-graph-protocol/segments/merge-end-path.blade.php --}}
{{--
    Segment: merge-end-path

    Usage:
    <x-ui.tw-graph-protocol.segments.merge-end-path :segment="$segment" />

    Composition:
    primitive path direction=top-bottom

    Layer rule:
    This segment is the vertical path part of path.merge-end. The label is a
    separate merge-end-text segment so path.merge-end is still composed from
    segments, not primitives directly.
--}}

@props([
    'segment' => [],
])

<x-ui.tw-graph-protocol.segment.path :segment="$segment" />
