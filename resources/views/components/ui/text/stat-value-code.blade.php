@props([
    'label',
    'value' => null,
    'color' => 'zinc',
    'variant' => 'subtle',
    'labelClass' => 'truncate',
    'valueClass' => '',
    'emptyValueLabel' => 'n. v.',
    'emptyColor' => 'red',
])

@php
    $hasValue = filled($value);
    $displayValue = $hasValue ? $value : $emptyValueLabel;
    $displayColor = $hasValue ? $color : $emptyColor;
@endphp

<div {{ $attributes->class('flex items-center justify-between gap-3') }}>
    <code class="{{ $labelClass }}">
        {{ $label }}
    </code>

    <flux:badge
        class="{{ $valueClass }}"
        variant="{{ $variant }}"
        color="{{ $displayColor }}"
    >
        <code>{{ $displayValue }}</code>
    </flux:badge>
</div>
