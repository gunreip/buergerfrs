{{-- resources/views/components/ui/checkbox/icon.blade.php --}}

@props([
    'id',
    'icon',
    'label',
    'title' => null,
    'text' => null,
    'iconClass' => 'size-5',
])

<x-ui.tooltip.trigger
    :title="$title ?? $label"
    :text="$text ?? $label"
>
    <div class="flex items-center gap-1.5">
        <flux:checkbox
            id="{{ $id }}"
            aria-label="{{ $label }}"
            {{ $attributes }}
        />
        <label
            class="cursor-pointer text-zinc-500 dark:text-zinc-400"
            for="{{ $id }}"
        >
            <x-ui.flux-icon
                :name="$icon"
                class="{{ $iconClass }}"
            />
        </label>
    </div>
</x-ui.tooltip.trigger>
