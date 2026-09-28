@php
    // Diagnostic settings belong exclusively to the enclosing tw-graph canvas.
    $dev = \Gunreip\TranslationWorkbench\Support\TwGraph\CanvasDiagnostics::current($__env)->dev;
    $coordinates = \Gunreip\TranslationWorkbench\Support\TwGraph\CanvasDiagnostics::current($__env)->coordinates;
    $attributes = ($attributes ?? new \Illuminate\View\ComponentAttributeBag)->except(['dev', 'dev-mode', 'coordinates']);
@endphp
{{-- packages/gunreip/laravel-translation-workbench/resources/views/components/ui/tw-graph/canvas-metrics.blade.php --}}
{{--
    DEV overlay: canvas bounds metrics

    Usage:
    <x-translation-workbench::ui.tw-graph.canvas-metrics graph-id="example" />

    Rule:
    Shows canvas inputs and measured output dimensions, without ID-based side statistics.
    Primitive geometry owns the bounds. Text dimensions are explicitly provisional until
    font layout is available. Geometry mismatches are reported, never corrected by measurement.
--}}

@props([
    'graphId' => null,
    'records' => [],
    'canvasInputs' => [],
    'canvasInputDefaults' => [],
    'horizontalPadding' => '12rem',
])

@php
    $showCoordinates = $coordinates;
    $canvasMetrics = filled($graphId)
        ? \Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry::canvasMetrics(
            (string) $graphId,
            '2rem',
            (string) $horizontalPadding,
        )
        : [
            'minX' => '0rem',
            'minXRem' => 0.0,
            'maxX' => '0rem',
            'maxXRem' => 0.0,
            'originLeft' => '0rem',
            'originLeftRem' => 0.0,
            'width' => '0rem',
            'widthRem' => 0.0,
            'minY' => '0rem',
            'minYRem' => 0.0,
            'maxY' => '0rem',
            'maxYRem' => 0.0,
            'originBottom' => '0rem',
            'originBottomRem' => 0.0,
            'height' => '0rem',
            'heightRem' => 0.0,
        ];
    $originLeft = (string) data_get($canvasMetrics, 'originLeft', '0rem');
    $canvasWidth = (string) data_get($canvasMetrics, 'width', '0rem');
    $minX = (string) data_get($canvasMetrics, 'minX', '0rem');
    $maxX = (string) data_get($canvasMetrics, 'maxX', '0rem');
    $originBottom = (string) data_get($canvasMetrics, 'originBottom', '0rem');
    $canvasHeight = (string) data_get($canvasMetrics, 'height', '0rem');
@endphp

@if (filled($graphId))
    <script type="application/json" data-tw-graph-bounds-records>{!! json_encode($records, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) !!}</script>
    <style>
        #{{ $graphId }} {
            --tw-graph-protocol-trunk-x: {{ $originLeft }};
            --tw-graph-protocol-content-min-x: {{ $minX }};
            --tw-graph-protocol-content-max-x: {{ $maxX }};
            --tw-graph-protocol-content-min-y: {{ $canvasMetrics['minY'] }};
            --tw-graph-protocol-content-width: calc({{ $maxX }} - {{ $minX }});
            --tw-graph-protocol-content-height: calc({{ $canvasMetrics['maxY'] }} - {{ $canvasMetrics['minY'] }});
            --tw-graph-protocol-calculated-width: {{ $canvasWidth }};
            --tw-graph-protocol-origin-bottom: {{ $originBottom }};
            --tw-graph-protocol-calculated-height: {{ $canvasHeight }};
        }
    </style>
@endif

