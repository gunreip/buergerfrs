{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/key-findings/search.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>{{ __('Search') }}</flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.magnifying-glass />
        </flux:input.group.prefix>
        <flux:input
            clearable
            copyable
            wire:model.live.debounce.300ms="keyFindingsSearch"
            placeholder="{{ __('Relation ID, key ID, finding ID, relation type or status') }}"
        />
    </flux:input.group>
</flux:field>
