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
    'gradient' => false,
    'cap' => false,
    'capStart' => false,
    'capEnd' => null,
    'capLength' => '1.25rem',
    'dashed' => false,
    'color' => 'cyan',
    'zIndex' => null,
])

@php
    $colorRgb = \Gunreip\TranslationWorkbench\Support\TranslationWorkbenchColorPalette::rgb($color, '6 182 212');
    $devIdentifier = \Gunreip\TranslationWorkbench\Support\TwGraph\DevIdentifier::label($id);
    $resolvedCapEnd = $capEnd ?? $cap;
@endphp

<span
    {{ $attributes->class([
        'tw-graph-protocol-primitive',
        'tw-graph-protocol-primitive-line',
        'tw-graph-protocol-primitive-line-' . $direction,
        'tw-graph-protocol-primitive-line-start' => (bool) $gradient,
        'tw-graph-protocol-primitive-line-cap-start' => (bool) $capStart,
        'tw-graph-protocol-primitive-line-end' => (bool) $resolvedCapEnd,
        'tw-graph-protocol-primitive-line-dashed' => (bool) $dashed,
        'tw-graph-protocol-primitive-line-node-start' => (bool) $nodeStart,
        'tw-graph-protocol-primitive-line-node-end' => (bool) $nodeEnd,
    ])->style([
        '--tw-graph-protocol-start-x: ' . $startX,
        '--tw-graph-protocol-start-y: ' . $startY,
        '--tw-graph-protocol-end-x: ' . $endX,
        '--tw-graph-protocol-end-y: ' . $endY,
        '--tw-graph-protocol-local-length: ' . $length,
        '--tw-graph-protocol-local-color-rgb: ' . $colorRgb,
        '--tw-graph-protocol-line-end-cap-length: ' . $capLength,
        '--tw-graph-protocol-z-index: ' . $zIndex => filled($zIndex),
    ]) }}
    title="{{ $devIdentifier }}"
    data-tw-graph-path="{{ $devIdentifier }}"
    x-on:click.stop="navigator.clipboard?.writeText($el.dataset.twGraphPath)"
></span>
