{{-- resources/views/components/ui/tw-graph-v2/branch/extension.blade.php --}}
{{--
    Usage:
    <x-ui.tw-graph-v2.branch.extension
        side="right"
        anchor-index="4"
        parent-offset="2rem"
        connector-horizontal-length="4rem"
        connector-vertical-length="3rem"
        end-label="Branch #905"
    />

    Optional:
    direction="up|down" Default: up. Selects whether the extension bends upward or downward.
    side="left|right" Default: right. Selects the parent branch side.
    anchor-index="1..8" 1-based visible trunk node counter from bottom to top; no index 0, no trunk border anchor. attach-y overrides it.
    attach-y="10rem" Manual distance from graph canvas bottom to the parent branch row.
    parent-offset="2rem" Offset from the parent branch inner arc to the extension join.
    connector-horizontal-length="4rem" Horizontal connector length from parent branch to extension outer arc.
    connector-vertical-length="4rem" Optional vertical connector length above the outer arc.
    end-label="Branch #905" Optional branch extension end badge above the vertical connector.
    end-length="4rem" Optional path length above the branch extension end node.
    end-cap-length="2rem" Optional horizontal cap length above the branch extension end node.
    end-badge-color="rose" Optional Flux badge color; defaults to color.
    end-node-left-text="..." Optional label directly left of the branch extension end node.
    end-node-left-connector-length="2rem"
    end-node-left-color-badge="rose"
    end-node-right-text="..." Optional label directly right of the branch extension end node.
    end-node-right-connector-length="2rem"
    end-node-right-color-badge="rose"
    color="zinc|red|orange|amber|yellow|lime|green|emerald|teal|cyan|sky|blue|indigo|violet|purple|fuchsia|pink|rose"

    Composition:
    branch.extension joins an existing branch horizontal path and extends it
    further outward before bending upward. It is the branch-side counterpart to
    merge.extension.
--}}

@aware([
    'dev' => false,
])

@props([
    'direction' => 'up',
    'side' => 'right',
    'attachY' => '10rem',
    'anchorIndex' => null,
    'parentOffset' => '2rem',
    'connectorHorizontalLength' => '4rem',
    'connectorVerticalLength' => null,
    'endLabel' => null,
    'endLength' => '4rem',
    'endCapLength' => '2rem',
    'endBadgeColor' => null,
    'endNodeLeftText' => null,
    'endNodeLeftConnectorLength' => '2rem',
    'endNodeLeftColorBadge' => null,
    'endNodeRightText' => null,
    'endNodeRightConnectorLength' => '2rem',
    'endNodeRightColorBadge' => null,
    'color' => 'rose',
    'dev' => false,
    'devPath' => 'tw-graph-v2.branch.extension',
])

@php
    $resolvedDirection = $direction === 'down' ? 'down' : 'up';
    $resolvedEndBadgeColor = $endBadgeColor ?: $color;
    $endBadgeLines = collect(is_iterable($endLabel) && !is_string($endLabel) ? $endLabel : [$endLabel])
        ->filter(fn($line) => filled($line))
        ->take(2)
        ->values();
@endphp

<div
    {{ $attributes->class([
        'tw-graph-v2-branch-extension',
        'tw-graph-v2-branch-extension-up' => $resolvedDirection === 'up',
        'tw-graph-v2-branch-extension-down' => $resolvedDirection === 'down',
        'tw-graph-v2-branch-extension-left' => $side === 'left',
        'tw-graph-v2-branch-extension-right' => $side !== 'left',
        'tw-graph-v2-trunk-anchor-' . $anchorIndex => filled($anchorIndex),
    ])->style([
        '--tw-graph-v2-local-attach-y: ' . $attachY => blank($anchorIndex) && filled($attachY),
        '--tw-graph-v2-local-parent-offset: ' . $parentOffset => filled($parentOffset),
        '--tw-graph-v2-local-connector-horizontal-length: ' . $connectorHorizontalLength => filled($connectorHorizontalLength),
        '--tw-graph-v2-local-connector-vertical-length: ' . $connectorVerticalLength => filled($connectorVerticalLength),
        '--tw-graph-v2-local-end-length: ' . $endLength => filled($endLength),
    ]) }}
