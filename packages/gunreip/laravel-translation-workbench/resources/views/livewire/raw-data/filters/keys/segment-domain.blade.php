{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/keys/segment-domain.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>{{ __('Domain') }}</flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.route />
        </flux:input.group.prefix>
        <flux:select
            wire:model.live="keysSegmentDomain"
            variant="listbox"
            searchable
            :disabled="$keysNamespace === 'all' || $keysGroup === 'all' || count($keyOptions['segment_domains'] ?? []) === 0"
        >
            <x-ui.input.select-option
                value="all"
                icon="route"
            >
                {{ $keysNamespace === 'all' || $keysGroup === 'all' ? __('Select namespace and group first') : __('ui.filters.all-domains') }}
            </x-ui.input.select-option>

            @foreach ($keyOptions['segment_domains'] ?? [] as $segmentDomain)
                <x-ui.input.select-option
                    value="{{ $segmentDomain }}"
                    icon="route"
                >
                    {{ $segmentDomain }}
                </x-ui.input.select-option>
            @endforeach
        </flux:select>
    </flux:input.group>
</flux:field>
