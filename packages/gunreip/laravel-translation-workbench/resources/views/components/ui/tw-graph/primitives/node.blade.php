{{-- packages/gunreip/laravel-translation-workbench/resources/views/components/ui/tw-graph/primitives/node.blade.php --}}
{{--
    Primitive: node

    Usage:
    <x-translation-workbench::ui.tw-graph.primitives.node
        anchor-x="0rem"
        anchor-y="0rem"
    />

    Rule:
    Node renders only a point marker. Segments decide whether a node belongs
    to a start anchor, end anchor, or DEV-only inspection point.
--}}

@props([
    'id' => 'node',
    'anchorX' => '0rem',
    'anchorY' => '0rem',
    'size' => null,
    'color' => 'cyan',
])

@php
    $colorRgb = \Gunreip\TranslationWorkbench\Support\TranslationWorkbenchColorPalette::rgb($color, '6 182 212');
    $devIdentifier = \Gunreip\TranslationWorkbench\Support\TwGraph\DevIdentifier::label($id);
@endphp

<span
    {{ $attributes->class('tw-graph-protocol-primitive tw-graph-protocol-primitive-node')->style([
        '--tw-graph-protocol-anchor-x: ' . $anchorX,
        '--tw-graph-protocol-anchor-y: ' . $anchorY,
        '--tw-graph-protocol-local-node-size: ' . $size => filled($size),
        '--tw-graph-protocol-local-color-rgb: ' . $colorRgb,
    ]) }}
    title="{{ $devIdentifier }}"
    data-tw-graph-path="{{ $devIdentifier }}"
    x-on:click.stop="navigator.clipboard?.writeText($el.dataset.twGraphPath)"
></span>
