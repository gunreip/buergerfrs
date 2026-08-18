{{-- resources/views/components/ui/tw-graph-v2/trunk/main.blade.php --}}
{{--
    Usage:
    <x-ui.tw-graph-v2.trunk.main />
    <x-ui.tw-graph-v2.trunk.main length="6rem" />
    <x-ui.tw-graph-v2.trunk.main node />

    Optional:
    length="4rem" Vertical trunk segment length.
    color="zinc|red|orange|amber|yellow|lime|green|emerald|teal|cyan|sky|blue|indigo|violet|purple|fuchsia|pink|rose"
    node="true|false" Render a node at the lower end of this segment.
    node-left-text="..." Render a left node label if filled.
    node-left-connector-length="2rem" Optional left connector length.
    node-left-color-badge="green" Optional left Flux badge color.
    node-right-text="..." Render a right node label if filled.
    node-right-connector-length="2rem" Optional right connector length.
    node-right-color-badge="green" Optional right Flux badge color.

    Composition:
    trunk-main is built from elements.path + optional elements.node. The node
    overlap is handled by v2 CSS geometry vars, not by local pixel nudges.

    Slot:
    If a node is rendered, slot content is attached inside that node.
--}}

@aware([
    'dev' => false,
])

@props([
    'length' => '4rem',
    'color' => null,
    'node' => true,
    'nodeLeftText' => null,
    'nodeLeftConnectorLength' => '2rem',
    'nodeLeftColorBadge' => null,
    'nodeRightText' => null,
    'nodeRightConnectorLength' => '2rem',
    'nodeRightColorBadge' => null,
    'dev' => false,
    'devPath' => 'tw-graph-v2.trunk.main',
])

@php
    $showNode = filter_var($node, FILTER_VALIDATE_BOOLEAN);
@endphp

<div {{ $attributes->class('tw-graph-v2-path-main') }}>
    @if ($showNode)
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
        >
            @if ($slot->isNotEmpty())
                {{ $slot }}
            @endif
        </x-ui.tw-graph-v2.elements.node>
    @endif

    <x-ui.tw-graph-v2.elements.path
        length="{{ $length }}"
        color="{{ $color }}"
        :dev="$dev"
        dev-path="{{ $devPath }}.path"
    />
</div>
