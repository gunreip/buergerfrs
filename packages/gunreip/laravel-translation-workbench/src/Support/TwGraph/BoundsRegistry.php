<?php

declare(strict_types=1);

namespace Gunreip\TranslationWorkbench\Support\TwGraph;

final class BoundsRegistry
{
    /**
     * @var array<string, array<string, array{id: string, side: string, x: string, y: string, width: string, height: string}>>
     */
    private static array $bounds = [];

    public static function forgetGraph(string $graphId): void
    {
        unset(self::$bounds[$graphId]);
    }

    public static function put(
        string $graphId,
        string $id,
        string $x,
        string $y,
        string $width,
        string $height,
        ?string $side = null,
    ): void {
        self::$bounds[$graphId][$id] = [
            'id' => $id,
            'side' => $side ?: self::inferSide($id),
            'x' => $x,
            'y' => $y,
            'width' => $width,
            'height' => $height,
        ];
    }

    /**
     * @return array{left: array<string, mixed>, center: array<string, mixed>, right: array<string, mixed>}
     */
    public static function summary(string $graphId): array
    {
        $items = collect(self::$bounds[$graphId] ?? []);

        return collect(['left', 'center', 'right'])
            ->mapWithKeys(fn (string $side): array => [$side => self::sideSummary($items->where('side', $side)->values()->all())])
            ->all();
    }

    public static function originBottom(string $graphId, string $padding = '2rem'): string
    {
        $bottomOffsets = collect(self::$bounds[$graphId] ?? [])
            ->pluck('y')
            ->filter(fn (mixed $value): bool => is_string($value) && trim($value) !== '')
            ->map(fn (string $bottom): string => 'calc((' . $bottom . ') * -1 + ' . $padding . ')')
            ->all();

        return self::cssMax(['0rem', ...$bottomOffsets]);
    }

    public static function canvasHeight(string $graphId, string $padding = '2rem'): string
    {
        $tops = collect(self::$bounds[$graphId] ?? [])
            ->map(fn (array $item): string => 'calc(' . $item['y'] . ' + ' . $item['height'] . ')')
            ->all();

        return 'calc(' . self::originBottom($graphId, $padding) . ' + ' . self::cssMax(['0rem', ...$tops]) . ' + ' . $padding . ')';
    }

    private static function inferSide(string $id): string
    {
        if (str_contains($id, '.left') || str_contains($id, 'branch-left') || str_contains($id, 'merge-left')) {
            return 'left';
        }

        if (str_contains($id, '.right') || str_contains($id, 'branch-right') || str_contains($id, 'merge-right')) {
            return 'right';
        }

        return 'center';
    }

    /**
     * @param  array<int, array{id: string, side: string, x: string, y: string, width: string, height: string}>  $items
     * @return array{count: int, top: string, height: string, winner: string|null, items: array<int, array{id: string, side: string, x: string, y: string, width: string, height: string}>}
     */
    private static function sideSummary(array $items): array
    {
        $top = self::cssMax(array_map(fn (array $item): string => 'calc(' . $item['y'] . ' + ' . $item['height'] . ')', $items));
        $height = self::cssMax(array_map(fn (array $item): string => $item['height'], $items));

        return [
            'count' => count($items),
            'top' => $top,
            'height' => $height,
            'winner' => $items[0]['id'] ?? null,
            'items' => $items,
        ];
    }

    /**
     * @param  array<int, string>  $values
     */
    private static function cssMax(array $values): string
    {
        $values = array_values(array_filter($values, fn (string $value): bool => trim($value) !== ''));

        if ($values === []) {
            return '0rem';
        }

        if (count($values) === 1) {
            return $values[0];
        }

        return 'max(' . implode(', ', $values) . ')';
    }
}
