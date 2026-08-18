{{-- resources/views/components/ui/tw-graph-protocol/paths/merge-end.blade.php --}}
{{--
    Path: merge-end

    Usage:
    <x-ui.tw-graph-protocol.paths.merge-end :merge="$merge" side="left" />

    Composition:
    segments.merge-end-path
    segments.merge-end-text

    Node rule:
    The visible merge-end node sits at the terminal anchor of this vertical path.
    The path itself continues top-bottom into that terminal node.
--}}

@props([
    'merge' => [],
    'side' => 'left',
])

@php
    $path = data_get($merge, 'paths.mergeEnd', data_get($merge, 'paths.merge-end'));
    $segment = data_get($path, 'segment');
@endphp

@if (is_array($segment))
    <x-ui.tw-graph-protocol.segments.merge-end-path :segment="$segment" />
    <x-ui.tw-graph-protocol.segments.merge-end-text :path="$path" :segment="$segment" :side="$side" />
@endif
