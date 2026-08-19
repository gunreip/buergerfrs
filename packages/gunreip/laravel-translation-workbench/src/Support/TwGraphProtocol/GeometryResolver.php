<?php

declare(strict_types=1);

namespace Gunreip\TranslationWorkbench\Support\TwGraphProtocol;

final class GeometryResolver
{
    /**
     * Resolve a graph plan into a render protocol.
     *
     * The plan is the source of truth for visual intent. The cached protocol is
     * only trusted for coordinates when the segment fingerprint still matches.
     */
    public function resolve(array $plan, array $cachedProtocol = []): array
    {
        $protocol = [
            'meta' => data_get($plan, 'meta', data_get($cachedProtocol, 'meta', [])),
            'geometry' => data_get($plan, 'geometry', data_get($cachedProtocol, 'geometry', [])),
            'twGraph' => [
                'strang' => [
                    'trunk' => [
                        'trunk' => [
                            'paths' => [],
                            'end' => data_get($plan, 'twGraph.strang.trunk.trunk.end'),
                        ],
                    ],
                    'merge' => data_get($plan, 'twGraph.strang.merge', []),
                    'branch' => data_get($plan, 'twGraph.strang.branch', []),
                ],
            ],
        ];

        $cachedSegments = $this->cachedSegmentsById($cachedProtocol);
        $cachedPaths = $this->cachedPathsById($cachedProtocol);

        foreach (data_get($plan, 'twGraph.strang.trunk.trunk.paths', []) as $pathIndex => $path) {
            $resolvedPath = $path;
            $pathId = (string) data_get($path, 'id', 'trunk.path.' . ($pathIndex + 1));
            $pathFingerprint = $this->pathFingerprint($path);
            $cachedPath = $cachedPaths[$pathId] ?? null;
            $useCachedPathCoordinates = is_array($cachedPath) && data_get($cachedPath, '_fingerprint') === $pathFingerprint;
            $resolvedPath['segments'] = [
                'start' => null,
                'paths' => [],
                'end' => null,
            ];
            $resolvedPath['_fingerprint'] = $pathFingerprint;

            $cursor = ['x' => '0rem', 'y' => '0rem'];
            $start = data_get($path, 'segments.start');

            if (is_array($start)) {
                $resolvedStart = $this->resolveSegment($start, $cachedSegments, $cursor, $useCachedPathCoordinates);
                $resolvedPath['segments']['start'] = $resolvedStart;
                $cursor = data_get($resolvedStart, 'anchorEnd', $cursor);
            }

            foreach (data_get($path, 'segments.paths', []) as $segment) {
                if (! is_array($segment)) {
                    continue;
                }

                $resolvedSegment = $this->resolveSegment($segment, $cachedSegments, $cursor, $useCachedPathCoordinates);
                $resolvedPath['segments']['paths'][] = $resolvedSegment;
                $cursor = data_get($resolvedSegment, 'anchorEnd', $cursor);
            }

            $end = data_get($path, 'segments.end');

            if (is_array($end)) {
                $resolvedEnd = $this->resolveSegment($end, $cachedSegments, $cursor, $useCachedPathCoordinates);
                $resolvedPath['segments']['end'] = $resolvedEnd;
            }

            $protocol['twGraph']['strang']['trunk']['trunk']['paths'][$pathIndex] = $resolvedPath;
        }

        $resolvedSegments = $this->segmentsById($protocol);

        foreach (['left', 'right'] as $side) {
            $merge = data_get($plan, "twGraph.strang.merge.{$side}.merge");

            if (! is_array($merge)) {
                $protocol['twGraph']['strang']['merge'][$side] = data_get($plan, "twGraph.strang.merge.{$side}");
                continue;
            }

            $protocol['twGraph']['strang']['merge'][$side]['merge'] = $this->resolveMergeChain(
                $merge,
                $cachedSegments,
                $resolvedSegments,
            );
        }

        $resolvedSegments = $this->segmentsById($protocol);

        foreach (['left', 'right'] as $side) {
            foreach (data_get($plan, "twGraph.strang.branch.{$side}", []) as $branchKey => $branch) {
                if (! is_array($branch)) {
                    $protocol['twGraph']['strang']['branch'][$side][$branchKey] = $branch;
                    continue;
                }

                $resolvedBranch = $this->resolveSegmentChain($branch, $cachedSegments, $resolvedSegments);
                $protocol['twGraph']['strang']['branch'][$side][$branchKey] = $resolvedBranch;

                foreach (data_get($resolvedBranch, 'segments', []) as $segment) {
                    if (is_array($segment) && filled(data_get($segment, 'id'))) {
                        $resolvedSegments[(string) data_get($segment, 'id')] = $segment;
                    }
                }
            }
        }

        return $protocol;
    }

