<?php

namespace Gunreip\TranslationWorkbench\Generation;

/** Rules for newly authored keys; existing captured keys are not rewritten. */
final class TranslationKeyInput
{
    public const PATTERN = '/^(?:[a-z0-9_-]+\.){2,}[a-zA-Z0-9_-]+$/D';
}
