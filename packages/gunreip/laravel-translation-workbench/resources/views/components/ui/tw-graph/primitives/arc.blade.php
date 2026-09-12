{{-- packages/gunreip/laravel-translation-workbench/resources/views/components/ui/tw-graph/primitives/arc.blade.php --}}
{{--
    Primitive: arc

    Usage:
    <x-translation-workbench::ui.tw-graph.primitives.arc
        start-anchor="n"
        end-anchor="w"
        start-x="0rem"
        start-y="4rem"
        end-x="-2.75rem"
        end-y="1.25rem"
    />

    Rule:
    Arc is neutral. Segments decide whether it represents north-west,
    west-north, etc. Opposite anchor orders share the same visual corner but
    keep their semantic start/end anchors. nodeStart/nodeEnd are owned by the
    arc primitive because labels/connectors need visible anchor nodes.
--}}

@props([
    'id' => 'arc',
    'startAnchor' => 'n',
    'endAnchor' => 'w',
    'startX' => '0rem',
    'startY' => '0rem',
    'endX' => '0rem',
    'endY' => '0rem',
    'arcSize' => null,
    'nodeStart' => false,
    'nodeEnd' => false,
    'nodeStartSize' => null,
    'nodeEndSize' => null,
    'dashed' => false,
    'color' => 'zinc',
    'toColor' => null,
    'tone' => 'line',
    'zIndex' => null,
])

@php
    $anchors = collect([
        $startAnchor => ['x' => $startX, 'y' => $startY],
        $endAnchor => ['x' => $endX, 'y' => $endY],
    ]);
    $pair = collect([$startAnchor, $endAnchor])->sort()->implode('-');
    $corner = match ($pair) {
        'n-w' => 'nw',
        'e-n' => 'ne',
        's-w' => 'sw',
        default => 'se',
    };
    $styleStartX = match ($corner) {
        'se' => data_get($anchors, 'e.x', $startX),
        'sw' => data_get($anchors, 'w.x', $startX),
        default => $startX,
    };
    $styleStartY = match ($corner) {
        'nw', 'ne' => data_get($anchors, 'n.y', $startY),
        default => $startY,
    };
    $styleEndX = match ($corner) {
        'nw' => data_get($anchors, 'w.x', $endX),
        'ne' => data_get($anchors, 'e.x', $endX),
        default => $endX,
    };
    $styleEndY = match ($corner) {
        'se', 'sw' => data_get($anchors, 's.y', $endY),
        default => $endY,
    };
    $colorRgb = \Gunreip\TranslationWorkbench\Support\TranslationWorkbenchColorPalette::rgb($color, '113 113 122');
    $surfaceColorRgb = \Gunreip\TranslationWorkbench\Support\TranslationWorkbenchColorPalette::surfaceRgb($color, $colorRgb);
    $darkSurfaceColorRgb = \Gunreip\TranslationWorkbench\Support\TranslationWorkbenchColorPalette::darkSurfaceRgb($color, $surfaceColorRgb);
    $toColorRgb = \Gunreip\TranslationWorkbench\Support\TranslationWorkbenchColorPalette::rgb($toColor, $colorRgb);
    $toSurfaceColorRgb = \Gunreip\TranslationWorkbench\Support\TranslationWorkbenchColorPalette::surfaceRgb($toColor, $surfaceColorRgb);
    $toDarkSurfaceColorRgb = \Gunreip\TranslationWorkbench\Support\TranslationWorkbenchColorPalette::darkSurfaceRgb($toColor, $toSurfaceColorRgb);
    $devIdentifier = \Gunreip\TranslationWorkbench\Support\TwGraph\DevIdentifier::label($id);
    $devNodeStartIdentifier = \Gunreip\TranslationWorkbench\Support\TwGraph\DevIdentifier::label($id . '.node.start');
    $devNodeEndIdentifier = \Gunreip\TranslationWorkbench\Support\TwGraph\DevIdentifier::label($id . '.node.end');
@endphp

