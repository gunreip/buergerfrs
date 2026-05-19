{{-- resources/views/components/ui/text/stat-value.blade.php --}}
{{-- resources/views/components/ui/text/stat-value.blade.php --}}

@props([
    'label',
    'value' => null,
    'separator' => ':',
    'labelClass' => 'font-semibold',
    'valueClass' => 'tabular-nums',
])

<span {{ $attributes->class('inline-flex items-baseline gap-1') }}>
    <span class="{{ $labelClass }}">
        {{ $label }}{{ $separator }}
    </span>

    <span class="{{ $valueClass }}">
        {{ $value }}
    </span>
</span>
