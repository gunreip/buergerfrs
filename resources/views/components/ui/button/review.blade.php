{{-- resources/views/components/ui/button/review.blade.php --}}

{{-- Component-Calls
<x-ui.button.review />
<x-ui.button.review icon="shield-check" />
<x-ui.button.review :icon="false" />
<x-ui.button.review label="{{ __('ui.button.review.review') }}" />
--}}

{{-- review  → fuchsia/primary, scan-search, Review --}}

@props([
    'label' => __('ui.button.review.review'),
    'icon' => true,
    'type' => 'button',
    'size' => null,
])

@php
    $resolvedIcon = match (true) {
        $icon === false => null,
        is_string($icon) && trim($icon) !== '' => trim($icon),
        default => 'scan-search',
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
            color="fuchsia"
            :size="$size"
        ></flux:button>
        <flux:button
            class="hover:cursor-pointer"
            type="{{ $type }}"
            {{ $attributes }}
            variant="primary"
            color="fuchsia"
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
        color="fuchsia"
        :size="$size"
    >
        {{ $label }}
    </flux:button>
@endif
