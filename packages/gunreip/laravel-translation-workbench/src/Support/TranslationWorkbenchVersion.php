<?php

// packages/gunreip/laravel-translation-workbench/src/Support/TranslationWorkbenchVersion.php

namespace Gunreip\TranslationWorkbench\Support;

use Symfony\Component\Process\Process;
use Throwable;

final class TranslationWorkbenchVersion
{
    /**
     * The package version is derived from Git so it can move with the package:
     * exact package tag => release version, otherwise fallback version + commit.
     *
     * @return array{label: string, version: string, commit: string|null, dirty: bool, source: string}
     */
    public function toArray(): array
    {
        $fallback = $this->fallbackVersion();
        $commit = $this->git(['rev-parse', '--short', 'HEAD']);
        $exactTag = $this->exactVersionTag();
        $dirty = $this->hasDirtyPackageState();

        if ($exactTag !== null) {
            $version = $this->normalizeTag($exactTag);

            return [
                'label' => 'v' . $version . ($dirty ? '-dirty' : ''),
                'version' => $version,
                'commit' => $commit,
                'dirty' => $dirty,
                'source' => 'tag',
            ];
        }

        return [
            'label' => 'v' . $fallback . ($commit !== null ? '-dev.' . $commit : '-dev') . ($dirty ? '-dirty' : ''),
            'version' => $fallback,
            'commit' => $commit,
            'dirty' => $dirty,
            'source' => 'fallback',
        ];
    }

    public function label(): string
    {
        return $this->toArray()['label'];
    }

    private function fallbackVersion(): string
    {
        $version = trim((string) config('translation-workbench.version.fallback', '0.7.0'));

        return $version !== '' ? ltrim($version, 'v') : '0.7.0';
    }

    private function exactVersionTag(): ?string
    {
        foreach ((array) config('translation-workbench.version.tag_prefixes', ['translation-workbench/v', 'v']) as $prefix) {
            $tag = $this->git([
                'describe',
                '--tags',
                '--exact-match',
                '--match=' . rtrim((string) $prefix, '*') . '*',
                'HEAD',
            ]);

            if ($tag !== null) {
                return $tag;
            }
        }

        return null;
    }

    private function hasDirtyPackageState(): bool
    {
        $packagePath = trim((string) config('translation-workbench.version.package_path', 'packages/gunreip/laravel-translation-workbench'));

        if ($packagePath === '') {
            return false;
        }

        return $this->git(['status', '--short', '--', $packagePath]) !== null;
    }

    private function normalizeTag(string $tag): string
    {
        foreach ((array) config('translation-workbench.version.tag_prefixes', ['translation-workbench/v', 'v']) as $prefix) {
            $prefix = (string) $prefix;

            if ($prefix !== '' && str_starts_with($tag, $prefix)) {
                return ltrim(substr($tag, strlen($prefix)), 'v');
            }
        }

        return ltrim($tag, 'v');
    }

    /**
     * @param array<int, string> $arguments
     */
    private function git(array $arguments): ?string
    {
        try {
            $process = new Process(['git', ...$arguments], base_path());
            $process->setTimeout(2);
            $process->run();

            if (! $process->isSuccessful()) {
                return null;
            }

            $output = trim($process->getOutput());

            return $output !== '' ? $output : null;
        } catch (Throwable) {
            return null;
        }
    }
}
