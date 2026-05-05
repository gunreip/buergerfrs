{{-- resources/views/components/ui/button/create.blade.php --}}

{{-- Component-Calls
<x-ui.button.create />
<x-ui.button.create icon="plus" />
<x-ui.button.create :icon="false" />
<x-ui.button.create label="{{ __('Create') }}" />
--}}

{{-- create  → green/primary, plus, Create --}}

@props([
    'label' => __('Create'),
    'icon' => true,
    'type' => 'button',
    'size' => null,
])

@php
    $resolvedIcon = match (true) {
        $icon === false => null,
        is_string($icon) && trim($icon) !== '' => trim($icon),
        default => 'plus',
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
    <flux:button.group>
        <flux:button
            class="hover:cursor-pointer"
            type="{{ $type }}"
            icon="{{ $resolvedIcon }}"
            {{ $attributes }}
            variant="primary"
            color="green"
            :size="$size"
        >
            {{ $label }}
        </flux:button>
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
@endif
