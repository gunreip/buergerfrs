{{-- resources/views/components/admin/partials/client-list/⚡table.blade.php --}}

{{-- Table --}}
<flux:card class="mt-6">
    <x-ui.headers.card
        :title="__('Client List')"
        :description="__(
            'Browse and manage clients with ease: view client details, sort by name, number, type, status, people count, and creation date.',
        )"
    />

    <div class="mx-auto max-w-full">
        <div class="overflow-hidden rounded-t-lg">
            <flux:table>
                <flux:table.columns class="bg-zinc-800 text-zinc-400">
                    <flux:table.column align="center">
                        <x-ui.tooltip.trigger
                            :title="__('ID')"
                            :text="__('Unique identifier of the client, useful for tracking and reference.')"
                        >
                            {{ __('#') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    <flux:table.column
                        sortable
                        wire:click="sortBy('name')"
                    >
                        <x-ui.tooltip.trigger
                            :title="__('Client')"
                            :text="__('Name of the client, useful for identification.')"
                        >
                            {{ __('Client') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    <flux:table.column
                        sortable
                        wire:click="sortBy('client_number')"
                    >
                        <x-ui.tooltip.trigger
                            :title="__('Client number')"
                            :text="__('Unique number assigned to the client, useful for tracking and reference.')"
                        >
                            {{ __('Client number') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    <flux:table.column
                        sortable
                        wire:click="sortBy('type')"
                    >
                        <x-ui.tooltip.trigger
                            :title="__('Type')"
                            :text="__('Type of the client, useful for categorization and filtering.')"
                        >
                            {{ __('Type') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    <flux:table.column
                        sortable
                        wire:click="sortBy('status')"
                    >
                        <x-ui.tooltip.trigger
                            :title="__('Status')"
                            :text="__('Current status of the client, indicating their activity and engagement.')"
                        >
                            {{ __('Status') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    <flux:table.column
                        align="center"
                        sortable
                        wire:click="sortBy('people_count')"
                    >
                        <x-ui.tooltip.trigger
                            :title="__('People')"
                            :text="__(
                                'Number of people associated with the client, useful for understanding client size and engagement.',
                            )"
                        >
                            {{ __('People') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    <flux:table.column
                        sortable
                        wire:click="sortBy('created_at')"
                    >
                        <x-ui.tooltip.trigger
                            :title="__('Created')"
                            :text="__('Date when the client was created, useful for tracking client history.')"
                        >
                            {{ __('Created') }}
                        </x-ui.tooltip.trigger>
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

                        <flux:table.row wire:key="client-list-row-{{ $client->id }}">

                            <flux:table.cell
                                class="w-32 tabular-nums text-zinc-400"
                                align="end"
                            >
                                {{ $clients->firstItem() + $index }}
                            </flux:table.cell>

                            <flux:table.cell>
                                {{-- <div class="font-medium text-zinc-100"> --}}
                                <x-ui.text.highlight
                                    :value="$client->displayName()"
                                    :search="$search"
                                />
                                {{-- </div> --}}

                                @if ($client->legal_name && $client->legal_name !== $client->name)
                                    {{-- <div class="text-xs text-zinc-400"> --}}
                                    <x-ui.text.highlight
                                        :value="$client->legal_name"
                                        :search="$search"
                                    />
                                    {{-- </div> --}}
                                @endif

                                <div class="text-xs text-zinc-500">
                                    ID: {{ $client->id }}
                                </div>
                            </flux:table.cell>

                            <flux:table.cell>
                                @if ($client->client_number)
                                    {{-- <span class="font-mono text-sm"> --}}
                                    <x-ui.text.highlight
                                        :value="$client->client_number"
                                        :search="$search"
                                    />
                                    {{-- </span> --}}
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
            <flux:separator
                class="mt-4"
                text="{{ __('Pagination') }}"
            />

            <div class="mt-4">
                <x-ui.table.pagination :paginator="$clients" />
            </div>
        @endif

    </div>
</flux:card>
