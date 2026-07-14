{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/lang-values/source-path.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>{{ __('Source path') }}</flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.file-text />
        </flux:input.group.prefix>
        <flux:input
            clearable
            copyable
            wire:model.live.debounce.300ms="langValuesSourcePath"
            placeholder="{{ __('Contains source path') }}"
        />
    </flux:input.group>
</flux:field>
