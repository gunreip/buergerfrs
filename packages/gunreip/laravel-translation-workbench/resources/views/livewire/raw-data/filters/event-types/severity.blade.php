{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/event-types/severity.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>{{ __('Severity') }}</flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.exclamation-triangle />
        </flux:input.group.prefix>
        <flux:select
            wire:model.live="eventTypesSeverity"
            variant="listbox"
        >
            <x-ui.input.select-option value="all" icon="exclamation-triangle">
                {{ __('All severities') }}
            </x-ui.input.select-option>

            @foreach ($eventTypeOptions['severities'] ?? [] as $severity)
                <x-ui.input.select-option
                    value="{{ $severity }}"
                    icon="exclamation-triangle"
                    text-class="font-mono text-xs"
                >
                    {{ $severity }}
                </x-ui.input.select-option>
            @endforeach
        </flux:select>
    </flux:input.group>
</flux:field>
