{{-- resources/views/components/ui/tw-graph-protocol/primitives/text-name.blade.php --}}
{{--
    Primitive text-name element.

    Usage:
    <x-ui.tw-graph-protocol.primitives.text-name :dev="true" text="merge.left.1" />

    Rule:
    text-name is a DEV-only primitive. It renders a small technical name near an
    anchor and must not be visible in normal graph mode.
--}}

@props([
    'id' => 'text-name',
    'text' => null,
    'anchorX' => '0rem',
    'anchorY' => '0rem',
    'side' => 'right',
    'dev' => false,
])

@if ($dev && filled($text))
    <span
        {{ $attributes->class([
            'tw-graph-protocol-primitive',
            'tw-graph-protocol-primitive-text-name',
            'tw-graph-protocol-primitive-text-name-left' => $side === 'left',
            'tw-graph-protocol-primitive-text-name-right' => $side !== 'left',
        ])->style([
            '--tw-graph-protocol-anchor-x: ' . $anchorX,
            '--tw-graph-protocol-anchor-y: ' . $anchorY,
        ]) }}
        title="{{ $id }} | text-name"
        data-tw-graph-path="{{ $id }}"
        x-on:click.stop="navigator.clipboard?.writeText($el.dataset.twGraphPath)"
    >
        {{ $text }}
    </span>
@endif
