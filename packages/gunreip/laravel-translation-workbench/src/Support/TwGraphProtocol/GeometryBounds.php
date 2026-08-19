<?php

declare(strict_types=1);

namespace Gunreip\TranslationWorkbench\Support\TwGraphProtocol;

final class GeometryBounds
{
    /**
     * Builds CSS calc/min/max expressions for a set of protocol anchor points.
     *
     * @param  array<int, array{x?: string, y?: string}>  $points
     * @return array{minX: string, maxX: string, minY: string, maxY: string, left: string, bottom: string, width: string, height: string}
     */
    public static function fromPoints(array $points, string $padding = '0rem'): array
    {
        $xs = [];
        $ys = [];

        foreach ($points as $point) {
            if (is_string($point['x'] ?? null) && trim($point['x']) !== '') {
                $xs[] = $point['x'];
            }

            if (is_string($point['y'] ?? null) && trim($point['y']) !== '') {
                $ys[] = $point['y'];
            }
        }

        $minX = self::cssMin($xs);
        $maxX = self::cssMax($xs);
        $minY = self::cssMin($ys);
        $maxY = self::cssMax($ys);
        $doublePadding = self::cssAdd($padding, $padding);

        return [
            'minX' => $minX,
            'maxX' => $maxX,
            'minY' => $minY,
            'maxY' => $maxY,
            'left' => self::cssSubtract($minX, $padding),
            'bottom' => self::cssSubtract($minY, $padding),
            'width' => self::cssAdd(self::cssSubtract($maxX, $minX), $doublePadding),
            'height' => self::cssAdd(self::cssSubtract($maxY, $minY), $doublePadding),
        ];
    }

    /**
     * @param  array<int, string>  $values
     */
    private static function cssMin(array $values): string
    {
        return self::cssExtrema('min', $values);
    }

    /**
     * @param  array<int, string>  $values
     */
    private static function cssMax(array $values): string
    {
        return self::cssExtrema('max', $values);
    }

    /**
     * @param  array<int, string>  $values
     */
    private static function cssExtrema(string $function, array $values): string
    {
        $values = array_values(array_filter($values, fn (string $value): bool => trim($value) !== ''));

        if ($values === []) {
            return '0rem';
        }

        if (count($values) === 1) {
            return $values[0];
        }

        return $function . '(' . implode(', ', $values) . ')';
    }

    private static function cssAdd(string $value, string $delta): string
    {
        if ($delta === '0rem') {
            return $value;
        }

        if ($value === '0rem') {
            return $delta;
        }

        return 'calc(' . $value . ' + ' . $delta . ')';
    }

    private static function cssSubtract(string $value, string $delta): string
    {
        return self::cssAdd($value, self::cssNegate($delta));
    }

    private static function cssNegate(string $value): string
    {
        return 'calc(' . $value . ' * -1)';
    }
}
