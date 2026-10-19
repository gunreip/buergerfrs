<?php

namespace Gunreip\TranslationWorkbench\Scanner;

use Illuminate\Support\Facades\File;

/** Static view reachability evidence, not proof of runtime variable ownership. */
class TranslationWorkbenchOptionViewBinding
{
    public function inspect(string $sourceFile, string $targetPath, ?string $variable = null): array
    {
        $providerReference = str_contains($sourceFile, ':');
        $sourceFile = explode(':', $sourceFile, 2)[0];
        $absolute = base_path($sourceFile);
        if (! File::isFile($absolute)) {
            return ['status' => 'missing_source', 'paths' => []];
        }
        $code = '';
        foreach (token_get_all(File::get($absolute)) as $token) {
            $code .= is_array($token) ? (in_array($token[0], [T_COMMENT, T_DOC_COMMENT], true) ? '' : $token[1]) : $token;
        }
        preg_match_all('/return\s+view\s*\(\s*([\'"])([^\'"$]+)\1\s*[,)]/u', $code, $roots, PREG_SET_ORDER);
        $queue = [];
        foreach ($roots as $root) {
            if ($path = $this->viewPath($root[2])) {
                $queue[] = [$sourceFile, $path];
            }
        }
        $matches = [];
        $unresolved = [];
        $complete = $queue !== [] && count($queue) === count($roots) && count($roots) === preg_match_all('/return\s+view\s*\(/u', $code);
        $steps = 0;
        while ($chain = array_shift($queue)) {
            if (++$steps > 512) {
                $complete = false;
                break;
            }
            $path = end($chain);
            if (! File::isFile(base_path($path))) {
                $unresolved[] = $path;
                $complete = false;
                continue;
            }
            if ($path === $targetPath) {
                $matches[] = $chain;
                continue;
            }
            if (in_array($path, array_slice($chain, 0, -1), true)) {
                $complete = false;
                continue;
            }
            $contents = preg_replace('/\{\{--.*?--\}\}/s', '', File::get(base_path($path)));
            preg_match_all('/@include(?:If)?\s*\(\s*([\'"])([^\'"$]+)\1\s*\)/u', $contents, $includes, PREG_SET_ORDER);
            if (count($includes) !== preg_match_all('/@(?:include\w*|each)\s*\(/u', $contents)) {
                $complete = false;
            }
            foreach ($includes as $include) {
                if ($next = $this->viewPath($include[2])) {
                    $queue[] = [...$chain, $next];
                } else {
                    $complete = false;
                }
            }
        }

        return [
            'status' => $matches === [] ? 'unverified_view_binding' : 'static_view_path',
            'paths' => $matches, 'missing_views' => array_values(array_unique($unresolved)),
            'include_traversal_complete' => $complete,
            'variable_flow' => $variable === null ? null : $this->variableFlow($sourceFile, $matches, $variable, $providerReference),
        ];
    }

    /** Conservative diagnostic: absence of a detected write is not runtime proof. */
    private function variableFlow(string $sourceFile, array $paths, string $variable, bool $providerReference): array
    {
        if (! preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/D', $variable)) {
            return ['status' => 'unverified', 'findings' => []];
        }
        $files = array_values(array_unique(array_merge([$sourceFile], ...$paths)));
        $findings = [];
        $name = preg_quote($variable, '~');
        $write = '(?:\s*\[[^\]\r\n]*\])*\s*(?:=(?!=|>)|\?\?=|[.+*/%&|^-]=|\+\+|--)';
        foreach ($files as $file) {
            if (! File::isFile(base_path($file))) {
                $findings[] = ['file' => $file, 'line' => null, 'reason' => 'missing_file'];
                continue;
            }
            $contents = File::get(base_path($file));
            // Preserve offsets/line numbers while ignoring Blade comments.
            $contents = preg_replace_callback('/\{\{--.*?--\}\}/s', static fn ($m) => preg_replace('/[^\r\n]/', ' ', $m[0]), $contents);
            $patterns = $file === $sourceFile
                ? ['property_write' => '~\$this->'.$name.'\b'.$write.'~']
                : [
                    'variable_write' => '~\$'.$name.'\b'.$write.'~',
                    'loop_binding' => '~@(?:foreach|forelse)\s*\([^\r\n]*\bas\s+[^\r\n)]*\$'.$name.'\b~',
                    'variable_unset' => '~unset\s*\([^)]*\$'.$name.'\b~',
                    'property_write' => '~\$this->'.$name.'\b'.$write.'~',
                    'component_scope' => '~@(props|aware)\s*\(~',
                ];
            if ($file === $sourceFile && ! $providerReference) {
                $patterns['source_data_binding'] = '~[\'"]'.$name.'[\'"]\s*=>~';
            }
            foreach ($patterns as $reason => $pattern) {
                preg_match_all($pattern, $contents, $hits, PREG_OFFSET_CAPTURE);
                foreach ($hits[0] as [$text, $offset]) {
                    $findings[] = ['file' => $file, 'line' => substr_count(substr($contents, 0, $offset), "\n") + 1, 'reason' => $reason];
                }
            }
        }

        return [
            'status' => $paths === [] ? 'unverified' : ($findings === [] ? 'no_detected_override' : 'potential_override'),
            'variable' => $variable, 'findings' => $findings,
        ];
    }

    private function viewPath(string $name): ?string
    {
        if (str_contains($name, '::') || str_contains($name, '/') || str_contains($name, '..')) {
            return null;
        }

        return 'resources/views/'.str_replace('.', '/', $name).'.blade.php';
    }
}
