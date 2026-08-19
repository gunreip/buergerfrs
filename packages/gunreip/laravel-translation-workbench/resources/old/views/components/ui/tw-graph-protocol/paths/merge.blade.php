{{-- resources/views/components/ui/tw-graph-protocol/paths/merge.blade.php --}}
{{--
    Path: merge

    Usage:
    <x-ui.tw-graph-protocol.paths.merge :merge="$merge" side="left" />

    Composition:
    segments.merge-arc-se|merge-arc-sw
    segments.merge-path
    segments.merge-arc-nw|merge-arc-ne

    Protocol rule:
    path.merge is the inbound curve/horizontal/curve composition. Terminal
    vertical strokes belong to path.merge-end so they can be labelled and
    reviewed separately.
--}}

@props([
    'merge' => [],
    'side' => 'left',
])

@php($segments = collect(data_get($merge, 'paths.merge.segments', data_get($merge, 'segments', [])))->filter())

@foreach ($segments as $segment)
    @php($direction = data_get($segment, 'direction'))

    @switch($direction)
        @case('ne')
            <x-ui.tw-graph-protocol.segments.merge-arc-ne :segment="$segment" />
        @break

        @case('nw')
            <x-ui.tw-graph-protocol.segments.merge-arc-nw :segment="$segment" />
        @break

        @case('se')
            <x-ui.tw-graph-protocol.segments.merge-arc-se :segment="$segment" />
        @break

        @case('sw')
            <x-ui.tw-graph-protocol.segments.merge-arc-sw :segment="$segment" />
        @break

        @default
            <x-ui.tw-graph-protocol.segments.merge-path :segment="$segment" />
    @endswitch
@endforeach
