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
                        <x-ui.tooltip.trigger
                            :title="__('ID')"
                            :text="__('Unique identifier of the country, useful for tracking and reference.')"
                        >
                            {{ __('ID') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    <flux:table.column
                        sortable
                        wire:click="sortBy('iso2')"
                    >
                        <x-ui.tooltip.trigger
                            :title="__('ISO')"
                            :text="__('ISO 2-letter code of the country, useful for identification and reference.')"
                        >
                            {{ __('ISO') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    <flux:table.column
                        sortable
                        wire:click="sortBy('name')"
                    >
                        <x-ui.tooltip.trigger
                            :title="__('Name')"
                            :text="__('Name of the country, useful for identification and reference.')"
                        >
                            {{ __('Name') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    <flux:table.column
                        sortable
                        wire:click="sortBy('official_name')"
                    >
                        <x-ui.tooltip.trigger
                            :title="__('Official name')"
                            :text="__('Official name of the country, useful for identification and reference.')"
                        >
                            {{ __('Official name') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    <flux:table.column
                        sortable
                        wire:click="sortBy('phone_code')"
                    >
                        <x-ui.tooltip.trigger
                            :title="__('Phone')"
                            :text="__('Phone code of the country, useful for identification and reference.')"
                        >
                            {{ __('Phone') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    <flux:table.column
                        sortable
                        wire:click="sortBy('region')"
                    >
                        <x-ui.tooltip.trigger
                            :title="__('Region')"
                            :text="__('Region of the country, useful for identification and reference.')"
                        >
                            {{ __('Region') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    <flux:table.column
                        sortable
                        wire:click="sortBy('capital')"
                    >
                        <x-ui.tooltip.trigger
                            :title="__('Capital')"
                            :text="__('Capital of the country, useful for identification and reference.')"
                        >
                            {{ __('Capital') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    <flux:table.column align="center">
                        <x-ui.tooltip.trigger
                            :title="__('Address')"
                            :text="__(
                                'Address format availability of the country, useful for identification and reference.',
                            )"
                        >
                            {{ __('Address') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    <flux:table.column
                        align="center"
                        sortable
                        wire:click="sortBy('subdivisions_count')"
                    >
                        <x-ui.tooltip.trigger
                            :title="__('Subdivisions')"
                            :text="__(
                                'Number of subdivisions of the country, useful for identification and reference.',
                            )"
                        >
                            {{ __('Subdivisions') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    <flux:table.column
                        align="center"
                        sortable
                        wire:click="sortBy('is_active')"
                    >
                        <x-ui.tooltip.trigger
                            :title="__('Status')"
                            :text="__('Active status of the country, useful for identification and reference.')"
                        >
                            {{ __('Status') }}
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
