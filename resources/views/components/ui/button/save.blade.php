{{-- resources/views/components/ui/button/save.blade.php --}}

{{-- Component-Calls
<x-ui.button.save />
<x-ui.button.save icon="check" />
<x-ui.button.save :icon="false" />
<x-ui.button.save label="{{ __('ui.button.save.save_role') }}" />
--}}

{{-- save    → green/primary, check, Save --}}

@props([
    'label' => __('ui.button.save.save'),
    'icon' => true,
    'type' => 'button',
    'size' => null,
])

@php
    $resolvedIcon = match (true) {
        $icon === false => null,
        is_string($icon) && trim($icon) !== '' => trim($icon),
        default => 'save',
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
