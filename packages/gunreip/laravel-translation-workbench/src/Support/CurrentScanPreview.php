<?php

namespace Gunreip\TranslationWorkbench\Support;

/** Reads only a completed, hash-checked current scan; never runs scans on page requests. */
class CurrentScanPreview
{
    public function load(string $filter = 'review'): ?array
    {
        try {
            $pointer = $this->read(storage_path('app/private/workbench-previews/current-scan.json'));
            if (! preg_match('/^\d{8}-\d{6}-[a-f0-9]{12}$/D', $pointer['run'] ?? '')) {
                return null;
            }
            $data = $this->read($this->directory($pointer['run']).'/manifest.json', $pointer['sha256'] ?? '');
            if (($data['state'] ?? null) !== 'complete' || ($data['mode'] ?? null) !== 'current_source_scan'
                || ($data['run'] ?? null) !== $pointer['run'] || ($data['production_connected'] ?? null) !== false) {
                return null;
            }

            $states = match ($filter) {
                'review' => ['key_review', 'expression_review'],
                'reusable_key', 'literal_new' => [$filter],
                default => ['reusable_key', 'literal_new', 'key_review', 'expression_review'],
            };
            $data['groups'] = [];
            foreach ($states as $state) {
                $index = $data['indexes'][$state];
                $rows = $this->read($this->directory($pointer['run']).'/index-'.$state.'.json', $index['sha256']);
                if (count($rows) !== $index['groups']) {
                    return null;
                }
                array_push($data['groups'], ...$rows);
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
        foreach ($snapshot['groups'] as $group) {
            if ($group['id'] !== $id) {
                continue;
            }
            try {
                $data = $this->read($this->directory($snapshot['run']).'/'.$id.'.json', $group['sha256']);

                return ($data['id'] ?? null) === $id ? $data : null;
            } catch (\Throwable) {
                return null;
            }
        }

        return null;
    }

    private function directory(string $run): string
    {
        return storage_path('app/private/workbench-previews/current-scan-'.$run);
    }

    private function read(string $path, ?string $hash = null): array
    {
        $bytes = @file_get_contents($path);
        if ($bytes === false || ($hash !== null && hash('sha256', $bytes) !== $hash)) {
            throw new \RuntimeException('Scan evidence unavailable or changed');
        }

        return json_decode($bytes, true, flags: JSON_THROW_ON_ERROR);
    }
}
