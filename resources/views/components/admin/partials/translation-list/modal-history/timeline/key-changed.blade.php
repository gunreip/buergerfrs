{{-- resources/views/components/admin/partials/translation-list/modal-history/timeline/key-changed.blade.php --}}

@props([
    'historyEvent',
    'historyContext' => [],
    'historyLocale' => null,
    'historyUsages' => null,
    'historyUsagesAreSnapshot' => false,
])

@php
    $reasonText = match ($historyEvent->reason) {
        'suggested_key_applied_from_review_modal' => __('The suggested translation key was applied during the review.'),
        null, '' => null,
        default => \Illuminate\Support\Str::of($historyEvent->reason)
            ->replace('_', ' ')
            ->ucfirst()
            ->append('.')
            ->toString(),
    };

    $nativeText = $historyEvent->new_value ?? $historyEvent->old_value;
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
            <flux:icon.key class="inset mr-1 inline h-4 w-4 min-w-0 text-blue-300 dark:text-blue-600" />
            {{ __('Key before') }}
        </div>

        <code class="wrap-anywhere col-span-3 block text-xs text-blue-300 dark:text-blue-600">
            {{ $historyEvent->old_key ?: '—' }}
        </code>
    </div>

    <div class="grid items-center gap-3 md:grid-cols-4">
        <div class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
            <flux:icon.key class="inset mr-1 inline h-4 w-4 min-w-0 text-orange-300 dark:text-orange-600" />
            {{ __('Key after') }}
        </div>

        <code class="wrap-anywhere col-span-3 block text-xs text-orange-300 dark:text-orange-600">
            {{ $historyEvent->new_key ?: '—' }}
        </code>
    </div>
</div>

@if ($nativeText !== null && $nativeText !== '')
    <div class="mt-2 grid gap-3 md:grid-cols-4">
        <div class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
            <flux:icon.language class="inset mr-1 inline h-4 w-4 min-w-0" />
            {{ __('admin.translation_list.modal.native_text') }}
        </div>

        <div class="wrap-anywhere col-span-3 text-sm">
            {{ $nativeText }}
        </div>
    </div>
@endif

<x-admin.partials.translation-list.modal-history.timeline.shared.affected-usages
    :usages="$historyUsages"
    :is-snapshot="$historyUsagesAreSnapshot"
/>
