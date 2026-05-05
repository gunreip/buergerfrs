{{-- resources/views/components/ui/button/confirm.blade.php --}}

{{-- Component-Calls
<x-ui.button.confirm />
<x-ui.button.confirm icon="shield-check" />
<x-ui.button.confirm :icon="false" />
<x-ui.button.confirm label="{{ __('Confirm') }}" />
--}}

{{-- confirm  → green/primary, shield-check, Confirm --}}

@props([
    'label' => __('Confirm'),
    'icon' => true,
    'type' => 'button',
    'size' => null,
])

@php
    $resolvedIcon = match (true) {
        $icon === false => null,
        is_string($icon) && trim($icon) !== '' => trim($icon),
        default => 'shield-check',
    };
@endphp

@if ($resolvedIcon !== null)
    <flux:button.group>
        <flux:button
            class="hover:cursor-pointer"
            type="{{ $type }}"
            icon="{{ $resolvedIcon }}"
            {{ $attributes }}
            variant="primary"
            color="green"
            :size="$size"
        ></flux:button>
        <flux:button
            class="hover:cursor-pointer"
            type="{{ $type }}"
            {{ $attributes }}
            variant="primary"
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
        variant="primary"
        color="green"
        :size="$size"
    >
        {{ $label }}
    </flux:button>
@endif
