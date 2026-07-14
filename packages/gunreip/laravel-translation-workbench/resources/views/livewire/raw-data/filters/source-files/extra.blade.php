{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/source-files/extra.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>
        {{ __('Extra') }}
    </flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.list-tree />
        </flux:input.group.prefix>
        <flux:select
            wire:key="source-files-extra-{{ $sourceFilesScope }}-{{ count($sourceFileOptions['extras'] ?? []) }}"
            wire:model.live="sourceFilesExtra"
            variant="listbox"
            searchable
            :disabled="count($sourceFileOptions['extras'] ?? []) === 0"
        >
            <x-ui.input.select-option
                value="all"
                icon="list-tree"
            >
                {{ count($sourceFileOptions['extras'] ?? []) === 0 ? __('No extras') : __('All extras') }}
            </x-ui.input.select-option>
            @foreach ($sourceFileOptions['extras'] ?? [] as $extra)
                <x-ui.input.select-option
                    value="{{ $extra }}"
                    icon="list-tree"
                >
                    {{ $extra }}
                </x-ui.input.select-option>
            @endforeach
        </flux:select>
    </flux:input.group>
</flux:field>
