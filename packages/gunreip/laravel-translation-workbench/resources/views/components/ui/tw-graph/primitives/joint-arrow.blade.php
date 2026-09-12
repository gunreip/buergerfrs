{{-- packages/gunreip/laravel-translation-workbench/resources/views/components/ui/tw-graph/primitives/joint-arrow.blade.php --}}
{{--
    Primitive: joint-arrow

    Usage:
    <x-translation-workbench::ui.tw-graph.primitives.joint-arrow
        direction="right"
        anchor-x="0rem"
        anchor-y="0rem"
    />

    Rule:
    A joint arrow marks technical arc/bridge transitions. It is not an
    anchorNode replacement for labels; owning parts decide where it belongs.
--}}

@props([
    'id' => 'joint-arrow',
    'direction' => 'right',
    'anchorX' => '0rem',
    'anchorY' => '0rem',
    'color' => 'zinc',
    'tone' => 'line',
    'zIndex' => null,
])

@php
    $colorRgb = \Gunreip\TranslationWorkbench\Support\TranslationWorkbenchColorPalette::rgb($color, '113 113 122');
    $surfaceColorRgb = \Gunreip\TranslationWorkbench\Support\TranslationWorkbenchColorPalette::surfaceRgb($color, $colorRgb);
    $darkSurfaceColorRgb = \Gunreip\TranslationWorkbench\Support\TranslationWorkbenchColorPalette::darkSurfaceRgb($color, $surfaceColorRgb);
    $devIdentifier = \Gunreip\TranslationWorkbench\Support\TwGraph\DevIdentifier::label($id);
@endphp

<span
    {{ $attributes->class([
        'tw-graph-protocol-primitive',
        'tw-graph-protocol-primitive-joint-arrow',
        'tw-graph-protocol-primitive-joint-arrow-' . $direction,
        'tw-graph-protocol-tone-surface' => $tone === 'surface',
    ])->style([
        '--tw-graph-protocol-anchor-x: ' . $anchorX,
        '--tw-graph-protocol-anchor-y: ' . $anchorY,
        '--tw-graph-protocol-local-color-rgb: ' . $colorRgb,
        '--tw-graph-protocol-local-surface-color-rgb: ' . $surfaceColorRgb,
        '--tw-graph-protocol-local-dark-surface-color-rgb: ' . $darkSurfaceColorRgb,
        '--tw-graph-protocol-z-index: ' . $zIndex => filled($zIndex),
    ]) }}
    title="{{ $devIdentifier }}"
    data-tw-graph-path="{{ $devIdentifier }}"
    x-on:click.stop="navigator.clipboard?.writeText($el.dataset.twGraphPath)"
></span>
