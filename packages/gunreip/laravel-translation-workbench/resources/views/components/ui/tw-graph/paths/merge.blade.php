{{-- packages/gunreip/laravel-translation-workbench/resources/views/components/ui/tw-graph/paths/merge.blade.php --}}
{{--
    Path: merge

    Usage:
    <x-translation-workbench::ui.tw-graph.paths.merge
        side="left"
        :anchor-start="['x' => '0rem', 'y' => '0.75rem']"
        bridge-length="3rem"
        :stem-lengths="[1 => '2rem']"
        :stem-continuation="[1 => ['length' => '5rem', 'compressed' => true]]"
        :node-labels="[1 => ['right' => 'Source'], 3 => ['left' => 'Attach']]"
    />

    Path role:
    Merge owns one inbound side path and calculates its anchor chain from
    branch origin to trunk attach point:
    start -> stem1 -> optional stem2/stem3/... -> arc-in -> bridge -> arc-out.
    A compressed lifecycle marker is a property of a concrete stem continuation,
    e.g. stem2 after anchorNode2, not of the whole merge path.
--}}

@aware([
    'color' => null,
])

@php
    $inheritedColor = $color ?? null;



@endphp

@props([
    'id' => 'path.merge',
    'side' => 'left',
    'anchorStart' => ['x' => '0rem', 'y' => '0rem'],
    'startLength' => null,
    'startShiftLength' => null,
    'lineLength' => null,
    'lineWidth' => null,
    'arcSize' => null,
    'arcSizes' => [],
    'bridgeLength' => null,
    'stemLengths' => [],
    'stemContinuation' => [],
    'compressedStemParts' => [],
    'startLabel' => null,
    'color' => null,
    'zIndex' => null,
    'nodeLabels' => [],
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
    $resolvedLineWidth = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::localOrGraphString($lineWidth ?? null, 'line_width', '0.25rem');
    $resolvedArcSize = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::localOrGraphString($arcSize ?? null, 'arc_size', '2.75rem');
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
    $resolvedColor = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($color, $inheritedColor ?? null, 'zinc');
    $resolvedBridgeLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($bridgeLength, \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::graphString('bridge_length', $resolvedLineLength), '4rem');
    $resolvedStemLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(null, \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::graphString('stem_length', $resolvedLineLength), '4rem');
    $stemLengthEntries = is_array($stemLengths) ? $stemLengths : [];
    $compressedStemParts = is_array($compressedStemParts) ? $compressedStemParts : [];
    $stemContinuationEntries = is_array($stemContinuation) ? $stemContinuation : [];
    $normalizeLabel = fn (mixed $label, ?string $side = null): ?array => \Gunreip\TranslationWorkbench\Support\TwGraph\TextLabel::normalize($label, $side, $resolvedColor);
    $pathNodeLabels = fn (int $nodeNumber, string $defaultSide): mixed => \Gunreip\TranslationWorkbench\Support\TwGraph\TextLabel::nodeLabels(
        data_get($nodeLabels, $nodeNumber),
        $defaultSide,
        $resolvedColor,
    );
    $hasLabels = static fn (mixed $node): bool => is_array($node) && collect($node)->filter()->isNotEmpty();
    $arcInStartAnchor = $isLeft ? 'w' : 'e';
    $arcInEndAnchor = 'n';
    $arcOutStartAnchor = 's';
    $arcOutEndAnchor = $isLeft ? 'e' : 'w';
    $bridgeDirection = $isLeft ? 'left-right' : 'right-left';
    $bridgeDelta = $isLeft ? $resolvedBridgeLength : $neg($resolvedBridgeLength);
    $startLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($startLength, $resolvedArcInSize, '2.75rem');
    $startShiftLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($startShiftLength, null, '0rem');
    $hasStartShiftJoint = $toRem($startShiftLength) >= 1.0;
    $startShiftLength = $hasStartShiftJoint ? $startShiftLength : '0rem';
    $resolvedStartLabel = $normalizeLabel($startLabel, 'bottom')
        ?? $normalizeLabel(data_get($nodeLabels, 'start'), 'bottom')
        ?? [
            'text' => ['Merge', 'start'],
            'side' => 'bottom',
            'offset' => '0.75rem',
            'badgeColor' => $resolvedColor,
        ];
    $startEnd = [
        'x' => $currentAnchor['x'],
        'y' => $add($currentAnchor['y'], $startLength),
    ];
    $shiftEnd = [
        'x' => $startEnd['x'],
        'y' => $add($startEnd['y'], $startShiftLength),
    ];
    $stemLengthBlueprints = [];
    $stemLengthStart = $shiftEnd;

    foreach ($stemLengthEntries as $stemLengthIndex => $stemLengthEntry) {
        $stemNumber = is_int($stemLengthIndex)
            ? ($stemLengthIndex + (array_is_list($stemLengthEntries) ? 1 : 0))
            : (int) $stemLengthIndex;
        $stemNumber = max(1, $stemNumber);
        $stemNodeNumber = $stemNumber + 1;
        $currentStemLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(
            is_array($stemLengthEntry)
                ? data_get($stemLengthEntry, 'length', data_get($stemLengthEntry, 0))
                : $stemLengthEntry,
            null,
            '0rem',
        );

        if (blank($currentStemLength) || $toRem($currentStemLength) <= 0.0) {
            continue;
        }

        $stemLengthEnd = [
            'x' => $stemLengthStart['x'],
            'y' => $add($stemLengthStart['y'], $currentStemLength),
        ];
        $stemNodeEnd = $pathNodeLabels($stemNodeNumber, $isLeft ? 'right' : 'left');
        $stemLengthBlueprints[] = [
            'component' => 'path',
            'segment' => [
                'id' => $id . '.stem' . $stemNumber,
                'direction' => 'bottom-top',
                'length' => $currentStemLength,
                'anchorStart' => $stemLengthStart,
                'anchorEnd' => $stemLengthEnd,
                'nodeStart' => false,
                'nodeEnd' => $stemNodeEnd,
                'nodeEndDot' => $hasLabels($stemNodeEnd),
                'jointArrowEnd' => ! $hasLabels($stemNodeEnd),
                'devCounterColor' => $color,
                'color' => $color,
                'zIndex' => $zIndex,
                'dev' => $dev,
            ],
        ];
        $stemLengthStart = $stemLengthEnd;
    }

    $stemEnd = $stemLengthStart;
    $stemContinuationBlueprints = [];
    $stemContinuationStart = $stemEnd;
    $stemContinuationEnd = $stemEnd;
    $stemLengthCount = count($stemLengthBlueprints);

    foreach ($stemContinuationEntries as $stemContinuationIndex => $stemContinuationEntry) {
        $stemContinuationNumber = is_int($stemContinuationIndex)
            ? ($stemContinuationIndex + (array_is_list($stemContinuationEntries) ? 1 : 0))
            : (int) $stemContinuationIndex;
        $stemContinuationNumber = max(1, $stemContinuationNumber);
        $stemNumber = $stemLengthCount + $stemContinuationNumber;
        $stemNodeNumber = $stemNumber + 1;
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
        $compressedStemContinuation = is_array($stemContinuationEntry)
            && (bool) data_get($stemContinuationEntry, 'compressed', false);
        $stemNodeEnd = $pathNodeLabels($stemNodeNumber, $isLeft ? 'right' : 'left');
        $stemContinuationBlueprints[] = [
            'component' => $compressedStemContinuation ? 'stem-compressed' : 'path',
            'segment' => [
                'id' => $id . '.stem' . $stemNumber,
                'direction' => 'bottom-top',
                'length' => $stemContinuationLength,
                'beforeLength' => is_array($stemContinuationEntry) ? data_get($stemContinuationEntry, 'beforeLength', data_get($compressedStemParts, 'beforeLength', '1rem')) : data_get($compressedStemParts, 'beforeLength', '1rem'),
                'gapLength' => is_array($stemContinuationEntry) ? data_get($stemContinuationEntry, 'gapLength', data_get($compressedStemParts, 'gapLength', '1rem')) : data_get($compressedStemParts, 'gapLength', '1rem'),
                'afterLength' => is_array($stemContinuationEntry) ? data_get($stemContinuationEntry, 'afterLength', data_get($compressedStemParts, 'afterLength', '1rem')) : data_get($compressedStemParts, 'afterLength', '1rem'),
                'anchorStart' => $stemContinuationStart,
                'anchorEnd' => $stemContinuationEnd,
                'nodeStart' => false,
                'nodeEnd' => $stemNodeEnd,
                'nodeEndDot' => $hasLabels($stemNodeEnd),
                'jointArrowEnd' => ! $hasLabels($stemNodeEnd),
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
    $arcInNodeNumber = 2 + $stemLengthCount + $stemContinuationCount;
    $arcOutStartNodeNumber = $arcInNodeNumber + 1;
    $arcOutEndNodeNumber = $arcOutStartNodeNumber + 1;
    $mergeEndLabelNodeNumber = 2 + $stemLengthCount + $stemContinuationCount;
    $hasExplicitEndNodeLabel = is_array($nodeLabels) && array_key_exists('end', $nodeLabels);
    $hasNumericEndNodeLabel = is_array($nodeLabels) && array_key_exists($mergeEndLabelNodeNumber, $nodeLabels);
    $mergeEndNodeLabelEntry = $hasExplicitEndNodeLabel
        ? data_get($nodeLabels, 'end')
        : data_get($nodeLabels, $mergeEndLabelNodeNumber);
    $mergeEndNodeLabels = \Gunreip\TranslationWorkbench\Support\TwGraph\TextLabel::nodeLabels(
        $mergeEndNodeLabelEntry,
        $isLeft ? 'left' : 'right',
        $resolvedColor,
    );
    $nodeLabelEndOverride = $hasExplicitEndNodeLabel && $hasNumericEndNodeLabel;
    $nodeLabelEndOverrideText = 'nodeLabel-EndOverride | end wins | numeric: ' . $mergeEndLabelNodeNumber;
    $configuredNodeLabelNumbers = collect(is_array($nodeLabels) ? array_keys($nodeLabels) : [])
        ->filter(static fn (mixed $key): bool => is_int($key) || (is_string($key) && ctype_digit($key)))
        ->map(static fn (mixed $key): int => (int) $key)
        ->filter(static fn (int $nodeNumber): bool => $nodeNumber > 0)
        ->unique()
        ->sort()
        ->values();
    $availableNodeLabelNumbers = collect(range(1, $mergeEndLabelNodeNumber));
    $ignoredNodeLabelNumbers = $configuredNodeLabelNumbers
        ->diff($availableNodeLabelNumbers)
        ->values();
    $nodeLabelMismatch = $ignoredNodeLabelNumbers->isNotEmpty();
    $nodeLabelMismatchText = 'nodeLabel-Mismatch | labels: '
        . $configuredNodeLabelNumbers->count()
        . ' | anchors: '
        . $availableNodeLabelNumbers->count()
        . ' | ignored: '
        . $ignoredNodeLabelNumbers->count();
    $nodeLabelMismatchTitle = $nodeLabelMismatchText
        . ' | ignored nodes: '
        . $ignoredNodeLabelNumbers->implode(', ');

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
                'startLabel' => $resolvedStartLabel,
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

    foreach ($stemLengthBlueprints as $stemLengthBlueprint) {
        $stemLengthBlueprint['segment']['devCounterEnd'] = $counter++;
        $segments[] = $stemLengthBlueprint;
    }

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
                'endLabel' => null,
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
                'startLabel' => null,
                'endLabel' => null,
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
    @elseif ($segment['component'] === 'stem-compressed')
        <x-translation-workbench::ui.tw-graph.segments.stem-compressed
            :segment="$segment['segment']"
            :dev="$dev"
        />
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

@if ($hasLabels($mergeEndNodeLabels))
    @foreach (collect($mergeEndNodeLabels)->take(2) as $labelIndex => $label)
        @continue(blank($label))

        @php
            $requestedSide = data_get($label, 'side');
            $side = in_array($requestedSide, ['left', 'right'], true)
                ? $requestedSide
                : ($labelIndex === 0 ? ($isLeft ? 'left' : 'right') : ($isLeft ? 'right' : 'left'));
        @endphp

        <x-translation-workbench::ui.tw-graph.segments.label
            :id="$id . '.end.label.' . $side . '.' . ($labelIndex + 1)"
            :label="$label"
            :side="$side"
            :anchor-x="$arcOutEnd['x']"
            :anchor-y="$arcOutEnd['y']"
            :color="$color"
        />
    @endforeach
@endif

@if ($dev && $nodeLabelMismatch)
    <span
        class="tw-graph-protocol-dev-only absolute z-50"
        style="
            left: calc(var(--tw-graph-protocol-trunk-x) + {{ $arcOutEnd['x'] }});
            bottom: calc(var(--tw-graph-protocol-origin-bottom) + {{ $arcOutEnd['y'] }} + 1rem);
        "
        title="{{ $nodeLabelMismatchTitle }}"
    >
        <flux:badge color="red">
            {{ $nodeLabelMismatchText }}
        </flux:badge>
    </span>
@endif

@if ($dev && $nodeLabelEndOverride)
    <span
        class="tw-graph-protocol-dev-only absolute z-50"
        style="
            left: calc(var(--tw-graph-protocol-trunk-x) + {{ $arcOutEnd['x'] }});
            bottom: calc(var(--tw-graph-protocol-origin-bottom) + {{ $arcOutEnd['y'] }} + 3rem);
        "
        title="{{ $nodeLabelEndOverrideText }}"
    >
        <flux:badge color="amber">
            {{ $nodeLabelEndOverrideText }}
        </flux:badge>
    </span>
@endif
