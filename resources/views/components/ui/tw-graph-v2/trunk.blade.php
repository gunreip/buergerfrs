{{-- resources/views/components/ui/tw-graph-v2/trunk.blade.php --}}
{{--
    Usage:
    <x-ui.tw-graph-v2.trunk
        :start-text="[$rootKey, __('shared Key')]"
        color="green"
    />

    Optional:
    color="zinc|red|orange|amber|yellow|lime|green|emerald|teal|cyan|sky|blue|indigo|violet|purple|fuchsia|pink|rose"
    start-text="Root #701" or :start-text="[...]"
    start-length="5rem"
    :path-lengths="['4rem', '5rem', ...]" Optional individual trunk main segment lengths. If null/empty, default segment lengths are used.
    end-text="Trunk"
    end-length="2.5rem"
    end-cap-length="2rem"
    end-color-badge="green"
    node-left-text="..." Optional first trunk-path node left label.
    node-left-connector-length="2rem"
    node-left-color-badge="green"
    node-right-text="..." Optional first trunk-path node right label.
    node-right-connector-length="2rem"
    node-right-color-badge="green"
    context-node-left-text="..." Optional second trunk-path node left label.
    context-node-left-connector-length="2rem"
    context-node-left-color-badge="red"
    context-node-right-text="..." Optional second trunk-path node right label.
    context-node-right-connector-length="2rem"
    context-node-right-color-badge="sky"

    Composition:
    This is a semantic trunk partial. It is written from bottom to top and uses
    column-reverse internally, mirroring the root render rule.
--}}

@aware([
    'dev' => false,
    'graphId' => null,
])

@props([
    'color' => 'zinc',
    'startText' => null,
    'startLength' => '5rem',
    'pathLengths' => null,
    'endText' => null,
    'endLength' => '2.5rem',
    'endCapLength' => '2rem',
    'endColorBadge' => null,
    'nodeLeftText' => null,
    'nodeLeftConnectorLength' => '2rem',
    'nodeLeftColorBadge' => null,
    'nodeRightText' => __('ui.states.all'),
    'nodeRightConnectorLength' => '2rem',
    'nodeRightColorBadge' => null,
    'contextNodeLeftText' => __('Label Left'),
    'contextNodeLeftConnectorLength' => '5rem',
    'contextNodeLeftColorBadge' => 'red',
    'contextNodeRightText' => __('Right label'),
    'contextNodeRightConnectorLength' => '1rem',
    'contextNodeRightColorBadge' => 'sky',
    'dev' => false,
    'devPath' => 'tw-graph-v2.trunk',
])

@php
    $defaultPathLengths = ['4rem', '4rem', '4rem', '4rem', '4rem', '4rem', '4rem'];
    $resolvedPathLengths = collect(is_iterable($pathLengths) && ! is_string($pathLengths) ? $pathLengths : [$pathLengths])
        ->filter(fn ($length) => filled($length))
        ->values();

    if ($resolvedPathLengths->isEmpty()) {
        $resolvedPathLengths = collect($defaultPathLengths);
    }

    $trunkRootStyles = [
        '--tw-graph-v2-trunk-start-length: ' . $startLength,
    ];

    foreach ($resolvedPathLengths->take(8) as $index => $pathLength) {
        $trunkRootStyles[] = '--tw-graph-v2-trunk-path-' . ($index + 1) . '-length: ' . $pathLength;
    }
@endphp

@if (filled($graphId))
    <style>
        #{{ $graphId }} {
            @foreach ($trunkRootStyles as $trunkRootStyle)
                {{ $trunkRootStyle }};
            @endforeach
        }
    </style>
@endif

<div {{ $attributes->class('tw-graph-v2-trunk') }}>
    {{-- Trunk Start (bottom) --}}
    <x-ui.tw-graph-v2.trunk.start
        :text="$startText"
        color="{{ $color }}"
        length="{{ $startLength }}"
        :node="true"
        :dev="$dev"
        dev-path="{{ $devPath }}.start"
    />
    @foreach ($resolvedPathLengths as $pathSegmentLength)
        {{-- Trunk Path Segment --}}
        <x-ui.tw-graph-v2.trunk.main
            color="{{ $color }}"
            length="{{ $pathSegmentLength }}"
            :node="true"
            :node-left-text="$loop->iteration === 1 ? $nodeLeftText : ($loop->iteration === 2 ? $contextNodeLeftText : null)"
            :node-left-connector-length="$loop->iteration === 1 ? $nodeLeftConnectorLength : ($loop->iteration === 2 ? $contextNodeLeftConnectorLength : '2rem')"
            :node-left-color-badge="$loop->iteration === 1 ? $nodeLeftColorBadge : ($loop->iteration === 2 ? $contextNodeLeftColorBadge : null)"
            :node-right-text="$loop->iteration === 1 ? $nodeRightText : ($loop->iteration === 2 ? $contextNodeRightText : null)"
            :node-right-connector-length="$loop->iteration === 1 ? $nodeRightConnectorLength : ($loop->iteration === 2 ? $contextNodeRightConnectorLength : '2rem')"
            :node-right-color-badge="$loop->iteration === 1 ? $nodeRightColorBadge : ($loop->iteration === 2 ? $contextNodeRightColorBadge : null)"
            :dev="$dev"
            dev-path="{{ $devPath }}.main-{{ $loop->iteration }}"
        />
    @endforeach

    {{-- Trunk End (top) --}}
    <x-ui.tw-graph-v2.trunk.end
        :text="$endText"
        color="{{ $color }}"
        length="{{ $endLength }}"
        cap-length="{{ $endCapLength }}"
        :badge-color="$endColorBadge"
        :dev="$dev"
        dev-path="{{ $devPath }}.end"
    />
</div>