    private function resolveMergeChain(array $chain, array $cachedSegments, array $resolvedSegments): array
    {
        if (! data_get($chain, 'paths')) {
            return $this->resolveSegmentChain($chain, $cachedSegments, $resolvedSegments);
        }

        $resolvedChain = $chain;
        $cursor = $this->anchorFrom($chain, $resolvedSegments);
        $resolvedChain['paths']['merge']['segments'] = [];

        foreach (data_get($chain, 'paths.merge.segments', []) as $segment) {
            if (! is_array($segment)) {
                continue;
            }

            $resolvedSegment = $this->resolveSegment($segment, $cachedSegments, $cursor, false);
            $resolvedChain['paths']['merge']['segments'][] = $resolvedSegment;
            $resolvedSegments[(string) data_get($resolvedSegment, 'id')] = $resolvedSegment;
            $cursor = data_get($resolvedSegment, 'anchorEnd', $cursor);
        }

        $endSegment = data_get($chain, 'paths.mergeEnd.segment');

        if (is_array($endSegment)) {
            $resolvedEndSegment = $this->resolveSegment($endSegment, $cachedSegments, $cursor, false);
            $resolvedChain['paths']['mergeEnd']['segment'] = $resolvedEndSegment;
            $resolvedSegments[(string) data_get($resolvedEndSegment, 'id')] = $resolvedEndSegment;
        }

        return $resolvedChain;
    }

    private function resolveSegmentChain(array $chain, array $cachedSegments, array $resolvedSegments): array
    {
        $resolvedChain = $chain;
        $resolvedChain['segments'] = [];
        $cursor = $this->anchorFrom($chain, $resolvedSegments);

        foreach (data_get($chain, 'segments', []) as $segment) {
            if (! is_array($segment)) {
                continue;
            }

            $resolvedSegment = $this->resolveSegment($segment, $cachedSegments, $cursor, false);
            $resolvedChain['segments'][] = $resolvedSegment;
            $resolvedSegments[(string) data_get($resolvedSegment, 'id')] = $resolvedSegment;
            $cursor = data_get($resolvedSegment, 'anchorEnd', $cursor);
        }

        return $resolvedChain;
    }

    private function anchorFrom(array $chain, array $resolvedSegments): array
    {
        $segmentId = data_get($chain, 'anchorFrom.segmentId');

        if (filled($segmentId) && isset($resolvedSegments[(string) $segmentId])) {
            return data_get(
                $resolvedSegments[(string) $segmentId],
                data_get($chain, 'anchorFrom.anchor', 'anchorEnd'),
                ['x' => '0rem', 'y' => '0rem'],
            );
        }

        return data_get($chain, 'anchorStart', ['x' => '0rem', 'y' => '0rem']);
    }

    private function resolveSegment(array $segment, array $cachedSegments, array $fallbackStart, bool $useCache): array
    {
        $id = (string) data_get($segment, 'id');
        $fingerprint = $this->fingerprint($segment);
        $cached = $cachedSegments[$id] ?? null;

        if ($useCache && is_array($cached) && data_get($cached, '_fingerprint') === $fingerprint) {
            $segment['anchorStart'] = data_get($cached, 'anchorStart', data_get($segment, 'anchorStart', $fallbackStart));
            $segment['anchorEnd'] = data_get($cached, 'anchorEnd', data_get($segment, 'anchorEnd', $fallbackStart));
            $segment['_fingerprint'] = $fingerprint;

            return $segment;
        }

        $start = data_get($segment, 'anchorStart', $fallbackStart);
        $segment['anchorStart'] = $start;
        $segment['anchorEnd'] = $this->calculateAnchorEnd($segment, $start);
        $segment['_fingerprint'] = $fingerprint;

        return $segment;
    }

