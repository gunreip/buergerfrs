{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/dynamic-source-values/translation-key.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>
        {{ __('Translation key') }}
    </flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.key />
        </flux:input.group.prefix>
        <flux:input
            clearable
            copyable
            wire:model.live.debounce.300ms="dynamicSourceValuesTranslationKey"
            placeholder="{{ __('Contains translation key') }}"
        />
    </flux:input.group>
</flux:field>
