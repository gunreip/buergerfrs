@props(['graphId'])
@php
    $returnColors = \Gunreip\TranslationWorkbench\Support\TwGraph\ReturnColorRegistry::resolve($graphId);
@endphp
@if ($returnColors !== [])
    <style data-tw-graph-return-colors>
        @foreach ($returnColors as $path => $color)
            @php
                $rgb = \Gunreip\TranslationWorkbench\Support\TranslationWorkbenchColorPalette::rgb($color);
                $surface = \Gunreip\TranslationWorkbench\Support\TranslationWorkbenchColorPalette::surfaceRgb($color, $rgb);
                $dark = \Gunreip\TranslationWorkbench\Support\TranslationWorkbenchColorPalette::darkSurfaceRgb($color, $surface);
            @endphp
            [id={!! \Gunreip\TranslationWorkbench\Support\TwGraph\ReturnColorRegistry::selectorValue($graphId) !!}] [data-tw-graph-path={!! \Gunreip\TranslationWorkbench\Support\TwGraph\ReturnColorRegistry::selectorValue($path) !!}] {
                --tw-graph-protocol-local-color-rgb: {{ $rgb }} !important;
                --tw-graph-protocol-local-to-color-rgb: {{ $rgb }} !important;
                --tw-graph-protocol-local-surface-color-rgb: {{ $surface }} !important;
                --tw-graph-protocol-local-to-surface-color-rgb: {{ $surface }} !important;
                --tw-graph-protocol-local-dark-surface-color-rgb: {{ $dark }} !important;
                --tw-graph-protocol-local-to-dark-surface-color-rgb: {{ $dark }} !important;
            }
        @endforeach
    </style>
@endif
