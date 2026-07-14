{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/timeline-events/event-type.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>{{ __('Event type') }}</flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.activity />
        </flux:input.group.prefix>
        <flux:select
            wire:model.live="timelineEventsEventType"
            variant="listbox"
            searchable
        >
            <x-ui.input.select-option
                value="all"
                icon="asterisk"
                icon-class="text-sky-400"
            >
                {{ __('All event types') }}
            </x-ui.input.select-option>

            @foreach ($timelineEventOptions['event_types'] ?? [] as $eventType)
                <x-ui.input.select-option
                    value="{{ $eventType }}"
                    icon="activity"
                >
                    {{ $eventType }}
                </x-ui.input.select-option>
            @endforeach
        </flux:select>
    </flux:input.group>
</flux:field>