>
    <x-ui.tw-graph-v2.elements.path
        class="tw-graph-v2-branch-extension-connector-horizontal"
        direction="horizontal"
        length="{{ $connectorHorizontalLength }}"
        color="{{ $color }}"
        :dev="$dev"
        dev-path="{{ $devPath }}.{{ $side }}.{{ $resolvedDirection }}.connector-horizontal"
    />

    @if ($side === 'left' && $resolvedDirection === 'up')
        <x-ui.tw-graph-v2.elements.arc.sw
            class="tw-graph-v2-branch-extension-arc-outer"
            color="{{ $color }}"
            :dev="$dev"
            dev-path="{{ $devPath }}.{{ $side }}.{{ $resolvedDirection }}.arc-outer"
        />
    @elseif ($side !== 'left' && $resolvedDirection === 'up')
        <x-ui.tw-graph-v2.elements.arc.se
            class="tw-graph-v2-branch-extension-arc-outer"
            color="{{ $color }}"
            :dev="$dev"
            dev-path="{{ $devPath }}.{{ $side }}.{{ $resolvedDirection }}.arc-outer"
        />
    @elseif ($side === 'left')
        <x-ui.tw-graph-v2.elements.arc.nw
            class="tw-graph-v2-branch-extension-arc-outer"
            color="{{ $color }}"
            :dev="$dev"
            dev-path="{{ $devPath }}.{{ $side }}.{{ $resolvedDirection }}.arc-outer"
        />
    @else
        <x-ui.tw-graph-v2.elements.arc.ne
            class="tw-graph-v2-branch-extension-arc-outer"
            color="{{ $color }}"
            :dev="$dev"
            dev-path="{{ $devPath }}.{{ $side }}.{{ $resolvedDirection }}.arc-outer"
        />
    @endif

    @if (filled($connectorVerticalLength))
        <x-ui.tw-graph-v2.elements.path
            class="tw-graph-v2-branch-extension-connector-vertical"
            direction="vertical"
            length="{{ $connectorVerticalLength }}"
            color="{{ $color }}"
            :dev="$dev"
            dev-path="{{ $devPath }}.{{ $side }}.{{ $resolvedDirection }}.connector-vertical"
        />
    @endif

    @if (filled($connectorVerticalLength) && ($endBadgeLines->isNotEmpty() || filled($endLength)))
        <div class="tw-graph-v2-branch-extension-end">
            <x-ui.tw-graph-v2.elements.node
                color="{{ $color }}"
                :left-text="$endNodeLeftText"
                :left-connector-length="$endNodeLeftConnectorLength"
                :left-color-badge="$endNodeLeftColorBadge"
                :right-text="$endNodeRightText"
                :right-connector-length="$endNodeRightConnectorLength"
                :right-color-badge="$endNodeRightColorBadge"
                :dev="$dev"
                dev-path="{{ $devPath }}.{{ $side }}.{{ $resolvedDirection }}.end-node"
            />

            @if (filled($endLength))
                <x-ui.tw-graph-v2.elements.path
                    length="{{ $endLength }}"
                    color="{{ $color }}"
                    :dev="$dev"
                    dev-path="{{ $devPath }}.{{ $side }}.{{ $resolvedDirection }}.end-path"
                />
            @endif

            @if ($endBadgeLines->isNotEmpty())
                <x-ui.tw-graph-v2.elements.path
                    class="tw-graph-v2-branch-extension-end-cap tw-graph-v2-trunk-end-cap"
                    direction="horizontal"
                    length="{{ $endCapLength }}"
                    color="{{ $color }}"
                    :dev="$dev"
                    dev-path="{{ $devPath }}.{{ $side }}.{{ $resolvedDirection }}.end-cap"
                />

                <span class="tw-graph-v2-branch-extension-end-badge">
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
