{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/dynamic-key-values/status.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>{{ __('ui.state.status') }}</flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.badge-check />
        </flux:input.group.prefix>
        <flux:select
            wire:model.live="dynamicKeyValuesStatus"
            variant="listbox"
            searchable
        >
            <x-ui.input.select-option
                value="all"
                icon="badge-check"
            >
                {{ __('ui.states.all-statuses') }}
            </x-ui.input.select-option>
            @foreach ($dynamicKeyValueOptions['statuses'] ?? [] as $status)
                <x-ui.input.select-option
                    value="{{ $status }}"
                    icon="badge-check"
                >
                    {{ $status }}
                </x-ui.input.select-option>
            @endforeach
        </flux:select>
    </flux:input.group>
</flux:field>
