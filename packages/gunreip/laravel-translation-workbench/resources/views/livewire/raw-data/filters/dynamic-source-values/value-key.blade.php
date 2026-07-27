{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/dynamic-source-values/value-key.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>
        {{ __('Value key') }}
    </flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.key />
        </flux:input.group.prefix>
        <flux:input
            clearable
            copyable
            wire:model.live.debounce.300ms="dynamicSourceValuesValueKey"
            placeholder="{{ __('Contains value key') }}"
        />
    </flux:input.group>
</flux:field>
