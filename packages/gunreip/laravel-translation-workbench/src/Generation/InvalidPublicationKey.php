<?php

namespace Gunreip\TranslationWorkbench\Generation;

use RuntimeException;

final class InvalidPublicationKey extends RuntimeException
{
    public function __construct(public readonly string $heading, public readonly string $detail, public readonly int $keyId)
    {
        parent::__construct($heading.'. '.$detail);
    }
}
