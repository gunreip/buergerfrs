{{-- packages/gunreip/laravel-translation-workbench/resources/views/components/ui/tw-graph/strang/branch-end.blade.php --}}
{{--
    Strang: branch-end

    Usage:
    <x-translation-workbench::ui.tw-graph.strang.branch-end
        side="left"
        attach-to="strang.branch-left.end"
        length="2rem"
    />

    Component chain:
    tw-graph -> strang.branch-end -> segments.end -> primitives.*

    Rule:
    Use this as a terminal cap for an already rendered branch-left/right strang.
    It attaches to a registered branch end anchor and does not redraw the branch
    end node itself.
--}}

@aware([
    'graphId' => null,
    'dev' => false,
    'defaultColor' => null,
    'lineLength' => null,
    'capLength' => null,
])

@props([
    'id' => null,
    'componentCounter' => 1,
    'side' => 'left',
    'attachTo' => null,
    'anchorStart' => ['x' => '0rem', 'y' => '0rem'],
    'length' => null,
    'capLength' => null,
    'color' => null,
    'endLabel' => null,
    'counterStart' => null,
    'zIndex' => 10,
    'devMode' => null,
])

@php
    $resolvedGraphId = filled($graphId ?? null) ? (string) $graphId : 'tw-graph';
    $resolvedComponentCounter = max(1, (int) $componentCounter);
    $resolvedSide = $side === 'right' ? 'right' : 'left';
    $id = filled($id)
        ? (string) $id
        : $resolvedGraphId . '.strang.branch-end.' . $resolvedSide . '.' . $resolvedComponentCounter;
    $resolvedColor = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($color, $defaultColor ?? null, 'red');
    $resolvedDev = $devMode ?? $dev;
    $resolvedLineLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::localOrGraphString($lineLength ?? null, 'line_length', '4rem');
    $resolvedLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($length, $resolvedLineLength, '4rem');
    $resolvedCapLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::graphStringFor($capLength, null, 'cap_length', '1.75rem');
    $add = fn (string $value, string $delta): string => $delta === '0rem' ? $value : 'calc(' . $value . ' + ' . $delta . ')';
    $attachTarget = filled($attachTo)
        ? \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get($resolvedGraphId, (string) $attachTo)
        : null;
    $missingAttachTarget = filled($attachTo) && $attachTarget === null;
    $anchor = $attachTarget ?: [
        'x' => data_get($anchorStart, 'x', '0rem'),
        'y' => data_get($anchorStart, 'y', '0rem'),
    ];
    $endAnchor = [
        'x' => data_get($anchor, 'x', '0rem'),
        'y' => $add(data_get($anchor, 'y', '0rem'), $resolvedLength),
    ];
    $counter = $counterStart ?? 'E';
    $endLabelConfig = is_array($endLabel)
        ? $endLabel
        : (filled($endLabel) ? ['text' => $endLabel] : null);
    $segment = [
        'id' => $id . '.path.branch-end',
        'direction' => 'bottom-top',
        'length' => $resolvedLength,
        'anchorStart' => $anchor,
        'anchorEnd' => $endAnchor,
        'nodeStart' => false,
        'nodeEnd' => false,
        'cap' => true,
        'capLength' => $resolvedCapLength,
        'devCounterEnd' => $counter,
        'devCounterColor' => $resolvedColor,
        'color' => $resolvedColor,
        'zIndex' => $zIndex,
        'dev' => $resolvedDev,
        'endLabel' => $endLabelConfig,
    ];
    $bounds = \Gunreip\TranslationWorkbench\Support\TwGraphProtocol\GeometryBounds::fromPoints([
        $anchor,
        $endAnchor,
    ], '1rem');

    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.branch-end.' . $resolvedSide . '.start', $anchor);
    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.branch-end.' . $resolvedSide . '.end', $endAnchor);
@endphp

@if ($resolvedDev && $missingAttachTarget)
    <span
        class="tw-graph-protocol-dev-only absolute z-50"
        style="
            left: calc(var(--tw-graph-protocol-trunk-x) + {{ data_get($anchorStart, 'x', '0rem') }});
            bottom: calc(var(--tw-graph-protocol-origin-bottom) + {{ data_get($anchorStart, 'y', '0rem') }});
        "
        title="{{ \Gunreip\TranslationWorkbench\Support\TwGraph\DevIdentifier::label($id) }} | missing attach-to: {{ \Gunreip\TranslationWorkbench\Support\TwGraph\ElementIdentifier::normalize($attachTo) }}"
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
    :metrics-side="$resolvedSide"
/>

<x-translation-workbench::ui.tw-graph.segments.end
    :segment="$segment"
    :dev="$resolvedDev"
/>
