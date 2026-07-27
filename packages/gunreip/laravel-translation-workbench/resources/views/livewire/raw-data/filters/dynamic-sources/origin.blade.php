{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/dynamic-sources/origin.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>
        {{ __('Origin') }}
    </flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.database />
        </flux:input.group.prefix>
        <flux:select
            wire:model.live="dynamicSourcesOrigin"
            variant="listbox"
            searchable
        >
            <x-ui.input.select-option
                value="all"
                icon="database"
            >
                {{ __('ui.states.all') }}
            </x-ui.input.select-option>
            @foreach ($dynamicSourceOptions['origins'] ?? [] as $option)
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
