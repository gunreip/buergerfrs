{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/timeline-events/time-span.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:time-picker
        class="w-full"
        wire:model.live="timelineEventsTimeSpan"
        label="{{ __('Set Timespan') }}"
        time-format="24-hour"
        locale="de"
        interval="60"
        min="00:00"
        max="12:00"
        clearable
    />
</flux:field>
