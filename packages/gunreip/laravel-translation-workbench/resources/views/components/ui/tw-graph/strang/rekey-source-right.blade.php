{{-- packages/gunreip/laravel-translation-workbench/resources/views/components/ui/tw-graph/strang/rekey-source-right.blade.php --}}
{{--
    Strang: rekey-source-right

    Component chain:
    tw-graph -> strang.rekey-source-right -> paths.merge -> segments.* -> primitives.*

    Rule:
    Right-side mirror of rekey-source-left.
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
    'stemContinuation' => [],
    'compressedStemParts' => [
        'beforeLength' => '0.75rem',
        'gapLength' => '1rem',
    ],
    'counterStart' => 1,
    'zIndex' => 8,
    'devMode' => null,
])

@php
    $resolvedGraphId = filled($graphId ?? null) ? (string) $graphId : 'tw-graph';
    $resolvedComponentCounter = max(1, (int) $componentCounter);
    $id = filled($id) ? (string) $id : $resolvedGraphId . '.strang.rekey-source-right.' . $resolvedComponentCounter;
    $resolvedColor = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($color, $defaultColor ?? null, 'sky');
    $resolvedDev = $devMode ?? $dev;
    $resolvedLineLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::localOrGraphString($lineLength ?? null, 'line_length', '4rem');
    $resolvedArcSize = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::localOrGraphString($arcSize ?? null, 'arc_size', '2.75rem');
    $resolvedArcInSize = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(data_get($arcSizes, 1, data_get($arcSizes, 'in')), $resolvedArcSize, '2.75rem');
    $resolvedArcOutSize = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(data_get($arcSizes, 2, data_get($arcSizes, 'out')), $resolvedArcSize, '2.75rem');
    $resolvedStartLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($startLength, $resolvedArcInSize, '2.75rem');
    $resolvedBridgeLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($attributes->get('bridge-length'), $bridgeLength ?? null, \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::graphString('bridge_length', $resolvedLineLength));
    $resolvedStemLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($attributes->get('stem-length'), $stemLength ?? null, \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::graphString('stem_length', $resolvedLineLength));
    $add = fn (string $value, string $delta): string => $delta === '0rem' ? $value : 'calc(' . $value . ' + ' . $delta . ')';
    $neg = fn (string $value): string => 'calc(' . $value . ' * -1)';
    $subtract = fn (string $value, string $delta): string => $add($value, $neg($delta));
    $stemContinuationEntries = is_array($stemContinuation) ? $stemContinuation : [];
    $stemContinuationTotal = function (array $continuation) use ($add, $resolvedStemLength): string {
        $total = '0rem';

        foreach ($continuation as $entry) {
            $length = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(is_array($entry) ? data_get($entry, 'length', data_get($entry, 0)) : $entry, $resolvedStemLength, '4rem');
            $total = $add($total, $length);
        }

        return $total;
    };
    $resolvedStemContinuationTotal = $stemContinuationTotal($stemContinuationEntries);
    $attachTarget = filled($attachTo) ? \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get($resolvedGraphId, (string) $attachTo) : null;
    $missingAttachTarget = filled($attachTo) && $attachTarget === null;
    $rekeyWidth = $add($add($resolvedArcInSize, $resolvedBridgeLength), $resolvedArcOutSize);
    $rekeyHeight = $add($add($add($add($resolvedStartLength, $resolvedStemLength), $resolvedStemContinuationTotal), $resolvedArcInSize), $resolvedArcOutSize);
    $anchor = [
        'x' => $attachTarget ? $add($attachTarget['x'], $rekeyWidth) : data_get($anchorStart, 'x', '0rem'),
        'y' => $attachTarget ? $subtract($attachTarget['y'], $rekeyHeight) : data_get($anchorStart, 'y', '0rem'),
    ];
    $attachAnchor = [
        'x' => $add($anchor['x'], $neg($rekeyWidth)),
        'y' => $add($anchor['y'], $rekeyHeight),
    ];
    $bounds = \Gunreip\TranslationWorkbench\Support\TwGraphProtocol\GeometryBounds::fromPoints([$anchor, $attachAnchor], '1rem');

    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.rekey-source-right.start', $anchor);
    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.rekey-source-right.end', $attachAnchor);
@endphp

@if ($resolvedDev && $missingAttachTarget)
    <span
        class="tw-graph-protocol-dev-only absolute z-50"
        style="left: calc(var(--tw-graph-protocol-trunk-x) + {{ data_get($anchorStart, 'x', '0rem') }}); bottom: calc(var(--tw-graph-protocol-origin-bottom) + {{ data_get($anchorStart, 'y', '0rem') }});"
        title="{{ \Gunreip\TranslationWorkbench\Support\TwGraph\DevIdentifier::label($id) }} | missing attach-to: {{ \Gunreip\TranslationWorkbench\Support\TwGraph\ElementIdentifier::normalize($attachTo) }}"
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

<x-translation-workbench::ui.tw-graph.paths.merge
    :id="$id . '.paths.rekey-source'"
    side="right"
    :anchor-start="$anchor"
    :start-length="$resolvedStartLength"
    :line-width="$lineWidth"
    :bridge-length="$resolvedBridgeLength"
    :stem-length="$resolvedStemLength"
    :stem-continuation="$stemContinuationEntries"
    :compressed-stem-parts="$compressedStemParts"
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
