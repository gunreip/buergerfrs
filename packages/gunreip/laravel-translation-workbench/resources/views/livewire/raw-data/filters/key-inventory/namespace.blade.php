{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/key-inventory/namespace.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>{{ __('Namespace') }}</flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.folder-tree />
        </flux:input.group.prefix>
        <flux:select
            wire:model.live="keyInventoryNamespace"
            variant="listbox"
            searchable
        >
            <x-ui.input.select-option
                value="all"
                icon="asterisk"
                icon-class="text-sky-400"
            >
                {{ __('ui.filters.all-namespaces') }}
            </x-ui.input.select-option>

            @foreach ($keyInventoryOptions['namespaces'] ?? [] as $namespace)
                <x-ui.input.select-option
                    value="{{ $namespace }}"
                    icon="folder-tree"
                >
                    {{ $namespace }}
                </x-ui.input.select-option>
            @endforeach
        </flux:select>
    </flux:input.group>
</flux:field>
