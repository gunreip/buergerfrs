{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/dynamic-source-values/origin.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>
        {{ __('Origin') }}
    </flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.database />
        </flux:input.group.prefix>
        <flux:select
            wire:model.live="dynamicSourceValuesOrigin"
            variant="listbox"
            searchable
        >
            <x-ui.input.select-option
                value="all"
                icon="database"
            >
                {{ __('All origins') }}
            </x-ui.input.select-option>
            @foreach ($dynamicSourceValueOptions['origins'] ?? [] as $origin)
                <x-ui.input.select-option
                    value="{{ $origin }}"
                    icon="database"
                >
                    {{ $origin }}
                </x-ui.input.select-option>
            @endforeach
        </flux:select>
    </flux:input.group>
</flux:field>
