{{-- resources/views/components/admin/partials/translation-list/modal-history/timeline/native-text-changed.blade.php --}}

@props([
    'historyEvent',
    'historyContext' => [],
    'historyLocale' => null,
    'historyUsages' => null,
    'historyUsagesAreSnapshot' => false,
])

<div class="mt-3 grid gap-3 md:grid-cols-4">
    <div class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
        <flux:icon.package class="inset mr-1 inline h-4 w-4 min-w-0" />
        {{ __('Reason') }}
    </div>

    <div class="wrap-anywhere col-span-3 text-sm">
        {{ __('The native text detected in the audit source has changed.') }}
    </div>
</div>

<div class="mt-3 space-y-2">
    <div class="grid items-start gap-3 md:grid-cols-4">
        <div class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
            <flux:icon.language class="inset mr-1 inline h-4 w-4 min-w-0 text-blue-300 dark:text-blue-600" />
            {{ __('Native text before') }}
        </div>

        <div class="wrap-anywhere col-span-3 text-sm text-blue-300 dark:text-blue-600">
            {{ $historyEvent->old_value ?? '—' }}
        </div>
    </div>

    <div class="grid items-start gap-3 md:grid-cols-4">
        <div class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
            <flux:icon.language class="inset mr-1 inline h-4 w-4 min-w-0 text-orange-300 dark:text-orange-600" />
            {{ __('Native text after') }}
        </div>

        <div class="wrap-anywhere col-span-3 text-sm text-orange-300 dark:text-orange-600">
            {{ $historyEvent->new_value ?? '—' }}
        </div>
    </div>
</div>

<x-admin.partials.translation-list.modal-history.timeline.shared.affected-usages
    :usages="$historyUsages"
    :is-snapshot="$historyUsagesAreSnapshot"
/>
