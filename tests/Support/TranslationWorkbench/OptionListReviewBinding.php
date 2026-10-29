<?php

namespace Tests\Support\TranslationWorkbench;

/** Explicit reviewed source-to-list mapping, not inferred runtime ownership. */
final class OptionListReviewBinding
{
    public function bind(array $capture, array $inventory, array $bindings, callable $read): ?array
    {
        $owner = $capture['option_source']['owner_key'] ?? null;
        $selection = $bindings[$capture['source_path']][$owner] ?? null;
        if ($selection === null) {
            return null;
        }
        $stale = ['state' => 'source_changed', 'entries' => []];
        $source = $read($capture['source_path']);
        if (! is_string($source) || hash('sha256', $source) !== $capture['source_sha256']
            || substr($source, $capture['source_offset'], strlen($capture['raw_expression'])) !== $capture['raw_expression']) {
            return $stale;
        }
        // Pin the reviewed provider chain too, not only the final array definition.
        foreach ($inventory['files'] as $path => $file) {
            $bytes = $read($path);
            if (! is_string($bytes) || hash('sha256', $bytes) !== $file['sha256']) {
                return $stale;
            }
        }
        $matches = array_values(array_filter($inventory['option_lists'], function ($list) use ($selection) {
            foreach ($selection as $key => $value) {
                if (($list[$key] ?? null) !== $value) {
                    return false;
                }
            }

            return true;
        }));
        if (count($matches) !== 1) {
            return ['state' => 'ambiguous_list', 'entries' => []];
        }
        $list = $matches[0];
        if (($inventory['files'][$list['path']]['sha256'] ?? null) !== $list['source_sha256']) {
            return $stale;
        }
        foreach ($list['entries'] as $entry) {
            $dependency = $entry['key_dependency'] ?? null;
            if ($dependency !== null && ($inventory['files'][$dependency['path']]['sha256'] ?? null) !== $dependency['source_sha256']) {
                return $stale;
            }
        }

        return ['state' => 'reviewed_default_source', 'runtime_exhaustiveness_proven' => false,
            'path' => $list['path'], 'line' => $list['line'], 'source_sha256' => $list['source_sha256'],
            'entries' => $list['entries'], 'complete_static_default' => $list['complete_static_default']];
    }
}
