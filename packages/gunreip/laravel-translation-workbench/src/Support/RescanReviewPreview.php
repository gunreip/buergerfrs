<?php

namespace Gunreip\TranslationWorkbench\Support;

/** Reads exported rehearsal evidence only; no scanner, database connection or writes. */
class RescanReviewPreview
{
    public function load(): ?array
    {
        try {
            $pointer = $this->read(storage_path('app/private/workbench-previews/rescan-review.json'));
            if (! preg_match('/^\d{8}-\d{6}-[a-f0-9]{12}$/D', $pointer['run'] ?? '')) {
                return null;
            }
            $data = $this->read($this->directory($pointer['run']).'/manifest.json', $pointer['sha256'] ?? '');
            $generation = $this->read(storage_path('app/private/workbench-previews/current-generation.json'));
            if (($data['mode'] ?? null) !== 'isolated_rescan_review' || ($data['run'] ?? null) !== $pointer['run']
                || ($data['generation'] ?? null) !== ($generation['generation']['generation'] ?? '')
                || ($data['production_connected'] ?? null) !== false || ($data['workbench_switched'] ?? null) !== false) {
                return null;
            }

            return $data;
        } catch (\Throwable) {
            return null;
        }
    }

    public function detail(?array $snapshot, string $id): ?array
    {
        if ($snapshot === null || ! preg_match('/^[a-f0-9]{64}$/D', $id)) {
            return null;
        }
        foreach ($snapshot['files'] as $file) {
            if ($file['id'] !== $id) {
                continue;
            }
            try {
                $detail = $this->read($this->directory($snapshot['run']).'/'.$id.'.json', $file['sha256']);

                return ($detail['id'] ?? null) === $id && ($detail['path'] ?? null) === $file['path'] ? $detail : null;
            } catch (\Throwable) {
                return null;
            }
        }

        return null;
    }

    private function directory(string $run): string
    {
        return storage_path('app/private/workbench-previews/rescan-review-'.$run);
    }

    private function read(string $path, ?string $hash = null): array
    {
        $bytes = @file_get_contents($path);
        if ($bytes === false || ($hash !== null && hash('sha256', $bytes) !== $hash)) {
            throw new \RuntimeException('Rescan evidence unavailable or changed');
        }

        return json_decode($bytes, true, flags: JSON_THROW_ON_ERROR);
    }
}
