<?php

namespace Gunreip\TranslationWorkbench\Support;

/** Reads a dated export; never queries runtime tables on a page request. */
class RuntimeOptionPreview
{
    public function load(): ?array
    {
        try {
            $base = storage_path('app/private/workbench-previews');
            $pointer = json_decode(@file_get_contents($base.'/runtime-options.json') ?: '', true, flags: JSON_THROW_ON_ERROR);
            if (! preg_match('/^\d{8}-\d{6}-[a-f0-9]{12}$/D', $pointer['run'] ?? '')) {
                return null;
            }
            $bytes = @file_get_contents($base.'/runtime-options-'.$pointer['run'].'.json');
            if ($bytes === false || hash('sha256', $bytes) !== ($pointer['sha256'] ?? null)) {
                return null;
            }
            $data = json_decode($bytes, true, flags: JSON_THROW_ON_ERROR);
            if (($data['mode'] ?? null) !== 'read_only_runtime_option_observation' || ($data['database_read_only'] ?? null) !== true) {
                return null;
            }
            foreach ($data['source_hashes'] as $path => $hash) {
                if (str_contains($path, '..') || ! str_starts_with($path, 'app/') || @hash_file('sha256', base_path($path)) !== $hash) {
                    return null;
                }
            }

            return $data;
        } catch (\Throwable) {
            return null;
        }
    }
}
