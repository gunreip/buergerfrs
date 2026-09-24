<?php

namespace App\Support;

use RuntimeException;

final class WatchVersion
{
    private readonly string $file;

    public function __construct(?string $file = null)
    {
        $this->file = $file ?? storage_path('app/development/watch-version.json');
    }

    public function current(?string $commit): ?array
    {
        if ($commit === null || ! is_readable($this->file)) {
            return null;
        }

        $handle = fopen($this->file, 'r');
        if ($handle === false) {
            return null;
        }
        try {
            flock($handle, LOCK_SH);
            $state = json_decode(stream_get_contents($handle), true);

            return is_array($state) && ($state['commit'] ?? null) === $commit
                && is_int($state['count'] ?? null) && $state['count'] > 0 ? $state : null;
        } finally {
            flock($handle, LOCK_UN);
            fclose($handle);
        }
    }

    public function record(?string $commit, bool $successful, bool $changed): ?array
    {
        if ($commit === null || ! $successful || ! $changed) {
            return null;
        }

        $directory = dirname($this->file);
        if (! is_dir($directory) && ! mkdir($directory, 0775, true) && ! is_dir($directory)) {
            throw new RuntimeException('Cannot create watch version directory.');
        }
        $handle = fopen($this->file, 'c+');
        if ($handle === false) {
            throw new RuntimeException('Cannot open watch version file.');
        }
        try {
            if (! flock($handle, LOCK_EX)) {
                throw new RuntimeException('Cannot lock watch version file.');
            }
            $previous = json_decode(stream_get_contents($handle), true);
            $count = ($previous['commit'] ?? null) === $commit ? (int) ($previous['count'] ?? 0) : 0;
            $state = ['commit' => $commit, 'count' => $count + 1, 'updated_at' => gmdate(DATE_ATOM)];
            rewind($handle);
            ftruncate($handle, 0);
            fwrite($handle, json_encode($state, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR).PHP_EOL);
            fflush($handle);

            return $state;
        } finally {
            flock($handle, LOCK_UN);
            fclose($handle);
        }
    }
}
