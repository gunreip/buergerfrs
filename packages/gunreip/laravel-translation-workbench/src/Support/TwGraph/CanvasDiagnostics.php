<?php

namespace Gunreip\TranslationWorkbench\Support\TwGraph;

use Illuminate\View\Factory;

final readonly class CanvasDiagnostics
{
    public bool $dev;

    public bool $coordinates;

    public function __construct(mixed $dev = false, mixed $coordinates = false)
    {
        $this->dev = Defaults::bool($dev);
        $this->coordinates = $this->dev && Defaults::bool($coordinates);
    }

    public static function current(Factory $view): self
    {
        return $view->getConsumableComponentData('twGraphDiagnostics') ?? new self;
    }
}
