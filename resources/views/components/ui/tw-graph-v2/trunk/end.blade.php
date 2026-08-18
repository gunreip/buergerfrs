{{-- resources/views/components/ui/tw-graph-v2/trunk/end.blade.php --}}
{{--
    Usage:
    <x-ui.tw-graph-v2.trunk.end text="Trunk" />
    <x-ui.tw-graph-v2.trunk.end text="Trunk" length="2.5rem" cap-length="2rem" />

    Optional:
    text="..." Optional top badge.
    length="2.5rem" Vertical end segment length.
    cap-length="2rem" Horizontal cap length.
    color="zinc|red|orange|amber|yellow|lime|green|emerald|teal|cyan|sky|blue|indigo|violet|purple|fuchsia|pink|rose"
    badge-color="green" Flux badge color; defaults to color.

    Composition:
    trunk-end is built from elements.path only. It is a semantic top/end part
    of the trunk, while path thickness and cap geometry stay in the element CSS.
--}}

@aware([
    'dev' => false,
])

@props([
    'text' => null,
    'length' => '2.5rem',
    'capLength' => '2rem',
    'color' => 'zinc',
    'badgeColor' => null,
    'dev' => false,
    'devPath' => 'tw-graph-v2.trunk.end',
])

@php
    $resolvedBadgeColor = $badgeColor ?: $color;
@endphp

<div {{ $attributes->class('tw-graph-v2-trunk-end') }}>
    @if (filled($text))
        <flux:badge
            class="mb-2"
            color="{{ $resolvedBadgeColor }}"
        >
            {{ $text }}
        </flux:badge>
    @endif

    <x-ui.tw-graph-v2.elements.path
        class="tw-graph-v2-trunk-end-cap"
        direction="horizontal"
        length="{{ $capLength }}"
        color="{{ $color }}"
        :dev="$dev"
        dev-path="{{ $devPath }}.cap"
    />

    <x-ui.tw-graph-v2.elements.path
        length="{{ $length }}"
        color="{{ $color }}"
        :dev="$dev"
        dev-path="{{ $devPath }}.path"
    />
</div>
