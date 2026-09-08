{{-- packages/gunreip/laravel-translation-workbench/resources/views/components/ui/tw-graph/paths/merge-extension.blade.php --}}
{{--
    Path: merge-extension

    Usage:
    <x-translation-workbench::ui.tw-graph.paths.merge-extension
        side="left"
        :anchor-start="['x' => '0rem', 'y' => '0rem']"
        start-length="2rem"
        stem-length="2rem"
        :stem-continuation="[1 => '2rem']"
        bridge-length="3rem"
        :node-labels="[4 => ['top' => 'Root #1']]"
    />

    Path role:
    Merge-extension continues a merge side chain outward:
    left:  segments.start -> segments.path stem1 bottom-top -> optional stem2/stem3/... -> segments.arc west-north -> segments.path left-right
    right: segments.start -> segments.path stem1 bottom-top -> optional stem2/stem3/... -> segments.arc east-north -> segments.path right-left
--}}

@aware([
    'color' => null,
])

@php
    $inheritedColor = $color ?? null;



@endphp

@props([
    'id' => 'path.merge-extension',
    'side' => 'left',
    'anchorStart' => ['x' => '0rem', 'y' => '0rem'],
    'startLength' => null,
    'startShiftLength' => null,
    'lineLength' => null,
    'arcSize' => null,
    'stemLength' => null,
    'stemContinuation' => [],
    'bridgeLength' => null,
    'nodeLabels' => [],
    'color' => null,
    'zIndex' => null,
    'counterStart' => 1,
    'dev' => false,
    'showDevBox' => true,
])

