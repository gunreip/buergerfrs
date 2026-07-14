{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/keys/group.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>{{ __('Group') }}</flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.component />
        </flux:input.group.prefix>
        <flux:select
            wire:model.live="keysGroup"
            variant="listbox"
            searchable
            :disabled="$keysNamespace === 'all'"
        >
            <x-ui.input.select-option
                value="all"
                icon="component"
            >
                {{ $keysNamespace === 'all' ? __('Select namespace first') : __('All groups') }}
            </x-ui.input.select-option>

            @foreach ($keyOptions['groups'] ?? [] as $group)
                <x-ui.input.select-option
                    value="{{ $group }}"
                    icon="component"
                >
                    {{ $group }}
                </x-ui.input.select-option>
            @endforeach
        </flux:select>
    </flux:input.group>
</flux:field>
