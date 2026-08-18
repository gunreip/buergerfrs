{{-- resources/views/components/ui/tw-graph-v2/elements/label.blade.php --}}
{{--
    Geometry note:
    This element is only the visual node label primitive. It owns the connector
    line between node and badge; callers describe side, length, and text instead
    of placing the connector line manually.

    Usage:
    <x-ui.tw-graph-v2.elements.label text="Key #5" />
    <x-ui.tw-graph-v2.elements.label side="left" text="Root #701" />

    Optional:
    side="left|right" Default: right.
    length="2rem" Connector length from node edge to label.
    color="zinc|red|orange|amber|yellow|lime|green|emerald|teal|cyan|sky|blue|indigo|violet|purple|fuchsia|pink|rose"
    badge-color="green" Flux badge color; defaults to color.
--}}

@aware([
    'dev' => false,
])

@props([
    'text' => null,
    'side' => 'right',
    'length' => '2rem',
    'color' => null,
    'badgeColor' => null,
    'dev' => false,
    'devPath' => null,
])

@php
    $resolvedBadgeColor = $badgeColor ?: ($color ?: 'zinc');
    $colorRgb = \Gunreip\TranslationWorkbench\Support\TranslationWorkbenchColorPalette::rgb($color);
    $badgeColorRgb = \Gunreip\TranslationWorkbench\Support\TranslationWorkbenchColorPalette::rgb($resolvedBadgeColor, '113 113 122');
    $badgeLines = collect(is_iterable($text) && ! is_string($text) ? $text : [$text])
        ->filter(fn ($line) => filled($line))
        ->take(2)
        ->values();
    $showDevTitle = filter_var($dev, FILTER_VALIDATE_BOOLEAN) && filled($devPath);
    $resolvedDevPath = is_string($devPath) ? preg_replace('/^tw-graph-v2\./', '', $devPath) : $devPath;
@endphp

@if ($badgeLines->isNotEmpty())
    <div
        @if ($showDevTitle)
            title="{{ $resolvedDevPath }}"
            data-tw-graph-path="{{ $resolvedDevPath }}"
            data-tw-graph-path-full="{{ $devPath }}"
        @endif
        {{ $attributes->class([
            'tw-graph-v2-element-label',
            'tw-graph-v2-element-label-left' => $side === 'left',
            'tw-graph-v2-element-label-right' => $side !== 'left',
        ])->style([
            '--tw-graph-v2-local-color-rgb: ' . $colorRgb => filled($colorRgb),
            '--tw-graph-v2-local-badge-color-rgb: ' . $badgeColorRgb,
            '--tw-graph-v2-local-label-length: ' . $length => filled($length),
        ]) }}
    >
        <span class="tw-graph-v2-element-label-line"></span>

        <flux:badge color="{{ $resolvedBadgeColor }}">
            <span class="inline-flex max-w-56 flex-col items-center gap-0.5 text-center leading-tight">
                @foreach ($badgeLines as $badgeLine)
                    <span @class(['text-xs' => ! $loop->first])>
                        {{ $badgeLine }}
                    </span>
                @endforeach
            </span>
        </flux:badge>
    </div>
@endif
