{{-- resources/views/components/ui/tw-graph-protocol/primitives/path-end.blade.php --}}
{{--
    Primitive path-end element.

    Usage:
    <x-ui.tw-graph-protocol.primitives.path-end ... />

    Rule:
    A path-end is a path primitive with an end-specific cap state.

    Defaults:
    nodeStart=true connects to the previous segment. nodeEnd=false keeps the
    capped/open end free of an artificial connection node.
    capLength controls the horizontal cap marker length.
--}}

@props([
    'id' => 'path-end',
    'direction' => 'bottom-top',
    'length' => '4rem',
    'startX' => '0rem',
    'startY' => '0rem',
    'endX' => '0rem',
    'endY' => '0rem',
    'nodeStart' => true,
    'nodeEnd' => false,
    'capLength' => '1.25rem',
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
    {{ $attributes->class('tw-graph-protocol-primitive-path-end')->style([
        '--tw-graph-protocol-path-end-cap-length: ' . $capLength,
    ]) }}
/>
