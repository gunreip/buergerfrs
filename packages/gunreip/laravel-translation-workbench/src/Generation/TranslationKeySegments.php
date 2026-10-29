<?php

namespace Gunreip\TranslationWorkbench\Generation;

use InvalidArgumentException;

/** Navigation for assigned keys. Never generates or modifies a SuggestedKey. */
final class TranslationKeySegments
{
    public static function configured(): self
    {
        return new self(config('translation-workbench.allowed_first_translation_key_segments', []));
    }

    public function assertAllowed(string $key): void
    {
        if (!$this->allows($key)) {
            throw new \RuntimeException(__('Choose a configured first key segment (:segments) and at least two further segments. Uppercase letters are allowed only in the final segment.', ['segments' => implode(', ', $this->allowedFirstSegments)]));
        }
    }

    public function __construct(private array $allowedFirstSegments)
    {
        foreach ($allowedFirstSegments as $segment) {
            if (!is_string($segment) || preg_match('/^[a-z0-9_-]+$/D', $segment) !== 1) {
                throw new InvalidArgumentException('Invalid configured first translation-key segment.');
            }
        }
        $this->allowedFirstSegments = array_values(array_unique($allowedFirstSegments));
    }

    public function allows(string $key): bool
    {
        return preg_match(TranslationKeyInput::PATTERN, $key) === 1
            && in_array(explode('.', $key, 2)[0], $this->allowedFirstSegments, true);
    }

    /** Only immediate child segments are offered; configured roots need no stored key. */
    public function options(array $keys, string $prefix = ''): array
    {
        if ($prefix === '') {
            return $this->allowedFirstSegments;
        }
        if (!in_array(explode('.', $prefix, 2)[0], $this->allowedFirstSegments, true)) {
            return [];
        }
        $children = [];
        foreach ($keys as $key) {
            if (($key['namespace'] ?? '') !== '' || !str_starts_with($key['full_name'], $prefix.'.')) {
                continue;
            }
            $segment = explode('.', substr($key['full_name'], strlen($prefix) + 1), 2)[0];
            if ($segment !== '') {
                $children[] = $segment;
            }
        }
        $children = array_values(array_unique($children));
        sort($children, SORT_STRING);

        return $children;
    }
}
