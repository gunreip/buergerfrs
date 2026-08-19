{{-- resources/views/components/ui/tw-graph-protocol/primitives/path-start.blade.php --}}
{{--
    Primitive path-start element.

    Usage:
    <x-ui.tw-graph-protocol.primitives.path-start ... />

    Rule:
    A path-start is a path primitive with a start-specific gradient/cap state.

    Defaults:
    nodeStart=false because the open start of a graph path is usually not an
    anchor node. nodeEnd=true marks the first connection point.
--}}

@props([
    'id' => 'path-start',
    'direction' => 'bottom-top',
    'length' => '4rem',
    'startX' => '0rem',
    'startY' => '0rem',
    'endX' => '0rem',
    'endY' => '0rem',
    'nodeStart' => false,
    'nodeEnd' => true,
    'color' => 'cyan',
])

<x-ui.tw-graph-protocol.primitives.path
    :id="$id"
    :direction="$direction"
    :length="$length"
    :start-x="$startX"
    :start-y="$startY"
    :end-x="$endX"
    :end-y="$endY"
    :node-start="$nodeStart"
    :node-end="$nodeEnd"
    :color="$color"
    {{ $attributes->class('tw-graph-protocol-primitive-path-start') }}
/>