<span
    {{ $attributes->class([
        'tw-graph-protocol-primitive',
        'tw-graph-protocol-primitive-arc',
        'tw-graph-protocol-primitive-arc-' . $corner,
        'tw-graph-protocol-primitive-arc-dashed' => (bool) $dashed,
        'tw-graph-protocol-tone-surface' => $tone === 'surface',
    ])->style([
        '--tw-graph-protocol-start-x: ' . $styleStartX,
        '--tw-graph-protocol-start-y: ' . $styleStartY,
        '--tw-graph-protocol-end-x: ' . $styleEndX,
        '--tw-graph-protocol-end-y: ' . $styleEndY,
        '--tw-graph-protocol-local-arc-size: ' . $arcSize => filled($arcSize),
        '--tw-graph-protocol-local-color-rgb: ' . $colorRgb,
        '--tw-graph-protocol-local-surface-color-rgb: ' . $surfaceColorRgb,
        '--tw-graph-protocol-local-dark-surface-color-rgb: ' . $darkSurfaceColorRgb,
        '--tw-graph-protocol-local-to-color-rgb: ' . $toColorRgb,
        '--tw-graph-protocol-local-to-surface-color-rgb: ' . $toSurfaceColorRgb,
        '--tw-graph-protocol-local-to-dark-surface-color-rgb: ' . $toDarkSurfaceColorRgb,
        '--tw-graph-protocol-z-index: ' . $zIndex => filled($zIndex),
    ]) }}
    title="{{ $devIdentifier }}"
    data-tw-graph-path="{{ $devIdentifier }}"
    x-on:click.stop="navigator.clipboard?.writeText($el.dataset.twGraphPath)"
></span>

@if ($nodeStart)
    <span
        @class([
            'tw-graph-protocol-primitive',
            'tw-graph-protocol-primitive-node',
            'tw-graph-protocol-tone-surface' => $tone === 'surface',
        ])
        style="
            --tw-graph-protocol-anchor-x: {{ $startX }};
            --tw-graph-protocol-anchor-y: {{ $startY }};
            --tw-graph-protocol-local-color-rgb: {{ $colorRgb }};
            --tw-graph-protocol-local-surface-color-rgb: {{ $surfaceColorRgb }};
            --tw-graph-protocol-local-dark-surface-color-rgb: {{ $darkSurfaceColorRgb }};
            @if (filled($nodeStartSize))
                --tw-graph-protocol-local-node-size: {{ $nodeStartSize }};
            @endif
            @if (filled($zIndex))
                --tw-graph-protocol-z-index: {{ $zIndex }};
            @endif
        "
        title="{{ $devNodeStartIdentifier }}"
        data-tw-graph-path="{{ $devNodeStartIdentifier }}"
        x-on:click.stop="navigator.clipboard?.writeText($el.dataset.twGraphPath)"
    ></span>
@endif

@if ($nodeEnd)
    <span
        @class([
            'tw-graph-protocol-primitive',
            'tw-graph-protocol-primitive-node',
            'tw-graph-protocol-tone-surface' => $tone === 'surface',
        ])
        style="
            --tw-graph-protocol-anchor-x: {{ $endX }};
            --tw-graph-protocol-anchor-y: {{ $endY }};
            --tw-graph-protocol-local-color-rgb: {{ $colorRgb }};
            --tw-graph-protocol-local-surface-color-rgb: {{ $surfaceColorRgb }};
            --tw-graph-protocol-local-dark-surface-color-rgb: {{ $darkSurfaceColorRgb }};
            @if (filled($nodeEndSize))
                --tw-graph-protocol-local-node-size: {{ $nodeEndSize }};
            @endif
            @if (filled($zIndex))
                --tw-graph-protocol-z-index: {{ $zIndex }};
            @endif
        "
        title="{{ $devNodeEndIdentifier }}"
        data-tw-graph-path="{{ $devNodeEndIdentifier }}"
        x-on:click.stop="navigator.clipboard?.writeText($el.dataset.twGraphPath)"
    ></span>
@endif
