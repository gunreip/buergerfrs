{{-- resources/views/components/ui/tw-graph-protocol/primitives/text-start.blade.php --}}
{{--
    Primitive text-start element.

    Usage:
    <x-ui.tw-graph-protocol.primitives.text-start text="Root #701" />
    <x-ui.tw-graph-protocol.primitives.text-start text="Root #701" connector-placement="bottom" connector-length="0.75rem" />

    Rule:
    text-start labels the open start side of a path. connectorPlacement
    describes where the connector leaves the anchor; the badge follows it.
    Default connectorPlacement is bottom because graph trunks/roots currently
    grow bottom-top.
--}}

@props([
    'id' => 'text-start',
    'text' => null,
    'connectorPlacement' => null,
    'placement' => null,
    'anchorX' => '0rem',
    'anchorY' => '0rem',
    'connectorLength' => '0.75rem',
    'connectorGap' => null,
    'color' => 'green',
    'badgeColor' => null,
])

@php
    $resolvedConnectorPlacement = $connectorPlacement ?: ($placement ?: 'bottom');
    $colorRgb = \Gunreip\TranslationWorkbench\Support\TranslationWorkbenchColorPalette::rgb($color, '44 144 103');
    $resolvedBadgeColor = $badgeColor ?: $color;
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
            'tw-graph-protocol-primitive-text-terminal',
            'tw-graph-protocol-primitive-text-start',
            'tw-graph-protocol-primitive-text-terminal-top' => $resolvedConnectorPlacement === 'top',
            'tw-graph-protocol-primitive-text-terminal-bottom' => $resolvedConnectorPlacement !== 'top',
        ])->style([
            '--tw-graph-protocol-anchor-x: ' . $anchorX,
            '--tw-graph-protocol-anchor-y: ' . $anchorY,
            '--tw-graph-protocol-text-connector-length: ' . $connectorLength,
            '--tw-graph-protocol-text-connector-anchor-gap: ' . $connectorGap => filled($connectorGap),
            '--tw-graph-protocol-local-color-rgb: ' . $colorRgb,
        ]) }}
        title="{{ $id }} | text-start connectorPlacement={{ $resolvedConnectorPlacement }}"
        data-tw-graph-path="{{ $id }}"
        x-on:click.stop="navigator.clipboard?.writeText($el.dataset.twGraphPath)"
    >
        <span class="tw-graph-protocol-primitive-text-connector"></span>
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
