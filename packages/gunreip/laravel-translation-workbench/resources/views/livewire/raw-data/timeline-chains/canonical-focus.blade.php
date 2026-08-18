{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/timeline-chains/canonical-focus.blade.php --}}

    <flux:callout
        class="mt-4"
        color="green"
        icon="git-merge"
    >
        <flux:callout.heading>
            <span class="inline-flex flex-wrap items-center gap-2">
                <span>{{ __('Canonical chain focus') }}</span>
                <flux:badge
                    size="sm"
                    color="green"
                >
                    #{{ $mainRow['id'] }}
                </flux:badge>
                <flux:badge
                    size="sm"
                    color="{{ match ($mainRow['chain_type']) {
                        'bulk' => 'purple',
                        'shared' => 'pink',
                        'moved' => 'amber',
                        default => 'zinc',
                    } }}"
                >
                    {{ str((string) $mainRow['chain_type'])->headline() }}
                </flux:badge>
            </span>
        </flux:callout.heading>
        <flux:callout.text>
            {{ __('Focused main branch candidate for reviewing how one active translation key absorbs related historical or shared entries.') }}
        </flux:callout.text>

        <div class="mt-4 grid gap-3 xl:grid-cols-4">
            <flux:callout
                color="green"
                icon="key-round"
            >
                <flux:callout.heading>
                    {{ __('Main branch') }}
                </flux:callout.heading>
                <flux:callout.text>
                    <div class="space-y-2">
                        <div class="wrap-anywhere text-wrap font-mono text-sm text-zinc-900 dark:text-zinc-100">
                            {{ $mainRow['translation_key'] }}
                        </div>
                        <div class="flex flex-wrap gap-1">
                            <flux:badge
                                size="sm"
                                color="green"
                            >
                                {{ __('Active findings') }}:
                                {{ number_format((int) $mainRow['active_finding_count']) }}
                            </flux:badge>
                            <flux:badge
                                size="sm"
                                color="emerald"
                            >
                                {{ __('Lang values') }}: {{ number_format((int) $mainRow['lang_value_count']) }}
                            </flux:badge>
                        </div>
                    </div>
                </flux:callout.text>
            </flux:callout>

            <flux:callout
                color="{{ $mainRow['obsolete_finding_count'] > 0 ? 'amber' : 'zinc' }}"
                icon="archive-x"
            >
                <flux:callout.heading>
                    {{ __('Historical branches') }}
                </flux:callout.heading>
                <flux:callout.text>
                    <div class="flex flex-wrap gap-1">
                        <flux:badge
                            size="sm"
                            color="sky"
                        >
                            {{ __('Findings') }}: {{ number_format((int) $mainRow['finding_count']) }}
                        </flux:badge>
                        <flux:badge
                            size="sm"
                            color="amber"
                        >
                            {{ __('Obsolete') }}: {{ number_format((int) $mainRow['obsolete_finding_count']) }}
                        </flux:badge>
                        @if ((int) $mainRow['commented_out_finding_count'] > 0)
                            <flux:badge
                                size="sm"
                                color="zinc"
                            >
                                {{ __('Commented out') }}:
                                {{ number_format((int) $mainRow['commented_out_finding_count']) }}
                            </flux:badge>
                        @endif
                    </div>
                </flux:callout.text>
            </flux:callout>

            <flux:callout
                color="violet"
                icon="badge-check"
            >
                <flux:callout.heading>
                    {{ __('Review context') }}
                </flux:callout.heading>
                <flux:callout.text>
                    <div class="flex flex-wrap gap-1">
                        <flux:badge
                            size="sm"
                            color="violet"
                        >
                            {{ __('Keys') }}: {{ number_format((int) $mainRow['key_count']) }}
                        </flux:badge>
                        <flux:badge
                            size="sm"
                            color="amber"
                        >
                            {{ __('Reviews') }}: {{ number_format((int) $mainRow['review_count']) }}
                        </flux:badge>
                        @if ((int) $mainRow['bulk_review_count'] > 0)
                            <flux:badge
                                size="sm"
                                color="purple"
                            >
                                {{ __('Bulk') }}: {{ number_format((int) $mainRow['bulk_review_count']) }}
                            </flux:badge>
                        @endif
                    </div>
                </flux:callout.text>
            </flux:callout>

            <flux:callout
                color="rose"
                icon="activity"
            >
                <flux:callout.heading>
                    {{ __('Timeline signal') }}
                </flux:callout.heading>
                <flux:callout.text>
                    <div class="space-y-2">
                        <flux:badge
                            size="sm"
                            color="rose"
                        >
                            {{ __('Events') }}: {{ number_format((int) $mainRow['timeline_event_count']) }}
                        </flux:badge>
                        <div class="flex flex-wrap gap-1">
                            @foreach ($mainEventSummary as $eventType => $eventCount)
                                <flux:badge
                                    size="sm"
                                    variant="subtle"
                                >
                                    {{ str((string) $eventType)->replace('_', ' ')->headline() }}:
                                    {{ number_format((int) $eventCount) }}
                                </flux:badge>
                            @endforeach
                        </div>
                    </div>
                </flux:callout.text>
            </flux:callout>
        </div>

        <div class="mt-3 grid gap-3 xl:grid-cols-2">
            <flux:callout
                color="{{ $mainRelatedKeys->isNotEmpty() ? 'cyan' : 'zinc' }}"
                icon="git-branch"
            >
                <flux:callout.heading>
                    {{ __('Merged or related keys') }}
                </flux:callout.heading>
                <flux:callout.text>
                    <div class="max-h-32 space-y-1 overflow-auto">
                        @forelse ($mainRelatedKeys as $relatedKey)
                            <div class="wrap-anywhere text-wrap font-mono text-xs text-zinc-700 dark:text-zinc-200">
                                {{ $relatedKey }}
                            </div>
                        @empty
                            <flux:text class="text-sm text-zinc-500">
                                {{ __('No related translation keys collected for this chain yet.') }}
                            </flux:text>
                        @endforelse
                    </div>
                </flux:callout.text>
            </flux:callout>

            <flux:callout
                color="{{ $mainLangValueSummary->isNotEmpty() ? 'emerald' : 'zinc' }}"
                icon="languages"
            >
                <flux:callout.heading>
                    {{ __('Lang value state') }}
                </flux:callout.heading>
                <flux:callout.text>
                    <div class="flex flex-wrap gap-1">
                        @forelse ($mainLangValueSummary as $locale => $statusCounts)
                            @php
                                $activeCount = (int) ($statusCounts['active'] ?? 0);
                                $obsoleteCount = (int) ($statusCounts['obsolete'] ?? 0);
                                $deletedCount = (int) ($statusCounts['deleted'] ?? 0);
                            @endphp
                            <flux:badge
                                size="sm"
                                color="{{ $activeCount > 0 ? 'green' : 'zinc' }}"
                            >
                                {{ $locale }}:
                                {{ __('Active') }} {{ number_format($activeCount) }}
                                @if ($obsoleteCount > 0)
                                    · {{ __('Obsolete') }} {{ number_format($obsoleteCount) }}
                                @endif
                                @if ($deletedCount > 0)
                                    · {{ __('Deleted') }} {{ number_format($deletedCount) }}
                                @endif
                            </flux:badge>
                        @empty
                            <flux:text class="text-sm text-zinc-500">
                                {{ __('No lang values collected for this chain yet.') }}
                            </flux:text>
                        @endforelse
                    </div>
                </flux:callout.text>
            </flux:callout>
        </div>
    </flux:callout>
