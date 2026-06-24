{{-- resources/views/components/admin/partials/translation-list/modal-history/timeline/⚡created.blade.php --}}

@props([
    'historyEvent',
    'historyContext' => [],
    'historyLocale' => null,
    'historyUsages' => null,
    'historyUsagesAreSnapshot' => false,
])

@php
    $sourceUsages = collect($historyUsages)->reject(
        static fn($historyUsage) => $historyUsage->classification === 'key',
    );

    $originalSourceCalls = $sourceUsages
        ->map(static fn($historyUsage) => $historyUsage->original_raw ?: $historyUsage->raw)
        ->filter(static fn($sourceCall) => is_string($sourceCall) && trim($sourceCall) !== '')
        ->unique()
        ->values();
@endphp

<div class="mt-3 grid gap-3 md:grid-cols-4">
    <div class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
        <flux:icon.key class="inset mr-1 inline h-4 w-4 min-w-0" />
        {{ __('Translation key') }}
    </div>

    <code class="wrap-anywhere col-span-3 text-xs">
        {{ $historyEvent->new_key ?: $historyEvent->old_key ?: '—' }}
    </code>
</div>

@if ($originalSourceCalls->isNotEmpty())
    <div class="mt-2 grid gap-3 md:grid-cols-4">
        <div class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
            <flux:icon.package class="inset mr-1 inline h-4 w-4 min-w-0" />
            {{ $originalSourceCalls->count() === 1 ? __('Original source call') : __('Original source calls') }}
        </div>

        <div class="col-span-3 space-y-1">
            @foreach ($originalSourceCalls as $originalSourceCall)
                <code class="wrap-anywhere block text-xs">
                    {{ $originalSourceCall }}
                </code>
            @endforeach
        </div>
    </div>
@endif

@if ($historyEvent->new_value !== null && $historyEvent->new_value !== '')
    <div class="mt-2 grid gap-3 md:grid-cols-4">
        <div class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
            <flux:icon.arrow-right-left class="inset mr-1 inline h-4 w-4 min-w-0" />
            {{ __('Created translation value') }}
        </div>

        <div class="wrap-anywhere col-span-3 font-mono text-xs">
            {{ $historyEvent->new_value }}
        </div>
    </div>
@endif

<x-admin.partials.translation-list.modal-history.timeline.shared.affected-usages
    :usages="$historyUsages"
    :is-snapshot="$historyUsagesAreSnapshot"
/>
