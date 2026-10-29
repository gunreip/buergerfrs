<?php

namespace Gunreip\TranslationWorkbench\Support;

use PhpParser\Error;
use PhpParser\Node;
use PhpParser\NodeFinder;
use PhpParser\ParserFactory;

/** Conservative static context check. Never compiles/evaluates application Blade directives. */
final class CopiedSourceContext
{
    private string $identity = '';

    private array $regions = [];

    public function check(string $path, string $bytes, int $offset): array
    {
        $identity = $path.':'.hash('sha256', $bytes);
        if ($identity !== $this->identity) {
            $this->regions = [];
            $this->identity = $identity;
            if (str_ends_with($path, '.blade.php')) {
                $this->blade($bytes);
            } else {
                $this->php($bytes, 0, 0, strlen($bytes), 'php_file');
            }
        }
        foreach ($this->regions as $region) {
            if ($offset < $region['start'] || $offset >= $region['end']) {
                continue;
            }
            if (isset($region['state'])) {
                return ['state' => $region['state'], 'context' => $region['context']];
            }
            if (isset($region['calls'][$offset])) {
                return ['state' => 'confirmed_call', 'context' => $region['context'], 'call' => $region['calls'][$offset]];
            }
            foreach ($region['excluded'] as $span) {
                if ($offset >= $span['start'] && $offset < $span['end']) {
                    return ['state' => $span['state'], 'context' => $region['context']];
                }
            }

            return ['state' => 'unresolved', 'context' => $region['context']];
        }

        return ['state' => 'unresolved', 'context' => 'outside_supported_code_region'];
    }

    private function php(string $php, int $shift, int $start, int $end, string $context): void
    {
        $excluded = [];
        $position = 0;
        foreach (token_get_all($php) as $token) {
            $text = is_array($token) ? $token[1] : $token;
            if (is_array($token) && in_array($token[0], [T_COMMENT, T_DOC_COMMENT, T_CONSTANT_ENCAPSED_STRING, T_ENCAPSED_AND_WHITESPACE], true)) {
                $excluded[] = ['start' => $position + $shift, 'end' => $position + $shift + strlen($text),
                    'state' => in_array($token[0], [T_COMMENT, T_DOC_COMMENT], true) ? 'excluded_comment' : 'excluded_string'];
            }
            $position += strlen($text);
        }
        $calls = [];
        try {
            $nodes = (new ParserFactory)->createForHostVersion()->parse($php) ?? [];
            foreach ((new NodeFinder)->find($nodes, fn (Node $node) => $node instanceof Node\Expr\FuncCall || $node instanceof Node\Expr\StaticCall) as $node) {
                if ($node instanceof Node\Expr\FuncCall && $node->name instanceof Node\Name) {
                    $calls[$node->getStartFilePos() + $shift] = $node->name->toString();
                } elseif ($node instanceof Node\Expr\StaticCall && $node->class instanceof Node\Name && $node->name instanceof Node\Identifier) {
                    $calls[$node->getStartFilePos() + $shift] = $node->class->toString().'::'.$node->name->toString();
                }
            }
        } catch (Error) {
            $context .= '_parse_failed';
        }
        $this->regions[] = compact('start', 'end', 'context', 'calls', 'excluded');
    }

    private function expression(string $bytes, int $start, int $end, string $context, ?int $directiveOffset = null): void
    {
        $code = substr($bytes, $start, $end - $start);
        // Wrapping as an array also supports comma-separated directive arguments.
        $prefix = '<?php [';
        $this->php($prefix.$code.'];', $start - strlen($prefix), $directiveOffset ?? $start, $end, $context);
        if ($directiveOffset !== null && ! str_ends_with($this->regions[array_key_last($this->regions)]['context'], '_parse_failed')) {
            $this->regions[array_key_last($this->regions)]['calls'][$directiveOffset] = $context;
        }
    }

    private function covered(int $offset): bool
    {
        foreach ($this->regions as $region) {
            if ($offset >= $region['start'] && $offset < $region['end']) {
                return true;
            }
        }

        return false;
    }

    /** End delimiter outside PHP strings/comments, or balanced closing parenthesis. */
    private function close(string $bytes, int $start, string $delimiter): ?int
    {
        $position = $start;
        $depth = 0;
        $tokens = token_get_all('<?php '.substr($bytes, $start));
        array_shift($tokens);
        foreach ($tokens as $token) {
            $text = is_array($token) ? $token[1] : $token;
            if (! is_array($token)) {
                if ($delimiter === ')') {
                    if ($token === '(') {
                        $depth++;
                    } elseif ($token === ')') {
                        if ($depth === 0) {
                            return $position;
                        }
                        $depth--;
                    }
                } elseif (substr($bytes, $position, strlen($delimiter)) === $delimiter) {
                    return $position;
                }
            }
            $position += strlen($text);
        }

        return null;
    }

