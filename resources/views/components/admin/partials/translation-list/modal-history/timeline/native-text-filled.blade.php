{{-- resources/views/components/admin/partials/translation-list/modal-history/timeline/native-text-filled.blade.php --}}

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
        {{ __('admin.translation_list.modal_history.reason') }}
    </div>

    <div class="wrap-anywhere col-span-3 text-sm">
        {{ __('The native text was populated from the latest audit source.') }}
    </div>
</div>

<div class="mt-3 grid items-start gap-3 md:grid-cols-4">
    <div class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
        <flux:icon.language class="inset mr-1 inline h-4 w-4 min-w-0 text-teal-500 dark:text-teal-400" />
        {{ __('admin.translation_list.modal.native_text') }}
    </div>

    <div class="wrap-anywhere col-span-3 text-sm text-teal-600 dark:text-teal-400">
        {{ $historyEvent->new_value ?? '—' }}
    </div>
</div>

<x-admin.partials.translation-list.modal-history.timeline.shared.affected-usages
    :usages="$historyUsages"
    :is-snapshot="$historyUsagesAreSnapshot"
/>
