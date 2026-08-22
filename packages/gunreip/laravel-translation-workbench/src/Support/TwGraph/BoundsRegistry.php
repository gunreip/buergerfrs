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
        return self::canvasMetrics($graphId, $padding)['originBottom'];
    }

    public static function canvasHeight(string $graphId, string $padding = '2rem'): string
    {
        return self::canvasMetrics($graphId, $padding)['height'];
    }

    /**
     * @return array{minY: string, minYRem: float|null, maxY: string, maxYRem: float|null, originBottom: string, originBottomRem: float|null, height: string, heightRem: float|null}
     */
    public static function canvasMetrics(string $graphId, string $padding = '2rem'): array
    {
        $items = collect(self::$bounds[$graphId] ?? []);
        $bottoms = $items
            ->pluck('y')
            ->filter(fn (mixed $value): bool => is_string($value) && trim($value) !== '')
            ->values()
            ->all();
        $tops = $items
            ->map(fn (array $item): string => 'calc(' . $item['y'] . ' + ' . $item['height'] . ')')
            ->values()
            ->all();

        $minY = self::cssMin(['0rem', ...$bottoms]);
        $maxY = self::cssMax(['0rem', ...$tops]);
        $originBottom = 'calc((' . $minY . ') * -1 + ' . $padding . ')';
        $height = 'calc(' . $maxY . ' - ' . $minY . ' + (' . $padding . ' * 2))';

        return [
            'minY' => $minY,
            'minYRem' => self::evaluateRemExpression($minY),
            'maxY' => $maxY,
            'maxYRem' => self::evaluateRemExpression($maxY),
            'originBottom' => $originBottom,
            'originBottomRem' => self::evaluateRemExpression($originBottom),
            'height' => $height,
            'heightRem' => self::evaluateRemExpression($height),
        ];
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
     * @return array{count: int, top: string, topRem: float|null, height: string, heightRem: float|null, winner: string|null, items: array<int, array{id: string, side: string, x: string, y: string, width: string, height: string}>}
     */
    private static function sideSummary(array $items): array
    {
        $tops = array_map(fn (array $item): string => 'calc(' . $item['y'] . ' + ' . $item['height'] . ')', $items);
        $top = self::cssMax($tops);
        $height = self::cssMax(array_map(fn (array $item): string => $item['height'], $items));
        $topRem = self::maxRem($tops);
        $heightRem = self::maxRem(array_map(fn (array $item): string => $item['height'], $items));

        return [
            'count' => count($items),
            'top' => $top,
            'topRem' => $topRem,
            'height' => $height,
            'heightRem' => $heightRem,
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

    /**
     * @param  array<int, string>  $values
     */
    private static function cssMin(array $values): string
    {
        $values = array_values(array_filter($values, fn (string $value): bool => trim($value) !== ''));

        if ($values === []) {
            return '0rem';
        }

        if (count($values) === 1) {
            return $values[0];
        }

        return 'min(' . implode(', ', $values) . ')';
    }

    /**
     * @param  array<int, string>  $values
     */
    private static function maxRem(array $values): ?float
    {
        $resolved = array_values(array_filter(
            array_map(fn (string $value): ?float => self::evaluateRemExpression($value), $values),
            fn (?float $value): bool => $value !== null,
        ));

        return $resolved === [] ? null : max($resolved);
    }

    private static function evaluateRemExpression(string $expression): ?float
    {
        $expression = trim($expression);

        if ($expression === '' || str_contains($expression, 'var(')) {
            return null;
        }

        if (str_starts_with($expression, 'max(') && str_ends_with($expression, ')')) {
            return self::maxRem(self::splitTopLevel(substr($expression, 4, -1)));
        }

        if (str_starts_with($expression, 'min(') && str_ends_with($expression, ')')) {
            return self::minRem(self::splitTopLevel(substr($expression, 4, -1)));
        }

        $resolvedExpression = self::resolveCssFunctions($expression);
        if ($resolvedExpression === null) {
            return null;
        }

        $arithmetic = str_replace(['calc(', 'rem'], ['(', ''], $resolvedExpression);

        if (! preg_match('/^[0-9+*\/().\s-]+$/', $arithmetic)) {
            return null;
        }

        try {
            /** @var int|float $result */
            $result = eval('return ' . $arithmetic . ';');
        } catch (\Throwable) {
            return null;
        }

        return is_numeric($result) ? round((float) $result, 3) : null;
    }

    /**
     * @param  array<int, string>  $values
     */
    private static function minRem(array $values): ?float
    {
        $resolved = array_values(array_filter(
            array_map(fn (string $value): ?float => self::evaluateRemExpression($value), $values),
            fn (?float $value): bool => $value !== null,
        ));

        return $resolved === [] ? null : min($resolved);
    }

    private static function resolveCssFunctions(string $expression): ?string
    {
        $resolved = $expression;

        while (preg_match('/\\b(min|max)\\(/', $resolved, $match, PREG_OFFSET_CAPTURE)) {
            $function = $match[1][0];
            $start = (int) $match[0][1];
            $open = $start + strlen($function);
            $close = self::findClosingParenthesis($resolved, $open);

            if ($close === null) {
                return null;
            }

            $arguments = self::splitTopLevel(substr($resolved, $open + 1, $close - $open - 1));
            $value = $function === 'max' ? self::maxRem($arguments) : self::minRem($arguments);

            if ($value === null) {
                return null;
            }

            $resolved = substr($resolved, 0, $start) . $value . 'rem' . substr($resolved, $close + 1);
        }

        return $resolved;
    }

    private static function findClosingParenthesis(string $value, int $openPosition): ?int
    {
        $depth = 0;
        $length = strlen($value);

        for ($index = $openPosition; $index < $length; $index++) {
            if ($value[$index] === '(') {
                $depth++;
            }

            if ($value[$index] === ')') {
                $depth--;

                if ($depth === 0) {
                    return $index;
                }
            }
        }

        return null;
    }

    /**
     * @return array<int, string>
     */
    private static function splitTopLevel(string $value): array
    {
        $parts = [];
        $depth = 0;
        $current = '';

        foreach (str_split($value) as $character) {
            if ($character === '(') {
                $depth++;
            }

            if ($character === ')') {
                $depth--;
            }

            if ($character === ',' && $depth === 0) {
                $parts[] = trim($current);
                $current = '';

                continue;
            }

            $current .= $character;
        }

        if (trim($current) !== '') {
            $parts[] = trim($current);
        }

        return $parts;
    }
}
