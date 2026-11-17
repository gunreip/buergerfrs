<?php

namespace Gunreip\TranslationWorkbench\Support;

use Gunreip\TranslationWorkbench\Generation\SourceLocationUnavailable;
use Gunreip\TranslationWorkbench\Generation\SourceReplacementPreview;

final class FindingUsage
{
    public static function forFinding(array $finding, string $root): ?string
    {
        $expression = $finding['raw_expression'] ?? '';
        if (($finding['status'] ?? '') !== 'active' || $expression === '' || !str_ends_with($finding['path'], '.blade.php')) return null;
        try {
            $path = (new SourceReplacementPreview)->path($root, $finding['path']);
        } catch (SourceLocationUnavailable) {
            return null;
        }
        $lines = @file($path);
        $line = (int) $finding['source_line'] - 1;
        $column = (int) $finding['source_column'] - 1;
        if ($lines === false || !isset($lines[$line]) || $column < 0 || $column >= strlen($lines[$line])) return null;
        $offset = strlen(implode('', array_slice($lines, 0, $line))) + $column;
        $source = implode('', $lines);
        if (substr($source, $offset, strlen($expression)) !== $expression) return null;

        // Keep track of enclosing controls, skipping comments and markup inside Blade echoes.
        $pattern = '~\{\{--.*?--\}\}|<!--.*?-->|\{\{.*?\}\}|\{!!.*?!!\}|</?([a-z][\w.:-]*)(?=[\s/>])(?:[^\x22\x27<>]++|\x22[^\x22]*\x22|\x27[^\x27]*\x27)*>~is';
        preg_match_all($pattern, $source, $tokens, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
        $controls = [];
        foreach ($tokens as $token) {
            [$markup, $start] = $token[0];
            if ($start > $offset) break;
            $inside = $offset < $start + strlen($markup);
            if (!isset($token[1])) {
                if ($inside && (str_starts_with($markup, '{{--') || str_starts_with($markup, '<!--'))) return null;
                continue;
            }
            $tag = strtolower($token[1][0]);
            $usage = match ($tag) {
                'button', 'flux:button' => 'Button',
                'th', 'flux:table.column' => 'Table header',
                'flux:tooltip.content', 'x-translation-workbench::ui.common.tooltip-content',
                'x-translation-workbench::ui.common.tooltip-content-tabular' => 'Tooltip',
                default => null,
            };
            if ($inside) {
                $prefix = substr($markup, 0, $offset - $start);
                if (preg_match('~\s:?(?:tooltip|title)\s*=\s*(?:"[^"]*|\x27[^\x27]*)$~i', $prefix)) return 'Tooltip';
                if (in_array($tag, ['x-translation-workbench::ui.common.text-preview', 'x-translation-workbench::ui.common.path-preview'], true)
                    && preg_match('~\s:?heading\s*=\s*(?:"[^"]*|\x27[^\x27]*)$~i', $prefix)) return 'Tooltip';
                return $usage;
            }
            if ($usage === null) continue;
            if (str_starts_with($markup, '</')) {
                if (!$controls || end($controls)['tag'] !== $tag) return null;
                array_pop($controls);
            } elseif (!str_ends_with($markup, '/>')) {
                $controls[] = ['tag' => $tag, 'usage' => $usage];
            }
        }
        return $controls ? end($controls)['usage'] : null;
    }
}
