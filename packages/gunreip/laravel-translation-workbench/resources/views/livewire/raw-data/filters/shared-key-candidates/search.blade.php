{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/shared-key-candidates/search.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>{{ __('Search') }}</flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.magnifying-glass />
        </flux:input.group.prefix>
        <flux:input
            clearable
            copyable
            wire:model.live.debounce.300ms="sharedKeyCandidatesSearch"
            placeholder="{{ __('IDs, literal, translation keys or status') }}"
        />
    </flux:input.group>
</flux:field>
