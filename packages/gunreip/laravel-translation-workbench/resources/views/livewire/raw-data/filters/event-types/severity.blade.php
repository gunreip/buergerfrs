{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/event-types/severity.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>{{ __('Severity') }}</flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.triangle-alert />
        </flux:input.group.prefix>
        <flux:select
            wire:model.live="eventTypesSeverity"
            variant="listbox"
        >
            <x-ui.input.select-option
                value="all"
                icon="triangle-alert"
            >
                {{ __('All severities') }}
            </x-ui.input.select-option>

            @foreach ($eventTypeOptions['severities'] ?? [] as $severity)
                <x-ui.input.select-option
                    value="{{ $severity }}"
                    icon="triangle-alert"
                    text-class="text-sm"
                >
                    {{ $severity }}
                </x-ui.input.select-option>
            @endforeach
        </flux:select>
    </flux:input.group>
</flux:field>
