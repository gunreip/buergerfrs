{{-- resources/views/components/ui/tw-graph-v2/path-segment.blade.php --}}
{{--
    Compatibility wrapper:
    Prefer <x-ui.tw-graph-v2.elements.path ... /> in new v2 components.

    Usage:
    <x-ui.tw-graph-v2.path-segment />
    <x-ui.tw-graph-v2.path-segment length="5rem" />
    <x-ui.tw-graph-v2.path-segment direction="horizontal" length="8rem" />
    <x-ui.tw-graph-v2.path-segment variant="start" length="5rem" />

    Optional:
    direction="vertical|horizontal" Default: vertical.
    variant="solid|start" Default: solid. start renders the bottom-to-top fade-in.
    length="4rem" Segment length in the selected direction.
    color="zinc|red|orange|amber|yellow|lime|green|emerald|teal|cyan|sky|blue|indigo|violet|purple|fuchsia|pink|rose"
--}}

@aware([
    'dev' => false,
])

@props([
    'direction' => 'vertical',
    'variant' => 'solid',
    'length' => '4rem',
    'color' => null,
    'dev' => false,
    'devPath' => null,
])

<x-ui.tw-graph-v2.elements.path
    :direction="$direction"
    :variant="$variant"
    :length="$length"
    :color="$color"
    :dev="$dev"
    :dev-path="$devPath"
    {{ $attributes }}
/>
