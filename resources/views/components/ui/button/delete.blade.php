{{-- resources/views/components/ui/button/delete.blade.php --}}

{{-- Component-Calls
<x-ui.button.delete />
<x-ui.button.delete icon="trash" />
<x-ui.button.delete :icon="false" />
<x-ui.button.delete label="{{ __('ui.button.delete') }}" />
--}}

{{-- delete  → red/danger, trash, Delete --}}

@props([
    'label' => __('ui.button.delete'),
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
            variant="danger"
            color="red"
            :size="$size"
        ></flux:button>
        <flux:button
            class="hover:cursor-pointer"
            type="{{ $type }}"
            {{ $attributes }}
            variant="danger"
            color="red"
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
        variant="danger"
        color="red"
        :size="$size"
    >
        {{ $label }}
    </flux:button>
@endif
