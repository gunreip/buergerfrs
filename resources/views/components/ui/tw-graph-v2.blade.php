{{-- resources/views/components/ui/tw-graph-v2.blade.php --}}
{{--
    Usage:
    <x-ui.tw-graph-v2>
        <x-ui.tw-graph-v2.trunk.start text="Root #701" />
    </x-ui.tw-graph-v2>

    Optional:
    color="zinc|red|orange|amber|yellow|lime|green|emerald|teal|cyan|sky|blue|indigo|violet|purple|fuchsia|pink|rose"
    min-width="40rem|64rem|..." Canvas minimum width.
    min-height="18rem|32rem|..." Canvas minimum height.
    path-width="0.25rem" Shared path/border width. Prefer pixel-friendly values.
    node-core-size="0.1rem" Inner node core; border grows it to node-size.
    node-size="1rem" Shared node diameter.
    arc-size="2.75rem" Shared arc box size.
    path-start-stop="85%" Gradient stop for path-start.
    dev="true|false" Render graph primitives with transparent debug opacity.
    anchor-points="true|false" Root display mode. true shows only <x-ui.tw-graph-v2.anchor-points>; false shows graph parts and hides anchor-points.

    Geometry rule:
    v2 elements own their internal border/path geometry. Compose graph parts by
    logical center points, length, side, and branch offset; avoid scattering
    small ad-hoc offset corrections through higher-level components.

    Render order:
    Callers write graph parts from bottom to top. The root component reverses
    that order visually, so the markup can stay close to the timeline/root
    mental model while the graph still renders upward.
--}}

@props([
    'color' => 'zinc',
    'minWidth' => '40rem',
    'minHeight' => '18rem',
    'pathWidth' => '0.25rem',
    'nodeCoreSize' => '0.1rem',
    'nodeSize' => '1rem',
    'arcSize' => '2.75rem',
    'pathStartStop' => '85%',
    'graphId' => null,
    'dev' => false,
    'anchorPoints' => false,
])

@php
    $colorRgb = \Gunreip\TranslationWorkbench\Support\TranslationWorkbenchColorPalette::rgb($color, '113 113 122');
    $showDevMode = filter_var($dev, FILTER_VALIDATE_BOOLEAN);
    $showAnchorPointsMode = filter_var($anchorPoints, FILTER_VALIDATE_BOOLEAN);
    $resolvedGraphId = filled($graphId) ? $graphId : 'tw-graph-v2-' . str()->uuid()->toString();

    $dynamicStyles = [
        '--tw-graph-v2-color-rgb: ' . $colorRgb,
        '--tw-graph-v2-canvas-min-width: ' . $minWidth,
        '--tw-graph-v2-canvas-min-height: ' . $minHeight,
        '--tw-graph-v2-path-width: ' . $pathWidth,
        '--tw-graph-v2-node-core-size: ' . $nodeCoreSize,
        '--tw-graph-v2-node-size: ' . $nodeSize,
        '--tw-graph-v2-arc-size: ' . $arcSize,
        '--tw-graph-v2-path-start-stop: ' . $pathStartStop,
    ];
@endphp

<div
    {{ $attributes->merge(['id' => $resolvedGraphId])->class([
        'tw-graph-v2',
        'tw-graph-dev' => $showDevMode,
        'tw-graph-v2-anchor-points-mode' => $showAnchorPointsMode,
    ])->style($dynamicStyles) }}
>
    {{ $slot }}
</div>
