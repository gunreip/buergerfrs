{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/findings/group.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>
        {{ __('Group') }}
    </flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.component />
        </flux:input.group.prefix>
        <flux:select
            wire:key="findings-group-{{ $findingsScope }}-{{ count($findingOptions['groups'] ?? []) }}"
            wire:model.live="findingsGroup"
            variant="listbox"
            searchable
        >
            <x-ui.input.select-option
                value="all"
                icon="component"
            >
                {{ __('ui.filters.all-groups') }}
            </x-ui.input.select-option>

            @foreach ($findingOptions['groups'] ?? [] as $group)
                <x-ui.input.select-option
                    value="{{ $group }}"
                    icon="component"
                >
                    {{ $group }}
                </x-ui.input.select-option>
            @endforeach
        </flux:select>
    </flux:input.group>
</flux:field>
