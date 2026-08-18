{{-- resources/views/components/ui/tw-graph/inbound.blade.php --}}
{{--
    Usage:
    <x-ui.tw-graph.inbound side="left" color="amber">
        <x-ui.tw-graph.inbound.arc-inner />
        <x-ui.tw-graph.inbound.connector-horizontal length="8.5rem" />
        <x-ui.tw-graph.inbound.arc-outer branch-offset="8.5rem" />
        <x-ui.tw-graph.inbound.connector-vertical length="3.3rem" branch-offset="8.5rem" node />
    </x-ui.tw-graph.inbound>

    Props:
    side="left|right" Default: right
    color="zinc|red|orange|amber|yellow|lime|green|emerald|teal|cyan|sky|blue|indigo|violet|purple|fuchsia|pink|rose" Default: zinc

    Children inherit side/color unless they explicitly override them.
--}}

@props([
    'side' => 'right',
    'color' => 'zinc',
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
        default => '113 113 122',
    };
@endphp

<div
    {{ $attributes->class([
        'tw-graph-branch',
        'tw-graph-inbound',
        'tw-graph-inbound-left' => $side === 'left',
        'tw-graph-inbound-right' => $side !== 'left',
    ])->style([
        '--tw-graph-local-path-color-rgb: ' . $colorRgb,
    ]) }}
>
    {{ $slot }}
</div>
