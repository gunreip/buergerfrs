<?php

namespace Gunreip\TranslationWorkbench\Support;

/** Selects recorded changes; only a uniquely selected snapshot file can be inspected. */
class BaselineSourceChanges
{
    public function load(): ?array
    {
        $path = storage_path('app/private/workbench-previews/source-changes.json');
        if (! is_file($path) || ! is_readable($path)) {
            return null;
        }
        try {
            $bytes = @file_get_contents($path);
            $data = $bytes === false ? null : json_decode($bytes, true, flags: JSON_THROW_ON_ERROR);
            if (! is_array($data) || ($data['schema'] ?? null) !== 1 || ($data['mode'] ?? null) !== 'isolated_source_change_preview'
                || ! is_array($data['files'] ?? null) || ! is_array($data['movements'] ?? null)) {
                return null;
            }
            foreach (['recorded_at', 'input_dump_sha256', 'source_key', 'synthetic_target'] as $field) {
                if (! is_string($data[$field] ?? null)) {
                    return null;
                }
            }
            foreach ($data['files'] as $file) {
                if (! is_array($file) || ! is_string($file['path'] ?? null) || ! is_array($file['edits'] ?? null)) {
                    return null;
                }
                foreach ($file['edits'] as $edit) {
                    foreach (['finding', 'line', 'column'] as $field) {
                        if (! is_int($edit[$field] ?? null) || $edit[$field] < 1) {
                            return null;
                        }
                    }
                    if (! is_string($edit['before'] ?? null) || ! is_string($edit['after'] ?? null)) {
                        return null;
                    }
                }
            }
            foreach ($data['movements'] as $movement) {
                if (! is_array($movement) || ! is_string($movement['path'] ?? null) || ! is_string($movement['expression'] ?? null)) {
                    return null;
                }
                foreach (['before_line', 'before_column', 'after_line', 'after_column'] as $field) {
                    if (! is_int($movement[$field] ?? null) || $movement[$field] < 1) {
                        return null;
                    }
                }
            }

            return $data;
        } catch (\JsonException|\ErrorException) {
            return null;
        }
    }

    public function select(?array $data, string $path): array
    {
        $matches = array_values(array_filter($data['files'] ?? [], fn ($file) => $file['path'] === $path));

        return ['snapshot' => $data, 'file' => count($matches) === 1 ? $matches[0] : null,
            'movements' => array_values(array_filter($data['movements'] ?? [], fn ($movement) => $movement['path'] === $path))];
    }

    public function currentFile(?array $data, string $path, string $root): ?array
    {
        $file = $this->select($data, $path)['file'];
        if ($file === null) {
            return null;
        }
        $before = $file['before_sha256'] ?? null;
        $after = $file['after_sha256'] ?? null;
        $result = (new BaselineFileFreshness)->inspect(['blocked_sources' => [
            ['path' => $file['path'], 'sha256' => $before],
        ]], $root)['files'][0];
        $status = $result['status'];
        if (! is_string($after) || ! preg_match('/^[a-f0-9]{64}$/D', $after) || $before === $after) {
            $status = 'missing_evidence';
        } elseif ($status === 'unchanged') {
            $status = 'matches_before';
        } elseif ($status === 'changed') {
            $status = hash_equals($after, $result['actual_sha256']) ? 'matches_after' : 'diverged';
        }

        return ['status' => $status, 'checked_at' => gmdate('Y-m-d H:i:s').' UTC',
            'before_sha256' => $before, 'after_sha256' => $after, 'actual_sha256' => $result['actual_sha256']];
    }

    /** Checks the complete recorded set, including ambiguous duplicate paths. No scan or writes. */
    public function currentFiles(?array $data, string $root): array
    {
        $files = [];
        foreach ($data['files'] ?? [] as $file) {
            $path = $file['path'];
            if (isset($files[$path])) {
                continue;
            }
            $current = $this->currentFile($data, $path, $root);
            $files[$path] = ['path' => $path, 'status' => $current['status'] ?? 'conflicting_evidence'];
        }
        $counts = array_count_values(array_column($files, 'status'));

        return ['checked_at' => gmdate('Y-m-d H:i:s').' UTC', 'files' => array_values($files), 'counts' => $counts,
            'total' => count($files),
            'all_match_before' => $files !== [] && ($counts['matches_before'] ?? 0) === count($files)];
    }
}
