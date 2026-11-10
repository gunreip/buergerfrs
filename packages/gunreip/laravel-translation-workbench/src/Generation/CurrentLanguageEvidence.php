<?php

namespace Gunreip\TranslationWorkbench\Generation;

/** Revalidate captured English values against current files without executing PHP. */
final class CurrentLanguageEvidence
{
    public function resolve(array $rows, string $root): array
    {
        $root = realpath($root);
        if ($root === false) return [];
        $files = $groups = [];
        foreach ($rows as $row) {
            $relative = $row['path'];
            if (str_starts_with($relative, '/') || in_array('..', explode('/', $relative), true)) continue;
            $path = realpath($root.'/'.$relative);
            if ($path === false || !str_starts_with($path, $root.DIRECTORY_SEPARATOR)) continue;
            if (!array_key_exists($path, $files)) {
                $files[$path] = null;
                $bytes = @file_get_contents($path);
                if ($bytes !== false) {
                    try {
                        $files[$path] = ['tree' => (new BaselineValueReader)->treeFromBytes($bytes), 'sha256' => hash('sha256', $bytes)];
                    } catch (\RuntimeException|\PhpParser\Error $error) {
                        // Unsupported or ambiguous files cannot provide verified evidence.
                    }
                }
            }
            if ($files[$path] === null) continue;
            $segments = explode('.', $row['full_name']);
            if (array_shift($segments) !== pathinfo($path, PATHINFO_FILENAME) || $segments === []) continue;
            $value = $files[$path]['tree'];
            foreach ($segments as $segment) {
                if (!is_array($value) || !array_key_exists($segment, $value)) { $value = null; break; }
                $value = $value[$segment];
            }
            if (!is_string($value) || $value !== $row['value']) continue;
            // Import evidence and a publication receipt may verify the very same file entry.
            $groups[$row['full_name']][$path] = ['value' => $value, 'provenance' => [
                'key' => $row['full_name'], 'locale' => 'en', 'path' => $relative,
                'sha256' => $files[$path]['sha256'],
            ]];
        }
        $result = [];
        foreach ($groups as $key => $evidence) {
            if (count($evidence) === 1) $result[$key] = array_values($evidence)[0];
        }
        return $result;
    }
}
