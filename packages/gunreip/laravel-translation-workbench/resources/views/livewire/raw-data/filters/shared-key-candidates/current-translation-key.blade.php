{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/shared-key-candidates/current-translation-key.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>{{ __('Current translation key') }}</flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.key-round />
        </flux:input.group.prefix>
        <flux:input
            clearable
            copyable
            wire:model.live.debounce.300ms="sharedKeyCandidatesCurrentTranslationKey"
            placeholder="{{ __('Contains current key') }}"
        />
    </flux:input.group>
</flux:field>
