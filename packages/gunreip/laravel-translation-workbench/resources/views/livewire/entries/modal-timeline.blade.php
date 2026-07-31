{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/modal-timeline.blade.php --}}

<flux:modal
    class="max-w-[calc(90vw-2rem)] overflow-hidden lg:w-[calc(90vw-4rem)] lg:max-w-[calc(90vw-4rem)]"
    name="translation-workbench-finding-timeline"
    wire:model="timelineModalOpen"
>
    <div class="flex h-[calc(80vh-2rem)] flex-col gap-4 overflow-hidden lg:h-[calc(80vh-4rem)]">
        <div class="flex shrink-0 items-start justify-between gap-4">
            <x-ui.headers.card
                :title="__('ui.time.timeline')"
                :description="__('The latest workflow events for the selected finding and its linked key.')"
            />

            @if ($timelineFinding)
                <div class="mr-8 flex shrink-0 flex-wrap items-center justify-end gap-1.5">
                    <flux:badge
                        class="tabular-nums"
                        color="sky"
                        variant="subtle"
                    >
                        F#{{ $timelineFinding->id }}
                    </flux:badge>

                    @if ($timelineFinding->key_id)
                        <flux:badge
                            class="tabular-nums"
                            color="sky"
                            variant="subtle"
                        >
                            K#{{ $timelineFinding->key_id }}
                        </flux:badge>
                    @endif
                </div>
            @endif
        </div>

        @if ($timelineFinding)
            @if (count($timelineRows) === 0)
                <flux:callout
                    color="zinc"
                    icon="circle-minus"
                >
                    <flux:callout.heading>{{ __('No timeline events') }}</flux:callout.heading>
                    <flux:callout.text>
                        {{ __('This finding and its linked key do not have timeline events yet.') }}
                    </flux:callout.text>
                </flux:callout>
            @else
                <div class="flex min-h-0 w-full flex-1 flex-col">
                    <div class="mb-3 flex shrink-0 gap-3">
                        <div
                            class="w-8 shrink-0"
                            aria-hidden="true"
                        ></div>

                        <div class="grid min-w-0 flex-1 grid-cols-1 gap-3 lg:grid-cols-5">
                            <flux:callout
                                class="flex items-center justify-between gap-2"
                                color="sky"
                            >
                                <flux:callout.heading>
                                    <span class="inline-flex items-center gap-1.5">
                                        {{ __('Event') }}
                                        <x-ui.tooltip.simple
                                            :title="__('Timeline event')"
                                            :text="__(
                                                'Shows what happened and when it happened. The time below each event includes the exact timestamp and a relative time value.',
                                            )"
                                        />
                                    </span>

                                    <flux:badge
                                        class="ml-auto shrink-0 tabular-nums"
                                        size="sm"
                                    >
                                        {{ number_format(max(count($timelineRows), 0)) }}
                                    </flux:badge>
                                </flux:callout.heading>
                            </flux:callout>

                            <div class="col-span-3 min-w-0">
                                <flux:callout
                                    class="flex items-center gap-1.5"
                                    color="red"
                                >
                                    <flux:callout.heading>
                                        {{ __('Changed values') }}
                                        <x-ui.tooltip.simple
                                            :title="__('Changed values')"
                                            :text="__(
                                                'Lists the relevant values changed by this event as field, previous value and new value. Technical timestamp-only changes are intentionally hidden here.',
                                            )"
                                        />
                                    </flux:callout.heading>
                                </flux:callout>
                            </div>

                            <div class="min-w-0">
                                <flux:callout
                                    class="flex items-center gap-1.5"
                                    color="green"
                                >
                                    <flux:callout.heading>
                                        {{ __('Origin') }}
                                        <x-ui.tooltip.simple
                                            :title="__('Origin and context')"
                                            :text="__(
                                                'Shows whether the event came from scanner/system processing or UI editing, plus short source context when available.',
                                            )"
                                        />
                                    </flux:callout.heading>
                                </flux:callout>
                            </div>
                        </div>
                    </div>

                    <div class="max-h-[calc(80vh-2rem)] min-h-0 flex-1 overflow-y-auto pr-1 lg:max-h-[calc(80vh-4rem)]">
                        <flux:timeline
                            class="w-full"
                            align="start"
                        >
                            @foreach ($timelineRows as $event)
                                <flux:timeline.item class="w-full">
                                    <flux:timeline.indicator color="{{ $event['color'] }}">
                                        <flux:icon
                                            name="{{ $event['origin_icon'] }}"
                                            variant="micro"
                                        />
                                    </flux:timeline.indicator>

                                    <flux:timeline.content class="w-full min-w-0">
                                        <div class="grid grid-cols-1 gap-3 lg:grid-cols-5">
                                            <flux:callout
                                                class="min-w-0"
                                                color="sky"
                                            >
                                                <div class="flex items-start justify-between gap-2">
                                                    <flux:heading
                                                        class="flex min-w-0 flex-wrap items-baseline gap-x-2 gap-y-1"
                                                    >
                                                        <span>{{ $event['label'] }}</span>
                                                    </flux:heading>

                                                    <flux:badge
                                                        class="shrink-0 tabular-nums"
                                                        color="zinc"
                                                        size="sm"
                                                        variant="subtle"
                                                    >
                                                        {{ count($timelineRows) - $loop->index - 1 }}
                                                    </flux:badge>
                                                </div>

                                                <flux:text class="text-xs text-zinc-500 dark:text-zinc-400">
                                                    <x-ui.date-time.date-time
                                                        format="ddd, DD.MMM.YYYY, HH:mm:ss"
                                                        :value="$event['created_at']"
                                                        size="xs"
                                                        color="muted"
                                                    />
                                                    · <x-ui.date-time.ago
                                                        color="text-blue-600 dark:text-blue-400"
                                                        :value="$event['created_at']"
                                                        size="xs"
                                                    />

                                                    @if ($event['created_by'])
                                                        <div class="text-xs text-amber-600">{{ $event['created_by'] }}
                                                        </div>
                                                    @endif
                                                </flux:text>
                                            </flux:callout>

                                            <flux:callout
                                                class="col-span-3 min-w-0 space-y-1"
                                                color="red"
                                            >
                                                @forelse ($event['change_rows'] as $change)
                                                    <div class="grid grid-cols-10 gap-1 text-xs">
                                                        <div
                                                            class="{{ $change['key_class'] }}">
                                                            {{ $change['label'] }}
                                                        </div>
                                                        @if ($change['old_title'])
                                                            <x-ui.tooltip.simple
                                                                class="{{ $change['old_class'] }}"
                                                                :title="__('ui.time.timeline-value-context')"
                                                                :text="$change['old_title']"
                                                            >
                                                                {{ $change['old'] }}
                                                            </x-ui.tooltip.simple>
                                                        @else
                                                            <div class="{{ $change['old_class'] }}">
                                                                {{ $change['old'] }}
                                                            </div>
                                                        @endif
                                                        <div class="col-span-1 text-center text-zinc-400">
                                                            {{-- → --}}
                                                            <flux:icon.arrow-right
                                                                class="h-4 w-4"
                                                                color="zinc"
                                                            />
                                                        </div>
                                                        @if ($change['new_title'])
                                                            <x-ui.tooltip.simple
                                                                class="{{ $change['new_class'] }}"
                                                                :title="__('ui.time.timeline-value-context')"
                                                                :text="$change['new_title']"
                                                            >
                                                                {{ $change['new'] }}
                                                            </x-ui.tooltip.simple>
                                                        @else
                                                            <div class="{{ $change['new_class'] }}">
                                                                {{ $change['new'] }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                @empty
                                                    <flux:text class="text-xs text-zinc-400">
                                                        {{ __('No primary value changes') }}
                                                    </flux:text>
                                                @endforelse

                                                @if ($event['relationship_count'] > 0)
                                                    <div
                                                        class="mt-2 space-y-1 border-t border-sky-200/70 pt-2 dark:border-sky-500/20">
                                                        <div class="flex items-center gap-1.5">
                                                            <flux:text
                                                                class="text-xs font-semibold text-sky-700 dark:text-sky-300">
                                                                {{ __('Relations') }}
                                                            </flux:text>

                                                            <x-ui.tooltip.simple
                                                                :title="__('IDs and relations')"
                                                                :text="__(
                                                                    'Shows identifier and relation fields carried by this event, for example finding IDs, key IDs, review IDs, translation keys or language keys. These values help track how records are connected or how those links changed.',
                                                                )"
                                                            />
                                                        </div>

                                                        @foreach ($event['relationship_rows'] as $change)
                                                            <div class="grid grid-cols-10 gap-1 text-xs">
                                                                <div class="{{ $change['key_class'] }}">
                                                                    {{ $change['label'] }}
                                                                </div>

                                                                @if ($change['old_title'])
                                                                    <x-ui.tooltip.simple
                                                                        class="{{ $change['old_class'] }}"
                                                                        :title="__('ui.time.timeline-relation-context')"
                                                                        :text="$change['old_title']"
                                                                    >
                                                                        {{ $change['old'] }}
                                                                    </x-ui.tooltip.simple>
                                                                @else
                                                                    <div class="{{ $change['old_class'] }}">
                                                                        {{ $change['old'] }}
                                                                    </div>
                                                                @endif

                                                                <div class="col-span-1 text-center text-sky-400">
                                                                    <flux:icon.arrow-right
                                                                        class="h-4 w-4"
                                                                        color="sky"
                                                                    />
                                                                </div>

                                                                @if ($change['new_title'])
                                                                    <x-ui.tooltip.simple
                                                                        class="{{ $change['new_class'] }}"
                                                                        :title="__('ui.time.timeline-relation-context')"
                                                                        :text="$change['new_title']"
                                                                    >
                                                                        {{ $change['new'] }}
                                                                    </x-ui.tooltip.simple>
                                                                @else
                                                                    <div class="{{ $change['new_class'] }}">
                                                                        {{ $change['new'] }}
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif

                                                @if ($event['hidden_change_count'] > 0)
                                                    <flux:accordion>
                                                        <flux:accordion.item>
                                                            <flux:accordion.heading>
                                                                {{ __('+:count more changes', ['count' => $event['hidden_change_count']]) }}
                                                            </flux:accordion.heading>

                                                            <flux:accordion.content>
                                                                <div class="space-y-1">
                                                                    @foreach ($event['hidden_change_rows'] as $change)
                                                                        <div class="grid grid-cols-10 gap-1 text-xs">
                                                                            <div
                                                                                class="{{ $change['key_class'] }}">
                                                                                {{ $change['label'] }}
                                                                            </div>
                                                                            @if ($change['old_title'])
                                                                                <x-ui.tooltip.simple
                                                                                    class="{{ $change['old_class'] }}"
                                                                                    :title="__('ui.time.timeline-value-context')"
                                                                                    :text="$change['old_title']"
                                                                                >
                                                                                    {{ $change['old'] }}
                                                                                </x-ui.tooltip.simple>
                                                                            @else
                                                                                <div class="{{ $change['old_class'] }}">
                                                                                    {{ $change['old'] }}
                                                                                </div>
                                                                            @endif
                                                                            <div
                                                                                class="col-span-1 text-center text-zinc-400">
                                                                                <flux:icon.arrow-right
                                                                                    class="h-4 w-4"
                                                                                    color="zinc"
                                                                                />
                                                                            </div>
                                                                            @if ($change['new_title'])
                                                                                <x-ui.tooltip.simple
                                                                                    class="{{ $change['new_class'] }}"
                                                                                    :title="__('ui.time.timeline-value-context')"
                                                                                    :text="$change['new_title']"
                                                                                >
                                                                                    {{ $change['new'] }}
                                                                                </x-ui.tooltip.simple>
                                                                            @else
                                                                                <div class="{{ $change['new_class'] }}">
                                                                                    {{ $change['new'] }}
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </flux:accordion.content>
                                                        </flux:accordion.item>
                                                    </flux:accordion>
                                                @endif
                                            </flux:callout>

                                            <flux:callout
                                                class="col-span-1 min-w-0 space-y-1 text-xs"
                                                color="green"
                                            >
                                                <div class="flex flex-wrap items-center gap-1.5">
                                                    <flux:badge
                                                        color="{{ $event['color'] }}"
                                                        variant="subtle"
                                                    >
                                                        {{ $event['origin_label'] }}
                                                    </flux:badge>

                                                    <flux:badge
                                                        color="zinc"
                                                        variant="subtle"
                                                    >
                                                        {{ $event['category'] }}
                                                    </flux:badge>
                                                </div>

                                                @if ($event['context_label'])
                                                    <div
                                                        class="wrap-anywhere font-mono text-zinc-500 dark:text-zinc-400">
                                                        {{ $event['context_label'] }}
                                                    </div>
                                                @endif
                                            </flux:callout>
                                        </div>
                                    </flux:timeline.content>
                                </flux:timeline.item>
                            @endforeach
                        </flux:timeline>
                    </div>
                </div>
            @endif
        @else
            <flux:text class="text-sm text-zinc-500">
                {{ __('No finding selected.') }}
            </flux:text>
        @endif
    </div>
</flux:modal>
