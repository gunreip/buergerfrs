{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/key-inventory/translation-key.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>{{ __('ui.translation.translation-key') }}</flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.key-round />
        </flux:input.group.prefix>
        <flux:input
            clearable
            copyable
            wire:model.live.debounce.300ms="keyInventoryTranslationKey"
            placeholder="{{ __('Contains translation key') }}"
        />
    </flux:input.group>
</flux:field>
