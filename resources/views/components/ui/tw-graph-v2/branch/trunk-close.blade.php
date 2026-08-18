{{-- resources/views/components/ui/tw-graph-v2/branch/trunk-close.blade.php --}}
{{--
    Usage:
    <x-ui.tw-graph-v2.branch.trunk-close side="right" anchor-index="5" />
    <x-ui.tw-graph-v2.branch.trunk-close
        side="left"
        anchor-index="5"
        connector-horizontal-length="6rem"
        node-left-text="returns"
        color="emerald"
    />

    Optional:
    side="left|right" Default: right. Selects the branch side that returns to the trunk.
    anchor-index="1..8" 1-based visible trunk node counter where the branch returns; no index 0, no trunk border anchor. attach-y overrides it.
    attach-y="10rem" Manual distance from graph canvas bottom to the trunk return node center.
    connector-horizontal-length="4rem" Horizontal connector length between branch-side arc and trunk-side arc.
    node="true|false" Render a colored node on the trunk return point. Default: true.
    node-left-text="..." Optional label directly left of the trunk return node.
    node-left-connector-length="2rem"
    node-left-color-badge="emerald"
    node-right-text="..." Optional label directly right of the trunk return node.
    node-right-connector-length="2rem"
    node-right-color-badge="emerald"
    color="zinc|red|orange|amber|yellow|lime|green|emerald|teal|cyan|sky|blue|indigo|violet|purple|fuchsia|pink|rose"

    Composition:
    branch.trunk-close is the counterpart to branch.trunk. It takes an existing
    branch-side path and bends it back into a selected trunk node.
--}}

@aware([
    'dev' => false,
])

@props([
    'side' => 'right',
    'attachY' => '10rem',
    'anchorIndex' => null,
    'connectorHorizontalLength' => '4rem',
    'node' => true,
    'nodeLeftText' => null,
    'nodeLeftConnectorLength' => '2rem',
    'nodeLeftColorBadge' => null,
    'nodeRightText' => null,
    'nodeRightConnectorLength' => '2rem',
    'nodeRightColorBadge' => null,
    'color' => 'emerald',
    'dev' => false,
    'devPath' => 'tw-graph-v2.branch.trunk-close',
])

@php
    $showNode = filter_var($node, FILTER_VALIDATE_BOOLEAN);
@endphp

<div
    {{ $attributes->class([
        'tw-graph-v2-branch-trunk-close',
        'tw-graph-v2-branch-trunk-close-left' => $side === 'left',
        'tw-graph-v2-branch-trunk-close-right' => $side !== 'left',
        'tw-graph-v2-trunk-anchor-' . $anchorIndex => filled($anchorIndex),
    ])->style([
        '--tw-graph-v2-local-attach-y: ' . $attachY => blank($anchorIndex) && filled($attachY),
        '--tw-graph-v2-local-connector-horizontal-length: ' . $connectorHorizontalLength => filled($connectorHorizontalLength),
    ]) }}
>
    @if ($side === 'left')
        <x-ui.tw-graph-v2.elements.arc.nw
            class="tw-graph-v2-branch-trunk-close-arc-start"
            color="{{ $color }}"
            :dev="$dev"
            dev-path="{{ $devPath }}.{{ $side }}.arc-start"
        />
    @else
        <x-ui.tw-graph-v2.elements.arc.ne
            class="tw-graph-v2-branch-trunk-close-arc-start"
            color="{{ $color }}"
            :dev="$dev"
            dev-path="{{ $devPath }}.{{ $side }}.arc-start"
        />
    @endif

    <x-ui.tw-graph-v2.elements.path
        class="tw-graph-v2-branch-trunk-close-connector-horizontal"
        direction="horizontal"
        length="{{ $connectorHorizontalLength }}"
        color="{{ $color }}"
        :dev="$dev"
        dev-path="{{ $devPath }}.{{ $side }}.connector-horizontal"
    />

    @if ($side === 'left')
        <x-ui.tw-graph-v2.elements.arc.se
            class="tw-graph-v2-branch-trunk-close-arc-end"
            color="{{ $color }}"
            :dev="$dev"
            dev-path="{{ $devPath }}.{{ $side }}.arc-end"
        />
    @else
        <x-ui.tw-graph-v2.elements.arc.sw
            class="tw-graph-v2-branch-trunk-close-arc-end"
            color="{{ $color }}"
            :dev="$dev"
            dev-path="{{ $devPath }}.{{ $side }}.arc-end"
        />
    @endif

    @if ($showNode)
        <div class="tw-graph-v2-branch-trunk-close-node">
            <x-ui.tw-graph-v2.elements.node
                color="{{ $color }}"
                :left-text="$nodeLeftText"
                :left-connector-length="$nodeLeftConnectorLength"
                :left-color-badge="$nodeLeftColorBadge"
                :right-text="$nodeRightText"
                :right-connector-length="$nodeRightConnectorLength"
                :right-color-badge="$nodeRightColorBadge"
                :dev="$dev"
                dev-path="{{ $devPath }}.{{ $side }}.node"
            />
        </div>
    @endif
</div>
