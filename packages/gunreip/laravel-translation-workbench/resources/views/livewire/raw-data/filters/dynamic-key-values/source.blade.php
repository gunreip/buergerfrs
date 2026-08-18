{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/dynamic-key-values/source.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>{{ __('ui.source.source') }}</flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.database />
        </flux:input.group.prefix>
        <flux:select
            wire:model.live="dynamicKeyValuesSource"
            variant="listbox"
            searchable
        >
            <x-ui.input.select-option
                value="all"
                icon="database"
            >
                {{ __('ui.filters.all-sources') }}
            </x-ui.input.select-option>
            @foreach ($dynamicKeyValueOptions['sources'] ?? [] as $source)
                <x-ui.input.select-option
                    value="{{ $source }}"
                    icon="database"
                >
                    {{ $source }}
                </x-ui.input.select-option>
            @endforeach
        </flux:select>
    </flux:input.group>
</flux:field>
