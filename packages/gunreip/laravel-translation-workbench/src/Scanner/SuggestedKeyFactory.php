<?php

namespace Gunreip\TranslationWorkbench\Scanner;

class SuggestedKeyFactory
{
    private const MAX_KEY_LENGTH = 220;

    private const MAX_KEY_SEGMENT_LENGTH = 96;

    public function forLiteral(string $literal, string $sourcePath): SuggestedKeyResult
    {
        $pathSegments = $this->pathSegments($sourcePath);
        $keyName = $this->shortenKeySegment($this->slugSegment($literal));

        return $this->forPathAndKeyName($pathSegments, $keyName);
    }

    public function forExistingKeyAtSource(string $existingKey, string $sourcePath): SuggestedKeyResult
    {
        $segments = collect(explode('.', trim($existingKey, '.')))
            ->map(fn (string $segment): string => $this->slugSegment($segment))
            ->filter()
            ->values()
            ->all();

        $keyName = $this->shortenKeySegment((string) (end($segments) ?: 'translation'));

        return $this->forPathAndKeyName(
            pathSegments: $this->pathSegments($sourcePath),
            keyName: $keyName,
        );
    }

    public function forDynamicExpressionAtSource(string $keyName, string $sourcePath): SuggestedKeyResult
    {
        $keyName = $this->shortenKeySegment($this->slugSegment($keyName));

        return $this->forPathAndKeyName(
            pathSegments: $this->pathSegments($sourcePath),
            keyName: $keyName !== '' ? $keyName : 'dynamic_value',
        );
    }

    public function forExistingKey(string $key): SuggestedKeyResult
    {
        $key = $this->shortenKeyCandidate(trim($key, '.'));
        $segments = explode('.', $key);

        return new SuggestedKeyResult(
            key: $key,
            namespace: $this->namespaceFromSegments($segments),
            group: $this->groupFromSegments($segments),
            pathSegments: $segments,
            keyName: $segments !== [] ? end($segments) : null,
        );
    }

    /**
     * @return array<int, string>
     */
    private function pathSegments(string $sourcePath): array
    {
        $path = str_replace('\\', '/', $sourcePath);
        $path = preg_replace('/\.blade\.php$|\.php$/', '', $path) ?? $path;

        foreach ([
            'resources/views/components/',
            'resources/views/',
            'routes/',
            'app/',
        ] as $prefix) {
            if (str_starts_with($path, $prefix)) {
                $path = substr($path, strlen($prefix));
                break;
            }
        }

        return collect(explode('/', $path))
            ->map(fn (string $segment): string => $this->shortenKeySegment($this->slugSegment($segment)))
            ->filter()
            ->values()
            ->all();
    }

    private function namespaceFromSegments(array $segments): ?string
    {
        return isset($segments[0]) && $segments[0] !== ''
            ? $this->shortenKeySegment((string) $segments[0])
            : null;
    }

    private function groupFromSegments(array $segments): ?string
    {
        return isset($segments[1]) && $segments[1] !== ''
            ? $this->shortenKeySegment((string) $segments[1])
            : null;
    }

    /**
     * @param  array<int, string>  $pathSegments
     */
    private function forPathAndKeyName(array $pathSegments, string $keyName): SuggestedKeyResult
    {
        $key = $this->shortenKeyCandidate(implode('.', array_filter([
            ...$pathSegments,
            $keyName,
        ])));

        return new SuggestedKeyResult(
            key: $key,
            namespace: $this->namespaceFromSegments($pathSegments),
            group: $this->groupFromSegments($pathSegments),
            pathSegments: $pathSegments,
            keyName: $keyName,
        );
    }

    private function slugSegment(string $value): string
    {
        $value = str_replace('⚡', '', $value);
        $value = preg_replace('/([a-z])([A-Z])/', '$1_$2', $value) ?? $value;
        $value = strtolower($value);
        $value = preg_replace('/[^a-z0-9]+/', '_', $value) ?? $value;

        return trim($value, '_');
    }

    private function shortenKeyCandidate(string $key): string
    {
        $key = trim($key, '.');

        if (strlen($key) <= self::MAX_KEY_LENGTH) {
            return $key;
        }

        $hash = substr(hash('sha256', $key), 0, 12);
        $prefix = rtrim(substr($key, 0, self::MAX_KEY_LENGTH - 13), '._-');

        return "{$prefix}_{$hash}";
    }

    private function shortenKeySegment(string $segment): string
    {
        $segment = trim($segment, '._-');

        if (strlen($segment) <= self::MAX_KEY_SEGMENT_LENGTH) {
            return $segment;
        }

        $hash = substr(hash('sha256', $segment), 0, 10);
        $prefix = rtrim(substr($segment, 0, self::MAX_KEY_SEGMENT_LENGTH - 11), '._-');

        return "{$prefix}_{$hash}";
    }
}
