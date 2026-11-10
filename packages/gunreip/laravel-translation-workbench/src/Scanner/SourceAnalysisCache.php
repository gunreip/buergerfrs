<?php

namespace Gunreip\TranslationWorkbench\Scanner;

use Composer\InstalledVersions;
use Illuminate\Support\Collection;
use Throwable;

/** Disposable extraction results only; never stores database reconciliation decisions. */
final class SourceAnalysisCache
{
    public function __construct(private string $directory, private string $revision) {}

    public static function configured(): ?self
    {
        if (! config('translation-workbench.analysis_cache.enabled', true)) {
            return null;
        }

        try {
            // Include extraction helpers and installed dependency versions, not just the scanner.
            $files = glob(__DIR__.'/*.php');
            sort($files);
            $hashes = array_map(static fn (string $file) => @hash_file('sha256', $file), $files);
            if (in_array(false, $hashes, true)) {
                return null;
            }
            $revision = hash('sha256', serialize([
                PHP_VERSION, base_path(), config('translation-workbench'), $hashes, InstalledVersions::getAllRawData(),
            ]));
            $cache = new self(
                config('translation-workbench.analysis_cache.path') ?? storage_path('framework/cache/workbench-source-analysis'),
                $revision,
            );
            $cache->prune();

            return $cache;
        } catch (Throwable) {
            return null; // Cache availability must never determine whether a source scan succeeds.
        }
    }

    public function remember(string $path, string $contents, callable $analyze): Collection
    {
        // One slot per source path; edits replace the previous result instead of accumulating versions.
        $file = $this->directory.'/'.hash('sha256', $path).'.json';
        $signature = hash('sha256', $this->revision."\0".$path."\0".$contents);
        try {
            if (is_file($file) && filemtime($file) >= time() - 604800) {
                $cached = json_decode(@file_get_contents($file), true, flags: JSON_THROW_ON_ERROR);
                if (($cached['signature'] ?? null) === $signature && is_array($cached['items'] ?? null)) {
                    return collect($cached['items'])->map(static fn (array $row) => new DiscoveredTranslation(...$row));
                }
            }
        } catch (Throwable) {
            // Missing, unreadable or damaged cache: analyze the current bytes normally.
        }

        $items = $analyze(); // Extraction failures must propagate; never substitute older results.
        $temporary = null;
        try {
            if (! is_dir($this->directory)) {
                @mkdir($this->directory, 0775, true);
            }
            if (! is_dir($this->directory) || ! is_writable($this->directory)) {
                return $items;
            }
            $json = json_encode(['signature' => $signature, 'items' => $items->map(
                static fn (DiscoveredTranslation $item) => get_object_vars($item),
            )->all()], JSON_THROW_ON_ERROR);
            $temporary = @tempnam($this->directory, 'analysis-');
            if ($temporary !== false && @file_put_contents($temporary, $json) === strlen($json)) {
                @chmod($temporary, 0660);
                @rename($temporary, $file);
            }
        } catch (Throwable) {
            // This optimization is optional, including on read-only installations.
        } finally {
            if ($temporary && is_file($temporary)) {
                @unlink($temporary);
            }
        }

        return $items;
    }

    private function prune(): void
    {
        // Also removes abandoned temporary files; no database history is involved.
        foreach (glob($this->directory.'/*') ?: [] as $file) {
            if (preg_match('/^(?:[a-f0-9]{64}\.json|analysis-[a-zA-Z0-9]+)$/', basename($file))
                && is_file($file) && filemtime($file) < time() - 604800) {
                @unlink($file);
            }
        }
    }
}
