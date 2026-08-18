{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/timeline-chains/canonical-root.blade.php --}}

    <flux:callout
        class="mt-4"
        color="green"
        icon="git-merge"
    >
        <flux:callout.heading>
            <span class="inline-flex flex-wrap items-center gap-2">
                <span>{{ __('Canonical chain root') }}</span>
                <flux:badge
                    size="sm"
                    color="green"
                >
                    {{ $mainRow['translation_key'] }}
                </flux:badge>
                <flux:badge
                    size="sm"
                    color="cyan"
                >
                    {{ number_format($rootRows->count()) }}
                </flux:badge>
            </span>
        </flux:callout.heading>
        <flux:callout.text>
            {{ __('Direct root entries for the selected canonical chain only. Detailed surrounding history stays out of this first overview.') }}
        </flux:callout.text>

        <div class="mt-3 overflow-auto">
            <flux:table container:class="overflow-auto">
                <flux:table.columns
                    class="bg-white dark:bg-zinc-800"
                    sticky
                >
                    <flux:table.column class="w-24">
                        {{ __('Timestamp') }}
                    </flux:table.column>
                    <flux:table.column>
                        {{ __('Trunk') }}
                    </flux:table.column>
                    <flux:table.column>
                        {{ __('Branch') }}
                    </flux:table.column>
                    <flux:table.column>
                        {{ __('ui.translation.translation-key') }}
                    </flux:table.column>
                    <flux:table.column>
                        {{ __('Event') }}
                    </flux:table.column>
                    <flux:table.column>
                        {{ __('State') }}
                    </flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse ($rootRows as $rootRow)
                        <flux:table.row
                            wire:key="translation-workbench-timeline-chain-root-row-{{ $loop->index }}-{{ md5((string) $rootRow['trunk'] . (string) $rootRow['branch'] . (string) $rootRow['timestamp']) }}"
                        >
                            <flux:table.cell>
                                <div class="space-y-0.5 text-xs text-zinc-500">
                                    <x-ui.date-time.date :value="$rootRow['timestamp']" />
                                    <x-ui.date-time.time :value="$rootRow['timestamp']" />
                                </div>
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:badge
                                    size="sm"
                                    color="green"
                                >
                                    {{ $rootRow['trunk'] }}
                                </flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:badge
                                    size="sm"
                                    color="{{ $rootRow['branch_color'] ?? 'zinc' }}"
                                >
                                    {{ $rootRow['branch'] }}
                                </flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="wrap-anywhere max-w-xl text-wrap font-mono text-xs">
                                    {{ $rootRow['translation_key'] }}
                                </div>
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:badge
                                    size="sm"
                                    color="{{ $rootRow['color'] ?? 'zinc' }}"
                                >
                                    {{ $rootRow['event'] }}
                                </flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>
                                <span
                                    class="wrap-anywhere text-wrap font-mono text-xs text-zinc-500 dark:text-zinc-400">
                                    {{ $rootRow['state'] }}
                                </span>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="6">
                                <flux:text class="text-sm text-zinc-500">
                                    {{ __('No direct root entries collected for this canonical chain yet.') }}
                                </flux:text>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>
    </flux:callout>
