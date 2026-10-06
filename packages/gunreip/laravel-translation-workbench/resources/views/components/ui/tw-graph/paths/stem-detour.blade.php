@php
    // Diagnostic settings belong exclusively to the enclosing tw-graph canvas.
    $attributes = ($attributes ?? new \Illuminate\View\ComponentAttributeBag)->except(['dev', 'dev-mode', 'coordinates']);
@endphp
{{-- Explicit sideways detour. The endpoint stays on the original vertical axis. --}}
@aware(['graphId' => null])
@props([
    'id', 'anchorStart', 'length', 'side' => 'right', 'direction' => 'bottom-top',
    'bridgeLength' => '4rem', 'arcRadius' => '2rem', 'beforeLength' => '0rem', 'afterLength' => '2rem',
    'color' => 'zinc', 'nodeLabelLeft' => null, 'nodeLabelRight' => null,
     'devCounterStart' => 1, 'zIndex' => 20,
])

@php
    $twGraphRegionFrame = \Gunreip\TranslationWorkbench\Support\TwGraph\ComponentRegion::begin(
        '...::ui.tw-graph.paths.stem-detour',
        \Gunreip\TranslationWorkbench\Support\TwGraph\CanvasDiagnostics::current($__env),
    );
    try {
@endphp

@php
    if (!in_array($side, ['left', 'right'], true) || !in_array($direction, ['bottom-top', 'top-bottom'], true)) {
        throw new \InvalidArgumentException('stem-detour requires left/right and bottom-top/top-bottom.');
    }
    $measure = static function ($value, $name, $positive = false) {
        $number = \Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry::evaluateRemExpression((string) $value);
        if ($number === null || !is_finite($number) || ($positive ? $number <= 0 : $number < 0)) {
            throw new \InvalidArgumentException('stem-detour '.$name.' must resolve to a '.($positive ? 'positive' : 'nonnegative').' rem length.');
        }
        return $number;
    };
    $radius = $measure($arcRadius, 'arcRadius', true);
    $measure($bridgeLength, 'bridgeLength');
    $before = $measure($beforeLength, 'beforeLength');
    $after = $measure($afterLength, 'afterLength');
    $height = $measure($length, 'length', true);
    if ($height < $before + $after + 4 * $radius) {
        throw new \InvalidArgumentException('stem-detour length must cover beforeLength + afterLength + 4 * arcRadius.');
    }
    $middleLength = 'calc('.$length.' - '.$beforeLength.' - '.$afterLength.' - (4 * '.$arcRadius.'))';
    $detourGraph = $graphId ?: 'tw-graph';
    $detourCursor = $anchorStart;
    $detourCounter = (int) $devCounterStart;
@endphp
@if ($before > 0)
    <x-translation-workbench::ui.tw-graph.parts.start
        :id="$id . '.before'" :anchor-start="$detourCursor" :length="$beforeLength" :direction="$direction"
        :gradient="false" :joint-arrow-end="true" :dev-counter-end="$detourCounter"
        :color="$color" :z-index="$zIndex"
    />
    @php
        $detourCounter++;
        $detourCursor = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get($detourGraph, $id . '.before.anchorNode-end');
    @endphp
@endif
<x-translation-workbench::ui.tw-graph.parts.sideways
    :id="$id . '.outward'" :anchor-start="$detourCursor" :side="$side"
    :direction="$direction" :arc-radius="$arcRadius" :bridge-length="$bridgeLength"
    :joint-arrow-end="true" :dev-counter-end="$detourCounter"
    :color="$color" :z-index="$zIndex"
/>
@php
    $detourCounter++;
@endphp
<x-translation-workbench::ui.tw-graph.parts.start
    :id="$id . '.stem'"
    :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get($detourGraph, $id . '.outward.anchorNode-end')"
    :length="$middleLength" :direction="$direction" :gradient="false"
    :joint-arrow-end="true" :dev-counter-end="$detourCounter"
    :color="$color" :z-index="$zIndex"
/>
@php
    $detourCounter++;
@endphp
<x-translation-workbench::ui.tw-graph.parts.sideways
    :id="$id . '.inward'"
    :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get($detourGraph, $id . '.stem.anchorNode-end')"
    :side="$side === 'right' ? 'left' : 'right'" :direction="$direction"
    :arc-radius="$arcRadius" :bridge-length="$bridgeLength"
    :joint-arrow-end="true" :dev-counter-end="$detourCounter"
    :color="$color" :z-index="$zIndex"
/>
@php
    $detourCounter++;
@endphp
<x-translation-workbench::ui.tw-graph.parts.start
    :id="$id . '.after'"
    :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get($detourGraph, $id . '.inward.anchorNode-end')"
    :length="$afterLength" :direction="$direction" :gradient="false"
    :node-label-left="$nodeLabelLeft" :node-label-right="$nodeLabelRight"
    :dev-counter-end="$detourCounter" :color="$color" :z-index="$zIndex"
/>
@php
    $detourCounter++;
    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($detourGraph, $id . '.anchorNode-start', $anchorStart);
    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($detourGraph, $id . '.anchorNode-end', array_replace(
        \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get($detourGraph, $id . '.after.anchorNode-end'),
        ['devCounterNext' => (string) $detourCounter],
    ));
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
