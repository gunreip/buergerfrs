{{-- resources/views/components/admin/partials/translation-list/modal-history/timeline/shared/affected-usages.blade.php --}}

@props(['usages', 'isSnapshot' => false])

@php
    $affectedUsages = collect($usages)->values();
    $usageFunctions = $affectedUsages->pluck('function')->filter()->unique()->values();
    $usageClassifications = $affectedUsages->pluck('classification')->filter()->unique()->values();
@endphp

@if ($affectedUsages->isNotEmpty())
    <div class="mt-2 grid gap-3 md:grid-cols-4">
        <div>
            <div class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                <flux:icon.map-pin class="inset mr-1 inline h-4 w-4 min-w-0" />
                {{ $isSnapshot ? __('Affected usages at this event') : __('Current affected usages') }}
            </div>

            <x-ui.tooltip.trigger
                :title="$isSnapshot ? __('Historical usage snapshot') : __('Incomplete historical usage data')"
                :text="$isSnapshot
                    ? __('These usage locations were stored with this audit event.')
                    : __(
                        'This older event has no historical usage snapshot. The locations shown are the current usage rows and may differ from the state at the time of the event.',
                    )"
            >
                <flux:badge
                    class="mt-1"
                    size="sm"
                    variant="subtle"
                    :color="$isSnapshot ? 'lime' : 'amber'"
                    :icon="$isSnapshot ? 'archive-box' : 'triangle-alert'"
                >
                    {{ $isSnapshot ? __('Historical snapshot') : __('Current data · history incomplete') }}
                </flux:badge>
            </x-ui.tooltip.trigger>

            @if ($usageFunctions->isNotEmpty() || $usageClassifications->isNotEmpty())
                <div class="mt-1 flex flex-wrap gap-1.5">
                    @foreach ($usageFunctions as $usageFunction)
                        <flux:badge
                            size="sm"
                            variant="subtle"
                            color="zinc"
                        >
                            {{ __('Function') }}: {{ $usageFunction }}
                        </flux:badge>
                    @endforeach

                    @foreach ($usageClassifications as $usageClassification)
                        <flux:badge
                            size="sm"
                            variant="subtle"
                            color="sky"
                        >
                            {{ __('Classification') }}: {{ $usageClassification }}
                        </flux:badge>
                    @endforeach
                </div>
            @endif
        </div>

        <details class="group col-span-3 -mt-1">
            <summary class="cursor-pointer text-sm font-semibold text-zinc-600 dark:text-zinc-300">
                <span class="inline-flex items-center gap-2">
                    {{ __('Show usage locations') }}

                    <flux:badge
                        size="sm"
                        variant="subtle"
                        color="zinc"
                    >
                        {{ $affectedUsages->count() }}
                    </flux:badge>
                </span>
            </summary>

            <div class="mt-2 max-h-48 space-y-2 overflow-y-auto pr-2">
                @foreach ($affectedUsages as $affectedUsage)
                    <div class="flex items-start gap-3 rounded-lg bg-zinc-100 p-2 dark:bg-zinc-900">
                        <code class="wrap-anywhere min-w-0 flex-1 text-xs">
                            {{ $affectedUsage->file ?: '—' }}
                        </code>

                        <code class="flex w-20 shrink-0 items-baseline justify-end gap-1 whitespace-nowrap text-xs">
                            <span>{{ __('Line') }}</span>
                            <span class="w-8 tabular-nums">
                                {{ $affectedUsage->line ?: '—' }}
                            </span>
                        </code>
                    </div>
                @endforeach
            </div>
        </details>
    </div>
@endif
