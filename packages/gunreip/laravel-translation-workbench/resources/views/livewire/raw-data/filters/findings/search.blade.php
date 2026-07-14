{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/findings/search.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>{{ __('Search') }}</flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.magnifying-glass />
        </flux:input.group.prefix>
        <flux:input
            clearable
            copyable
            wire:model.live.debounce.300ms="findingsSearch"
            placeholder="{{ __('ID, source file ID, literal, key, expression or fingerprint') }}"
        />
    </flux:input.group>
</flux:field>
