<?php

declare(strict_types=1);

namespace Gunreip\TranslationWorkbench\Support\TwGraph;

/** Shared default and anchored geometry for segments.stem-compressed. */
final class CompressedStemLayout
{
    public static function defaultLength(): string
    {
        return Defaults::graphString('compressed_stem_length', '8rem');
    }

    public static function sections(?string $length = null): array
    {
        $length ??= self::defaultLength();
        $rem = BoundsRegistry::evaluateRemExpression($length);
        $part = static fn (int $divisor): string => $rem === null
            ? 'calc('.$length.' / '.$divisor.')'
            : ($rem / $divisor).'rem';

        return ['beforeLength' => $part(4), 'gapLength' => $part(2), 'afterLength' => $part(4)];
    }
}
