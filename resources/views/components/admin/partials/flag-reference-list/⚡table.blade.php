{{-- resources/views/components/admin/partials/flag-reference-list/⚡table.blade.php --}}

<flux:card class="mt-6">
    <x-ui.headers.card
        :title="__('Flag Reference Entries')"
        :description="__('Locale codes with resolved icon candidates, DB source footprint and editable review comments.')"
    />

    <div class="mx-auto mt-4 max-w-full">
        <div class="overflow-hidden rounded-t-lg">
            <flux:table class="app-table">
                <flux:table.columns class="bg-zinc-800 text-zinc-400">
                    <flux:table.column
                        class="w-44"
                        align="center"
                        sortable
                        :sorted="$sortField === 'code'"
                        :direction="$sortDirection"
                        wire:click="sortBy('code')"
                    >
                        {{ __('Code') }}
                    </flux:table.column>

                    <flux:table.column
                        class="w-28"
                        sortable
                        :sorted="$sortField === 'type'"
                        :direction="$sortDirection"
                        wire:click="sortBy('type')"
                    >
                        {{ __('ui.type') }}
                    </flux:table.column>

                    <flux:table.column
                        class="w-36"
                        sortable
                        :sorted="$sortField === 'status'"
                        :direction="$sortDirection"
                        wire:click="sortBy('status')"
                    >
                        {{ __('ui.state.status') }}
                    </flux:table.column>

                    <flux:table.column
                        class="w-72"
                        sortable
                        :sorted="$sortField === 'resolved'"
                        :direction="$sortDirection"
                        wire:click="sortBy('resolved')"
                    >
                        {{ __('Resolved icon') }}
                    </flux:table.column>

                    <flux:table.column
                        class="w-96"
                        sortable
                        :sorted="$sortField === 'candidates'"
                        :direction="$sortDirection"
                        wire:click="sortBy('candidates')"
                    >
                        {{ __('Candidates') }}
                    </flux:table.column>

                    <flux:table.column
                        sortable
                        :sorted="$sortField === 'sources'"
                        :direction="$sortDirection"
                        wire:click="sortBy('sources')"
                    >
                        {{ __('ui.sources.sources') }}
                    </flux:table.column>

                    <flux:table.column
                        class="w-96"
                        sortable
                        :sorted="$sortField === 'comment'"
                        :direction="$sortDirection"
                        wire:click="sortBy('comment')"
                    >
                        {{ __('Comment') }}
                    </flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse ($entries as $entry)
                        <flux:table.row wire:key="flag-reference-row-{{ $entry['code'] }}">
                            <flux:table.cell align="center">
                                <span class="inline-flex items-center gap-2">
                                    <x-ui.locale.flag
                                        :locale="$entry['code']"
                                        size="lg"
                                        :title="strtoupper((string) $entry['code'])"
                                    />

                                    <span
                                        class="font-mono text-sm font-semibold uppercase text-zinc-800 dark:text-zinc-100"
                                    >
                                        {{ $entry['code'] }}
                                    </span>
                                </span>
                            </flux:table.cell>

                            <flux:table.cell>
                                <flux:badge
                                    color="zinc"
                                    variant="subtle"
                                    size="sm"
                                >
                                    {{ $entry['type'] }}
                                </flux:badge>
                            </flux:table.cell>

                            <flux:table.cell>
                                @if ($entry['status'] === 'resolved')
                                    <flux:badge
                                        color="green"
                                        variant="subtle"
                                        size="sm"
                                    >
                                        {{ __('Resolved') }}
                                    </flux:badge>
                                @else
                                    <flux:badge
                                        color="amber"
                                        variant="subtle"
                                        size="sm"
                                    >
                                        {{ __('Needs review') }}
                                    </flux:badge>
                                @endif
                            </flux:table.cell>

                            <flux:table.cell>
                                <span class="font-mono text-sm text-zinc-700 dark:text-zinc-200">
                                    {{ $entry['resolved'] !== '' ? $entry['resolved'] : '—' }}
                                </span>
                            </flux:table.cell>

                            <flux:table.cell>
                                <div class="space-y-1">
                                    @forelse (array_slice($entry['candidates'], 0, 3) as $candidate)
                                        <div class="font-mono text-[11px] text-zinc-600 dark:text-zinc-300">
                                            {{ $candidate }}
                                        </div>
                                    @empty
                                        <div class="text-xs text-zinc-400">—</div>
                                    @endforelse

                                    @if (count($entry['candidates']) > 3)
                                        <div class="text-[11px] text-zinc-500 dark:text-zinc-400">
                                            +{{ count($entry['candidates']) - 3 }} {{ __('more') }}
                                        </div>
                                    @endif
                                </div>
                            </flux:table.cell>

                            <flux:table.cell>
                                <div class="flex flex-wrap gap-1">
                                    @forelse ($entry['sources'] as $source)
                                        <flux:badge
                                            color="sky"
                                            variant="subtle"
                                            size="sm"
                                        >
                                            {{ $source }}
                                        </flux:badge>
                                    @empty
                                        <span class="text-xs text-zinc-400">—</span>
                                    @endforelse
                                </div>
                            </flux:table.cell>

                            <flux:table.cell>
                                <flux:input
                                    value="{{ $entry['comment'] }}"
                                    placeholder="{{ __('Optional comment') }}"
                                    wire:change="saveComment({{ Illuminate\Support\Js::from($entry['code']) }}, $event.target.value)"
                                />
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="7">
                                <div class="py-8 text-center text-sm text-zinc-500 dark:text-zinc-400">
                                    {{ __('No entries found for the current filters.') }}
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>

        <flux:separator
            class="mt-4"
            text="{{ __('ui.nouns.pagination') }}"
        />

        @if ($entries->hasPages())
            <div class="mt-4">
                <x-ui.table.pagination :paginator="$entries" />
            </div>
        @endif
    </div>
</flux:card>
