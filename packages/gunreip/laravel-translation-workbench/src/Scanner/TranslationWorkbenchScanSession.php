<?php

namespace Gunreip\TranslationWorkbench\Scanner;

use Closure;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

/** One observed source state per pipeline phase; never a cache shared across runs. */
class TranslationWorkbenchScanSession
{
    private bool $active = false;

    private array $paths = [];

    private array $knownPaths = [];

    private ?array $manifest = null;

    private ?Throwable $failure = null;

    private array $references = [];

    private ?int $pipelineRunId = null;

    private string $phase = 'initial';

    public function begin(?array $paths, array $knownPaths, ?int $pipelineRunId = null): void
    {
        if ($this->active) {
            throw new RuntimeException('A translation scan session is already active.');
        }
        $this->active = true;
        $this->paths = $this->normalize($paths ?? config('translation-workbench.paths', []));
        $this->knownPaths = $knownPaths;
        $this->pipelineRunId = $pipelineRunId;
    }

    public function failed(): bool
    {
        return $this->failure !== null;
    }

    public function active(): bool
    {
        return $this->active;
    }

    public function manifest(): ?array
    {
        return $this->manifest === null ? null : [...$this->manifest, 'items' => clone $this->manifest['items']];
    }

    public function references(): array
    {
        return $this->references;
    }

    public function paths(): array
    {
        return $this->paths;
    }

    public function nextPhase(string $phase): void
    {
        $this->manifest = null;
        $this->failure = null;
        $this->phase = $phase;
    }

    public function end(): void
    {
        $this->active = false;
        $this->paths = $this->knownPaths = $this->references = [];
        $this->manifest = null;
        $this->failure = null;
        $this->pipelineRunId = null;
        $this->phase = 'initial';
    }

    public function resolve(?array $paths, Closure $capture): array
    {
        if ($paths !== null && $this->normalize($paths) !== $this->paths) {
            throw new RuntimeException('Scan scope differs from the active pipeline snapshot. Start a separate scan.');
        }
        if ($this->failure !== null) {
            throw $this->failure;
        }
        if ($this->manifest !== null) {
            return [...$this->manifest, 'items' => clone $this->manifest['items']];
        }
        $id = (string) Str::uuid();
        $record = [
            'schema_version' => 1, 'id' => $id, 'pipeline_run_id' => $this->pipelineRunId,
            'phase' => $this->phase, 'started_at' => now()->toISOString(),
            'requested_paths' => $this->paths,
            'configuration' => collect(config('translation-workbench', []))->only([
                'paths', 'exclude_paths', 'file_patterns', 'ignored_filename_contains', 'translation_functions',
            ])->all(),
        ];
        try {
            $manifest = $capture($this->paths, $this->knownPaths);
            $record += ['status' => 'completed', 'finished_at' => now()->toISOString(), 'result' => [
                ...$manifest,
                'items' => $manifest['items']->map(fn (DiscoveredTranslation $item): array => get_object_vars($item))->all(),
            ]];
            $path = $this->persist($id, $record);
            $reference = ['id' => $id, 'status' => 'completed', 'phase' => $this->phase, 'path' => $path];
            $this->references[] = $reference;
            $this->manifest = [...$manifest, 'scan_run' => $reference, 'requested_paths' => $this->paths,
                'file_patterns' => $record['configuration']['file_patterns'] ?? []];

            return [...$this->manifest, 'items' => clone $this->manifest['items']];
        } catch (Throwable $exception) {
            $this->failure = $exception;
            // Failed captures are recorded once and never retried implicitly by a report.
            $record['status'] = 'failed';
            unset($record['result']);
            $record['finished_at'] = now()->toISOString();
            $record['error'] = ['class' => $exception::class, 'message' => $exception->getMessage()];
            try {
                $path = $this->persist($id.'-failed', $record);
                $this->references[] = ['id' => $id, 'status' => 'failed', 'phase' => $this->phase, 'path' => $path];
            } catch (Throwable $storageFailure) {
                $this->references[] = ['id' => $id, 'status' => 'failed', 'phase' => $this->phase,
                    'path' => null, 'storage_error' => $storageFailure->getMessage()];
            }
            throw $exception;
        }
    }

    private function persist(string $id, array $record): string
    {
        $directory = storage_path('translation-workbench/scan-runs');
        File::ensureDirectoryExists($directory, 0700);
        $path = $directory.'/'.$id.'.json';
        $temporary = $path.'.tmp';
        $json = json_encode($record, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        try {
            if (File::put($temporary, $json) !== strlen($json) || ! chmod($temporary, 0600) || ! rename($temporary, $path)) {
                throw new RuntimeException('Cannot persist translation scan snapshot: '.$path);
            }
        } finally {
            if (is_file($temporary)) {
                unlink($temporary);
            }
        }

        return $path;
    }

    private function normalize(array $paths): array
    {
        $paths = array_values(array_unique(array_filter(array_map(
            fn (string $path): string => rtrim(str_replace('\\', '/', trim($path)), '/'), $paths,
        ))));
        sort($paths);

        return $paths;
    }
}
