{{-- resources/views/components/admin/partials/translation-list/modal-history/timeline/workflow-status-changed.blade.php --}}

@props([
    'historyEvent',
    'historyContext' => [],
    'historyLocale' => null,
    'historyUsages' => null,
    'historyUsagesAreSnapshot' => false,
])

@php
    $oldWorkflowStatus = is_array($historyContext)
        ? $historyContext['old_workflow_status'] ?? null
        : null;
    $newWorkflowStatus = is_array($historyContext)
        ? $historyContext['new_workflow_status'] ?? null
        : null;
    $reviewedAt = is_array($historyContext) ? $historyContext['reviewed_at'] ?? null : null;
    $reviewedByUserId = is_array($historyContext) ? $historyContext['reviewed_by_user_id'] ?? null : null;
    $supersedingKey = is_array($historyContext) ? $historyContext['superseding_key'] ?? null : null;
    $supersedingKeyId = is_array($historyContext)
        ? $historyContext['superseding_translation_key_id'] ?? null
        : null;

    $reasonText = match ($historyEvent->reason) {
        'obsolete_marked_reviewed_from_tooltip' => __(
            'The obsolete translation entry was manually marked as reviewed.',
        ),
        'native_row_superseded_by_key_sync' => __(
            'The native entry was superseded by a matching keyed translation during the audit sync.',
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
            <flux:icon.history class="inset mr-1 inline h-4 w-4 min-w-0 text-blue-300 dark:text-blue-600" />
            {{ __('Workflow status before') }}
        </div>

        <code class="wrap-anywhere col-span-3 text-xs text-blue-300 dark:text-blue-600">
            {{ $oldWorkflowStatus ?: '—' }}
        </code>
    </div>

    <div class="grid items-center gap-3 md:grid-cols-4">
        <div class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
            <flux:icon.package-check class="inset mr-1 inline h-4 w-4 min-w-0 text-lime-500 dark:text-lime-400" />
            {{ __('Workflow status after') }}
        </div>

        <code class="wrap-anywhere col-span-3 text-xs text-lime-600 dark:text-lime-400">
            {{ $newWorkflowStatus ?: '—' }}
        </code>
    </div>
</div>

@if ($supersedingKey)
    <div class="mt-2 grid items-center gap-3 md:grid-cols-4">
        <div class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
            <flux:icon.key class="inset mr-1 inline h-4 w-4 min-w-0" />
            {{ __('Superseding translation key') }}
        </div>

        <div class="col-span-3 flex min-w-0 items-center gap-2">
            <code class="wrap-anywhere min-w-0 text-xs">
                {{ $supersedingKey }}
            </code>

            @if ($supersedingKeyId)
                <flux:badge
                    size="sm"
                    variant="subtle"
                    color="zinc"
                >
                    #{{ $supersedingKeyId }}
                </flux:badge>
            @endif
        </div>
    </div>
@endif

@if ($reviewedAt || $reviewedByUserId)
    <div class="mt-2 grid items-center gap-3 md:grid-cols-4">
        <div class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
            <flux:icon.circle-user-round class="inset mr-1 inline h-4 w-4 min-w-0" />
            {{ __('admin.translation_list.modal_edit.reviewed') }}
        </div>

        <div class="col-span-3 flex flex-wrap items-center gap-2">
            @if ($reviewedAt)
                <x-ui.date-time.date
                    class="text-sm"
                    :value="$reviewedAt"
                />

                <x-ui.date-time.time
                    class="text-sm text-zinc-500 dark:text-zinc-400"
                    :value="$reviewedAt"
                />
            @endif

            @if ($reviewedByUserId)
                <flux:badge
                    size="sm"
                    variant="subtle"
                    color="zinc"
                >
                    {{ __('admin.user_list.meta.user') }} #{{ $reviewedByUserId }}
                </flux:badge>
            @endif
        </div>
    </div>
@endif

<x-admin.partials.translation-list.modal-history.timeline.shared.affected-usages
    :usages="$historyUsages"
    :is-snapshot="$historyUsagesAreSnapshot"
/>
