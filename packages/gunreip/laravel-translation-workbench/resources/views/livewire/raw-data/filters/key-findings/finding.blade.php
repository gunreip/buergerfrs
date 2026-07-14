{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/key-findings/finding.blade.php --}}

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
            wire:model.live.debounce.300ms="keyFindingsFindingId"
            placeholder="{{ __('Exact finding ID') }}"
        />
    </flux:input.group>
</flux:field>
