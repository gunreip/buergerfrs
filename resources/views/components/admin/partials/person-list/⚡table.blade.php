{{-- resources/views/components/admin/partials/person-list/⚡table.blade.php --}}

{{-- Table --}}
<flux:card class="mt-6">
    <flux:heading
        class="mb-4"
        size="lg"
    >
        {{ __('People List') }}
    </flux:heading>

    <div class="overflow-hidden rounded-t-lg">
        <flux:table>
            <flux:table.columns class="bg-zinc-800 text-zinc-400">
                <flux:table.column align="center">
                    {{ __('#') }}
                </flux:table.column>

                <flux:table.column
                    sortable
                    wire:click="sortBy('last_name')"
                >
                    {{ __('Person') }}
                </flux:table.column>

                <flux:table.column
                    sortable
                    wire:click="sortBy('person_number')"
                >
                    {{ __('Person number') }}
                </flux:table.column>

                <flux:table.column
                    sortable
                    wire:click="sortBy('date_of_birth')"
                >
                    {{ __('Date of birth') }}
                </flux:table.column>

                <flux:table.column>
                    {{ __('User') }}
                </flux:table.column>

                <flux:table.column
                    align="center"
                    sortable
                    wire:click="sortBy('clients_count')"
                >
                    {{ __('Clients') }}
                </flux:table.column>

                <flux:table.column
                    sortable
                    wire:click="sortBy('created_at')"
                >
                    {{ __('Created') }}
                </flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($people as $index => $person)
                    <flux:table.row>
                        <flux:table.cell
                            class="w-32 tabular-nums text-zinc-400"
                            align="end"
                        >
                            {{ $people->firstItem() + $index }}
                        </flux:table.cell>

                        <flux:table.cell>
                            <div class="font-medium text-zinc-100">
                                {!! $highlightSearchMatch($person->displayName(), $search) !!}
                            </div>

                            <div class="text-xs text-zinc-500">
                                ID: {{ $person->id }}
                            </div>
                        </flux:table.cell>

                        <flux:table.cell>
                            @if ($person->person_number)
                                <span class="font-mono text-sm">
                                    {!! $highlightSearchMatch($person->person_number, $search) !!}
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
                            {{ $person->date_of_birth?->format('Y-m-d') ?? '—' }}
                        </flux:table.cell>

                        <flux:table.cell>
                            @if ($person->user)
                                <div class="font-medium text-zinc-100">
                                    {!! $highlightSearchMatch($person->user->name, $search) !!}
                                </div>

                                <div class="text-xs text-zinc-400">
                                    {!! $highlightSearchMatch($person->user->email, $search) !!}
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
        <div class="mt-4">
            {{ $people->links() }}
        </div>
    @endif
</flux:card>
