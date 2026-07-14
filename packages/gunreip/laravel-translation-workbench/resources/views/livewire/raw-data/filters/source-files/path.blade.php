{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/source-files/path.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>{{ __('Path') }}</flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.folder />
        </flux:input.group.prefix>
        <flux:select
            wire:key="source-files-path-{{ $sourceFilesSourceType }}-{{ $sourceFilesExtension }}-{{ $sourceFilesStatus }}-{{ $sourceFilesRoot }}-{{ $sourceFilesArea }}-{{ $sourceFilesPackageVendor }}-{{ $sourceFilesPackageName }}-{{ $sourceFilesDomain }}-{{ $sourceFilesSection }}-{{ $sourceFilesContext }}-{{ $sourceFilesScope }}-{{ $sourceFilesExtra }}-{{ $sourceFilesFilename }}-{{ md5($sourceFilesSearch) }}-{{ count($sourceFilePathOptions ?? []) }}"
            wire:model.live="sourceFilesPath"
            variant="listbox"
            searchable
            :disabled="count($sourceFilePathOptions ?? []) === 0"
        >
            <x-ui.input.select-option
                value="all"
                icon="files"
                icon-class="text-zinc-400"
            >
                {{ count($sourceFilePathOptions ?? []) === 0 ? __('Select source type, extension, status or search first') : __('All paths') }}
            </x-ui.input.select-option>

            @foreach ($sourceFilePathOptions ?? [] as $path)
                <x-ui.input.select-option
                    value="{{ $path }}"
                    icon="file-code"
                    icon-class="shrink-0 text-zinc-400"
                    text-class="truncate font-mono text-xs"
                >
                    {{ $path }}
                </x-ui.input.select-option>
            @endforeach
        </flux:select>
    </flux:input.group>
</flux:field>
