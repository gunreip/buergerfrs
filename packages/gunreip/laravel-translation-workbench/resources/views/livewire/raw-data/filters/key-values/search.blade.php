{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/key-values/search.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>{{ __('Search') }}</flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.search />
        </flux:input.group.prefix>
        <flux:input
            clearable
            copyable
            wire:model.live.debounce.300ms="keyValuesSearch"
            placeholder="{{ __('Locale, value, status or source') }}"
        />
    </flux:input.group>
</flux:field>
