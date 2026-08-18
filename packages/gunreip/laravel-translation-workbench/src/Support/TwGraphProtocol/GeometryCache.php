<?php

declare(strict_types=1);

namespace Gunreip\TranslationWorkbench\Support\TwGraphProtocol;

final class GeometryCache
{
    public function __construct(
        private readonly string $path,
        private readonly ?string $seedPath = null,
    ) {}

    public static function forDevPreview(): self
    {
        return new self(
            storage_path('translation-workbench/tw-graph-v2/geometry-cache.json'),
            base_path('packages/gunreip/laravel-translation-workbench/resources/dev/tw-graph-v2/geometry-cache-seed.json'),
        );
    }

    public function path(): string
    {
        return $this->path;
    }

    public function seedPath(): ?string
    {
        return $this->seedPath;
    }

    public function readSource(): string
    {
        if (is_file($this->path)) {
            return 'cache';
        }

        if ($this->seedPath && is_file($this->seedPath)) {
            return 'seed';
        }

        return 'empty';
    }

    public function read(): array
    {
        $path = is_file($this->path) ? $this->path : $this->seedPath;

        if (! $path || ! is_file($path)) {
            return [];
        }

        $decoded = json_decode((string) file_get_contents($path), true);

        return is_array($decoded) ? $decoded : [];
    }

    public function canWrite(): bool
    {
        $directory = dirname($this->path);

        if (is_file($this->path)) {
            return is_writable($this->path);
        }

        return is_dir($directory) && is_writable($directory);
    }

    public function write(array $protocol): bool
    {
        $directory = dirname($this->path);

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        if (! $this->canWrite()) {
            return false;
        }

        $written = file_put_contents(
            $this->path,
            json_encode($protocol, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL,
        );

        return $written !== false;
    }
}
