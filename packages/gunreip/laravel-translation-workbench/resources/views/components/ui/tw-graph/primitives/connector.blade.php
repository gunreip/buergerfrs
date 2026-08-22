{{-- packages/gunreip/laravel-translation-workbench/resources/views/components/ui/tw-graph/primitives/connector.blade.php --}}
{{--
    Primitive: connector

    Usage:
    <x-translation-workbench::ui.tw-graph.primitives.connector
        placement="right"
        anchor-x="0rem"
        anchor-y="0rem"
    />

    Rule:
    Connector renders only a thin helper line from an existing anchor. It does
    not render text, nodes, counters, or path geometry.
--}}

@props([
    'id' => 'connector',
    'placement' => 'right',
    'anchorX' => '0rem',
    'anchorY' => '0rem',
    'length' => '2rem',
    'gap' => null,
    'color' => 'cyan',
])

@php
    $colorRgb = \Gunreip\TranslationWorkbench\Support\TranslationWorkbenchColorPalette::rgb($color, '6 182 212');
    $devIdentifier = \Gunreip\TranslationWorkbench\Support\TwGraph\DevIdentifier::label($id);
@endphp

<span
    {{ $attributes->class([
        'tw-graph-protocol-primitive',
        'tw-graph-protocol-primitive-connector',
        'tw-graph-protocol-primitive-connector-' . $placement,
    ])->style([
        '--tw-graph-protocol-anchor-x: ' . $anchorX,
        '--tw-graph-protocol-anchor-y: ' . $anchorY,
        '--tw-graph-protocol-connector-length: ' . $length,
        '--tw-graph-protocol-connector-anchor-gap: ' . $gap => filled($gap),
        '--tw-graph-protocol-local-color-rgb: ' . $colorRgb,
    ]) }}
    title="{{ $devIdentifier }}"
    data-tw-graph-path="{{ $devIdentifier }}"
    x-on:click.stop="navigator.clipboard?.writeText($el.dataset.twGraphPath)"
></span>
