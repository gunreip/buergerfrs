{{-- resources/views/components/ui/tw-graph-v2/merge/extension.blade.php --}}
{{--
    Usage:
    <x-ui.tw-graph-v2.merge.extension side="left" anchor-index="1" parent-offset="4rem" />
    <x-ui.tw-graph-v2.merge.extension
        side="right"
        anchor-index="1"
        parent-offset="2rem"
        connector-horizontal-length="4rem"
        connector-vertical-length="3rem"
        start-label="Root #703"
    />

    Optional:
    side="left|right" Default: left. Selects the parent merge side.
    anchor-index="1..8" 1-based visible trunk node counter from bottom to top; no index 0, no trunk border anchor. attach-y overrides it.
    attach-y="10rem" Manual distance from graph canvas bottom to the parent merge row.
    parent-offset="2rem" Offset from the parent merge inner arc to the extension join.
    connector-horizontal-length="4rem" Horizontal connector length from parent path to extension outer arc.
    connector-vertical-length="4rem" Optional vertical connector length below the outer arc.
    start-label="Root #701" Optional merge extension start badge below the vertical connector.
    start-length="4rem" Optional fade-in path length below the merge extension start node.
    start-badge-color="amber" Optional Flux badge color; defaults to color.
    start-node-left-text="..." Optional label directly left of the merge extension start node.
    start-node-left-connector-length="2rem"
    start-node-left-color-badge="amber"
    start-node-right-text="..." Optional label directly right of the merge extension start node.
    start-node-right-connector-length="2rem"
    start-node-right-color-badge="amber"
    color="zinc|red|orange|amber|yellow|lime|green|emerald|teal|cyan|sky|blue|indigo|violet|purple|fuchsia|pink|rose"

    Composition:
    merge.extension joins an existing merge branch without an inner arc. It
    attaches directly to the parent merge horizontal connector by coordinates,
    then adds another root/start path outward from that branch.
--}}

@aware([
    'dev' => false,
])

@props([
    'side' => 'left',
    'attachY' => '10rem',
    'anchorIndex' => null,
    'parentOffset' => '2rem',
    'connectorHorizontalLength' => '4rem',
    'connectorVerticalLength' => null,
    'startLabel' => null,
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
    'devPath' => 'tw-graph-v2.merge.extension',
])

@php
    $resolvedStartBadgeColor = $startBadgeColor ?: $color;
    $startBadgeLines = collect(is_iterable($startLabel) && !is_string($startLabel) ? $startLabel : [$startLabel])
        ->filter(fn($line) => filled($line))
        ->take(2)
        ->values();
@endphp

<div
    {{ $attributes->class([
        'tw-graph-v2-merge-extension',
        'tw-graph-v2-merge-extension-left' => $side === 'left',
        'tw-graph-v2-merge-extension-right' => $side !== 'left',
        'tw-graph-v2-trunk-anchor-' . $anchorIndex => filled($anchorIndex),
    ])->style([
        '--tw-graph-v2-local-attach-y: ' . $attachY => blank($anchorIndex) && filled($attachY),
        '--tw-graph-v2-local-parent-offset: ' . $parentOffset => filled($parentOffset),
        '--tw-graph-v2-local-connector-horizontal-length: ' . $connectorHorizontalLength => filled($connectorHorizontalLength),
        '--tw-graph-v2-local-connector-vertical-length: ' . $connectorVerticalLength => filled($connectorVerticalLength),
        '--tw-graph-v2-local-start-length: ' . $startLength => filled($startLength),
    ]) }}
>
    <x-ui.tw-graph-v2.elements.path
        class="tw-graph-v2-merge-extension-connector-horizontal"
        direction="horizontal"
        length="{{ $connectorHorizontalLength }}"
        color="{{ $color }}"
        :dev="$dev"
        dev-path="{{ $devPath }}.{{ $side }}.connector-horizontal"
    />

    @if ($side === 'left')
        <x-ui.tw-graph-v2.elements.arc.nw
            class="tw-graph-v2-merge-extension-arc-outer"
            color="{{ $color }}"
            :dev="$dev"
            dev-path="{{ $devPath }}.{{ $side }}.arc-outer"
        />
    @else
        <x-ui.tw-graph-v2.elements.arc.ne
            class="tw-graph-v2-merge-extension-arc-outer"
            color="{{ $color }}"
            :dev="$dev"
            dev-path="{{ $devPath }}.{{ $side }}.arc-outer"
        />
    @endif

    @if (filled($connectorVerticalLength))
        <x-ui.tw-graph-v2.elements.path
            class="tw-graph-v2-merge-extension-connector-vertical"
            direction="vertical"
            length="{{ $connectorVerticalLength }}"
            color="{{ $color }}"
            :dev="$dev"
            dev-path="{{ $devPath }}.{{ $side }}.connector-vertical"
        />
    @endif

    @if (filled($connectorVerticalLength) && ($startBadgeLines->isNotEmpty() || filled($startLength)))
        <div class="tw-graph-v2-merge-extension-start">
            <x-ui.tw-graph-v2.elements.node
                color="{{ $color }}"
                :left-text="$startNodeLeftText"
                :left-connector-length="$startNodeLeftConnectorLength"
                :left-color-badge="$startNodeLeftColorBadge"
                :right-text="$startNodeRightText"
                :right-connector-length="$startNodeRightConnectorLength"
                :right-color-badge="$startNodeRightColorBadge"
                :dev="$dev"
                dev-path="{{ $devPath }}.{{ $side }}.start-node"
            />

            <x-ui.tw-graph-v2.elements.path
                variant="start"
                length="{{ $startLength }}"
                color="{{ $color }}"
                :dev="$dev"
                dev-path="{{ $devPath }}.{{ $side }}.start-path"
            />

            @if ($startBadgeLines->isNotEmpty())
                <span class="tw-graph-v2-merge-extension-start-badge">
                    <flux:badge color="{{ $resolvedStartBadgeColor }}">
                        <span class="inline-flex max-w-56 flex-col items-center gap-0.5 text-center leading-tight">
                            @foreach ($startBadgeLines as $startBadgeLine)
                                <span @class(['text-xs' => !$loop->first])>
                                    {{ $startBadgeLine }}
                                </span>
                            @endforeach
                        </span>
                    </flux:badge>
                </span>
            @endif
        </div>
    @endif
</div>
