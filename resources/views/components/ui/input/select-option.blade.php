{{-- resources/views/components/ui/input/select-option.blade.php --}}

{{-- Component-Calls
<x-ui.input.select-option value="all" icon="asterisk" icon-class="text-sky-400">
    {{ __('ui.states.all') }}
</x-ui.input.select-option>

<x-ui.input.select-option value="yes" icon="check">
    {{ __('ui.filters.yes') }}
</x-ui.input.select-option>
--}}

@props([
    'value',
    'icon' => null,
    'iconClass' => 'text-zinc-400',
    'iconVariant' => 'outline',
    'iconFallback' => 'file-x',
    'textClass' => null,
])

<flux:select.option value="{{ $value }}" :icon="null">
    @if (filled($icon))
        <x-slot:icon>
            <x-ui.flux-icon
                class="{{ $iconClass }} me-2 shrink-0"
                :name="$icon"
                :variant="$iconVariant"
                :fallback="$iconFallback"
            />
        </x-slot:icon>
    @endif

    <div {{ $attributes->class('flex min-w-0 items-center gap-2') }}>
        <span @class(['truncate', $textClass => filled($textClass)])>
            {{ $slot }}
        </span>
    </div>
</flux:select.option>
