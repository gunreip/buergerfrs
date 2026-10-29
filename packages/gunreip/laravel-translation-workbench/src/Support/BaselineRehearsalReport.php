<?php

namespace Gunreip\TranslationWorkbench\Support;

/** Reads recorded test evidence only; never runs a rehearsal or accesses its database. */
class BaselineRehearsalReport
{
    public function load(): ?array
    {
        $path = storage_path('app/private/workbench-previews/workflow-rehearsal.json');
        if (! is_file($path) || ! is_readable($path)) {
            return null;
        }
        try {
            // The file can disappear or lose readability after the check above.
            $bytes = @file_get_contents($path);

            return $bytes === false ? null : $this->validate(json_decode($bytes, true, flags: JSON_THROW_ON_ERROR));
        } catch (\JsonException|\ErrorException) {
            return null;
        }
    }

    public function validate(mixed $data): ?array
    {
        if (! is_array($data) || ($data['state'] ?? null) !== 'isolated_correction_application_verified'
            || ($data['production_connected'] ?? null) !== false || ($data['cluster_stopped'] ?? null) !== true
            || ! is_int($data['assertions'] ?? null) || $data['assertions'] < 1
            || ! is_string($data['synthetic_target'] ?? null)
            || ! is_string($data['input_dump_sha256'] ?? null)
            || ! preg_match('/^[a-f0-9]{64}$/D', $data['input_dump_sha256'])) {
            return null;
        }

        return $data;
    }
}
