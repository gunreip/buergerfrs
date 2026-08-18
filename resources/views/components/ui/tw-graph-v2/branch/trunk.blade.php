{{-- resources/views/components/ui/tw-graph-v2/branch/trunk.blade.php --}}
{{--
    Usage:
    <x-ui.tw-graph-v2.branch.trunk side="right" anchor-index="3" color="rose" />
    <x-ui.tw-graph-v2.branch.trunk
        side="left"
        anchor-index="3"
        connector-horizontal-length="5rem"
        connector-vertical-length="4rem"
        end-label="Branch #901"
    />

    Optional:
    side="left|right" Default: right. Selects the trunk side the branch leaves from.
    anchor-index="1..8" 1-based visible trunk node counter from bottom to top; no index 0, no trunk border anchor. attach-y overrides it.
    attach-y="10rem" Manual distance from graph canvas bottom to the trunk node center.
    connector-horizontal-length="4rem" Horizontal connector length away from the trunk.
    connector-vertical-length="4rem" Optional vertical connector length above the outer arc.
    end-label="Branch #901" Optional branch end badge above the vertical connector.
    end-length="4rem" Optional path length above the branch end node.
    end-badge-color="rose" Optional Flux badge color; defaults to color.
    end-node-left-text="..." Optional label directly left of the branch end node.
    end-node-left-connector-length="2rem"
    end-node-left-color-badge="rose"
    end-node-right-text="..." Optional label directly right of the branch end node.
    end-node-right-connector-length="2rem"
    end-node-right-color-badge="rose"
    color="zinc|red|orange|amber|yellow|lime|green|emerald|teal|cyan|sky|blue|indigo|violet|purple|fuchsia|pink|rose"

    Composition:
    branch.trunk describes a path that leaves the trunk and continues upward.
    It is the directional counterpart to merge.trunk.
--}}

@aware([
    'dev' => false,
])

@props([
    'side' => 'right',
    'attachY' => '10rem',
    'anchorIndex' => null,
    'connectorHorizontalLength' => '4rem',
    'connectorVerticalLength' => null,
    'endLabel' => null,
    'endLength' => '4rem',
    'endBadgeColor' => null,
    'endNodeLeftText' => null,
    'endNodeLeftConnectorLength' => '2rem',
    'endNodeLeftColorBadge' => null,
    'endNodeRightText' => null,
    'endNodeRightConnectorLength' => '2rem',
    'endNodeRightColorBadge' => null,
    'color' => 'rose',
    'dev' => false,
    'devPath' => 'tw-graph-v2.branch.trunk',
])

@php
    $resolvedEndBadgeColor = $endBadgeColor ?: $color;
    $endBadgeLines = collect(is_iterable($endLabel) && !is_string($endLabel) ? $endLabel : [$endLabel])
        ->filter(fn($line) => filled($line))
        ->take(2)
        ->values();
@endphp

<div
    {{ $attributes->class([
        'tw-graph-v2-branch',
        'tw-graph-v2-branch-left' => $side === 'left',
        'tw-graph-v2-branch-right' => $side !== 'left',
        'tw-graph-v2-trunk-anchor-' . $anchorIndex => filled($anchorIndex),
    ])->style([
        '--tw-graph-v2-local-attach-y: ' . $attachY => blank($anchorIndex) && filled($attachY),
        '--tw-graph-v2-local-connector-horizontal-length: ' . $connectorHorizontalLength => filled($connectorHorizontalLength),
        '--tw-graph-v2-local-connector-vertical-length: ' . $connectorVerticalLength => filled($connectorVerticalLength),
        '--tw-graph-v2-local-end-length: ' . $endLength => filled($endLength),
    ]) }}
>
    @if ($side === 'left')
        <x-ui.tw-graph-v2.elements.arc.ne
            class="tw-graph-v2-branch-arc-inner"
            color="{{ $color }}"
            :dev="$dev"
            dev-path="{{ $devPath }}.{{ $side }}.arc-inner"
        />
    @else
        <x-ui.tw-graph-v2.elements.arc.nw
            class="tw-graph-v2-branch-arc-inner"
            color="{{ $color }}"
            :dev="$dev"
            dev-path="{{ $devPath }}.{{ $side }}.arc-inner"
        />
    @endif

    <x-ui.tw-graph-v2.elements.path
        class="tw-graph-v2-branch-connector-horizontal"
        direction="horizontal"
        length="{{ $connectorHorizontalLength }}"
        color="{{ $color }}"
        :dev="$dev"
        dev-path="{{ $devPath }}.{{ $side }}.connector-horizontal"
    />

    @if ($side === 'left')
        <x-ui.tw-graph-v2.elements.arc.sw
            class="tw-graph-v2-branch-arc-outer"
            color="{{ $color }}"
            :dev="$dev"
            dev-path="{{ $devPath }}.{{ $side }}.arc-outer"
        />
    @else
        <x-ui.tw-graph-v2.elements.arc.se
            class="tw-graph-v2-branch-arc-outer"
            color="{{ $color }}"
            :dev="$dev"
            dev-path="{{ $devPath }}.{{ $side }}.arc-outer"
        />
    @endif

    @if (filled($connectorVerticalLength))
        <x-ui.tw-graph-v2.elements.path
            class="tw-graph-v2-branch-connector-vertical"
            direction="vertical"
            length="{{ $connectorVerticalLength }}"
            color="{{ $color }}"
            :dev="$dev"
            dev-path="{{ $devPath }}.{{ $side }}.connector-vertical"
        />
    @endif

    @if (filled($connectorVerticalLength) && ($endBadgeLines->isNotEmpty() || filled($endLength)))
        <div class="tw-graph-v2-branch-end">
            <x-ui.tw-graph-v2.elements.node
                color="{{ $color }}"
                :left-text="$endNodeLeftText"
                :left-connector-length="$endNodeLeftConnectorLength"
                :left-color-badge="$endNodeLeftColorBadge"
                :right-text="$endNodeRightText"
                :right-connector-length="$endNodeRightConnectorLength"
                :right-color-badge="$endNodeRightColorBadge"
                :dev="$dev"
                dev-path="{{ $devPath }}.{{ $side }}.end-node"
            />

            <x-ui.tw-graph-v2.elements.path
                length="{{ $endLength }}"
                color="{{ $color }}"
                :dev="$dev"
                dev-path="{{ $devPath }}.{{ $side }}.end-path"
            />

            @if ($endBadgeLines->isNotEmpty())
                <span class="tw-graph-v2-branch-end-badge">
                    <flux:badge color="{{ $resolvedEndBadgeColor }}">
                        <span class="inline-flex max-w-56 flex-col items-center gap-0.5 text-center leading-tight">
                            @foreach ($endBadgeLines as $endBadgeLine)
                                <span @class(['text-xs' => !$loop->first])>
                                    {{ $endBadgeLine }}
                                </span>
                            @endforeach
                        </span>
                    </flux:badge>
                </span>
            @endif
        </div>
    @endif
</div>
