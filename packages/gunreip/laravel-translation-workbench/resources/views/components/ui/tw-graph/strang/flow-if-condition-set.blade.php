{{-- packages/gunreip/laravel-translation-workbench/resources/views/components/ui/tw-graph/strang/flow-if-condition-set.blade.php --}}
{{--
    Strang: flow-if-condition-set

    Coordinates one IF row and zero to N ELSEIF rows. The set renders the
    existing atomic condition components, but first normalizes all condition
    labels to the widest label width used by IF/ELSEIF.
--}}

@aware([
    'graphId' => null,
    'color' => null,
    'dev' => false,
])

@php
    $inheritedColor = $color ?? null;
@endphp

@props([
    'side' => 'left',
    'id' => null,
    'attachTo' => null,
    'ifId' => null,
    'elseifId' => null,
    'ifConditionLabel' => null,
    'elseifConditions' => [],
    'conditionRailWidth' => null,
    'arcSize' => null,
    'bridgeLength' => null,
    'thenArcReach' => null,
    'leftStemLength' => null,
    'thenStemLength' => null,
    'leftStem' => true,
    'thenContinuation' => 'stem',
    'color' => null,
    'pathTone' => 'surface',
    'zIndex' => 20,
    'devMode' => null,
    'counterStart' => 1,
    'devCounterColor' => 'zinc',
])

@php
    $resolvedGraphId = filled($graphId ?? null) ? (string) $graphId : 'tw-graph';
    $id = filled($id) ? (string) $id : 'strang.flow.if-condition-set';
    $resolvedIfId = filled($ifId) ? (string) $ifId : $id . '.if';
    $resolvedElseifId = filled($elseifId) ? (string) $elseifId : $id . '.elseif';
    $resolvedColor = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(
        $color,
        $inheritedColor ?? null,
        'zinc',
    );
    $resolvedDev = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::bool(
        $devMode,
        \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::bool($dev),
    );
    $elseifConditions = is_iterable($elseifConditions) ? collect($elseifConditions)->values() : collect();
    $widthRank = fn (mixed $width): int => match (true) {
        $width === 'long' => 4,
        in_array($width, ['halfLong', 'half-long', 'half_long'], true) => 3,
        $width === 'default' || blank($width) => 2,
        in_array($width, ['half', 'halfWidth', 'half-width', 'half_width'], true) => 1,
        default => 2,
    };
    $widthName = fn (int $rank): string => match ($rank) {
        4 => 'long',
        3 => 'halfLong',
        1 => 'half',
        default => 'default',
    };
    $sharedWidth = filled($conditionRailWidth)
        ? (string) $conditionRailWidth
        : $widthName(max(
            $widthRank(data_get($ifConditionLabel, 'width')),
            $elseifConditions
                ->map(fn ($condition) => $widthRank(data_get($condition, 'label.width', data_get($condition, 'conditionLabel.width'))))
                ->max() ?? 2,
        ));
    $normalizedIfConditionLabel = \Gunreip\TranslationWorkbench\Support\TwGraph\TextLabel::normalize(
        $ifConditionLabel,
        null,
        $resolvedColor,
    ) ?? ['text' => ['IF'], 'color' => $resolvedColor];
    $normalizedIfConditionLabel['width'] = $sharedWidth;
    $counterStart = is_numeric($counterStart) ? (int) $counterStart : 1;
    $elseifCounterStart = $counterStart + 5;
@endphp

<x-translation-workbench::ui.tw-graph.strang.flow-if-condition
    :side="$side"
    :id="$resolvedIfId"
    :attach-to="$attachTo"
    :arc-size="$arcSize"
    :condition-label="$normalizedIfConditionLabel"
    :condition-rail-width="$sharedWidth"
    :bridge-length="$bridgeLength"
    :then-arc-reach="$thenArcReach"
    :left-stem-length="$leftStemLength"
    :then-stem-length="$thenStemLength"
    :left-stem="$leftStem"
    :then-continuation="$thenContinuation"
    :color="$resolvedColor"
    :from-color="$resolvedColor"
    :path-tone="$pathTone"
    :z-index="$zIndex"
    :dev-mode="$resolvedDev"
    :counter-condition="$counterStart"
    :counter-bridge-end="$counterStart + 1"
    :counter-then-arc-end="$counterStart + 2"
    :counter-then-end="$counterStart + 3"
    :counter-left-end="$counterStart + 4"
    :dev-counter-color="$devCounterColor"
/>

<x-translation-workbench::ui.tw-graph.strang.flow-if-elseif-group
    :side="$side"
    :id="$resolvedElseifId"
    :attach-to="$resolvedIfId . '.left.anchorNode-end'"
    :conditions="$elseifConditions"
    :condition-rail-width="$sharedWidth"
    :arc-size="$arcSize"
    :bridge-length="$bridgeLength"
    :then-arc-reach="$thenArcReach"
    :left-stem-length="$leftStemLength"
    :then-stem-length="$thenStemLength"
    :left-stem="$leftStem"
    :then-continuation="$thenContinuation"
    :color="$resolvedColor"
    :path-tone="$pathTone"
    :z-index="$zIndex"
    :dev-mode="$resolvedDev"
    :counter-start="$elseifCounterStart"
    :dev-counter-color="$devCounterColor"
/>

@php
    $anchorSourceId = $elseifConditions->isNotEmpty() ? $resolvedElseifId : $resolvedIfId;

    foreach (['left.anchorNode-end', 'then.anchorNode-end', 'condition.anchorNode-end'] as $anchorName) {
        $anchor = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
            $resolvedGraphId,
            $anchorSourceId . '.' . $anchorName,
        );

        if ($anchor !== null) {
            \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put(
                $resolvedGraphId,
                $id . '.' . $anchorName,
                $anchor,
            );
        }
    }
@endphp
