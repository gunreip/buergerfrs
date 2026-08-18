{{-- resources/views/components/ui/tw-graph-protocol/segment/arc.blade.php --}}
{{--
    Usage:
    <x-ui.tw-graph-protocol.segment.arc :segment="$segment" />

    Required segment fields:
    id, direction=se|sw|ne|nw, anchorStart{x,y}, anchorEnd{x,y}
--}}

@props([
    'segment' => [],
])

@php
    $id = data_get($segment, 'id', 'arc');
    $direction = data_get($segment, 'direction', 'se');
    $color = data_get($segment, 'color', 'cyan');
    $primitive = match ($direction) {
        'nw' => 'ui.tw-graph-protocol.primitives.arc-nw',
        'ne' => 'ui.tw-graph-protocol.primitives.arc-ne',
        'sw' => 'ui.tw-graph-protocol.primitives.arc-sw',
        default => 'ui.tw-graph-protocol.primitives.arc-se',
    };
@endphp

<x-dynamic-component
    :component="$primitive"
    :id="$id"
    :start-x="data_get($segment, 'anchorStart.x', '0rem')"
    :start-y="data_get($segment, 'anchorStart.y', '0rem')"
    :end-x="data_get($segment, 'anchorEnd.x', '0rem')"
    :end-y="data_get($segment, 'anchorEnd.y', '0rem')"
    :color="$color"
/>
