<?php

namespace App\Support;

use Symfony\Component\Process\Process;
use Throwable;

final class AppVersion
{
    private readonly string $path;

    public function __construct(?string $path = null)
    {
        $this->path = $path ?? base_path();
    }

    public function label(): string
    {
        // A checkout must never silently display an outdated build snapshot.
        // .git may also be a file, as it is in linked worktrees.
        if (file_exists($this->path.'/.git')) {
            return $this->gitVersion() ?? 'n/a';
        }

        $file = $this->path.'/public/version.txt';
        $version = is_readable($file) ? trim((string) file_get_contents($file)) : '';

        return $version !== '' ? $version : 'n/a';
    }

    public function gitVersion(): ?string
    {
        return $this->git(['describe', '--tags', '--always', '--dirty']);
    }

    public function commit(): ?string
    {
        return $this->git(['rev-parse', '--verify', 'HEAD']);
    }

    public function details(): array
    {
        $commit = $this->commit();

        return [
            'versions' => app(VersionManifest::class)->all(),
            'git' => $this->label(),
            'watch' => app(WatchVersion::class)->current($commit),
        ];
    }

    private function git(array $arguments): ?string
    {
        if (! file_exists($this->path.'/.git')) {
            return null;
        }

        try {
            // Trust only this application's checkout when PHP runs as the web user.
            // Avoid index refresh writes from the read-only footer lookup.
            $process = new Process([
                'git', '--no-optional-locks', '-c', 'safe.directory='.$this->path,
                ...$arguments,
            ], $this->path);
            $process->setTimeout(2);
            $process->run();

            $version = trim($process->getOutput());

            return $process->isSuccessful() && $version !== '' ? $version : null;
        } catch (Throwable) {
            return null;
        }
    }
}
