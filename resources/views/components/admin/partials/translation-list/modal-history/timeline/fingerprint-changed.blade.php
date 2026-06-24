{{-- resources/views/components/admin/partials/translation-list/modal-history/timeline/fingerprint-changed.blade.php --}}

@props([
    'historyEvent',
    'historyContext' => [],
    'historyLocale' => null,
    'historyUsages' => null,
    'historyUsagesAreSnapshot' => false,
])

@php
    $usageRaw = is_array($historyContext) ? $historyContext['raw'] ?? null : null;
    $usageFunction = is_array($historyContext) ? $historyContext['function'] ?? null : null;
    $usageClassification = is_array($historyContext) ? $historyContext['classification'] ?? null : null;

    $affectedUsages = collect($historyUsages)
        ->filter(
            static fn($historyUsage) => (int) $historyUsage->id === (int) $historyEvent->translation_usage_id,
        )
        ->values();

    if ($affectedUsages->isEmpty() && ($historyEvent->new_file || $historyEvent->old_file)) {
        $affectedUsages = collect([
            (object) [
                'file' => $historyEvent->new_file ?: $historyEvent->old_file,
                'line' => $historyEvent->new_line ?: $historyEvent->old_line,
                'function' => $usageFunction,
                'classification' => $usageClassification,
            ],
        ]);
    }

    $reasonText = match ($historyEvent->reason) {
        'same_usage_seen_with_different_fingerprint' => __(
            'The same translation usage was found again, but its fingerprint has changed.',
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
    <div class="grid items-center gap-3 md:grid-cols-4">
        <div class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
            <flux:icon.fingerprint-pattern class="inset mr-1 inline h-4 w-4 min-w-0 text-blue-300 dark:text-blue-600" />
            {{ __('Fingerprint before') }}
        </div>

        <code class="wrap-anywhere col-span-3 block text-xs text-blue-300 dark:text-blue-600">
            {{ $historyEvent->old_fingerprint ?: '—' }}
        </code>
    </div>

    <div class="grid items-center gap-3 md:grid-cols-4">
        <div class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
            <flux:icon.fingerprint-pattern
                class="inset mr-1 inline h-4 w-4 min-w-0 text-orange-300 dark:text-orange-600"
            />
            {{ __('Fingerprint after') }}
        </div>

        <code class="wrap-anywhere col-span-3 block text-xs text-orange-300 dark:text-orange-600">
            {{ $historyEvent->new_fingerprint ?: '—' }}
        </code>
    </div>
</div>

<x-admin.partials.translation-list.modal-history.timeline.shared.affected-usages
    :usages="$affectedUsages"
    :is-snapshot="$historyUsagesAreSnapshot"
/>

@if ($usageRaw || $usageFunction || $usageClassification)
    <div class="mt-2 grid gap-3 md:grid-cols-4">
        <div class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
            <flux:icon.language class="inset mr-1 inline h-4 w-4 min-w-0" />
            {{ __('Translation call') }}
        </div>

        <div class="col-span-3 min-w-0">
            @if ($usageRaw)
                <code class="wrap-anywhere block text-xs">
                    {{ $usageRaw }}
                </code>
            @endif
        </div>
    </div>
@endif
