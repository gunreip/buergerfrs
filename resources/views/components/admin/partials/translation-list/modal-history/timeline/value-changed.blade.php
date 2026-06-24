{{-- resources/views/components/admin/partials/translation-list/modal-history/timeline/value-changed.blade.php --}}

@props([
    'historyEvent',
    'historyContext' => [],
    'historyLocale' => null,
    'historyUsages' => null,
    'historyUsagesAreSnapshot' => false,
])

@php
    $reasonText = match ($historyEvent->reason) {
        'translation_value_saved_from_edit_modal' => __(
            'The translation value was updated in the translation edit modal.',
        ),
        null, '' => null,
        default => \Illuminate\Support\Str::of($historyEvent->reason)
            ->replace('_', ' ')
            ->ucfirst()
            ->append('.')
            ->toString(),
    };
@endphp

@if ($reasonText)
    <div class="mt-3 grid gap-3 md:grid-cols-4">
        <div class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
            <flux:icon.package class="inset mr-1 inline h-4 w-4 min-w-0" />
            {{ __('Reason') }}
        </div>

        <div class="wrap-anywhere col-span-3 text-sm">
            {{ $reasonText }}
        </div>
    </div>
@endif

<div class="mt-3 space-y-2">
    <div class="grid items-start gap-3 md:grid-cols-4">
        <div class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
            <flux:icon.language class="inset mr-1 inline h-4 w-4 min-w-0 text-blue-300 dark:text-blue-600" />
            {{ __('Translation value before') }}
        </div>

        <div class="wrap-anywhere col-span-3 font-mono text-xs text-blue-300 dark:text-blue-600">
            {{ $historyEvent->old_value ?? '—' }}
        </div>
    </div>

    <div class="grid items-start gap-3 md:grid-cols-4">
        <div class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
            <flux:icon.language class="inset mr-1 inline h-4 w-4 min-w-0 text-orange-300 dark:text-orange-600" />
            {{ __('Translation value after') }}
        </div>

        <div class="wrap-anywhere col-span-3 font-mono text-xs text-orange-300 dark:text-orange-600">
            {{ $historyEvent->new_value ?? '—' }}
        </div>
    </div>
</div>

@if ($historyEvent->old_status !== $historyEvent->new_status)
    <div class="mt-2 grid items-center gap-3 md:grid-cols-4">
        <div class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
            <flux:icon.refresh-ccw class="inset mr-1 inline h-4 w-4 min-w-0" />
            {{ __('Value status') }}
        </div>

        <code class="wrap-anywhere col-span-3 text-xs">
            {{ $historyEvent->old_status ?: '—' }} → {{ $historyEvent->new_status ?: '—' }}
        </code>
    </div>
@endif

<x-admin.partials.translation-list.modal-history.timeline.shared.affected-usages
    :usages="$historyUsages"
    :is-snapshot="$historyUsagesAreSnapshot"
/>
