{{-- resources/views/components/ui/tw-graph-v2/elements/node.blade.php --}}
{{--
    Geometry note:
    This element is only the visual node primitive. It owns the node geometry
    internally: the tiny core plus border creates the final dot size, so callers
    should position the node by its logical center and avoid one-off offset fixes.

    Usage:
    <x-ui.tw-graph-v2.elements.node />
    <x-ui.tw-graph-v2.elements.node color="green" />
    <x-ui.tw-graph-v2.elements.node left-text="Root #701" right-text="Key #5" />
    <x-ui.tw-graph-v2.elements.node>
        <x-ui.tw-graph-v2.elements.arc.se />
    </x-ui.tw-graph-v2.elements.node>

    Optional:
    color="zinc|red|orange|amber|yellow|lime|green|emerald|teal|cyan|sky|blue|indigo|violet|purple|fuchsia|pink|rose"
    left-text="..." Render a left label if filled.
    left-connector-length="2rem" Optional left connector length.
    left-color-badge="green" Optional left Flux badge color; defaults to color.
    right-text="..." Render a right label if filled.
    right-connector-length="2rem" Optional right connector length.
    right-color-badge="green" Optional right Flux badge color; defaults to color.

    Slot:
    Render node-attached graph elements, such as inbound/outbound arcs.
--}}

@aware([
    'dev' => false,
])

@props([
    'color' => null,
    'leftText' => null,
    'leftConnectorLength' => '2rem',
    'leftColorBadge' => null,
    'rightText' => null,
    'rightConnectorLength' => '2rem',
    'rightColorBadge' => null,
    'dev' => false,
    'devPath' => null,
])

@php
    $colorRgb = \Gunreip\TranslationWorkbench\Support\TranslationWorkbenchColorPalette::rgb($color);
    $showDevTitle = filter_var($dev, FILTER_VALIDATE_BOOLEAN) && filled($devPath);
    $resolvedDevPath = is_string($devPath) ? preg_replace('/^tw-graph-v2\./', '', $devPath) : $devPath;
@endphp

<div
    @if ($showDevTitle)
        title="{{ $resolvedDevPath }}"
        data-tw-graph-path="{{ $resolvedDevPath }}"
        data-tw-graph-path-full="{{ $devPath }}"
        x-on:click.stop="navigator.clipboard?.writeText($el.dataset.twGraphPath)"
    @endif
    {{ $attributes->class('tw-graph-v2-element-node')->style([
        '--tw-graph-v2-local-color-rgb: ' . $colorRgb => filled($colorRgb),
    ]) }}
>
    @if (filled($leftText))
        <x-ui.tw-graph-v2.elements.label
            side="left"
            :text="$leftText"
            :length="$leftConnectorLength"
            :color="$color"
            :badge-color="$leftColorBadge"
            :dev="$dev"
            :dev-path="filled($devPath) ? $devPath . '.label-left' : null"
        />
    @endif

    @if (filled($rightText))
        <x-ui.tw-graph-v2.elements.label
            side="right"
            :text="$rightText"
            :length="$rightConnectorLength"
            :color="$color"
            :badge-color="$rightColorBadge"
            :dev="$dev"
            :dev-path="filled($devPath) ? $devPath . '.label-right' : null"
        />
    @endif

    @if ($slot->isNotEmpty())
        {{ $slot }}
    @endif
</div>
