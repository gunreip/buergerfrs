{{-- resources/views/components/management/people/person-overview/⚡table.blade.php --}}

<flux:card class="mt-6">
    <x-ui.headers.card
        :title="__('ui.people.people')"
        :description="__('Person records with core metadata and relationship state.')"
    />

    <div class="overflow-hidden rounded-t-lg">
        <flux:table>
            <flux:table.columns class="bg-zinc-800 text-zinc-400">

                {{-- Sequence Number Column --}}
                <flux:table.column align="center">
                    {{ __('ui.labels.number_short') }}
                </flux:table.column>

                {{-- Person Column --}}
                <flux:table.column
                    sortable
                    wire:click="sortBy('last_name')"
                >
                    {{ __('Person') }}
                </flux:table.column>

                {{-- Person Number Column --}}
                <flux:table.column
                    sortable
                    wire:click="sortBy('person_number')"
                >
                    {{ __('Person number') }}
                </flux:table.column>

                {{-- Status Column --}}
                <flux:table.column align="center">
                    {{ __('ui.state.status') }}
                </flux:table.column>

                {{-- Date of Birth Column --}}
                <flux:table.column
                    sortable
                    wire:click="sortBy('date_of_birth')"
                >
                    {{ __('Date of birth') }}
                </flux:table.column>

                {{-- Birth Place Column --}}
                <flux:table.column
                    sortable
                    wire:click="sortBy('birth_place_text')"
                >
                    {{ __('Birth place') }}
                </flux:table.column>

                {{-- Contact Column --}}
                <flux:table.column>
                    {{ __('Contact') }}
                </flux:table.column>

                {{-- User Column --}}
                <flux:table.column>
                    {{ __('admin.user_list.meta.user') }}
                </flux:table.column>

                {{-- Clients Column --}}
                <flux:table.column
                    align="end"
                    sortable
                    wire:click="sortBy('clients_count')"
                >
                    {{ __('Clients') }}
                </flux:table.column>

                {{-- Created At Column --}}
                <flux:table.column
                    sortable
                    wire:click="sortBy('created_at')"
                >
                    {{ __('ui.labels.created') }}
                </flux:table.column>

                {{-- Actions Column --}}
                <flux:table.column align="center">
                    {{ __('ui.table.headers.actions') }}
                </flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($people as $index => $person)
                    <flux:table.row wire:key="person-overview-row-{{ $person->id }}">

                        {{-- Sequence Number --}}
                        <flux:table.cell
                            class="w-20 tabular-nums text-zinc-400"
                            align="end"
                        >
                            {{ $people->firstItem() + $index }}
                        </flux:table.cell>

                        {{-- Personname, Person-ID --}}
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

                        {{-- Person Number --}}
                        <flux:table.cell>
                            @if ($person->person_number)
                                <span class="text-sm tabular-nums">
                                    <x-ui.text.highlight
                                        :value="$person->person_number"
                                        :search="$search"
                                    />
                                </span>
                            @else
                                <x-ui.badge.no-value />
                            @endif
                        </flux:table.cell>

                        {{-- Person Status / Test Data --}}
                        <flux:table.cell align="center">
                            <div class="flex flex-wrap gap-1">
                                <x-ui.badge.test-data :show="$person->is_test_data" />

                                @if (!$person->is_test_data)
                                    <flux:badge
                                        color="green"
                                        size="sm"
                                        variant="subtle"
                                    >
                                        {{ __('Real data') }}
                                    </flux:badge>
                                @endif
                            </div>
                        </flux:table.cell>

                        {{-- Person Date Of Birth --}}
                        <flux:table.cell class="tabular-nums">
                            <x-ui.date-time.date :value="$person->date_of_birth" />
                        </flux:table.cell>

                        {{-- Person Birth Place --}}
                        <flux:table.cell>
                            <div>
                                <x-ui.text.highlight
                                    :value="$person->birth_place_text ?: '—'"
                                    :search="$search"
                                />
                            </div>

                            @if ($person->birthCountry)
                                <div class="mt-1 flex items-center gap-1 text-xs text-zinc-500">
                                    <x-ui.country.flag
                                        class="size-5"
                                        :country="$person->birthCountry->iso2"
                                    />
                                    <span
                                        class="text-sm text-zinc-500 dark:text-white/60">{{ $person->birthCountry->name }}</span>
                                </div>
                            @endif
                        </flux:table.cell>

                        {{-- Person Contact Information --}}
                        <flux:table.cell>
                            <div class="space-y-1 text-sm">
                                @if ($person->email_private)
                                    <div>
                                        <x-ui.text.highlight
                                            :value="$person->email_private"
                                            :search="$search"
                                        />
                                    </div>
                                @endif

                                @if ($person->mobile)
                                    <div class="text-xs text-zinc-500">{{ $person->mobile }}</div>
                                @endif

                                @if (!$person->email_private && !$person->mobile)
                                    <span class="text-zinc-500">—</span>
                                @endif
                            </div>
                        </flux:table.cell>

                        {{-- User Information --}}
                        <flux:table.cell>
                            @if ($person->user)
                                <div class="font-medium text-zinc-100">
                                    <x-ui.text.highlight
                                        :value="$person->user->name"
                                        :search="$search"
                                    />
                                </div>

                                <div class="text-xs text-zinc-500">
                                    <x-ui.text.highlight
                                        :value="$person->user->email"
                                        :search="$search"
                                    />
                                </div>
                            @else
                                <flux:badge
                                    color="orange"
                                    size="sm"
                                    variant="subtle"
                                >
                                    {{ __('Without user') }}
                                </flux:badge>
                            @endif
                        </flux:table.cell>

                        {{-- Clients --}}
                        <flux:table.cell
                            class="tabular-nums"
                            align="end"
                        >
                            {{ $person->clients_count }}
                        </flux:table.cell>

                        {{-- Created At --}}
                        <flux:table.cell>
                            <x-ui.date-time.date :value="$person->created_at" />
                            <x-ui.date-time.time :value="$person->created_at" />
                            {{-- {{ $person->created_at?->format('Y-m-d H:i') }} --}}
                        </flux:table.cell>

                        {{-- Actions --}}
                        <flux:table.cell align="end">
                            <x-ui.button.edit
                                size="sm"
                                icon="pencil-square"
                                :label="__('ui.button.edit.edit')"
                                :href="route('management.people.edit', $person)"
                                wire:navigate
                            />
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        {{-- Empty Row --}}
                        <flux:table.cell colspan="11">
                            <flux:text>{{ __('No people found.') }}</flux:text>
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

        <x-ui.table.pagination :paginator="$people" />
    @endif
</flux:card>
