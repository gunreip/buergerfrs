{{-- resources/views/components/ui/tw-graph/inbound/join.blade.php --}}
{{--
    Usage:
    <x-ui.tw-graph.inbound.join
        side="left"
        parent-offset="8.5rem"
        length="4rem"
        vertical-length="3rem"
        level-offset="1.25rem"
        text="Inbound nested"
        badge-color="lime"
    />

    Props:
    side="left|right" Default: right
    parent-offset="3rem|8.5rem|..." Offset of the inbound path this join flows into.
    length="2rem|4rem|..." Horizontal distance from parent inbound path to this join.
    vertical-length="2rem|4rem|..." Length of the free vertical connector.
    level-offset="0rem|1.25rem|..." Vertical offset from the parent inbound horizontal row.
    color="zinc|red|orange|amber|yellow|lime|green|emerald|teal|cyan|sky|blue|indigo|violet|purple|fuchsia|pink|rose" Default: inherited, fallback zinc
    text="..." Optional. When present, renders a node dot with attached label.
    label-side="left|right" Default: away from trunk.
    label-length="4rem|6rem|..." Default: 4rem
    label-color="zinc|red|orange|amber|yellow|lime|green|emerald|teal|cyan|sky|blue|indigo|violet|purple|fuchsia|pink|rose" Default: inherited connector color
    badge-color="zinc|red|orange|amber|yellow|lime|green|emerald|teal|cyan|sky|blue|indigo|violet|purple|fuchsia|pink|rose" Default: zinc
    origin-id="123" Optional. Renders a path-start below the node dot.
    origin-text="#123" Optional. Overrides origin-id display text.
    origin-length="h-10|h-16|..." Default: h-10
    origin-badge-color="zinc|..." Default: badge-color

    Purpose:
    Adds an inbound path without an inner arc. It flows into an already existing
    inbound path and can be stacked to sketch multiple roots joining one branch.
--}}

@props([
    'side' => null,
    'parentOffset' => null,
    'length' => null,
    'verticalLength' => null,
    'levelOffset' => '0rem',
    'color' => null,
    'text' => null,
    'labelSide' => null,
    'labelLength' => '4rem',
    'labelColor' => null,
    'badgeColor' => 'zinc',
    'originId' => null,
    'originText' => null,
    'originLength' => 'h-10',
    'originBadgeColor' => null,
])

@php
    $resolvedSide = $side === 'left' ? 'left' : 'right';
    $resolvedLabelSide = $labelSide ?? ($resolvedSide === 'left' ? 'left' : 'right');
    $hasText = $text !== null && $text !== '';
    $resolvedOriginText = $originText ?? (filled($originId) ? '#' . $originId : null);
    $hasOrigin = filled($resolvedOriginText);
    $resolvedOriginBadgeColor = $originBadgeColor ?? $badgeColor;

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
        'tw-graph-inbound-join',
        'tw-graph-inbound-join-left' => $resolvedSide === 'left',
        'tw-graph-inbound-join-right' => $resolvedSide !== 'left',
    ])->style([
        '--tw-graph-local-parent-offset: ' . $parentOffset => filled($parentOffset),
        '--tw-graph-local-connector-width: ' . $length => filled($length),
        '--tw-graph-local-connector-height: ' . $verticalLength => filled($verticalLength),
        '--tw-graph-local-level-offset: ' . $levelOffset => filled($levelOffset),
        '--tw-graph-local-path-color-rgb: ' . $colorRgb => filled($colorRgb),
    ]) }}
>
    <div class="tw-graph-inbound-join-connector-horizontal"></div>
    <div class="tw-graph-inbound-join-arc-outer"></div>
    <div
        class="tw-graph-inbound-join-connector-vertical-group"
        @if (! $hasText)
            aria-hidden="true"
        @endif
    >
        <div class="tw-graph-inbound-join-connector-vertical"></div>

        <div class="tw-graph-branch-node tw-graph-inbound-join-node">
            @if ($hasText)
                <x-ui.tw-graph.node-label
                    text="{{ $text }}"
                    side="{{ $resolvedLabelSide }}"
                    length="{{ $labelLength }}"
                    color="{{ $labelColor }}"
                    badge-color="{{ $badgeColor }}"
                />
            @endif
            @if ($hasOrigin)
                <x-ui.tw-graph.path-start
                    class="tw-graph-branch-path-start"
                    text="{{ $resolvedOriginText }}"
                    height="{{ $originLength }}"
                    color="{{ $resolvedOriginBadgeColor }}"
                    node="false"
                />
            @endif
        </div>
    </div>
</div>
