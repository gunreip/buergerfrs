{{-- resources/views/components/ui/tw-graph/outbound/extension.blade.php --}}
{{--
    Usage:
    <x-ui.tw-graph.outbound.extension
        branch-offset="5rem"
        branch-length="4rem"
        length="3rem"
        node
    />

    Optional:
    side="left|right" Default: right
    branch-offset="3rem|5rem|..." Offset of the outbound path being extended.
    branch-length="2rem|4rem|..." Existing outbound vertical connector length.
    length="2rem|4rem|..." Extension length above the existing top node.
    color="zinc|red|orange|amber|yellow|lime|green|emerald|teal|cyan|sky|blue|indigo|violet|purple|fuchsia|pink|rose" Default: inherited, fallback zinc
    node="true|false" Render a node dot at the extension top.
    text="..." Optional. When present, renders node dot with attached label.
    label-side="left|right" Default: away from trunk.
    label-length="4rem|6rem|..." Default: 4rem
    label-color="zinc|red|orange|amber|yellow|lime|green|emerald|teal|cyan|sky|blue|indigo|violet|purple|fuchsia|pink|rose" Default: inherited connector color
    badge-color="zinc|red|orange|amber|yellow|lime|green|emerald|teal|cyan|sky|blue|indigo|violet|purple|fuchsia|pink|rose" Default: zinc
    end-id="123" Optional. Renders a path-end above the extension top node.
    end-text="#123" Optional. Overrides end-id display text.
    end-length="h-10|h-16|..." Default: h-10
    end-badge-color="zinc|..." Default: badge-color

    Purpose:
    Extends the top of an outbound vertical path with another straight segment
    and optional node marker. Use this before adding another outbound join above it.
--}}

@props([
    'side' => null,
    'branchOffset' => null,
    'branchLength' => null,
    'length' => null,
    'color' => null,
    'node' => false,
    'text' => null,
    'labelSide' => null,
    'labelLength' => '4rem',
    'labelColor' => null,
    'badgeColor' => 'zinc',
    'endId' => null,
    'endText' => null,
    'endLength' => 'h-10',
    'endBadgeColor' => null,
])

@php
    $resolvedSide = $side === 'left' ? 'left' : ($side === 'right' ? 'right' : null);
    $resolvedLabelSide = $labelSide ?? ($resolvedSide === 'left' ? 'left' : 'right');
    $hasText = $text !== null && $text !== '';
    $resolvedEndText = $endText ?? (filled($endId) ? '#' . $endId : null);
    $hasEnd = filled($resolvedEndText);
    $showNode = filter_var($node, FILTER_VALIDATE_BOOLEAN) || $hasText || $hasEnd;
    $resolvedEndBadgeColor = $endBadgeColor ?? $badgeColor;

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
        'tw-graph-outbound-extension',
        'tw-graph-outbound-extension-left' => $side === 'left',
        'tw-graph-outbound-extension-right' => $side === 'right',
    ])->style([
        '--tw-graph-local-branch-offset: ' . $branchOffset => filled($branchOffset),
        '--tw-graph-local-branch-height: ' . $branchLength => filled($branchLength),
        '--tw-graph-local-extension-height: ' . $length => filled($length),
        '--tw-graph-local-path-color-rgb: ' . $colorRgb => filled($colorRgb),
    ]) }}
    @if (! $hasText && ! $hasEnd)
        aria-hidden="true"
    @endif
>
    <div class="tw-graph-outbound-extension-path"></div>

    @if ($showNode)
        <div class="tw-graph-branch-node tw-graph-outbound-extension-node">
            @if ($hasText)
                <x-ui.tw-graph.node-label
                    text="{{ $text }}"
                    side="{{ $resolvedLabelSide }}"
                    length="{{ $labelLength }}"
                    color="{{ $labelColor }}"
                    badge-color="{{ $badgeColor }}"
                />
            @endif
            @if ($hasEnd)
                <x-ui.tw-graph.path-end
                    class="tw-graph-branch-path-end"
                    text="{{ $resolvedEndText }}"
                    length="{{ $endLength }}"
                    color="{{ $resolvedEndBadgeColor }}"
                />
            @endif
        </div>
    @endif
</div>
