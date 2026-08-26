<?php

declare(strict_types=1);

namespace Gunreip\TranslationWorkbench\Support\TwGraph\DataDriven\TimelineChainGraphData;

final class ValueNormalizer
{
    /**
     * @return array<int, int>
     */
    public static function integerList(mixed $value): array
    {
        return collect(is_array($value) ? $value : [])
            ->map(static fn(mixed $id): int => (int) $id)
            ->filter(static fn(int $id): bool => $id > 0)
            ->unique()
            ->values()
            ->all();
    }
}
