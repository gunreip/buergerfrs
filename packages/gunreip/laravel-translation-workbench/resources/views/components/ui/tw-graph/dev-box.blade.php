{{-- packages/gunreip/laravel-translation-workbench/resources/views/components/ui/tw-graph/dev-box.blade.php --}}
{{--
    DEV overlay: bounding box

    Usage:
    <x-translation-workbench::ui.tw-graph.dev-box
        id="catalog.path.merge"
        x="0rem"
        y="0rem"
        width="8rem"
        height="6rem"
        color="amber"
        label="paths.merge"
    />

    Rule:
    This is a pure diagnostic overlay. It must not affect graph geometry.
--}}

@aware([
    'graphId' => null,
])

@props([
    'id' => 'tw-graph.dev-box',
    'x' => '0rem',
    'y' => '0rem',
    'width' => '0rem',
    'height' => '0rem',
    'color' => 'sky',
    'label' => null,
    'dev' => false,
    'metricsScope' => null,
    'metricsSide' => null,
])

@php
    $colorRgb = \Gunreip\TranslationWorkbench\Support\TranslationWorkbenchColorPalette::rgb($color, '14 165 233');
    $devIdentifier = \Gunreip\TranslationWorkbench\Support\TwGraph\DevIdentifier::label($label ?? $id);

    if (filled($graphId ?? null) && $metricsScope === 'canvas') {
        \Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry::put(
            (string) $graphId,
            (string) $id,
            (string) $x,
            (string) $y,
            (string) $width,
            (string) $height,
            filled($metricsSide) ? (string) $metricsSide : null,
        );
    }
@endphp

@if ($dev)
    <span
        class="tw-graph-protocol-dev-only group pointer-events-none absolute rounded border border-dashed"
        style="
            left: calc(var(--tw-graph-protocol-trunk-x) + {{ $x }});
            bottom: calc(var(--tw-graph-protocol-origin-bottom) + {{ $y }});
            width: {{ $width }};
            height: {{ $height }};
            border-color: rgb({{ $colorRgb }} / 0.35);
        "
        title="{{ $devIdentifier }}"
    >
        <span
            class="absolute left-1 top-0 -translate-y-full rounded-sm px-1 py-0.5 font-mono text-[0.6rem] leading-none opacity-0 transition-opacity group-hover:opacity-100"
            style="
                background-color: rgb({{ $colorRgb }} / 0.85);
                color: rgb(24 24 27);
            "
        >
            {{ $devIdentifier }}
        </span>
    </span>
@endif
