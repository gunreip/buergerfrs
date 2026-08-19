{{-- resources/views/components/ui/tw-graph-protocol.blade.php --}}
{{--
    Protocol-driven graph renderer.

    The JSON protocol is the source of truth. Components render only from
    explicit anchors and segment metadata; they must not rediscover geometry.

    Usage:
    <x-ui.tw-graph-protocol :protocol="$protocol" />
--}}

@props([
    'protocol' => [],
    'graphId' => null,
    'dev' => false,
    'color' => null,
    'minWidth' => null,
    'minHeight' => null,
    'direction' => null,
])

@php
    $geometry = (array) data_get($protocol, 'geometry', []);
    $resolvedDirection = $direction ?: data_get($geometry, 'direction', 'bottom-top');
    $resolvedColor = $color ?: data_get($geometry, 'color', 'cyan');
    $colorRgb = \Gunreip\TranslationWorkbench\Support\TranslationWorkbenchColorPalette::rgb(
        $resolvedColor,
        '6 182 212',
    );
    $resolvedGraphId = filled($graphId) ? $graphId : 'tw-graph-protocol-' . str()->uuid()->toString();
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
            foreach (data_get($protocol, "twGraph.strang.merge.{$side}.extensions", []) as $extension) {
                $segments = $segments->merge(data_get($extension, 'segments', []));
            }
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
@endphp

<div
    data-tw-graph-direction="{{ $resolvedDirection }}"
    {{ $attributes->merge(['id' => $resolvedGraphId])->class('tw-graph-protocol')->style([
            '--tw-graph-protocol-color-rgb: ' . $colorRgb,
            '--tw-graph-protocol-color-alpha: ' . ($dev ? '0.5' : '1'),
            '--tw-graph-protocol-min-width: ' . ($minWidth ?: data_get($geometry, 'minWidth', $canvasWidth)),
            '--tw-graph-protocol-min-height: ' . ($minHeight ?: data_get($geometry, 'minHeight', $canvasHeight)),
            '--tw-graph-protocol-path-width: ' . data_get($geometry, 'pathWidth', '0.25rem'),
            '--tw-graph-protocol-node-size: ' . data_get($geometry, 'nodeSize', '1rem'),
            '--tw-graph-protocol-arc-size: ' . data_get($geometry, 'arcSize', '2.75rem'),
        ]) }}
>
    <x-ui.tw-graph-protocol.canvas
        :protocol="$protocol"
        :direction="$resolvedDirection"
    />
</div>
