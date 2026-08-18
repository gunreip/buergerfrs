{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/shared-key-candidates/literal.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>{{ __('Literal') }}</flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.scroll-text />
        </flux:input.group.prefix>
        <flux:input
            clearable
            copyable
            wire:model.live.debounce.300ms="sharedKeyCandidatesNormalizedLiteral"
            placeholder="{{ __('Contains literal') }}"
        />
    </flux:input.group>
</flux:field>
