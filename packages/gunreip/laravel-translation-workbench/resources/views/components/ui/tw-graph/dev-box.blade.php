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
    Region boxes show their caption; individual segment boxes disable showLabel
    because the rendered element already owns its tooltip and copy action.
--}}

@props([
    'id' => 'tw-graph.dev-box',
    'x' => '0rem',
    'y' => '0rem',
    'width' => '0rem',
    'height' => '0rem',
    'color' => 'sky',
    'label' => null,
    'component' => null,
    'showLabel' => true,
    'region' => null,
])

@php
    $colorRgb = $region['colorRgb'] ?? \Gunreip\TranslationWorkbench\Support\TranslationWorkbenchColorPalette::rgb($color, '14 165 233');
    $devIdentifier = \Gunreip\TranslationWorkbench\Support\TwGraph\DevIdentifier::label($label ?? $id);

@endphp

@if ($dev)
    <span
        data-tw-graph-dev-box="{{ $id }}"
        @if ($region !== null) data-tw-graph-region="{{ $region['token'] }}" @endif
        class="tw-graph-protocol-dev-only group pointer-events-none absolute rounded border border-dashed"
        style="
            left: calc(var(--tw-graph-protocol-trunk-x) + {{ $x }});
            bottom: calc(var(--tw-graph-protocol-origin-bottom) + {{ $y }});
            width: {{ $width }};
            height: {{ $height }};
            border-color: rgb({{ $colorRgb }} / 1);
            {{ $region !== null ? 'visibility:hidden;' : '' }}
        "
    >
        @if (\Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::bool($showLabel, true))
            <span
                data-tw-graph-dev-caption
                class="absolute left-1 top-0 rounded-sm px-1 py-0.5 font-mono text-[0.6rem] leading-none"
                title="{{ $devIdentifier }}{{ \Gunreip\TranslationWorkbench\Support\TwGraph\RootIdentifier::tooltipSuffix() }}"
                style="width:max-content; overflow-wrap:anywhere; background-color:rgb({{ $colorRgb }} / 0.85); color:rgb(24 24 27);"
            >
                @if (filled($component))
                    <span class="block">{{ $component }}</span>
                    <span class="block">id="{{ $label ?? $id }}"</span>
                @else
                    {{ $devIdentifier }}
                @endif
            </span>
        @endif
    </span>
@endif
