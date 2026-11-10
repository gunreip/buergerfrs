<?php

namespace Gunreip\TranslationWorkbench\Generation;

/** Verify imported evidence against the current entry, not the historical whole-file hash. */
final class WorkflowFileEvidence
{
    public function languages(array $evidence, string $root): array
    {
        $result = [];
        $root = realpath($root);
        if ($root === false) return [];
        foreach ($evidence as $row) {
            $path = realpath($root.'/'.preg_replace('~^lang/~', '', $row['path']));
            if ($path === false || !str_starts_with($path, $root.DIRECTORY_SEPARATOR) || !is_readable($path)) continue;
            try {
                $bytes = file_get_contents($path);
                if ($bytes === false) continue;
                $tree = (new BaselineValueReader)->treeFromBytes($bytes);
                $segments = explode('.', $row['full_name']);
                if (array_shift($segments) !== pathinfo($path, PATHINFO_FILENAME)) continue;
                $value = $tree;
                foreach ($segments as $segment) {
                    if (!is_array($value) || !array_key_exists($segment, $value)) { $value = null; break; }
                    $value = $value[$segment];
                }
                if (!is_string($value) || $value !== $row['value']) continue;
                $result[$row['locale']] = ['locale' => $row['locale'], 'state' => 'existing_publication', 'revision' => null];
            } catch (\RuntimeException|\PhpParser\Error $error) {
                // Unverifiable evidence must not complete a workflow step.
            }
        }
        return $result;
    }
}
