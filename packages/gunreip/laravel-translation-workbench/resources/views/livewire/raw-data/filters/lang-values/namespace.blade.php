{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/lang-values/namespace.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>
        {{ __('Namespace') }}
    </flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.folder-tree />
        </flux:input.group.prefix>
        <flux:select
            wire:model.live="langValuesNamespace"
            variant="listbox"
            searchable
        >
            <x-ui.input.select-option
                value="all"
                icon="folder-tree"
                icon-class="text-zinc-400"
            >
                {{ __('All namespaces') }}
            </x-ui.input.select-option>

            @foreach ($langValueOptions['namespaces'] ?? [] as $namespace)
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
