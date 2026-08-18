{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/key-inventory/candidate-for-lang-delete.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>{{ __('Lang delete candidate') }}</flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.trash-2 />
        </flux:input.group.prefix>
        <flux:select wire:model.live="keyInventoryCandidateForLangDelete" variant="listbox">
            <x-ui.input.select-option value="all" icon="asterisk" icon-class="text-sky-400">{{ __('ui.states.all') }}</x-ui.input.select-option>
            <x-ui.input.select-option value="yes" icon="check" icon-class="text-green-400">{{ __('ui.filters.yes') }}</x-ui.input.select-option>
            <x-ui.input.select-option value="no" icon="x" icon-class="text-red-400">{{ __('No') }}</x-ui.input.select-option>
        </flux:select>
    </flux:input.group>
</flux:field>
