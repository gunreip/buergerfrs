{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/results-timeline-events.blade.php --}}

<div class="mt-3 flex flex-wrap items-center justify-center gap-x-6 gap-y-2 text-sm tabular-nums">
    <div class="flex min-w-0 items-center gap-2">
        <span class="font-semibold text-zinc-500 dark:text-zinc-400">
            {{ __('ui.created') }}:
        </span>
        <span class="font-mono text-zinc-700 dark:text-zinc-200">
            {{ $timelineEventsCreatedResult }}
        </span>
    </div>

    <div class="flex min-w-0 items-center gap-2">
        <span class="font-semibold text-zinc-500 dark:text-zinc-400">
            {{ __('ui.updated') }}:
        </span>
        <span class="font-mono text-zinc-700 dark:text-zinc-200">
            {{ $timelineEventsChangedResult }}
        </span>
    </div>

    <div class="flex min-w-0 items-center gap-2">
        <span class="font-semibold text-zinc-500 dark:text-zinc-400">
            {{ __('ui.timespan') }}:
        </span>
        <span class="font-mono text-zinc-700 dark:text-zinc-200">
            +/- {{ $timelineEventsTimeSpanResult }}
        </span>
    </div>
</div>
