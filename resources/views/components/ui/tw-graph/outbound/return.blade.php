{{-- resources/views/components/ui/tw-graph/outbound/return.blade.php --}}
{{--
    Usage:
    <x-ui.tw-graph.outbound.return
        side="left"
        branch-offset="0.5rem"
        branch-length="2rem"
        extension-length="6rem"
        length="6rem"
        text="Return to trunk"
        badge-color="emerald"
    />

    Optional:
    side="left|right" Default: right
    branch-offset="0.5rem|5rem|..." Offset of the outbound path returning.
    branch-length="2rem|4rem|..." Existing outbound vertical connector length.
    extension-length="0rem|6rem|..." Existing outbound extension length above it.
    length="2rem|6rem|..." Horizontal return connector length.
    level-offset="0rem|1rem|..." Additional vertical offset above the extension top.
    color="zinc|red|orange|amber|yellow|lime|green|emerald|teal|cyan|sky|blue|indigo|violet|purple|fuchsia|pink|rose" Default: inherited, fallback zinc
    text="..." Optional. Renders an attached node label near the trunk merge.
    label-side="left|right" Default: toward branch.
    label-length="4rem|6rem|..." Default: 4rem
    label-color="zinc|..." Default: inherited connector color
    badge-color="zinc|..." Default: zinc

    Purpose:
    Returns an outbound path back toward the trunk with a reverse connector:
    upper arc, horizontal line, lower arc.
--}}

@props([
    'side' => null,
    'branchOffset' => null,
    'branchLength' => null,
    'extensionLength' => null,
    'length' => null,
    'levelOffset' => '0rem',
    'color' => null,
    'text' => null,
    'labelSide' => null,
    'labelLength' => '4rem',
    'labelColor' => null,
    'badgeColor' => 'zinc',
])

@php
    $resolvedSide = $side === 'left' ? 'left' : 'right';
    $resolvedLabelSide = $labelSide ?? ($resolvedSide === 'left' ? 'right' : 'left');
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
            'tw-graph-branch',
            'tw-graph-outbound-return',
            'tw-graph-outbound-return-left' => $resolvedSide === 'left',
            'tw-graph-outbound-return-right' => $resolvedSide !== 'left',
        ])->style([
            '--tw-graph-local-branch-offset: ' . $branchOffset => filled($branchOffset),
            '--tw-graph-local-branch-height: ' . $branchLength => filled($branchLength),
            '--tw-graph-local-extension-height: ' . $extensionLength => filled($extensionLength),
            '--tw-graph-local-connector-width: ' . $length => filled($length),
            '--tw-graph-local-level-offset: ' . $levelOffset => filled($levelOffset),
            '--tw-graph-local-path-color-rgb: ' . $colorRgb => filled($colorRgb),
        ]) }}
    @if (!$hasText) aria-hidden="true" @endif
>
    <div class="tw-graph-outbound-return-arc-start"></div>
    <div class="tw-graph-outbound-return-connector-horizontal"></div>
    <div class="tw-graph-outbound-return-arc-end"></div>
    {{-- <div class="tw-graph-branch-node tw-graph-outbound-return-node">
        @if ($hasText)
            <x-ui.tw-graph.node-label
                text="{{ $text }}"
                side="{{ $resolvedLabelSide }}"
                length="{{ $labelLength }}"
                color="{{ $labelColor }}"
                badge-color="{{ $badgeColor }}"
            />
        @endif
    </div> --}}
</div>
