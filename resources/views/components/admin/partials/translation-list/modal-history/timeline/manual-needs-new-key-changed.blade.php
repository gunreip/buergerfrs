{{-- resources/views/components/admin/partials/translation-list/modal-history/timeline/manual-needs-new-key-changed.blade.php --}}

@props([
    'historyEvent',
    'historyContext' => [],
    'historyLocale' => null,
    'historyUsages' => null,
    'historyUsagesAreSnapshot' => false,
])

@php
    $wasActive = is_array($historyContext)
        ? ($historyContext['manual_needs_new_key_old_active'] ?? ($historyEvent->old_value === 'active'))
        : $historyEvent->old_value === 'active';
    $isActive = is_array($historyContext)
        ? ($historyContext['manual_needs_new_key_new_active'] ?? ($historyEvent->new_value === 'active'))
        : $historyEvent->new_value === 'active';
    $markerNote = is_array($historyContext) ? $historyContext['needs_new_key_note'] ?? null : null;

    $reasonText = match ($historyEvent->reason) {
        'manual_needs_new_key_marked_from_translation_list' => __(
            'The translation was manually marked as requiring a new translation key.',
        ),
        'manual_needs_new_key_resolved_from_translation_list' => __(
            'The manual Needs-New-Key marker was resolved for this translation.',
        ),
        null, '' => null,
        default => \Illuminate\Support\Str::of($historyEvent->reason)
            ->replace('_', ' ')
            ->ucfirst()
            ->append('.')
            ->toString(),
    };

    $noteText = match ($markerNote) {
        'marked_manually_from_translation_list' => __('Marked manually from the translation list.'),
        null, '' => null,
        default => \Illuminate\Support\Str::of($markerNote)
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
            <flux:icon.key class="inset mr-1 inline h-4 w-4 min-w-0" />
            {{ __('Marker before') }}
        </div>

        <code
            @class([
                'wrap-anywhere col-span-3 block text-xs',
                'text-amber-500 dark:text-amber-400' => $wasActive,
                'text-zinc-500 dark:text-zinc-400' => ! $wasActive,
            ])
        >
            {{ $wasActive ? __('Needs new key') : __('Not marked') }}
        </code>
    </div>

    <div class="grid items-center gap-3 md:grid-cols-4">
        <div class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
            <flux:icon.key class="inset mr-1 inline h-4 w-4 min-w-0" />
            {{ __('Marker after') }}
        </div>

        <code
            @class([
                'wrap-anywhere col-span-3 block text-xs',
                'text-amber-500 dark:text-amber-400' => $isActive,
                'text-lime-600 dark:text-lime-400' => ! $isActive,
            ])
        >
            {{ $isActive ? __('Needs new key') : __('Resolved') }}
        </code>
    </div>
</div>

@if ($noteText)
    <div class="mt-2 grid items-center gap-3 md:grid-cols-4">
        <div class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
            <flux:icon.document-text class="inset mr-1 inline h-4 w-4 min-w-0" />
            {{ __('Marker note') }}
        </div>

        <div class="wrap-anywhere col-span-3 text-sm">
            {{ $noteText }}
        </div>
    </div>
@endif

<x-admin.partials.translation-list.modal-history.timeline.shared.affected-usages
    :usages="$historyUsages"
    :is-snapshot="$historyUsagesAreSnapshot"
/>
