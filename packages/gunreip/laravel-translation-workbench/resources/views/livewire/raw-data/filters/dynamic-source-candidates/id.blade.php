{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/dynamic-source-candidates/id.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>{{ __('ID') }}</flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.hash />
        </flux:input.group.prefix>
        <flux:input clearable copyable inputmode="numeric" wire:model.live.debounce.300ms="dynamicSourceCandidatesId" placeholder="{{ __('ID') }}" />
    </flux:input.group>
</flux:field>
