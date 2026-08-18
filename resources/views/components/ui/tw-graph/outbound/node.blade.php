{{-- resources/views/components/ui/tw-graph/outbound/node.blade.php --}}
{{--
    Usage:
    <x-ui.tw-graph.outbound.node side="left" branch-offset="2.5rem" />
    <x-ui.tw-graph.outbound.node branch-offset="5rem" color="sky" />
    <x-ui.tw-graph.outbound.node text="Moved key" branch-offset="5rem" badge-color="sky" />

    Props:
    side="left|right" Default: right
    branch-offset="2rem|3rem|5rem|..."
    connector-length="2rem|3rem|5rem|..." Deprecated alias for branch-offset.
    color="zinc|red|orange|amber|yellow|lime|green|emerald|teal|cyan|sky|blue|indigo|violet|purple|fuchsia|pink|rose" Default: inherited, fallback zinc
    text="..." Optional. When present, renders an attached node label.
    label-side="left|right" Default: away from trunk.
    label-length="4rem|6rem|..." Default: 4rem
    label-color="zinc|red|orange|amber|yellow|lime|green|emerald|teal|cyan|sky|blue|indigo|violet|purple|fuchsia|pink|rose" Default: inherited connector color
    badge-color="zinc|red|orange|amber|yellow|lime|green|emerald|teal|cyan|sky|blue|indigo|violet|purple|fuchsia|pink|rose" Default: zinc

    Purpose:
    Dot marker for an outbound branch path. The marker is placed at the outer
    branch join where the horizontal connector reaches the outer arc/vertical path.
--}}

@props([
    'side' => null,
    'branchOffset' => null,
    'connectorLength' => null,
    'color' => null,
    'text' => null,
    'labelSide' => null,
    'labelLength' => '4rem',
    'labelColor' => null,
    'badgeColor' => 'zinc',
])

@php
    $resolvedBranchOffset = $branchOffset ?? $connectorLength;
    $resolvedSide = $side === 'left' ? 'left' : ($side === 'right' ? 'right' : null);
    $resolvedLabelSide = $labelSide ?? ($resolvedSide === 'left' ? 'left' : 'right');
    $hasText = $text !== null && $text !== '';

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
        'tw-graph-branch-node',
        'tw-graph-outbound-node',
        'tw-graph-outbound-node-left' => $side === 'left',
        'tw-graph-outbound-node-right' => $side === 'right',
    ])->style([
        '--tw-graph-local-branch-offset: ' . $resolvedBranchOffset => filled($resolvedBranchOffset),
        '--tw-graph-local-path-color-rgb: ' . $colorRgb => filled($colorRgb),
    ]) }}
    @if (! $hasText)
        aria-hidden="true"
    @endif
>
    @if ($hasText)
        <x-ui.tw-graph.node-label
            text="{{ $text }}"
            side="{{ $resolvedLabelSide }}"
            length="{{ $labelLength }}"
            color="{{ $labelColor }}"
            badge-color="{{ $badgeColor }}"
        />
    @endif
</div>
