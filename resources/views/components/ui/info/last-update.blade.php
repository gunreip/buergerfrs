{{-- resources/views/components/ui/info/last-update.blade.php --}}

@props([
    'value' => null,
    'label' => __('Last update'),
])

@php
    $lastUpdateText = null;

    if ($value) {
        try {
            $lastUpdateText = \Carbon\Carbon::parse($value)->diffForHumans();
        } catch (\Throwable) {
            $lastUpdateText = null;
        }
    }
@endphp

@if ($lastUpdateText)
    <span {{ $attributes->class('text-xs text-zinc-400 dark:text-zinc-500') }}>
        {{ $label }}: {{ $lastUpdateText }}
    </span>
@endif
