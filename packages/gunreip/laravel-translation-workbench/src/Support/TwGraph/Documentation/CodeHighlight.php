<?php

namespace Gunreip\TranslationWorkbench\Support\TwGraph\Documentation;

/** Adds orientation to already escaped code without changing its text or whitespace. */
final class CodeHighlight
{
    public static function render(string $escapedCode): string
    {
        if (preg_match('/^\s*&lt;\?php\b/', $escapedCode)) {
            return self::php($escapedCode);
        }

        return preg_replace_callback(
            '/\{\{--[\s\S]*?--\}\}|&lt;!--[\s\S]*?--&gt;|(?<!@)@php\b(?!\()[\s\S]*?@endphp\b|^[\t ]*&lt;\/?(?:x-|flux:)[^\r\n]*/m',
            static function (array $match): string {
                if (str_starts_with($match[0], '@php')) {
                    $body = substr($match[0], strlen('@php'), -strlen('@endphp'));

                    return '<span class="tw-graph-code-php">@php'.self::php($body, true).'@endphp</span>';
                }

                $class = match (true) {
                    str_starts_with($match[0], '{{--') => 'tw-graph-code-comment',
                    str_starts_with($match[0], '&lt;!--') => 'tw-graph-code-source-comment',
                    default => 'tw-graph-code-component',
                };

                return '<span class="'.$class.'">'.$match[0].'</span>';
            },
            $escapedCode,
        ) ?? $escapedCode;
    }

    private static function php(string $escapedCode, bool $fragment = false): string
    {
        $source = html_entity_decode($escapedCode, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        // PHP tokens distinguish comments from comment-like text inside strings and URLs.
        // No parsing flag: incomplete excerpts must remain displayable too.
        $tokens = \PhpToken::tokenize(($fragment ? '<?php ' : '').$source);
        if ($fragment) {
            array_shift($tokens);
        }

        $html = '';
        foreach ($tokens as $token) {
            $text = e($token->text);
            $html .= $token->is([T_COMMENT, T_DOC_COMMENT])
                ? '<span class="tw-graph-code-source-comment">'.$text.'</span>'
                : $text;
        }

        return $html;
    }
}
