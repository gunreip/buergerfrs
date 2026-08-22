{{-- packages/gunreip/laravel-translation-workbench/resources/views/components/ui/tw-graph/primitives/dev-node-counter.blade.php --}}
{{--
    Primitive: dev-node-counter

    Usage:
    <x-translation-workbench::ui.tw-graph.primitives.dev-node-counter
        :dev="true"
        :segment="$segment"
        counter="1"
    />

    Rule:
    DEV-only marker for a segment node/anchor. The owning segment decides the
    placement so counters can be offset away from neighboring path segments.
    This primitive does not infer placement from the line direction. Prefer
    offsetX/offsetY when a segment can provide exact anchor-relative offsets.
--}}

@props([
    'id' => 'dev-node-counter',
    'counter' => null,
    'segment' => [],
    'anchorX' => null,
    'anchorY' => null,
    'offsetX' => null,
    'offsetY' => null,
    'direction' => null,
    'side' => 'right',
    'placement' => null,
    'dev' => false,
    'color' => 'zinc',
])

@php
    $resolvedAnchorX = $anchorX ?? data_get($segment, 'anchorStart.x', '0rem');
    $resolvedAnchorY = $anchorY ?? data_get($segment, 'anchorStart.y', '0rem');
    $resolvedOffsetX = $offsetX ?? data_get($segment, 'devCounterOffset.x');
    $resolvedOffsetY = $offsetY ?? data_get($segment, 'devCounterOffset.y');
    $resolvedId = filled($id) ? $id : data_get($segment, 'id', 'dev-node-counter');
    $devIdentifier = \Gunreip\TranslationWorkbench\Support\TwGraph\DevIdentifier::label($resolvedId);
    $resolvedPlacement = $placement ?: $side;
    $hasOffset = filled($resolvedOffsetX) || filled($resolvedOffsetY);
@endphp

@if ($dev && filled($counter))
    <span
        {{ $attributes->class([
            'tw-graph-protocol-primitive',
            'tw-graph-protocol-primitive-dev-node-counter',
            'tw-graph-protocol-primitive-dev-node-counter-offset' => $hasOffset,
            'tw-graph-protocol-primitive-dev-node-counter-' . $resolvedPlacement => ! $hasOffset,
        ])->style([
            '--tw-graph-protocol-anchor-x: ' . $resolvedAnchorX,
            '--tw-graph-protocol-anchor-y: ' . $resolvedAnchorY,
            '--tw-graph-protocol-dev-node-counter-offset-x: ' . ($resolvedOffsetX ?: '0rem'),
            '--tw-graph-protocol-dev-node-counter-offset-y: ' . ($resolvedOffsetY ?: '0rem'),
        ]) }}
        title="{{ $devIdentifier }}"
        data-tw-graph-path="{{ $devIdentifier }}"
        x-on:click.stop="navigator.clipboard?.writeText($el.dataset.twGraphPath)"
    >
        <flux:badge
            size="sm"
            color="{{ $color }}"
        >
            {{ $counter }}
        </flux:badge>
    </span>
@endif
