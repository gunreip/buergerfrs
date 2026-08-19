{{-- resources/views/components/ui/tw-graph-protocol/strangs/trunk.blade.php --}}
{{--
    Strang: trunk

    Usage:
    <x-ui.tw-graph-protocol.strangs.trunk :trunk="$protocol['trunk']" />

    Composition:
    paths.trunk

    Structure rule:
    There is only one trunk strang, so strang.trunk and path.trunk currently
    render the same visible line. The strang layer still stays explicit because
    merge/branch will contain multiple strangs built from multiple paths.
--}}

@props([
    'trunk' => [],
    'direction' => 'bottom-top',
])

<x-ui.tw-graph-protocol.paths.trunk
    :trunk="$trunk"
    :direction="data_get($trunk, 'direction', $direction)"
/>
