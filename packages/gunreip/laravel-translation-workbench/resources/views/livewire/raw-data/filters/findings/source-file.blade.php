{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/findings/source-file.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>
        {{ __('Source file ID') }}
    </flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.hash />
        </flux:input.group.prefix>
        <flux:input
            clearable
            copyable
            inputmode="numeric"
            wire:model.live.debounce.300ms="findingsSourceFileId"
            placeholder="{{ __('Exact source file ID') }}"
        />
    </flux:input.group>
</flux:field>
