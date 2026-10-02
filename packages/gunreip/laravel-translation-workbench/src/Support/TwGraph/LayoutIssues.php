<?php

namespace Gunreip\TranslationWorkbench\Support\TwGraph;

/** Per-canvas layout failures; never changes authored geometry. */
final class LayoutIssues
{
    private array $issues = [];

    public function add(string $component, string $id, string $property, ?float $actual, string $expected): void
    {
        $this->issues[] = compact('component', 'id', 'property', 'actual', 'expected');
    }

    public function all(): array
    {
        return $this->issues;
    }
}
