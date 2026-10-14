<?php

namespace Gunreip\TranslationWorkbench\Foundation;

/** Stable, context-specific option keys; rendering never reads Workbench tables. */
class TranslationWorkbenchOption
{
    public static function key(string $key, string $value): string
    {
        if (! preg_match('/^[a-zA-Z0-9_-]+(?:\.[a-zA-Z0-9_-]+)+$/D', $key) || $value === '') {
            throw new \InvalidArgumentException('An option requires a dotted translation key and a non-empty value.');
        }

        // A value is one array segment, including dots, percent signs and Unicode.
        return $key.'.options.'.str_replace('.', '%2E', rawurlencode($value));
    }

    public static function label(string $key, string $value, string $fallback, ?string $locale = null): string
    {
        if ($value === '') {
            return $fallback;
        }
        $translationKey = self::key($key, $value);
        $translated = app('translator')->get($translationKey, [], $locale);

        return is_string($translated) && $translated !== $translationKey ? $translated : $fallback;
    }
}
