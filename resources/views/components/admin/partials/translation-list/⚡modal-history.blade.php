{{-- resources/views/components/admin/partials/translation-list/⚡modal-history.blade.php --}}

<flux:modal
    class="w-full max-w-6xl"
    name="translation-list-history"
    wire:model="translationHistoryModalOpen"
>
    @if ($historyTranslationKey)
        <div class="flex max-h-[calc(100vh-8rem)] flex-col gap-6 overflow-hidden">
            <div class="flex shrink-0 items-start justify-between gap-4">
                <x-ui.headers.card
                    :title="__('admin.translation_list.modal_history.translation_history')"
                    :description="__(
                        'admin.translation_list.modal_history.audit_events_and_change_history_for_the_selected_translation_key',
                    )"
                />

                <div class="mr-8 mt-2 flex items-center gap-2">
                    {{-- Badge with translation key ID --}}
                    <flux:badge
                        variant="subtle"
                        color="zinc"
                    >
                        #{{ $historyTranslationKey->id }}
                    </flux:badge>

                    @if ($nextHistoryTranslationKeyId !== null)
                        <x-ui.button.next-edit
                            wire:click="openNextTranslationHistoryFromList"
                            :aria-label="__('Open next translation history entry')"
                        />
                    @endif
                </div>
            </div>

            <flux:callout
                icon="key-round"
                stroke-width="1"
            >
                <flux:callout.heading>
                    {{ __('admin.translation_list.modal_edit.translation_key') }}
                </flux:callout.heading>

                <flux:text class="wrap-anywhere mt-2 font-mono text-sm">
                    {{ $historyTranslationKey->key ?: '—' }}
                </flux:text>
            </flux:callout>

            <flux:callout
                color="zinc"
                icon="history"
                stroke-width="1"
            >
                <div class="mb-4 flex items-center justify-between gap-3">
                    <div>
                        <flux:callout.heading>
                            {{ __('admin.translation_list.modal_history.events') }}
                        </flux:callout.heading>

                        <flux:callout.text>
                            {{ __('admin.translation_list.modal_history.latest_audit_events_for_this_translation_key') }}
                        </flux:callout.text>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        @if (! $historyHasDiscoveredEvent || $historyHasBackfilledBaseline)
                            <x-ui.tooltip.trigger
                                :title="__('Incomplete history')"
                                :text="$historyHasBackfilledBaseline
                                    ? __(
                                        'The beginning of this history was backfilled from the current translation record. Its timestamp is chronological, but historical field and usage details may be incomplete.',
                                    )
                                    : __(
                                        'This translation has no discovered baseline event yet. Run the discovered-event backfill to complete the beginning of its history.',
                                    )"
                            >
                                <flux:badge
                                    variant="subtle"
                                    color="amber"
                                    icon="triangle-alert"
                                >
                                    {{ __('Incomplete history') }}
                                </flux:badge>
                            </x-ui.tooltip.trigger>
                        @endif

                        <flux:badge
                            variant="subtle"
                            color="zinc"
                        >
                            {{ $historyEvents->count() }} / {{ $historyEventTotal }}
                        </flux:badge>
                    </div>
                </div>

                <flux:tab.group>
                    <flux:tabs
                        class="px-4"
                        {{-- variant="segmented" --}}
                    >
                        <flux:tab
                            name="details"
                            icon="list-chevrons-down-up"
                        >
                            {{ __('Details') }}
                        </flux:tab>

                        <flux:tab
                            name="timeline"
                            icon="clock-arrow-right"
                        >
                            {{ __('Timeline') }}
                        </flux:tab>
                    </flux:tabs>

                    {{-- Panel Tab Details with list of history events --}}
                    <flux:tab.panel name="details">
                        <x-admin.partials.translation-list.modal-history.details :history-events="$historyEvents" />
                    </flux:tab.panel>

                    {{-- Panel Tab Timeline with list of history events in timeline view --}}
                    <flux:tab.panel name="timeline">
                        <x-admin.partials.translation-list.modal-history.timeline
                            :history-events="$historyEvents"
                            :history-usages="$historyTranslationKey->usages"
                        />
                    </flux:tab.panel>
                </flux:tab.group>

                @if ($historyHasMoreEvents)
                    <div class="mt-3 flex justify-center">
                        <flux:button
                            type="button"
                            size="sm"
                            variant="ghost"
                            icon="history"
                            wire:click="loadOlderTranslationHistoryEvents"
                        >
                            {{ __('Load older events') }}
                        </flux:button>
                    </div>
                @endif

            </flux:callout>

            <div class="flex shrink-0 justify-end">
                <x-ui.button.cancel
                    label="{{ __('ui.close') }}"
                    icon="circle-x"
                    wire:click="closeTranslationHistory"
                />
            </div>
        </div>
    @endif
</flux:modal>
