{{-- resources/views/components/admin/partials/person-list/⚡table.blade.php --}}

{{-- Table --}}
<flux:card class="mt-6">

    <x-ui.headers.card
        :title="__('People List')"
        :description="__('Detailed list of all people in the system, their linked user accounts and client assignments.')"
    />

    <div class="mx-auto max-w-full">

        <div class="overflow-hidden rounded-t-lg">
            <flux:table>
                <flux:table.columns class="bg-zinc-800 text-zinc-400">
                    <flux:table.column align="center">
                        <x-ui.tooltip.trigger
                            :title="__('ui.labels.number_short')"
                            :text="__('Unique identifier of the person, useful for tracking and reference.')"
                        >
                            {{ __('ui.labels.number_short') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    <flux:table.column
                        sortable
                        wire:click="sortBy('last_name')"
                    >
                        <x-ui.tooltip.trigger
                            :title="__('Person')"
                            :text="__('Full name of the person, useful for identification and reference.')"
                        >
                            {{ __('Person') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    <flux:table.column
                        sortable
                        wire:click="sortBy('person_number')"
                    >
                        <x-ui.tooltip.trigger
                            :title="__('Person number')"
                            :text="__(
                                'Unique number assigned to the person, useful for identification and reference.',
                            )"
                        >
                            {{ __('Person number') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    <flux:table.column
                        sortable
                        wire:click="sortBy('date_of_birth')"
                    >
                        <x-ui.tooltip.trigger
                            :title="__('Date of birth')"
                            :text="__('Date of birth of the person, useful for identification and reference.')"
                        >
                            {{ __('Date of birth') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    <flux:table.column>
                        {{ __('admin.user_list.meta.user') }}
                    </flux:table.column>

                    <flux:table.column
                        align="center"
                        sortable
                        wire:click="sortBy('clients_count')"
                    >
                        <x-ui.tooltip.trigger
                            :title="__('Clients')"
                            :text="__(
                                'Number of clients associated with the person, useful for identification and reference.',
                            )"
                        >
                            {{ __('Clients') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    <flux:table.column
                        sortable
                        wire:click="sortBy('created_at')"
                    >
                        <x-ui.tooltip.trigger
                            :title="__('ui.labels.created')"
                            :text="__('Date when the person was created, useful for identification and reference.')"
                        >
                            {{ __('ui.labels.created') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse ($people as $index => $person)
                        <flux:table.row wire:key="person-list-row-{{ $person->id }}">
                            <flux:table.cell
                                class="w-32 tabular-nums text-zinc-400"
                                align="end"
                            >
                                {{ $people->firstItem() + $index }}
                            </flux:table.cell>

                            <flux:table.cell>
                                <div class="font-medium text-zinc-100">
                                    <x-ui.text.highlight
                                        :value="$person->displayName()"
                                        :search="$search"
                                    />
                                </div>

                                <div class="text-xs text-zinc-500">
                                    ID: {{ $person->id }}
                                </div>
                            </flux:table.cell>

                            <flux:table.cell>
                                @if ($person->person_number)
                                    <span class="font-mono text-sm">
                                        <x-ui.text.highlight
                                            :value="$person->person_number"
                                            :search="$search"
                                        />
                                    </span>
                                @else
                                    <flux:badge
                                        color="zinc"
                                        variant="subtle"
                                    >
                                        {{ __('ui.state.missing') }}
                                    </flux:badge>
                                @endif
                            </flux:table.cell>

                            <flux:table.cell>
                                {{ $person->date_of_birth?->format('Y-m-d') ?? '—' }}
                            </flux:table.cell>

                            <flux:table.cell>
                                @if ($person->user)
                                    <div class="font-medium text-zinc-100">
                                        <x-ui.text.highlight
                                            :value="$person->user->name"
                                            :search="$search"
                                        />
                                    </div>

                                    <div class="text-xs text-zinc-400">
                                        <x-ui.text.highlight
                                            :value="$person->user->email"
                                            :search="$search"
                                        />
                                    </div>
                                @else
                                    <flux:badge
                                        color="orange"
                                        variant="subtle"
                                    >
                                        {{ __('Without user') }}
                                    </flux:badge>
                                @endif
                            </flux:table.cell>

                            <flux:table.cell
                                class="tabular-nums"
                                align="end"
                            >
                                {{ $person->clients_count }}
                            </flux:table.cell>

                            <flux:table.cell>
                                {{ $person->created_at?->format('Y-m-d H:i') }}
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="7">
                                <flux:text>
                                    {{ __('No people found.') }}
                                </flux:text>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>

        @if ($people->hasPages())
            <flux:separator
                class="mt-4"
                text="{{ __('ui.nouns.pagination') }}"
            />

            <div class="mt-4">
                <x-ui.table.pagination :paginator="$people" />
            </div>
        @endif

    </div>
</flux:card>
