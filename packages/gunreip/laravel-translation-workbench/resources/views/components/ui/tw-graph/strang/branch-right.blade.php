{{-- packages/gunreip/laravel-translation-workbench/resources/views/components/ui/tw-graph/strang/branch-right.blade.php --}}
{{--
    Strang: branch-right

    Usage:
    <x-translation-workbench::ui.tw-graph.strang.branch-right
        attach-to="strang.trunk.node.5"
        bridge-length="4rem"
        :node-labels="[3 => ['top' => 'Branch turn']]"
        :continuation-stem="[1 => ['3rem']]"
    />

    Component chain:
    tw-graph -> strang.branch-right -> paths.branch -> segments.* -> primitives.*

    Rule:
    Authoring enters through strang.*. This component owns the right branch
    bounds and delegates only path rendering to paths.branch.
--}}

@aware([
    'graphId' => null,
    'dev' => false,
    'defaultColor' => null,
    'lineLength' => '4rem',
    'arcSize' => '2.75rem',
    'bridgeLength' => null,
    'stemHeight' => null,
])

@props([
    'id' => null,
    'componentCounter' => 1,
    'attachTo' => null,
    'anchorStart' => ['x' => '0rem', 'y' => '0rem'],
    'color' => null,
    'bridgeLength' => null,
    'stemHeight' => null,
    'nodeLabels' => [],
    'continuationStem' => [],
    'counterStart' => 1,
    'zIndex' => 10,
    'devMode' => null,
])

@php
    $resolvedGraphId = filled($graphId ?? null) ? (string) $graphId : 'tw-graph';
    $resolvedComponentCounter = max(1, (int) $componentCounter);
    $id = filled($id)
        ? (string) $id
        : $resolvedGraphId . '.strang.branch-right.' . $resolvedComponentCounter;
    $resolvedColor = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($color, $defaultColor ?? null, 'rose');
    $resolvedDev = $devMode ?? $dev;
    $resolvedLineLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::localOrFallback($lineLength ?? null, '4rem');
    $resolvedArcSize = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::localOrFallback($arcSize ?? null, '2.75rem');
    $localBridgeLength = $attributes->get('bridge-length');
    $localStemHeight = $attributes->get('stem-height');
    $resolvedBridgeLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(
        $localBridgeLength,
        $bridgeLength ?? null,
        $resolvedLineLength,
    );
    $resolvedStemHeight = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(
        $localStemHeight,
        $stemHeight ?? null,
        $resolvedLineLength,
    );

    $add = fn (string $value, string $delta): string => $delta === '0rem' ? $value : 'calc(' . $value . ' + ' . $delta . ')';
    $attachTarget = filled($attachTo)
        ? \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get($resolvedGraphId, (string) $attachTo)
        : null;
    $missingAttachTarget = filled($attachTo) && $attachTarget === null;
    $anchor = $attachTarget ?: [
        'x' => data_get($anchorStart, 'x', '0rem'),
        'y' => data_get($anchorStart, 'y', '0rem'),
    ];

    $node1 = [
        'x' => $add($anchor['x'], $resolvedArcSize),
        'y' => $add($anchor['y'], $resolvedArcSize),
    ];
    $node2 = [
        'x' => $add($node1['x'], $resolvedBridgeLength),
        'y' => $node1['y'],
    ];
    $node3 = [
        'x' => $add($node2['x'], $resolvedArcSize),
        'y' => $add($node2['y'], $resolvedArcSize),
    ];
    $continuationEntries = is_array($continuationStem) ? $continuationStem : [];
    if ($continuationEntries === [] && filled($localStemHeight)) {
        $continuationEntries = [1 => [$resolvedStemHeight]];
    }
    $continuationEnd = collect($continuationEntries)
        ->reduce(function (array $anchor, mixed $entry) use ($add, $resolvedLineLength): array {
            $length = is_array($entry)
                ? (string) (data_get($entry, 'length', data_get($entry, 0)) ?: $resolvedLineLength)
                : (filled($entry) ? (string) $entry : $resolvedLineLength);

            return [
                'x' => $anchor['x'],
                'y' => $add($anchor['y'], $length),
            ];
        }, $node3);
    $bounds = \Gunreip\TranslationWorkbench\Support\TwGraphProtocol\GeometryBounds::fromPoints([
        $anchor,
        $node1,
        $node2,
        $node3,
        $continuationEnd,
    ], '1rem');

    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.branch-right.start', $anchor);
    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.branch-right.node.1', $node1);
    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.branch-right.node.2', $node2);
    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.branch-right.node.3', $node3);
    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.branch-right.bridge.start', $node1);
    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.branch-right.bridge.end', $node2);
    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.branch-right.end', $continuationEnd);
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
/>

<span
    aria-hidden="true"
    class="block pointer-events-none invisible"
    style="
        width: {{ $bounds['width'] }};
        height: {{ $bounds['height'] }};
    "
></span>

<x-translation-workbench::ui.tw-graph.paths.branch
    :id="$id . '.paths.branch'"
    side="right"
    :anchor-start="$anchor"
    :bridge-length="$resolvedBridgeLength"
    :arc-size="$resolvedArcSize"
    :color="$resolvedColor"
    :z-index="$zIndex"
    :node-labels="$nodeLabels"
    :continuation-stem="$continuationStem"
    :counter-start="$counterStart"
    :dev="$resolvedDev"
/>
