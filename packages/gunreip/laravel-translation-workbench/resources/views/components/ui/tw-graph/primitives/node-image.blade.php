{{-- packages/gunreip/laravel-translation-workbench/resources/views/components/ui/tw-graph/primitives/node-image.blade.php --}}
{{--
    Primitive: node-image

    Usage:
    <x-translation-workbench::ui.tw-graph.primitives.node-image
        source="/images/example.jpg"
        size="3rem"
        anchor-x="0rem"
        anchor-y="0rem"
    />

    Rule:
    Node images are visual replacements for an anchor dot. Owning parts or
    segments decide which anchor receives the image.
--}}

@props([
    'id' => 'node-image',
    'source' => null,
    'size' => '3rem',
    'anchorX' => '0rem',
    'anchorY' => '0rem',
    'alt' => '',
    'color' => 'zinc',
    'zIndex' => null,
])

@php
    $colorRgb = \Gunreip\TranslationWorkbench\Support\TranslationWorkbenchColorPalette::rgb($color, '113 113 122');
    $devIdentifier = \Gunreip\TranslationWorkbench\Support\TwGraph\DevIdentifier::label($id);
@endphp

@if (filled($source))
    <span
        {{ $attributes->class([
            'tw-graph-protocol-primitive',
            'tw-graph-protocol-primitive-node-image',
        ])->style([
            '--tw-graph-protocol-anchor-x: ' . $anchorX,
            '--tw-graph-protocol-anchor-y: ' . $anchorY,
            '--tw-graph-protocol-node-image-size: ' . $size,
            '--tw-graph-protocol-local-color-rgb: ' . $colorRgb,
            '--tw-graph-protocol-z-index: ' . $zIndex => filled($zIndex),
        ]) }}
        title="{{ $devIdentifier }}"
        data-tw-graph-path="{{ $devIdentifier }}"
        x-on:click.stop="navigator.clipboard?.writeText($el.dataset.twGraphPath)"
    >
        <img
            src="{{ $source }}"
            alt="{{ $alt }}"
        >
    </span>
@endif
