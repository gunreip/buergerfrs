{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/lang-values/value-type.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>
        {{ __('Value type') }}
    </flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.type />
        </flux:input.group.prefix>
        <flux:select
            wire:model.live="langValuesValueType"
            variant="listbox"
            searchable
        >
            <x-ui.input.select-option
                value="all"
                icon="type"
            >
                {{ __('All value types') }}
            </x-ui.input.select-option>

            @foreach ($langValueOptions['value_types'] ?? [] as $valueType)
                <x-ui.input.select-option
                    value="{{ $valueType }}"
                    icon="type"
                >
                    {{ $valueType }}
                </x-ui.input.select-option>
            @endforeach
        </flux:select>
    </flux:input.group>
</flux:field>
