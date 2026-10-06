@php
    // Diagnostic settings belong exclusively to the enclosing tw-graph canvas.
    $dev = \Gunreip\TranslationWorkbench\Support\TwGraph\CanvasDiagnostics::current($__env)->dev;
    $attributes = ($attributes ?? new \Illuminate\View\ComponentAttributeBag)->except(['dev', 'dev-mode', 'coordinates']);
@endphp
{{-- packages/gunreip/laravel-translation-workbench/resources/views/components/ui/tw-graph/strang/merge-left.blade.php --}}
{{--
    Strang: merge-left

    Usage:
    <x-translation-workbench::ui.tw-graph.strang.merge-left
        attach-to="strang.trunk.node.3"
        bridge-length="3rem"
        :stem-lengths="[1 => '2rem']"
        :stem-continuation="[1 => '2rem']"
        :node-labels="[1 => ['right' => 'Source'], 5 => ['left' => 'Attach']]"
        :extension-count="2"
        extension-bridge-length="3rem"
        :extension-bridge-continuations="[1 => '5rem']"
        extension-stem-length="2rem"
        :extension-stem-lengths="[1 => '2rem']"
        :extension-stem-continuations="[1 => [1 => '2rem']]"
        :extension-node-labels="[1 => [4 => ['top' => 'Root #1']]]"
    />

    extensionEndLabels[index] optionally closes an outer extension with an end
    cap and label instead of its start marker. Other extensions keep their start.

    Component chain:
    tw-graph -> strang.merge-left -> paths.merge -> segments.* -> primitives.*

    Rule:
    Authoring enters through strang.*. This component owns the left merge
    bounds and delegates only the path rendering to paths.merge.
--}}

@aware([
    'graphId' => null,
    'color' => null,

    'lineLength' => null,
    'lineWidth' => null,
    'arcRadius' => null,
    'bridgeLength' => null,
    'stemLength' => null,
])

@php
    $inheritedColor = $color ?? null;


@endphp

@props([
    'direction' => 'bottom-top',
    'id' => null,
    'componentCounter' => 1,
    'attachTo' => null,
    'anchorStart' => ['x' => '0rem', 'y' => '0rem'],
    'color' => null,
    'startLength' => null,
    'startShiftEnabled' => null,
    'startShiftLength' => null,
    'startLabel' => null,
    'nodeLabels' => [],
    'arcRadiuss' => [],
    'stemLengths' => [],
    'extensionCount' => 0,
    'extensionColors' => [],
    'extensionStartLength' => null,
    'extensionStartShiftEnabled' => null,
    'extensionStartShiftLength' => null,
    'stemContinuation' => [],
    'extensionStemLength' => null,
    'extensionStemLengths' => [],
    'extensionStemContinuations' => [],
    'extensionBridgeLength' => null,
    'extensionBridgeContinuations' => [],
    'extensionArcRadius' => null,
    'extensionArcRadiuss' => [],
    'extensionNodeLabels' => [],
    'extensionEndLabels' => [],
    'counterStart' => 1,
    'zIndex' => 10,
])