    private function blade(string $bytes): void
    {
        // Read in source order so PHP strings inside a recognized region cannot open Blade regions.
        $pattern = '/<!--|\{\{--|(?<!@)@verbatim\b|<\?php\b|<\?=|(?<!@)@php\b|@?\{!!|@?\{\{|(?<!@)@[A-Za-z_][A-Za-z0-9_]*\s*\(|<(?:x-|flux:)[A-Za-z0-9_.:-]+\b/';
        preg_match_all($pattern, $bytes, $matches, PREG_OFFSET_CAPTURE);
        foreach ($matches[0] as [$opening, $offset]) {
            if ($this->covered($offset)) {
                continue;
            }
            if (in_array($opening, ['{{--', '<!--', '@verbatim'], true)) {
                $closing = match ($opening) {
                    '{{--' => '--}}', '<!--' => '-->', default => '@endverbatim'
                };
                $end = strpos($bytes, $closing, $offset + strlen($opening));
                $this->regions[] = ['start' => $offset, 'end' => $end === false ? strlen($bytes) : $end + strlen($closing),
                    'state' => $opening !== '@verbatim' ? 'excluded_comment' : 'excluded_verbatim', 'context' => 'blade_literal_region'];
            } elseif (str_starts_with($opening, '<?')) {
                // Tokenize native PHP to locate its closing tag without matching tags inside strings.
                $end = strlen($bytes);
                $position = $offset;
                foreach (token_get_all(substr($bytes, $offset)) as $token) {
                    $position += strlen(is_array($token) ? $token[1] : $token);
                    if (is_array($token) && $token[0] === T_CLOSE_TAG) {
                        $end = $position;
                        break;
                    }
                }
                $this->php(substr($bytes, $offset, $end - $offset), $offset, $offset, $end, 'blade_native_php');
            } elseif ($opening === '@php') {
                if (preg_match('/^\s*\(/', substr($bytes, $offset + 4))) {
                    $start = strpos($bytes, '(', $offset + 4) + 1;
                    $end = $this->close($bytes, $start, ')');
                    if ($end !== null) {
                        $this->expression($bytes, $start, $end, 'blade_inline_php');
                    }

                    continue;
                }
                $end = false;
                $position = $offset + 4;
                $tokens = token_get_all('<?php '.substr($bytes, $position));
                array_shift($tokens);
                foreach ($tokens as $token) {
                    if ($token === '@' && substr($bytes, $position, 7) === '@endphp') {
                        $end = $position;
                        break;
                    }
                    $position += strlen(is_array($token) ? $token[1] : $token);
                }
                if ($end !== false) {
                    $this->php('<?php '.substr($bytes, $offset + 4, $end - $offset - 4), $offset - 2, $offset, $end + 7, 'blade_php_block');
                }
            } elseif (str_contains($opening, '{{') || str_contains($opening, '{!!')) {
                $delimiter = str_contains($opening, '{!!') ? '!!}' : '}}';
                $start = $offset + strlen($opening);
                $end = $this->close($bytes, $start, $delimiter);
                if ($end !== null) {
                    if (str_starts_with($opening, '@')) {
                        $this->regions[] = ['start' => $offset, 'end' => $end + strlen($delimiter), 'state' => 'excluded_escaped_blade', 'context' => 'escaped_echo'];
                    } else {
                        $this->expression($bytes, $start, $end, 'blade_echo');
                    }
                }
            } elseif (str_starts_with($opening, '@')) {
                $name = trim(substr($opening, 1), " \t\r\n(");
                $start = $offset + strlen($opening);
                $end = $this->close($bytes, $start, ')');
                if ($end === null) {
                    continue;
                }
                $supported = ['lang', 'js', 'if', 'elseif', 'unless', 'switch', 'case', 'include', 'includeIf', 'includeWhen', 'includeUnless', 'props', 'aware', 'class', 'style', 'section', 'yield', 'checked', 'selected', 'disabled', 'readonly', 'required'];
                if (in_array($name, ['foreach', 'forelse', 'for', 'while'], true)) {
                    $prefix = '<?php '.($name === 'forelse' ? 'foreach' : $name).'(';
                    $this->php($prefix.substr($bytes, $start, $end - $start).') {}', $start - strlen($prefix), $offset, $end + 1, 'blade_loop_'.$name);
                } elseif (in_array($name, $supported, true)) {
                    $this->expression($bytes, $start, $end, 'blade_directive_'.$name, in_array($name, ['lang', 'js'], true) ? $offset : null);
                } else {
                    $this->regions[] = ['start' => $offset, 'end' => $end + 1, 'state' => 'unresolved', 'context' => 'unsupported_directive_'.$name];
                }
            } else {
                $position = $offset + strlen($opening);
                while (preg_match('/\G\s+/A', $bytes, $space, 0, $position)) {
                    $position += strlen($space[0]);
                    if (substr($bytes, $position, 4) === '{{--') {
                        $end = strpos($bytes, '--}}', $position + 4);
                        if ($end === false) {
                            break;
                        }
                        $position = $end + 4;

                        continue;
                    }
                    if (substr($bytes, $position, 2) === '{{') {
                        $end = $this->close($bytes, $position + 2, '}}');
                        if ($end === null) {
                            break;
                        }
                        $position = $end + 2;

                        continue;
                    }
                    if (preg_match('/\G@[A-Za-z]+\s*\(/A', $bytes, $directive, 0, $position)) {
                        $end = $this->close($bytes, $position + strlen($directive[0]), ')');
                        if ($end === null) {
                            break;
                        }
                        $position = $end + 1;

                        continue;
                    }
                    if (! preg_match('/\G([A-Za-z_:][A-Za-z0-9_.:-]*)(?:\s*=\s*(?:"([^"]*)"|\x27([^\x27]*)\x27|([^\s>]+)))?/A', $bytes, $attribute, PREG_OFFSET_CAPTURE | PREG_UNMATCHED_AS_NULL, $position)) {
                        break;
                    }
                    $position += strlen($attribute[0][0]);
                    $name = $attribute[1][0];
                    $value = $attribute[2][0] !== null ? $attribute[2] : $attribute[3];
                    if (str_starts_with($name, ':') && ! str_starts_with($name, '::') && $value[0] !== null) {
                        $this->expression($bytes, $value[1], $value[1] + strlen($value[0]), 'blade_bound_attribute');
                    }
                }
                // Ordinary attributes may still contain Blade echos, handled separately.
            }
        }
    }
}
