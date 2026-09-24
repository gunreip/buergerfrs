<?php

namespace Gunreip\TranslationWorkbench\Support;

use InvalidArgumentException;

final class HeadingCounter
{
    private array $examples = [];

    public function __construct(public readonly string $group)
    {
        if (trim($group) === '') {
            throw new InvalidArgumentException('A heading counter group must not be empty.');
        }
    }

    public function number(string $example): int
    {
        if (trim($example) === '') {
            throw new InvalidArgumentException('A heading counter example key must not be empty.');
        }

        return $this->examples[$example] ??= count($this->examples) + 1;
    }
}
