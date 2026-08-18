{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/shared-key-candidates/suggested-shared-translation-key.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>{{ __('Suggested shared key') }}</flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.combine />
        </flux:input.group.prefix>
        <flux:input
            clearable
            copyable
            wire:model.live.debounce.300ms="sharedKeyCandidatesSuggestedSharedTranslationKey"
            placeholder="{{ __('Contains suggested shared key') }}"
        />
    </flux:input.group>
</flux:field>
