{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/findings/source-line.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>{{ __('Line') }}</flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.list-ordered />
        </flux:input.group.prefix>
        <flux:input
            clearable
            copyable
            inputmode="numeric"
            wire:model.live.debounce.300ms="findingsSourceLine"
            placeholder="{{ __('Exact line') }}"
        />
    </flux:input.group>
</flux:field>
