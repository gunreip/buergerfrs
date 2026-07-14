{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/source-files/search.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>{{ __('Search') }}</flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.magnifying-glass />
        </flux:input.group.prefix>
        <flux:input
            clearable
            copyable
            wire:model.live.debounce.300ms="sourceFilesSearch"
            placeholder="{{ __('Search ID, path, source type, extension or status') }}"
        />
    </flux:input.group>
</flux:field>
