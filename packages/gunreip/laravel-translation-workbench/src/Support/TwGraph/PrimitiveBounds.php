<?php

declare(strict_types=1);

namespace Gunreip\TranslationWorkbench\Support\TwGraph;

/** Bounds belong to the drawing primitive, not to selected callers or DEV boxes. */
final class PrimitiveBounds
{
    private const PATH = 'var(--tw-graph-protocol-path-width)';

    private const NODE = 'var(--tw-graph-protocol-node-size)';

    private const ARC = 'var(--tw-graph-protocol-arc-radius)';

    private static function calc(string $value): string
    {
        return 'calc('.$value.')';
    }

    private static function rect(string $x, string $y, string $width, string $height): array
    {
        return compact('x', 'y', 'width', 'height');
    }

    private static function centered(string $x, string $y, string $width, ?string $height = null): array
    {
        $height ??= $width;

        return self::rect(self::calc("$x - ($width / 2)"), self::calc("$y - ($height / 2)"), $width, $height);
    }

    public static function node(string $id, string $x, string $y, ?string $size = null): array
    {
        return self::record($id, 'geometry', [self::centered($x, $y, $size ?: self::NODE)]);
    }

    public static function arrow(string $id, string $x, string $y, string $direction): array
    {
        $short = self::calc(self::NODE.' * 0.6');

        return self::record($id, 'geometry', [self::centered($x, $y,
            in_array($direction, ['left', 'right'], true) ? $short : self::NODE,
            in_array($direction, ['left', 'right'], true) ? self::NODE : $short)]);
    }

    public static function line(string $id, string $direction, string $x, string $y, string $endX, string $endY,
        string $length, bool $nodeStart, bool $nodeEnd, ?string $nodeStartSize, ?string $nodeEndSize,
        bool $capStart, bool $capEnd, string $capLength): array
    {
        $horizontal = in_array($direction, ['left-right', 'right-left'], true);
        $half = '('.self::PATH.' / 2)';
        $left = $horizontal ? ($direction === 'right-left' ? $endX : $x) : self::calc("$x - $half");
        $bottom = $horizontal ? self::calc("$y - $half") : ($direction === 'top-bottom' ? $endY : $y);
        $rects = [self::rect($left, $bottom, $horizontal ? $length : self::PATH, $horizontal ? self::PATH : $length)];
        // Endpoints are the rendered line ends, including local length overrides.
        $lowX = $horizontal ? $left : $x;
        $lowY = $horizontal ? $y : $bottom;
        $highX = $horizontal ? self::calc("$left + $length") : $x;
        $highY = $horizontal ? $y : self::calc("$bottom + $length");
        $reverse = in_array($direction, ['right-left', 'top-bottom'], true);
        foreach ([[true, $nodeStart, $nodeStartSize, $capStart], [false, $nodeEnd, $nodeEndSize, $capEnd]] as [$start, $node, $size, $cap]) {
            if (! $node && ! $cap) {
                continue;
            }
            $low = $start !== $reverse;
            $width = $node ? ($size ?: self::NODE) : ($horizontal ? self::PATH : $capLength);
            $height = $node ? ($size ?: self::NODE) : ($horizontal ? $capLength : self::PATH);
            $rects[] = self::centered($low ? $lowX : $highX, $low ? $lowY : $highY, $width, $height);
        }

        return self::record($id, 'geometry', $rects);
    }

    public static function arc(string $id, string $corner, string $startX, string $startY, string $endX, string $endY, ?string $size): array
    {
        $size = $size ?: self::ARC;
        $half = '('.self::PATH.' / 2)';
        $x = match ($corner) {
            'se' => self::calc("$startX - $size + $half"),
            'sw' => self::calc("$startX - $half"),
            'nw' => self::calc("$endX - $half"),
            default => self::calc("$endX - $size + $half"),
        };
        $y = in_array($corner, ['se', 'sw'], true)
            ? self::calc("$endY - $half") : self::calc("$startY - $size + $half");

        return self::record($id, 'geometry', [self::rect($x, $y, $size, $size)]);
    }

    public static function connector(string $id, string $side, string $x, string $y, string $length, ?string $gap): array
    {
        $gap = $gap ?: '0.25rem';
        $reach = '('.self::NODE." / 2 + $gap)";
        $width = self::calc(self::PATH.' / 2');
        $half = '('.self::PATH.' / 4)';

        return self::record($id, 'geometry', [match ($side) {
            'left' => self::rect(self::calc("$x - $reach - $length"), self::calc("$y - $half"), $length, $width),
            'top' => self::rect(self::calc("$x - $half"), self::calc("$y + $reach"), $width, $length),
            'bottom' => self::rect(self::calc("$x - $half"), self::calc("$y - $reach - $length"), $width, $length),
            default => self::rect(self::calc("$x + $reach"), self::calc("$y - $half"), $length, $width),
        }]);
    }

    public static function text(string $id, string $x, string $y, string $side, string $offset, bool $badge, bool $long, bool $halfLong, bool $half, int $lines): array
    {
        // Explicit provisional text box only; actual font/wrapping dimensions are resolved in the browser.
        $width = (($long ? 24 : ($halfLong ? 18 : ($half ? 6 : 12))) + ($badge ? 1 : 0)).'rem';
        $height = (1.75 + max(0, $lines - 1) * 1.25).'rem';
        $left = match ($side) {
            'left' => self::calc("$x - $offset - $width"),
            'top', 'bottom', 'center' => self::calc("$x - ($width / 2)"),
            default => self::calc("$x + $offset"),
        };
        $bottom = match ($side) {
            'top' => self::calc("$y + $offset"),
            'bottom' => self::calc("$y - $offset - $height"),
            default => self::calc("$y - ($height / 2)"),
        };

        return self::record($id, 'text', [self::rect($left, $bottom, $width, $height)]);
    }

    public static function jump(string $id, string $x, string $y, string $radius, ?string $width): array
    {
        $width = $width ?: self::PATH;
        $size = self::calc("$radius * 2 + $width");

        return self::record($id, 'geometry', [self::centered($x, $y, $size)]);
    }

    private static function record(string $id, string $kind, array $rects): array
    {
        return ['id' => $id, 'kind' => $kind, 'rects' => $rects];
    }
}
