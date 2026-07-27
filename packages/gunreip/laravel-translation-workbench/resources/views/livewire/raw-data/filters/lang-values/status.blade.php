{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/lang-values/status.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>
        {{ __('Status') }}
    </flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.badge-check />
        </flux:input.group.prefix>
        <flux:select
            wire:model.live="langValuesStatus"
            variant="listbox"
            searchable
        >
            <x-ui.input.select-option
                value="all"
                icon="badge-check"
            >
                {{ __('ui.all-statuses') }}
            </x-ui.input.select-option>

            @foreach ($langValueOptions['statuses'] ?? [] as $status)
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
