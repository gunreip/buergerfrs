{{-- resources/views/components/ui/tw-graph/outbound/arc-outer.blade.php --}}
{{--
    Usage:
    <x-ui.tw-graph.outbound.arc-outer side="left" branch-offset="2.5rem" />
    <x-ui.tw-graph.outbound.arc-outer branch-offset="5rem" color="sky" />

    Props:
    side="left|right" Default: right
    branch-offset="2rem|3rem|5rem|..."
    connector-length="2rem|3rem|5rem|..." Deprecated alias for branch-offset.
    color="zinc|red|orange|amber|yellow|lime|green|emerald|teal|cyan|sky|blue|indigo|violet|purple|fuchsia|pink|rose" Default: inherited, fallback zinc

    Keep branch-offset in sync with the paired outbound.connector-horizontal length.
--}}

@props([
    'side' => null,
    'branchOffset' => null,
    'connectorLength' => null,
    'color' => null,
])

@php
    $resolvedBranchOffset = $branchOffset ?? $connectorLength;

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
        'tw-graph-outbound-arc-outer',
        'tw-graph-outbound-arc-outer-left' => $side === 'left',
        'tw-graph-outbound-arc-outer-right' => $side === 'right',
    ])->style([
        '--tw-graph-local-branch-offset: ' . $resolvedBranchOffset => filled($resolvedBranchOffset),
        '--tw-graph-local-path-color-rgb: ' . $colorRgb => filled($colorRgb),
    ]) }}
    aria-hidden="true"
></div>
