{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/dynamic-sources/status.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>
        {{ __('Status') }}
    </flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.waypoints />
        </flux:input.group.prefix>
        <flux:select
            wire:model.live="dynamicSourcesStatus"
            variant="listbox"
            searchable
        >
            <x-ui.input.select-option
                value="all"
                icon="waypoints"
            >
                {{ __('ui.states.all') }}
            </x-ui.input.select-option>
            @foreach ($dynamicSourceOptions['statuses'] ?? [] as $option)
                <x-ui.input.select-option
                    value="{{ $option }}"
                    icon="waypoints"
                >
                    {{ $option }}
                </x-ui.input.select-option>
            @endforeach
        </flux:select>
    </flux:input.group>
</flux:field>
