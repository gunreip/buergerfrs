{{-- resources/views/components/ui/tw-graph-protocol/primitives/line.blade.php --}}
{{--
    Primitive: line

    Usage:
    <x-ui.tw-graph-protocol.primitives.line direction="top-bottom" length="4rem" />

    Rule:
    line is neutral. Segments decide whether it becomes path.top-bottom,
    path-start, path-end, merge-path, branch-path, etc.
--}}

@props([
    'id' => 'line',
    'direction' => 'bottom-top',
    'length' => '4rem',
    'startX' => '0rem',
    'startY' => '0rem',
    'endX' => '0rem',
    'endY' => '0rem',
    'nodeStart' => false,
    'nodeEnd' => false,
    'gradient' => false,
    'cap' => false,
    'capLength' => '1.25rem',
    'color' => 'cyan',
])

@if ($gradient)
    <x-ui.tw-graph-protocol.primitives.path-start
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
    />
@elseif ($cap)
    <x-ui.tw-graph-protocol.primitives.path-end
        :id="$id"
        :direction="$direction"
        :length="$length"
        :start-x="$startX"
        :start-y="$startY"
        :end-x="$endX"
        :end-y="$endY"
        :node-start="$nodeStart"
        :node-end="$nodeEnd"
        :cap-length="$capLength"
        :color="$color"
    />
@else
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
    />
@endif
