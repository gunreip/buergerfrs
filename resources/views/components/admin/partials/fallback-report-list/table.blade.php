{{-- resources/views/components/admin/partials/fallback-report-list/table.blade.php --}}

{{-- Table --}}
<flux:card class="mt-6">
    <flux:heading
        class="mb-4"
        size="lg"
    >
        {{ __('Fallback Reports') }}
    </flux:heading>

    <div class="overflow-hidden rounded-t-lg">
        <flux:table>
            <flux:table.columns class="bg-zinc-800 text-zinc-400">
                <flux:table.column
                    sortable
                    :sorted="$sortField === 'id'"
                    :direction="$sortDirection"
                    align="center"
                    wire:click="sortBy('id')"
                >
                    {{ __('#') }}
                </flux:table.column>

                <flux:table.column
                    sortable
                    align="center"
                    :sorted="$sortField === 'status'"
                    :direction="$sortDirection"
                    wire:click="sortBy('status')"
                >
                    {{ __('Status') }}
                </flux:table.column>

                <flux:table.column
                    sortable
                    :sorted="$sortField === 'type'"
                    :direction="$sortDirection"
                    wire:click="sortBy('type')"
                >
                    {{ __('Type') }}
                </flux:table.column>

                <flux:table.column
                    sortable
                    :sorted="$sortField === 'key'"
                    :direction="$sortDirection"
                    wire:click="sortBy('key')"
                >
                    {{ __('Key') }}
                </flux:table.column>

                <flux:table.column
                    sortable
                    :sorted="$sortField === 'fallback'"
                    :direction="$sortDirection"
                    wire:click="sortBy('fallback')"
                >
                    {{ __('Fallback') }}
                </flux:table.column>

                <flux:table.column
                    sortable
                    align="center"
                    :sorted="$sortField === 'count'"
                    :direction="$sortDirection"
                    wire:click="sortBy('count')"
                >
                    {{ __('Count') }}
                </flux:table.column>

                <flux:table.column
                    sortable
                    :sorted="$sortField === 'last_seen_at'"
                    :direction="$sortDirection"
                    wire:click="sortBy('last_seen_at')"
                >
                    {{ __('Last reported') }}
                </flux:table.column>

                <flux:table.column>
                    {{ __('Context') }}
                </flux:table.column>

                <flux:table.column align="center">
                    {{ __('Actions') }}
                </flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($reports as $report)
                    <flux:table.row wire:key="fallback-report-{{ $report->id }}">
                        <flux:table.cell
                            class="w-32 tabular-nums text-zinc-400"
                            align="end"
                        >
                            {{ $report->id }}
                        </flux:table.cell>

                        <flux:table.cell align="center">
                            @if ($report->reviewed)
                                <flux:badge
                                    color="green"
                                    variant="subtle"
                                >
                                    {{ __('Reviewed') }}
                                </flux:badge>
                            @else
                                <flux:badge
                                    color="amber"
                                    variant="subtle"
                                >
                                    {{ __('Open') }}
                                </flux:badge>
                            @endif
                        </flux:table.cell>

                        <flux:table.cell>
                            <span class="font-mono">
                                <span class="">{{ $report->type }}<span>
                        </flux:table.cell>

                        <flux:table.cell>
                            <span class="font-mono">{{ $report->key }}</span>
                        </flux:table.cell>

                        <flux:table.cell>
                            <span class="font-mono">{{ $report->fallback ?: '—' }}</span>
                        </flux:table.cell>

                        <flux:table.cell align="center">
                            {{ $report->count }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ $report->last_seen_at?->format('Y-m-d H:i:s') ?? '—' }}
                        </flux:table.cell>

                        <flux:table.cell>
                            <pre
                                class="max-w-md overflow-auto rounded-md bg-zinc-950/5 p-2 text-xs text-zinc-700 dark:bg-white/5 dark:text-zinc-300">{{ json_encode($report->context ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</pre>
                        </flux:table.cell>

                        <flux:table.cell align="center">
                            @if ($report->reviewed)
                                <flux:button
                                    type="button"
                                    size="xs"
                                    variant="ghost"
                                    wire:click="markUnreviewed({{ $report->id }})"
                                >
                                    {{ __('Reopen') }}
                                </flux:button>
                            @else
                                <flux:button
                                    type="button"
                                    size="xs"
                                    color="sky"
                                    variant="primary"
                                    wire:click="markReviewed({{ $report->id }})"
                                >
                                    {{ __('Mark reviewed') }}
                                </flux:button>
                            @endif
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="8">
                            <div class="py-8 text-center text-sm text-zinc-500 dark:text-zinc-400">
                                {{ __('No fallback reports found.') }}
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>

    @if ($reports->hasPages())
        <div class="mt-4">
            {{ $reports->links() }}
        </div>
    @endif

</flux:card>
