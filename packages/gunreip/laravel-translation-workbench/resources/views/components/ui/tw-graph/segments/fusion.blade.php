{{-- One horizontal lane changes level through two opposed arcs and a compensating stem. --}}
@props(['id' => 'segment.fusion', 'anchorStart', 'anchorEnd', 'direction' => 'right-left', 'arcRadius' => '1.375rem', 'minStemLength' => '1rem', 'color' => 'zinc', 'dev' => false, 'zIndex' => 20])
@php
    $number = fn ($value) => \Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry::evaluateRemExpression((string) $value);
    $x = $number($anchorStart['x']); $y = $number($anchorStart['y']);
    $endX = $number($anchorEnd['x']); $endY = $number($anchorEnd['y']);
    $radius = $number($arcRadius);
    $minimumStem = $number($minStemLength);
    if ($minimumStem === null || $minimumStem < 0) {
        throw new \InvalidArgumentException('Fusion min-stem-length must resolve to a nonnegative rem length.');
    }
    if (in_array(null, [$x, $y, $endX, $endY, $radius], true) || $radius <= 0 || !in_array($direction, ['left-right', 'right-left'], true)) {
        throw new \InvalidArgumentException('fusion requires resolvable rem coordinates, a positive radius and a horizontal direction.');
    }
    $sx = $direction === 'left-right' ? 1 : -1;
    if (($endX - $x) * $sx < 0 || ($endX === $x && $endY !== $y)) {
        throw new \InvalidArgumentException('fusion output must lie ahead of its input.');
    }
    $dy = $endY - $y;
    $r = \Gunreip\TranslationWorkbench\Support\TwGraph\FusionGeometry::radius($dy, $radius, $minimumStem, abs($endX - $x));
    $sy = $dy >= 0 ? 1 : -1;
    $point = fn ($px, $py) => ['x' => $px . 'rem', 'y' => $py . 'rem'];
    $lead = max(0, abs($endX - $x) - 2 * $r);
    $bendStart = $point($x + $sx * $lead, $y);
    $a = $point($endX - $sx * $r, $y + $sy * $r);
    $b = $point($endX - $sx * $r, $endY - $sy * $r);
    $c = $anchorEnd;
    $common = ['color' => $color, 'dev' => $dev, 'zIndex' => $zIndex, 'nodeStart' => false, 'nodeEnd' => false];
@endphp
@if ($lead > 0)
    <x-translation-workbench::ui.tw-graph.segments.path :segment="array_merge($common, [
        'id' => $id . '.bridge-in', 'anchorStart' => $anchorStart, 'anchorEnd' => $bendStart,
        'direction' => $direction, 'length' => $lead . 'rem',
        'nodeStart' => true, 'nodeStartDot' => false, 'jointArrowStart' => true,
        'jointArrowStartDirection' => $sx > 0 ? 'right' : 'left', 'devCounterStart' => false,
    ])" />
@endif
@if ($r > 0)
    <x-translation-workbench::ui.tw-graph.segments.arc :segment="array_merge($common, [
        'id' => $id . '.arc-in', 'anchorStart' => $bendStart, 'anchorEnd' => $a,
        'nodeStart' => true, 'nodeStartDot' => false, 'jointArrowStart' => true,
        'jointArrowStartDirection' => $sx > 0 ? 'right' : 'left',
        'nodeEnd' => true, 'nodeEndDot' => false, 'jointArrowEnd' => true,
        'jointArrowEndDirection' => $sy > 0 ? 'top' : 'bottom',
        'devCounterStart' => false, 'devCounterEnd' => false,
        'startAnchor' => $sy > 0 ? 's' : 'n', 'endAnchor' => $sx > 0 ? 'e' : 'w', 'arcSize' => $r . 'rem',
    ])" />
    <x-translation-workbench::ui.tw-graph.segments.path :segment="array_merge($common, [
        'id' => $id . '.stem', 'anchorStart' => $a, 'anchorEnd' => $b,
        'direction' => $sy > 0 ? 'bottom-top' : 'top-bottom', 'length' => max(0, abs($dy) - 2 * $r) . 'rem',
    ])" />
    <x-translation-workbench::ui.tw-graph.segments.arc :segment="array_merge($common, [
        'id' => $id . '.arc-out', 'anchorStart' => $b, 'anchorEnd' => $c,
        'nodeStart' => abs($dy) > 2 * $r, 'nodeStartDot' => false, 'jointArrowStart' => true,
        'jointArrowStartDirection' => $sy > 0 ? 'top' : 'bottom', 'devCounterStart' => false,
        'startAnchor' => $sx > 0 ? 'w' : 'e', 'endAnchor' => $sy > 0 ? 'n' : 's', 'arcSize' => $r . 'rem',
    ])" />
@endif
