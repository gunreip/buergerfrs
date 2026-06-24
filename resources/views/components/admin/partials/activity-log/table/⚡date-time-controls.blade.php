{{-- resources/views/components/admin/partials/activity-log/table/⚡date-time-controls.blade.php --}}

<div
    class="ml-auto flex w-full flex-wrap items-end justify-end gap-3 rounded-md bg-zinc-50/50 px-3 py-2 align-middle xl:w-auto xl:flex-nowrap dark:bg-zinc-800/50">
    <div class="mr-2 flex w-28 shrink-0 self-center">
        <x-ui.tooltip.trigger
            :title="__('Date/Time')"
            :text="__(
                'Filter activity log entries by a primary created_at date range and optional time boundaries.',
            )"
        >
            <span class="text-sm font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                {{ __('Date/Time') }}
            </span>
        </x-ui.tooltip.trigger>
    </div>

    {{-- Primary date range --}}
    <div class="w-4/9 shrink-0">
        <flux:date-picker
            mode="range"
            wire:model.live="customDateRange"
            locale="de"
            :min="$datePickerMinDate"
            :max="$datePickerMaxDate"
            with-presets
            with-today
            fixed-weeks
            week-numbers
            selectable-header
            clearable
        >
            <x-slot name="trigger">
                <div class="flex items-end gap-2">
                    <div class="w-48 shrink-0">
                        <flux:date-picker.input
                            class="w-full tabular-nums"
                            variant="custom"
                            label="{{ __('From date') }}"
                            clearable
                        />
                    </div>

                    <div class="w-48 shrink-0">
                        <flux:date-picker.input
                            class="w-full tabular-nums"
                            variant="custom"
                            label="{{ __('To date') }}"
                            clearable
                        />
                    </div>
                </div>
            </x-slot>
        </flux:date-picker>
    </div>

    {{--
    TODO: locale aus App-/User-Settings setzen
    TODO: interval-Auswahl aus APP-Settings setzen
    --}}

    {{-- Optional time boundaries --}}
    <div class="flex shrink-0 items-end gap-2">
        <div class="w-42 shrink-0">
            <flux:time-picker
                class="w-full"
                wire:model.live="timeFrom"
                label="{{ __('From time') }}"
                time-format="24-hour"
                locale="de"
                :interval="$timeFromInterval"
                min="00:00"
                max="23:59"
                :disabled="$timePickersDisabled"
                clearable
            />
        </div>

        <div class="w-42 shrink-0">
            <flux:time-picker
                class="w-full"
                wire:model.live="timeTo"
                label="{{ __('To time') }}"
                time-format="24-hour"
                locale="de"
                :interval="$timeToInterval"
                :min="$timeToMinTime"
                max="23:59"
                :disabled="$timeToDisabled"
                clearable
            />
        </div>
    </div>
</div>
