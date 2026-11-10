<?php

namespace Gunreip\TranslationWorkbench\Generation;

use RuntimeException;

final class PublicationFileReserved extends RuntimeException
{
    public function __construct(public readonly string $runId, public readonly string $relativePath)
    {
        parent::__construct('File reserved by publication '.$runId.'. Open that run first.');
    }
}
