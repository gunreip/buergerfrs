<?php

namespace Gunreip\TranslationWorkbench\Generation;

use RuntimeException;

/** A stored occurrence needs the regular scanner before source replacement. */
final class SourceScanRequired extends RuntimeException
{
    public function __construct(public readonly int $finding, public readonly string $path, public readonly int $sourceLine, public readonly int $sourceColumn)
    {
        parent::__construct('Stored source position is stale for finding #'.$finding.'. A new scan is required before source replacement.');
    }

    public function details(): array
    {
        return ['finding' => $this->finding, 'path' => $this->path, 'line' => $this->sourceLine, 'column' => $this->sourceColumn];
    }
}
