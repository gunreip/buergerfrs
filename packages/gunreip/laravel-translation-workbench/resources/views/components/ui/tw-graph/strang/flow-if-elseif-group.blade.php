{{-- packages/gunreip/laravel-translation-workbench/resources/views/components/ui/tw-graph/strang/flow-if-elseif-group.blade.php --}}
{{--
    Strang: flow-if-elseif-group

    Renders zero to N ELSEIF rows by chaining flow-if-condition components.
    The group keeps ELSEIF rows aligned by rendering every condition label
    with the widest label width used in the group.
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
    'conditions' => [],
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
    $id = filled($id) ? (string) $id : 'strang.flow.if-elseif-group';
    $resolvedColor = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(
        $color,
        $inheritedColor ?? null,
        'zinc',
    );
    $resolvedDev = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::bool(
        $devMode,
        \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::bool($dev),
    );
    $conditions = is_iterable($conditions) ? collect($conditions)->values() : collect();
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
    $sharedRailWidth = filled($conditionRailWidth)
        ? (string) $conditionRailWidth
        : $widthName($conditions
            ->map(fn ($condition) => $widthRank(data_get($condition, 'label.width', data_get($condition, 'conditionLabel.width'))))
            ->max() ?? 2);
    $counterStart = is_numeric($counterStart) ? (int) $counterStart : 1;
    $currentAttachTo = $attachTo;
    $lastConditionId = null;
@endphp

@foreach ($conditions as $index => $condition)
    @php
        $rowCounterStart = $counterStart + ($index * 5);
        $conditionKey = data_get($condition, 'key', data_get($condition, 'id', $index + 1));
        $conditionId = $id . '.' . $conditionKey;
        $lastConditionId = $conditionId;
        $conditionColor = data_get($condition, 'color', $resolvedColor);
        $conditionRail = data_get($condition, 'conditionRailWidth', $sharedRailWidth);
        $conditionLabel = \Gunreip\TranslationWorkbench\Support\TwGraph\TextLabel::normalize(
            data_get($condition, 'label', data_get($condition, 'conditionLabel')),
            null,
            $conditionColor,
        ) ?? ['text' => ['ELSEIF'], 'color' => $conditionColor];
        $conditionLabel['width'] = $conditionRail;
    @endphp

    <x-translation-workbench::ui.tw-graph.strang.flow-if-condition
        :side="$side"
        :id="$conditionId"
        :attach-to="$currentAttachTo"
        :arc-size="data_get($condition, 'arcSize', $arcSize)"
        :condition-label="$conditionLabel"
        :condition-rail-width="$conditionRail"
        :bridge-length="data_get($condition, 'bridgeLength', $bridgeLength)"
        :then-arc-reach="data_get($condition, 'thenArcReach', $thenArcReach)"
        :left-stem-length="data_get($condition, 'leftStemLength', $leftStemLength)"
        :then-stem-length="data_get($condition, 'thenStemLength', $thenStemLength)"
        :left-stem="data_get($condition, 'leftStem', $leftStem)"
        :then-continuation="data_get($condition, 'thenContinuation', $thenContinuation)"
        :color="$resolvedColor"
        :from-color="$resolvedColor"
        :path-tone="data_get($condition, 'pathTone', $pathTone)"
        :z-index="data_get($condition, 'zIndex', $zIndex)"
        :dev-mode="data_get($condition, 'devMode', $resolvedDev)"
        :counter-condition="data_get($condition, 'counterCondition', $rowCounterStart)"
        :counter-bridge-end="data_get($condition, 'counterBridgeEnd', $rowCounterStart + 1)"
        :counter-then-arc-end="data_get($condition, 'counterThenArcEnd', $rowCounterStart + 2)"
        :counter-then-end="data_get($condition, 'counterThenEnd', $rowCounterStart + 3)"
        :counter-left-end="data_get($condition, 'counterLeftEnd', $rowCounterStart + 4)"
        :dev-counter-color="data_get($condition, 'devCounterColor', $devCounterColor)"
    />

    @php
        $currentAttachTo = $conditionId . '.left.anchorNode-end';
    @endphp
@endforeach

@php
    if ($lastConditionId !== null) {
        foreach (['left.anchorNode-end', 'then.anchorNode-end', 'condition.anchorNode-end'] as $anchorName) {
            $anchor = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                $resolvedGraphId,
                $lastConditionId . '.' . $anchorName,
            );

            if ($anchor !== null) {
                \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put(
                    $resolvedGraphId,
                    $id . '.' . $anchorName,
                    $anchor,
                );
            }
        }
    }
@endphp
