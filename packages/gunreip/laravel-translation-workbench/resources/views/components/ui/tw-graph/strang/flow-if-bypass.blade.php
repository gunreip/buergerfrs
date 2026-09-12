{{-- Connect the IF entry rail to the ENDIF output using the registered anchors. --}}
@aware(['graphId' => null, 'color' => null, 'dev' => false])
@php
    $inheritedColor = $color ?? null;
@endphp
@props([
    'side' => 'left',
    'id',
    'attachTo',
    'mergeTo',
    'arcSize' => null,
    'color' => null,
    'pathTone' => 'surface',
    'zIndex' => 20,
    'devMode' => null,
    'counterStart' => 1,
    'devCounterColor' => 'zinc',
])

@php
    $isRight = $side === 'right';
    $start = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get($graphId, $attachTo);
    $end = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get($graphId, $mergeTo);
    $radius = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::localOrGraphString($arcSize, 'arc_size', '2.75rem');
    $resolvedDev = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::bool($devMode, (bool) $dev);
    $counterStart = is_numeric($counterStart) ? (int) $counterStart : 1;
    $resolvedColor = $color ?? $inheritedColor ?? 'zinc';
@endphp

@if ($start !== null && $end !== null)
    @php
        $stemEnd = ['x' => $start['x'], 'y' => 'calc(' . $end['y'] . ' - 2 * ' . $radius . ')'];
        $bridgeStart = ['x' => 'calc(' . $start['x'] . ($isRight ? ' - ' : ' + ') . $radius . ')', 'y' => 'calc(' . $end['y'] . ' - ' . $radius . ')'];
        $bridgeEnd = ['x' => 'calc(' . $end['x'] . ($isRight ? ' + ' : ' - ') . $radius . ')', 'y' => $bridgeStart['y']];
        $shared = ['color' => $resolvedColor, 'tone' => $pathTone, 'zIndex' => $zIndex, 'dev' => $resolvedDev, 'devCounterColor' => $devCounterColor];
    @endphp

    <x-translation-workbench::ui.tw-graph.segments.path :segment="array_merge($shared, [
        'id' => $id . '.stem',
        'direction' => 'bottom-top',
        'length' => 'calc(' . $stemEnd['y'] . ' - ' . $start['y'] . ')',
        'anchorStart' => $start,
        'anchorEnd' => $stemEnd,
        'devCounterEnd' => $counterStart,
        'nodeEnd' => true,
        'nodeEndDot' => false,
        'jointArrowEnd' => true,
    ])" />
    <x-translation-workbench::ui.tw-graph.segments.arc :segment="array_merge($shared, [
        'id' => $id . ($isRight ? '.arc-east-north' : '.arc-west-north'),
        'startAnchor' => $isRight ? 'e' : 'w',
        'endAnchor' => 'n',
        'arcSize' => $radius,
        'anchorStart' => $stemEnd,
        'anchorEnd' => $bridgeStart,
        'devCounterEnd' => $counterStart + 1,
        'nodeEnd' => true,
        'nodeEndDot' => false,
        'jointArrowEnd' => true,
        'jointArrowEndDirection' => $isRight ? 'left' : 'right',
    ])" />
    <x-translation-workbench::ui.tw-graph.segments.path :segment="array_merge($shared, [
        'id' => $id . '.bridge',
        'direction' => $isRight ? 'right-left' : 'left-right',
        'length' => $isRight
            ? 'calc(' . $bridgeStart['x'] . ' - ' . $bridgeEnd['x'] . ')'
            : 'calc(' . $bridgeEnd['x'] . ' - ' . $bridgeStart['x'] . ')',
        'anchorStart' => $bridgeStart,
        'anchorEnd' => $bridgeEnd,
        'devCounterEnd' => $counterStart + 2,
        'nodeEnd' => true,
        'nodeEndDot' => false,
        'jointArrowEnd' => true,
    ])" />
    <x-translation-workbench::ui.tw-graph.segments.arc :segment="array_merge($shared, [
        'id' => $id . ($isRight ? '.arc-south-west' : '.arc-south-east'),
        'startAnchor' => 's',
        'endAnchor' => $isRight ? 'w' : 'e',
        'arcSize' => $radius,
        'anchorStart' => $bridgeEnd,
        'anchorEnd' => $end,
    ])" />
@endif
