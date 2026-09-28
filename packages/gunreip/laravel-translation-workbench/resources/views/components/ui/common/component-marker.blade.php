{{-- UI diagnostics only: never participates in graph geometry or source examples. --}}
@props(['name'])

<span
    style="display: none;"
    data-ui-component-marker="{{ $name }}"
    class="absolute left-0 top-0 z-50 text-red-500"
>
    <flux:tooltip :content="$name">
        <span tabindex="0" aria-label="{{ $name }}" class="inline-flex">
            <flux:icon.component class="size-3" />
        </span>
    </flux:tooltip>
</span>
