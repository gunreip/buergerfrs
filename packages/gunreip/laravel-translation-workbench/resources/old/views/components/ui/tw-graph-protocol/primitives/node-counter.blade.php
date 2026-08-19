{{-- resources/views/components/ui/tw-graph-protocol/primitives/node-counter.blade.php --}}
{{--
    Primitive node-counter element.

    Usage:
    <x-ui.tw-graph-protocol.primitives.node-counter :dev="true" counter="3" />

    Rule:
    node-counter is a DEV-only primitive. It labels an existing node/anchor with
    a small counter and must not be rendered in normal graph mode.
--}}

@props([
    'id' => 'node-counter',
    'counter' => null,
    'anchorX' => '0rem',
    'anchorY' => '0rem',
    'dev' => false,
    'color' => 'zinc',
])

@if ($dev && filled($counter))
    <span
        {{ $attributes->class('tw-graph-protocol-primitive tw-graph-protocol-primitive-node-counter')->style([
            '--tw-graph-protocol-anchor-x: ' . $anchorX,
            '--tw-graph-protocol-anchor-y: ' . $anchorY,
        ]) }}
        title="{{ $id }} | node-counter {{ $counter }}"
        data-tw-graph-path="{{ $id }}"
        x-on:click.stop="navigator.clipboard?.writeText($el.dataset.twGraphPath)"
    >
        <flux:badge
            size="sm"
            color="{{ $color }}"
        >
            {{ $counter }}
        </flux:badge>
    </span>
@endif
