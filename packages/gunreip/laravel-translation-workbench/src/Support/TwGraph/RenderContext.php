<?php

declare(strict_types=1);

namespace Gunreip\TranslationWorkbench\Support\TwGraph;

use Gunreip\TranslationWorkbench\Support\TranslationWorkbenchColorPalette;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

final class RenderContext
{
    /**
     * @param  array<string, mixed>  $protocol
     * @return array{
     *     geometry: array<string, mixed>,
     *     direction: string,
     *     defaultColor: string,
     *     color: string,
     *     colorRgb: string,
     *     graphId: string,
     *     canvasWidth: string,
     *     canvasHeight: string,
     *     minWidth: string,
     *     minHeight: string,
     *     pathWidth: string,
     *     nodeSize: string,
     *     arcSize: string
     * }
     */
    public static function make(
        array $protocol,
        mixed $graphId,
        bool $slotHasContent,
        mixed $color,
        mixed $defaultColor,
        string $lineWidth,
        string $nodeSize,
        string $arcSize,
        string $slotMinHeight,
        mixed $minWidth,
        mixed $minHeight,
    ): array {
        $geometry = (array) data_get($protocol, 'geometry', []);
        $resolvedDirection = (string) data_get($geometry, 'direction', 'bottom-top');
        $resolvedDefaultColor = (string) ($defaultColor ?: ($color ?: data_get($geometry, 'color', 'zinc')));
        $resolvedColor = (string) ($color ?: data_get($geometry, 'color', $resolvedDefaultColor));
        $resolvedGraphId = filled($graphId) ? (string) $graphId : 'tw-graph-' . Str::uuid()->toString();
        $segments = self::collectSegments($protocol);
        $canvasWidth = self::canvasWidth($segments);
        $canvasHeight = self::canvasHeight($segments);

        return [
            'geometry' => $geometry,
            'direction' => $resolvedDirection,
            'defaultColor' => $resolvedDefaultColor,
            'color' => $resolvedColor,
            'colorRgb' => TranslationWorkbenchColorPalette::rgb($resolvedColor, '6 182 212'),
            'graphId' => $resolvedGraphId,
            'canvasWidth' => $canvasWidth,
            'canvasHeight' => $canvasHeight,
            'minWidth' => (string) ($minWidth ?: data_get($geometry, 'minWidth', $canvasWidth)),
            'minHeight' => (string) ($minHeight ?: ($slotHasContent ? $slotMinHeight : data_get($geometry, 'minHeight', $canvasHeight))),
            'pathWidth' => (string) data_get($geometry, 'pathWidth', $lineWidth),
            'nodeSize' => (string) data_get($geometry, 'nodeSize', $nodeSize),
            'arcSize' => (string) data_get($geometry, 'arcSize', $arcSize),
        ];
    }

    /**
     * @param  array<string, mixed>  $protocol
     * @return Collection<int, array<string, mixed>>
     */
    private static function collectSegments(array $protocol): Collection
    {
        $segments = collect();

        foreach (data_get($protocol, 'twGraph.strang.trunk.trunk.paths', []) as $path) {
            $segments = $segments
                ->when(
                    data_get($path, 'segments.start'),
                    fn (Collection $items, array $segment) => $items->push($segment),
                )
                ->merge(data_get($path, 'segments.paths', []))
                ->when(
                    data_get($path, 'segments.end'),
                    fn (Collection $items, array $segment) => $items->push($segment),
                );
        }

        foreach (['left', 'right'] as $side) {
            $segments = $segments
                ->merge(data_get($protocol, "twGraph.strang.merge.{$side}.merge.paths.merge.segments", []))
                ->when(
                    data_get($protocol, "twGraph.strang.merge.{$side}.merge.paths.mergeEnd.segment"),
                    fn (Collection $items, array $segment) => $items->push($segment),
                );

            foreach (data_get($protocol, "twGraph.strang.branch.{$side}", []) as $branch) {
                $segments = $segments->merge(data_get($branch, 'segments', []));

                foreach (data_get($branch, 'extensions', []) as $extension) {
                    $segments = $segments->merge(data_get($extension, 'segments', []));
                }
            }
        }

        return $segments->filter(fn (mixed $segment): bool => is_array($segment))->values();
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $segments
     */
    private static function canvasWidth(Collection $segments): string
    {
        $minX = self::coordinate($segments, 'x', 'min');
        $maxX = self::coordinate($segments, 'x', 'max');

        return max(40, abs($minX) + abs($maxX) + 12) . 'rem';
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $segments
     */
    private static function canvasHeight(Collection $segments): string
    {
        $minY = self::coordinate($segments, 'y', 'min');
        $maxY = self::coordinate($segments, 'y', 'max');

        return max(18, abs($minY) + $maxY + 6) . 'rem';
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $segments
     */
    private static function coordinate(Collection $segments, string $axis, string $aggregate): float
    {
        $values = $segments
            ->flatMap(fn (array $segment): array => [
                data_get($segment, "anchorStart.{$axis}"),
                data_get($segment, "anchorEnd.{$axis}"),
            ])
            ->filter(fn (mixed $value): bool => $value !== null)
            ->map(fn (mixed $value): float => self::toRem($value))
            ->values();

        if ($values->isEmpty()) {
            return 0.0;
        }

        return $aggregate === 'min' ? (float) $values->min() : (float) $values->max();
    }

    private static function toRem(mixed $value): float
    {
        return (float) str_replace('rem', '', (string) $value);
    }
}
