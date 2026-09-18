{{-- Authored branch definitions; the component owns repeated anchor wiring, not the example view. --}}
@aware(['graphId' => null, 'color' => null, 'dev' => false])
@php
    $inheritedColor = $color;
    $inheritedDev = $dev;
@endphp
@props([
    'id' => null,
    'componentCounter' => 1,
    'attachTo' => null,
    'anchorStart' => ['x' => '0rem', 'y' => '0rem'],
    'side' => 'left',
    'direction' => 'bottom-top',
    'conditionLabel' => ['text' => ['IF condition?'], 'width' => 'halfLong'],
    'ifStart' => ['text' => ['IF action'], 'width' => 'half'],
    'elseifs' => [],
    'beforeLength' => '2rem',
    'afterLength' => '2rem',
    'elseifBeforeLength' => '6rem',
    'elseifAfterLength' => '2rem',
    'stemLength' => '8rem',
    'arcRadius' => null,
    'bridgeLength' => '2rem',
    'ifEnd' => [],
    'nodeLabels' => [],
    'nodeEnd' => true,
    'color' => null,
    'devMode' => null,
    'zIndex' => 20,
])

@php
    // Keep the authoring ID throughout the internal component chain.
    $previousRootIdentifier = \Gunreip\TranslationWorkbench\Support\TwGraph\RootIdentifier::enter($id);
    try {
@endphp@php
    if (! is_array($elseifs) || $elseifs === []) {
        throw new \InvalidArgumentException('flow-if-elseif-multi requires at least one elseifs entry.');
    }
    $resolvedGraphId = $graphId ?: 'tw-graph';
    $id = filled($id) ? (string) $id : $resolvedGraphId . '.flow.if-elseif-multi.' . max(1, (int) $componentCounter);
    $resolvedColor = $color ?? $inheritedColor ?? 'zinc';
    $resolvedDev = $devMode ?? $inheritedDev;
    $rows = [[
        'id' => $id . '.if',
        'conditionLabel' => $conditionLabel,
        'actionLabel' => $ifStart,
        'beforeLength' => $beforeLength,
        'afterLength' => $afterLength,
    ]];
    $keys = [];
    foreach (array_values($elseifs) as $index => $branch) {
        if (! is_array($branch)) {
            throw new \InvalidArgumentException('Each elseifs entry must be an array.');
        }
        $key = (string) ($branch['key'] ?? ($index + 1));
        if ($key === '' || in_array($key, $keys, true)) {
            throw new \InvalidArgumentException('ELSEIF branch keys must be nonempty and unique.');
        }
        $keys[] = $key;
        $rows[] = [
            'id' => $id . '.elseif.' . $key,
            'conditionLabel' => $branch['conditionLabel'] ?? ['text' => ['ELSEIF condition?']],
            'actionLabel' => $branch['actionLabel'] ?? ['text' => ['ELSEIF action']],
            'beforeLength' => $branch['beforeLength'] ?? $elseifBeforeLength,
            'afterLength' => $branch['afterLength'] ?? $elseifAfterLength,
            'color' => $branch['color'] ?? $resolvedColor,
        ];
    }
    $widths = [];
    foreach ($rows as $rowIndex => &$row) {
        $row['color'] = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(
            is_array($row['conditionLabel']) ? data_get($row['conditionLabel'], 'color') : null,
            $row['color'] ?? $resolvedColor, 'zinc',
        );
        $row['actionColor'] = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(
            is_array($row['actionLabel']) ? data_get($row['actionLabel'], 'color') : null,
            $row['color'], 'zinc',
        );
        $row['actionLabel'] = \Gunreip\TranslationWorkbench\Support\TwGraph\TextLabel::normalize($row['actionLabel'], 'center', $row['actionColor'])
            ?? ['text' => ['Action'], 'width' => 'half'];
        if ($rowIndex > 0 && ! \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::bool(data_get($row, 'actionLabel.return'), true)) {
            $row['actionLabel']['returnOffset'] ??= '12rem';
        }
        $row['width'] = \Gunreip\TranslationWorkbench\Support\TwGraph\LabelBridge::labelWidth($row['actionLabel']);
        $widths[] = $row['width'];
    }
    unset($row);
    $returnColor = $rows[0]['actionColor'];
    $normalizedFalseLabel = \Gunreip\TranslationWorkbench\Support\TwGraph\TextLabel::normalize($ifEnd, 'center', $resolvedColor);
    if ($normalizedFalseLabel !== null) {
        $widths[] = \Gunreip\TranslationWorkbench\Support\TwGraph\LabelBridge::labelWidth($normalizedFalseLabel);
    }
    $bridge = \Gunreip\TranslationWorkbench\Support\TwGraph\LabelBridge::bridgeLength($bridgeLength);
    $span = 'calc(max(' . implode(', ', $widths) . ') + (' . $bridge . ' * 2))';
    $routeSide = $side === 'right' ? 'left' : 'right';
    $falseSide = $side === 'right' ? 'left' : 'right';
    $trueInformation = ['text' => ['True'], 'width' => 'half', 'badgeColor' => 'green'];
    $falseInformation = ['text' => ['False'], 'width' => 'half', 'badgeColor' => 'rose'];
    $cursor = filled($attachTo) ? \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get($resolvedGraphId, (string) $attachTo) : null;
    $cursor = $cursor ?: $anchorStart;
    $inputAnchor = $cursor;
    $previousRow = null;
@endphp

@foreach ($rows as $index => $row)
    @php
        $rowBridge = 'calc((' . $span . ' - ' . $row['width'] . ') / 2)';
        $counter = ($index * 2) + 1;
    @endphp
    @if ($loop->last)
        {{-- The final simple IF owns the one bypass and the common output. --}}
        <x-translation-workbench::ui.tw-graph.strang.flow-if
            :id="$row['id']"
            :anchor-start="$cursor"
            :side="$side"
            :direction="$direction"
            :condition-label="$row['conditionLabel']"
            :if-start="$row['actionLabel']"
            :return-color="$returnColor"
            :if-end="$ifEnd"
            :before-length="$row['beforeLength']"
            :after-length="$row['afterLength']"
            :stem-length="$stemLength"
            :arc-radius="$arcRadius"
            :true-bridge-length="$rowBridge"
            :bridge-length="$bridge"
            :node-labels="$nodeLabels"
            :node-end="$nodeEnd"
            :counter-start="$counter"
            :left-counter-end="$counter + 1"
            :false-stem-counter="$counter + 2"
            :right-counter-end="$counter + 3"
            :color="$row['color']"
            :dev-mode="$resolvedDev"
            :z-index="$zIndex"
        />
    @else
        <x-translation-workbench::ui.tw-graph.strang.flow-step
            :id="$row['id'] . '.question'"
            :anchor-start="$cursor"
            :direction="$direction"
            :before-length="$row['beforeLength']"
            :before-color="data_get($cursor, 'color')"
            :after-length="$row['afterLength']"
            :step-label="$row['conditionLabel']"
            :node-labels="[$falseSide => $falseInformation]"
            :counter-end="$counter"
            :color="$row['color']"
            :dev-mode="$resolvedDev"
            :z-index="$zIndex"
        />
        <x-translation-workbench::ui.tw-graph.parts.sideways
            :id="$row['id'] . '.true'"
            :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get($resolvedGraphId, $row['id'] . '.question.anchorNode-end')"
            :side="$routeSide"
            :direction="$direction"
            :arc-radius="$arcRadius"
            :bridge-length="$rowBridge"
            :bridge-label="$row['actionLabel']"
            :bridge-out-length="! \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::bool(data_get($row, 'actionLabel.return'), true) && isset($row['actionLabel']['returnOffset']) ? 'calc(' . $rowBridge . ' + ' . $row['actionLabel']['returnOffset'] . ')' : null"
            :node-label-left="$side === 'left' ? $trueInformation : null"
            :node-label-right="$side === 'right' ? $trueInformation : null"
            :dev-counter-end="$counter + 1"
            :color="$row['actionColor']"
            :dev-mode="$resolvedDev"
            :z-index="$zIndex"
        />
    @endif
    @php
        $actionEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get($resolvedGraphId, $row['id'] . '.true.anchorNode-end');
        $cursor = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get($resolvedGraphId, $row['id'] . '.question.anchorNode-end');
        $rows[$index]['actionEnd'] = $actionEnd;
        $previousRow = $row;
    @endphp
@endforeach
@foreach ($rows as $index => $returnRow)
    @continue($loop->last)
    @php
        $returnTarget = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get($resolvedGraphId, $previousRow['id'] . '.anchorNode-end');
        foreach (array_slice($rows, $index + 1) as $candidate) {
            if (\Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::bool(data_get($candidate, 'actionLabel.return'), true)) {
                $returnTarget = $candidate['actionEnd'];
                break;
            }
        }
        \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, $returnRow['id'] . '.true.anchorNode-return', $returnTarget);
    @endphp
    @if (\Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::bool(data_get($returnRow, 'actionLabel.return'), true))
        <x-translation-workbench::ui.tw-graph.parts.start
            :id="$returnRow['id'] . '.true.stem'"
            :line-jumps="data_get($returnRow['actionLabel'], 'stemLineJumps', [])"
            :anchor-start="$returnRow['actionEnd']"
            :direction="$direction"
            :length="$direction === 'top-bottom' ? 'calc(' . $returnRow['actionEnd']['y'] . ' - ' . $returnTarget['y'] . ')' : 'calc(' . $returnTarget['y'] . ' - ' . $returnRow['actionEnd']['y'] . ')'"
            :gradient="false" :node-end="false" :dev-counter-end="false"
            :color="$returnColor" :dev-mode="$resolvedDev" :z-index="$zIndex"
        />
    @endif
