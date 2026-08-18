{{-- resources/views/components/ui/tw-graph-protocol/primitives/text-end.blade.php --}}
{{--
    Primitive text-end element.

    Usage:
    <x-ui.tw-graph-protocol.primitives.text-end text="Shared key" />
    <x-ui.tw-graph-protocol.primitives.text-end text="Shared key" connector-placement="top" connector-length="0.75rem" />

    Rule:
    text-end labels the open end side of a path. connectorPlacement describes
    where the connector leaves the anchor; the badge follows it. Default is top
    for bottom-top graph paths.
--}}

@props([
    'id' => 'text-end',
    'text' => null,
    'connectorPlacement' => null,
    'placement' => null,
    'anchorX' => '0rem',
    'anchorY' => '0rem',
    'connectorLength' => '0.75rem',
    'connectorGap' => null,
    'color' => 'green',
    'badgeColor' => null,
])

<x-ui.tw-graph-protocol.primitives.text-start
    :id="$id"
    :text="$text"
    :connector-placement="$connectorPlacement ?: ($placement ?: 'top')"
    :anchor-x="$anchorX"
    :anchor-y="$anchorY"
    :connector-length="$connectorLength"
    :connector-gap="$connectorGap"
    :color="$color"
    :badge-color="$badgeColor"
    {{ $attributes->class('tw-graph-protocol-primitive-text-end') }}
/>
