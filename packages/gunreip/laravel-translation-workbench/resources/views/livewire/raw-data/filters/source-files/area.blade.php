{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/source-files/area.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>
        {{ __('Area') }}
    </flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.layout-panel-top />
        </flux:input.group.prefix>
        <flux:select
            wire:key="source-files-area-{{ $sourceFilesRoot }}-{{ count($sourceFileOptions['areas'] ?? []) }}"
            wire:model.live="sourceFilesArea"
            variant="listbox"
            searchable
            :disabled="count($sourceFileOptions['areas'] ?? []) === 0"
        >
            <x-ui.input.select-option
                value="all"
                icon="layout-panel-top"
            >
                {{ count($sourceFileOptions['areas'] ?? []) === 0 ? __('No areas') : __('All areas') }}
            </x-ui.input.select-option>
            @foreach ($sourceFileOptions['areas'] ?? [] as $area)
                <x-ui.input.select-option
                    value="{{ $area }}"
                    icon="layout-panel-top"
                >
                    {{ $area }}
                </x-ui.input.select-option>
            @endforeach
        </flux:select>
    </flux:input.group>
</flux:field>
