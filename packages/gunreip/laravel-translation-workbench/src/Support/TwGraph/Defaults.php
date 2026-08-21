<?php

declare(strict_types=1);

namespace Gunreip\TranslationWorkbench\Support\TwGraph;

final class Defaults
{
    public static function string(mixed $local, mixed $inherited, string $fallback): string
    {
        if (filled($local)) {
            return (string) $local;
        }

        if (filled($inherited)) {
            return (string) $inherited;
        }

        return $fallback;
    }

    public static function localOrFallback(mixed $local, string $fallback): string
    {
        return filled($local) ? (string) $local : $fallback;
    }
}
