{{-- resources/views/components/ui/tw-graph-v2/elements/arc/se.blade.php --}}
{{--
    Geometry note:
    This element is only the visual south-east arc primitive. The arc box uses
    border-box geometry, so the border sits inside the arc box and callers can
    attach it to a logical node/connection point without local half-border hacks.

    Usage:
    <x-ui.tw-graph-v2.elements.arc.se />
    <x-ui.tw-graph-v2.elements.arc.se color="amber" />

    Optional:
    color="zinc|red|orange|amber|yellow|lime|green|emerald|teal|cyan|sky|blue|indigo|violet|purple|fuchsia|pink|rose"
--}}

@aware([
    'dev' => false,
])

@props([
    'color' => null,
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
    {{ $attributes->class('tw-graph-v2-element-arc tw-graph-v2-element-arc-se')->style([
        '--tw-graph-v2-local-color-rgb: ' . $colorRgb => filled($colorRgb),
    ]) }}
></div>
