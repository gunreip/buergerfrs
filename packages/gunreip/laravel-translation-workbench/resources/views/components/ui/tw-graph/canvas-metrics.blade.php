{{-- packages/gunreip/laravel-translation-workbench/resources/views/components/ui/tw-graph/canvas-metrics.blade.php --}}
{{--
    DEV overlay: canvas bounds metrics

    Usage:
    <x-translation-workbench::ui.tw-graph.canvas-metrics graph-id="example" />

    Rule:
    Shows the current BoundsRegistry summary for left/center/right graph areas.
    Primitive geometry owns the bounds. Text dimensions are explicitly provisional until
    font layout is available. Geometry mismatches are reported, never corrected by measurement.
--}}

@props([
    'graphId' => null,
    'records' => [],
    'dev' => false,
    'coordinates' => true,
    'horizontalPadding' => '12rem',
])

@php
    $dev = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::bool($dev);
    $showCoordinates = filter_var($coordinates, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? (bool) $coordinates;
    $summary = filled($graphId)
        ? \Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry::summary((string) $graphId)
        : ['left' => [], 'center' => [], 'right' => []];
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
    $positions = [
        'left' => 'left-2 top-2',
        'center' => 'left-1/2 top-2 -translate-x-1/2',
        'right' => 'right-2 top-2',
    ];
    $labels = [
        'left' => 'canvas-corner-left-top',
        'center' => 'canvas-center-top',
        'right' => 'canvas-corner-right-top',
    ];
    $linePositions = [
        'left' => 'left: 0; width: 33%;',
        'center' => 'left: 33%; width: 34%;',
        'right' => 'right: 0; width: 33%;',
    ];
    $lineColors = [
        'left' => '244 114 182',
        'center' => '56 189 248',
        'right' => '168 85 247',
    ];
    $formatRem = fn(mixed $value): ?string => is_numeric($value)
        ? rtrim(rtrim(number_format((float) $value, 2, '.', ''), '0'), '.') . 'rem'
        : null;
    $largestSide = collect(['left', 'center', 'right'])
        ->mapWithKeys(fn(string $side): array => [$side => data_get($summary, $side . '.heightRem')])
        ->filter(fn(mixed $value): bool => is_numeric($value))
        ->sortDesc()
        ->keys()
        ->first();
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
    <div data-tw-graph-bounds-warning hidden class="tw-graph-protocol-dev-only absolute left-2 top-16 z-50 max-w-xl rounded border border-amber-500 bg-amber-50 p-2 text-xs text-amber-950">
        <strong>Bounds mismatch — geometry was not overridden</strong>
        <pre data-tw-graph-bounds-warning-details class="max-h-48 overflow-auto whitespace-pre-wrap"></pre>
    </div>
    <span data-tw-graph-bounds-state class="tw-graph-protocol-dev-only absolute bottom-2 right-2 z-50 rounded bg-zinc-900 px-2 py-1 text-xs text-white">Bounds: geometry calculated; text dimensions provisional</span>
    @php
        $displayOriginBottom =
            $formatRem(data_get($canvasMetrics, 'originBottomRem')) ??
            (strlen($originBottom) > 24 ? 'calc(...)' : $originBottom);
        $displayCanvasHeight =
            $formatRem(data_get($canvasMetrics, 'heightRem')) ??
            (strlen($canvasHeight) > 24 ? 'calc(...)' : $canvasHeight);
        $displayOriginLeft =
            $formatRem(data_get($canvasMetrics, 'originLeftRem')) ??
            (strlen($originLeft) > 24 ? 'calc(...)' : $originLeft);
        $displayCanvasWidth =
            $formatRem(data_get($canvasMetrics, 'widthRem')) ??
            (strlen($canvasWidth) > 24 ? 'calc(...)' : $canvasWidth);
        $displayMinX = $formatRem(data_get($canvasMetrics, 'minXRem')) ?? (strlen($minX) > 24 ? 'min(...)' : $minX);
        $displayMaxX = $formatRem(data_get($canvasMetrics, 'maxXRem')) ?? (strlen($maxX) > 24 ? 'max(...)' : $maxX);
    @endphp

        <span data-tw-graph-content-bounds data-tw-graph-dev-box="{{ $graphId }}.content-bounds" class="tw-graph-protocol-dev-only pointer-events-none absolute z-40" aria-hidden="true"
            style="left:calc(var(--tw-graph-protocol-trunk-x) + var(--tw-graph-protocol-content-min-x, 0rem)); bottom:calc(var(--tw-graph-protocol-origin-bottom) + var(--tw-graph-protocol-content-min-y, 0rem)); width:var(--tw-graph-protocol-content-width, 0rem); height:var(--tw-graph-protocol-content-height, 0rem); outline:1px dashed rgb(56 189 248 / .7);"></span>

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

        @foreach (['left', 'center', 'right'] as $side)
            @php
                $sideSummary = $summary[$side] ?? ['count' => 0, 'height' => '0rem', 'items' => []];
                $height = (string) data_get($sideSummary, 'height', '0rem');
                $top = (string) data_get($sideSummary, 'top', '0rem');
                $displayHeight = strlen($height) > 28 ? 'max(...)' : $height;
                $displayTop = strlen($top) > 24 ? 'max(...)' : $top;
                $resultHeight = $formatRem(data_get($sideSummary, 'heightRem')) ?? $displayHeight;
                $resultTop = $formatRem(data_get($sideSummary, 'topRem')) ?? $displayTop;
            @endphp

            <span
                class="tw-graph-protocol-dev-only tw-graph-protocol-coordinate-only pointer-events-none absolute z-40 h-px"
                style="
                    {{ $linePositions[$side] }}
                    bottom: calc(var(--tw-graph-protocol-origin-bottom) + var(--tw-graph-protocol-side-{{ $side }}-top, {{ $top }}));
                    background-color: rgb({{ $lineColors[$side] }} / 0.85);
                "
            ></span>

            <span
                class="tw-graph-protocol-dev-only tw-graph-protocol-coordinate-only {{ $positions[$side] }} pointer-events-auto absolute z-50 rounded border border-zinc-400/50 bg-white/90 px-2 py-1 font-mono text-[0.65rem] leading-tight text-zinc-800 shadow-sm dark:border-zinc-500/50 dark:bg-zinc-900/90 dark:text-zinc-100"
            >
                <span class="block uppercase tracking-wide">
                    {{ $labels[$side] }}
                </span>
                <span class="block" data-tw-graph-side-value="{{ $side }}.top">
                    top={{ $resultTop }}
                </span>
                <span class="block" data-tw-graph-side-value="{{ $side }}.height">
                    h={{ $resultHeight }}
                </span>
                <span class="block" data-tw-graph-side-value="{{ $side }}.count">
                    n={{ data_get($sideSummary, 'count', 0) }}
                </span>
                <span data-tw-graph-side-largest="{{ $side }}" @if ($largestSide !== $side) hidden @endif class="mt-0.5 rounded bg-red-500/15 px-1 text-red-700 dark:text-red-300">
                    largest
                </span>
                @if ($side === 'center')
                    <span class="block" data-tw-graph-bound-value="left">
                        left={{ $displayOriginLeft }}
                    </span>
                    <span class="block" data-tw-graph-bound-value="width">
                        width={{ $displayCanvasWidth }}
                    </span>
                    <span class="block" data-tw-graph-bound-value="minX">
                        minX={{ $displayMinX }}
                    </span>
                    <span class="block" data-tw-graph-bound-value="maxX">
                        maxX={{ $displayMaxX }}
                    </span>
                    <span class="block" data-tw-graph-bound-value="bottom">
                        bottom={{ $displayOriginBottom }}
                    </span>
                    <span class="block" data-tw-graph-bound-value="height">
                        height={{ $displayCanvasHeight }}
                    </span>
                @endif
            </span>
        @endforeach
    @endif
@endif
