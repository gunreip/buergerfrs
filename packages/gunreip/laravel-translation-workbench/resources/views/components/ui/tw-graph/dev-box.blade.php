@php
    // Diagnostic settings belong exclusively to the enclosing tw-graph canvas.
    $dev = \Gunreip\TranslationWorkbench\Support\TwGraph\CanvasDiagnostics::current($__env)->dev;
    $attributes = ($attributes ?? new \Illuminate\View\ComponentAttributeBag)->except(['dev', 'dev-mode', 'coordinates']);
@endphp
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

@props([
    'id' => 'tw-graph.dev-box',
    'x' => '0rem',
    'y' => '0rem',
    'width' => '0rem',
    'height' => '0rem',
    'color' => 'sky',
    'label' => null,
])

@php
    $colorRgb = \Gunreip\TranslationWorkbench\Support\TranslationWorkbenchColorPalette::rgb($color, '14 165 233');
    $devIdentifier = \Gunreip\TranslationWorkbench\Support\TwGraph\DevIdentifier::label($label ?? $id);

@endphp

@if ($dev)
    <span
        data-tw-graph-dev-box="{{ $id }}"
        class="tw-graph-protocol-dev-only group pointer-events-none absolute rounded border border-dashed"
        style="
            left: calc(var(--tw-graph-protocol-trunk-x) + {{ $x }});
            bottom: calc(var(--tw-graph-protocol-origin-bottom) + {{ $y }});
            width: {{ $width }};
            height: {{ $height }};
            border-color: rgb({{ $colorRgb }} / 0.35);
        "
        title="{{ $devIdentifier }}{{ \Gunreip\TranslationWorkbench\Support\TwGraph\RootIdentifier::tooltipSuffix() }}"
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
