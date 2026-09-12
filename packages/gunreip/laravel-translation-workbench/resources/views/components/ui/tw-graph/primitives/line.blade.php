{{-- packages/gunreip/laravel-translation-workbench/resources/views/components/ui/tw-graph/primitives/line.blade.php --}}
{{--
    Primitive: line

    Usage:
    <x-translation-workbench::ui.tw-graph.primitives.line
        direction="top-bottom"
        length="4rem"
    />

    Rule:
    Line is neutral. Segments decide whether it becomes path, path-start,
    path-end, merge-path, branch-path, etc.

--}}

@props([
    'id' => 'line',
    'direction' => 'bottom-top',
    'length' => '4rem',
    'startX' => '0rem',
    'startY' => '0rem',
    'endX' => '0rem',
    'endY' => '0rem',
    'nodeStart' => false,
    'nodeEnd' => false,
    'nodeStartSize' => null,
    'nodeEndSize' => null,
    'gradient' => false,
    'cap' => false,
    'capStart' => false,
    'capEnd' => null,
    'capLength' => '1.25rem',
    'dashed' => false,
    'color' => 'zinc',
    'toColor' => null,
    'colorGradient' => false,
    'tone' => 'line',
    'zIndex' => null,
])

@php
    $colorRgb = \Gunreip\TranslationWorkbench\Support\TranslationWorkbenchColorPalette::rgb($color, '113 113 122');
    $surfaceColorRgb = \Gunreip\TranslationWorkbench\Support\TranslationWorkbenchColorPalette::surfaceRgb($color, $colorRgb);
    $darkSurfaceColorRgb = \Gunreip\TranslationWorkbench\Support\TranslationWorkbenchColorPalette::darkSurfaceRgb($color, $surfaceColorRgb);
    $toColorRgb = \Gunreip\TranslationWorkbench\Support\TranslationWorkbenchColorPalette::rgb($toColor, $colorRgb);
    $toSurfaceColorRgb = \Gunreip\TranslationWorkbench\Support\TranslationWorkbenchColorPalette::surfaceRgb($toColor, $surfaceColorRgb);
    $toDarkSurfaceColorRgb = \Gunreip\TranslationWorkbench\Support\TranslationWorkbenchColorPalette::darkSurfaceRgb($toColor, $toSurfaceColorRgb);
    $devIdentifier = \Gunreip\TranslationWorkbench\Support\TwGraph\DevIdentifier::label($id);
    $resolvedCapEnd = $capEnd ?? $cap;
@endphp

<span
    {{ $attributes->class([
        'tw-graph-protocol-primitive',
        'tw-graph-protocol-primitive-line',
        'tw-graph-protocol-primitive-line-' . $direction,
        'tw-graph-protocol-primitive-line-start' => (bool) $gradient,
        'tw-graph-protocol-primitive-line-color-gradient' => (bool) $colorGradient,
        'tw-graph-protocol-primitive-line-cap-start' => (bool) $capStart,
        'tw-graph-protocol-primitive-line-end' => (bool) $resolvedCapEnd,
        'tw-graph-protocol-primitive-line-dashed' => (bool) $dashed,
        'tw-graph-protocol-primitive-line-node-start' => (bool) $nodeStart,
        'tw-graph-protocol-primitive-line-node-end' => (bool) $nodeEnd,
        'tw-graph-protocol-tone-surface' => $tone === 'surface',
    ])->style([
        '--tw-graph-protocol-start-x: ' . $startX,
        '--tw-graph-protocol-start-y: ' . $startY,
        '--tw-graph-protocol-end-x: ' . $endX,
        '--tw-graph-protocol-end-y: ' . $endY,
        '--tw-graph-protocol-local-length: ' . $length,
        '--tw-graph-protocol-local-color-rgb: ' . $colorRgb,
        '--tw-graph-protocol-local-surface-color-rgb: ' . $surfaceColorRgb,
        '--tw-graph-protocol-local-dark-surface-color-rgb: ' . $darkSurfaceColorRgb,
        '--tw-graph-protocol-local-to-color-rgb: ' . $toColorRgb,
        '--tw-graph-protocol-local-to-surface-color-rgb: ' . $toSurfaceColorRgb,
        '--tw-graph-protocol-local-to-dark-surface-color-rgb: ' . $toDarkSurfaceColorRgb,
        '--tw-graph-protocol-line-node-start-size: ' . $nodeStartSize => filled($nodeStartSize),
        '--tw-graph-protocol-line-node-end-size: ' . $nodeEndSize => filled($nodeEndSize),
        '--tw-graph-protocol-line-end-cap-length: ' . $capLength,
        '--tw-graph-protocol-z-index: ' . $zIndex => filled($zIndex),
    ]) }}
    title="{{ $devIdentifier }}"
    data-tw-graph-path="{{ $devIdentifier }}"
    x-on:click.stop="navigator.clipboard?.writeText($el.dataset.twGraphPath)"
></span>
