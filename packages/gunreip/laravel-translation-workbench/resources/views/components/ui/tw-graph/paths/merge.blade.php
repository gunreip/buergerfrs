{{-- packages/gunreip/laravel-translation-workbench/resources/views/components/ui/tw-graph/paths/merge.blade.php --}}
{{--
    Path: merge

    Usage:
    <x-translation-workbench::ui.tw-graph.paths.merge
        side="left"
        :anchor-start="['x' => '0rem', 'y' => '0.75rem']"
        bridge-length="3rem"
        stem-length="2rem"
        :stem-continuation="[1 => '2rem']"
        :node-labels="[1 => ['right' => 'Source'], 5 => ['left' => 'Attach']]"
    />

    Path role:
    Merge owns one inbound side path and calculates its anchor chain from
    branch origin to trunk attach point:
    start -> stem1 -> optional stem2/stem3/... -> arc-in -> bridge -> arc-out.
--}}

@props([
    'id' => 'path.merge',
    'side' => 'left',
    'anchorStart' => ['x' => '0rem', 'y' => '0rem'],
    'startLength' => null,
    'lineLength' => '4rem',
    'lineWidth' => '0.25rem',
    'arcSize' => '2.75rem',
    'arcSizes' => [],
    'bridgeLength' => null,
    'stemLength' => null,
    'stemContinuation' => [],
    'startLabel' => null,
    'color' => 'amber',
    'zIndex' => null,
    'nodeLabels' => [],
    'counterStart' => 1,
    'dev' => false,
    'showDevBox' => true,
])

