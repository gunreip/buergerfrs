{{-- resources/views/components/ui/tw-graph-v2/elements/path.blade.php --}}
{{--
    Geometry note:
    This element is only the visual path primitive. The line thickness is owned
    by --tw-graph-v2-path-width, and callers should describe direction/length
    instead of compensating with local offset nudges.

    Usage:
    <x-ui.tw-graph-v2.elements.path />
    <x-ui.tw-graph-v2.elements.path length="6rem" />
    <x-ui.tw-graph-v2.elements.path direction="horizontal" length="8rem" />
    <x-ui.tw-graph-v2.elements.path variant="start" length="5rem" />

    Optional:
    direction="vertical|horizontal" Default: vertical.
    variant="solid|start" Default: solid. start renders the bottom-to-top fade-in.
    length="4rem" Segment length in the selected direction.
    color="zinc|red|orange|amber|yellow|lime|green|emerald|teal|cyan|sky|blue|indigo|violet|purple|fuchsia|pink|rose"
--}}

@aware([
    'dev' => false,
])

@props([
    'direction' => 'vertical',
    'variant' => 'solid',
    'length' => '4rem',
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
    {{ $attributes->class([
        'tw-graph-v2-element-path',
        'tw-graph-v2-element-path-horizontal' => $direction === 'horizontal',
        'tw-graph-v2-element-path-vertical' => $direction !== 'horizontal',
        'tw-graph-v2-element-path-start' => $variant === 'start',
    ])->style([
        '--tw-graph-v2-local-color-rgb: ' . $colorRgb => filled($colorRgb),
        '--tw-graph-v2-local-path-length: ' . $length => filled($length),
    ]) }}
></div>
