{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/timeline-events/created-by-user-id.blade.php --}}

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
            wire:model.live.debounce.300ms="timelineEventsCreatedByUserId"
            placeholder="{{ __('Exact user ID') }}"
        />
    </flux:input.group>
</flux:field>

