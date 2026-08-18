                {{-- <div
                    class="grid gap-4"
                    style="grid-template-columns: repeat(var(--tw-origin-columns), minmax(18rem, 1fr));"
                >
                    @foreach ($originRows as $originRow)
                        <div
                            class="relative flex min-w-72 flex-col items-center gap-3"
                            wire:key="translation-workbench-timeline-chain-graph-root-{{ $loop->index }}-{{ md5((string) $originRow['first_root'] . (string) $originRow['first_timestamp'] . (string) $originRow['last_timestamp']) }}"
                        >
                            <div
                                class="absolute bottom-0 left-1/2 top-0 border-l border-dashed border-cyan-300 dark:border-cyan-500/70">
                            </div>

                            <div
                                class="relative z-10 w-full rounded-lg border border-amber-200/70 bg-amber-50 px-3 py-2 shadow-sm dark:border-amber-500/20 dark:bg-amber-500/10">
                                <div class="mb-1 flex flex-wrap items-center gap-2">
                                    <flux:badge
                                        size="sm"
                                        color="amber"
                                    >
                                        {{ $originRow['last_root'] }}
                                    </flux:badge>
                                    <flux:badge
                                        size="sm"
                                        color="{{ $originRow['last_color'] ?? 'zinc' }}"
                                    >
                                        {{ $originRow['last_event'] }}
                                    </flux:badge>
                                </div>
                                <div class="wrap-anywhere text-wrap font-mono text-xs text-zinc-700 dark:text-zinc-200">
                                    {{ $originRow['last_origin_key'] }}
                                </div>
                                <div
                                    class="mt-1 flex flex-wrap items-center gap-2 text-xs text-zinc-500 dark:text-zinc-400">
                                    <x-ui.date-time.date :value="$originRow['last_timestamp']" />
                                    <x-ui.date-time.time :value="$originRow['last_timestamp']" />
                                    <span class="font-mono">{{ $originRow['last_state'] }}</span>
                                </div>
                            </div>

                            <div
                                class="relative z-10 w-full rounded-lg border border-sky-200/70 bg-sky-50 px-3 py-2 shadow-sm dark:border-sky-500/20 dark:bg-sky-500/10">
                                <div class="mb-1 flex flex-wrap items-center gap-2">
                                    <flux:badge
                                        size="sm"
                                        color="sky"
                                    >
                                        {{ $originRow['first_root'] }}
                                    </flux:badge>
                                    <flux:badge
                                        size="sm"
                                        color="{{ $originRow['first_color'] ?? 'zinc' }}"
                                    >
                                        {{ $originRow['first_event'] }}
                                    </flux:badge>
                                </div>
                                <div class="wrap-anywhere text-wrap font-mono text-xs text-zinc-700 dark:text-zinc-200">
                                    {{ $originRow['first_origin_key'] }}
                                </div>
                                <div
                                    class="mt-1 flex flex-wrap items-center gap-2 text-xs text-zinc-500 dark:text-zinc-400">
                                    <x-ui.date-time.date :value="$originRow['first_timestamp']" />
                                    <x-ui.date-time.time :value="$originRow['first_timestamp']" />
                                    <span class="font-mono">{{ $originRow['first_state'] }}</span>
                                </div>
                            </div>

                            <flux:text
                                class="wrap-anywhere relative z-10 max-w-72 text-center text-xs text-zinc-500 dark:text-zinc-400"
                            >
                                {{ $originRow['context'] }}
                            </flux:text>
                        </div>
                    @endforeach
                </div> --}}
