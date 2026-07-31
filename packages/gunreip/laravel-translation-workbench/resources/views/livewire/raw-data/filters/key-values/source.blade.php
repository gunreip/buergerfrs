{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/key-values/source.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>{{ __('ui.source') }}</flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.database />
        </flux:input.group.prefix>
        <flux:select
            wire:model.live="keyValuesSource"
            variant="listbox"
            placeholder="{{ __('Select a source') }}"
            searchable
        >
            <x-ui.input.select-option
                value="all"
                icon="database"
                {{-- icon-class="text-sky-400" --}}
            >
                {{ __('ui.filters.all-sources') }}
            </x-ui.input.select-option>
            @foreach ($keyValueOptions['sources'] ?? [] as $source)
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
