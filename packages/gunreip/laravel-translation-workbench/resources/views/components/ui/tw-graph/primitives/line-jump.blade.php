{{-- Drawing only: a semicircle with round ends, without anchors or crossing lookup. --}}
@props([
    'id' => 'line-jump',
    'template' => false,
    'side' => 'top',
    'radius' => '0.5rem',
    'anchorX' => '0rem',
    'anchorY' => '0rem',
    'lineWidth' => null,
    'color' => 'zinc',
    'tone' => 'line',
    'zIndex' => null,
])
@php
    $path = match ($side) {
        'top' => 'M -1 0 A 1 1 0 0 1 1 0',
        'bottom' => 'M -1 0 A 1 1 0 0 0 1 0',
        'left' => 'M 0 -1 A 1 1 0 0 0 0 1',
        'right' => 'M 0 -1 A 1 1 0 0 1 0 1',
        default => throw new \InvalidArgumentException('Invalid line-jump side: ' . $side),
    };
    $rgb = \Gunreip\TranslationWorkbench\Support\TranslationWorkbenchColorPalette::rgb($color);
    $surface = \Gunreip\TranslationWorkbench\Support\TranslationWorkbenchColorPalette::surfaceRgb($color, $rgb);
    $dark = \Gunreip\TranslationWorkbench\Support\TranslationWorkbenchColorPalette::darkSurfaceRgb($color, $surface);
@endphp
<svg xmlns="http://www.w3.org/2000/svg" viewBox="-1 -1 2 2" overflow="visible"
    @unless ($template)
    data-tw-graph-bounds="{{ json_encode(\Gunreip\TranslationWorkbench\Support\TwGraph\PrimitiveBounds::jump($id, $anchorX, $anchorY, $radius, $lineWidth)) }}"
    data-tw-graph-path="{{ \Gunreip\TranslationWorkbench\Support\TwGraph\DevIdentifier::label($id) }}"
    data-tw-graph-jump-side="{{ $side }}"
    {{ $attributes->class(['tw-graph-protocol-primitive', 'tw-graph-protocol-primitive-line-jump', 'tw-graph-protocol-tone-surface' => $tone === 'surface'])->style([
        'left: calc(var(--tw-graph-protocol-trunk-x) + ' . $anchorX . ' - ' . $radius . ')',
        'bottom: calc(var(--tw-graph-protocol-origin-bottom) + ' . $anchorY . ' - ' . $radius . ')',
        'width: calc(' . $radius . ' * 2)',
        'height: calc(' . $radius . ' * 2)',
        '--tw-graph-protocol-local-color-rgb: ' . $rgb,
        '--tw-graph-protocol-local-surface-color-rgb: ' . $surface,
        '--tw-graph-protocol-local-dark-surface-color-rgb: ' . $dark,
        '--tw-graph-protocol-jump-width: ' . $lineWidth => filled($lineWidth),
        'z-index: ' . $zIndex => filled($zIndex),
    ]) }}
    @endunless
>
    <path d="{{ $path }}" fill="none" stroke="currentColor" stroke-linecap="round"
        vector-effect="non-scaling-stroke"
        style="stroke-width: var(--tw-graph-protocol-jump-width, var(--tw-graph-protocol-path-width));" />
</svg>
