{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/event-types/search.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>{{ __('Search') }}</flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.magnifying-glass />
        </flux:input.group.prefix>
        <flux:input
            clearable
            copyable
            wire:model.live.debounce.300ms="eventTypesSearch"
            placeholder="{{ __('Search key, label, category or severity') }}"
        />
    </flux:input.group>
</flux:field>
