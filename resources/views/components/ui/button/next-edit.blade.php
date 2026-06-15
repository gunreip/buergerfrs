{{-- resources/views/components/ui/button/next-edit.blade.php --}}

{{-- Component-Calls
<x-ui.button.next-edit />
<x-ui.button.next-edit label="Next" />
<x-ui.button.next-edit :label="false" />
--}}

{{-- next-edit → zinc/ghost, arrow-big-right, icon-only by default --}}

@props([
    'label' => false,
    'type' => 'button',
    'size' => 'sm',
    'title' => __('admin.translation_list.modal_edit.open_next_editable_entry'),
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
    {{-- :title="$title" --}}
    {{ $attributes }}
>
    <flux:icon.arrow-big-right
        class="size-5"
        stroke-width="1"
    />

    @if ($label !== false)
        <span class="ml-2">
            {{ $label }}
        </span>
    @endif
</flux:button>