@php
    $add = fn (string $value, string $delta): string => $delta === '0rem' ? $value : 'calc(' . $value . ' + ' . $delta . ')';
    $neg = fn (string $value): string => 'calc(' . $value . ' * -1)';
    $toRem = static function (mixed $value): float {
        if (is_numeric($value)) {
            return (float) $value;
        }

        preg_match('/-?\d+(?:\.\d+)?/', (string) $value, $matches);

        return isset($matches[0]) ? (float) $matches[0] : 0.0;
    };
    $currentAnchor = [
        'x' => data_get($anchorStart, 'x', '0rem'),
        'y' => data_get($anchorStart, 'y', '0rem'),
    ];
    $counter = (int) $counterStart;
    $isLeft = $side === 'left';
    $resolvedLineLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::localOrGraphString($lineLength ?? null, 'line_length', '4rem');
    $resolvedArcSize = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::localOrGraphString($arcSize ?? null, 'arc_size', '2.75rem');
    $resolvedColor = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($color, $inheritedColor ?? null, 'zinc');
    $resolvedStemLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($stemLength, \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::graphString('stem_length', $resolvedLineLength), '4rem');
    $resolvedBridgeLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($bridgeLength, \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::graphString('bridge_length', $resolvedLineLength), '4rem');
    $stemContinuationEntries = is_array($stemContinuation) ? $stemContinuation : [];

    $arcStartAnchor = $isLeft ? 'w' : 'e';
    $arcEndAnchor = 'n';
    $bridgeDirection = $isLeft ? 'left-right' : 'right-left';
    $arcDelta = $isLeft ? $resolvedArcSize : $neg($resolvedArcSize);
    $bridgeDelta = $isLeft ? $resolvedBridgeLength : $neg($resolvedBridgeLength);
    $startLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($startLength, $resolvedArcSize, '2.75rem');
    $startShiftLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($startShiftLength, null, '0rem');
    $hasStartShiftJoint = $toRem($startShiftLength) >= 1.0;
    $startShiftLength = $hasStartShiftJoint ? $startShiftLength : '0rem';
    $startEnd = [
        'x' => $currentAnchor['x'],
        'y' => $add($currentAnchor['y'], $startLength),
    ];
    $shiftEnd = [
        'x' => $startEnd['x'],
        'y' => $add($startEnd['y'], $startShiftLength),
    ];
    $stemEnd = [
        'x' => $shiftEnd['x'],
        'y' => $add($shiftEnd['y'], $resolvedStemLength),
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
                'nodeEnd' => true,
                'nodeEndLabelNumber' => $stemNodeNumber,
                'devCounterColor' => $resolvedColor,
                'color' => $resolvedColor,
                'zIndex' => $zIndex,
                'dev' => $dev,
            ],
        ];
        $stemContinuationStart = $stemContinuationEnd;
    }

    $arcEnd = [
        'x' => $add($stemContinuationEnd['x'], $arcDelta),
        'y' => $add($stemContinuationEnd['y'], $resolvedArcSize),
    ];
    $bridgeEnd = [
        'x' => $add($arcEnd['x'], $bridgeDelta),
        'y' => $arcEnd['y'],
    ];
    $pathBoxPadding = '0.75rem';
    $pathBoxX = $isLeft ? $currentAnchor['x'] : $bridgeEnd['x'];
    $pathBoxY = $currentAnchor['y'];
    $pathBoxWidth = $isLeft
        ? 'calc(' . $bridgeEnd['x'] . ' - ' . $currentAnchor['x'] . ')'
        : 'calc(' . $currentAnchor['x'] . ' - ' . $bridgeEnd['x'] . ')';
    $pathBoxHeight = 'calc(' . $bridgeEnd['y'] . ' - ' . $currentAnchor['y'] . ')';
    $normalizeLabel = fn (mixed $label, ?string $side = null): ?array => \Gunreip\TranslationWorkbench\Support\TwGraph\TextLabel::normalize($label, $side, $resolvedColor);
    $pathNodeLabels = function (int|array $nodeNumber, string $defaultSide) use ($nodeLabels, $resolvedColor): mixed {
        $rawLabel = is_array($nodeNumber)
            ? $nodeNumber
            : data_get($nodeLabels, $nodeNumber);

        return \Gunreip\TranslationWorkbench\Support\TwGraph\TextLabel::nodeLabels(
            $rawLabel,
            $defaultSide,
            $resolvedColor,
            ['length', 'component', 'compressed', 'beforeLength', 'gapLength', 'afterLength', 'capLength'],
        );
    };
    $hasLabels = static fn (mixed $node): bool => is_array($node) && collect($node)->filter()->isNotEmpty();
    $arcNodeLabel = fn (int $nodeNumber, string $defaultSide): ?array => $normalizeLabel(
        data_get($nodeLabels, $nodeNumber),
        $defaultSide,
    );
    $startLabel = $normalizeLabel(data_get($nodeLabels, 'start'), 'bottom') ?? [
        'text' => ['Merge extension', 'start'],
        'side' => 'bottom',
        'offset' => '0.75rem',
        'badgeColor' => $resolvedColor,
    ];

    $stemContinuationSegments = [];
    $stemContinuationCount = count($stemContinuationEntries);
    $arcNodeNumber = 3 + $stemContinuationCount;
    $bridgeNodeNumber = 4 + $stemContinuationCount;

    $segments = [
        [
            'component' => 'start',
            'segment' => [
                'id' => $id . '.start',
                'direction' => 'bottom-top',
                'length' => $startLength,
                'anchorStart' => $currentAnchor,
                'anchorEnd' => $startEnd,
                'nodeEnd' => $hasStartShiftJoint ? false : $pathNodeLabels(1, $isLeft ? 'right' : 'left'),
                'devCounterEnd' => $hasStartShiftJoint ? null : $counter++,
                'devCounterColor' => $color,
                'startLabel' => $startLabel,
                'color' => $color,
                'zIndex' => $zIndex,
                'dev' => $dev,
            ],
        ],
    ];

    if (filled($startShiftLength) && $startShiftLength !== '0rem') {
        $segments[] = [
            'component' => 'path',
            'segment' => [
                'id' => $id . '.start-shift',
                'direction' => 'bottom-top',
                'length' => $startShiftLength,
                'anchorStart' => $startEnd,
                'anchorEnd' => $shiftEnd,
                'nodeStart' => false,
                'nodeEnd' => $pathNodeLabels(1, $isLeft ? 'right' : 'left'),
                'devCounterEnd' => $counter++,
                'devCounterColor' => $color,
                'color' => $color,
                'zIndex' => $zIndex,
                'dev' => $dev,
            ],
        ];
    }

    $segments = [
        ...$segments,
        [
            'component' => 'path',
            'segment' => [
                'id' => $id . '.stem1',
                'direction' => 'bottom-top',
                'length' => $resolvedStemLength,
                'anchorStart' => $shiftEnd,
                'anchorEnd' => $stemEnd,
                'nodeStart' => false,
                'nodeEnd' => $stem1NodeEnd = $pathNodeLabels(2, $isLeft ? 'right' : 'left'),
                'nodeEndDot' => $hasLabels($stem1NodeEnd),
                'jointArrowEnd' => ! $hasLabels($stem1NodeEnd),
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
        $stemContinuationBlueprint['segment']['nodeEnd'] = $pathNodeLabels(
            (int) data_get($stemContinuationBlueprint, 'segment.nodeEndLabelNumber'),
            $isLeft ? 'right' : 'left',
        );
        $stemContinuationBlueprint['segment']['nodeEndDot'] = $hasLabels($stemContinuationBlueprint['segment']['nodeEnd']);
        $stemContinuationBlueprint['segment']['jointArrowEnd'] = ! $hasLabels($stemContinuationBlueprint['segment']['nodeEnd']);
        unset($stemContinuationBlueprint['segment']['nodeEndLabelNumber']);
        $stemContinuationSegments[] = $stemContinuationBlueprint;
    }

    $segments = [
        ...$segments,
        ...$stemContinuationSegments,
        [
            'component' => 'arc',
            'segment' => [
                'id' => $id . '.arc',
                'startAnchor' => $arcStartAnchor,
                'endAnchor' => $arcEndAnchor,
                'arcSize' => $resolvedArcSize,
                'anchorStart' => $stemContinuationEnd,
                'anchorEnd' => $arcEnd,
                'nodeStart' => false,
                'nodeEnd' => true,
                'devCounterEnd' => $counter++,
                'devCounterColor' => $color,
                'endLabel' => $arcNodeLabel($arcNodeNumber, 'top'),
                'color' => $color,
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
                'anchorStart' => $arcEnd,
                'anchorEnd' => $bridgeEnd,
                'nodeStart' => false,
                'nodeEnd' => $pathNodeLabels($bridgeNodeNumber, 'top'),
                'devCounterEnd' => $counter++,
                'devCounterColor' => $color,
                'color' => $color,
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

@if ($hasStartShiftJoint)
    <x-translation-workbench::ui.tw-graph.primitives.joint-arrow
        :id="$id . '.start.start-shift.joint-arrow'"
        direction="top"
        :anchor-x="$startEnd['x']"
        :anchor-y="$startEnd['y']"
        :color="$resolvedColor"
        :z-index="$zIndex + 1"
    />
@endif
