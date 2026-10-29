<?php

namespace Gunreip\TranslationWorkbench\Support;

use PhpParser\Error;
use RuntimeException;

/** Read-only preview for an explicitly entered key and an evidenced source file. */
final class BaselineProposedSourceChanges
{
    /** A complete read-only check. A partial preview never receives a plan fingerprint. */
    public function previewAll(?array $snapshot, string $source, string $target, string $root): array
    {
        $proposal = (new BaselineKeyCorrectionPreview)->inspect($snapshot, $source, $target);
        $result = ['status' => 'proposal_required', 'source' => $source, 'target' => $proposal['target'],
            'checked_at' => gmdate('Y-m-d H:i:s').' UTC', 'files' => [], 'issues' => [],
            'finding_count' => 0, 'fingerprint' => null,
            'snapshot_sha256' => hash('sha256', json_encode($snapshot, JSON_THROW_ON_ERROR))];
        if ($proposal['status'] !== 'no_snapshot_conflict') {
            return $result;
        }
        $item = $snapshot['items'][$source];
        $sources = $item['blocked_sources'] ?? [];
        $ids = array_column($sources, 'finding_id');
        if ($sources === [] || count($ids) !== count($sources)
            || count(array_unique($ids)) !== count($ids)) {
            return [...$result, 'status' => 'source_evidence_incomplete'];
        }
        $freshness = (new BaselineFileFreshness)->inspect($item, $root);
        foreach ($freshness['files'] as $file) {
            if ($file['status'] !== 'unchanged') {
                $result['issues'][] = ['kind' => $file['kind'], 'path' => $file['path'], 'status' => $file['status']];
            }
        }
        if (! $freshness['matches']) {
            return [...$result, 'status' => 'files_not_verified'];
        }
        $paths = array_unique(array_column($sources, 'path'));
        sort($paths, SORT_STRING);
        $plans = [];
        foreach ($paths as $path) {
            $preview = $this->preview($snapshot, $source, $proposal['target'], $path, $root);
            if ($preview['status'] !== 'preview') {
                $result['issues'][] = ['kind' => 'source', 'path' => $path, 'status' => 'source_not_verified'];

                continue;
            }
            $plans[] = $preview;
            $result['files'][] = ['path' => $path, 'finding_count' => count($preview['edits'])];
            $result['finding_count'] += count($preview['edits']);
        }
        // Recheck all referenced bytes after parsing, including language files.
        $after = (new BaselineFileFreshness)->inspect($item, $root);
        foreach ($after['files'] as $file) {
            if ($file['status'] !== 'unchanged') {
                $result['issues'][] = ['kind' => $file['kind'], 'path' => $file['path'], 'status' => $file['status']];
            }
        }
        if ($result['issues'] !== [] || ! $after['matches'] || $result['finding_count'] !== count($sources)) {
            return [...$result, 'status' => 'files_not_verified'];
        }
        $fingerprint = hash('sha256', json_encode([
            'schema' => 1, 'snapshot_sha256' => $result['snapshot_sha256'], 'source' => $source,
            'target' => $proposal['target'], 'plans' => $plans, 'references' => $after['files'],
        ], JSON_THROW_ON_ERROR));

        return [...$result, 'status' => 'preview', 'fingerprint' => $fingerprint];
    }

    public function preview(?array $snapshot, string $source, string $target, string $path, string $root): array
    {
        $proposal = (new BaselineKeyCorrectionPreview)->inspect($snapshot, $source, $target);
        $result = ['status' => 'proposal_required', 'path' => $path, 'edits' => []];
        if ($proposal['status'] !== 'no_snapshot_conflict') {
            return $result;
        }
        $sources = array_values(array_filter($snapshot['items'][$source]['blocked_sources'] ?? [],
            fn ($row) => $row['path'] === $path));
        if ($sources === []) {
            return [...$result, 'status' => 'select_file'];
        }
        $freshness = (new BaselineFileFreshness)->inspect(['blocked_sources' => $sources], $root);
        if (! $freshness['matches']) {
            return [...$result, 'status' => 'source_not_verified'];
        }
        // Resolve again before reading; never accept a path supplied outside the selected evidence.
        $root = realpath($root);
        $resolved = $root === false ? false : realpath($root.'/'.$path);
        if ($resolved === false || ! str_starts_with($resolved, $root.DIRECTORY_SEPARATOR)) {
            return [...$result, 'status' => 'source_not_verified'];
        }
        try {
            $bytes = @file_get_contents($resolved);
            if ($bytes === false) {
                return [...$result, 'status' => 'source_not_verified'];
            }
            $findings = [];
            foreach ($sources as $row) {
                if (! is_int($row['finding_id'] ?? null) || $row['finding_id'] < 1
                    || ! is_int($row['line'] ?? null) || ! is_int($row['column'] ?? null)
                    || ! is_string($row['expression'] ?? null)) {
                    throw new RuntimeException('Incomplete source evidence');
                }
                $findings[] = ['id' => $row['finding_id'], 'source_line' => $row['line'],
                    'source_column' => $row['column'], 'raw_expression' => $row['expression']];
            }
            // The parser checks the hash again against the bytes actually read.
            $preview = (new SourceKeyChangePreview)->preview($bytes, $sources[0]['sha256'], $path,
                $findings, $source, $proposal['target']);

            return [...$result, 'status' => 'preview', 'edits' => $preview['edits'],
                'before_sha256' => $preview['before_sha256'], 'after_sha256' => $preview['after_sha256']];
        } catch (RuntimeException|Error|\ErrorException) {
            return [...$result, 'status' => 'source_not_verified'];
        }
    }
}
