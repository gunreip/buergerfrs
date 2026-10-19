<?php

namespace Gunreip\TranslationWorkbench\Scanner;

/** Recognizes explicit owners with direct option variables or matching indexed fallbacks. */
class WorkbenchOptionCall
{
    public static function matches(string $code): array
    {
        $function = preg_quote('\\Gunreip\\TranslationWorkbench\\Foundation\\TranslationWorkbenchOption::label', '~');
        preg_match_all('~'.$function.'\s*\(\s*([\'"])(?<owner>[^\'"\r\n]+)\1\s*,\s*(?:\(string\)\s*)?\$(?<key>[A-Za-z_][A-Za-z0-9_]*)\s*,\s*\$(?<label>[A-Za-z_][A-Za-z0-9_]*)\s*\)~', $code, $matches, PREG_SET_ORDER);

        return $matches;
    }
    /** Direct array access only; the selected index must equal the call's option key. */
    public static function indexedMatches(string $code): array
    {
        $function = preg_quote('\\Gunreip\\TranslationWorkbench\\Foundation\\TranslationWorkbenchOption::label', '~');
        $pattern = '~'.$function.'\s*\(\s*([\'"])(?<owner>[^\'"\r\n]+)\1\s*,\s*(?:\(string\)\s*)?\$(?<key>[A-Za-z_][A-Za-z0-9_]*)\s*,\s*\$(?<options>[A-Za-z_][A-Za-z0-9_]*)\s*\[\s*\$\k<key>\s*\]\s*(?:\?\?\s*\$(?<fallback>[A-Za-z_][A-Za-z0-9_]*)\s*)?\)~';
        preg_match_all($pattern, $code, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);

        return array_map(static fn (array $match): array => [
            'owner' => $match['owner'][0], 'key' => $match['key'][0], 'options' => $match['options'][0],
            'offset' => $match[0][1], 'raw' => $match[0][0],
        ], $matches);
    }
}
