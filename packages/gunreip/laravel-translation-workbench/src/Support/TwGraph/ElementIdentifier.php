<?php

declare(strict_types=1);

namespace Gunreip\TranslationWorkbench\Support\TwGraph;

final class ElementIdentifier
{
    /**
     * Normalize render/component ids into stable element ids for debug bounds,
     * collision reports and correction targets.
     */
    public static function normalize(mixed $id): string
    {
        $id = trim((string) $id);

        if ($id === '') {
            return '';
        }

        $id = self::stripGraphPrefix($id);
        $id = self::normalizeStrangKindAndSide($id);
        $id = self::normalizeChapterNumbers($id);
        $id = self::removeRedundantPathLevel($id);
        $id = self::normalizeElements($id);

        return trim($id, '.');
    }

    public static function equals(mixed $left, mixed $right): bool
    {
        return self::normalize($left) === self::normalize($right);
    }

    public static function startsWith(mixed $id, mixed $prefix): bool
    {
        $id = self::normalize($id);
        $prefix = self::normalize($prefix);

        return $id === $prefix || str_starts_with($id, $prefix . '.');
    }

    private static function stripGraphPrefix(string $id): string
    {
        $position = strpos($id, 'strang.');

        return $position === false ? $id : substr($id, $position);
    }

    private static function normalizeStrangKindAndSide(string $id): string
    {
        return preg_replace(
            '/^strang\.([a-z]+(?:-[a-z]+)*)-(left|right)(\.|$)/',
            'strang.$1.$2$3',
            $id,
        ) ?? $id;
    }

    private static function normalizeChapterNumbers(string $id): string
    {
        $id = preg_replace('/\.extension(\d+)(\.|$)/', '.extension.$1$2', $id) ?? $id;
        $id = preg_replace('/\.return(\d+)(\.|$)/', '.return.$1$2', $id) ?? $id;

        return preg_replace('/\.branch-return\.(\d+)(\.|$)/', '.return.$1$2', $id) ?? $id;
    }

    private static function removeRedundantPathLevel(string $id): string
    {
        return preg_replace(
            '/\.(main|extension\.\d+|return\.\d+|start|end)\.path\.(trunk|merge|merge-extension|branch|branch-extension|branch-return|branch-return-extension|rekey-source|rekey-target|rekey-target-end|branch-end)(\.|$)/',
            '.$1$3',
            $id,
        ) ?? $id;
    }

    private static function normalizeElements(string $id): string
    {
        $id = str_replace('.start-stem', '.start.stem', $id);
        $id = str_replace('.label-bounds', '.bounds.label', $id);
        $id = str_replace('.stem-labels', '.stem.labels', $id);
        $id = str_replace('.devBox', '.dev-box', $id);

        $id = preg_replace('/\.path(\d+)(\.|$)/', '.stem$1$2', $id) ?? $id;
        $id = preg_replace('/\.path\.(\d+)(\.|$)/', '.stem$1$2', $id) ?? $id;

        return $id;
    }
}
