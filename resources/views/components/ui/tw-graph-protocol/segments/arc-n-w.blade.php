{{-- resources/views/components/ui/tw-graph-protocol/segments/arc-n-w.blade.php --}}
@props(['segment' => []])

<x-ui.tw-graph-protocol.segment.arc :segment="array_replace($segment, ['type' => 'arc', 'direction' => 'nw'])" />
