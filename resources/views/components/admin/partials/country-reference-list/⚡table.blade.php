{{-- resources/views/components/admin/partials/country-reference-list/⚡table.blade.php --}}

{{-- Table --}}
<flux:card class="mt-6">
    <x-ui.headers.card
        :title="__('layouts.sidebar.administration.countries')"
        :description="__('admin.country_reference_list.table.list_of_imported_country_reference_data_address_formats_and_available_subdivisio')"
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
                        <x-ui.tooltip.trigger
                            :title="__('admin.user_list.table.id')"
                            :text="__('admin.country_reference_list.table.unique_identifier_of_the_country_useful_for_tracking_and_reference')"
                        >
                            {{ __('admin.user_list.table.id') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    <flux:table.column
                        sortable
                        wire:click="sortBy('iso2')"
                    >
                        <x-ui.tooltip.trigger
                            :title="__('admin.country_reference_list.table.iso')"
                            :text="__('ISO 2-letter code of the country, useful for identification and reference.')"
                        >
                            {{ __('admin.country_reference_list.table.iso') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    <flux:table.column
                        sortable
                        wire:click="sortBy('name')"
                    >
                        <x-ui.tooltip.trigger
                            :title="__('ui.labels.name')"
                            :text="__('Name of the country, useful for identification and reference.')"
                        >
                            {{ __('ui.labels.name') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    <flux:table.column
                        sortable
                        wire:click="sortBy('official_name')"
                    >
                        <x-ui.tooltip.trigger
                            :title="__('admin.country_reference_list.table.official_name')"
                            :text="__('Official name of the country, useful for identification and reference.')"
                        >
                            {{ __('admin.country_reference_list.table.official_name') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    <flux:table.column
                        sortable
                        wire:click="sortBy('phone_code')"
                    >
                        <x-ui.tooltip.trigger
                            :title="__('Phone')"
                            :text="__('admin.country_reference_list.table.phone_code_of_the_country_useful_for_identification_and_reference')"
                        >
                            {{ __('Phone') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    <flux:table.column
                        sortable
                        wire:click="sortBy('region')"
                    >
                        <x-ui.tooltip.trigger
                            :title="__('ui.filters.region')"
                            :text="__('admin.country_reference_list.table.region_of_the_country_useful_for_identification_and_reference')"
                        >
                            {{ __('ui.filters.region') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    <flux:table.column
                        sortable
                        wire:click="sortBy('capital')"
                    >
                        <x-ui.tooltip.trigger
                            :title="__('admin.country_reference_list.table.capital')"
                            :text="__('admin.country_reference_list.table.capital_of_the_country_useful_for_identification_and_reference')"
                        >
                            {{ __('admin.country_reference_list.table.capital') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    <flux:table.column align="center">
                        <x-ui.tooltip.trigger
                            :title="__('ui.address')"
                            :text="__('admin.country_reference_list.table.address_format_availability_of_the_country_useful_for_identification_and_referen',
                            )"
                        >
                            {{ __('ui.address') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    <flux:table.column
                        align="center"
                        sortable
                        wire:click="sortBy('subdivisions_count')"
                    >
                        <x-ui.tooltip.trigger
                            :title="__('admin.country_reference_list.meta.subdivisions')"
                            :text="__('admin.country_reference_list.table.number_of_subdivisions_of_the_country_useful_for_identification_and_reference',
                            )"
                        >
                            {{ __('admin.country_reference_list.meta.subdivisions') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    <flux:table.column
                        align="center"
                        sortable
                        wire:click="sortBy('is_active')"
                    >
                        <x-ui.tooltip.trigger
                            :title="__('ui.status')"
                            :text="__('admin.country_reference_list.table.active_status_of_the_country_useful_for_identification_and_reference')"
                        >
                            {{ __('ui.status') }}
                        </x-ui.tooltip.trigger>
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
                                    <x-ui.country.flag
                                        :country="$country->iso2"
                                        size="xl"
                                        :title="$country->name"
                                    />

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
                                    <flux:tooltip content="{{ __('admin.partials.country_reference_list.table.address_format_available') }}">
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
                                    <flux:tooltip content="{{ __('admin.country_reference_list.table.address_format_missing') }}">
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
                                    <flux:tooltip content="{{ __('ui.active') }}">
                                        <flux:badge
                                            color="green"
                                            variant="subtle"
                                        >
                                            {{-- {{ __('ui.active') }} --}}
                                            <flux:icon.check
                                                class="text-green-900"
                                                variant="micro"
                                            />
                                        </flux:badge>
                                    </flux:tooltip>
                                @else
                                    <flux:tooltip content="{{ __('ui.filters.inactive') }}">
                                        <flux:badge
                                            color="zinc"
                                            variant="subtle"
                                        >
                                            {{-- {{ __('ui.filters.inactive') }} --}}
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

        <flux:separator text="{{ __('ui.pagination') }}" />

        @if ($countries->hasPages())
            <x-ui.table.pagination :paginator="$countries" />
        @endif
    </div>
</flux:card>
