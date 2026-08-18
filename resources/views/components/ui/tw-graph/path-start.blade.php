{{-- resources/views/components/ui/tw-graph/path-start.blade.php --}}
{{--
    Usage:
    <x-ui.tw-graph.path-start text="Root key" />
    <x-ui.tw-graph.path-start :text="[$rootKey, __('shared Key')]" />

    Optional:
    height="h-16"
    width="w-1.5"
    color="zinc|red|orange|amber|yellow|lime|green|emerald|teal|cyan|sky|blue|indigo|violet|purple|fuchsia|pink|rose" Default: zinc
    node="true|false"
    class="..."

    Slot content is rendered after the default start badge.
--}}

@props([
    'height' => 'h-16',
    'width' => 'w-1.5',
    'text' => null,
    'color' => 'zinc',
    'node' => true,
])

@php
    $showNode = filter_var($node, FILTER_VALIDATE_BOOLEAN);
    $badgeLines = collect(is_iterable($text) && ! is_string($text) ? $text : [$text])
        ->filter(fn ($line) => filled($line))
        ->take(2)
        ->values();
@endphp

@if ($showNode)
    <x-ui.tw-graph.node />
@endif

<div {{ $attributes->class('tw-graph-path-start') }}>
    <x-ui.tw-graph.path-segment
        height="{{ $height }}"
        width="{{ $width }}"
    />

    @if ($badgeLines->isNotEmpty())
        <flux:badge color="{{ $color }}">
            <span class="inline-flex max-w-56 flex-col items-center gap-0.5 text-center leading-tight">
                @foreach ($badgeLines as $badgeLine)
                    <span @class(['text-xs' => ! $loop->first])>
                        {{ $badgeLine }}
                    </span>
                @endforeach
            </span>
        </flux:badge>
    @endif

    @if ($slot->isNotEmpty())
        {{ $slot }}
    @endif
</div>
