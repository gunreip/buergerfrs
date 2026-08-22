{{-- packages/gunreip/laravel-translation-workbench/resources/views/components/ui/tw-graph/strang/merge-right.blade.php --}}
{{--
    Strang: merge-right

    Usage:
    <x-translation-workbench::ui.tw-graph.strang.merge-right
        attach-to="strang.trunk.node.3"
        bridge-length="3rem"
        stem-length="2rem"
        :node-labels="[1 => ['left' => 'Source'], 5 => ['right' => 'Attach']]"
        :extension-count="2"
        :extension-node-labels="[1 => [4 => ['top' => 'Root #1']]]"
    />

    Component chain:
    tw-graph -> strang.merge-right -> paths.merge -> segments.* -> primitives.*

    Rule:
    Authoring enters through strang.*. This component owns the right merge
    bounds and delegates only the path rendering to paths.merge.
--}}

@aware([
    'graphId' => null,
    'dev' => false,
    'defaultColor' => null,
    'lineLength' => '4rem',
    'lineWidth' => '0.25rem',
    'arcSize' => '2.75rem',
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
    'nodeLabels' => [],
    'extensionCount' => 0,
    'extensionStartLength' => null,
    'extensionStemLength' => null,
    'extensionStemLengths' => [],
    'extensionBridgeLength' => null,
    'extensionBridgeLengths' => [],
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
        : $resolvedGraphId . '.strang.merge-right.' . $resolvedComponentCounter;
    $resolvedColor = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($color, $defaultColor ?? null, 'green');
    $resolvedDev = $devMode ?? $dev;
    $resolvedLineLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::localOrFallback($lineLength ?? null, '4rem');
    $resolvedLineWidth = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::localOrFallback($lineWidth ?? null, '0.25rem');
    $resolvedArcSize = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::localOrFallback($arcSize ?? null, '2.75rem');
    $resolvedStartLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($startLength, $resolvedArcSize, '2.75rem');
    $localBridgeLength = $attributes->get('bridge-length');
    $localStemLength = $attributes->get('stem-length');
    $resolvedBridgeLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(
        $localBridgeLength,
        $bridgeLength ?? null,
        $resolvedLineLength,
    );
    $resolvedStemLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(
        $localStemLength,
        $stemLength ?? null,
        $resolvedLineLength,
    );
    $add = fn (string $value, string $delta): string => $delta === '0rem' ? $value : 'calc(' . $value . ' + ' . $delta . ')';
    $neg = fn (string $value): string => 'calc(' . $value . ' * -1)';
    $subtract = fn (string $value, string $delta): string => $add($value, $neg($delta));
    $attachTarget = filled($attachTo)
        ? \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get($resolvedGraphId, (string) $attachTo)
        : null;
    $missingAttachTarget = filled($attachTo) && $attachTarget === null;
    $mergeWidth = $add($add($resolvedArcSize, $resolvedBridgeLength), $resolvedArcSize);
    $mergeHeight = $add($add($add($resolvedStartLength, $resolvedStemLength), $resolvedArcSize), $resolvedArcSize);
    $anchor = [
        'x' => $attachTarget ? $add($attachTarget['x'], $mergeWidth) : data_get($anchorStart, 'x', '0rem'),
        'y' => $attachTarget ? $subtract($attachTarget['y'], $mergeHeight) : data_get($anchorStart, 'y', '0rem'),
    ];
    $attachAnchor = [
        'x' => $add($anchor['x'], $neg($mergeWidth)),
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
    $node3 = [
        'x' => $add($node2['x'], $neg($resolvedArcSize)),
        'y' => $add($node2['y'], $resolvedArcSize),
    ];
    $node4 = [
        'x' => $add($node3['x'], $neg($resolvedBridgeLength)),
        'y' => $node3['y'],
    ];
    $node5 = [
        'x' => $add($node4['x'], $neg($resolvedArcSize)),
        'y' => $add($node4['y'], $resolvedArcSize),
    ];
    $resolvedExtensionCount = max(0, (int) $extensionCount);
    $resolvedExtensionStartLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($extensionStartLength, $resolvedArcSize, '2.75rem');
    $resolvedExtensionStemLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($extensionStemLength, $resolvedStemLength, '4rem');
    $resolvedExtensionBridgeLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($extensionBridgeLength, $resolvedBridgeLength, '4rem');
    $extensionStemLengthFor = function (int $extensionIndex) use ($extensionStemLengths, $resolvedExtensionStemLength): string {
        return (string) (
            data_get($extensionStemLengths, $extensionIndex)
            ?? data_get($extensionStemLengths, $extensionIndex - 1)
            ?? $resolvedExtensionStemLength
        );
    };
    $extensionBridgeLengthFor = function (int $extensionIndex) use ($extensionBridgeLengths, $resolvedExtensionBridgeLength): string {
        return (string) (
            data_get($extensionBridgeLengths, $extensionIndex)
            ?? data_get($extensionBridgeLengths, $extensionIndex - 1)
            ?? $resolvedExtensionBridgeLength
        );
    };
    $extensionAnchors = [];
    $extensionBoundsPoints = [];
    $extensionResolvedStemLengths = [];
    $extensionResolvedBridgeLengths = [];
    $nextExtensionTarget = $node3;

    for ($extensionIndex = 1; $extensionIndex <= $resolvedExtensionCount; $extensionIndex++) {
        $currentExtensionStemLength = $extensionStemLengthFor($extensionIndex);
        $currentExtensionBridgeLength = $extensionBridgeLengthFor($extensionIndex);
        $extensionDeltaX = $add($resolvedArcSize, $currentExtensionBridgeLength);
        $extensionDeltaY = $add($add($resolvedExtensionStartLength, $currentExtensionStemLength), $resolvedArcSize);
        $extensionAnchor = [
            'x' => $add($nextExtensionTarget['x'], $extensionDeltaX),
            'y' => $subtract($nextExtensionTarget['y'], $extensionDeltaY),
        ];
        $extensionAnchors[$extensionIndex] = $extensionAnchor;
        $extensionBoundsPoints[] = $extensionAnchor;
        $extensionResolvedStemLengths[$extensionIndex] = $currentExtensionStemLength;
        $extensionResolvedBridgeLengths[$extensionIndex] = $currentExtensionBridgeLength;

        $extensionNode1 = [
            'x' => $extensionAnchor['x'],
            'y' => $add($extensionAnchor['y'], $resolvedExtensionStartLength),
        ];
        $extensionNode2 = [
            'x' => $extensionNode1['x'],
            'y' => $add($extensionNode1['y'], $currentExtensionStemLength),
        ];
        $extensionNode3 = [
            'x' => $add($extensionNode2['x'], $neg($resolvedArcSize)),
            'y' => $add($extensionNode2['y'], $resolvedArcSize),
        ];
        $extensionNode4 = [
            'x' => $add($extensionNode3['x'], $neg($currentExtensionBridgeLength)),
            'y' => $extensionNode3['y'],
        ];
        array_push($extensionBoundsPoints, $extensionNode1, $extensionNode2, $extensionNode3, $extensionNode4);

        \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.merge-right.extension.' . $extensionIndex . '.start', $extensionAnchor);
        \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.merge-right.extension.' . $extensionIndex . '.node.1', $extensionNode1);
        \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.merge-right.extension.' . $extensionIndex . '.node.2', $extensionNode2);
        \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.merge-right.extension.' . $extensionIndex . '.node.3', $extensionNode3);
        \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.merge-right.extension.' . $extensionIndex . '.node.4', $extensionNode4);
        \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.merge-right.extension.' . $extensionIndex . '.end', $extensionNode4);

        $nextExtensionTarget = [
            'x' => $add($nextExtensionTarget['x'], $currentExtensionBridgeLength),
            'y' => $nextExtensionTarget['y'],
        ];
    }

    $bounds = \Gunreip\TranslationWorkbench\Support\TwGraphProtocol\GeometryBounds::fromPoints([
        $anchor,
        $attachAnchor,
        ...$extensionBoundsPoints,
    ], '1rem');

    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.merge-right.start', $anchor);
    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.merge-right.node.1', $node1);
    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.merge-right.node.2', $node2);
    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.merge-right.node.3', $node3);
    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.merge-right.node.4', $node4);
    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.merge-right.node.5', $node5);
    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.merge-right.stem.start', $node1);
    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.merge-right.stem.end', $node2);
    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.merge-right.bridge.start', $node3);
    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.merge-right.bridge.end', $node4);
    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.merge-right.end', $node5);
@endphp

@if ($resolvedDev && $missingAttachTarget)
    <span
        class="tw-graph-protocol-dev-only absolute z-50"
        style="
            left: calc(var(--tw-graph-protocol-trunk-x) + {{ data_get($anchorStart, 'x', '0rem') }});
            bottom: calc(var(--tw-graph-protocol-origin-bottom) + {{ data_get($anchorStart, 'y', '0rem') }});
        "
        title="{{ $id }} | missing attach-to: {{ $attachTo }}"
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
    metrics-side="right"
/>

@foreach (array_reverse($extensionAnchors, true) as $extensionIndex => $extensionAnchor)
    <x-translation-workbench::ui.tw-graph.paths.merge-extension
        :id="$id . '.extension.' . $extensionIndex"
        side="right"
        :anchor-start="$extensionAnchor"
        :start-length="$resolvedExtensionStartLength"
        :stem-length="$extensionResolvedStemLengths[$extensionIndex]"
        :bridge-length="$extensionResolvedBridgeLengths[$extensionIndex]"
        :arc-size="$resolvedArcSize"
        :node-labels="data_get($extensionNodeLabels, $extensionIndex, data_get($extensionNodeLabels, $extensionIndex - 1, []))"
        :color="$resolvedColor"
        :z-index="$zIndex"
        :counter-start="$counterStart + 5 + (($extensionIndex - 1) * 4)"
        :dev="$resolvedDev"
        :show-dev-box="false"
    />
@endforeach

<x-translation-workbench::ui.tw-graph.paths.merge
    :id="$id . '.paths.merge'"
    side="right"
    :anchor-start="$anchor"
    :start-length="$resolvedStartLength"
    :line-width="$resolvedLineWidth"
    :bridge-length="$resolvedBridgeLength"
    :stem-length="$resolvedStemLength"
    :arc-size="$resolvedArcSize"
    :color="$resolvedColor"
    :z-index="$zIndex"
    :node-labels="$nodeLabels"
    :counter-start="$counterStart"
    :dev="$resolvedDev"
    :show-dev-box="false"
/>
