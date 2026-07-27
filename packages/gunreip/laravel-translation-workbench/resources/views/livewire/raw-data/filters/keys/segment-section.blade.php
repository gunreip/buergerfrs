{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/keys/segment-section.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>{{ __('Section') }}</flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.folder />
        </flux:input.group.prefix>
        <flux:select
            wire:model.live="keysSegmentSection"
            variant="listbox"
            searchable
            :disabled="$keysSegmentDomain === 'all' || count($keyOptions['segment_sections'] ?? []) === 0"
        >
            <x-ui.input.select-option
                value="all"
                icon="folder"
            >
                {{ $keysSegmentDomain === 'all' ? __('Select domain first') : __('ui.filters.all-sections') }}
            </x-ui.input.select-option>

            @foreach ($keyOptions['segment_sections'] ?? [] as $segmentSection)
                <x-ui.input.select-option
                    value="{{ $segmentSection }}"
                    icon="folder"
                >
                    {{ $segmentSection }}
                </x-ui.input.select-option>
            @endforeach
        </flux:select>
    </flux:input.group>
</flux:field>
