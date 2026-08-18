{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/timeline-chains/samples.blade.php --}}

<flux:callout
    class="mt-4"
    color="{{ $sampleRows->isNotEmpty() ? 'cyan' : 'zinc' }}"
    icon="git-merge"
>
    <flux:callout.heading>
        <span class="inline-flex flex-wrap items-center gap-2">
            <span>{{ __('Timeline chain samples') }}</span>
            <flux:badge
                size="sm"
                color="{{ $sampleRows->isNotEmpty() ? 'cyan' : 'zinc' }}"
            >
                {{ number_format($sampleRows->count()) }}
            </flux:badge>
        </span>
    </flux:callout.heading>
    <flux:callout.text>
        {{ __('Five stable sample chains selected from bulk, shared and moved chain contexts for focused review before building the extended timeline view.') }}
    </flux:callout.text>

    <div class="mt-3 overflow-auto">
        <flux:table container:class="max-h-96 overflow-auto">
            <flux:table.columns
                class="bg-white dark:bg-zinc-800"
                sticky
            >
                <flux:table.column class="w-20">
                    {{ __('ID') }}
                </flux:table.column>
                <flux:table.column>
                    {{ __('ui.translation.translation-key') }}
                </flux:table.column>
                <flux:table.column>
                    {{ __('Type') }}
                </flux:table.column>
                <flux:table.column>
                    {{ __('Findings') }}
                </flux:table.column>
                <flux:table.column>
                    {{ __('Relations') }}
                </flux:table.column>
                <flux:table.column>
                    {{ __('Events') }}
                </flux:table.column>
                <flux:table.column>
                    {{ __('Related keys') }}
                </flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($sampleRows as $row)
                    @php
                        $relatedKeys = collect($row['related_translation_keys'] ?? [])
                            ->filter()
                            ->values();
                        $eventSummary = collect($row['timeline_event_summary'] ?? [])
                            ->sortDesc()
                            ->take(4);
                    @endphp
                    <flux:table.row wire:key="translation-workbench-timeline-chain-sample-{{ $row['id'] }}">
                        <flux:table.cell>
                            <flux:badge
                                size="sm"
                                color="cyan"
                            >
                                #{{ $row['id'] }}
                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="wrap-anywhere max-w-xl text-wrap font-mono text-xs">
                                {{ $row['translation_key'] }}
                            </div>
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex flex-wrap gap-1">
                                <flux:badge
                                    size="sm"
                                    color="{{ match ($row['chain_type']) {
                                        'bulk' => 'purple',
                                        'shared' => 'pink',
                                        'moved' => 'amber',
                                        default => 'zinc',
                                    } }}"
                                >
                                    {{ str((string) $row['chain_type'])->headline() }}
                                </flux:badge>
                                <flux:badge
                                    size="sm"
                                    color="{{ $row['chain_status'] === 'active' ? 'green' : 'zinc' }}"
                                >
                                    {{ str((string) $row['chain_status'])->headline() }}
                                </flux:badge>
                            </div>
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex flex-wrap gap-1">
                                <flux:badge
                                    size="sm"
                                    color="sky"
                                >
                                    {{ __('All') }}: {{ number_format((int) $row['finding_count']) }}
                                </flux:badge>
                                <flux:badge
                                    size="sm"
                                    color="green"
                                >
                                    {{ __('Active') }}: {{ number_format((int) $row['active_finding_count']) }}
                                </flux:badge>
                                @if ((int) $row['obsolete_finding_count'] > 0)
                                    <flux:badge
                                        size="sm"
                                        color="amber"
                                    >
                                        {{ __('Obsolete') }}:
                                        {{ number_format((int) $row['obsolete_finding_count']) }}
                                    </flux:badge>
                                @endif
                                @if ((int) $row['commented_out_finding_count'] > 0)
                                    <flux:badge
                                        size="sm"
                                        color="zinc"
                                    >
                                        {{ __('Commented out') }}:
                                        {{ number_format((int) $row['commented_out_finding_count']) }}
                                    </flux:badge>
                                @endif
                            </div>
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex flex-wrap gap-1">
                                <flux:badge
                                    size="sm"
                                    color="violet"
                                >
                                    {{ __('Keys') }}: {{ number_format((int) $row['key_count']) }}
                                </flux:badge>
                                <flux:badge
                                    size="sm"
                                    color="amber"
                                >
                                    {{ __('Reviews') }}: {{ number_format((int) $row['review_count']) }}
                                </flux:badge>
                                <flux:badge
                                    size="sm"
                                    color="emerald"
                                >
                                    {{ __('Values') }}: {{ number_format((int) $row['lang_value_count']) }}
                                </flux:badge>
                            </div>
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="space-y-1">
                                <flux:badge
                                    size="sm"
                                    color="rose"
                                >
                                    {{ __('Timeline') }}: {{ number_format((int) $row['timeline_event_count']) }}
                                </flux:badge>
                                <div class="flex max-w-sm flex-wrap gap-1">
                                    @foreach ($eventSummary as $eventType => $eventCount)
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
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="max-w-md space-y-1">
                                @foreach ($relatedKeys->take(4) as $relatedKey)
                                    <div
                                        class="wrap-anywhere text-wrap font-mono text-xs text-zinc-700 dark:text-zinc-200">
                                        {{ $relatedKey }}
                                    </div>
                                @endforeach

                                @if ($relatedKeys->count() > 4)
                                    <flux:badge
                                        size="sm"
                                        color="cyan"
                                    >
                                        {{ __('+:count more', ['count' => number_format($relatedKeys->count() - 4)]) }}
                                    </flux:badge>
                                @endif
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="7">
                            <flux:text class="text-sm text-zinc-500">
                                {{ __('No timeline-chain samples available yet. Run the timeline-chain collector with sync first.') }}
                            </flux:text>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>
</flux:callout>
