{{-- resources/views/components/ui/tw-graph/node-label.blade.php --}}
{{--
    Usage:
    <x-ui.tw-graph.node-label text="ui.states.all" />
    <x-ui.tw-graph.node-label text="ui.states.all" side="left" length="6rem" badge-color="green" />
    <x-ui.tw-graph.node-label text="ui.states.all" color="amber" badge-color="green" />

    Slot fallback:
    <x-ui.tw-graph.node-label>
        <x-ui.tw-graph.node-line />
        ...
    </x-ui.tw-graph.node-label>

    Props:
    text="..." Optional. When present, renders connector + flux:badge.
    side="left|right" Default: right
    length="4rem|6rem|..." Default: 4rem
    color="zinc|red|orange|amber|yellow|lime|green|emerald|teal|cyan|sky|blue|indigo|violet|purple|fuchsia|pink|rose" Default: inherited connector color
    badge-color="zinc|red|orange|amber|yellow|lime|green|emerald|teal|cyan|sky|blue|indigo|violet|purple|fuchsia|pink|rose" Default: zinc
    class="..."
--}}

@props([
    'text' => null,
    'side' => 'right',
    'length' => '4rem',
    'color' => null,
    'badgeColor' => 'zinc',
])

@php
    $colorRgb = match ($color) {
        'zinc' => '113 113 122',
        'red' => '239 68 68',
        'orange' => '249 115 22',
        'amber' => '245 158 11',
        'yellow' => '234 179 8',
        'lime' => '132 204 22',
        'green' => '44 144 103',
        'emerald' => '16 185 129',
        'teal' => '20 184 166',
        'cyan' => '6 182 212',
        'sky' => '14 165 233',
        'blue' => '59 130 246',
        'indigo' => '99 102 241',
        'violet' => '139 92 246',
        'purple' => '168 85 247',
        'fuchsia' => '217 70 239',
        'pink' => '236 72 153',
        'rose' => '244 63 94',
        default => null,
    };

    $resolvedBadgeColor = in_array($badgeColor, ['zinc', 'red', 'orange', 'amber', 'yellow', 'lime', 'green', 'emerald', 'teal', 'cyan', 'sky', 'blue', 'indigo', 'violet', 'purple', 'fuchsia', 'pink', 'rose'], true)
        ? $badgeColor
        : 'zinc';

    $hasText = $text !== null && $text !== '';
@endphp

<div
    {{ $attributes->class([
        'tw-graph-node-label',
        'tw-graph-node-label-left' => $side === 'left',
        'tw-graph-node-label-right' => $side !== 'left',
    ])->style([
        '--tw-graph-node-label-line-length: ' . $length => filled($length),
        '--tw-graph-local-path-color-rgb: ' . $colorRgb => filled($colorRgb),
    ]) }}
>
    @if ($hasText)
        <x-ui.tw-graph.node-line />
        <flux:badge color="{{ $resolvedBadgeColor }}">
            {{ $text }}
        </flux:badge>
    @else
        {{ $slot }}
    @endif
</div>