    private function cachedPathsById(array $protocol): array
    {
        $paths = [];

        foreach (data_get($protocol, 'twGraph.strang.trunk.trunk.paths', []) as $path) {
            if (is_array($path) && filled(data_get($path, 'id'))) {
                $paths[(string) data_get($path, 'id')] = $path;
            }
        }

        return $paths;
    }

    private function calculateAnchorEnd(array $segment, array $start): array
    {
        if (data_get($segment, 'type') === 'arc' && filled(data_get($segment, 'startAnchor')) && filled(data_get($segment, 'endAnchor'))) {
            return $this->calculateSemanticArcAnchorEnd($segment, $start);
        }

        $length = $this->toRem(data_get($segment, 'length', '0rem'));
        $x = $this->toRem(data_get($start, 'x', '0rem'));
        $y = $this->toRem(data_get($start, 'y', '0rem'));

        return match (data_get($segment, 'direction', 'bottom-top')) {
            'bottom-top' => ['x' => $this->rem($x), 'y' => $this->rem($y + $length)],
            'top-bottom' => ['x' => $this->rem($x), 'y' => $this->rem($y - $length)],
            'left-right' => ['x' => $this->rem($x + $length), 'y' => $this->rem($y)],
            'right-left' => ['x' => $this->rem($x - $length), 'y' => $this->rem($y)],
            'se' => ['x' => $this->rem($x - $this->arcSpan($segment)), 'y' => $this->rem($y - $this->arcSpan($segment))],
            'sw' => ['x' => $this->rem($x + $this->arcSpan($segment)), 'y' => $this->rem($y - $this->arcSpan($segment))],
            'nw' => ['x' => $this->rem($x - $this->arcSpan($segment)), 'y' => $this->rem($y - $this->arcSpan($segment))],
            'ne' => ['x' => $this->rem($x + $this->arcSpan($segment)), 'y' => $this->rem($y - $this->arcSpan($segment))],
            default => data_get($segment, 'anchorEnd', $start),
        };
    }

    private function calculateSemanticArcAnchorEnd(array $segment, array $start): array
    {
        $span = $this->arcSpan($segment);
        $x = $this->toRem(data_get($start, 'x', '0rem'));
        $y = $this->toRem(data_get($start, 'y', '0rem'));
        $pair = data_get($segment, 'startAnchor') . '-' . data_get($segment, 'endAnchor');

        return match ($pair) {
            'n-e' => ['x' => $this->rem($x + $span), 'y' => $this->rem($y - $span)],
            'e-n' => ['x' => $this->rem($x - $span), 'y' => $this->rem($y + $span)],
            'n-w' => ['x' => $this->rem($x - $span), 'y' => $this->rem($y - $span)],
            'w-n' => ['x' => $this->rem($x + $span), 'y' => $this->rem($y + $span)],
            's-e' => ['x' => $this->rem($x + $span), 'y' => $this->rem($y + $span)],
            'e-s' => ['x' => $this->rem($x - $span), 'y' => $this->rem($y - $span)],
            's-w' => ['x' => $this->rem($x - $span), 'y' => $this->rem($y + $span)],
            'w-s' => ['x' => $this->rem($x + $span), 'y' => $this->rem($y - $span)],
            default => data_get($segment, 'anchorEnd', $start),
        };
    }

    private function arcSpan(array $segment): float
    {
        return $this->toRem(data_get($segment, 'arcSpan', '2.5rem'));
    }

    private function cachedSegmentsById(array $protocol): array
    {
        $segments = [];

        foreach (data_get($protocol, 'twGraph.strang.trunk.trunk.paths', []) as $path) {
            foreach ([
                data_get($path, 'segments.start'),
                ...data_get($path, 'segments.paths', []),
                data_get($path, 'segments.end'),
            ] as $segment) {
                if (is_array($segment) && filled(data_get($segment, 'id'))) {
                    $segments[(string) data_get($segment, 'id')] = $segment;
                }
            }
        }

        return $segments;
    }

