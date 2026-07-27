{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/reviews/finding.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>{{ __('Finding ID') }}</flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.hash />
        </flux:input.group.prefix>
        <flux:input
            clearable
            copyable
            inputmode="numeric"
            wire:model.live.debounce.300ms="reviewsFindingId"
            placeholder="{{ __('Finding ID') }}"
        />
    </flux:input.group>
</flux:field>
