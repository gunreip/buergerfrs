{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/dynamic-sources/search.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>{{ __('Search') }}</flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.search />
        </flux:input.group.prefix>
        <flux:input
            clearable
            copyable
            wire:model.live.debounce.300ms="dynamicSourcesSearch"
            placeholder="{{ __('Path, expression, scope, classification or status') }}"
        />
    </flux:input.group>
</flux:field>
