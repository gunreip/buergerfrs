{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/findings/entry-type.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>
        {{ __('Entry type') }}
    </flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.tag />
        </flux:input.group.prefix>
        <flux:select
            wire:model.live="findingsEntryType"
            variant="listbox"
            searchable
        >
            <x-ui.input.select-option
                value="all"
                icon="tag"
            >
                {{ __('All entry types') }}
            </x-ui.input.select-option>

            @foreach ($findingOptions['entry_types'] ?? [] as $entryType)
                <x-ui.input.select-option
                    value="{{ $entryType }}"
                    icon="tag"
                >
                    {{ $entryType }}
                </x-ui.input.select-option>
            @endforeach
        </flux:select>
    </flux:input.group>
</flux:field>
