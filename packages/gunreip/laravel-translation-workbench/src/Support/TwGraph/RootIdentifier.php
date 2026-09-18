<?php

declare(strict_types=1);

namespace Gunreip\TranslationWorkbench\Support\TwGraph;

/** Render-scoped provenance; never used for anchor lookup or clipboard identifiers. */
final class RootIdentifier
{
    private static ?string $current = null;

    public static function enter(mixed $id): ?string
    {
        $previous = self::$current;

        // Internally composed strangs retain their authoring component's ID.
        if ($previous === null && is_string($id) && trim($id) !== '') {
            self::$current = $id;
        }

        return $previous;
    }

    public static function restore(?string $previous): void
    {
        self::$current = $previous;
    }

    public static function tooltipSuffix(): string
    {
        return self::$current === null ? '' : "\nRoot-ID: " . self::$current;
    }
}
