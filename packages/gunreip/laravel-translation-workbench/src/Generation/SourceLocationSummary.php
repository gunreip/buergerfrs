<?php

namespace Gunreip\TranslationWorkbench\Generation;

/** Lightweight position check only: does not classify, scan, or change findings. */
final class SourceLocationSummary
{
    public function inspect(iterable $findings, string $root): array
    {
        $root = realpath($root);
        $count = 0;
        $files = [];
        $examples = [];
        $lastPath = null;
        $bytes = false;
        $offsets = [];
        foreach ($findings as $finding) {
            $relative = $finding['path'];
            if ($lastPath !== $relative) {
                $lastPath = $relative;
                $path = $root === false ? false : realpath($root.'/'.$relative);
                $bytes = $path !== false && str_starts_with($path, $root.DIRECTORY_SEPARATOR) && is_file($path)
                    ? @file_get_contents($path) : false;
                $offsets = [1 => 0];
                if ($bytes !== false) {
                    $offset = 0;
                    foreach (explode("\n", $bytes) as $index => $line) {
                        $offsets[$index + 1] = $offset;
                        $offset += strlen($line) + 1;
                    }
                }
            }
            $line = (int) $finding['source_line'];
            $column = (int) $finding['source_column'];
            $expression = (string) $finding['raw_expression'];
            $start = ($offsets[$line] ?? 0) + $column - 1;
            $lineEnd = $offsets[$line + 1] ?? ($bytes === false ? 0 : strlen($bytes) + 1);
            if ($bytes !== false && isset($offsets[$line]) && $column > 0 && $start < $lineEnd && $expression !== ''
                && substr($bytes, $start, strlen($expression)) === $expression) continue;
            $count++;
            $files[$relative] = true;
            if (count($examples) < 5) $examples[] = ['finding' => (int) $finding['id'], 'path' => $relative, 'line' => $line, 'column' => $column];
        }

        return ['required' => $count > 0, 'findings' => $count, 'files' => count($files), 'examples' => $examples];
    }
}
