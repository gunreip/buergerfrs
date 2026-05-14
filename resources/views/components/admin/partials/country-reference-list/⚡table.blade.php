{{-- resources/views/components/admin/partials/country-reference-list/⚡table.blade.php --}}

{{-- Table --}}
<flux:card class="mt-6">
    <x-ui.headers.card
        :title="__('Countries')"
        :description="__('List of imported country reference data, address formats and available subdivisions.')"
    />

    <div class="mx-auto max-w-full">
        <div class="overflow-hidden rounded-t-lg">
            <flux:table>
                <flux:table.columns class="bg-zinc-800 text-zinc-400">
                    <flux:table.column
                        sortable
                        wire:click="sortBy('id')"
                        align="center"
                    >
                        {{ __('ID') }}
                    </flux:table.column>

                    <flux:table.column
                        sortable
                        wire:click="sortBy('iso2')"
                    >
                        {{ __('ISO') }}
                    </flux:table.column>

                    <flux:table.column
                        sortable
                        wire:click="sortBy('name')"
                    >
                        {{ __('Name') }}
                    </flux:table.column>

                    <flux:table.column
                        sortable
                        wire:click="sortBy('official_name')"
                    >
                        {{ __('Official name') }}
                    </flux:table.column>

                    <flux:table.column
                        sortable
                        wire:click="sortBy('phone_code')"
                    >
                        {{ __('Phone') }}
                    </flux:table.column>

                    <flux:table.column
                        sortable
                        wire:click="sortBy('region')"
                    >
                        {{ __('Region') }}
                    </flux:table.column>

                    <flux:table.column
                        sortable
                        wire:click="sortBy('capital')"
                    >
                        {{ __('Capital') }}
                    </flux:table.column>

                    <flux:table.column align="center">
                        {{ __('Address') }}
                    </flux:table.column>

                    <flux:table.column
                        align="center"
                        sortable
                        wire:click="sortBy('subdivisions_count')"
                    >
                        {{ __('Subdivisions') }}
                    </flux:table.column>

                    <flux:table.column
                        align="center"
                        sortable
                        wire:click="sortBy('is_active')"
                    >
                        {{ __('Status') }}
                    </flux:table.column>
                </flux:table.columns>

                {{-- ID, ISO, name, official name, phone code, region, capital, address format availability, subdivisions count and active status for each country --}}
                <flux:table.rows>
                    @forelse ($countries as $country)
                        <flux:table.row wire:key="country-reference-row-{{ $country->id }}">

                            {{-- ID --}}
                            <flux:table.cell
                                class="w-32 tabular-nums"
                                align="end"
                            >
                                {{ $country->id }}
                            </flux:table.cell>

                            {{-- ISO (flag, iso2, iso3, iso numeric) --}}
                            <flux:table.cell>
                                <div class="flex items-center gap-2">
                                    <flux:text
                                        class="text-3xl leading-none"
                                        inline
                                    >
                                        {{ $country->emoji_flag ?: '—' }}
                                    </flux:text>

                                    <flux:field>
                                        <flux:text variant="strong">
                                            {{ $country->iso2 }}
                                        </flux:text>

                                        <flux:text
                                            class="-mt-2 text-xs"
                                            variant="subtle"
                                        >
                                            {{ $country->iso3 }} · {{ $country->iso_numeric }}
                                        </flux:text>
                                    </flux:field>
                                </div>
                            </flux:table.cell>

                            {{-- Name and common name --}}
                            <flux:table.cell>
                                <flux:text variant="strong">
                                    {{ $country->name }}
                                </flux:text>

                                <flux:text
                                    class="text-xs"
                                    variant="subtle"
                                >
                                    {{ $country->common_name ?: '—' }}
                                </flux:text>
                            </flux:table.cell>

                            {{-- Official name --}}
                            <flux:table.cell>
                                <flux:text
                                    class="max-w-sm truncate"
                                    variant="strong"
                                >
                                    {{ $country->official_name ?: '—' }}
                                </flux:text>
                            </flux:table.cell>

                            {{-- Phone code --}}
                            <flux:table.cell>
                                <flux:text
                                    class="tabular-nums"
                                    variant="strong"
                                >
                                    {{ $country->phone_code ?: '—' }}
                                </flux:text>
                            </flux:table.cell>

                            {{-- Region and subregion --}}
                            <flux:table.cell>
                                <flux:text variant="strong">
                                    {{ $country->region ?: '—' }}
                                </flux:text>

                                <flux:text
                                    class="text-xs"
                                    variant="subtle"
                                >
                                    {{ $country->subregion ?: '—' }}
                                </flux:text>
                            </flux:table.cell>

                            {{-- Capital --}}
                            <flux:table.cell>
                                <flux:text
                                    class="max-w-xs truncate"
                                    variant="strong"
                                >
                                    {{ $country->capital ?: '—' }}
                                </flux:text>
                            </flux:table.cell>

                            <flux:table.cell align="center">
                                @if ($country->address_format_key)
                                    <flux:tooltip content="{{ __('Address format available') }}">
                                        <flux:badge
                                            color="green"
                                            variant="subtle"
                                        >
                                            <flux:icon.check
                                                class="text-green-900"
                                                variant="micro"
                                            />
                                        </flux:badge>
                                    </flux:tooltip>
                                @else
                                    <flux:tooltip content="{{ __('Address format missing') }}">
                                        <flux:badge
                                            color="amber"
                                            variant="subtle"
                                        >
                                            <flux:icon.shield-x
                                                class="text-red-900"
                                                variant="micro"
                                            />
                                        </flux:badge>
                                    </flux:tooltip>
                                @endif
                            </flux:table.cell>

                            <flux:table.cell align="center">
                                <span class="tabular-nums">
                                    {{ $country->subdivisions_count }}
                                </span>
                            </flux:table.cell>

                            <flux:table.cell align="center">
                                @if ($country->is_active)
                                    <flux:tooltip content="{{ __('Active') }}">
                                        <flux:badge
                                            color="green"
                                            variant="subtle"
                                        >
                                            {{-- {{ __('Active') }} --}}
                                            <flux:icon.check
                                                class="text-green-900"
                                                variant="micro"
                                            />
                                        </flux:badge>
                                    </flux:tooltip>
                                @else
                                    <flux:tooltip content="{{ __('Inactive') }}">
                                        <flux:badge
                                            color="zinc"
                                            variant="subtle"
                                        >
                                            {{-- {{ __('Inactive') }} --}}
                                            <flux:icon.x
                                                class="text-red-900"
                                                variant="micro"
                                            />
                                        </flux:badge>
                                    </flux:tooltip>
                                @endif
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="10">
                                <div class="py-8 text-center text-sm text-zinc-500 dark:text-zinc-400">
                                    {{ __('No countries found.') }}
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>

        <flux:separator text="{{ __('Pagination') }}" />

        @if ($countries->hasPages())
            <x-ui.table.pagination :paginator="$countries" />
        @endif
    </div>
</flux:card>
