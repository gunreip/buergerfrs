{{-- resources/views/components/admin/partials/translation-list/modal-history/timeline/legacy-status-normalized.blade.php --}}

@props([
    'historyEvent',
    'historyContext' => [],
    'historyLocale' => null,
    'historyUsages' => null,
    'historyUsagesAreSnapshot' => false,
])

@php
    $classification = is_array($historyContext) ? $historyContext['classification'] ?? null : null;
    $workflowStatus = is_array($historyContext) ? $historyContext['workflow_status'] ?? null : null;
    $nativeText = $historyEvent->new_value ?? $historyEvent->old_value;
@endphp

<div class="mt-3 grid gap-3 md:grid-cols-4">
    <div class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
        <flux:icon.package class="inset mr-1 inline h-4 w-4 min-w-0" />
        {{ __('admin.translation_list.modal_history.reason') }}
    </div>

    <div class="wrap-anywhere col-span-3 text-sm">
        {{ __('The legacy non-key entry was removed from the obsolete bucket and restored to its classification status.') }}
    </div>
</div>

<div class="mt-3 space-y-2">
    <div class="grid items-center gap-3 md:grid-cols-4">
        <div class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
            <flux:icon.archive-x class="inset mr-1 inline h-4 w-4 min-w-0 text-red-400 dark:text-red-500" />
            {{ __('Status before') }}
        </div>

        <code class="wrap-anywhere col-span-3 text-xs text-red-400 dark:text-red-500">
            {{ $historyEvent->old_status ?: '—' }}
        </code>
    </div>

    <div class="grid items-center gap-3 md:grid-cols-4">
        <div class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
            <flux:icon.refresh-ccw class="inset mr-1 inline h-4 w-4 min-w-0 text-orange-400 dark:text-orange-500" />
            {{ __('Status after') }}
        </div>

        <code class="wrap-anywhere col-span-3 text-xs text-orange-500 dark:text-orange-400">
            {{ $historyEvent->new_status ?: '—' }}
        </code>
    </div>
</div>

@if ($classification || $workflowStatus)
    <div class="mt-2 grid items-start gap-3 md:grid-cols-4">
        <div class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
            <flux:icon.tag class="inset mr-1 inline h-4 w-4 min-w-0" />
            {{ __('Classification') }}

            @if ($workflowStatus)
                <div class="mt-1">
                    <flux:badge
                        size="sm"
                        variant="subtle"
                        color="zinc"
                    >
                        {{ __('ui.nouns.workflow') }}: {{ $workflowStatus }}
                    </flux:badge>
                </div>
            @endif
        </div>

        <div class="col-span-3">
            @if ($classification)
                <flux:badge
                    size="sm"
                    variant="subtle"
                    color="orange"
                >
                    {{ $classification }}
                </flux:badge>
            @endif
        </div>
    </div>
@endif

@if ($nativeText !== null && $nativeText !== '')
    <div class="mt-2 grid items-start gap-3 md:grid-cols-4">
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
