{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/key-inventory/group.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>{{ __('Group') }}</flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.folder />
        </flux:input.group.prefix>
        <flux:select
            wire:model.live="keyInventoryGroup"
            variant="listbox"
            searchable
        >
            <x-ui.input.select-option
                value="all"
                icon="asterisk"
                icon-class="text-sky-400"
            >
                {{ __('ui.filters.all-groups') }}
            </x-ui.input.select-option>

            @foreach ($keyInventoryOptions['groups'] ?? [] as $group)
                <x-ui.input.select-option
                    value="{{ $group }}"
                    icon="folder"
                >
                    {{ $group }}
                </x-ui.input.select-option>
            @endforeach
        </flux:select>
    </flux:input.group>
</flux:field>
