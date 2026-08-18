{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/key-inventory/key-type.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>{{ __('Key type') }}</flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.tags />
        </flux:input.group.prefix>
        <flux:select
            wire:model.live="keyInventoryKeyType"
            variant="listbox"
            searchable
        >
            <x-ui.input.select-option
                value="all"
                icon="asterisk"
                icon-class="text-sky-400"
            >
                {{ __('All key types') }}
            </x-ui.input.select-option>

            @foreach ($keyInventoryOptions['key_types'] ?? [] as $keyType)
                <x-ui.input.select-option
                    value="{{ $keyType }}"
                    icon="tag"
                >
                    {{ $keyType }}
                </x-ui.input.select-option>
            @endforeach
        </flux:select>
    </flux:input.group>
</flux:field>
