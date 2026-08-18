{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/dynamic-sources/source-type.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>
        {{ __('ui.source.source-type') }}
    </flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.database />
        </flux:input.group.prefix>
        <flux:select
            wire:model.live="dynamicSourcesSourceType"
            variant="listbox"
            searchable
        >
            <x-ui.input.select-option
                value="all"
                icon="database"
            >
                {{ __('ui.states.all') }}
            </x-ui.input.select-option>
            @foreach ($dynamicSourceOptions['source_types'] ?? [] as $option)
                <x-ui.input.select-option
                    value="{{ $option }}"
                    icon="database"
                >
                    {{ $option }}
                </x-ui.input.select-option>
            @endforeach
        </flux:select>
    </flux:input.group>
</flux:field>
