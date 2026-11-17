<?php

namespace Gunreip\TranslationWorkbench\Generation;

/** Rules for newly authored keys; existing captured keys are not rewritten. */
final class TranslationKeyInput
{
    private const PREFIX_SEGMENT = '[a-z0-9_-]+';

    public const PATTERN = '/^(?:'.self::PREFIX_SEGMENT.'\.){2,}[a-zA-Z0-9_-]+$/D';

    public static function canAppendSegment(string $draft): bool
    {
        return preg_match('/^'.self::PREFIX_SEGMENT.'(?:\.'.self::PREFIX_SEGMENT.')+\.?$/D', trim($draft)) === 1;
    }
}
