<?php

declare(strict_types=1);

namespace Gunreip\TranslationWorkbench\Support\TwGraph;

/** File provenance uses existing public-call regions, never layout dimensions. */
final class FileRegion
{
    public static function tokens(string $markup): array
    {
        preg_match_all('/<script[^>]*data-tw-graph-component-region[^>]*>(.*?)<\/script>/s', $markup, $matches);
        $tokens = [];
        foreach ($matches[1] as $json) {
            $region = json_decode($json, true, flags: JSON_THROW_ON_ERROR);
            $tokens[] = $region['token'];
        }
        // A nested file owns its calls; the parent only owns its remaining calls.
        preg_match_all('/data-tw-graph-file-tokens="([^"]*)"/', $markup, $nested);
        foreach ($nested[1] as $json) {
            $tokens = array_diff($tokens, json_decode(html_entity_decode($json, ENT_QUOTES), true, flags: JSON_THROW_ON_ERROR));
        }

        return array_values(array_unique($tokens));
    }
}
