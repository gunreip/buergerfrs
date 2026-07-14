{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/event-types/key.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>{{ __('Key') }}</flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.key />
        </flux:input.group.prefix>
        <flux:select
            wire:model.live="eventTypesKey"
            variant="listbox"
            searchable
        >
            <x-ui.input.select-option value="all" icon="key">
                {{ __('All keys') }}
            </x-ui.input.select-option>

            @foreach ($eventTypeOptions['keys'] ?? [] as $key)
                <x-ui.input.select-option
                    value="{{ $key }}"
                    icon="key"
                    text-class="font-mono text-xs"
                >
                    {{ $key }}
                </x-ui.input.select-option>
            @endforeach
        </flux:select>
    </flux:input.group>
</flux:field>
