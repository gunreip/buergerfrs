<?php

declare(strict_types=1);

namespace Tests\Support;

use PHPUnit\Framework\Assert;

/** Collect all observable prop mismatches before failing this dataset. */
final class PropContract
{
    private array $mismatches = [];

    public function __construct(private string $component) {}

    public function check(string $prop, mixed $expected, mixed $actual): void
    {
        if ($expected !== $actual) {
            $this->mismatches[] = sprintf(
                '%s | %s | expected %s; rendered %s',
                $this->component, $prop, json_encode($expected), json_encode($actual),
            );
        }
    }

    public function verify(): void
    {
        Assert::assertSame([], $this->mismatches, "Unexpected prop override(s):\n" . implode("\n", $this->mismatches));
    }
}
