{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/source-files/root.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>
        {{ __('Root') }}
    </flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.folder-tree />
        </flux:input.group.prefix>
        <flux:select
            wire:model.live="sourceFilesRoot"
            variant="listbox"
            searchable
        >
            <x-ui.input.select-option
                value="all"
                icon="folder-tree"
            >{{ __('All roots') }}
            </x-ui.input.select-option>
            @foreach ($sourceFileOptions['roots'] ?? [] as $root)
                <x-ui.input.select-option
                    value="{{ $root }}"
                    icon="folder-tree"
                >
                    {{ $root }}
                </x-ui.input.select-option>
            @endforeach
        </flux:select>
    </flux:input.group>
</flux:field>
