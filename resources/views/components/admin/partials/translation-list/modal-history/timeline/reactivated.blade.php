{{-- resources/views/components/admin/partials/translation-list/modal-history/timeline/reactivated.blade.php --}}

@props([
    'historyEvent',
    'historyContext' => [],
    'historyLocale' => null,
    'historyUsages' => null,
    'historyUsagesAreSnapshot' => false,
])

@php
    $isUsageEvent = $historyEvent->entity_type === 'translation_usage';
    $usageRaw = is_array($historyContext) ? $historyContext['raw'] ?? null : null;
    $usageFunction = is_array($historyContext) ? $historyContext['function'] ?? null : null;
    $usageClassification = is_array($historyContext) ? $historyContext['classification'] ?? null : null;
    $oldObsoleteAt = is_array($historyContext) ? $historyContext['old_obsolete_at'] ?? null : null;

    $reasonText = match ($historyEvent->reason) {
        'audit_key_seen_again_in_latest_sync' => __(
            'The translation key was found again during the latest audit scan.',
        ),
        'stale_usage_seen_again_in_latest_sync' => __(
            'The previously stale translation usage was found again during the latest audit scan.',
        ),
        null, '' => null,
        default => \Illuminate\Support\Str::of($historyEvent->reason)
            ->replace('_', ' ')
            ->ucfirst()
            ->append('.')
            ->toString(),
    };

    $stateBefore = $isUsageEvent ? __('ui.stale.marked-stale') : $historyEvent->old_status;
    $stateAfter = $isUsageEvent ? __('ui.state.active') : $historyEvent->new_status;

    $affectedUsages = $isUsageEvent
        ? collect($historyUsages)
            ->filter(static fn($historyUsage) => (int) $historyUsage->id === (int) $historyEvent->translation_usage_id)
            ->values()
        : collect($historyUsages)->values();

    if ($isUsageEvent && $affectedUsages->isEmpty() && ($historyEvent->new_file || $historyEvent->old_file)) {
        $affectedUsages = collect([
            (object) [
                'file' => $historyEvent->new_file ?: $historyEvent->old_file,
                'line' => $historyEvent->new_line ?: $historyEvent->old_line,
                'function' => $usageFunction,
                'classification' => $usageClassification,
            ],
        ]);
    }
@endphp

@if ($reasonText)
    <div class="mt-3 grid gap-3 md:grid-cols-4">
        <div class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
            <flux:icon.package class="inset mr-1 inline h-4 w-4 min-w-0" />
            {{ __('admin.translation_list.modal_history.reason') }}
        </div>

        <div class="wrap-anywhere col-span-3 text-sm">
            {{ $reasonText }}
        </div>
    </div>
@endif

<div class="mt-3 space-y-2">
    <div class="grid items-center gap-3 md:grid-cols-4">
        <div class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
            <flux:icon.archive-x class="inset mr-1 inline h-4 w-4 min-w-0 text-red-400 dark:text-red-500" />
            {{ $isUsageEvent ? __('ui.usage.usage-state-before') : __('Status before') }}
        </div>

        <code class="wrap-anywhere col-span-3 block text-xs text-red-400 dark:text-red-500">
            {{ $stateBefore ?: '—' }}
        </code>
    </div>

    <div class="grid items-center gap-3 md:grid-cols-4">
        <div class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
            <flux:icon.refresh-ccw class="inset mr-1 inline h-4 w-4 min-w-0 text-lime-500 dark:text-lime-400" />
            {{ $isUsageEvent ? __('ui.usage.usage-state-after') : __('Status after') }}
        </div>

        <code class="wrap-anywhere col-span-3 block text-xs text-lime-600 dark:text-lime-400">
            {{ $stateAfter ?: '—' }}
        </code>
    </div>
</div>

@if ($oldObsoleteAt)
    <div class="mt-2 grid items-center gap-3 md:grid-cols-4">
        <div class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
            <flux:icon.history class="inset mr-1 inline h-4 w-4 min-w-0" />
            {{ __('Previously obsolete since') }}
        </div>

        <div class="col-span-3 -mt-1 flex flex-wrap items-center gap-2">
            <x-ui.date-time.date
                class="text-sm"
                :value="$oldObsoleteAt"
            />

            <x-ui.date-time.time
                class="text-sm text-zinc-500 dark:text-zinc-400"
                :value="$oldObsoleteAt"
            />
        </div>
    </div>
@endif

<x-admin.partials.translation-list.modal-history.timeline.shared.affected-usages
    :usages="$affectedUsages"
    :is-snapshot="$historyUsagesAreSnapshot"
/>

@if ($isUsageEvent && $usageRaw)
    <div class="mt-2 grid gap-3 md:grid-cols-4">
        <div class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
            <flux:icon.language class="inset mr-1 inline h-4 w-4 min-w-0" />
            {{ __('Translation call') }}
        </div>

        <code class="wrap-anywhere col-span-3 block text-xs">
            {{ $usageRaw }}
        </code>
    </div>
@endif
