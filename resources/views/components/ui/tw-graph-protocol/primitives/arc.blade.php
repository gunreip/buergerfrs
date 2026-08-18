{{-- resources/views/components/ui/tw-graph-protocol/primitives/arc.blade.php --}}
{{--
    Primitive: arc

    Usage:
    <x-ui.tw-graph-protocol.primitives.arc start-anchor="e" end-anchor="s" />

    Rule:
    arc is neutral. Segments decide which anchor pair is required.
--}}

@props([
    'id' => 'arc',
    'startAnchor' => 'e',
    'endAnchor' => 's',
    'startX' => '0rem',
    'startY' => '0rem',
    'endX' => '0rem',
    'endY' => '0rem',
    'color' => 'cyan',
])

@php
    $primitive = match ($startAnchor . '-' . $endAnchor) {
        'w-s' => 'ui.tw-graph-protocol.primitives.arc-sw',
        'n-w' => 'ui.tw-graph-protocol.primitives.arc-nw',
        'n-e' => 'ui.tw-graph-protocol.primitives.arc-ne',
        default => 'ui.tw-graph-protocol.primitives.arc-se',
    };
@endphp

<x-dynamic-component
    :component="$primitive"
    :id="$id"
    :start-x="$startX"
    :start-y="$startY"
    :end-x="$endX"
    :end-y="$endY"
    :color="$color"
/>
