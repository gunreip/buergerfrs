{{-- packages/gunreip/laravel-translation-workbench/resources/views/components/ui/tw-graph/strang/merge-left.blade.php --}}
{{--
    Strang: merge-left

    Usage:
    <x-translation-workbench::ui.tw-graph.strang.merge-left
        attach-to="strang.trunk.node.3"
        bridge-length="3rem"
        stem-length="2rem"
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

    Component chain:
    tw-graph -> strang.merge-left -> paths.merge -> segments.* -> primitives.*

    Rule:
    Authoring enters through strang.*. This component owns the left merge
    bounds and delegates only the path rendering to paths.merge.
--}}

@aware([
    'graphId' => null,
    'dev' => false,
    'defaultColor' => null,
    'lineLength' => null,
    'lineWidth' => null,
    'arcSize' => null,
    'bridgeLength' => null,
    'stemLength' => null,
])

@props([
    'id' => null,
    'componentCounter' => 1,
    'attachTo' => null,
    'anchorStart' => ['x' => '0rem', 'y' => '0rem'],
    'color' => null,
    'startLength' => null,
    'startLabel' => null,
    'nodeLabels' => [],
    'arcSizes' => [],
    'extensionCount' => 0,
    'extensionStartLength' => null,
    'stemContinuation' => [],
    'extensionStemLength' => null,
    'extensionStemLengths' => [],
    'extensionStemContinuations' => [],
    'extensionBridgeLength' => null,
    'extensionBridgeContinuations' => [],
    'extensionArcSize' => null,
    'extensionArcSizes' => [],
    'extensionNodeLabels' => [],
    'counterStart' => 1,
    'zIndex' => 10,
    'devMode' => null,
])

