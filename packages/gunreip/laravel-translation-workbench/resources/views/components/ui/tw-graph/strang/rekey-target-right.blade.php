{{-- packages/gunreip/laravel-translation-workbench/resources/views/components/ui/tw-graph/strang/rekey-target-right.blade.php --}}
{{--
    Strang: rekey-target-right

    Component chain:
    tw-graph -> strang.rekey-target-right -> paths.branch -> segments.* -> primitives.*

    Rule:
    Right-side mirror of rekey-target-left.
--}}

@aware([
    'graphId' => null,
    'dev' => false,
    'defaultColor' => null,
    'lineLength' => '4rem',
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
    'bridgeLength' => null,
    'stemLength' => null,
    'stemContinuation' => [],
    'endLength' => null,
    'endLabel' => null,
    'capLength' => null,
    'nodeLabels' => [],
    'counterStart' => 1,
    'zIndex' => 7,
    'devMode' => null,
])

@php
    $resolvedGraphId = filled($graphId ?? null) ? (string) $graphId : 'tw-graph';
    $resolvedComponentCounter = max(1, (int) $componentCounter);
    $id = filled($id) ? (string) $id : $resolvedGraphId . '.strang.rekey-target-right.' . $resolvedComponentCounter;
    $resolvedColor = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($color, $defaultColor ?? null, 'sky');
    $resolvedDev = $devMode ?? $dev;
    $resolvedLineLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::localOrFallback($lineLength ?? null, '4rem');
    $resolvedArcSize = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::localOrFallback($arcSize ?? null, '2.75rem');
    $resolvedBridgeLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($bridgeLength, $bridgeLength ?? null, $resolvedLineLength);
    $resolvedStemLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($stemLength, $stemLength ?? null, $resolvedLineLength);
    $resolvedEndLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($endLength, $resolvedLineLength, '4rem');
    $resolvedCapLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($capLength, $capLength ?? null, '1.75rem');
    $add = fn (string $value, string $delta): string => $delta === '0rem' ? $value : 'calc(' . $value . ' + ' . $delta . ')';
    $stemContinuationEntries = is_array($stemContinuation) ? $stemContinuation : [];
    $stemTotal = '0rem';

    foreach ($stemContinuationEntries === [] ? [1 => $resolvedStemLength] : $stemContinuationEntries as $entry) {
        $length = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(
            is_array($entry) ? data_get($entry, 'length', data_get($entry, 0)) : $entry,
            $resolvedStemLength,
            '4rem',
        );
        $stemTotal = $add($stemTotal, $length);
    }

    $attachTarget = filled($attachTo) ? \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get($resolvedGraphId, (string) $attachTo) : null;
    $missingAttachTarget = filled($attachTo) && $attachTarget === null;
    $anchor = $attachTarget ?: [
        'x' => data_get($anchorStart, 'x', '0rem'),
        'y' => data_get($anchorStart, 'y', '0rem'),
    ];
    $arcInEnd = ['x' => $add($anchor['x'], $resolvedArcSize), 'y' => $add($anchor['y'], $resolvedArcSize)];
    $bridgeEnd = ['x' => $add($arcInEnd['x'], $resolvedBridgeLength), 'y' => $arcInEnd['y']];
    $arcOutEnd = ['x' => $add($bridgeEnd['x'], $resolvedArcSize), 'y' => $add($bridgeEnd['y'], $resolvedArcSize)];
    $stemEnd = ['x' => $arcOutEnd['x'], 'y' => $add($arcOutEnd['y'], $stemTotal)];
    $endAnchor = ['x' => $stemEnd['x'], 'y' => $add($stemEnd['y'], $resolvedEndLength)];
    $endLabelConfig = is_array($endLabel)
        ? $endLabel
        : (filled($endLabel) ? ['text' => $endLabel] : null);
    $stemCount = count($stemContinuationEntries === [] ? [1 => $resolvedStemLength] : $stemContinuationEntries);
    $endCounter = (int) $counterStart + 3 + $stemCount;
    $endSegment = [
        'id' => $id . '.path.rekey-target.end',
        'direction' => 'bottom-top',
        'length' => $resolvedEndLength,
        'anchorStart' => $stemEnd,
        'anchorEnd' => $endAnchor,
        'nodeStart' => false,
        'nodeEnd' => false,
        'cap' => true,
        'capLength' => $resolvedCapLength,
        'devCounterEnd' => $endCounter,
        'devCounterColor' => $resolvedColor,
        'color' => $resolvedColor,
        'zIndex' => $zIndex,
        'dev' => $resolvedDev,
        'endLabel' => $endLabelConfig,
    ];
    $bounds = \Gunreip\TranslationWorkbench\Support\TwGraphProtocol\GeometryBounds::fromPoints([$anchor, $arcInEnd, $bridgeEnd, $arcOutEnd, $stemEnd, $endAnchor], '1rem');

    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.rekey-target-right.start', $anchor);
    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.rekey-target-right.stem.end', $stemEnd);
    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.rekey-target-right.end', $endAnchor);
@endphp

@if ($resolvedDev && $missingAttachTarget)
    <span
        class="tw-graph-protocol-dev-only absolute z-50"
        style="left: calc(var(--tw-graph-protocol-trunk-x) + {{ data_get($anchorStart, 'x', '0rem') }}); bottom: calc(var(--tw-graph-protocol-origin-bottom) + {{ data_get($anchorStart, 'y', '0rem') }});"
        title="{{ $id }} | missing attach-to: {{ $attachTo }}"
    >
        <flux:badge color="red">{{ __('Missing anchor') }}: {{ $attachTo }}</flux:badge>
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

<x-translation-workbench::ui.tw-graph.paths.branch
    :id="$id . '.paths.rekey-target'"
    side="right"
    :anchor-start="$anchor"
    :bridge-length="$resolvedBridgeLength"
    :stem-length="$resolvedStemLength"
    :stem-continuation="$stemContinuationEntries"
    :color="$resolvedColor"
    :z-index="$zIndex"
    :node-labels="$nodeLabels"
    :counter-start="$counterStart"
    :dev="$resolvedDev"
/>

<x-translation-workbench::ui.tw-graph.segments.end
    :segment="$endSegment"
    :dev="$resolvedDev"
/>
