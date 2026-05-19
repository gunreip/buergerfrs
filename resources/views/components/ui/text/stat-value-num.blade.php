{{-- resources/views/components/ui/text/stat-value-num.blade.php --}}

@props([
    'label',
    'value' => null,
    'color' => 'zinc',
    'variant' => 'subtle',
    'labelClass' => 'truncate',
    'valueClass' => 'tabular-nums',
    'emptyValueLabel' => 'n. v.',
    'emptyColor' => 'red',
])

@php
    $hasValue = $value !== null && $value !== '';
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
        {{ $displayValue }}
    </flux:badge>
</div>
