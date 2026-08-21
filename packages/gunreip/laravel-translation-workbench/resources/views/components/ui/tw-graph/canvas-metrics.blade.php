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
])

@php
    $summary = filled($graphId)
        ? \Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry::summary((string) $graphId)
        : ['left' => [], 'center' => [], 'right' => []];
    $originBottom = filled($graphId)
        ? \Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry::originBottom((string) $graphId)
        : '0rem';
    $canvasHeight = filled($graphId)
        ? \Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry::canvasHeight((string) $graphId)
        : '0rem';
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
@endphp

@if ($dev)
    @if (filled($graphId))
        <style>
            #{{ $graphId }} {
                --tw-graph-protocol-origin-bottom: {{ $originBottom }};
                --tw-graph-protocol-calculated-height: {{ $canvasHeight }};
            }
        </style>
    @endif

    <span
        class="tw-graph-protocol-dev-only pointer-events-none absolute left-0 right-0 z-40 h-px"
        style="
            bottom: {{ $originBottom }};
            background-color: rgb(239 68 68 / 0.75);
        "
        title="canvas-origin-bottom | {{ $originBottom }}"
    ></span>

    @foreach (['left', 'center', 'right'] as $side)
        @php
            $sideSummary = $summary[$side] ?? ['count' => 0, 'height' => '0rem', 'items' => []];
            $height = (string) data_get($sideSummary, 'height', '0rem');
            $top = (string) data_get($sideSummary, 'top', '0rem');
            $displayHeight = strlen($height) > 28 ? 'max(...)' : $height;
            $title = collect($sideSummary['items'] ?? [])
                ->map(fn (array $item): string => $item['id'] . ' | h=' . $item['height'])
                ->implode("\n");
        @endphp

        <span
            class="tw-graph-protocol-dev-only pointer-events-none absolute z-40 h-px"
            style="
                {{ $linePositions[$side] }}
                bottom: calc({{ $originBottom }} + {{ $top }});
                background-color: rgb({{ $lineColors[$side] }} / 0.85);
            "
            title="{{ $labels[$side] }} | top={{ $top }}"
        ></span>

        <span
            class="tw-graph-protocol-dev-only pointer-events-auto absolute z-50 rounded border border-zinc-400/50 bg-white/90 px-2 py-1 font-mono text-[0.65rem] leading-tight text-zinc-800 shadow-sm dark:border-zinc-500/50 dark:bg-zinc-900/90 dark:text-zinc-100 {{ $positions[$side] }}"
            title="{{ $title }}"
        >
            <span class="block uppercase tracking-wide">
                {{ $labels[$side] }}
            </span>
            <span class="block">
                h={{ $displayHeight }}
            </span>
            <span class="block">
                top={{ strlen($top) > 24 ? 'max(...)' : $top }}
            </span>
            <span class="block">
                n={{ data_get($sideSummary, 'count', 0) }}
            </span>
            @if ($side === 'center')
                <span class="block">
                    bottom={{ strlen($originBottom) > 24 ? 'max(...)' : $originBottom }}
                </span>
                <span class="block">
                    height={{ strlen($canvasHeight) > 24 ? 'calc(...)' : $canvasHeight }}
                </span>
            @endif
        </span>
    @endforeach
@endif
