{{-- resources/views/components/ui/tw-graph-protocol/primitives/arc-se.blade.php --}}
{{--
    Primitive arc-se element.

    Usage:
    <x-ui.tw-graph-protocol.primitives.arc-se
        id="merge.left.1.arc-se"
        start-x="0rem"
        start-y="8rem"
        end-x="-2.5rem"
        end-y="5.5rem"
        color="amber"
    />
--}}

@props([
    'id' => 'arc-se',
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
    {{ $attributes->class('tw-graph-protocol-primitive tw-graph-protocol-primitive-arc tw-graph-protocol-primitive-arc-se')->style([
        '--tw-graph-protocol-start-x: ' . $startX,
        '--tw-graph-protocol-start-y: ' . $startY,
        '--tw-graph-protocol-end-x: ' . $endX,
        '--tw-graph-protocol-end-y: ' . $endY,
        '--tw-graph-protocol-local-color-rgb: ' . $colorRgb,
    ]) }}
    title="{{ $id }} | arc-se"
    data-tw-graph-path="{{ $id }}"
    x-on:click.stop="navigator.clipboard?.writeText($el.dataset.twGraphPath)"
></span>
