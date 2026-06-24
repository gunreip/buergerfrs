{{-- resources/views/components/admin/partials/translation-list/modal-history/details.blade.php --}}

@props(['historyEvents'])

<div class="max-h-104 -mr-4 space-y-3 overflow-y-auto pr-4">
    @forelse ($historyEvents as $historyEvent)
        @php
            $historyContext = $historyEvent->context ? json_decode($historyEvent->context, true) : [];

            $historyLocale = is_array($historyContext) ? $historyContext['locale'] ?? null : null;

            $eventColor = match ($historyEvent->event_type) {
                'created' => 'green',
                'value_changed' => 'cyan',
                'key_changed' => 'pink',
                'moved' => 'amber',
                'stale_marked' => 'red',
                'reactivated' => 'lime',
                'fingerprint_changed' => 'sky',
                'discovered' => 'indigo',
                default => 'zinc',
            };
        @endphp

        <div class="rounded-xl border border-zinc-200 bg-white/70 p-4 dark:border-zinc-700 dark:bg-zinc-950/30">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div class="space-y-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <flux:badge
                            size="sm"
                            variant="subtle"
                            color="zinc"
                        >
                            {{ $historyEvent->entity_type }}
                        </flux:badge>

                        <flux:badge
                            size="sm"
                            variant="solid"
                            :color="$eventColor"
                        >
                            {{ $historyEvent->event_type }}
                        </flux:badge>

                        @if ($historyLocale)
                            <flux:badge
                                size="sm"
                                variant="subtle"
                                color="indigo"
                            >
                                {{ strtoupper($historyLocale) }}
                            </flux:badge>
                        @endif
                    </div>

                    <div class="text-xs text-zinc-500 dark:text-zinc-400">
                        {{ $historyEvent->created_at }}
                    </div>
                </div>
                <div class="text-right text-xs text-zinc-500 dark:text-zinc-400">
                    @if ($historyEvent->id)
                        {{ __('admin.translation_list.modal_history.event') }}
                        #{{ $historyEvent->id }}
                    @else
                        {{ __('Derived baseline') }}
                    @endif
                </div>
            </div>

            @if ($historyEvent->reason)
                <div class="mt-3 text-sm">
                    <span class="font-semibold">{{ __('admin.translation_list.modal_history.reason') }}:</span>
                    <span class="ml-2">{{ $historyEvent->reason }}</span>
                </div>
            @endif

            <div class="mt-3 grid gap-3 md:grid-cols-2">
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                        {{ __('admin.translation_list.modal_history.old_value') }}
                    </div>

                    <div class="wrap-anywhere mt-1 rounded-lg bg-zinc-100 p-2 font-mono text-xs dark:bg-zinc-900">
                        {{ $historyEvent->old_value ?? '—' }}
                    </div>
                </div>

                <div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                        {{ __('admin.translation_list.modal_history.new_value') }}
                    </div>

                    <div class="wrap-anywhere mt-1 rounded-lg bg-zinc-100 p-2 font-mono text-xs dark:bg-zinc-900">
                        {{ $historyEvent->new_value ?? '—' }}
                    </div>
                </div>
            </div>

            @if ($historyEvent->old_status || $historyEvent->new_status)
                <div class="mt-3 flex flex-wrap gap-3 text-sm">
                    <div>
                        <span class="font-semibold">{{ __('admin.translation_list.modal_history.old_status') }}:</span>
                        <span class="ml-2 font-mono">{{ $historyEvent->old_status ?? '—' }}</span>
                    </div>

                    <div>
                        <span class="font-semibold">{{ __('admin.translation_list.modal_history.new_status') }}:</span>
                        <span class="ml-2 font-mono">{{ $historyEvent->new_status ?? '—' }}</span>
                    </div>
                </div>
            @endif

            @if ($historyEvent->old_file || $historyEvent->new_file || $historyEvent->old_line || $historyEvent->new_line)
                <div class="mt-3 grid gap-3 md:grid-cols-2">
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                            {{ __('admin.translation_list.modal_history.old_location') }}
                        </div>

                        <code class="wrap-anywhere mt-1 block text-xs">
                            {{ $historyEvent->old_file ?: '—' }}:{{ $historyEvent->old_line ?: '—' }}
                        </code>
                    </div>

                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                            {{ __('admin.translation_list.modal_history.new_location') }}
                        </div>

                        <code class="wrap-anywhere mt-1 block text-xs">
                            {{ $historyEvent->new_file ?: '—' }}:{{ $historyEvent->new_line ?: '—' }}
                        </code>
                    </div>
                </div>
            @endif

            @if ($historyEvent->context)
                <details class="mt-3">
                    <summary class="cursor-pointer text-sm font-semibold text-zinc-600 dark:text-zinc-300">
                        {{ __('admin.translation_list.modal_history.context') }}
                    </summary>

                    <pre class="wrap-anywhere mt-2 overflow-x-auto rounded-lg bg-zinc-100 p-3 text-xs dark:bg-zinc-900">{{ $historyEvent->context }}</pre>
                </details>
            @endif
        </div>
    @empty
        <div
            class="rounded-xl border border-dashed border-zinc-300 p-6 text-center text-sm text-zinc-500 dark:border-zinc-700 dark:text-zinc-400">
            {{ __('admin.translation_list.modal_history.no_history_events_available_for_this_translation_key') }}
        </div>
    @endforelse
</div>
