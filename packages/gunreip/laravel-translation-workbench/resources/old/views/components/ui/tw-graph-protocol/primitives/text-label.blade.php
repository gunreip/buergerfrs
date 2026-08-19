{{-- resources/views/components/ui/tw-graph-protocol/primitives/text-label.blade.php --}}
{{--
    Primitive text-label element.

    Usage:
    <x-ui.tw-graph-protocol.primitives.text-label text="Key #5" side="right" />

    Rule:
    text-label renders only the visible label/badge. Connectors belong to the
    connector primitive and are composed at segment level.
--}}

@props([
    'id' => 'text-label',
    'text' => null,
    'side' => 'right',
    'anchorX' => '0rem',
    'anchorY' => '0rem',
    'offset' => '0rem',
    'badgeColor' => null,
])

@php
    $resolvedBadgeColor = $badgeColor ?: 'cyan';
    $badgeLines = collect(is_iterable($text) && ! is_string($text) ? $text : [$text])
        ->filter(fn ($line) => filled($line))
        ->take(2)
        ->values();
@endphp

@if ($badgeLines->isNotEmpty())
    <span
        {{ $attributes->class([
            'tw-graph-protocol-primitive',
            'tw-graph-protocol-primitive-text',
            'tw-graph-protocol-primitive-text-label',
            'tw-graph-protocol-primitive-text-label-left' => $side === 'left',
            'tw-graph-protocol-primitive-text-label-right' => $side !== 'left',
        ])->style([
            '--tw-graph-protocol-anchor-x: ' . $anchorX,
            '--tw-graph-protocol-anchor-y: ' . $anchorY,
            '--tw-graph-protocol-text-label-offset: ' . $offset,
        ]) }}
        title="{{ $id }} | text-label {{ $side }}"
        data-tw-graph-path="{{ $id }}"
        x-on:click.stop="navigator.clipboard?.writeText($el.dataset.twGraphPath)"
    >
        <flux:badge color="{{ $resolvedBadgeColor }}">
            <span class="inline-flex max-w-48 flex-col items-center gap-0.5 text-center leading-tight">
                @foreach ($badgeLines as $badgeLine)
                    <span @class(['text-xs' => ! $loop->first])>
                        {{ $badgeLine }}
                    </span>
                @endforeach
            </span>
        </flux:badge>
    </span>
@endif