@php
    $twGraphRegionFrame = \Gunreip\TranslationWorkbench\Support\TwGraph\ComponentRegion::begin(
        '...::ui.tw-graph.strang.merge-left',
        \Gunreip\TranslationWorkbench\Support\TwGraph\CanvasDiagnostics::current($__env),
    );
    try {
@endphp


@php
    // Keep the authoring ID throughout the internal component chain.
    $previousRootIdentifier = \Gunreip\TranslationWorkbench\Support\TwGraph\RootIdentifier::enter($id);
    try {
    $resolvedGraphId = filled($graphId ?? null) ? (string) $graphId : 'tw-graph';
    $resolvedComponentCounter = max(1, (int) $componentCounter);
    $id = filled($id)
        ? (string) $id
        : $resolvedGraphId . '.strang.merge-left.' . $resolvedComponentCounter;
    \Gunreip\TranslationWorkbench\Support\TwGraph\MergeLengthProvenance::record(
        $__env->getConsumableComponentData('twGraphCalculatedLengths'), 'strang.merge-left', $id, $id . '.paths.merge',
        compact('startLength', 'startShiftLength', 'bridgeLength', 'stemLengths', 'stemContinuation'),
    );
    $resolvedColor = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($color, $inheritedColor ?? null, 'zinc');

    $resolvedLineLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::localOrGraphString($lineLength ?? null, 'line_length', '4rem');
    $resolvedLineWidth = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::localOrGraphString($lineWidth ?? null, 'line_width', '0.25rem');
    $resolvedArcRadius = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::localOrGraphString($arcRadius ?? null, 'arc_radius', '2.75rem');
    $resolvedArcInSize = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(
        data_get($arcRadiuss, 1, data_get($arcRadiuss, 'in')),
        $resolvedArcRadius,
        '2.75rem',
    );
    $resolvedArcOutSize = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(
        data_get($arcRadiuss, 2, data_get($arcRadiuss, 'out')),
        $resolvedArcRadius,
        '2.75rem',
    );
    $resolvedStartLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($startLength, $resolvedArcInSize, '2.75rem');
    $resolvedStartShiftEnabled = $startShiftEnabled === null
        ? \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::graphBool('merge_start_shift_enabled', false)
        : (filter_var($startShiftEnabled, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false);
    $resolvedStartShiftLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(
        $startShiftLength,
        null,
        \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::graphString('merge_start_shift_length', '0rem'),
    );
    $resolvedStartShiftSegmentLength = $resolvedStartShiftEnabled ? $resolvedStartShiftLength : '0rem';
    $localBridgeLength = $attributes->get('bridge-length');
    $resolvedBridgeLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(
        $localBridgeLength,
        $bridgeLength ?? null,
        \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::graphString('bridge_length', $resolvedLineLength),
    );
    $resolvedStemLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(
        null,
        \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::graphString('stem_length', $resolvedLineLength),
        '4rem',
    );
    $stemLengthEntries = is_array($stemLengths) ? $stemLengths : [];
    $add = fn (string $value, string $delta): string => $delta === '0rem' ? $value : 'calc(' . $value . ' + ' . $delta . ')';
    $neg = fn (string $value): string => 'calc(' . $value . ' * -1)';
    $subtract = fn (string $value, string $delta): string => $add($value, $neg($delta));
    $resolvedStemLengths = [];
    $resolvedStemLengthTotal = '0rem';

    foreach ($stemLengthEntries as $stemLengthIndex => $stemLengthEntry) {
        $stemNumber = is_int($stemLengthIndex)
            ? ($stemLengthIndex + (array_is_list($stemLengthEntries) ? 1 : 0))
            : (int) $stemLengthIndex;
        $stemNumber = max(1, $stemNumber);
        $currentStemLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(
            is_array($stemLengthEntry) ? data_get($stemLengthEntry, 'length', data_get($stemLengthEntry, 0)) : $stemLengthEntry,
            null,
            '0rem',
        );

        if (blank($currentStemLength) || $currentStemLength === '0rem') {
            continue;
        }

        $resolvedStemLengths[$stemNumber] = $currentStemLength;
        $resolvedStemLengthTotal = $add($resolvedStemLengthTotal, $currentStemLength);
    }

    $stemLengthCount = count($resolvedStemLengths);
    $stemContinuationEntries = is_array($stemContinuation) ? $stemContinuation : [];
    $stemContinuationTotal = function (array $continuation) use ($add, $resolvedStemLength): string {
        $total = '0rem';

        foreach ($continuation as $entry) {
            $length = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(
                is_array($entry) ? data_get($entry, 'length', data_get($entry, 0)) : $entry,
                $resolvedStemLength,
                '4rem',
            );
            $total = $add($total, $length);
        }

        return $total;
    };
    $resolvedStemContinuationTotal = $stemContinuationTotal($stemContinuationEntries);
    $attachTarget = filled($attachTo)
        ? \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get($resolvedGraphId, (string) $attachTo)
        : null;
    $missingAttachTarget = filled($attachTo) && $attachTarget === null;
    $mergeWidth = $add($add($resolvedArcInSize, $resolvedBridgeLength), $resolvedArcOutSize);
    $mergeHeight = $add($add($add($add($add($resolvedStartLength, $resolvedStartShiftSegmentLength), $resolvedStemLengthTotal), $resolvedStemContinuationTotal), $resolvedArcInSize), $resolvedArcOutSize);
    $anchor = [
        'x' => $attachTarget ? $subtract($attachTarget['x'], $mergeWidth) : data_get($anchorStart, 'x', '0rem'),
        'y' => $attachTarget ? $subtract($attachTarget['y'], $mergeHeight) : data_get($anchorStart, 'y', '0rem'),
    ];
    $attachAnchor = [
        'x' => $add($anchor['x'], $mergeWidth),
        'y' => $add($anchor['y'], $mergeHeight),
    ];

    $node1 = [
        'x' => $anchor['x'],
        'y' => $add($anchor['y'], $resolvedStartLength),
    ];
    $stemStart = [
        'x' => $node1['x'],
        'y' => $add($node1['y'], $resolvedStartShiftSegmentLength),
    ];
    $node2 = [
        'x' => $stemStart['x'],
        'y' => $add($stemStart['y'], $resolvedStemLengthTotal),
    ];
    $stemContinuationAnchors = [];
    $stemContinuationStart = $node2;
    foreach ($stemContinuationEntries as $stemContinuationIndex => $stemContinuationEntry) {
        $stemContinuationNumber = is_int($stemContinuationIndex)
            ? ($stemContinuationIndex + (array_is_list($stemContinuationEntries) ? 1 : 0))
            : (int) $stemContinuationIndex;
        $stemContinuationNumber = max(1, $stemContinuationNumber);
        $stemContinuationLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(
            is_array($stemContinuationEntry)
                ? data_get($stemContinuationEntry, 'length', data_get($stemContinuationEntry, 0))
                : $stemContinuationEntry,
            $resolvedStemLength,
            '4rem',
        );
        $stemContinuationStart = [
            'x' => $stemContinuationStart['x'],
            'y' => $add($stemContinuationStart['y'], $stemContinuationLength),
        ];
        $stemContinuationAnchors[$stemContinuationNumber] = $stemContinuationStart;
    }
    $stemContinuationEnd = $stemContinuationStart;
    $stemContinuationCount = count($stemContinuationEntries);
    $arcInNodeIndex = 2 + $stemLengthCount + $stemContinuationCount;
    $bridgeNodeIndex = $arcInNodeIndex + 1;
    $attachNodeIndex = $bridgeNodeIndex + 1;
    $node3 = [
        'x' => $add($stemContinuationEnd['x'], $resolvedArcInSize),
        'y' => $add($stemContinuationEnd['y'], $resolvedArcInSize),
    ];
    $node4 = [
        'x' => $add($node3['x'], $resolvedBridgeLength),
        'y' => $node3['y'],
    ];
    $node5 = [
        'x' => $add($node4['x'], $resolvedArcOutSize),
        'y' => $add($node4['y'], $resolvedArcOutSize),
    ];
    $resolvedExtensionCount = max(0, (int) $extensionCount);
    $resolvedExtensionStartLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($extensionStartLength, $resolvedArcRadius, '2.75rem');
    $resolvedExtensionStartShiftEnabled = $extensionStartShiftEnabled === null
        ? \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::graphBool('merge_extension_start_shift_enabled', false)
        : (filter_var($extensionStartShiftEnabled, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false);
    $resolvedExtensionStartShiftLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(
        $extensionStartShiftLength,
        null,
        \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::graphString('merge_extension_start_shift_length', '0rem'),
    );
    $resolvedExtensionStartShiftSegmentLength = $resolvedExtensionStartShiftEnabled ? $resolvedExtensionStartShiftLength : '0rem';
    $resolvedExtensionStemLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($extensionStemLength, $resolvedStemLength, '4rem');
    $resolvedExtensionBridgeLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($extensionBridgeLength, $resolvedBridgeLength, '4rem');
    $resolvedExtensionArcRadius = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($extensionArcRadius, $resolvedArcRadius, '2.75rem');
    $extensionStemContinuationFor = function (int $extensionIndex) use ($extensionStemContinuations): array {
        $continuation = data_get($extensionStemContinuations, $extensionIndex, []);

        return is_array($continuation) ? $continuation : [];
    };
    $extensionStemContinuationTotal = function (array $continuation) use ($add, $resolvedExtensionStemLength): string {
        $total = '0rem';

        foreach ($continuation as $entry) {
            $length = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(
                is_array($entry) ? data_get($entry, 'length', data_get($entry, 0)) : $entry,
                $resolvedExtensionStemLength,
                '4rem',
            );
            $total = $add($total, $length);
        }

        return $total;
    };
    $extensionBridgeLengthFor = function (int $extensionIndex) use ($extensionBridgeContinuations, $resolvedExtensionBridgeLength): string {
        return (string) (
            data_get($extensionBridgeContinuations, $extensionIndex)
            ?? $resolvedExtensionBridgeLength
        );
    };
    $extensionArcRadiusFor = function (int $extensionIndex) use ($extensionArcRadiuss, $resolvedExtensionArcRadius): string {
        return (string) (
            data_get($extensionArcRadiuss, $extensionIndex)
            ?? $resolvedExtensionArcRadius
        );
    };
    $extensionStemLengthFor = function (int $extensionIndex) use ($extensionStemLengths, $resolvedExtensionStemLength): string {
        return (string) (
            data_get($extensionStemLengths, $extensionIndex)
            ?? $resolvedExtensionStemLength
        );
    };
    $extensionAnchors = [];
    $extensionResolvedColors = [];
    $currentExtensionColor = $resolvedColor;

    $extensionResolvedStemLengths = [];
    $extensionResolvedStemContinuations = [];
    $extensionResolvedBridgeLengths = [];
    $extensionResolvedArcRadiuss = [];
    $extensionCounterStarts = [];
    $nextExtensionCounterStart = $counterStart + 3 + $stemLengthCount + $stemContinuationCount;
    $nextExtensionTarget = $node3;

    for ($extensionIndex = 1; $extensionIndex <= $resolvedExtensionCount; $extensionIndex++) {
        $currentExtensionColor = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(data_get($extensionColors, $extensionIndex), $currentExtensionColor, $resolvedColor);
        $extensionResolvedColors[$extensionIndex] = $currentExtensionColor;
        $currentExtensionStemLength = $extensionStemLengthFor($extensionIndex);
        $currentExtensionStemContinuation = $extensionStemContinuationFor($extensionIndex);
        $currentExtensionStemContinuationTotal = $extensionStemContinuationTotal($currentExtensionStemContinuation);
        $currentExtensionBridgeLength = $extensionBridgeLengthFor($extensionIndex);
        $currentExtensionArcRadius = $extensionArcRadiusFor($extensionIndex);
        $extensionDeltaX = $add($currentExtensionArcRadius, $currentExtensionBridgeLength);
        $extensionDeltaY = $add($add($add($add($resolvedExtensionStartLength, $resolvedExtensionStartShiftSegmentLength), $currentExtensionStemLength), $currentExtensionStemContinuationTotal), $currentExtensionArcRadius);
        $extensionAnchor = [
            'x' => $subtract($nextExtensionTarget['x'], $extensionDeltaX),
            'y' => $subtract($nextExtensionTarget['y'], $extensionDeltaY),
        ];
        $extensionAnchors[$extensionIndex] = $extensionAnchor;

        $extensionResolvedStemLengths[$extensionIndex] = $currentExtensionStemLength;
        $extensionResolvedStemContinuations[$extensionIndex] = $currentExtensionStemContinuation;
        $extensionResolvedBridgeLengths[$extensionIndex] = $currentExtensionBridgeLength;
        $extensionResolvedArcRadiuss[$extensionIndex] = $currentExtensionArcRadius;
        $extensionCounterStarts[$extensionIndex] = $nextExtensionCounterStart;

        $extensionNode1 = [
            'x' => $extensionAnchor['x'],
            'y' => $add($extensionAnchor['y'], $resolvedExtensionStartLength),
        ];
        $extensionStemStart = [
            'x' => $extensionNode1['x'],
            'y' => $add($extensionNode1['y'], $resolvedExtensionStartShiftSegmentLength),
        ];
        $extensionNode2 = [
            'x' => $extensionStemStart['x'],
            'y' => $add($extensionStemStart['y'], $currentExtensionStemLength),
        ];
        $extensionStemContinuationEnd = [
            'x' => $extensionNode2['x'],
            'y' => $add($extensionNode2['y'], $currentExtensionStemContinuationTotal),
        ];
        $extensionNode3 = [
            'x' => $add($extensionStemContinuationEnd['x'], $currentExtensionArcRadius),
            'y' => $add($extensionStemContinuationEnd['y'], $currentExtensionArcRadius),
        ];
        $extensionNode4 = [
            'x' => $add($extensionNode3['x'], $currentExtensionBridgeLength),
            'y' => $extensionNode3['y'],
        ];


        \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.merge-left.extension.' . $extensionIndex . '.start', array_replace($extensionAnchor, ['color' => $currentExtensionColor]));
        \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.merge-left.extension.' . $extensionIndex . '.node.1', array_replace($extensionNode1, ['color' => $currentExtensionColor]));
        if ($resolvedExtensionStartShiftSegmentLength !== '0rem') {
            \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.merge-left.extension.' . $extensionIndex . '.start-shift.start', array_replace($extensionNode1, ['color' => $currentExtensionColor]));
            \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.merge-left.extension.' . $extensionIndex . '.start-shift.end', array_replace($extensionStemStart, ['color' => $currentExtensionColor]));
        }
        \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.merge-left.extension.' . $extensionIndex . '.node.2', array_replace($extensionNode2, ['color' => $currentExtensionColor]));
        \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.merge-left.extension.' . $extensionIndex . '.stem1.end', array_replace($extensionNode2, ['color' => $currentExtensionColor]));
        \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.merge-left.extension.' . $extensionIndex . '.stem.end', array_replace($extensionStemContinuationEnd, ['color' => $currentExtensionColor]));
        \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.merge-left.extension.' . $extensionIndex . '.stem' . (count($currentExtensionStemContinuation) + 1) . '.end', array_replace($extensionStemContinuationEnd, ['color' => $currentExtensionColor]));
        \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.merge-left.extension.' . $extensionIndex . '.node.3', array_replace($extensionNode3, ['color' => $currentExtensionColor]));
        \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.merge-left.extension.' . $extensionIndex . '.node.4', array_replace($extensionNode4, ['color' => $currentExtensionColor]));
        \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.merge-left.extension.' . $extensionIndex . '.end', array_replace($extensionNode4, ['color' => $currentExtensionColor]));

        $nextExtensionTarget = [
            'x' => $subtract($nextExtensionTarget['x'], $currentExtensionBridgeLength),
            'y' => $nextExtensionTarget['y'],
        ];
        $nextExtensionCounterStart += 4 + count($currentExtensionStemContinuation);
    }


    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.merge-left.start', array_replace($anchor, ['color' => $resolvedColor]));
    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.merge-left.node.1', array_replace($node1, ['color' => $resolvedColor]));
    if ($resolvedStartShiftSegmentLength !== '0rem') {
        \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.merge-left.start-shift.start', array_replace($node1, ['color' => $resolvedColor]));
        \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.merge-left.start-shift.end', array_replace($stemStart, ['color' => $resolvedColor]));
    }
    $currentStemAnchor = $stemStart;
    foreach ($resolvedStemLengths as $stemNumber => $currentStemLength) {
        $currentStemAnchor = [
            'x' => $currentStemAnchor['x'],
            'y' => $add($currentStemAnchor['y'], $currentStemLength),
        ];
        \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.merge-left.node.' . ($stemNumber + 1), array_replace($currentStemAnchor, ['color' => $resolvedColor]));
        \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.merge-left.stem' . $stemNumber . '.end', array_replace($currentStemAnchor, ['color' => $resolvedColor]));
    }
    foreach ($stemContinuationAnchors as $stemContinuationNumber => $stemContinuationAnchor) {
        $stemNumber = $stemLengthCount + $stemContinuationNumber;
        \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.merge-left.node.' . ($stemNumber + 1), array_replace($stemContinuationAnchor, ['color' => $resolvedColor]));
        \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.merge-left.stem' . $stemNumber . '.end', array_replace($stemContinuationAnchor, ['color' => $resolvedColor]));
    }
    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.merge-left.stem.end', array_replace($stemContinuationEnd, ['color' => $resolvedColor]));
    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.merge-left.node.' . $arcInNodeIndex, array_replace($node3, ['color' => $resolvedColor]));
    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.merge-left.node.' . $bridgeNodeIndex, array_replace($node4, ['color' => $resolvedColor]));
    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.merge-left.node.' . $attachNodeIndex, array_replace($node5, ['color' => $resolvedColor]));
    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.merge-left.stem.start', array_replace($node1, ['color' => $resolvedColor]));
    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.merge-left.bridge.start', array_replace($node3, ['color' => $resolvedColor]));
    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.merge-left.bridge.end', array_replace($node4, ['color' => $resolvedColor]));
    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.merge-left.end', array_replace($node5, ['color' => $resolvedColor]));
@endphp

@if ($dev && $missingAttachTarget)
    <span
        class="tw-graph-protocol-dev-only absolute z-50"
        style="
            left: calc(var(--tw-graph-protocol-trunk-x) + {{ data_get($anchorStart, 'x', '0rem') }});
            bottom: calc(var(--tw-graph-protocol-origin-bottom) + {{ data_get($anchorStart, 'y', '0rem') }});
        "
        title="{{ \Gunreip\TranslationWorkbench\Support\TwGraph\DevIdentifier::label($id) }} | missing attach-to: {{ \Gunreip\TranslationWorkbench\Support\TwGraph\ElementIdentifier::normalize($attachTo) }}{{ \Gunreip\TranslationWorkbench\Support\TwGraph\RootIdentifier::tooltipSuffix() }}"
    >
        <flux:badge color="red">
            {{ __('Missing anchor') }}: {{ $attachTo }}
        </flux:badge>
    </span>
@endif


@foreach (array_reverse($extensionAnchors, true) as $extensionIndex => $extensionAnchor)
    @php
        $extensionLengthProps = [
            'startLength' => $extensionStartLength,
            'startShiftLength' => $extensionStartShiftLength,
            'stemLength' => data_get($extensionStemLengths, $extensionIndex, $extensionStemLength),
            'bridgeLength' => data_get($extensionBridgeContinuations, $extensionIndex, $extensionBridgeLength),
            'stemContinuation' => data_get($extensionStemContinuations, $extensionIndex, []),
        ];
        $extensionLengthNames = [
            'startLength' => 'extension-start-length',
            'startShiftLength' => 'extension-start-shift-length',
            'stemLength' => array_key_exists($extensionIndex, (array) $extensionStemLengths)
                ? 'extension-stem-lengths.' . $extensionIndex : 'extension-stem-length',
            'bridgeLength' => array_key_exists($extensionIndex, (array) $extensionBridgeContinuations)
                ? 'extension-bridge-continuations.' . $extensionIndex : 'extension-bridge-length',
        ];
        foreach ((array) $extensionLengthProps['stemContinuation'] as $continuationIndex => $entry) {
            $extensionLengthNames['stemContinuation.' . $continuationIndex] = 'extension-stem-continuations.' . $extensionIndex . '.' . $continuationIndex;
        }
        \Gunreip\TranslationWorkbench\Support\TwGraph\MergeLengthProvenance::record(
            $__env->getConsumableComponentData('twGraphCalculatedLengths'), 'strang.merge-left', $id,
            $id . '.extension.' . $extensionIndex . '.paths.merge-extension',
            $extensionLengthProps, true, $extensionLengthNames,
        );
    @endphp
    <x-translation-workbench::ui.tw-graph.paths.merge-extension
        :id="$id . '.extension.' . $extensionIndex . '.paths.merge-extension'"
        :direction="$direction"
    side="left"
        :anchor-start="$extensionAnchor"
        :start-length="$resolvedExtensionStartLength"
        :start-shift-length="$resolvedExtensionStartShiftSegmentLength"
        :stem-length="$extensionResolvedStemLengths[$extensionIndex]"
        :stem-continuation="$extensionResolvedStemContinuations[$extensionIndex]"
        :bridge-length="$extensionResolvedBridgeLengths[$extensionIndex]"
        :arc-radius="$extensionResolvedArcRadiuss[$extensionIndex]"
        :node-labels="data_get($extensionNodeLabels, $extensionIndex, [])"
        :end-label="data_get($extensionEndLabels, $extensionIndex)"
        :color="$extensionResolvedColors[$extensionIndex]"
        :z-index="$zIndex"
        :counter-start="$extensionCounterStarts[$extensionIndex]"
        :show-dev-box="false"
    />
@endforeach

<x-translation-workbench::ui.tw-graph.paths.merge
    :id="$id . '.paths.merge'"
    :direction="$direction"
    side="left"
    :anchor-start="$anchor"
    :start-length="$resolvedStartLength"
    :start-shift-length="$resolvedStartShiftSegmentLength"
    :line-width="$resolvedLineWidth"
        :bridge-length="$resolvedBridgeLength"
        :stem-lengths="$resolvedStemLengths"
        :stem-continuation="$stemContinuationEntries"
        :arc-radius="$resolvedArcRadius"
        :arc-radiuss="$arcRadiuss"
    :start-label="$startLabel"
    :color="$resolvedColor"
    :z-index="$zIndex"
    :node-labels="$nodeLabels"
    :counter-start="$counterStart"
    :show-dev-box="false"
/>

@php
    } finally {
        \Gunreip\TranslationWorkbench\Support\TwGraph\RootIdentifier::restore($previousRootIdentifier);
    }
@endphp

@php
    } finally {
        $twGraphRegionDefinition = \Gunreip\TranslationWorkbench\Support\TwGraph\ComponentRegion::finish(
            $twGraphRegionFrame, $id ?? null, $resolvedColor ?? $color ?? 'sky',
        );
    }
@endphp
@if ($twGraphRegionDefinition !== null)
    <script type="application/json" data-tw-graph-component-region>{!! json_encode($twGraphRegionDefinition, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) !!}</script>
@endif
