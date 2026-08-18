{{-- resources/views/components/ui/tw-graph-v2/branch/path.blade.php --}}
{{--
    Usage:
    <x-ui.tw-graph-v2.branch.path
        side="right"
        anchor-index="4"
        branch-horizontal-length="5rem"
        branch-vertical-length="2.5rem"
        length="3rem"
        node-right-text="extended"
    />

    Optional:
    side="left|right" Default: right. Selects the parent branch side.
    anchor-index="1..8" 1-based visible trunk node counter from bottom to top; no index 0, no trunk border anchor. attach-y overrides it.
    attach-y="10rem" Manual distance from graph canvas bottom to the trunk node center.
    branch-horizontal-length="4rem" Parent branch horizontal connector length.
    branch-vertical-length="4rem" Parent branch vertical connector length.
    length="4rem" Extension vertical segment length.
    node="true|false" Render a node at the extension top. Default: true.
    node-left-text="..." Optional label directly left of the extension node.
    node-left-connector-length="2rem"
    node-left-color-badge="rose"
    node-right-text="..." Optional label directly right of the extension node.
    node-right-connector-length="2rem"
    node-right-color-badge="rose"
    end-label="Branch #903" Optional branch end badge above the extension node.
    end-length="4rem" Optional path length above the extension node.
    end-cap-length="2rem" Optional horizontal cap length above the extension node.
    end-badge-color="rose" Optional Flux badge color; defaults to color.
    color="zinc|red|orange|amber|yellow|lime|green|emerald|teal|cyan|sky|blue|indigo|violet|purple|fuchsia|pink|rose"

    Composition:
    branch.path adds a straight vertical segment to an existing branch path.
    It does not render arcs; it continues the already established branch line.
--}}

@aware([
    'dev' => false,
])

@props([
    'side' => 'right',
    'attachY' => '10rem',
    'anchorIndex' => null,
    'branchHorizontalLength' => '4rem',
    'branchVerticalLength' => '4rem',
    'length' => '4rem',
    'node' => true,
    'nodeLeftText' => null,
    'nodeLeftConnectorLength' => '2rem',
    'nodeLeftColorBadge' => null,
    'nodeRightText' => null,
    'nodeRightConnectorLength' => '2rem',
    'nodeRightColorBadge' => null,
    'endLabel' => null,
    'endLength' => null,
    'endCapLength' => '2rem',
    'endBadgeColor' => null,
    'color' => 'rose',
    'dev' => false,
    'devPath' => 'tw-graph-v2.branch.path',
])

@php
    $showNode = filter_var($node, FILTER_VALIDATE_BOOLEAN);
    $resolvedEndBadgeColor = $endBadgeColor ?: $color;
    $endBadgeLines = collect(is_iterable($endLabel) && !is_string($endLabel) ? $endLabel : [$endLabel])
        ->filter(fn($line) => filled($line))
        ->take(2)
        ->values();
@endphp

<div
    {{ $attributes->class([
        'tw-graph-v2-branch-main',
        'tw-graph-v2-branch-main-left' => $side === 'left',
        'tw-graph-v2-branch-main-right' => $side !== 'left',
        'tw-graph-v2-trunk-anchor-' . $anchorIndex => filled($anchorIndex),
    ])->style([
        '--tw-graph-v2-local-attach-y: ' . $attachY => blank($anchorIndex) && filled($attachY),
        '--tw-graph-v2-local-branch-horizontal-length: ' . $branchHorizontalLength => filled($branchHorizontalLength),
        '--tw-graph-v2-local-branch-vertical-length: ' . $branchVerticalLength => filled($branchVerticalLength),
        '--tw-graph-v2-local-extension-length: ' . $length => filled($length),
        '--tw-graph-v2-local-end-length: ' . $endLength => filled($endLength),
    ]) }}
>
    <x-ui.tw-graph-v2.elements.path
        class="tw-graph-v2-branch-main-path"
        direction="vertical"
        length="{{ $length }}"
        color="{{ $color }}"
        :dev="$dev"
        dev-path="{{ $devPath }}.{{ $side }}.path"
    />

    @if ($showNode)
        <div class="tw-graph-v2-branch-main-node">
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

    @if ($endBadgeLines->isNotEmpty() || filled($endLength))
        <div class="tw-graph-v2-branch-main-end">
            @if ($endBadgeLines->isNotEmpty())
                <x-ui.tw-graph-v2.elements.path
                    class="tw-graph-v2-branch-main-end-cap tw-graph-v2-trunk-end-cap"
                    direction="horizontal"
                    length="{{ $endCapLength }}"
                    color="{{ $color }}"
                    :dev="$dev"
                    dev-path="{{ $devPath }}.{{ $side }}.end-cap"
                />
            @endif

            @if (filled($endLength))
                <x-ui.tw-graph-v2.elements.path
                    length="{{ $endLength }}"
                    color="{{ $color }}"
                    :dev="$dev"
                    dev-path="{{ $devPath }}.{{ $side }}.end-path"
                />
            @endif

            @if ($endBadgeLines->isNotEmpty())
                <span class="tw-graph-v2-branch-main-end-badge">
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
