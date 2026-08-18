{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/timeline-chains/canonical-roots.blade.php --}}

    <flux:callout
        class="mt-4"
        color="amber"
        icon="git-pull-request-arrow"
    >
        <flux:callout.heading>
            <span class="inline-flex flex-wrap items-center gap-2">
                <span>{{ __('Canonical chain roots') }}</span>
                <flux:badge
                    size="sm"
                    color="green"
                >
                    {{ $mainRow['translation_key'] }}
                </flux:badge>
                <flux:badge
                    size="sm"
                    color="amber"
                >
                    {{ number_format($originRows->count()) }}
                </flux:badge>
            </span>
        </flux:callout.heading>
        <flux:callout.text>
            {{ __('Earliest and latest original findings that were folded into the shared key, shown as first seen and last single state only.') }}
        </flux:callout.text>

        <div class="mt-3 space-y-3">
            <flux:callout {{-- class="flex flex-wrap items-center justify-center gap-2 rounded-lg border border-amber-200/70 bg-amber-50/70 px-3 py-2 dark:border-amber-500/20 dark:bg-amber-500/10" --}}>
                <flux:badge
                    size="sm"
                    color="green"
                >
                    {{ __('Shared key') }}
                </flux:badge>
                <flux:badge
                    size="sm"
                    color="green"
                >
                    {{ $mainRow['translation_key'] }}
                </flux:badge>
                <flux:text class="col-span-2 text-xs text-zinc-500 dark:text-zinc-400">
                    {{ __('The original strands below merge into this continued key.') }}
                </flux:text>
            </flux:callout>

            @if ($originRows->isNotEmpty())
                <div class="grid gap-4 xl:grid-cols-2">
                    @foreach ($originRows as $originRow)
                        <flux:callout
                            {{-- class="space-y-2 rounded-lg border border-zinc-200/70 bg-white/60 p-3 dark:border-zinc-700/70 dark:bg-zinc-900/40" --}}
                            wire:key="translation-workbench-timeline-chain-origin-strand-{{ $loop->index }}-{{ md5((string) $originRow['first_root'] . (string) $originRow['first_timestamp'] . (string) $originRow['last_timestamp']) }}"
                        >
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <div class="flex flex-wrap items-center gap-2">
                                    <flux:badge
                                        size="sm"
                                        color="green"
                                    >
                                        {{ $originRow['trunk'] }}
                                    </flux:badge>
                                    <flux:badge
                                        size="sm"
                                        color="amber"
                                    >
                                        {{ $originRow['first_root'] }}
                                    </flux:badge>
                                </div>
                                <flux:text class="wrap-anywhere text-wrap text-xs text-zinc-500 dark:text-zinc-400">
                                    {{ $originRow['context'] }}
                                </flux:text>
                            </div>

                            <flux:table container:class="overflow-auto">
                                <flux:table.columns
                                    class="bg-white dark:bg-zinc-800"
                                    sticky
                                >
                                    <flux:table.column class="w-24">
                                        {{ __('Timestamp') }}
                                    </flux:table.column>
                                    <flux:table.column>
                                        {{ __('Root ID') }}
                                    </flux:table.column>
                                    <flux:table.column>
                                        {{ __('Origin key') }}
                                    </flux:table.column>
                                    <flux:table.column>
                                        {{ __('Event') }}
                                    </flux:table.column>
                                    <flux:table.column>
                                        {{ __('State') }}
                                    </flux:table.column>
                                </flux:table.columns>

                                <flux:table.rows>
                                    <flux:table.row>
                                        <flux:table.cell>
                                            <div class="space-y-0.5 text-xs text-zinc-500">
                                                <x-ui.date-time.date :value="$originRow['last_timestamp']" />
                                                <x-ui.date-time.time :value="$originRow['last_timestamp']" />
                                            </div>
                                        </flux:table.cell>
                                        <flux:table.cell>
                                            <flux:badge
                                                size="sm"
                                                color="amber"
                                            >
                                                {{ $originRow['last_root'] }}
                                            </flux:badge>
                                        </flux:table.cell>
                                        <flux:table.cell>
                                            <div class="wrap-anywhere max-w-md text-wrap font-mono text-xs">
                                                {{ $originRow['last_origin_key'] }}
                                            </div>
                                        </flux:table.cell>
                                        <flux:table.cell>
                                            <flux:badge
                                                size="sm"
                                                color="{{ $originRow['last_color'] ?? 'zinc' }}"
                                            >
                                                {{ $originRow['last_event'] }}
                                            </flux:badge>
                                        </flux:table.cell>
                                        <flux:table.cell>
                                            <span
                                                class="wrap-anywhere text-wrap font-mono text-xs text-zinc-500 dark:text-zinc-400"
                                            >
                                                {{ $originRow['last_state'] }}
                                            </span>
                                        </flux:table.cell>
                                    </flux:table.row>

                                    <flux:table.row>
                                        <flux:table.cell>
                                            <div class="space-y-0.5 text-xs text-zinc-500">
                                                <x-ui.date-time.date :value="$originRow['first_timestamp']" />
                                                <x-ui.date-time.time :value="$originRow['first_timestamp']" />
                                            </div>
                                        </flux:table.cell>
                                        <flux:table.cell>
                                            <flux:badge
                                                size="sm"
                                                color="sky"
                                            >
                                                {{ $originRow['first_root'] }}
                                            </flux:badge>
                                        </flux:table.cell>
                                        <flux:table.cell>
                                            <div class="wrap-anywhere max-w-md text-wrap font-mono text-xs">
                                                {{ $originRow['first_origin_key'] }}
                                            </div>
                                        </flux:table.cell>
                                        <flux:table.cell>
                                            <flux:badge
                                                size="sm"
                                                color="{{ $originRow['first_color'] ?? 'zinc' }}"
                                            >
                                                {{ $originRow['first_event'] }}
                                            </flux:badge>
                                        </flux:table.cell>
                                        <flux:table.cell>
                                            <span
                                                class="wrap-anywhere text-wrap font-mono text-xs text-zinc-500 dark:text-zinc-400"
                                            >
                                                {{ $originRow['first_state'] }}
                                            </span>
                                        </flux:table.cell>
                                    </flux:table.row>
                                </flux:table.rows>
                            </flux:table>
                        </flux:callout>
                    @endforeach
                </div>
            @else
                <flux:text class="text-sm text-zinc-500">
                    {{ __('No origin rows could be derived for this canonical chain yet.') }}
                </flux:text>
            @endif
        </div>
    </flux:callout>
