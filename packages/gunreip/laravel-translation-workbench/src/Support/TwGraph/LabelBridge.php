<?php

declare(strict_types=1);

namespace Gunreip\TranslationWorkbench\Support\TwGraph;

final class LabelBridge
{
    public static function labelWidth(array $label, mixed $override = null): string
    {
        if (filled($override)) {
            return self::widthValue($override);
        }

        return match (true) {
            data_get($label, 'width') === 'long' || (bool) data_get($label, 'long', false) => Defaults::graphString('label_width.long', '24rem'),
            in_array(data_get($label, 'width'), ['halfLong', 'half-long', 'half_long'], true) || (bool) data_get($label, 'halfLong', false) => Defaults::graphString('label_width.half_long', '18rem'),
            in_array(data_get($label, 'width'), ['half', 'halfWidth', 'half-width', 'half_width'], true) || (bool) data_get($label, 'half', false) => Defaults::graphString('label_width.half', '6rem'),
            default => Defaults::graphString('label_width.default', '12rem'),
        };
    }

    public static function widthValue(mixed $width): string
    {
        return match (true) {
            $width === 'long' => Defaults::graphString('label_width.long', '24rem'),
            in_array($width, ['halfLong', 'half-long', 'half_long'], true) => Defaults::graphString('label_width.half_long', '18rem'),
            in_array($width, ['half', 'halfWidth', 'half-width', 'half_width'], true) => Defaults::graphString('label_width.half', '6rem'),
            $width === 'default' || blank($width) => Defaults::graphString('label_width.default', '12rem'),
            default => (string) $width,
        };
    }

    public static function bridgeLength(mixed $length, float $minimum = 0.25, float $minimumRenderLength = 1.15, ?float $maximum = null): string
    {
        $value = Defaults::string($length, null, '0.75rem');
        $maximum ??= max($minimum, Defaults::graphRem('label_width.long', '24rem') / 2);

        if (preg_match('/^\s*(-?\d+(?:\.\d+)?)rem\s*$/', $value, $matches) !== 1) {
            return $value;
        }

        $numericValue = (float) $matches[1];
        $clampedValue = min(max($numericValue, $minimum), $maximum);

        if ($clampedValue > $minimum && $clampedValue < $minimumRenderLength) {
            $clampedValue = min($minimumRenderLength, $maximum);
        }

        return rtrim(rtrim(number_format($clampedValue, 2, '.', ''), '0'), '.') . 'rem';
    }

    /**
     * @return array{
     *     bridgeInEnd: array{x: string, y: string},
     *     bridgeOutStart: array{x: string, y: string},
     *     anchorEnd: array{x: string, y: string},
     *     labelAnchor: array{x: string, y: string},
     *     spanLength: string,
     *     spanHalfLength: string
     * }
     */
    public static function geometry(array $anchorStart, string $direction, string $labelWidth, string $bridgeLength): array
    {
        $x = (string) data_get($anchorStart, 'x', '0rem');
        $y = (string) data_get($anchorStart, 'y', '0rem');
        $spanLength = 'calc(' . $labelWidth . ' + (' . $bridgeLength . ' * 2))';
        $spanHalfLength = 'calc(' . $spanLength . ' / 2)';
        $add = static fn (string $value, string $delta): string => $delta === '0rem' ? $value : 'calc(' . $value . ' + ' . $delta . ')';
        $neg = static fn (string $value): string => 'calc(' . $value . ' * -1)';
        $isRightLeft = $direction === 'right-left';
        $signedBridgeLength = $isRightLeft ? $neg($bridgeLength) : $bridgeLength;
        $signedSpanLength = $isRightLeft ? $neg($spanLength) : $spanLength;
        $signedSpanHalfLength = $isRightLeft ? $neg($spanHalfLength) : $spanHalfLength;
        $anchorEnd = [
            'x' => $add($x, $signedSpanLength),
            'y' => $y,
        ];

        return [
            'bridgeInEnd' => [
                'x' => $add($x, $signedBridgeLength),
                'y' => $y,
            ],
            'bridgeOutStart' => [
                'x' => $add($anchorEnd['x'], $isRightLeft ? $bridgeLength : $neg($bridgeLength)),
                'y' => $y,
            ],
            'anchorEnd' => $anchorEnd,
            'labelAnchor' => [
                'x' => $add($x, $signedSpanHalfLength),
                'y' => $y,
            ],
            'spanLength' => $spanLength,
            'spanHalfLength' => $spanHalfLength,
        ];
    }
}
