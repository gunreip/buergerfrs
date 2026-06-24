{{-- resources/views/components/admin/partials/translation-list/modal-history/timeline/discovered.blade.php --}}

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
    $suggestedKey = is_array($historyContext) ? $historyContext['suggested_key'] ?? null : null;
    $firstSeenAt = is_array($historyContext) ? $historyContext['first_seen_at'] ?? null : null;
    $isDerived = is_array($historyContext) && ($historyContext['derived'] ?? false) === true;
    $isBackfilled = is_array($historyContext) && ($historyContext['backfilled'] ?? false) === true;

    $reasonText = match (true) {
        $isBackfilled => __(
            'This baseline was backfilled from the existing translation record and placed at its original first-seen time.',
        ),
        $isDerived => __(
            'This baseline was derived from the existing translation record because no earlier audit event exists.',
        ),
        default => __('The translation entry was discovered for the first time during an audit scan.'),
    };

    $originalSourceCalls = collect($historyUsages)
        ->map(static fn($historyUsage) => $historyUsage->original_raw ?: $historyUsage->raw)
        ->filter(static fn($sourceCall) => is_string($sourceCall) && trim($sourceCall) !== '')
        ->unique()
        ->values();
@endphp

<div class="mt-3 grid gap-3 md:grid-cols-4">
    <div class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
        <flux:icon.package class="inset mr-1 inline h-4 w-4 min-w-0" />
        {{ __('Reason') }}
    </div>

    <div class="wrap-anywhere col-span-3 text-sm">
        {{ $reasonText }}
    </div>
</div>

@if ($isBackfilled)
    <div class="mt-2 grid gap-3 md:grid-cols-4">
        <div></div>

        <div class="col-span-3">
            <x-ui.tooltip.trigger
                :title="__('Backfilled baseline')"
                :text="__(
                    'The timestamp reflects first_seen_at, but the stored field values and usages come from the record available during backfill and may not fully represent the original state.',
                )"
            >
                <flux:badge
                    size="sm"
                    variant="subtle"
                    color="amber"
                    icon="triangle-alert"
                >
                    {{ __('Backfilled baseline · historical details incomplete') }}
                </flux:badge>
            </x-ui.tooltip.trigger>
        </div>
    </div>
@endif

<div class="mt-2 grid items-center gap-3 md:grid-cols-4">
    <div class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
        <flux:icon.key class="inset mr-1 inline h-4 w-4 min-w-0" />
        {{ __('Translation key') }}
    </div>

    <div class="col-span-3">
        @if ($historyEvent->new_key)
            <code class="wrap-anywhere text-xs">
                {{ $historyEvent->new_key }}
            </code>
        @else
            <flux:badge
                size="sm"
                variant="subtle"
                color="amber"
            >
                {{ __('Not assigned yet') }}
            </flux:badge>
        @endif
    </div>
</div>

@if ($suggestedKey)
    <div class="mt-2 grid items-center gap-3 md:grid-cols-4">
        <div class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
            <flux:icon.key-round class="inset mr-1 inline h-4 w-4 min-w-0" />
            {{ __('Suggested key') }}
        </div>

        <code class="wrap-anywhere col-span-3 text-xs">
            {{ $suggestedKey }}
        </code>
    </div>
@endif

@if ($originalSourceCalls->isNotEmpty())
    <div class="mt-2 grid items-start gap-3 md:grid-cols-4">
        <div class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
            <flux:icon.code-xml class="inset mr-1 inline h-4 w-4 min-w-0" />
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
    <div class="mt-2 grid items-start gap-3 md:grid-cols-4">
        <div class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
            <flux:icon.language class="inset mr-1 inline h-4 w-4 min-w-0" />
            {{ __('Native text') }}
        </div>

        <div class="wrap-anywhere col-span-3 text-sm">
            {{ $historyEvent->new_value }}
        </div>
    </div>
@endif

<div class="mt-2 grid items-start gap-3 md:grid-cols-4">
    <div class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
        <flux:icon.tag class="inset mr-1 inline h-4 w-4 min-w-0" />
        {{ __('Initial state') }}
    </div>

    <div class="col-span-3 flex flex-wrap gap-1.5">
        @if ($classification)
            <flux:badge
                size="sm"
                variant="subtle"
                color="indigo"
            >
                {{ __('Classification') }}: {{ $classification }}
            </flux:badge>
        @endif

        @if ($historyEvent->new_status)
            <flux:badge
                size="sm"
                variant="subtle"
                color="zinc"
            >
                {{ __('Status') }}: {{ $historyEvent->new_status }}
            </flux:badge>
        @endif

        @if ($workflowStatus)
            <flux:badge
                size="sm"
                variant="subtle"
                color="zinc"
            >
                {{ __('Workflow') }}: {{ $workflowStatus }}
            </flux:badge>
        @endif
    </div>
</div>

@if ($firstSeenAt)
    <div class="mt-2 grid items-center gap-3 md:grid-cols-4">
        <div class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
            <flux:icon.history class="inset mr-1 inline h-4 w-4 min-w-0" />
            {{ __('First seen') }}
        </div>

        <div class="col-span-3 flex flex-wrap items-center gap-2">
            <x-ui.date-time.date
                class="text-sm"
                :value="$firstSeenAt"
            />

            <x-ui.date-time.time
                class="text-sm text-zinc-500 dark:text-zinc-400"
                :value="$firstSeenAt"
            />
        </div>
    </div>
@endif

<x-admin.partials.translation-list.modal-history.timeline.shared.affected-usages
    :usages="$historyUsages"
    :is-snapshot="$historyUsagesAreSnapshot"
/>
