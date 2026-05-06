{{-- resources/views/components/admin/partials/client-list/⚡table.blade.php --}}

{{-- Table --}}
<flux:card class="mt-6">
    <flux:heading
        class="mb-4"
        size="lg"
    >
        {{ __('Client List') }}
    </flux:heading>

    <div class="overflow-hidden rounded-t-lg">
        <flux:table>
            <flux:table.columns class="bg-zinc-800 text-zinc-400">
                <flux:table.column align="center">
                    {{ __('#') }}
                </flux:table.column>

                <flux:table.column
                    sortable
                    wire:click="sortBy('name')"
                >
                    {{ __('Client') }}
                </flux:table.column>

                <flux:table.column
                    sortable
                    wire:click="sortBy('client_number')"
                >
                    {{ __('Client number') }}
                </flux:table.column>

                <flux:table.column
                    sortable
                    wire:click="sortBy('type')"
                >
                    {{ __('Type') }}
                </flux:table.column>

                <flux:table.column
                    sortable
                    wire:click="sortBy('status')"
                >
                    {{ __('Status') }}
                </flux:table.column>

                <flux:table.column
                    align="center"
                    sortable
                    wire:click="sortBy('people_count')"
                >
                    {{ __('People') }}
                </flux:table.column>

                <flux:table.column
                    sortable
                    wire:click="sortBy('created_at')"
                >
                    {{ __('Created') }}
                </flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($clients as $index => $client)
                    @php
                        $statusColor = match ($client->status) {
                            \App\Models\Client::STATUS_ACTIVE => 'green',
                            \App\Models\Client::STATUS_PENDING => 'orange',
                            \App\Models\Client::STATUS_INACTIVE => 'zinc',
                            \App\Models\Client::STATUS_SUSPENDED => 'red',
                            default => 'zinc',
                        };
                    @endphp

                    <flux:table.row>
                        <flux:table.cell
                            class="w-16 tabular-nums text-zinc-400"
                            align="end"
                        >
                            {{ $clients->firstItem() + $index }}
                        </flux:table.cell>

                        <flux:table.cell>
                            <div class="font-medium text-zinc-100">
                                {!! $highlightSearchMatch($client->displayName(), $search) !!}
                            </div>

                            @if ($client->legal_name && $client->legal_name !== $client->name)
                                <div class="text-xs text-zinc-400">
                                    {!! $highlightSearchMatch($client->legal_name, $search) !!}
                                </div>
                            @endif

                            <div class="text-xs text-zinc-500">
                                ID: {{ $client->id }}
                            </div>
                        </flux:table.cell>

                        <flux:table.cell>
                            @if ($client->client_number)
                                <span class="font-mono text-sm">
                                    {!! $highlightSearchMatch($client->client_number, $search) !!}
                                </span>
                            @else
                                <flux:badge
                                    color="zinc"
                                    variant="subtle"
                                >
                                    {{ __('Missing') }}
                                </flux:badge>
                            @endif
                        </flux:table.cell>

                        <flux:table.cell>
                            @if ($client->type)
                                <flux:badge
                                    color="sky"
                                    variant="subtle"
                                >
                                    {{ Str::headline($client->type) }}
                                </flux:badge>
                            @else
                                <span class="text-zinc-500">—</span>
                            @endif
                        </flux:table.cell>

                        <flux:table.cell>
                            <flux:badge
                                color="{{ $statusColor }}"
                                variant="subtle"
                            >
                                {{ Str::headline($client->status) }}
                            </flux:badge>
                        </flux:table.cell>

                        <flux:table.cell
                            class="tabular-nums"
                            align="center"
                        >
                            {{ $client->people_count }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ $client->created_at?->format('Y-m-d H:i') }}
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="7">
                            <flux:text>
                                {{ __('No clients found.') }}
                            </flux:text>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>

    @if ($clients->hasPages())
        <div class="mt-4">
            {{ $clients->links() }}
        </div>
    @endif
</flux:card>
