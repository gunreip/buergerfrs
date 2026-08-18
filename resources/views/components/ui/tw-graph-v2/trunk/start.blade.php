{{-- resources/views/components/ui/tw-graph-v2/trunk/start.blade.php --}}
{{--
    Usage:
    <x-ui.tw-graph-v2.trunk.start text="Root #701" />
    <x-ui.tw-graph-v2.trunk.start :text="[$originKey, __('shared Key')]" color="green" />

    Optional:
    length="4rem" Path length below the node.
    color="zinc|red|orange|amber|yellow|lime|green|emerald|teal|cyan|sky|blue|indigo|violet|purple|fuchsia|pink|rose"
    badge-color="green" Flux badge color; defaults to color.
    node="true|false"

    Composition:
    trunk-start is built from elements.node + elements.path. The path/node
    overlap is handled by v2 CSS geometry vars, not by local pixel nudges.
--}}

@aware([
    'dev' => false,
])

@props([
    'text' => null,
    'length' => '4rem',
    'color' => 'zinc',
    'badgeColor' => null,
    'node' => true,
    'dev' => false,
    'devPath' => 'tw-graph-v2.trunk.start',
])

@php
    $colorRgb = \Gunreip\TranslationWorkbench\Support\TranslationWorkbenchColorPalette::rgb($color, '113 113 122');
    $showNode = filter_var($node, FILTER_VALIDATE_BOOLEAN);
    $resolvedBadgeColor = $badgeColor ?: $color;
    $badgeLines = collect(is_iterable($text) && !is_string($text) ? $text : [$text])
        ->filter(fn($line) => filled($line))
        ->take(2)
        ->values();
@endphp

<div
    {{ $attributes->class('tw-graph-v2-path-start')->style(['--tw-graph-v2-local-color-rgb: ' . $colorRgb, '--tw-graph-v2-local-path-length: ' . $length]) }}>
    @if ($showNode)
        <x-ui.tw-graph-v2.elements.node
            color="{{ $color }}"
            :dev="$dev"
            dev-path="{{ $devPath }}.node"
        />
    @endif

    <x-ui.tw-graph-v2.elements.path
        variant="start"
        length="{{ $length }}"
        color="{{ $color }}"
        :dev="$dev"
        dev-path="{{ $devPath }}.path"
    />

    @if ($badgeLines->isNotEmpty())
        <flux:badge color="{{ $resolvedBadgeColor }}">
            <span class="inline-flex max-w-56 flex-col items-center gap-0.5 text-center leading-tight">
                @foreach ($badgeLines as $badgeLine)
                    <span @class(['text-xs' => !$loop->first])>
                        {{ $badgeLine }}
                    </span>
                @endforeach
            </span>
        </flux:badge>
    @endif
</div>
