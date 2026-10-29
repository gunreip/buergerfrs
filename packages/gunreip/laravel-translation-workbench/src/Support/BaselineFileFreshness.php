<?php

namespace Gunreip\TranslationWorkbench\Support;

/** Compares referenced files only. Does not rescan, execute PHP, or repair evidence. */
final class BaselineFileFreshness
{
    public function inspect(array $item, string $root): array
    {
        $root = realpath($root);
        $references = [];
        foreach ($item['blocked_sources'] ?? [] as $source) {
            $references[] = ['kind' => 'source', 'path' => $source['path'], 'sha256' => $source['sha256'] ?? null];
        }
        foreach ($item['locales'] ?? [] as $row) {
            if (($row['published'] ?? null) !== null) {
                $references[] = ['kind' => 'language', 'path' => $row['published']['path'], 'sha256' => $row['published']['sha256'] ?? null];
            }
        }
        $files = [];
        $hashes = [];
        foreach ($references as $reference) {
            $identity = $reference['kind'].':'.$reference['path'];
            if (isset($files[$identity])) {
                if ($files[$identity]['expected_sha256'] !== $reference['sha256']) {
                    $files[$identity]['status'] = 'conflicting_evidence';
                }

                continue;
            }
            $path = $reference['path'];
            $expected = $reference['sha256'];
            $actual = null;
            if (! is_string($expected) || preg_match('/^[a-f0-9]{64}$/D', $expected) !== 1) {
                $status = 'missing_evidence';
            } elseif ($root === false || str_contains($path, "\0") || str_contains($path, '\\')
                || str_starts_with($path, '/') || preg_match('~(^|/)\.\.?(/|$)~', $path)) {
                $status = 'outside_root';
            } else {
                $resolved = realpath($root.'/'.$path);
                if ($resolved === false) {
                    $status = 'missing';
                } elseif (! str_starts_with($resolved, $root.DIRECTORY_SEPARATOR)) {
                    $status = 'outside_root';
                } elseif (! is_file($resolved) || ! is_readable($resolved)) {
                    $status = 'unreadable';
                } else {
                    // A concurrently removed file must yield an unreadable result, not break the UI.
                    if (! array_key_exists($resolved, $hashes)) {
                        $hashes[$resolved] = @hash_file('sha256', $resolved);
                    }
                    $actual = $hashes[$resolved] ?: null;
                    $status = $actual === null ? 'unreadable' : (hash_equals($expected, $actual) ? 'unchanged' : 'changed');
                }
            }
            $files[$identity] = ['kind' => $reference['kind'], 'path' => $path,
                'expected_sha256' => $expected, 'actual_sha256' => $actual, 'status' => $status];
        }
        $counts = array_count_values(array_column($files, 'status'));

        // Older exports without source evidence must never appear fully verified.
        return ['files' => array_values($files), 'counts' => $counts,
            'matches' => ($item['blocked_sources'] ?? []) !== [] && $files !== []
                && ($counts['unchanged'] ?? 0) === count($files)];
    }
}