@php
    $add = fn (string $value, string $delta): string => $delta === '0rem' ? $value : 'calc(' . $value . ' + ' . $delta . ')';
    $neg = fn (string $value): string => 'calc(' . $value . ' * -1)';
    $currentAnchor = [
        'x' => data_get($anchorStart, 'x', '0rem'),
        'y' => data_get($anchorStart, 'y', '0rem'),
    ];
    $counter = (int) $counterStart;
    $isLeft = $side === 'left';
    $resolvedLineLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::localOrFallback($lineLength ?? null, '4rem');
    $resolvedLineWidth = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::localOrFallback($lineWidth ?? null, '0.25rem');
    $resolvedArcSize = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::localOrFallback($arcSize ?? null, '2.75rem');
    $resolvedArcInSize = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(
        data_get($arcSizes, 1, data_get($arcSizes, 'in')),
        $resolvedArcSize,
        '2.75rem',
    );
    $resolvedArcOutSize = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(
        data_get($arcSizes, 2, data_get($arcSizes, 'out')),
        $resolvedArcSize,
        '2.75rem',
    );
    $resolvedBridgeLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($bridgeLength, $resolvedLineLength, '4rem');
    $resolvedStemLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($stemLength, $resolvedLineLength, '4rem');
    $stemContinuationEntries = is_array($stemContinuation) ? $stemContinuation : [];
    $normalizeLabel = function (mixed $label, ?string $side = null) use ($color): ?array {
        if (blank($label)) {
            return null;
        }

        if (is_array($label)) {
            $text = data_get($label, 'text');
            $left = data_get($label, 'left');
            $right = data_get($label, 'right');
            $top = data_get($label, 'top');
            $bottom = data_get($label, 'bottom');

            if (filled($left)) {
                $text = $left;
                $side = 'left';
            } elseif (filled($right)) {
                $text = $right;
                $side = 'right';
            } elseif (filled($top)) {
                $text = $top;
                $side = 'top';
            } elseif (filled($bottom)) {
                $text = $bottom;
                $side = 'bottom';
            }

            if (blank($text)) {
                return null;
            }

            return array_replace([
                'text' => $text,
                'side' => $side,
                'badgeColor' => $color,
            ], collect($label)->except(['left', 'right', 'top', 'bottom'])->all());
        }

        return [
            'text' => $label,
            'side' => $side,
            'badgeColor' => $color,
        ];
    };
    $pathNodeLabels = function (int $nodeNumber, string $defaultSide) use ($nodeLabels, $normalizeLabel): mixed {
        $rawLabel = data_get($nodeLabels, $nodeNumber);

        if (is_array($rawLabel)) {
            $commonLabelOptions = collect($rawLabel)->except(['left', 'right', 'top', 'bottom'])->all();
            $directedLabels = collect(['left', 'right', 'top', 'bottom'])
                ->map(static function (string $side) use ($rawLabel, $commonLabelOptions, $normalizeLabel): ?array {
                    if (! array_key_exists($side, $rawLabel) || blank($rawLabel[$side])) {
                        return null;
                    }

                    return $normalizeLabel(array_replace([
                        $side => $rawLabel[$side],
                    ], $commonLabelOptions), $side);
                })
                ->filter()
                ->values()
                ->all();

            if ($directedLabels !== []) {
                return array_pad(array_slice($directedLabels, 0, 2), 2, null);
            }
        }

        $label = $normalizeLabel($rawLabel, $defaultSide);

        return $label ? [$label, null] : true;
    };
    $arcNodeLabel = fn (int $nodeNumber, string $defaultSide): ?array => $normalizeLabel(
        data_get($nodeLabels, $nodeNumber),
        $defaultSide,
    );

    $arcInStartAnchor = $isLeft ? 'w' : 'e';
    $arcInEndAnchor = 'n';
    $arcOutStartAnchor = 's';
    $arcOutEndAnchor = $isLeft ? 'e' : 'w';
    $bridgeDirection = $isLeft ? 'left-right' : 'right-left';
    $bridgeDelta = $isLeft ? $resolvedBridgeLength : $neg($resolvedBridgeLength);
    $startLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($startLength, $resolvedArcInSize, '2.75rem');
    $resolvedStartLabel = $normalizeLabel($startLabel, 'bottom')
        ?? $normalizeLabel(data_get($nodeLabels, 'start'), 'bottom')
        ?? [
            'text' => ['Merge', 'start'],
            'side' => 'bottom',
            'offset' => '0.75rem',
            'badgeColor' => $color,
        ];
    $startEnd = [
        'x' => $currentAnchor['x'],
        'y' => $add($currentAnchor['y'], $startLength),
    ];
    $stemEnd = [
        'x' => $startEnd['x'],
        'y' => $add($startEnd['y'], $resolvedStemLength),
    ];
    $stemContinuationBlueprints = [];
    $stemContinuationStart = $stemEnd;
    $stemContinuationEnd = $stemEnd;

    foreach ($stemContinuationEntries as $stemContinuationIndex => $stemContinuationEntry) {
        $stemContinuationNumber = is_int($stemContinuationIndex)
            ? ($stemContinuationIndex + (array_is_list($stemContinuationEntries) ? 1 : 0))
            : (int) $stemContinuationIndex;
        $stemContinuationNumber = max(1, $stemContinuationNumber);
        $stemNumber = $stemContinuationNumber + 1;
        $stemNodeNumber = $stemContinuationNumber + 2;
        $stemContinuationLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(
            is_array($stemContinuationEntry)
                ? data_get($stemContinuationEntry, 'length', data_get($stemContinuationEntry, 0))
                : $stemContinuationEntry,
            $resolvedStemLength,
            '4rem',
        );
        $stemContinuationEnd = [
            'x' => $stemContinuationStart['x'],
            'y' => $add($stemContinuationStart['y'], $stemContinuationLength),
        ];
        $stemContinuationBlueprints[] = [
            'component' => 'path',
            'segment' => [
                'id' => $id . '.stem' . $stemNumber,
                'direction' => 'bottom-top',
                'length' => $stemContinuationLength,
                'anchorStart' => $stemContinuationStart,
                'anchorEnd' => $stemContinuationEnd,
                'nodeStart' => false,
                'nodeEnd' => $pathNodeLabels($stemNodeNumber, $isLeft ? 'right' : 'left'),
                'devCounterColor' => $color,
                'color' => $color,
                'zIndex' => $zIndex,
                'dev' => $dev,
            ],
        ];
        $stemContinuationStart = $stemContinuationEnd;
    }
    $arcInEnd = [
        'x' => $add($stemContinuationEnd['x'], $isLeft ? $resolvedArcInSize : $neg($resolvedArcInSize)),
        'y' => $add($stemContinuationEnd['y'], $resolvedArcInSize),
    ];
    $bridgeEnd = [
        'x' => $add($arcInEnd['x'], $bridgeDelta),
        'y' => $arcInEnd['y'],
    ];
    $arcOutEnd = [
        'x' => $add($bridgeEnd['x'], $isLeft ? $resolvedArcOutSize : $neg($resolvedArcOutSize)),
        'y' => $add($bridgeEnd['y'], $resolvedArcOutSize),
    ];
    $pathBoxPadding = '0.75rem';
    $pathBoxX = $isLeft ? $currentAnchor['x'] : $arcOutEnd['x'];
    $pathBoxY = $currentAnchor['y'];
    $pathBoxWidth = $isLeft
        ? 'calc(' . $arcOutEnd['x'] . ' - ' . $currentAnchor['x'] . ')'
        : 'calc(' . $currentAnchor['x'] . ' - ' . $arcOutEnd['x'] . ')';
    $pathBoxHeight = 'calc(' . $arcOutEnd['y'] . ' - ' . $currentAnchor['y'] . ')';
    $stemContinuationSegments = [];
    $stemContinuationCount = count($stemContinuationEntries);
    $arcInNodeNumber = 3 + $stemContinuationCount;
    $arcOutStartNodeNumber = 4 + $stemContinuationCount;
    $arcOutEndNodeNumber = 5 + $stemContinuationCount;

    $segments = [
        [
            'component' => 'start',
            'segment' => [
                'id' => $id . '.start',
                'direction' => 'bottom-top',
                'length' => $startLength,
                'anchorStart' => $currentAnchor,
                'anchorEnd' => $startEnd,
                'nodeEnd' => $pathNodeLabels(1, $isLeft ? 'right' : 'left'),
                'devCounterEnd' => $counter++,
                'devCounterColor' => $color,
                'startLabel' => $resolvedStartLabel,
                'color' => $color,
                'zIndex' => $zIndex,
                'dev' => $dev,
            ],
        ],
        [
            'component' => 'path',
            'segment' => [
                'id' => $id . '.stem1',
                'direction' => 'bottom-top',
                'length' => $resolvedStemLength,
                'anchorStart' => $startEnd,
                'anchorEnd' => $stemEnd,
                'nodeStart' => false,
                'nodeEnd' => $pathNodeLabels(2, $isLeft ? 'right' : 'left'),
                'devCounterEnd' => $counter++,
                'devCounterColor' => $color,
                'color' => $color,
                'zIndex' => $zIndex,
                'dev' => $dev,
            ],
        ],
    ];

    foreach ($stemContinuationBlueprints as $stemContinuationBlueprint) {
        $stemContinuationBlueprint['segment']['devCounterEnd'] = $counter++;
        $stemContinuationSegments[] = $stemContinuationBlueprint;
    }

    $segments = [
        ...$segments,
        ...$stemContinuationSegments,
        [
            'component' => 'arc',
            'segment' => [
                'id' => $id . '.arc.in',
                'startAnchor' => $arcInStartAnchor,
                'endAnchor' => $arcInEndAnchor,
                'anchorStart' => $stemContinuationEnd,
                'anchorEnd' => $arcInEnd,
                'arcSize' => $resolvedArcInSize,
                'nodeStart' => false,
                'nodeEnd' => true,
                'endLabel' => $arcNodeLabel($arcInNodeNumber, 'top'),
                'devCounterEnd' => $counter++,
                'color' => $color,
                'devCounterColor' => $color,
                'zIndex' => $zIndex,
                'dev' => $dev,
            ],
        ],
        [
            'component' => 'path',
            'segment' => [
                'id' => $id . '.bridge',
                'direction' => $bridgeDirection,
                'length' => $resolvedBridgeLength,
                'anchorStart' => $arcInEnd,
                'anchorEnd' => $bridgeEnd,
                'nodeStart' => false,
                'nodeEnd' => false,
                'color' => $color,
                'zIndex' => $zIndex,
                'dev' => $dev,
            ],
        ],
        [
            'component' => 'arc',
            'segment' => [
                'id' => $id . '.arc.out',
                'startAnchor' => $arcOutStartAnchor,
                'endAnchor' => $arcOutEndAnchor,
                'anchorStart' => $bridgeEnd,
                'anchorEnd' => $arcOutEnd,
                'arcSize' => $resolvedArcOutSize,
                'nodeStart' => true,
                'nodeEnd' => true,
                'startLabel' => $arcNodeLabel($arcOutStartNodeNumber, 'bottom'),
                'endLabel' => $arcNodeLabel($arcOutEndNodeNumber, $isLeft ? 'left' : 'right'),
                'devCounterStart' => $counter++,
                'devCounterEnd' => $counter++,
                'color' => $color,
                'devCounterColor' => $color,
                'zIndex' => $zIndex,
                'dev' => $dev,
            ],
        ],
    ];
@endphp

@if ($showDevBox)
    <x-translation-workbench::ui.tw-graph.dev-box
        :id="$id . '.dev-box'"
        :x="'calc(' . $pathBoxX . ' - ' . $pathBoxPadding . ')'"
        :y="'calc(' . $pathBoxY . ' - ' . $pathBoxPadding . ')'"
        :width="'calc(' . $pathBoxWidth . ' + (' . $pathBoxPadding . ' * 2))'"
        :height="'calc(' . $pathBoxHeight . ' + (' . $pathBoxPadding . ' * 2))'"
        color="amber"
        :label="$id"
        :dev="$dev"
    />
@endif

@foreach ($segments as $segment)
    @if ($segment['component'] === 'start')
        <x-translation-workbench::ui.tw-graph.segments.start :segment="$segment['segment']" />
    @elseif ($segment['component'] === 'arc')
        <x-translation-workbench::ui.tw-graph.segments.arc :segment="$segment['segment']" />
    @else
        <x-translation-workbench::ui.tw-graph.segments.path :segment="$segment['segment']" />
    @endif
@endforeach
