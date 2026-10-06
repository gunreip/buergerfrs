<?php

declare(strict_types=1);

namespace Gunreip\TranslationWorkbench\Support\TwGraph;

/** One sequence per canvas; allocation occurs only for rendered diagnostic badges. */
final class DevNodeCounters
{
    private int $next = 1;

    public function __construct(private readonly bool $automatic = false) {}

    public function label(mixed $authored): mixed
    {
        return $this->automatic ? $this->next++ : $authored;
    }
}
