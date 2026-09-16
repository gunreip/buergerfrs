<?php

declare(strict_types=1);

namespace Gunreip\TranslationWorkbench\Support\TwGraph;

/** Resolves return-rail colors after all handmade graph components have rendered. */
final class ReturnColorRegistry
{
    private static array $rails = [];
    private static array $connections = [];

    public static function rail(string $graph, string $target, array $paths, array $outputs): void
    {
        self::$rails[$graph][ElementIdentifier::normalize($target)] = compact('paths', 'outputs');
    }

    public static function connect(string $graph, string $target, string $color): void
    {
        self::$connections[$graph][ElementIdentifier::normalize($target)] = $color;
    }

    public static function forgetGraph(string $graph): void
    {
        unset(self::$rails[$graph], self::$connections[$graph]);
    }

    /** @return array<string, string> Rendered path IDs mapped to their resolved colors. */
    public static function resolve(string $graph): array
    {
        $colors = [];
        foreach (self::$connections[$graph] ?? [] as $target => $color) {
            $rail = self::$rails[$graph][$target] ?? null;
            if ($rail === null) {
                throw new \InvalidArgumentException('Unknown IF return target: ' . $target);
            }
            foreach ($rail['paths'] as $path) {
                $colors[DevIdentifier::label($path)] = $color;
            }
            foreach ($rail['outputs'] as $output) {
                $anchor = AnchorRegistry::get($graph, $output);
                if ($anchor !== null) {
                    $anchor['returnColor'] = $color;
                    AnchorRegistry::put($graph, $output, $anchor);
                }
            }
        }
        self::forgetGraph($graph);

        return $colors;
    }

    public static function selectorValue(string $value): string
    {
        return '"' . str_replace(
            ['\\', '"', "\n", "\r", '<'],
            ['\\\\', '\\"', '\\a ', '\\d ', '\\3c '],
            $value,
        ) . '"';
    }
}
