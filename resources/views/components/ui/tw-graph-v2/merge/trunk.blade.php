{{-- resources/views/components/ui/tw-graph-v2/merge/trunk.blade.php --}}
{{--
    Usage:
    <x-ui.tw-graph-v2.merge.trunk />
    <x-ui.tw-graph-v2.merge.trunk side="left" attach-y="26rem" color="amber" />
    <x-ui.tw-graph-v2.merge.trunk side="left" anchor-index="3" color="amber" />

    Optional:
    side="left|right" Default: left. The arc corner attaches to the selected side of the trunk.
    anchor-index="1..8" 1-based visible trunk node counter from bottom to top; no index 0, no trunk border anchor. attach-y overrides it.
    attach-y="10rem" Manual distance from graph canvas bottom to the trunk node center.
    connector-horizontal-length="4rem" Horizontal connector length from the inner arc away from the trunk.
    connector-vertical-length="4rem" Optional vertical connector length below the outer arc.
    start-label="Root #701" Optional merge start badge below the vertical connector.
    start-length="4rem" Optional fade-in path length below the merge start node.
    start-badge-color="amber" Optional Flux badge color; defaults to color.
    start-node-left-text="..." Optional label directly left of the merge start node.
    start-node-left-connector-length="2rem"
    start-node-left-color-badge="amber"
    start-node-right-text="..." Optional label directly right of the merge start node.
    start-node-right-connector-length="2rem"
    start-node-right-color-badge="amber"
    start-text="Root #701" Deprecated alias for start-label.
    connector-length="4rem" Deprecated alias for connector-horizontal-length.
    vertical-connector-length="4rem" Deprecated alias for connector-vertical-length.
    color="zinc|red|orange|amber|yellow|lime|green|emerald|teal|cyan|sky|blue|indigo|violet|purple|fuchsia|pink|rose"

    Composition:
    merge.trunk describes an incoming branch merging into a trunk node. It is
    an independent graph section and attaches its arc corner to the trunk center
    by coordinates, not by being nested inside the trunk.
--}}

@aware([
    'dev' => false,
])

@props([
    'side' => 'left',
    'attachY' => '10rem',
    'anchorIndex' => null,
    'connectorHorizontalLength' => null,
    'connectorLength' => null,
    'connectorVerticalLength' => null,
    'verticalConnectorLength' => null,
    'startLabel' => null,
    'startText' => null,
    'startLength' => '4rem',
    'startBadgeColor' => null,
    'startNodeLeftText' => null,
    'startNodeLeftConnectorLength' => '2rem',
    'startNodeLeftColorBadge' => null,
    'startNodeRightText' => null,
    'startNodeRightConnectorLength' => '2rem',
    'startNodeRightColorBadge' => null,
    'color' => 'amber',
    'dev' => false,
    'devPath' => 'tw-graph-v2.merge.trunk',
])

@php
    $resolvedConnectorHorizontalLength = filled($connectorHorizontalLength)
        ? $connectorHorizontalLength
        : (filled($connectorLength) ? $connectorLength : '4rem');

    $resolvedConnectorVerticalLength = filled($connectorVerticalLength)
        ? $connectorVerticalLength
        : $verticalConnectorLength;

    $resolvedStartBadgeColor = $startBadgeColor ?: $color;
    $resolvedStartLabel = filled($startLabel) ? $startLabel : $startText;
    $startBadgeLines = collect(is_iterable($resolvedStartLabel) && !is_string($resolvedStartLabel) ? $resolvedStartLabel : [$resolvedStartLabel])
        ->filter(fn($line) => filled($line))
        ->take(2)
        ->values();
@endphp

<div
    {{ $attributes->class([
        'tw-graph-v2-merge',
        'tw-graph-v2-merge-left' => $side === 'left',
        'tw-graph-v2-merge-right' => $side !== 'left',
        'tw-graph-v2-trunk-anchor-' . $anchorIndex => filled($anchorIndex),
    ])->style([
        '--tw-graph-v2-local-attach-y: ' . $attachY => blank($anchorIndex) && filled($attachY),
        '--tw-graph-v2-local-connector-horizontal-length: ' . $resolvedConnectorHorizontalLength => filled($resolvedConnectorHorizontalLength),
        '--tw-graph-v2-local-connector-vertical-length: ' . $resolvedConnectorVerticalLength => filled($resolvedConnectorVerticalLength),
        '--tw-graph-v2-local-start-length: ' . $startLength => filled($startLength),
    ]) }}
>
    @if ($side === 'left')
        <x-ui.tw-graph-v2.elements.arc.se
            class="tw-graph-v2-merge-arc-inner"
            color="{{ $color }}"
            :dev="$dev"
            dev-path="{{ $devPath }}.{{ $side }}.arc-inner"
        />
    @else
        <x-ui.tw-graph-v2.elements.arc.sw
            class="tw-graph-v2-merge-arc-inner"
            color="{{ $color }}"
            :dev="$dev"
            dev-path="{{ $devPath }}.{{ $side }}.arc-inner"
        />
    @endif

    <x-ui.tw-graph-v2.elements.path
        class="tw-graph-v2-merge-connector-horizontal"
        direction="horizontal"
        length="{{ $resolvedConnectorHorizontalLength }}"
        color="{{ $color }}"
        :dev="$dev"
        dev-path="{{ $devPath }}.{{ $side }}.connector-horizontal"
    />

    @if ($side === 'left')
        <x-ui.tw-graph-v2.elements.arc.nw
            class="tw-graph-v2-merge-arc-outer"
            color="{{ $color }}"
            :dev="$dev"
            dev-path="{{ $devPath }}.{{ $side }}.arc-outer"
        />
    @else
        <x-ui.tw-graph-v2.elements.arc.ne
            class="tw-graph-v2-merge-arc-outer"
            color="{{ $color }}"
            :dev="$dev"
            dev-path="{{ $devPath }}.{{ $side }}.arc-outer"
        />
    @endif

    @if (filled($resolvedConnectorVerticalLength))
        <x-ui.tw-graph-v2.elements.path
            class="tw-graph-v2-merge-connector-vertical"
            direction="vertical"
            length="{{ $resolvedConnectorVerticalLength }}"
            color="{{ $color }}"
            :dev="$dev"
            dev-path="{{ $devPath }}.{{ $side }}.connector-vertical"
        />
    @endif

    @if (filled($resolvedConnectorVerticalLength) && ($startBadgeLines->isNotEmpty() || filled($startLength)))
        <x-ui.tw-graph-v2.merge.start
            :label="$startBadgeLines"
            length="{{ $startLength }}"
            badge-color="{{ $resolvedStartBadgeColor }}"
            :node-left-text="$startNodeLeftText"
            :node-left-connector-length="$startNodeLeftConnectorLength"
            :node-left-color-badge="$startNodeLeftColorBadge"
            :node-right-text="$startNodeRightText"
            :node-right-connector-length="$startNodeRightConnectorLength"
            :node-right-color-badge="$startNodeRightColorBadge"
            color="{{ $color }}"
            :dev="$dev"
            dev-path="{{ $devPath }}.{{ $side }}.start"
        />
    @endif
</div>
