{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/keys/segment-extra.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>{{ __('Extra') }}</flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.list-tree />
        </flux:input.group.prefix>
        <flux:select
            wire:model.live="keysSegmentExtra"
            variant="listbox"
            searchable
            :disabled="$keysSegmentContext === 'all' || count($keyOptions['segment_extras'] ?? []) === 0"
        >
            <x-ui.input.select-option
                value="all"
                icon="list-tree"
            >
                {{ $keysSegmentContext === 'all' ? __('Select context first') : __('All extras') }}
            </x-ui.input.select-option>

            @foreach ($keyOptions['segment_extras'] ?? [] as $segmentExtra)
                <x-ui.input.select-option
                    value="{{ $segmentExtra }}"
                    icon="list-tree"
                >
                    {{ $segmentExtra }}
                </x-ui.input.select-option>
            @endforeach
        </flux:select>
    </flux:input.group>
</flux:field>