@php
    $resolvedGraphId = filled($graphId ?? null) ? (string) $graphId : 'tw-graph';
    $resolvedComponentCounter = max(1, (int) $componentCounter);
    $id = filled($id)
        ? (string) $id
        : $resolvedGraphId . '.strang.merge-left.' . $resolvedComponentCounter;
    $resolvedColor = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($color, $defaultColor ?? null, 'amber');
    $resolvedDev = $devMode ?? $dev;
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
    $resolvedStartLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($startLength, $resolvedArcInSize, '2.75rem');
    $localBridgeLength = $attributes->get('bridge-length');
    $localStemLength = $attributes->get('stem-length');
    $resolvedBridgeLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(
        $localBridgeLength,
        $bridgeLength ?? null,
        \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::graphString('bridge_length', $resolvedLineLength),
    );
    $resolvedStemLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(
        $localStemLength,
        $stemLength ?? null,
        \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::graphString('stem_length', $resolvedLineLength),
    );
    $add = fn (string $value, string $delta): string => $delta === '0rem' ? $value : 'calc(' . $value . ' + ' . $delta . ')';
    $neg = fn (string $value): string => 'calc(' . $value . ' * -1)';
    $subtract = fn (string $value, string $delta): string => $add($value, $neg($delta));
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
    $mergeHeight = $add($add($add($add($resolvedStartLength, $resolvedStemLength), $resolvedStemContinuationTotal), $resolvedArcInSize), $resolvedArcOutSize);
    $anchor = [
        'x' => $attachTarget ? $subtract($attachTarget['x'], $mergeWidth) : data_get($anchorStart, 'x', '0rem'),
        'y' => $attachTarget ? $subtract($attachTarget['y'], $mergeHeight) : data_get($anchorStart, 'y', '0rem'),
    ];
    $attachAnchor = [
        'x' => $add($anchor['x'], $mergeWidth),
        'y' => $add($anchor['y'], $mergeHeight),
    ];
    $bounds = \Gunreip\TranslationWorkbench\Support\TwGraphProtocol\GeometryBounds::fromPoints([
        $anchor,
        $attachAnchor,
    ], '1rem');

    $node1 = [
        'x' => $anchor['x'],
        'y' => $add($anchor['y'], $resolvedStartLength),
    ];
    $node2 = [
        'x' => $node1['x'],
        'y' => $add($node1['y'], $resolvedStemLength),
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
    $arcInNodeIndex = 3 + $stemContinuationCount;
    $bridgeNodeIndex = 4 + $stemContinuationCount;
    $attachNodeIndex = 5 + $stemContinuationCount;
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
    $resolvedExtensionStartLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($extensionStartLength, $resolvedArcSize, '2.75rem');
    $resolvedExtensionStemLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($extensionStemLength, $resolvedStemLength, '4rem');
    $resolvedExtensionBridgeLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($extensionBridgeLength, $resolvedBridgeLength, '4rem');
    $resolvedExtensionArcSize = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($extensionArcSize, $resolvedArcSize, '2.75rem');
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
    $extensionArcSizeFor = function (int $extensionIndex) use ($extensionArcSizes, $resolvedExtensionArcSize): string {
        return (string) (
            data_get($extensionArcSizes, $extensionIndex)
            ?? $resolvedExtensionArcSize
        );
    };
    $extensionStemLengthFor = function (int $extensionIndex) use ($extensionStemLengths, $resolvedExtensionStemLength): string {
        return (string) (
            data_get($extensionStemLengths, $extensionIndex)
            ?? $resolvedExtensionStemLength
        );
    };
    $extensionAnchors = [];
    $extensionBoundsPoints = [];
    $extensionResolvedStemLengths = [];
    $extensionResolvedStemContinuations = [];
    $extensionResolvedBridgeLengths = [];
    $extensionResolvedArcSizes = [];
    $extensionCounterStarts = [];
    $nextExtensionCounterStart = $counterStart + 5 + $stemContinuationCount;
    $nextExtensionTarget = $node3;

    for ($extensionIndex = 1; $extensionIndex <= $resolvedExtensionCount; $extensionIndex++) {
        $currentExtensionStemLength = $extensionStemLengthFor($extensionIndex);
        $currentExtensionStemContinuation = $extensionStemContinuationFor($extensionIndex);
        $currentExtensionStemContinuationTotal = $extensionStemContinuationTotal($currentExtensionStemContinuation);
        $currentExtensionBridgeLength = $extensionBridgeLengthFor($extensionIndex);
        $currentExtensionArcSize = $extensionArcSizeFor($extensionIndex);
        $extensionDeltaX = $add($currentExtensionArcSize, $currentExtensionBridgeLength);
        $extensionDeltaY = $add($add($add($resolvedExtensionStartLength, $currentExtensionStemLength), $currentExtensionStemContinuationTotal), $currentExtensionArcSize);
        $extensionAnchor = [
            'x' => $subtract($nextExtensionTarget['x'], $extensionDeltaX),
            'y' => $subtract($nextExtensionTarget['y'], $extensionDeltaY),
        ];
        $extensionAnchors[$extensionIndex] = $extensionAnchor;
        $extensionBoundsPoints[] = $extensionAnchor;
        $extensionResolvedStemLengths[$extensionIndex] = $currentExtensionStemLength;
        $extensionResolvedStemContinuations[$extensionIndex] = $currentExtensionStemContinuation;
        $extensionResolvedBridgeLengths[$extensionIndex] = $currentExtensionBridgeLength;
        $extensionResolvedArcSizes[$extensionIndex] = $currentExtensionArcSize;
        $extensionCounterStarts[$extensionIndex] = $nextExtensionCounterStart;

        $extensionNode1 = [
            'x' => $extensionAnchor['x'],
            'y' => $add($extensionAnchor['y'], $resolvedExtensionStartLength),
        ];
        $extensionNode2 = [
            'x' => $extensionNode1['x'],
            'y' => $add($extensionNode1['y'], $currentExtensionStemLength),
        ];
        $extensionStemContinuationEnd = [
            'x' => $extensionNode2['x'],
            'y' => $add($extensionNode2['y'], $currentExtensionStemContinuationTotal),
        ];
        $extensionNode3 = [
            'x' => $add($extensionStemContinuationEnd['x'], $currentExtensionArcSize),
            'y' => $add($extensionStemContinuationEnd['y'], $currentExtensionArcSize),
        ];
        $extensionNode4 = [
            'x' => $add($extensionNode3['x'], $currentExtensionBridgeLength),
            'y' => $extensionNode3['y'],
        ];
        array_push($extensionBoundsPoints, $extensionNode1, $extensionNode2, $extensionStemContinuationEnd, $extensionNode3, $extensionNode4);

        \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.merge-left.extension.' . $extensionIndex . '.start', $extensionAnchor);
        \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.merge-left.extension.' . $extensionIndex . '.node.1', $extensionNode1);
        \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.merge-left.extension.' . $extensionIndex . '.node.2', $extensionNode2);
        \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.merge-left.extension.' . $extensionIndex . '.stem1.end', $extensionNode2);
        \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.merge-left.extension.' . $extensionIndex . '.stem.end', $extensionStemContinuationEnd);
        \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.merge-left.extension.' . $extensionIndex . '.stem' . (count($currentExtensionStemContinuation) + 1) . '.end', $extensionStemContinuationEnd);
        \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.merge-left.extension.' . $extensionIndex . '.node.3', $extensionNode3);
        \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.merge-left.extension.' . $extensionIndex . '.node.4', $extensionNode4);
        \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.merge-left.extension.' . $extensionIndex . '.end', $extensionNode4);

        $nextExtensionTarget = [
            'x' => $subtract($nextExtensionTarget['x'], $currentExtensionBridgeLength),
            'y' => $nextExtensionTarget['y'],
        ];
        $nextExtensionCounterStart += 4 + count($currentExtensionStemContinuation);
    }

    $bounds = \Gunreip\TranslationWorkbench\Support\TwGraphProtocol\GeometryBounds::fromPoints([
        $anchor,
        $attachAnchor,
        ...$extensionBoundsPoints,
    ], '1rem');

    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.merge-left.start', $anchor);
    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.merge-left.node.1', $node1);
    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.merge-left.node.2', $node2);
    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.merge-left.stem1.end', $node2);
    foreach ($stemContinuationAnchors as $stemContinuationNumber => $stemContinuationAnchor) {
        \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.merge-left.node.' . ($stemContinuationNumber + 2), $stemContinuationAnchor);
        \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.merge-left.stem' . ($stemContinuationNumber + 1) . '.end', $stemContinuationAnchor);
    }
    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.merge-left.stem.end', $stemContinuationEnd);
    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.merge-left.node.' . $arcInNodeIndex, $node3);
    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.merge-left.node.' . $bridgeNodeIndex, $node4);
    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.merge-left.node.' . $attachNodeIndex, $node5);
    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.merge-left.stem.start', $node1);
    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.merge-left.bridge.start', $node3);
    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.merge-left.bridge.end', $node4);
    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.merge-left.end', $node5);
@endphp

@if ($resolvedDev && $missingAttachTarget)
    <span
        class="tw-graph-protocol-dev-only absolute z-50"
        style="
            left: calc(var(--tw-graph-protocol-trunk-x) + {{ data_get($anchorStart, 'x', '0rem') }});
            bottom: calc(var(--tw-graph-protocol-origin-bottom) + {{ data_get($anchorStart, 'y', '0rem') }});
        "
        title="{{ \Gunreip\TranslationWorkbench\Support\TwGraph\DevIdentifier::label($id) }} | missing attach-to: {{ \Gunreip\TranslationWorkbench\Support\TwGraph\ElementIdentifier::normalize($attachTo) }}"
    >
        <flux:badge color="red">
            {{ __('Missing anchor') }}: {{ $attachTo }}
        </flux:badge>
    </span>
@endif

<x-translation-workbench::ui.tw-graph.dev-box
    :id="$id . '.dev-box'"
    :x="$bounds['left']"
    :y="$bounds['bottom']"
    :width="$bounds['width']"
    :height="$bounds['height']"
    :color="$resolvedColor"
    :label="$id"
    :dev="$resolvedDev"
    metrics-scope="canvas"
    metrics-side="left"
/>

@foreach (array_reverse($extensionAnchors, true) as $extensionIndex => $extensionAnchor)
    <x-translation-workbench::ui.tw-graph.paths.merge-extension
        :id="$id . '.extension.' . $extensionIndex"
        side="left"
        :anchor-start="$extensionAnchor"
        :start-length="$resolvedExtensionStartLength"
        :stem-length="$extensionResolvedStemLengths[$extensionIndex]"
        :stem-continuation="$extensionResolvedStemContinuations[$extensionIndex]"
        :bridge-length="$extensionResolvedBridgeLengths[$extensionIndex]"
        :arc-size="$extensionResolvedArcSizes[$extensionIndex]"
        :node-labels="data_get($extensionNodeLabels, $extensionIndex, [])"
        :color="$resolvedColor"
        :z-index="$zIndex"
        :counter-start="$extensionCounterStarts[$extensionIndex]"
        :dev="$resolvedDev"
        :show-dev-box="false"
    />
@endforeach

<x-translation-workbench::ui.tw-graph.paths.merge
    :id="$id . '.paths.merge'"
    side="left"
    :anchor-start="$anchor"
    :start-length="$resolvedStartLength"
    :line-width="$resolvedLineWidth"
        :bridge-length="$resolvedBridgeLength"
        :stem-length="$resolvedStemLength"
        :stem-continuation="$stemContinuationEntries"
        :arc-size="$resolvedArcSize"
        :arc-sizes="$arcSizes"
    :start-label="$startLabel"
    :color="$resolvedColor"
    :z-index="$zIndex"
    :node-labels="$nodeLabels"
    :counter-start="$counterStart"
    :dev="$resolvedDev"
    :show-dev-box="false"
/>
