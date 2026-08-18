{{-- resources/views/components/ui/tw-graph-protocol/primitives/arc-ne.blade.php --}}
{{--
    Primitive arc-ne element.

    Usage:
    <x-ui.tw-graph-protocol.primitives.arc-ne ... />
--}}

@props([
    'id' => 'arc-ne',
    'startX' => '0rem',
    'startY' => '0rem',
    'endX' => '0rem',
    'endY' => '0rem',
    'color' => 'cyan',
])

@php
    $colorRgb = \Gunreip\TranslationWorkbench\Support\TranslationWorkbenchColorPalette::rgb($color, '6 182 212');
@endphp

<span
    {{ $attributes->class('tw-graph-protocol-primitive tw-graph-protocol-primitive-arc tw-graph-protocol-primitive-arc-ne')->style([
        '--tw-graph-protocol-start-x: ' . $startX,
        '--tw-graph-protocol-start-y: ' . $startY,
        '--tw-graph-protocol-end-x: ' . $endX,
        '--tw-graph-protocol-end-y: ' . $endY,
        '--tw-graph-protocol-local-color-rgb: ' . $colorRgb,
    ]) }}
    title="{{ $id }} | arc-ne"
    data-tw-graph-path="{{ $id }}"
    x-on:click.stop="navigator.clipboard?.writeText($el.dataset.twGraphPath)"
></span>
