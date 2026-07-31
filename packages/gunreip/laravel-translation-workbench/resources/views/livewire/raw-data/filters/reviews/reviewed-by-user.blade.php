{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/reviews/reviewed-by-user.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>{{ __('ui.user.user-id') }}</flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.user />
        </flux:input.group.prefix>
        <flux:input
            clearable
            copyable
            inputmode="numeric"
            wire:model.live.debounce.300ms="reviewsReviewedByUserId"
            placeholder="{{ __('ui.user.user-id') }}"
        />
    </flux:input.group>
</flux:field>
