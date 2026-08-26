{{-- packages/gunreip/laravel-translation-workbench/resources/views/components/ui/tw-graph/primitives/wavy-line.blade.php --}}
{{--
    Primitive: wavy-line

    Usage:
    <x-translation-workbench::ui.tw-graph.primitives.wavy-line
        direction="bottom-top"
        length="1.5rem"
        start-x="0rem"
        start-y="0rem"
    />

    Rule:
    Wavy-line is neutral. Segments decide whether it means compressed history,
    omission, continuation, or another graph-specific state.
--}}

@props([
    'id' => 'wavy-line',
    'direction' => 'bottom-top',
    'length' => '1.5rem',
    'startX' => '0rem',
    'startY' => '0rem',
    'color' => 'cyan',
    'zIndex' => null,
])

@php
    $colorRgb = \Gunreip\TranslationWorkbench\Support\TranslationWorkbenchColorPalette::rgb($color, '6 182 212');
    $devIdentifier = \Gunreip\TranslationWorkbench\Support\TwGraph\DevIdentifier::label($id);
@endphp

<span
    {{ $attributes->class([
        'tw-graph-protocol-primitive',
        'tw-graph-protocol-primitive-wavy-line',
        'tw-graph-protocol-primitive-wavy-line-' . $direction,
    ])->style([
        '--tw-graph-protocol-start-x: ' . $startX,
        '--tw-graph-protocol-start-y: ' . $startY,
        '--tw-graph-protocol-local-length: ' . $length,
        '--tw-graph-protocol-local-color-rgb: ' . $colorRgb,
        '--tw-graph-protocol-z-index: ' . $zIndex => filled($zIndex),
    ]) }}
    title="{{ $devIdentifier }}"
    data-tw-graph-path="{{ $devIdentifier }}"
    x-on:click.stop="navigator.clipboard?.writeText($el.dataset.twGraphPath)"
>~~~~~~~~~~~~~~~~</span>
