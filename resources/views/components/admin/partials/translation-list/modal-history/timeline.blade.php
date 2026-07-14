{{-- resources/views/components/admin/partials/translation-list/modal-history/⚡timeline.blade.php --}}

@props(['historyEvents', 'historyUsages' => null])

{{-- <div class="max-h-104 overflow-y-auto pr-2"> --}}
<div class="max-h-104 -mr-4 space-y-3 overflow-y-auto pr-4">

    @if ($historyEvents->isEmpty())
        <div
            class="rounded-xl border border-dashed border-zinc-300 p-6 text-center text-sm text-zinc-500 dark:border-zinc-700 dark:text-zinc-400">
            {{ __('admin.translation_list.modal_history.no_history_events_available_for_this_translation_key') }}
        </div>
    @else
        <flux:timeline
            class="overflow-x-auto"
            size="lg"
        >
            @foreach ($historyEvents as $historyEvent)
                @php
                    $historyContext = $historyEvent->context ? json_decode($historyEvent->context, true) : [];

                    $historyLocale = is_array($historyContext) ? $historyContext['locale'] ?? null : null;
                    $isDerivedEvent = is_array($historyContext) && ($historyContext['derived'] ?? false) === true;
                    $isBackfilledEvent = is_array($historyContext) && ($historyContext['backfilled'] ?? false) === true;
                    $hasAffectedUsageSnapshot = is_array($historyContext) &&
                        array_key_exists('affected_usages', $historyContext);
                    $eventHistoryUsages = $hasAffectedUsageSnapshot
                        ? collect($historyContext['affected_usages'])->map(
                            static fn($usage): object => (object) $usage,
                        )
                        : collect($historyUsages);
                    $historyUsagesAreSnapshot = $hasAffectedUsageSnapshot &&
                        ($historyContext['affected_usages_snapshot_complete'] ?? false) === true;

                    $eventColor = match ($historyEvent->event_type) {
                        'created' => 'green',
                        'value_changed' => 'cyan',
                        'key_changed' => 'pink',
                        'moved' => 'amber',
                        'stale_marked' => 'red',
                        'reactivated' => 'lime',
                        'fingerprint_changed' => 'sky',
                        'manual_needs_new_key_changed' => 'purple',
                        'workflow_status_changed' => 'violet',
                        'native_text_filled' => 'teal',
                        'native_text_changed' => 'blue',
                        'legacy_status_normalized' => 'orange',
                        'discovered' => 'indigo',
                        default => 'zinc',
                    };

                    $eventTitle = match ($historyEvent->event_type) {
                        'created' => __('admin.client_list.table.created'),
                        'value_changed' => __('Value changed'),
                        'key_changed' => __('Key changed'),
                        'moved' => __('Moved'),
                        'stale_marked' => __('Marked stale'),
                        'reactivated' => __('Reactivated'),
                        'fingerprint_changed' => __('Fingerprint changed'),
                        'manual_needs_new_key_changed' => __('Needs new key changed'),
                        'workflow_status_changed' => __('Workflow status changed'),
                        'native_text_filled' => __('Native text filled'),
                        'native_text_changed' => __('Native text changed'),
                        'legacy_status_normalized' => __('Legacy status normalized'),
                        'discovered' => __('Discovered'),
                        default => \Illuminate\Support\Str::of((string) $historyEvent->event_type)
                            ->replace('_', ' ')
                            ->title()
                            ->toString(),
                    };

                    $eventDescription = match ($historyEvent->event_type) {
                        'created' => __('A new translation value was created for this translation key.'),
                        'value_changed' => __('The translated text for this locale was changed.'),
                        'key_changed' => match ($historyEvent->reason) {
                            'suggested_key_applied_from_review_modal' => __(
                                'The suggested key was accepted during the review and adopted as the translation key.',
                            ),
                            default => __('The translation was assigned a different translation key.'),
                        },
                        'moved' => __('The translation usage was found at a different source-code location.'),
                        'stale_marked' => match ($historyEvent->reason) {
                            'key_not_seen_in_latest_audit_sync' => __(
                                'The translation key was no longer found during the latest scan and was marked as obsolete.',
                            ),
                            default => __(
                                'The translation usage was no longer found during the latest scan and was marked as stale.',
                            ),
                        },
                        'reactivated' => __(
                            'A previously stale or obsolete translation entry was found again and reactivated.',
                        ),
                        'fingerprint_changed' => __(
                            'The same translation usage was detected with a different fingerprint.',
                        ),
                        'manual_needs_new_key_changed' => match ($historyEvent->reason) {
                            'manual_needs_new_key_marked_from_translation_list' => __(
                                'The translation was manually marked as requiring a new translation key.',
                            ),
                            'manual_needs_new_key_resolved_from_translation_list' => __(
                                'The manual Needs-New-Key marker was resolved for this translation.',
                            ),
                            default => __('The manual Needs-New-Key state was changed.'),
                        },
                        'workflow_status_changed' => __(
                            'The review workflow status of the translation entry was changed.',
                        ),
                        'native_text_filled' => __(
                            'A previously empty native text was populated from the audit source.',
                        ),
                        'native_text_changed' => __(
                            'The native text detected in the audit source has changed.',
                        ),
                        'legacy_status_normalized' => __(
                            'A legacy non-key entry was moved from the obsolete state to its appropriate classification status.',
                        ),
                        'discovered' => match (true) {
                            $isBackfilledEvent => __(
                                'This initial state was backfilled from the existing translation record.',
                            ),
                            $isDerivedEvent => __(
                                'This initial state was derived from the existing translation record.',
                            ),
                            default => __(
                                'The translation entry was discovered for the first time during an audit scan.',
                            ),
                        },
                        default => __(
                            'This timeline entry records a change to the selected translation key.',
                        ),
                    };

                    $timelineBodyComponentName = \Illuminate\Support\Str::of((string) $historyEvent->event_type)
                        ->replace('_', '-')
                        ->toString();

                    $timelineBodyView =
                        'components.admin.partials.translation-list.modal-history.timeline.' .
                        $timelineBodyComponentName;

                    $usesTimelineFallback = !view()->exists($timelineBodyView);

                    $timelineBodyComponent = $usesTimelineFallback
                        ? 'admin.partials.translation-list.modal-history.timeline.fallback'
                        : 'admin.partials.translation-list.modal-history.timeline.' . $timelineBodyComponentName;

                    $eventTechnicalTooltip = collect([
                        $historyEvent->id
                            ? __('admin.translation_list.modal_history.event') . ' #' . $historyEvent->id
                            : __('Derived baseline'),
                        __('Entity') . ': ' . $historyEvent->entity_type,
                        __('admin.client_list.table.type') . ': ' . $historyEvent->event_type,
                        __('Renderer') . ': ' . ($usesTimelineFallback ? __('Fallback') : __('Specific component')),
                        $historyLocale ? __('Locale') . ': ' . strtoupper($historyLocale) : null,
                        $historyEvent->reason ? __('admin.translation_list.modal_history.reason') . ': ' . $historyEvent->reason : null,
                    ])
                        ->filter()
                        ->implode(' · ');
                @endphp

                <flux:timeline.item>
                    <flux:timeline.indicator>
                        <span class="text-xs font-semibold tabular-nums">
                            {{ $historyEvents->count() - $loop->iteration }}
                        </span>
                    </flux:timeline.indicator>

                    <flux:timeline.content>
                        <flux:callout>
                            <div class="grid items-center gap-3 md:grid-cols-12">
                                <div class="col-span-3 flex flex-wrap items-center gap-2">
                                    <x-ui.tooltip.trigger
                                        :title="$eventTitle"
                                        :text="$eventDescription"
                                    >
                                        <flux:badge
                                            size="sm"
                                            inset
                                            variant="solid"
                                            :color="$eventColor"
                                        >
                                            {{ $eventTitle }}
                                        </flux:badge>
                                    </x-ui.tooltip.trigger>

                                    @if ($historyLocale)
                                        <flux:badge
                                            class="ml-1"
                                            size="sm"
                                            variant="subtle"
                                            color="indigo"
                                        >
                                            <span class="inline-flex items-center gap-1.5">
                                                <x-ui.locale.flag
                                                    :locale="$historyLocale"
                                                    size="sm"
                                                />

                                                <span class="font-mono uppercase">
                                                    {{ $historyLocale }}
                                                </span>
                                            </span>
                                        </flux:badge>
                                    @endif

                                    @if (! $historyUsagesAreSnapshot)
                                        <x-ui.tooltip.trigger
                                            :title="__('Usage history incomplete')"
                                            :text="__(
                                                'This event predates historical usage snapshots. Where usage locations are shown, they come from the current translation record and may differ from the event-time state.',
                                            )"
                                        >
                                            <flux:badge
                                                size="sm"
                                                variant="subtle"
                                                color="amber"
                                                icon="triangle-alert"
                                            >
                                                {{ __('Usage snapshot unavailable') }}
                                            </flux:badge>
                                        </x-ui.tooltip.trigger>
                                    @endif

                                    @if ($usesTimelineFallback)
                                        <x-ui.tooltip.trigger
                                            :title="__('Fallback renderer')"
                                            :text="__(
                                                'No dedicated timeline component exists for this event type yet.',
                                            ) .
                                                ' ' .
                                                __('Event type') .
                                                ': ' .
                                                $historyEvent->event_type"
                                        >
                                            <flux:badge
                                                class="ml-1"
                                                size="sm"
                                                variant="solid"
                                                color="red"
                                            >
                                                {{ __('Fallback') }}
                                            </flux:badge>
                                        </x-ui.tooltip.trigger>
                                    @endif
                                </div>

                                <div class="col-span-6">
                                    <x-ui.date-time.date
                                        class="mr-2 text-sm"
                                        :value="$historyEvent->created_at"
                                    />

                                    <x-ui.date-time.time
                                        class="mr-2 text-sm text-zinc-500 dark:text-zinc-400"
                                        :value="$historyEvent->created_at"
                                    />

                                    <x-ui.date-time.ago
                                        class="text-sm text-zinc-500 dark:text-zinc-400"
                                        :value="$historyEvent->created_at"
                                    />
                                </div>

                                <div class="col-span-2 text-right text-sm text-zinc-500 dark:text-zinc-400">
                                    @if ($historyEvent->id)
                                        {{ __('admin.user_list.table.id') }}
                                        #{{ $historyEvent->id }}
                                    @else
                                        {{ __('Derived') }}
                                    @endif
                                </div>

                                <span class="col-span-1 flex justify-end">
                                    <x-ui.tooltip.trigger
                                        :title="__('Technical event metadata')"
                                        :text="$eventTechnicalTooltip"
                                    >
                                        <flux:icon.information-circle
                                            class="h-4 w-4 text-zinc-400 dark:text-zinc-500" />
                                    </x-ui.tooltip.trigger>
                                </span>
                            </div>

                            <x-dynamic-component
                                :component="$timelineBodyComponent"
                                :history-event="$historyEvent"
                                :history-context="$historyContext"
                                :history-locale="$historyLocale"
                                :history-usages="$eventHistoryUsages"
                                :history-usages-are-snapshot="$historyUsagesAreSnapshot"
                            />
                        </flux:callout>
                    </flux:timeline.content>
                </flux:timeline.item>
            @endforeach
        </flux:timeline>
    @endif
</div>
