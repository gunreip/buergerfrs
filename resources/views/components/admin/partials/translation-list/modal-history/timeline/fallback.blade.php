{{-- resources/views/components/admin/partials/translation-list/modal-history/timeline/⚡fallback.blade.php --}}

@props(['historyEvent', 'historyContext' => [], 'historyLocale' => null])

<div
    class="mt-3 rounded-lg border border-red-300 bg-red-50 p-3 text-sm text-red-800 dark:border-red-800 dark:bg-red-950/30 dark:text-red-200">
    <div class="font-semibold">
        {{ __('Fallback renderer') }}
    </div>

    <div class="mt-1">
        {{ __('No dedicated timeline component exists for this event type yet.') }}

        <span class="font-mono">
            {{ $historyEvent->event_type }}
        </span>
    </div>
</div>

@if ($historyEvent->reason)
    <div class="mt-3 text-sm">
        <span class="font-semibold">
            {{ __('Reason') }}:
        </span>

        <span class="ml-2">
            {{ $historyEvent->reason }}
        </span>
    </div>
@endif

@if ($historyEvent->old_value || $historyEvent->new_value)
    <div class="mt-3 grid gap-3 md:grid-cols-2">
        <div>
            <div class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                {{ __('Old value') }}
            </div>

            <div class="wrap-anywhere mt-1 rounded-lg bg-zinc-100 p-2 font-mono text-xs dark:bg-zinc-900">
                {{ $historyEvent->old_value ?? '—' }}
            </div>
        </div>

        <div>
            <div class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                {{ __('New value') }}
            </div>

            <div class="wrap-anywhere mt-1 rounded-lg bg-zinc-100 p-2 font-mono text-xs dark:bg-zinc-900">
                {{ $historyEvent->new_value ?? '—' }}
            </div>
        </div>
    </div>
@endif

@if ($historyEvent->old_status || $historyEvent->new_status)
    <div class="mt-3 flex flex-wrap gap-3 text-sm">
        <div>
            <span class="font-semibold">
                {{ __('Old status') }}:
            </span>

            <span class="ml-2 font-mono">
                {{ $historyEvent->old_status ?? '—' }}
            </span>
        </div>

        <div>
            <span class="font-semibold">
                {{ __('New status') }}:
            </span>

            <span class="ml-2 font-mono">
                {{ $historyEvent->new_status ?? '—' }}
            </span>
        </div>
    </div>
@endif

@if ($historyEvent->old_file || $historyEvent->new_file || $historyEvent->old_line || $historyEvent->new_line)
    <div class="mt-3 grid gap-3 md:grid-cols-2">
        <div>
            <div class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                {{ __('Old location') }}
            </div>

            <code class="wrap-anywhere mt-1 block text-xs">
                {{ $historyEvent->old_file ?: '—' }}:{{ $historyEvent->old_line ?: '—' }}
            </code>
        </div>

        <div>
            <div class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                {{ __('New location') }}
            </div>

            <code class="wrap-anywhere mt-1 block text-xs">
                {{ $historyEvent->new_file ?: '—' }}:{{ $historyEvent->new_line ?: '—' }}
            </code>
        </div>
    </div>
@endif
