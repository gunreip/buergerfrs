<?php

namespace Gunreip\TranslationWorkbench\Generation;

final class FindingSearch
{
    /** SQL operators are fixed; the search value is always bound as a parameter. */
    public static function condition(string $search, bool $caseSensitive = false, bool $wholeWord = false): array
    {
        $search = trim($search);
        if ($wholeWord) {
            $literal = strtr($search, array_combine(
                str_split('\\.^$*+?()[]{}|'),
                array_map(fn (string $character) => '\\'.$character, str_split('\\.^$*+?()[]{}|')),
            ));

            return [$caseSensitive ? '~' : '~*', '(?<![[:alnum:]])'.$literal.'(?![[:alnum:]])'];
        }

        return [$caseSensitive ? 'LIKE' : 'ILIKE', '%'.str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $search).'%'];
    }
}
