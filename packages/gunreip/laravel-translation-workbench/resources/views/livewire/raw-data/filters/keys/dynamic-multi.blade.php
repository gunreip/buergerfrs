{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/keys/dynamic-multi.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>{{ __('Dynamic multi') }}</flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.list-checks />
        </flux:input.group.prefix>
        <flux:select
            wire:model.live="keysIsDynamicMulti"
            variant="listbox"
        >
            <x-ui.input.select-option
                value="all"
                icon="asterisk"
                icon-class="text-sky-400"
            >
                {{ __('All') }}
            </x-ui.input.select-option>
            <x-ui.input.select-option
                value="yes"
                icon="check"
                icon-class="text-green-400"
            >
                {{ __('Yes') }}
            </x-ui.input.select-option>
            <x-ui.input.select-option
                value="no"
                icon="x"
                icon-class="text-red-400"
            >
                {{ __('No') }}
            </x-ui.input.select-option>
        </flux:select>
    </flux:input.group>
</flux:field>
