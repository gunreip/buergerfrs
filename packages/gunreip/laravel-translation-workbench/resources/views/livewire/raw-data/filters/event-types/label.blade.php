{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/event-types/label.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>{{ __('Label') }}</flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.tag />
        </flux:input.group.prefix>
        <flux:select
            wire:model.live="eventTypesLabel"
            variant="listbox"
            searchable
        >
            <x-ui.input.select-option value="all" icon="tag">
                {{ __('All labels') }}
            </x-ui.input.select-option>

            @foreach ($eventTypeOptions['labels'] ?? [] as $label)
                <x-ui.input.select-option value="{{ $label }}" icon="tag">
                    {{ $label }}
                </x-ui.input.select-option>
            @endforeach
        </flux:select>
    </flux:input.group>
</flux:field>
