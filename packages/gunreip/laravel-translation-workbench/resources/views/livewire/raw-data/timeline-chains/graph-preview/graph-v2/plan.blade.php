{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/timeline-chains/graph-preview/graph-v2/plan.blade.php --}}
{{--
    Active graph-v2 plan.

    This is the only active place to edit the current component-authored graph
    intent. GeometryCache JSON stores calculated coordinates, not this plan.
--}}

@php
    $pathProtocolCache = \Gunreip\TranslationWorkbench\Support\TwGraphProtocol\GeometryCache::forDevPreview();
    $pathProtocolReadSource = $pathProtocolCache->readSource();
    $pathProtocolCachePath = $pathProtocolCache->path();
    $pathProtocolCacheDirectory = dirname($pathProtocolCachePath);
    $pathProtocolProcessUser = get_current_user();
    $pathProtocolProcessEffectiveUser =
        function_exists('posix_getpwuid') && function_exists('posix_geteuid')
            ? data_get(posix_getpwuid(posix_geteuid()), 'name', (string) posix_geteuid())
            : 'unknown';
    $pathProtocolProcessEffectiveGroup =
        function_exists('posix_getgrgid') && function_exists('posix_getegid')
            ? data_get(posix_getgrgid(posix_getegid()), 'name', (string) posix_getegid())
            : 'unknown';
    $pathProtocolCacheFileWritable = is_file($pathProtocolCachePath) && is_writable($pathProtocolCachePath);
    $pathProtocolCacheDirectoryWritable =
        is_dir($pathProtocolCacheDirectory) && is_writable($pathProtocolCacheDirectory);
    $cachedPathProtocol = $pathProtocolCache->read();

    // Active trunk controls.
    $trunkStartLength = '8rem';
    $trunkPathLengths = ['3rem', '4rem', '3rem', '4rem', '4rem', '10rem', '4rem', '4rem', '6rem', '4rem'];
    $trunkEndLength = '2.5rem';
    $trunkEndCapLength = '1.75rem';
    $trunkColor = 'green';
    $trunkDirection = 'bottom-top';
    $trunkTerminalTextConnectorLength = '2.25rem';
    $trunkTerminalTextConnectorGapStart = '1.0rem';
    $trunkTerminalTextConnectorGapEnd = '1.0rem';
    $trunkTextStartConnectorPlacement = $trunkDirection === 'top-bottom' ? 'top' : 'bottom';
    $trunkTextEndConnectorPlacement = $trunkDirection === 'top-bottom' ? 'bottom' : 'top';
    $mergeColor = 'amber';
    $mergeAttachSegmentId = 'trunk.path.1.start';
    $mergeArcSpan = '2.5rem';
    $mergeHorizontalLength = '6rem';
    $mergeVerticalLength = '2rem';
    $mergePath = function (string $side) use (
        $mergeArcSpan,
        $mergeColor,
        $mergeHorizontalLength,
        $mergeVerticalLength,
    ): array {
        $left = $side === 'left';

        return [
            'id' => "merge.{$side}",
            'anchorFrom' => [
                'segmentId' => null,
                'anchor' => 'anchorEnd',
            ],
            'paths' => [
                'merge' => [
                    'id' => "merge.{$side}.path.merge",
                    'type' => 'merge',
                    'segments' => [
                        [
                            'id' => "merge.{$side}.path.merge.1.arc-" . ($left ? 'se' : 'sw'),
                            'type' => 'arc',
                            'direction' => $left ? 'se' : 'sw',
                            'arcSpan' => $mergeArcSpan,
                            'color' => $mergeColor,
                        ],
                        [
                            'id' => "merge.{$side}.path.merge.2." . ($left ? 'right-left' : 'left-right'),
                            'type' => 'path',
                            'direction' => $left ? 'right-left' : 'left-right',
                            'length' => $mergeHorizontalLength,
                            'nodeStart' => false,
                            'nodeEnd' => false,
                            'color' => $mergeColor,
                        ],
                        [
                            'id' => "merge.{$side}.path.merge.3.arc-" . ($left ? 'nw' : 'ne'),
                            'type' => 'arc',
                            'direction' => $left ? 'nw' : 'ne',
                            'arcSpan' => $mergeArcSpan,
                            'color' => $mergeColor,
                        ],
                    ],
                ],
                'mergeEnd' => [
                    'id' => "merge.{$side}.path.merge-end",
                    'type' => 'merge-end',
                    'textEnd' => ['Merge end', $side],
                    'textEndAnchor' => 'anchorEnd',
                    'textEndConnectorPlacement' => 'bottom',
                    'textEndConnectorLength' => '1.5rem',
                    'textEndConnectorGap' => '0.35rem',
                    'segment' => [
                        'id' => "merge.{$side}.path.merge-end.1.top-bottom",
                        'type' => 'path',
                        'direction' => 'top-bottom',
                        'length' => $mergeVerticalLength,
                        'nodeStart' => false,
                        'nodeEnd' => true,
                        'color' => $mergeColor,
                    ],
                ],
            ],
        ];
    };

    $trunkStartSegment =
        $trunkStartLength !== '0'
            ? [
                'id' => 'trunk.path.1.start',
                'segment' => 'trunk-start',
                'type' => 'path',
                'direction' => $trunkDirection,
                'length' => $trunkStartLength,
                'nodeStart' => false,
                'nodeEnd' => true,
                'color' => $trunkColor,
            ]
            : null;

    $trunkPathSegments = collect($trunkPathLengths)
        ->filter(fn(string $length): bool => $length !== '0')
        ->values()
        ->map(function (string $length, int $index) use ($trunkDirection, $trunkColor): array {
            $segmentNumber = $index + 1;
            $segment = [
                'id' => 'trunk.path.1.path.' . $segmentNumber,
                'segment' => 'trunk-path',
                'type' => 'path',
                'direction' => $trunkDirection,
                'length' => $length,
                'nodeStart' => false,
                'nodeEnd' => true,
                'color' => $trunkColor,
            ];

            if ($segmentNumber === 10) {
                $segment['textLabels'] = [
                    [
                        'id' => 'trunk.path.1.path.' . $segmentNumber . '.text-label.left',
                        'side' => 'left',
                        'text' => ['Left', 'label an Node ' . $segmentNumber],
                        'connectorLength' => '2rem',
                        'connectorGap' => '0.25rem',
                        'color' => 'sky',
                        'badgeColor' => 'sky',
                    ],
                    [
                        'id' => 'trunk.path.1.path.' . $segmentNumber . '.text-label.right',
                        'side' => 'right',
                        'text' => ['Right', 'label an Node ' . $segmentNumber],
                        'connectorLength' => '2rem',
                        'connectorGap' => '0.25rem',
                        'color' => 'sky',
                        'badgeColor' => 'sky',
                    ],
                ];
            }

            if ($segmentNumber === 2) {
                $segment['textLabels'] = [
                    [
                        'id' => 'trunk.path.1.path.' . $segmentNumber . '.text-label.left',
                        'side' => 'left',
                        'text' => ['Left', 'label an Node ' . $segmentNumber],
                        'connectorLength' => '2rem',
                        'connectorGap' => '0.25rem',
                        'color' => 'sky',
                        'badgeColor' => 'sky',
                    ],
                    [
                        'id' => 'trunk.path.1.path.' . $segmentNumber . '.text-label.right',
                        'side' => 'right',
                        'text' => ['Right', 'label an Node ' . $segmentNumber],
                        'connectorLength' => '2rem',
                        'connectorGap' => '0.25rem',
                        'color' => 'sky',
                        'badgeColor' => 'sky',
                    ],
                ];
            }

            return $segment;
        })
        ->all();

    $trunkEndSegment =
        $trunkEndLength !== '0'
            ? [
                'id' => 'trunk.path.1.end',
                'segment' => 'trunk-end',
                'type' => 'path',
                'direction' => $trunkDirection,
                'length' => $trunkEndLength,
                'nodeStart' => false,
                'nodeEnd' => false,
                'capLength' => $trunkEndCapLength,
                'color' => $trunkColor,
            ]
            : null;

    $pathProtocolPlan = [
        'meta' => [
            'id' => 'geometry-cache-dev-preview',
            'version' => '0.2',
            'description' => 'Component-authored graph plan. JSON caches coordinates only.',
        ],
        'geometry' => [
            'unit' => 'rem',
            'color' => 'cyan',
            'direction' => $trunkDirection,
            'pathWidth' => '0.25rem',
            'nodeSize' => '0.95rem',
            'arcSize' => '2.75rem',
        ],
        'twGraph' => [
            'strang' => [
                'trunk' => [
                    'trunk' => [
                        'direction' => $trunkDirection,
                        'terminalTextConnectorLength' => $trunkTerminalTextConnectorLength,
                        'terminalTextConnectorGapStart' => $trunkTerminalTextConnectorGapStart,
                        'terminalTextConnectorGapEnd' => $trunkTerminalTextConnectorGapEnd,
                        'paths' => [
                            [
                                'id' => 'trunk.path.1',
                                'type' => 'trunk',
                                'direction' => $trunkDirection,
                                'textStart' => ['Root', 'direction=' . $trunkDirection, 'Test-ID: #701'],
                                'textStartConnectorPlacement' => $trunkTextStartConnectorPlacement,
                                'textStartConnectorLength' => $trunkTerminalTextConnectorLength,
                                'textStartConnectorGap' => $trunkTerminalTextConnectorGapStart,
                                'textEnd' => ['Shared key', 'direction=' . $trunkDirection, 'Test-ID: #5'],
                                'textEndConnectorPlacement' => $trunkTextEndConnectorPlacement,
                                'textEndConnectorLength' => $trunkTerminalTextConnectorLength,
                                'textEndConnectorGap' => $trunkTerminalTextConnectorGapEnd,
                                'segments' => [
                                    'start' => $trunkStartSegment,
                                    'paths' => $trunkPathSegments,
                                    'end' => $trunkEndSegment,
                                ],
                            ],
                        ],
                        'end' => null,
                    ],
                ],
                'merge' => [
                    'left' => [
                        'merge' => [
                            ...$mergePath('left'),
                            'anchorFrom' => [
                                'segmentId' => $mergeAttachSegmentId,
                                'anchor' => 'anchorEnd',
                            ],
                        ],
                    ],
                    'right' => [
                        'merge' => [
                            ...$mergePath('right'),
                            'anchorFrom' => [
                                'segmentId' => $mergeAttachSegmentId,
                                'anchor' => 'anchorEnd',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ];

    $pathProtocol = app(\Gunreip\TranslationWorkbench\Support\TwGraphProtocol\GeometryResolver::class)->resolve(
        $pathProtocolPlan,
        $cachedPathProtocol,
    );
    $pathProtocolWriteOk = $pathProtocolCache->write($pathProtocol);
    $pathProtocolSegmentCount =
        collect(data_get($pathProtocol, 'twGraph.strang.trunk.trunk.paths', []))->sum(function (array $path): int {
            return count(data_get($path, 'segments.start') ? [data_get($path, 'segments.start')] : []) +
                count(data_get($path, 'segments.paths', [])) +
                count(data_get($path, 'segments.end') ? [data_get($path, 'segments.end')] : []);
        }) +
        collect(['left', 'right'])->sum(function (string $side) use ($pathProtocol): int {
            return count(data_get($pathProtocol, "twGraph.strang.merge.{$side}.merge.paths.merge.segments", [])) +
                count(data_get($pathProtocol, "twGraph.strang.merge.{$side}.merge.paths.mergeEnd.segment") ? [data_get($pathProtocol, "twGraph.strang.merge.{$side}.merge.paths.mergeEnd.segment")] : []);
        });

    $graphV2 = [
        'cache' => $pathProtocolCache,
        'readSource' => $pathProtocolReadSource,
        'cachePath' => $pathProtocolCachePath,
        'cacheDirectory' => $pathProtocolCacheDirectory,
        'processUser' => $pathProtocolProcessUser,
        'processEffectiveUser' => $pathProtocolProcessEffectiveUser,
        'processEffectiveGroup' => $pathProtocolProcessEffectiveGroup,
        'cacheFileWritable' => $pathProtocolCacheFileWritable,
        'cacheDirectoryWritable' => $pathProtocolCacheDirectoryWritable,
        'protocol' => $pathProtocol,
        'writeOk' => $pathProtocolWriteOk,
        'segmentCount' => $pathProtocolSegmentCount,
        'trunkStartLength' => $trunkStartLength,
        'trunkPathSegments' => $trunkPathSegments,
        'trunkEndLength' => $trunkEndLength,
        'trunkEndCapLength' => $trunkEndCapLength,
        'trunkColor' => $trunkColor,
        'trunkDirection' => $trunkDirection,
        'trunkTerminalTextConnectorLength' => $trunkTerminalTextConnectorLength,
        'trunkTerminalTextConnectorGapStart' => $trunkTerminalTextConnectorGapStart,
        'trunkTerminalTextConnectorGapEnd' => $trunkTerminalTextConnectorGapEnd,
    ];

    view()->share('graphV2', $graphV2);
@endphp
