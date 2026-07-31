{{-- resources/views/components/management/people/partials/person-overview/⚡filter.blade.php --}}

<flux:card class="mt-6">
    <x-ui.headers.card
        :title="__('Filter')"
        :description="__('Limit the person overview by text, data type and relationships.')"
    />

    <div class="grid grid-cols-1 items-end gap-3 md:grid-cols-2 xl:grid-cols-6">

        {{-- Search filter --}}
        <flux:field class="xl:col-span-2">
            <flux:label for="person-overview-search">
                <x-ui.tooltip.trigger
                    :title="__('ui.actions.search')"
                    :text="__(
                        'Enter a search term to filter the list of people by name, person number, place or email. The search is case-sensitive.',
                    )"
                >
                    {{ __('ui.actions.search') }}
                </x-ui.tooltip.trigger>
            </flux:label>

            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.magnifying-glass stroke-width="1" />
                </flux:input.group.prefix>

                <flux:input
                    id="person-overview-search"
                    name="person-overview-search"
                    type="text"
                    clearable
                    copyable
                    wire:model.live.debounce.300ms="search"
                    placeholder="{{ __('Name, person number, place, email') }}"
                />
            </flux:input.group>
        </flux:field>

        {{-- Data type filter --}}
        <flux:field>
            <flux:label for="person-overview-test-data-filter">
                <x-ui.tooltip.trigger
                    :title="__('Data type')"
                    :text="__('Select a data type to filter the list of people.')"
                >
                    {{ __('Data type') }}
                </x-ui.tooltip.trigger>
            </flux:label>

            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.database stroke-width="1" />
                </flux:input.group.prefix>

                <flux:select
                    id="person-overview-test-data-filter"
                    name="person-overview-test-data-filter"
                    variant="listbox"
                    wire:model.live="testDataFilter"
                >
                    <flux:select.option value="">{{ __('ui.states.all') }}</flux:select.option>
                    <flux:select.option value="test">{{ __('ui.badge.test-data') }}</flux:select.option>
                    <flux:select.option value="real">{{ __('Real data') }}</flux:select.option>
                </flux:select>
            </flux:input.group>
        </flux:field>

        {{-- User filter --}}
        <flux:field>
            <flux:label for="person-overview-user-filter">
                <x-ui.tooltip.trigger
                    :title="__('admin.user_list.meta.user')"
                    :text="__('Select a user filter to filter the list of people.')"
                >
                    {{ __('admin.user_list.meta.user') }}
                </x-ui.tooltip.trigger>
            </flux:label>

            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.user stroke-width="1" />
                </flux:input.group.prefix>

                <flux:select
                    id="person-overview-user-filter"
                    name="person-overview-user-filter"
                    variant="listbox"
                    wire:model.live="userFilter"
                >
                    <flux:select.option value="">{{ __('ui.states.all') }}</flux:select.option>
                    <flux:select.option value="with_user">{{ __('With user') }}</flux:select.option>
                    <flux:select.option value="without_user">{{ __('Without user') }}</flux:select.option>
                </flux:select>
            </flux:input.group>
        </flux:field>

        {{-- Client filter --}}
        <flux:field>
            <flux:label for="person-overview-client-filter">
                <x-ui.tooltip.trigger
                    :title="__('layouts.sidebar.management.client')"
                    :text="__('Select a client filter to filter the list of people.')"
                >
                    {{ __('layouts.sidebar.management.client') }}
                </x-ui.tooltip.trigger>
            </flux:label>

            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.building stroke-width="1" />
                </flux:input.group.prefix>

                <flux:select
                    id="person-overview-client-filter"
                    name="person-overview-client-filter"
                    variant="listbox"
                    wire:model.live="clientFilter"
                >
                    <flux:select.option value="">{{ __('ui.states.all') }}</flux:select.option>
                    <flux:select.option value="with_client">{{ __('With client') }}</flux:select.option>
                    <flux:select.option value="without_client">{{ __('Without client') }}</flux:select.option>
                </flux:select>
            </flux:input.group>
        </flux:field>

        {{-- Birth country filter --}}
        <flux:field>
            <flux:label for="person-overview-birth-country-filter">
                <x-ui.tooltip.trigger
                    :title="__('Birth country')"
                    :text="__('Select a birth country to filter the list of people.')"
                >
                    {{ __('Birth country') }}
                </x-ui.tooltip.trigger>
            </flux:label>

            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.globe stroke-width="1" />
                </flux:input.group.prefix>

                <flux:select
                    id="person-overview-birth-country-filter"
                    name="person-overview-birth-country-filter"
                    variant="listbox"
                    searchable
                    clearable
                    wire:model.live="birthCountryFilter"
                >
                    <flux:select.option value="">
                        <flux:icon.globe
                            class="mr-2 inline-block text-zinc-400"
                            stroke-width="1"
                        />
                        {{ __('ui.states.all') }}
                        {{ __('All') }}
                        {{--
                        TODO:Testweise implementiert,unbedingt wieder löschen --}}
                    </flux:select.option>

                    @foreach ($birthCountryOptions as $country)
                        <flux:select.option value="{{ (string) $country->id }}">
                            <x-ui.country.flag
                                class="mr-2"
                                size="sm"
                                :country="$country->iso2"
                            />
                            {{ $country->name }} ({{ $country->iso2 }})
                        </flux:select.option>
                    @endforeach
                </flux:select>
            </flux:input.group>
        </flux:field>
    </div>

    <div class="mt-4 flex flex-wrap items-end justify-between gap-3">
        <x-ui.table.per-page-selector
            id="person-overview-per-page"
            name="person-overview-per-page"
            model="perPage"
            :options="[10, 25, 50, 100]"
        />

        <x-ui.button.reset wire:click="clearFilters" />
    </div>
</flux:card>
