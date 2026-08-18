{{-- resources/views/components/ui/tw-graph/inbound/connector.blade.php --}}
{{--
    Deprecated alias:
    Prefer <x-ui.tw-graph.inbound.connector-horizontal ... /> for left-to-right connectors.

    Usage:
    <x-ui.tw-graph.inbound.connector side="left" length="4.5rem" />
    <x-ui.tw-graph.inbound.connector length="3rem" color="cyan" />

    Props:
    side="left|right" Default: right
    length="2rem|3rem|4.5rem|..."
    color="zinc|red|orange|amber|yellow|lime|green|emerald|teal|cyan|sky|blue|indigo|violet|purple|fuchsia|pink|rose" Default: inherited, fallback zinc

    Keep length in sync with the paired inbound.arc-outer branch-offset.
--}}

@props([
    'side' => null,
    'length' => null,
    'color' => null,
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
@endphp

<div
    {{ $attributes->class([
        'tw-graph-inbound-connector',
        'tw-graph-inbound-connector-left' => $side === 'left',
        'tw-graph-inbound-connector-right' => $side === 'right',
    ])->style([
        '--tw-graph-local-connector-width: ' . $length => filled($length),
        '--tw-graph-local-path-color-rgb: ' . $colorRgb => filled($colorRgb),
    ]) }}
    aria-hidden="true"
></div>
