{{-- resources/views/components/ui/tw-graph-v2/merge/index.blade.php --}}
{{--
    Usage:
    <x-ui.tw-graph-v2.merge side="left" />
    <x-ui.tw-graph-v2.merge side="right" color="sky" />

    Optional:
    side="left|right" Required for clarity in DEV previews.
    anchor-index="1..8" 1-based visible trunk node counter from bottom to top; no index 0, no trunk border anchor.
    connector-horizontal-length="4rem" Horizontal connector length between point 2 and point 3.
    connector-vertical-length="4rem" Optional vertical connector length below the outer arc.
    color="zinc|red|orange|amber|yellow|lime|green|emerald|teal|cyan|sky|blue|indigo|violet|purple|fuchsia|pink|rose"

    Composition:
    This DEV aggregate mirrors <x-ui.tw-graph-v2.trunk />: it keeps the
    graph-preview callsite compact while the still-static merge trunk and
    extension geometry remains easy to adjust in one focused component.
--}}

@aware([
    'dev' => false,
])

@props([
    'side' => 'left',
    'anchorIndex' => '1',
    'connectorHorizontalLength' => null,
    'connectorVerticalLength' => null,
    'color' => null,
    'dev' => false,
    'devPath' => 'tw-graph-v2.merge',
])

@php
    $isLeft = $side === 'left';
    $resolvedColor = $color ?: ($isLeft ? 'amber' : 'sky');
    $resolvedConnectorHorizontalLength = filled($connectorHorizontalLength) ? $connectorHorizontalLength : '4rem';

    $mergeTrunk = $isLeft
        ? [
            'connectorHorizontalLength' => $resolvedConnectorHorizontalLength,
            'connectorVerticalLength' => filled($connectorVerticalLength) ? $connectorVerticalLength : '6rem',
            'startLabel' => ['Root #701', 'origin'],
            'startNodeLeftText' => 'last seen',
            'startNodeRightText' => 'first seen',
        ]
        : [
            'connectorHorizontalLength' => $resolvedConnectorHorizontalLength,
            'connectorVerticalLength' => filled($connectorVerticalLength) ? $connectorVerticalLength : '2.5rem',
            'startLabel' => ['Root #702', 'origin'],
            'startNodeLeftText' => null,
            'startNodeRightText' => 'first seen',
        ];

    $mergeExtensions = $isLeft
        ? [
            [
                'parentOffset' => '4.3rem',
                'connectorHorizontalLength' => '12rem',
                'connectorVerticalLength' => '3rem',
                'startLabel' => ['Root #703', 'origin'],
                'startNodeLeftText' => 'first seen',
                'startNodeRightText' => null,
            ],
            // [
            //     'parentOffset' => '16.3rem',
            //     'connectorHorizontalLength' => '12rem',
            //     'connectorVerticalLength' => '2rem',
            //     'startLabel' => ['Root #705', 'origin'],
            //     'startNodeLeftText' => 'first seen',
            //     'startNodeRightText' => null,
            // ],
            // [
            //     'parentOffset' => '28.3rem',
            //     'connectorHorizontalLength' => '11rem',
            //     'connectorVerticalLength' => '5.5rem',
            //     'startLabel' => ['Root #707', 'origin'],
            //     'startNodeLeftText' => null,
            //     'startNodeRightText' => 'first seen',
            // ],
            // [
            //     'parentOffset' => '39.3rem',
            //     'connectorHorizontalLength' => '9rem',
            //     'connectorVerticalLength' => '1.5rem',
            //     'startLabel' => ['Root #709', 'origin'],
            //     'startNodeLeftText' => null,
            //     'startNodeRightText' => 'first seen',
            // ],
        ]
        : [
            // [
            //     'parentOffset' => '4.3rem',
            //     'connectorHorizontalLength' => '10rem',
            //     'connectorVerticalLength' => '3.5rem',
            //     'startLabel' => ['Root #704', 'origin'],
            //     'startNodeLeftText' => null,
            //     'startNodeRightText' => 'first seen',
            // ],
            // [
            //     'parentOffset' => '14.3rem',
            //     'connectorHorizontalLength' => '10rem',
            //     'connectorVerticalLength' => '1rem',
            //     'startLabel' => ['Root #706', 'origin'],
            //     'startNodeLeftText' => null,
            //     'startNodeRightText' => 'first seen',
            // ],
            // [
            //     'parentOffset' => '24.3rem',
            //     'connectorHorizontalLength' => '12rem',
            //     'connectorVerticalLength' => '3rem',
            //     'startLabel' => ['Root #708', 'origin'],
            //     'startNodeLeftText' => null,
            //     'startNodeRightText' => 'first seen',
            // ],
            // [
            //     'parentOffset' => '36.3rem',
            //     'connectorHorizontalLength' => '12rem',
            //     'connectorVerticalLength' => '1.5rem',
            //     'startLabel' => ['Root #710', 'origin'],
            //     'startNodeLeftText' => null,
            //     'startNodeRightText' => 'first seen',
            // ],
        ];
@endphp

<x-ui.tw-graph-v2.merge.trunk
    :side="$side"
    :anchor-index="$anchorIndex"
    :connector-horizontal-length="$mergeTrunk['connectorHorizontalLength']"
    :connector-vertical-length="$mergeTrunk['connectorVerticalLength']"
    :start-label="$mergeTrunk['startLabel']"
    :start-node-left-text="$mergeTrunk['startNodeLeftText']"
    :start-node-right-text="$mergeTrunk['startNodeRightText']"
    :color="$resolvedColor"
    :dev="$dev"
    dev-path="{{ $devPath }}.trunk"
/>

@foreach ($mergeExtensions as $mergeExtension)
    <x-ui.tw-graph-v2.merge.extension
        :side="$side"
        :anchor-index="$anchorIndex"
        :parent-offset="$mergeExtension['parentOffset']"
        :connector-horizontal-length="$mergeExtension['connectorHorizontalLength']"
        :connector-vertical-length="$mergeExtension['connectorVerticalLength']"
        :start-label="$mergeExtension['startLabel']"
        :start-node-left-text="$mergeExtension['startNodeLeftText']"
        :start-node-right-text="$mergeExtension['startNodeRightText']"
        :color="$resolvedColor"
        :dev="$dev"
        dev-path="{{ $devPath }}.extension-{{ $loop->iteration }}"
    />
@endforeach