    private function segmentsById(array $protocol): array
    {
        $segments = [];

        foreach (data_get($protocol, 'twGraph.strang.trunk.trunk.paths', []) as $path) {
            foreach ([
                data_get($path, 'segments.start'),
                ...data_get($path, 'segments.paths', []),
                data_get($path, 'segments.end'),
            ] as $segment) {
                if (is_array($segment) && filled(data_get($segment, 'id'))) {
                    $segments[(string) data_get($segment, 'id')] = $segment;
                }
            }
        }

        foreach (['left', 'right'] as $side) {
            foreach (data_get($protocol, "twGraph.strang.merge.{$side}.merge.paths.merge.segments", []) as $segment) {
                if (is_array($segment) && filled(data_get($segment, 'id'))) {
                    $segments[(string) data_get($segment, 'id')] = $segment;
                }
            }

            $mergeEndSegment = data_get($protocol, "twGraph.strang.merge.{$side}.merge.paths.mergeEnd.segment");
            if (is_array($mergeEndSegment) && filled(data_get($mergeEndSegment, 'id'))) {
                $segments[(string) data_get($mergeEndSegment, 'id')] = $mergeEndSegment;
            }

            foreach (data_get($protocol, "twGraph.strang.branch.{$side}", []) as $branch) {
                foreach (data_get($branch, 'segments', []) as $segment) {
                    if (is_array($segment) && filled(data_get($segment, 'id'))) {
                        $segments[(string) data_get($segment, 'id')] = $segment;
                    }
                }
            }
        }

        return $segments;
    }

    private function fingerprint(array $segment): string
    {
        return sha1(json_encode([
            'id' => data_get($segment, 'id'),
            'segment' => data_get($segment, 'segment'),
            'type' => data_get($segment, 'type'),
            'direction' => data_get($segment, 'direction'),
            'startAnchor' => data_get($segment, 'startAnchor'),
            'endAnchor' => data_get($segment, 'endAnchor'),
            'length' => data_get($segment, 'length'),
            'arcSpan' => data_get($segment, 'arcSpan'),
            'nodeStart' => data_get($segment, 'nodeStart'),
            'nodeEnd' => data_get($segment, 'nodeEnd'),
            'capLength' => data_get($segment, 'capLength'),
            'textLabels' => data_get($segment, 'textLabels'),
        ], JSON_THROW_ON_ERROR));
    }

    private function pathFingerprint(array $path): string
    {
        $segments = collect([
            data_get($path, 'segments.start'),
            ...data_get($path, 'segments.paths', []),
            data_get($path, 'segments.end'),
        ])
            ->filter(fn ($segment): bool => is_array($segment))
            ->map(fn (array $segment): array => [
                'id' => data_get($segment, 'id'),
                'segment' => data_get($segment, 'segment'),
                'type' => data_get($segment, 'type'),
                'direction' => data_get($segment, 'direction'),
                'length' => data_get($segment, 'length'),
                'nodeStart' => data_get($segment, 'nodeStart'),
                'nodeEnd' => data_get($segment, 'nodeEnd'),
                'capLength' => data_get($segment, 'capLength'),
                'textLabels' => data_get($segment, 'textLabels'),
            ])
            ->values()
            ->all();

        return sha1(json_encode([
            'id' => data_get($path, 'id'),
            'type' => data_get($path, 'type'),
            'direction' => data_get($path, 'direction'),
            'textStart' => data_get($path, 'textStart'),
            'textStartConnectorPlacement' => data_get($path, 'textStartConnectorPlacement'),
            'textStartConnectorLength' => data_get($path, 'textStartConnectorLength'),
            'textStartConnectorGap' => data_get($path, 'textStartConnectorGap'),
            'textEnd' => data_get($path, 'textEnd'),
            'textEndConnectorPlacement' => data_get($path, 'textEndConnectorPlacement'),
            'textEndConnectorLength' => data_get($path, 'textEndConnectorLength'),
            'textEndConnectorGap' => data_get($path, 'textEndConnectorGap'),
            'segments' => $segments,
        ], JSON_THROW_ON_ERROR));
    }

    private function toRem(mixed $value): float
    {
        return (float) str_replace('rem', '', (string) $value);
    }

    private function rem(float $value): string
    {
        if (abs($value) < 0.0001) {
            $value = 0.0;
        }

        return rtrim(rtrim(number_format($value, 4, '.', ''), '0'), '.') . 'rem';
    }
}
