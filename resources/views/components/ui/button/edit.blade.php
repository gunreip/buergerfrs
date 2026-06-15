{{-- resources/views/components/ui/button/edit.blade.php --}}

{{-- Component-Calls
<x-ui.button.edit />
<x-ui.button.edit icon="square-pen" />
<x-ui.button.edit :icon="false" />
<x-ui.button.edit label="{{ __('admin.translation_list.modal.edit') }}" />
--}}

{{-- edit  → sky/primary, square-pen, Edit --}}

@props([
    'label' => __('admin.translation_list.modal.edit'),
    'icon' => true,
    'type' => 'button',
    'size' => null,
])

@php
    $resolvedIcon = match (true) {
        $icon === false => null,
        is_string($icon) && trim($icon) !== '' => trim($icon),
        default => 'pen-line',
    };
@endphp

@if ($resolvedIcon !== null)
    <flux:button.group>
        <flux:button
            class="hover:cursor-pointer"
            type="{{ $type }}"
            stroke-width="1"
            icon="{{ $resolvedIcon }}"
            {{ $attributes }}
            variant="primary"
            color="amber"
            :size="$size"
        ></flux:button>
        <flux:button
            class="hover:cursor-pointer"
            type="{{ $type }}"
            {{ $attributes }}
            variant="primary"
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
        variant="primary"
        color="amber"
        :size="$size"
    >
        {{ $label }}
    </flux:button>
@endif
