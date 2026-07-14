{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/source-files/extension.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>{{ __('Extension') }}</flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.file-code />
        </flux:input.group.prefix>
        <flux:select
            wire:key="source-files-extension-{{ $sourceFilesSourceType }}-{{ $sourceFilesRoot }}-{{ $sourceFilesArea }}-{{ $sourceFilesDomain }}-{{ $sourceFilesSection }}-{{ $sourceFilesContext }}-{{ $sourceFilesFilename }}-{{ count($sourceFileOptions['extensions'] ?? []) }}"
            wire:model.live="sourceFilesExtension"
            variant="listbox"
            searchable
            :disabled="count($sourceFileOptions['extensions'] ?? []) === 0"
        >
            <x-ui.input.select-option
                value="all"
                icon="file-code"
            >
                {{ count($sourceFileOptions['extensions'] ?? []) === 0 ? __('No extensions') : __('All extensions') }}
            </x-ui.input.select-option>

            @foreach ($sourceFileOptions['extensions'] ?? [] as $extension)
                <x-ui.input.select-option
                    value="{{ $extension }}"
                    icon="file-code"
                >
                    {{ $extension }}
                </x-ui.input.select-option>
            @endforeach
        </flux:select>
    </flux:input.group>
</flux:field>
