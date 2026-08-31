<?php

declare(strict_types=1);

namespace Gunreip\TranslationWorkbench\Support\TwGraph;

final class Defaults
{
    public static function graph(string $key, mixed $fallback = null): mixed
    {
        $central = config('tw-graph-defaults.' . $key);

        if (filled($central)) {
            return $central;
        }

        return $fallback;
    }

    public static function graphString(string $key, string $fallback): string
    {
        $value = self::graph($key, $fallback);

        return filled($value) ? (string) $value : $fallback;
    }

    public static function graphRem(string $key, string $fallback): float
    {
        if (preg_match('/-?\d+(?:\.\d+)?/', self::graphString($key, $fallback), $matches) !== 1) {
            return 0.0;
        }

        return (float) $matches[0];
    }

    public static function dataDriven(string $key, mixed $fallback = null): mixed
    {
        $dataDriven = config('tw-graph-data-driven-defaults.' . $key);

        if (filled($dataDriven)) {
            return $dataDriven;
        }

        return self::graph($key, $fallback);
    }

    public static function dataDrivenString(string $key, string $fallback): string
    {
        $value = self::dataDriven($key, $fallback);

        return filled($value) ? (string) $value : $fallback;
    }

    public static function dataDrivenRem(string $key, string $fallback): float
    {
        if (preg_match('/-?\d+(?:\.\d+)?/', self::dataDrivenString($key, $fallback), $matches) !== 1) {
            return 0.0;
        }

        return (float) $matches[0];
    }


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

    public static function graphStringFor(mixed $local, mixed $inherited, string $key, string $fallback): string
    {
        return self::string($local, $inherited, self::graphString($key, $fallback));
    }

    public static function localOrGraphString(mixed $local, string $key, string $fallback): string
    {
        return filled($local) ? (string) $local : self::graphString($key, $fallback);
    }

    public static function localOrFallback(mixed $local, string $fallback): string
    {
        return filled($local) ? (string) $local : $fallback;
    }
}
