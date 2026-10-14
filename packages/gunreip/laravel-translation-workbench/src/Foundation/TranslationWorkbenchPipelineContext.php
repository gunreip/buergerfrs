<?php

namespace Gunreip\TranslationWorkbench\Foundation;

/** Initiation metadata, never a replacement for authentication or review authorship. */
class TranslationWorkbenchPipelineContext
{
    private ?array $current = null;

    public function current(): ?array
    {
        return $this->current;
    }

    public function replace(?array $context): void
    {
        $this->current = $context;
    }

    public function enrich(?array $context): ?array
    {
        return $this->current === null ? $context : [...($context ?? []), 'pipeline' => $this->current];
    }
}
