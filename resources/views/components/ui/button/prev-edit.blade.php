{{-- resources/views/components/ui/button/prev-edit.blade.php --}}

{{-- Component-Calls
<x-ui.button.prev-edit />
<x-ui.button.prev-edit label="Previous" />
<x-ui.button.prev-edit :label="false" />
--}}

{{-- prev-edit → zinc/ghost, arrow-big-left, icon-only by default --}}

@props([
    'label' => false,
    'type' => 'button',
    'size' => 'sm',
    'title' => __('ui.button.open.open_previous_editable_entry'),
    'ariaLabel' => null,
])

@php
    $resolvedAriaLabel = $ariaLabel ?: $title;
@endphp

<flux:button
    class="{{ $label === false ? 'w-8 p-0' : '' }} h-8 shrink-0 hover:cursor-pointer"
    type="{{ $type }}"
    aria-label="{{ $resolvedAriaLabel }}"
    variant="ghost"
    :size="$size"
    :title="$title"
    {{ $attributes }}
>
    <flux:icon.arrow-big-left
        class="size-5"
        stroke-width="1"
    />

    @if ($label !== false)
        <span class="ml-2">
            {{ $label }}
        </span>
    @endif
</flux:button>
