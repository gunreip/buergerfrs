{{-- packages/gunreip/laravel-translation-workbench/resources/views/components/ui/tw-graph/segments/label.blade.php --}}
{{--
    Segment: label

    Usage:
    <x-translation-workbench::ui.tw-graph.segments.label
        :label="$label"
        anchor-x="0rem"
        anchor-y="0rem"
        side="right"
    />

    Segment role:
    Label composes one connector primitive and one text primitive. It must only
    be used when the owning segment has a visible anchor/node for this label.
--}}

@props([
    'label' => [],
    'id' => 'segment.label',
    'anchorX' => '0rem',
    'anchorY' => '0rem',
    'side' => 'right',
    'color' => 'cyan',
])

@aware([
    'connectorLength' => null,
    'connectorGap' => null,
])

@php
    $connectorLength = data_get(
        $label,
        'connectorLength',
        \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::graphStringFor(
            $connectorLength ?? null,
            null,
            'connector_length',
            '2rem',
        ),
    );
    $connectorGap = data_get(
        $label,
        'connectorGap',
        \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::graphStringFor(
            $connectorGap ?? null,
            null,
            'connector_gap',
            '0.25rem',
        ),
    );
    $labelOffset = 'calc(var(--tw-graph-protocol-node-half) + ' . $connectorLength . ' + ' . $connectorGap . ')';
    $labelColor = data_get($label, 'color', $color);
    $badgeColor = data_get($label, 'badgeColor', $labelColor);
@endphp

<x-translation-workbench::ui.tw-graph.primitives.connector
    :id="$id . '.connector'"
    :placement="$side"
    :anchor-x="$anchorX"
    :anchor-y="$anchorY"
    :length="$connectorLength"
    :gap="$connectorGap"
    :color="$labelColor"
/>

<x-translation-workbench::ui.tw-graph.primitives.text
    :id="$id . '.text'"
    :text="data_get($label, 'text')"
    :side="$side"
    :anchor-x="$anchorX"
    :anchor-y="$anchorY"
    :offset="$labelOffset"
    :badge="data_get($label, 'badge', true)"
    :badge-color="$badgeColor"
    :long="data_get($label, 'long', false) || data_get($label, 'width') === 'long'"
    :half-long="data_get($label, 'halfLong', false) || in_array(data_get($label, 'width'), ['halfLong', 'half-long', 'half_long'], true)"
    :half="data_get($label, 'half', false) || in_array(data_get($label, 'width'), ['half', 'halfWidth', 'half-width', 'half_width'], true)"
    :align="data_get($label, 'align', 'center')"
    :justify="data_get($label, 'justify', false)"
    :max-lines="data_get($label, 'maxLines', 3)"
/>
