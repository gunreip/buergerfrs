{{-- resources/views/components/ui/tw-graph-protocol/paths/trunk.blade.php --}}
{{--
    Path: trunk

    Usage:
    <x-ui.tw-graph-protocol.paths.trunk :trunk="$protocol['trunk']" />

    Composition:
    segments.trunk-start
    segments.trunk-path
    segments.trunk-end

    Protocol rule:
    trunk.paths[*] are path records. Each path contains 1-N segments. Visible
    pathNodes are segment/primitive flags such as nodeStart/nodeEnd.

    Orientation rule:
    direction controls the visual reading order. For bottom-top, start is the
    lower/open anchor and end is the upper/terminal anchor. For top-bottom this
    is mirrored automatically.
--}}

@props([
    'trunk' => [],
    'direction' => 'bottom-top',
])

@php
    $paths = collect(data_get($trunk, 'paths', []))->filter();
    $trunkDirection = data_get($trunk, 'direction', $direction);
    $defaultTerminalTextConnectorLength = data_get($trunk, 'terminalTextConnectorLength', '1.25rem');
    $defaultTerminalTextConnectorGapStart = data_get($trunk, 'terminalTextConnectorGapStart', '0.25rem');
    $defaultTerminalTextConnectorGapEnd = data_get($trunk, 'terminalTextConnectorGapEnd', '0.25rem');
@endphp

@foreach ($paths as $path)
    @php
        $pathDirection = data_get($path, 'direction', $trunkDirection);
        $defaultStartConnectorPlacement = $pathDirection === 'top-bottom' ? 'top' : 'bottom';
        $defaultEndConnectorPlacement = $pathDirection === 'top-bottom' ? 'bottom' : 'top';
        $startSegment = data_get($path, 'segments.start');
        $pathSegments = collect(data_get($path, 'segments.paths', []))->filter();
        $endSegment = data_get($path, 'segments.end');
        $terminalSegment = $endSegment ?: $pathSegments->last() ?: $startSegment;
    @endphp

    @if ($startSegment)
        <x-ui.tw-graph-protocol.segments.trunk-start :segment="$startSegment" />
        <x-ui.tw-graph-protocol.primitives.text-start
            :id="data_get($startSegment, 'id', 'trunk.start') . '.text-start'"
            :text="data_get($path, 'textStart', ['Trunk', data_get($path, 'id', 'path')])"
            :connector-placement="data_get($path, 'textStartConnectorPlacement', $defaultStartConnectorPlacement)"
            :anchor-x="data_get($startSegment, 'anchorStart.x', '0rem')"
            :anchor-y="data_get($startSegment, 'anchorStart.y', '0rem')"
            :connector-length="data_get($path, 'textStartConnectorLength', $defaultTerminalTextConnectorLength)"
            :connector-gap="data_get($path, 'textStartConnectorGap', $defaultTerminalTextConnectorGapStart)"
            :color="data_get($startSegment, 'color', 'green')"
            badge-color="green"
        />
    @endif

    @foreach ($pathSegments as $segment)
        <x-ui.tw-graph-protocol.segments.trunk-path :segment="$segment" />

        @foreach (data_get($segment, 'textLabels', []) as $label)
            <x-ui.tw-graph-protocol.segments.text-label :label="$label" :segment="$segment" />
        @endforeach
    @endforeach

    @if ($endSegment)
        <x-ui.tw-graph-protocol.segments.trunk-end :segment="$endSegment" />
    @endif

    @if ($terminalSegment)
        <x-ui.tw-graph-protocol.primitives.text-end
            :id="data_get($terminalSegment, 'id', 'trunk.end') . '.text-end'"
            :text="data_get($path, 'textEnd', ['Current', 'end anchor'])"
            :connector-placement="data_get($path, 'textEndConnectorPlacement', $defaultEndConnectorPlacement)"
            :anchor-x="data_get($terminalSegment, 'anchorEnd.x', '0rem')"
            :anchor-y="data_get($terminalSegment, 'anchorEnd.y', '0rem')"
            :connector-length="data_get($path, 'textEndConnectorLength', $defaultTerminalTextConnectorLength)"
            :connector-gap="data_get($path, 'textEndConnectorGap', $defaultTerminalTextConnectorGapEnd)"
            :color="data_get($terminalSegment, 'color', 'green')"
            badge-color="green"
        />
    @endif
@endforeach
