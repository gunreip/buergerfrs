{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/source-files/section.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>
        {{ __('Section') }}
    </flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.folder />
        </flux:input.group.prefix>
        <flux:select
            wire:key="source-files-section-{{ $sourceFilesDomain }}-{{ count($sourceFileOptions['sections'] ?? []) }}"
            wire:model.live="sourceFilesSection"
            variant="listbox"
            searchable
            :disabled="count($sourceFileOptions['sections'] ?? []) === 0"
        >
            <x-ui.input.select-option
                value="all"
                icon="folder"
            >
                {{ count($sourceFileOptions['sections'] ?? []) === 0 ? __('No sections') : __('ui.filters.all-sections') }}
            </x-ui.input.select-option>
            @foreach ($sourceFileOptions['sections'] ?? [] as $section)
                <x-ui.input.select-option
                    value="{{ $section }}"
                    icon="folder"
                >
                    {{ $section }}
                </x-ui.input.select-option>
            @endforeach
        </flux:select>
    </flux:input.group>
</flux:field>