@if ($dev)
    @foreach (collect($records)->pluck('region')->filter(fn ($region) => is_array($region) && ($region['showBox'] ?? true))->unique('token') as $region)
        <x-translation-workbench::ui.tw-graph.dev-box
            :id="$region['id'] . '.dev-box'"
            :component="$region['component']"
            :label="$region['id']"
            :region="$region"
        />
    @endforeach
    <div data-tw-graph-bounds-warning hidden class="tw-graph-protocol-dev-only absolute left-2 top-16 z-50 max-w-xl rounded border border-amber-500 bg-amber-50 p-2 text-xs text-amber-950">
        <strong>Bounds mismatch — geometry was not overridden</strong>
        <pre data-tw-graph-bounds-warning-details class="max-h-48 overflow-auto whitespace-pre-wrap"></pre>
    </div>
    <span data-tw-graph-bounds-state class="tw-graph-protocol-dev-only absolute bottom-2 right-2 z-50 rounded bg-zinc-900 px-2 py-1 text-xs text-white">Bounds: geometry calculated; text dimensions provisional</span>
        <span data-tw-graph-content-bounds data-tw-graph-dev-box="{{ $graphId }}.content-bounds" class="tw-graph-protocol-dev-only pointer-events-none absolute z-40" aria-hidden="true"
            style="left:calc(var(--tw-graph-protocol-trunk-x) + var(--tw-graph-protocol-content-min-x, 0rem)); bottom:calc(var(--tw-graph-protocol-origin-bottom) + var(--tw-graph-protocol-content-min-y, 0rem)); width:var(--tw-graph-protocol-content-width, 0rem); height:var(--tw-graph-protocol-content-height, 0rem); outline:1px dashed rgb(56 189 248 / 1);"></span>

    @if ($showCoordinates)
        <span
            class="tw-graph-protocol-dev-only tw-graph-protocol-coordinate-only pointer-events-none absolute bottom-0 top-0 z-40 w-px"
            style="
                left: calc(var(--tw-graph-protocol-trunk-x) + var(--tw-graph-protocol-content-min-x, {{ $minX }}));
                background-color: rgb(244 114 182 / 0.8);
            "
        ></span>
        <span
            class="tw-graph-protocol-dev-only tw-graph-protocol-coordinate-only pointer-events-none absolute bottom-0 top-0 z-40 w-px"
            style="
                left: var(--tw-graph-protocol-trunk-x);
                background-color: rgb(56 189 248 / 0.8);
            "
        ></span>
        <span
            class="tw-graph-protocol-dev-only tw-graph-protocol-coordinate-only pointer-events-none absolute bottom-0 top-0 z-40 w-px"
            style="
                left: calc(var(--tw-graph-protocol-trunk-x) + var(--tw-graph-protocol-content-max-x, {{ $maxX }}));
                background-color: rgb(168 85 247 / 0.8);
            "
        ></span>

        <span
            class="tw-graph-protocol-dev-only tw-graph-protocol-coordinate-only pointer-events-none absolute left-0 right-0 z-40 h-px"
            style="
                bottom: var(--tw-graph-protocol-origin-bottom);
                background-color: rgb(239 68 68 / 0.75);
            "
        ></span>

        <span class="tw-graph-protocol-dev-only tw-graph-protocol-coordinate-only pointer-events-none absolute left-0 right-0 z-40 h-px"
            style="bottom:calc(var(--tw-graph-protocol-origin-bottom) + var(--tw-graph-protocol-content-min-y, 0rem) + var(--tw-graph-protocol-content-height, 0rem)); background-color:rgb(56 189 248 / .8);"></span>

        <div data-tw-graph-canvas-summary
            class="tw-graph-protocol-dev-only tw-graph-protocol-coordinate-only pointer-events-auto absolute left-2 top-2 z-50 max-w-full rounded border border-zinc-400/50 bg-white/95 px-3 py-2 text-xs leading-relaxed text-zinc-800 shadow-sm dark:border-zinc-500/50 dark:bg-zinc-900/95 dark:text-zinc-100">
            <flux:heading size="sm">...::ui.tw-graph · {{ __('Canvas dimensions') }}</flux:heading>
            <dl class="mt-1 grid grid-cols-[auto_auto] gap-x-4">
                @foreach ($canvasInputs as $prop => $value)
                    <dt><code>{{ $prop }}</code>@if ($canvasInputDefaults[$prop] ?? false) <span class="text-zinc-500">({{ __('Default') }})</span>@endif</dt>
                    <dd class="text-right font-mono" data-tw-graph-canvas-input="{{ $prop }}" data-value="{{ $value }}">{{ $value }}</dd>
                @endforeach
                <dt class="mt-1 border-t border-zinc-400/40">{{ __('Actual canvas (W × H)') }}</dt>
                <dd class="mt-1 border-t border-zinc-400/40 text-right font-mono" data-tw-graph-canvas-result="canvas">—</dd>
                <dt>{{ __('Graph content (W × H)') }}</dt>
                <dd class="text-right font-mono" data-tw-graph-canvas-result="content">—</dd>
                <dt>{{ __('Space left / right') }}</dt>
                <dd class="text-right font-mono" data-tw-graph-canvas-result="spacing">—</dd>
            </dl>
        </div>
    @endif
@endif