@endforeach
@php
    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, $id . '.anchorNode-start', $inputAnchor);
    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put(
        $resolvedGraphId, $id . '.anchorNode-end',
        \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get($resolvedGraphId, $previousRow['id'] . '.anchorNode-end'),
    );
    foreach ($rows as $index => $returnRow) {
        $followingRows = array_slice($rows, $index + 1);
        $returnPaths = [];
        $returnOutputs = [$id . '.anchorNode-end'];
        foreach ($followingRows as $followingRow) {
            if (\Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::bool(data_get($followingRow, 'actionLabel.return'), true)) {
                $returnPaths[] = $followingRow['id'] . '.true.stem';
            }
            $returnOutputs[] = $followingRow['id'] . '.true.anchorNode-return';
        }
        \Gunreip\TranslationWorkbench\Support\TwGraph\ReturnColorRegistry::rail(
            $resolvedGraphId, $returnRow['id'] . '.true.anchorNode-return', $returnPaths, $returnOutputs,
        );
    }
    // Publish the terminal fallback separately from the common continuation.
    $fallbackId = $previousRow['id'];
    foreach (['end', 'return'] as $fallbackAnchor) {
        \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put(
            $resolvedGraphId, $id . '.false.anchorNode-' . $fallbackAnchor,
            \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get($resolvedGraphId, $fallbackId . '.false.anchorNode-' . $fallbackAnchor),
        );
    }
    \Gunreip\TranslationWorkbench\Support\TwGraph\ReturnColorRegistry::rail(
        $resolvedGraphId, $id . '.false.anchorNode-return', [], [$id . '.anchorNode-end', $fallbackId . '.anchorNode-end'],
    );
@endphp

@php
    } finally {
        \Gunreip\TranslationWorkbench\Support\TwGraph\RootIdentifier::restore($previousRootIdentifier);
    }
@endphp
