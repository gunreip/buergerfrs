{{-- resources/views/components/ui/button/clear.blade.php --}}

{{-- Component-Calls
<x-ui.button.clear />
<x-ui.button.clear icon="trash" />
<x-ui.button.clear icon="x-mark" />
<x-ui.button.clear :icon="false" />
<x-ui.button.clear label="{{ __('ui.button.clear.clear') }}" />
--}}

{{-- clear   → amber/filled, trash, Clear --}}

@props([
    'label' => __('ui.button.clear.clear'),
    'icon' => true,
    'type' => 'button',
    'size' => null,
])

@php
    $resolvedIcon = match (true) {
        $icon === false => null,
        is_string($icon) && trim($icon) !== '' => trim($icon),
        default => 'trash',
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
            color="amber"
            :size="$size"
        ></flux:button>

        <flux:button
            class="hover:cursor-pointer"
            type="{{ $type }}"
            {{ $attributes }}
            variant="filled"
            color="amber"
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
        color="amber"
        :size="$size"
    >
        {{ $label }}
    </flux:button>
@endif
