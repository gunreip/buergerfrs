{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/source-files/filename.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>
        {{ __('Filename') }}
    </flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.file-code />
        </flux:input.group.prefix>
        <flux:select
            wire:key="source-files-filename-{{ $sourceFilesArea }}-{{ $sourceFilesDomain }}-{{ $sourceFilesSection }}-{{ $sourceFilesContext }}-{{ $sourceFilesScope }}-{{ $sourceFilesExtra }}-{{ count($sourceFileOptions['filenames'] ?? []) }}"
            wire:model.live="sourceFilesFilename"
            variant="listbox"
            searchable
            :disabled="count($sourceFileOptions['filenames'] ?? []) === 0"
        >
            <x-ui.input.select-option
                value="all"
                icon="file-code"
            >
                {{ count($sourceFileOptions['filenames'] ?? []) === 0 ? __('No filenames') : __('All filenames') }}
            </x-ui.input.select-option>
            @foreach ($sourceFileOptions['filenames'] ?? [] as $filename)
                <x-ui.input.select-option
                    value="{{ $filename }}"
                    icon="file-code"
                >
                    {{ $filename }}
                </x-ui.input.select-option>
            @endforeach
        </flux:select>
    </flux:input.group>
</flux:field>
