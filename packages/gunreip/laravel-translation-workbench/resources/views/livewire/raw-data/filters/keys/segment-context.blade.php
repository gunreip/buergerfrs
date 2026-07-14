{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/keys/segment-context.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>{{ __('Context') }}</flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.component />
        </flux:input.group.prefix>
        <flux:select
            wire:model.live="keysSegmentContext"
            variant="listbox"
            searchable
            :disabled="$keysSegmentSection === 'all' || count($keyOptions['segment_contexts'] ?? []) === 0"
        >
            <x-ui.input.select-option
                value="all"
                icon="component"
            >
                {{ $keysSegmentSection === 'all' ? __('Select section first') : __('All contexts') }}
            </x-ui.input.select-option>

            @foreach ($keyOptions['segment_contexts'] ?? [] as $segmentContext)
                <x-ui.input.select-option
                    value="{{ $segmentContext }}"
                    icon="component"
                >
                    {{ $segmentContext }}
                </x-ui.input.select-option>
            @endforeach
        </flux:select>
    </flux:input.group>
</flux:field>
