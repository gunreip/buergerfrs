{{-- resources/views/components/ui/tw-graph-protocol/primitives/path.blade.php --}}
{{--
    Primitive path element.

    Usage:
    <x-ui.tw-graph-protocol.primitives.path
        id="trunk.start"
        direction="bottom-top"
        length="8rem"
        start-x="0rem"
        start-y="0rem"
        end-x="0rem"
        end-y="8rem"
        :node-start="false"
        :node-end="true"
        color="green"
    />

    Rule:
    PathNodes are rendered by this element via ::before/::after when nodeStart
    or nodeEnd are true. There is no separate node component for path points.
--}}

@props([
    'id' => 'path',
    'direction' => 'bottom-top',
    'length' => '4rem',
    'startX' => '0rem',
    'startY' => '0rem',
    'endX' => '0rem',
    'endY' => '0rem',
    'nodeStart' => true,
    'nodeEnd' => true,
    'color' => 'cyan',
])

@php
    $colorRgb = \Gunreip\TranslationWorkbench\Support\TranslationWorkbenchColorPalette::rgb($color, '6 182 212');
@endphp

<span
    {{ $attributes->class([
        'tw-graph-protocol-primitive',
        'tw-graph-protocol-primitive-path',
        'tw-graph-protocol-primitive-path-' . $direction,
        'tw-graph-protocol-primitive-path-node-start' => (bool) $nodeStart,
        'tw-graph-protocol-primitive-path-node-end' => (bool) $nodeEnd,
    ])->style([
        '--tw-graph-protocol-start-x: ' . $startX,
        '--tw-graph-protocol-start-y: ' . $startY,
        '--tw-graph-protocol-end-x: ' . $endX,
        '--tw-graph-protocol-end-y: ' . $endY,
        '--tw-graph-protocol-local-length: ' . $length,
        '--tw-graph-protocol-local-color-rgb: ' . $colorRgb,
    ]) }}
    title="{{ $id }} | {{ $direction }}"
    data-tw-graph-path="{{ $id }}"
    x-on:click.stop="navigator.clipboard?.writeText($el.dataset.twGraphPath)"
></span>
