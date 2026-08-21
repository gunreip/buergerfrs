{{-- packages/gunreip/laravel-translation-workbench/resources/views/components/ui/tw-graph.blade.php --}}
{{--
    Authoring graph renderer baseline.

    Package rule:
    tw-graph is the new authoring family for the translation-workbench package.
    It was copied from tw-graph-protocol as a working baseline; future graph API
    changes belong here while tw-graph-protocol remains the frozen reference.

    Usage:
    <x-translation-workbench::ui.tw-graph graph-id="example">
        <x-translation-workbench::ui.tw-graph.strang.trunk />
    </x-translation-workbench::ui.tw-graph>
--}}

@props([
    'protocol' => [],
    'graphId' => null,
    'dev' => false,
    'color' => null,
    'defaultColor' => null,
    'lineLength' => '4rem',
    'lineWidth' => '0.25rem',
    'nodeSize' => '0.95rem',
    'arcSize' => '2.75rem',
    'capLength' => '1.75rem',
    'bridgeLength' => null,
    'stemHeight' => null,
    'connectorLength' => '2rem',
    'connectorGap' => '0.25rem',
    'slotMinHeight' => '52rem',
    'minWidth' => null,
    'minHeight' => null,
])

@php
    $geometry = (array) data_get($protocol, 'geometry', []);
    $resolvedDirection = data_get($geometry, 'direction', 'bottom-top');
    $resolvedDefaultColor = $defaultColor ?: ($color ?: data_get($geometry, 'color', 'zinc'));
    $resolvedColor = $color ?: data_get($geometry, 'color', $resolvedDefaultColor);
    $colorRgb = \Gunreip\TranslationWorkbench\Support\TranslationWorkbenchColorPalette::rgb(
        $resolvedColor,
        '6 182 212',
    );
    $resolvedGraphId = filled($graphId) ? $graphId : 'tw-graph-' . str()->uuid()->toString();
    $collectSegments = function (array $protocol): \Illuminate\Support\Collection {
        $segments = collect();

        foreach (data_get($protocol, 'twGraph.strang.trunk.trunk.paths', []) as $path) {
            $segments = $segments
                ->when(
                    data_get($path, 'segments.start'),
                    fn(\Illuminate\Support\Collection $items, array $segment) => $items->push($segment),
                )
                ->merge(data_get($path, 'segments.paths', []))
                ->when(
                    data_get($path, 'segments.end'),
                    fn(\Illuminate\Support\Collection $items, array $segment) => $items->push($segment),
                );
        }

        foreach (['left', 'right'] as $side) {
            $segments = $segments
                ->merge(data_get($protocol, "twGraph.strang.merge.{$side}.merge.paths.merge.segments", []))
                ->when(
                    data_get($protocol, "twGraph.strang.merge.{$side}.merge.paths.mergeEnd.segment"),
                    fn(\Illuminate\Support\Collection $items, array $segment) => $items->push($segment),
                );

            foreach (data_get($protocol, "twGraph.strang.branch.{$side}", []) as $branch) {
                $segments = $segments->merge(data_get($branch, 'segments', []));

                foreach (data_get($branch, 'extensions', []) as $extension) {
                    $segments = $segments->merge(data_get($extension, 'segments', []));
                }
            }
        }

        return $segments->filter(fn($segment): bool => is_array($segment));
    };
    $toRem = fn(mixed $value): float => (float) str_replace('rem', '', (string) $value);
    $segments = $collectSegments((array) $protocol);
    $maxY =
        $segments
            ->flatMap(
                fn(array $segment): array => [data_get($segment, 'anchorStart.y'), data_get($segment, 'anchorEnd.y')],
            )
            ->filter(fn($value): bool => $value !== null)
            ->map($toRem)
            ->max() ?? 0;
    $minY =
        $segments
            ->flatMap(
                fn(array $segment): array => [data_get($segment, 'anchorStart.y'), data_get($segment, 'anchorEnd.y')],
            )
            ->filter(fn($value): bool => $value !== null)
            ->map($toRem)
            ->min() ?? 0;
    $minX =
        $segments
            ->flatMap(
                fn(array $segment): array => [data_get($segment, 'anchorStart.x'), data_get($segment, 'anchorEnd.x')],
            )
            ->filter(fn($value): bool => $value !== null)
            ->map($toRem)
            ->min() ?? 0;
    $maxX =
        $segments
            ->flatMap(
                fn(array $segment): array => [data_get($segment, 'anchorStart.x'), data_get($segment, 'anchorEnd.x')],
            )
            ->filter(fn($value): bool => $value !== null)
            ->map($toRem)
            ->max() ?? 0;
    $canvasWidth = max(40, abs($minX) + abs($maxX) + 12) . 'rem';
    $canvasHeight = max(18, abs($minY) + $maxY + 6) . 'rem';
    $resolvedMinHeight =
        $minHeight ?: ($slot->isNotEmpty() ? $slotMinHeight : data_get($geometry, 'minHeight', $canvasHeight));
@endphp

<div
    data-tw-graph-direction="{{ $resolvedDirection }}"
    {{ $attributes->merge(['id' => $resolvedGraphId])->class('tw-graph-protocol')->style([
            '--tw-graph-protocol-color-rgb: ' . $colorRgb,
            '--tw-graph-protocol-color-alpha: ' . ($dev ? '0.5' : '1'),
            '--tw-graph-protocol-min-width: ' . ($minWidth ?: data_get($geometry, 'minWidth', $canvasWidth)),
            '--tw-graph-protocol-min-height: ' . $resolvedMinHeight,
            '--tw-graph-protocol-path-width: ' . data_get($geometry, 'pathWidth', $lineWidth),
            '--tw-graph-protocol-node-size: ' . data_get($geometry, 'nodeSize', $nodeSize),
            '--tw-graph-protocol-arc-size: ' . data_get($geometry, 'arcSize', $arcSize),
        ]) }}
>
    @if ($slot->isNotEmpty())
        <div class="tw-graph-protocol-canvas tw-graph-protocol-canvas-slot content-center">
            {{ $slot }}

            <x-translation-workbench::ui.tw-graph.canvas-metrics
                :graph-id="$resolvedGraphId"
                :dev="$dev"
            />
        </div>
    @else
        <x-translation-workbench::ui.tw-graph.canvas
            :protocol="$protocol"
            :direction="$resolvedDirection"
            :dev="$dev"
        />
    @endif
</div>
