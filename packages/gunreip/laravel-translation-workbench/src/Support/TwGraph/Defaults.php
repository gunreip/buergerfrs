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

    public static function graphBool(string $key, bool $fallback): bool
    {
        $config = app('config');
        $configKey = 'tw-graph-defaults.' . $key;
        $value = $config->has($configKey) ? $config->get($configKey) : $fallback;

        return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? $fallback;
    }

    public static function bool(mixed $value, bool $fallback = false): bool
    {
        if ($value === null) {
            return $fallback;
        }

        return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? (bool) $value;
    }

    public static function stepLabelContentGap(int $lineCount): string
    {
        return match (max(1, min(3, $lineCount))) {
            1 => self::graphString('step_label_content_gap_1_line', '1.75rem'),
            3 => self::graphString('step_label_content_gap_3_lines', '3.75rem'),
            default => self::graphString('step_label_content_gap_2_lines', '2.75rem'),
        };
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

    public static function dataDrivenBool(string $key, bool $fallback): bool
    {
        $config = app('config');
        $dataDrivenKey = 'tw-graph-data-driven-defaults.' . $key;

        if ($config->has($dataDrivenKey) && filled($config->get($dataDrivenKey))) {
            return filter_var($config->get($dataDrivenKey), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? $fallback;
        }

        return self::graphBool($key, $fallback);
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
