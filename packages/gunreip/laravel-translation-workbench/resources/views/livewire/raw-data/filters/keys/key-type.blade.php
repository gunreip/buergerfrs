{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/keys/key-type.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>{{ __('Key type') }}</flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.tag />
        </flux:input.group.prefix>
        <flux:select
            wire:model.live="keysKeyType"
            variant="listbox"
            searchable
        >
            <x-ui.input.select-option
                value="all"
                icon="tag"
                icon-variant="mini"
            >
                {{ __('All key types') }}
            </x-ui.input.select-option>

            @foreach ($keyOptions['key_types'] ?? [] as $keyType)
                <x-ui.input.select-option
                    value="{{ $keyType }}"
                    icon="tag"
                    icon-variant="mini"
                >
                    {{ $keyType }}
                </x-ui.input.select-option>
            @endforeach
        </flux:select>
    </flux:input.group>
</flux:field>
