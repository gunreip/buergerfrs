<?php

namespace Gunreip\TranslationWorkbench\Support\TwGraph\Documentation;

/** Adds orientation to already escaped code without changing its text or whitespace. */
final class CodeHighlight
{
    public static function render(string $escapedCode): string
    {
        return preg_replace_callback(
            '/\{\{--[\s\S]*?--\}\}|&lt;!--[\s\S]*?--&gt;|^[\t ]*&lt;\/?(?:x-|flux:)[^\r\n]*/m',
            static function (array $match): string {
                $isComment = str_starts_with($match[0], '{{--') || str_starts_with($match[0], '&lt;!--');
                $class = $isComment ? 'tw-graph-code-comment' : 'tw-graph-code-component';

                return '<span class="'.$class.'">'.$match[0].'</span>';
            },
            $escapedCode,
        ) ?? $escapedCode;
    }
}
