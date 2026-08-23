{{-- packages/gunreip/laravel-translation-workbench/resources/views/components/ui/tw-graph/canvas-metrics.blade.php --}}
{{--
    DEV overlay: canvas bounds metrics

    Usage:
    <x-translation-workbench::ui.tw-graph.canvas-metrics graph-id="example" />

    Rule:
    Shows the current BoundsRegistry summary for left/center/right graph areas.
    This is diagnostic only and must not affect graph geometry.
--}}

@props([
    'graphId' => null,
    'dev' => false,
    'coordinates' => true,
])

@php
    $showCoordinates = filter_var($coordinates, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? (bool) $coordinates;
    $summary = filled($graphId)
        ? \Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry::summary((string) $graphId)
        : ['left' => [], 'center' => [], 'right' => []];
    $canvasMetrics = filled($graphId)
        ? \Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry::canvasMetrics((string) $graphId, '2rem', '12rem')
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
    $formatRem = fn (mixed $value): ?string => is_numeric($value)
        ? rtrim(rtrim(number_format((float) $value, 2, '.', ''), '0'), '.') . 'rem'
        : null;
    $largestSide = collect(['left', 'center', 'right'])
        ->mapWithKeys(fn (string $side): array => [$side => data_get($summary, $side . '.heightRem')])
        ->filter(fn (mixed $value): bool => is_numeric($value))
        ->sortDesc()
        ->keys()
        ->first();
@endphp

@if ($dev)
    @if (filled($graphId))
        <style>
            #{{ $graphId }} {
                --tw-graph-protocol-trunk-x: {{ $originLeft }};
                --tw-graph-protocol-calculated-width: {{ $canvasWidth }};
                --tw-graph-protocol-origin-bottom: {{ $originBottom }};
                --tw-graph-protocol-calculated-height: {{ $canvasHeight }};
            }
        </style>
    @endif

    @php
        $displayOriginBottom = $formatRem(data_get($canvasMetrics, 'originBottomRem'))
            ?? (strlen($originBottom) > 24 ? 'calc(...)' : $originBottom);
        $displayCanvasHeight = $formatRem(data_get($canvasMetrics, 'heightRem'))
            ?? (strlen($canvasHeight) > 24 ? 'calc(...)' : $canvasHeight);
        $displayOriginLeft = $formatRem(data_get($canvasMetrics, 'originLeftRem'))
            ?? (strlen($originLeft) > 24 ? 'calc(...)' : $originLeft);
        $displayCanvasWidth = $formatRem(data_get($canvasMetrics, 'widthRem'))
            ?? (strlen($canvasWidth) > 24 ? 'calc(...)' : $canvasWidth);
        $displayMinX = $formatRem(data_get($canvasMetrics, 'minXRem'))
            ?? (strlen($minX) > 24 ? 'min(...)' : $minX);
        $displayMaxX = $formatRem(data_get($canvasMetrics, 'maxXRem'))
            ?? (strlen($maxX) > 24 ? 'max(...)' : $maxX);
    @endphp

    @if ($showCoordinates)
        <span
            class="tw-graph-protocol-dev-only tw-graph-protocol-coordinate-only pointer-events-none absolute bottom-0 top-0 z-40 w-px"
            style="
                left: calc(var(--tw-graph-protocol-trunk-x) + {{ $minX }});
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
                left: calc(var(--tw-graph-protocol-trunk-x) + {{ $maxX }});
                background-color: rgb(168 85 247 / 0.8);
            "
        ></span>

        <span
            class="tw-graph-protocol-dev-only tw-graph-protocol-coordinate-only pointer-events-none absolute left-0 right-0 z-40 h-px"
            style="
                bottom: {{ $originBottom }};
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
                    bottom: calc({{ $originBottom }} + {{ $top }});
                    background-color: rgb({{ $lineColors[$side] }} / 0.85);
                "
            ></span>

            <span
                class="tw-graph-protocol-dev-only tw-graph-protocol-coordinate-only pointer-events-auto absolute z-50 rounded border border-zinc-400/50 bg-white/90 px-2 py-1 font-mono text-[0.65rem] leading-tight text-zinc-800 shadow-sm dark:border-zinc-500/50 dark:bg-zinc-900/90 dark:text-zinc-100 {{ $positions[$side] }}"
            >
                <span class="block uppercase tracking-wide">
                    {{ $labels[$side] }}
                </span>
                <span class="block">
                    top={{ $resultTop }}
                </span>
                <span class="block">
                    h={{ $resultHeight }}
                </span>
                <span class="block">
                    n={{ data_get($sideSummary, 'count', 0) }}
                </span>
                @if ($largestSide === $side)
                    <span class="mt-0.5 block rounded bg-red-500/15 px-1 text-red-700 dark:text-red-300">
                        largest
                    </span>
                @endif
                @if ($side === 'center')
                    <span class="block">
                        left={{ $displayOriginLeft }}
                    </span>
                    <span class="block">
                        width={{ $displayCanvasWidth }}
                    </span>
                    <span class="block">
                        minX={{ $displayMinX }}
                    </span>
                    <span class="block">
                        maxX={{ $displayMaxX }}
                    </span>
                    <span class="block">
                        bottom={{ $displayOriginBottom }}
                    </span>
                    <span class="block">
                        height={{ $displayCanvasHeight }}
                    </span>
                @endif
            </span>
        @endforeach
    @endif
@endif
