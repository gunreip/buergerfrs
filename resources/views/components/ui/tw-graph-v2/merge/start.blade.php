{{-- resources/views/components/ui/tw-graph-v2/merge/start.blade.php --}}
{{--
    Usage:
    <x-ui.tw-graph-v2.merge.start />
    <x-ui.tw-graph-v2.merge.start label="Root #701" color="amber" />
    <x-ui.tw-graph-v2.merge.start :label="['Root #701', 'origin']" node-left-text="first seen" />

    Optional:
    label="Root #701" or :label="[...]"
    length="4rem" Fade-in path length below the merge start node.
    badge-color="amber" Optional Flux badge color; defaults to color.
    node-left-text="..." Optional label directly left of the merge start node.
    node-left-connector-length="2rem"
    node-left-color-badge="amber"
    node-right-text="..." Optional label directly right of the merge start node.
    node-right-connector-length="2rem"
    node-right-color-badge="amber"
    color="zinc|red|orange|amber|yellow|lime|green|emerald|teal|cyan|sky|blue|indigo|violet|purple|fuchsia|pink|rose"
--}}

@aware([
    'dev' => false,
])

@props([
    'label' => null,
    'length' => '4rem',
    'badgeColor' => null,
    'nodeLeftText' => null,
    'nodeLeftConnectorLength' => '2rem',
    'nodeLeftColorBadge' => null,
    'nodeRightText' => null,
    'nodeRightConnectorLength' => '2rem',
    'nodeRightColorBadge' => null,
    'color' => 'amber',
    'dev' => false,
    'devPath' => 'tw-graph-v2.merge.start',
])

@php
    $resolvedBadgeColor = $badgeColor ?: $color;
    $badgeLines = collect(is_iterable($label) && ! is_string($label) ? $label : [$label])
        ->filter(fn ($line) => filled($line))
        ->take(2)
        ->values();
@endphp

<div {{ $attributes->class('tw-graph-v2-merge-start') }}>
    <x-ui.tw-graph-v2.elements.node
        color="{{ $color }}"
        :left-text="$nodeLeftText"
        :left-connector-length="$nodeLeftConnectorLength"
        :left-color-badge="$nodeLeftColorBadge"
        :right-text="$nodeRightText"
        :right-connector-length="$nodeRightConnectorLength"
        :right-color-badge="$nodeRightColorBadge"
        :dev="$dev"
        dev-path="{{ $devPath }}.node"
    />

    <x-ui.tw-graph-v2.elements.path
        variant="start"
        length="{{ $length }}"
        color="{{ $color }}"
        :dev="$dev"
        dev-path="{{ $devPath }}.path"
    />

    @if ($badgeLines->isNotEmpty())
        <span class="tw-graph-v2-merge-start-badge">
            <flux:badge color="{{ $resolvedBadgeColor }}">
                <span class="inline-flex max-w-56 flex-col items-center gap-0.5 text-center leading-tight">
                    @foreach ($badgeLines as $badgeLine)
                        <span @class(['text-xs' => ! $loop->first])>
                            {{ $badgeLine }}
                        </span>
                    @endforeach
                </span>
            </flux:badge>
        </span>
    @endif
</div>
