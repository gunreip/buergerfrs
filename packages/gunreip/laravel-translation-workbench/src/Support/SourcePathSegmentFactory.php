<?php

namespace Gunreip\TranslationWorkbench\Support;

class SourcePathSegmentFactory
{
    /**
     * Split a source path into stable, filterable segments.
     *
     * The source file row remains the canonical path entity. Other tables
     * should use source_file_id and read these segments through joins instead
     * of storing their own path breakdown.
     *
     * @return array{
     *     source_root: string|null,
     *     source_area: string|null,
     *     package_vendor: string|null,
     *     package_name: string|null,
     *     path_domain: string|null,
     *     path_section: string|null,
     *     path_context: string|null,
     *     path_scope: string|null,
     *     path_extra: string|null,
     *     filename: string|null
     * }
     */
    public function fromPath(?string $path): array
    {
        $segments = collect(explode('/', str_replace('\\', '/', trim((string) $path, '/'))))
            ->map(fn(string $segment): string => $this->normalizeSegment($segment))
            ->filter(static fn(string $segment): bool => $segment !== '')
            ->values();

        $sourceRoot = $segments->get(0);
        $packageVendor = null;
        $packageName = null;
        $offset = 1;

        if ($sourceRoot === 'packages') {
            $packageVendor = $segments->get(1);
            $packageName = $segments->get(2);
            $offset = 3;
        }

        $remaining = $segments->slice($offset)->values();
        $filename = $this->filename($remaining->pop());
        $area = $this->area($sourceRoot, $remaining);
        $pathSegments = $remaining->slice($area['consumed'])->values();

        return [
            'source_root' => $this->nullable($sourceRoot),
            'source_area' => $this->nullable($area['value']),
            'package_vendor' => $this->nullable($packageVendor),
            'package_name' => $this->nullable($packageName),
            'path_domain' => $this->nullable($pathSegments->get(0)),
            'path_section' => $this->nullable($pathSegments->get(1)),
            'path_context' => $this->nullable($pathSegments->get(2)),
            'path_scope' => $this->nullable($pathSegments->get(3)),
            'path_extra' => $pathSegments->count() > 4 ? $pathSegments->slice(4)->implode('.') : null,
            'filename' => $this->nullable($filename),
        ];
    }

    /**
     * @return array{value: string|null, consumed: int}
     */
    private function area(?string $sourceRoot, mixed $remaining): array
    {
        if ($remaining->isEmpty()) {
            return ['value' => null, 'consumed' => 0];
        }

        if (in_array($sourceRoot, ['resources', 'packages'], true)
            && $remaining->get(0) === 'resources'
            && $remaining->count() > 1) {
            return ['value' => $remaining->slice(0, 2)->implode('.'), 'consumed' => 2];
        }

        if ($sourceRoot === 'resources' && $remaining->get(0) === 'views' && $remaining->count() > 1) {
            return ['value' => $remaining->slice(0, 2)->implode('.'), 'consumed' => 2];
        }

        return ['value' => $remaining->get(0), 'consumed' => 1];
    }

    private function filename(?string $filename): ?string
    {
        if ($filename === null || $filename === '') {
            return null;
        }

        return $this->normalizeSegment(preg_replace('/\.blade\.php$|\.php$|\.json$|\.css$|\.js$/', '', $filename) ?? $filename);
    }

    private function normalizeSegment(string $segment): string
    {
        $segment = str_replace('⚡', '', $segment);
        $segment = preg_replace('/\.blade\.php$|\.php$|\.json$|\.css$|\.js$/', '', $segment) ?? $segment;
        $segment = str($segment)->snake()->toString();

        return trim($segment, '._-');
    }

    private function nullable(?string $segment): ?string
    {
        $segment = trim((string) $segment);

        return $segment !== '' ? $segment : null;
    }
}
