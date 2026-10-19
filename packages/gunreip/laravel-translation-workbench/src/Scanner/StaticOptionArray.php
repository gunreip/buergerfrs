<?php

namespace Gunreip\TranslationWorkbench\Scanner;

use Illuminate\Support\Facades\File;

/** Restricted literal parser. Never evaluates PHP, autoloads classes or invokes providers. */
class StaticOptionArray
{
    public function read(string $body, string $ownerCode): array
    {
        $tokens = $this->tokens($body);
        $options = [];
        $index = 0;
        while ($index < count($tokens)) {
            $token = $tokens[$index++] ?? null;
            $key = $this->literal($token);
            if ($key === null && is_array($token) && in_array($token[0], [T_STRING, T_NAME_QUALIFIED, T_NAME_FULLY_QUALIFIED], true)) {
                $separator = $tokens[$index++] ?? null;
                $constant = $tokens[$index++] ?? null;
                if (! is_array($separator) || $separator[0] !== T_DOUBLE_COLON || ! is_array($constant) || $constant[0] !== T_STRING) {
                    return [];
                }
                $key = $this->constant($token[1], $constant[1], $ownerCode);
            }
            $arrow = $tokens[$index++] ?? null;
            $value = $this->literal($tokens[$index++] ?? null);
            if ($key === null || trim($key) === '' || $value === null || trim($value) === '' || ! is_array($arrow) || $arrow[0] !== T_DOUBLE_ARROW) {
                return [];
            }
            $options[$key] = $value;
            if ($index < count($tokens) && $tokens[$index++] !== ',') {
                return [];
            }
        }
        return $options;
    }

    private function tokens(string $code): array
    {
        return array_values(array_filter(token_get_all('<?php '.$code), static fn ($token) => ! is_array($token)
            || ! in_array($token[0], [T_OPEN_TAG, T_WHITESPACE, T_COMMENT, T_DOC_COMMENT], true)));
    }

    private function literal(mixed $token): ?string
    {
        if (! is_array($token) || $token[0] !== T_CONSTANT_ENCAPSED_STRING) {
            return null;
        }
        $quoted = $token[1];
        $value = substr($quoted, 1, -1);
        if ($quoted[0] === "'") {
            return str_replace(["\\\\", "\\'"], ["\\", "'"], $value);
        }
        // Unsupported escapes must not silently change a translation's raw text.
        if (str_contains($value, '\\u{')) {
            return null;
        }
        return preg_replace_callback('/\\\\(?:[nrtvef\\\\"$]|[0-7]{1,3}|x[0-9a-fA-F]{1,2})/',
            static fn (array $match): string => $match[0] === '\\$' ? '$' : stripcslashes($match[0]), $value);
    }

    private function constant(string $class, string $name, string $ownerCode): ?string
    {
        // Support explicit App imports/FQCNs only. Inheritance, aliases via expressions,
        // nonliteral constants and external classes remain unresolved.
        $code = '';
        foreach (token_get_all($ownerCode) as $token) {
            $code .= is_array($token) ? (in_array($token[0], [T_COMMENT, T_DOC_COMMENT], true) ? ' ' : $token[1]) : $token;
        }
        if (! str_starts_with($class, '\\')) {
            preg_match_all('/^\s*use\s+(App\\\\[A-Za-z0-9_\\\\]+)(?:\s+as\s+(\w+))?\s*;/m', $code, $imports, PREG_SET_ORDER);
            $resolved = [];
            foreach ($imports as $import) {
                $alias = $import[2] ?? substr($import[1], strrpos($import[1], '\\') + 1);
                if ($alias === $class) {
                    $resolved[] = $import[1];
                }
            }
            if (count($resolved) !== 1) {
                return null;
            }
            $class = $resolved[0];
        }
        $class = ltrim($class, '\\');
        if (! str_starts_with($class, 'App\\')) {
            return null;
        }
        $path = app_path(str_replace('\\', '/', substr($class, 4)).'.php');
        if (! File::isFile($path)) {
            return null;
        }
        $contents = File::get($path);
        if (! str_starts_with($contents, '<?php')) {
            return null;
        }
        $tokens = $this->tokens(substr($contents, 5));
        $namespace = substr($class, 0, strrpos($class, '\\'));
        $shortName = substr($class, strrpos($class, '\\') + 1);
        $declaredNamespace = null;
        $insideClass = false;
        $depth = 0;
        $matches = [];
        for ($i = 0; $i < count($tokens) - 5; $i++) {
            if (is_array($tokens[$i]) && $tokens[$i][0] === T_NAMESPACE) {
                $declaredNamespace = is_array($tokens[$i + 1]) ? $tokens[$i + 1][1] : null;
            }
            if (! $insideClass && $declaredNamespace === $namespace && is_array($tokens[$i]) && $tokens[$i][0] === T_CLASS
                && is_array($tokens[$i + 1]) && $tokens[$i + 1][0] === T_STRING && $tokens[$i + 1][1] === $shortName) {
                while ($i < count($tokens) && $tokens[$i] !== '{') { $i++; }
                $insideClass = true;
                $depth = 1;
                continue;
            }
            if (! $insideClass) { continue; }
            if ($tokens[$i] === '{') { $depth++; }
            if ($tokens[$i] === '}') { $depth--; }
            if ($depth === 0) { break; }
            if ($depth === 1 && is_array($tokens[$i]) && $tokens[$i][0] === T_PUBLIC
                && is_array($tokens[$i + 1]) && $tokens[$i + 1][0] === T_CONST
                && is_array($tokens[$i + 2]) && $tokens[$i + 2][1] === $name
                && $tokens[$i + 3] === '=' && $tokens[$i + 5] === ';') {
                $matches[] = $this->literal($tokens[$i + 4]);
            }
        }
        return count($matches) === 1 ? $matches[0] : null;
    }
}
