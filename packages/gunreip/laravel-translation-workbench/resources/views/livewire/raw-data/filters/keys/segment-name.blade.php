{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/keys/segment-name.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>{{ __('Name') }}</flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.tag />
        </flux:input.group.prefix>
        <flux:select
            wire:model.live="keysSegmentName"
            variant="listbox"
            searchable
            :disabled="$keysNamespace === 'all' || $keysGroup === 'all' || count($keyOptions['segment_names'] ?? []) === 0"
        >
            <x-ui.input.select-option
                value="all"
                icon="tag"
            >
                {{ $keysNamespace === 'all' || $keysGroup === 'all' ? __('Select namespace and group first') : __('All names') }}
            </x-ui.input.select-option>

            @foreach ($keyOptions['segment_names'] ?? [] as $segmentName)
                <x-ui.input.select-option
                    value="{{ $segmentName }}"
                    icon="tag"
                >
                    {{ $segmentName }}
                </x-ui.input.select-option>
            @endforeach
        </flux:select>
    </flux:input.group>
</flux:field>
