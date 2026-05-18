{{-- resources/views/components/admin/partials/country-reference-list/⚡filter.blade.php --}}

{{-- Filter --}}
<flux:card class="mt-6">
    <x-ui.headers.card
        :title="__('Filtering')"
        :description="__(
            'Refine the list of reference countries by searching for specific attributes or applying filters based on region, status, membership and data quality.',
        )"
    />

    <div class="grid gap-3 xl:grid-cols-7">
        <div class="col-span-2">
            <flux:label for="country-reference-list-search">
                <x-ui.tooltip.trigger
                    :title="__('Search')"
                    :text="__(
                        'Enter a search term to filter countries by ISO code, name, phone code, capital city or region. The search is case-sensitive.',
                    )"
                >
                    {{ __('Search') }}
                </x-ui.tooltip.trigger>
            </flux:label>

            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.magnifying-glass stroke-width="1" />
                </flux:input.group.prefix>

                <flux:input
                    id="country-reference-list-search"
                    name="country-reference-list-search"
                    type="text"
                    clearable
                    copyable
                    wire:model.live.debounce.300ms="search"
                    placeholder="{{ __('ISO, name, phone, capital, region') }}"
                />
            </flux:input.group>
        </div>

        <div>
            <flux:label for="country-reference-list-region-filter">
                <x-ui.tooltip.trigger
                    :title="__('Filter by region')"
                    :text="__('Select a region to filter the list of reference countries.')"
                >
                    {{ __('Region') }}
                </x-ui.tooltip.trigger>
            </flux:label>

            <flux:select
                id="country-reference-list-region-filter"
                name="country-reference-list-region-filter"
                wire:model.live="regionFilter"
            >
                <flux:select.option value="">
                    {{ __('All') }}
                </flux:select.option>

                @foreach ($regions as $region)
                    <flux:select.option value="{{ $region }}">
                        {{ $region }}
                    </flux:select.option>
                @endforeach
            </flux:select>
        </div>

        <div>
            <flux:label for="country-reference-list-status-filter">
                <x-ui.tooltip.trigger
                    :title="__('Filter by status')"
                    :text="__('Select a status to filter the list of reference countries.')"
                >
                    {{ __('Status') }}
                </x-ui.tooltip.trigger>
            </flux:label>

            <flux:select
                id="country-reference-list-status-filter"
                name="country-reference-list-status-filter"
                wire:model.live="statusFilter"
            >
                <flux:select.option value="">
                    {{ __('All') }}
                </flux:select.option>

                <flux:select.option value="active">
                    {{ __('Active') }}
                </flux:select.option>

                <flux:select.option value="inactive">
                    {{ __('Inactive') }}
                </flux:select.option>
            </flux:select>
        </div>

        <div>
            <flux:label for="country-reference-list-membership-filter">
                <x-ui.tooltip.trigger
                    :title="__('Filter by membership')"
                    :text="__('Select a membership to filter the list of reference countries.')"
                >
                    {{ __('Membership') }}
                </x-ui.tooltip.trigger>
            </flux:label>

            <flux:select
                id="country-reference-list-membership-filter"
                name="country-reference-list-membership-filter"
                wire:model.live="membershipFilter"
            >
                <flux:select.option value="">
                    {{ __('All') }}
                </flux:select.option>

                <flux:select.option value="eu">
                    {{ __('EU') }}
                </flux:select.option>

                <flux:select.option value="eea">
                    {{ __('EEA') }}
                </flux:select.option>

                <flux:select.option value="schengen">
                    {{ __('Schengen') }}
                </flux:select.option>
            </flux:select>
        </div>

        <div>
            <flux:label for="country-reference-list-data-filter">
                <x-ui.tooltip.trigger
                    :title="__('Filter by data quality')"
                    :text="__('Select a data quality filter to filter the list of reference countries.')"
                >
                    {{ __('Data quality') }}
                </x-ui.tooltip.trigger>
            </flux:label>

            <flux:select
                id="country-reference-list-data-filter"
                name="country-reference-list-data-filter"
                wire:model.live="dataFilter"
            >
                <flux:select.option value="">
                    {{ __('All') }}
                </flux:select.option>

                <flux:select.option value="missing_capital">
                    {{ __('Missing capital') }}
                </flux:select.option>

                <flux:select.option value="missing_phone_code">
                    {{ __('Missing phone code') }}
                </flux:select.option>

                <flux:select.option value="missing_address_format">
                    {{ __('Missing address format') }}
                </flux:select.option>

                <flux:select.option value="with_subdivisions">
                    {{ __('With subdivisions') }}
                </flux:select.option>

                <flux:select.option value="without_subdivisions">
                    {{ __('Without subdivisions') }}
                </flux:select.option>
            </flux:select>
        </div>

        <div class="ml-auto flex-none">
            <x-ui.table.per-page-selector
                id="country-reference-list-per-page"
                name="country-reference-list-per-page"
                model="perPage"
                :options="[10, 25, 50, 100]"
            />
        </div>
    </div>
</flux:card>
