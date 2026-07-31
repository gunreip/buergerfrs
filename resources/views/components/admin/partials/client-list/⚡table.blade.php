{{-- resources/views/components/admin/partials/client-list/⚡table.blade.php --}}

{{-- Table --}}
<flux:card class="mt-6">
    <x-ui.headers.card
        :title="__('admin.client_list.table.client_list')"
        :description="__('admin.client_list.table.browse_and_manage_clients_with_ease_view_client_details_sort_by_name_number_type',
        )"
    />

    <div class="mx-auto max-w-full">
        <div class="overflow-hidden rounded-t-lg">
            <flux:table>
                <flux:table.columns class="bg-zinc-800 text-zinc-400">
                    <flux:table.column align="center">
                        <x-ui.tooltip.trigger
                            :title="__('admin.user_list.table.id')"
                            :text="__('admin.client_list.table.unique_identifier_of_the_client_useful_for_tracking_and_reference')"
                        >
                            {{ __('ui.labels.number_short') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    <flux:table.column
                        sortable
                        wire:click="sortBy('name')"
                    >
                        <x-ui.tooltip.trigger
                            :title="__('layouts.sidebar.management.client')"
                            :text="__('admin.client_list.table.name_of_the_client_useful_for_identification')"
                        >
                            {{ __('layouts.sidebar.management.client') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    <flux:table.column
                        sortable
                        wire:click="sortBy('client_number')"
                    >
                        <x-ui.tooltip.trigger
                            :title="__('ui.client-number')"
                            :text="__('admin.client_list.table.unique_number_assigned_to_the_client_useful_for_tracking_and_reference')"
                        >
                            {{ __('ui.client-number') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    <flux:table.column
                        sortable
                        wire:click="sortBy('type')"
                    >
                        <x-ui.tooltip.trigger
                            :title="__('ui.type')"
                            :text="__('admin.client_list.table.type_of_the_client_useful_for_categorization_and_filtering')"
                        >
                            {{ __('ui.type') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    <flux:table.column
                        sortable
                        wire:click="sortBy('status')"
                    >
                        <x-ui.tooltip.trigger
                            :title="__('ui.status')"
                            :text="__('admin.client_list.table.current_status_of_the_client_indicating_their_activity_and_engagement')"
                        >
                            {{ __('ui.status') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    <flux:table.column
                        align="center"
                        sortable
                        wire:click="sortBy('people_count')"
                    >
                        <x-ui.tooltip.trigger
                            :title="__('ui.people')"
                            :text="__('admin.client_list.table.number_of_people_associated_with_the_client_useful_for_understanding_client_size',
                            )"
                        >
                            {{ __('ui.people') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    <flux:table.column
                        sortable
                        wire:click="sortBy('created_at')"
                    >
                        <x-ui.tooltip.trigger
                            :title="__('ui.created')"
                            :text="__('admin.client_list.table.date_when_the_client_was_created_useful_for_tracking_client_history')"
                        >
                            {{ __('ui.created') }}
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
                                        {{ __('ui.missing') }}
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
                                    {{ __('admin.client_list.table.no_clients_found') }}
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
                text="{{ __('ui.pagination') }}"
            />

            <div class="mt-4">
                <x-ui.table.pagination :paginator="$clients" />
            </div>
        @endif

    </div>
</flux:card>
