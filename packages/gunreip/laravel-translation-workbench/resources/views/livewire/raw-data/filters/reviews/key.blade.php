{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/reviews/key.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>{{ __('Key ID') }}</flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.key-round />
        </flux:input.group.prefix>
        <flux:input
            clearable
            copyable
            inputmode="numeric"
            wire:model.live.debounce.300ms="reviewsKeyId"
            placeholder="{{ __('Key ID') }}"
        />
    </flux:input.group>
</flux:field>
