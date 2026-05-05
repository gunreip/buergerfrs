{{-- resources/views/components/ui/button/reset.blade.php --}}

{{-- Component-Calls
<x-ui.button.reset />
<x-ui.button.reset icon="rotate-ccw" />
<x-ui.button.reset :icon="false" />
<x-ui.button.reset label="{{ __('Reset') }}" />
--}}

{{-- reset  → green/filled, rotate-ccw, Reset --}}

@props([
    'label' => __('Reset'),
    'icon' => true,
    'type' => 'button',
    'size' => null,
])

@php
    $resolvedIcon = match (true) {
        $icon === false => null,
        is_string($icon) && trim($icon) !== '' => trim($icon),
        default => 'rotate-ccw',
    };
@endphp

@if ($resolvedIcon !== null)
    <flux:button.group>
        <flux:button
            class="hover:cursor-pointer"
            type="{{ $type }}"
            icon="{{ $resolvedIcon }}"
            {{ $attributes }}
            variant="filled"
            color="green"
            :size="$size"
        ></flux:button>
        <flux:button
            class="hover:cursor-pointer"
            type="{{ $type }}"
            {{ $attributes }}
            variant="filled"
            color="green"
            :size="$size"
        >
            {{ $label }}
        </flux:button>
    </flux:button.group>
@else
    <flux:button
        class="hover:cursor-pointer"
        type="{{ $type }}"
        {{ $attributes }}
        variant="filled"
        color="green"
        :size="$size"
    >
        {{ $label }}
    </flux:button>
@endif
