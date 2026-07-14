{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/timeline-events/changed-range.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <div class="grid grid-cols-3 gap-3">
        <flux:date-picker
            class="col-span-2"
            mode="range"
            wire:model.live="timelineEventsChangedRange"
            locale="de"
            :min="$timelineEventsChangedMinDate"
            :max="$timelineEventsChangedMaxDate"
            with-presets
            presets="last7Days yesterday today thisWeek thisMonth yearToDate allTime"
            with-today
            fixed-weeks
            week-numbers
            selectable-header
            clearable
        >
            <x-slot name="trigger">
                <div class="grid grid-cols-2 gap-3">
                    <flux:date-picker.input
                        class="w-full tabular-nums"
                        variant="custom"
                        label="{{ __('Changed from') }}"
                        clearable
                    />

                    <flux:date-picker.input
                        class="w-full tabular-nums"
                        variant="custom"
                        label="{{ __('Changed to') }}"
                        clearable
                    />
                </div>
            </x-slot>
        </flux:date-picker>

        <flux:time-picker
            class="w-full"
            wire:model.live="timelineEventsChangedTime"
            label="{{ __('Changed time') }}"
            time-format="24-hour"
            locale="de"
            interval="60"
            :min="$timelineEventsChangedMinTime"
            :max="$timelineEventsChangedMaxTime"
            :disabled="$timelineEventsChangedTimePickerDisabled"
            clearable
        />
    </div>
</flux:field>
